<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const LOW_STOCK_LIMIT = 3;

    public function index(Request $request): View
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();
        $selectedDate = $request->query('date', now()->toDateString());

        $pickupSlots = $this->pickupSlots($canteen->id);
        $manualMenus = $this->menusForManualOrder($canteen->id);
        $orders = $this->activeOrders($canteen->id, $selectedDate);
        $orderGroups = $this->groupOrdersByPickupSlot($orders);

        return view('penjual.dashboard', [
            'canteen' => $canteen,
            'selectedDate' => $selectedDate,
            'stats' => $this->stats($canteen->id, $selectedDate),
            'readyWarningCount' => $this->readyWarningCount($canteen->id, $selectedDate),
            'pickupSlots' => $pickupSlots,
            'manualMenus' => $manualMenus,
            'statusOptions' => $this->statusOptions(),
            'orderGroups' => $orderGroups,
        ]);
    }

    public function updateStatus(Request $request, int $order)
    {
        $this->ensureSeller();

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->statusOptions()))],
        ]);

        $canteen = $this->currentCanteen();

        $existingOrder = DB::table('orders')
            ->where('id', $order)
            ->where('canteen_id', $canteen->id)
            ->first();

        if (! $existingOrder) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Pesanan tidak ditemukan atau bukan milik kantin Anda.',
                ], 404);
            }

            return back()->with('error', 'Pesanan tidak ditemukan atau bukan milik kantin Anda.');
        }

        DB::table('orders')
            ->where('id', $order)
            ->where('canteen_id', $canteen->id)
            ->update([
                'status' => $validated['status'],
                'updated_at' => now(),
            ]);

        $stats = $this->stats($canteen->id, $existingOrder->pickup_date);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Status pesanan berhasil diperbarui.',
                'order' => [
                    'id' => $order,
                    'status' => $validated['status'],
                    'status_label' => $this->statusOptions()[$validated['status']],
                    'status_classes' => $this->statusClasses($validated['status']),
                ],
                'stats' => [
                    'diproses' => $stats['diproses'],
                    'siap_ambil' => $stats['siap_ambil'],
                    'selesai' => $stats['selesai'],
                ],
            ]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function storeManual(Request $request): RedirectResponse
    {
        $this->ensureSeller();

        $validated = $request->validate([
            'canteen_pickup_slot_id' => ['required', 'integer', 'exists:canteen_pickup_slots,id'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $canteen = $this->currentCanteen();
        $items = $this->normaliseRequestedItems($validated['items']);

        if ($items->isEmpty()) {
            return back()->with('error', 'Pilih minimal 1 menu.');
        }

        DB::transaction(function () use ($validated, $canteen, $items) {
            $this->assertPickupSlotBelongsToCanteen(
                (int) $validated['canteen_pickup_slot_id'],
                $canteen->id
            );

            $menus = $this->lockedMenus($canteen->id, $items->keys()->all());

            $this->assertRequestedStockIsAvailable($items, $menus);

            /**
             * Pesanan manual ditandai secara implisit:
             * customer_id = auth()->id()
             *
             * Karena yang membuat adalah akun penjual yang sedang login.
             */
            $orderId = DB::table('orders')->insertGetId([
                'customer_id' => auth()->id(),
                'canteen_id' => $canteen->id,
                'canteen_pickup_slot_id' => (int) $validated['canteen_pickup_slot_id'],
                'pickup_date' => now()->toDateString(),
                'status' => 'diproses',
                'note' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($items as $menuId => $quantity) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'menu_id' => $menuId,
                    'quantity' => $quantity,
                ]);

                DB::table('menus')
                    ->where('id', $menuId)
                    ->decrement('stock_qty', $quantity);
            }
        });

        return back()->with('success', 'Pesanan manual berhasil ditambahkan.');
    }

    public function updateManual(Request $request, int $order): RedirectResponse
    {
        $this->ensureSeller();

        $validated = $request->validate([
            'canteen_pickup_slot_id' => ['required', 'integer', 'exists:canteen_pickup_slots,id'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $canteen = $this->currentCanteen();
        $newItems = $this->normaliseRequestedItems($validated['items']);

        if ($newItems->isEmpty()) {
            return back()->with('error', 'Pesanan manual harus memiliki minimal 1 menu.');
        }

        DB::transaction(function () use ($validated, $canteen, $newItems, $order) {
            $this->assertPickupSlotBelongsToCanteen(
                (int) $validated['canteen_pickup_slot_id'],
                $canteen->id
            );

            /**
             * Untuk edit pesanan manual:
             * - harus masuk ke kantin milik penjual
             * - harus dibuat oleh penjual yang sedang login
             *
             * Jadi tidak perlu kolom is_manual.
             */
            $manualOrder = DB::table('orders')
                ->where('id', $order)
                ->where('canteen_id', $canteen->id)
                ->where('customer_id', auth()->id())
                ->first();

            if (! $manualOrder) {
                throw ValidationException::withMessages([
                    'order' => 'Pesanan manual tidak ditemukan atau bukan pesanan yang Anda buat.',
                ]);
            }

            if (($manualOrder->status ?? null) === 'selesai') {
                throw ValidationException::withMessages([
                    'order' => 'Pesanan yang sudah selesai tidak dapat diedit.',
                ]);
            }

            $oldItems = DB::table('order_items')
                ->where('order_id', $order)
                ->get(['menu_id', 'quantity'])
                ->keyBy('menu_id');

            $menuIds = $oldItems->keys()
                ->merge($newItems->keys())
                ->unique()
                ->values()
                ->all();

            $menus = $this->lockedMenus($canteen->id, $menuIds);

            /**
             * Kembalikan stok lama dulu.
             * Misal awalnya 1 Nasi Gila, lalu diedit jadi 2.
             * Kalau stok lama tidak dikembalikan dulu, hitungan stok bisa salah.
             */
            foreach ($oldItems as $oldItem) {
                DB::table('menus')
                    ->where('id', $oldItem->menu_id)
                    ->increment('stock_qty', (int) $oldItem->quantity);

                if ($menus->has($oldItem->menu_id)) {
                    $menus[$oldItem->menu_id]->stock_qty += (int) $oldItem->quantity;
                }
            }

            $this->assertRequestedStockIsAvailable($newItems, $menus);

            DB::table('order_items')
                ->where('order_id', $order)
                ->delete();

            foreach ($newItems as $menuId => $quantity) {
                DB::table('order_items')->insert([
                    'order_id' => $order,
                    'menu_id' => $menuId,
                    'quantity' => $quantity,
                ]);

                DB::table('menus')
                    ->where('id', $menuId)
                    ->decrement('stock_qty', $quantity);
            }

            DB::table('orders')
                ->where('id', $order)
                ->update([
                    'canteen_pickup_slot_id' => (int) $validated['canteen_pickup_slot_id'],
                    'updated_at' => now(),
                ]);
        });

        return back()->with('success', 'Pesanan manual berhasil diubah.');
    }

    private function ensureSeller(): void
    {
        abort_unless(
            auth()->check() && (auth()->user()->role ?? null) === 'penjual',
            403,
            'Akses hanya untuk penjual.'
        );
    }

    private function currentCanteen(): object
    {
        $canteen = DB::table('canteens')
            ->where('user_id', auth()->id())
            ->first();

        abort_if(! $canteen, 403, 'Akun penjual ini belum memiliki data kantin.');

        return $canteen;
    }

    private function statusOptions(): array
    {
        return [
            'diproses' => 'Diproses',
            'siap_ambil' => 'Siap diambil',
            'selesai' => 'Selesai',
        ];
    }

    private function stats(int $canteenId, string $date): array
    {
        $baseQuery = DB::table('orders')
            ->where('canteen_id', $canteenId)
            ->whereDate('pickup_date', $date);

        return [
            'diproses' => (clone $baseQuery)->where('status', 'diproses')->count(),
            'siap_ambil' => (clone $baseQuery)->where('status', 'siap_ambil')->count(),
            'selesai' => (clone $baseQuery)->where('status', 'selesai')->count(),
            'stok_menipis' => DB::table('menus')
                ->where('canteen_id', $canteenId)
                ->where('stock_qty', '>', 0)
                ->where('stock_qty', '<=', self::LOW_STOCK_LIMIT)
                ->count(),
        ];
    }

    private function readyWarningCount(int $canteenId, string $date): int
    {
        return DB::table('orders')
            ->where('canteen_id', $canteenId)
            ->whereDate('pickup_date', $date)
            ->where('status', 'siap_ambil')
            ->where('updated_at', '<=', now()->subMinutes(15))
            ->count();
    }

    private function pickupSlots(int $canteenId)
    {
        return DB::table('canteen_pickup_slots')
            ->join(
                'pickup_slot_options',
                'canteen_pickup_slots.pickup_slot_option_id',
                '=',
                'pickup_slot_options.id'
            )
            ->select([
                'canteen_pickup_slots.id',
                'pickup_slot_options.start_time',
                'pickup_slot_options.end_time',
                'canteen_pickup_slots.quota',
            ])
            ->where('canteen_pickup_slots.canteen_id', $canteenId)
            ->where('canteen_pickup_slots.is_active', true)
            ->where('pickup_slot_options.is_active', true)
            ->orderBy('pickup_slot_options.start_time')
            ->get()
            ->map(function ($slot) {
                $slot->formatted_time = $this->formatTime($slot->start_time)
                    . ' - '
                    . $this->formatTime($slot->end_time);

                return $slot;
            });
    }

    private function menusForManualOrder(int $canteenId)
    {
        return DB::table('menus')
            ->select(['id', 'name', 'price', 'image', 'stock_qty'])
            ->where('canteen_id', $canteenId)
            ->orderBy('name')
            ->get()
            ->map(function ($menu) {
                $menu->formatted_price = $this->formatRupiah($menu->price);

                $menu->image_url = $menu->image
                    ? 'data:image/jpeg;base64,' . base64_encode($menu->image)
                    : asset('images/nasi-gila.jpg');

                unset($menu->image);

                return $menu;
            });
    }

    private function activeOrders(int $canteenId, string $date)
    {
        $customerNameColumn = Schema::hasColumn('users', 'username')
            ? 'users.username'
            : 'users.name';

        $orders = DB::table('orders')
            ->join('users', 'orders.customer_id', '=', 'users.id')
            ->leftJoin(
                'canteen_pickup_slots',
                'orders.canteen_pickup_slot_id',
                '=',
                'canteen_pickup_slots.id'
            )
            ->leftJoin(
                'pickup_slot_options',
                'canteen_pickup_slots.pickup_slot_option_id',
                '=',
                'pickup_slot_options.id'
            )
            ->select([
                'orders.id',
                'orders.customer_id',
                'orders.canteen_pickup_slot_id',
                'orders.pickup_date',
                'orders.status',
                'orders.note',
                'orders.created_at',
                'orders.updated_at',
                DB::raw($customerNameColumn . ' as customer_name'),
                'pickup_slot_options.start_time',
                'pickup_slot_options.end_time',
            ])
            ->where('orders.canteen_id', $canteenId)
            ->whereDate('orders.pickup_date', $date)
            ->whereIn('orders.status', ['diproses', 'siap_ambil', 'selesai'])
            ->orderBy('pickup_slot_options.start_time')
            ->orderBy('orders.created_at')
            ->get();

        $orderIds = $orders->pluck('id')->all();

        $itemsByOrder = collect();

        if (! empty($orderIds)) {
            $itemsByOrder = DB::table('order_items')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->select([
                    'order_items.order_id',
                    'order_items.menu_id',
                    'order_items.quantity',
                    'menus.name',
                    'menus.price',
                ])
                ->whereIn('order_items.order_id', $orderIds)
                ->orderBy('menus.name')
                ->get()
                ->groupBy('order_id');
        }

        return $orders->map(function ($order) use ($itemsByOrder) {
            $items = $itemsByOrder->get($order->id, collect());

            /**
             * Pesanan dianggap manual kalau customer_id adalah id penjual yang login.
             */
            $isManualOrder = (int) $order->customer_id === (int) auth()->id();

            $order->code = '#ORD-' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
            $order->status_label = $this->statusOptions()[$order->status] ?? ucfirst($order->status);
            $order->status_classes = $this->statusClasses($order->status);

            $order->customer_name = $isManualOrder
                ? 'Anda'
                : ($order->customer_name ?: 'Pelanggan');

            $order->items = $items;
            $order->item_count = $items->sum('quantity');

            $order->note_text = $order->note
                ?: ($isManualOrder ? 'Pesanan manual dari penjual' : null);

            $order->pickup_time = $this->formatTime($order->start_time)
                . ' - '
                . $this->formatTime($order->end_time);

            $order->can_edit_manual = $isManualOrder && $order->status !== 'selesai';

            $order->manual_payload = [
                'id' => $order->id,
                'pickup_slot_id' => $order->canteen_pickup_slot_id,
                'items' => $items->map(fn ($item) => [
                    'menu_id' => (int) $item->menu_id,
                    'quantity' => (int) $item->quantity,
                ])->values(),
            ];

            return $order;
        });
    }

    private function groupOrdersByPickupSlot($orders)
    {
        return $orders
            ->groupBy(fn ($order) => $order->canteen_pickup_slot_id ?: 'tanpa-slot')
            ->map(function ($slotOrders) {
                $firstOrder = $slotOrders->first();

                return (object) [
                    'pickup_time' => $firstOrder->pickup_time ?: 'Tanpa slot',
                    'order_count' => $slotOrders->count(),
                    'orders' => $slotOrders,
                ];
            })
            ->values();
    }

    private function normaliseRequestedItems(array $items)
    {
        return collect($items)
            ->mapWithKeys(fn ($quantity, $menuId) => [(int) $menuId => (int) $quantity])
            ->filter(fn ($quantity, $menuId) => $menuId > 0 && $quantity > 0);
    }

    private function assertPickupSlotBelongsToCanteen(int $pickupSlotId, int $canteenId): void
    {
        $exists = DB::table('canteen_pickup_slots')
            ->join(
                'pickup_slot_options',
                'canteen_pickup_slots.pickup_slot_option_id',
                '=',
                'pickup_slot_options.id'
            )
            ->where('canteen_pickup_slots.id', $pickupSlotId)
            ->where('canteen_pickup_slots.canteen_id', $canteenId)
            ->where('canteen_pickup_slots.is_active', true)
            ->where('pickup_slot_options.is_active', true)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'canteen_pickup_slot_id' => 'Waktu pengambilan tidak tersedia untuk kantin ini.',
            ]);
        }
    }

    private function lockedMenus(int $canteenId, array $menuIds)
    {
        $menus = DB::table('menus')
            ->where('canteen_id', $canteenId)
            ->whereIn('id', $menuIds)
            ->lockForUpdate()
            ->get(['id', 'name', 'stock_qty'])
            ->keyBy('id');

        if ($menus->count() !== count(array_unique($menuIds))) {
            throw ValidationException::withMessages([
                'items' => 'Ada menu yang tidak valid untuk kantin ini.',
            ]);
        }

        return $menus;
    }

    private function assertRequestedStockIsAvailable($items, $menus): void
    {
        foreach ($items as $menuId => $quantity) {
            $menu = $menus->get($menuId);

            if (! $menu || (int) $menu->stock_qty < (int) $quantity) {
                throw ValidationException::withMessages([
                    'items' => 'Stok menu ' . ($menu->name ?? '') . ' tidak mencukupi.',
                ]);
            }
        }
    }

    private function statusClasses(string $status): string
    {
        return match ($status) {
            'siap_ambil' => 'bg-green-100 text-green-700',
            'selesai' => 'bg-gray-200 text-gray-700',
            default => 'bg-yellow-100 text-yellow-700',
        };
    }

    private function formatRupiah(float|int|string $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }

    private function formatTime(?string $time): string
    {
        if (! $time) {
            return '-';
        }

        return str_replace(':', '.', substr($time, 0, 5));
    }
}
