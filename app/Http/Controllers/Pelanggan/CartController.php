<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    private int $serviceFee = 1000;

    public function index(Request $request): View|JsonResponse
    {
        $data = $this->getCartPageData($request);

        if ($request->ajax()) {
            return $this->cartJsonResponse($data);
        }

        return view('pelanggan.keranjang', $data);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'menu_id' => ['required', 'integer', 'exists:menus,id'],
        ]);

        $customerId = auth()->id();

        $menu = DB::table('menus')
            ->join('canteens', 'menus.canteen_id', '=', 'canteens.id')
            ->select([
                'menus.id',
                'menus.canteen_id',
                'menus.stock_qty',
                'canteens.is_open',
            ])
            ->where('menus.id', $validated['menu_id'])
            ->first();

        if (! $menu || ! $menu->is_open) {
            return $this->failResponse($request, 'Menu tidak tersedia.');
        }

        if ($menu->stock_qty <= 0) {
            return $this->failResponse($request, 'Stok menu sedang habis.');
        }

        $cartId = DB::transaction(function () use ($customerId, $menu) {
            $cartId = DB::table('carts')
                ->where('customer_id', $customerId)
                ->where('canteen_id', $menu->canteen_id)
                ->value('id');

            if (! $cartId) {
                $cartId = DB::table('carts')->insertGetId([
                    'customer_id' => $customerId,
                    'canteen_id' => $menu->canteen_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $cartItem = DB::table('cart_items')
                ->where('cart_id', $cartId)
                ->where('menu_id', $menu->id)
                ->first();

            if ($cartItem) {
                $newQuantity = min((int) $cartItem->quantity + 1, (int) $menu->stock_qty);

                DB::table('cart_items')
                    ->where('id', $cartItem->id)
                    ->update([
                        'quantity' => $newQuantity,
                    ]);
            } else {
                DB::table('cart_items')->insert([
                    'cart_id' => $cartId,
                    'menu_id' => $menu->id,
                    'quantity' => 1,
                ]);
            }

            DB::table('carts')
                ->where('id', $cartId)
                ->update([
                    'updated_at' => now(),
                ]);

            return $cartId;
        });

        if ($request->ajax()) {
            $request->merge([
                'cart_id' => $cartId,
            ]);

            return $this->cartJsonResponse(
                $this->getCartPageData($request),
                'Menu berhasil ditambahkan ke keranjang.'
            );
        }

        return redirect()
            ->route('pelanggan.keranjang', ['cart_id' => $cartId])
            ->with('success', 'Menu berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, int $item): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItem = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('menus', 'cart_items.menu_id', '=', 'menus.id')
            ->select([
                'cart_items.id',
                'cart_items.cart_id',
                'menus.stock_qty',
            ])
            ->where('cart_items.id', $item)
            ->where('carts.customer_id', auth()->id())
            ->first();

        if (! $cartItem) {
            return $this->failResponse($request, 'Item keranjang tidak ditemukan.');
        }

        if ($cartItem->stock_qty <= 0) {
            return $this->failResponse($request, 'Stok menu sedang habis.');
        }

        $quantity = min((int) $validated['quantity'], (int) $cartItem->stock_qty);

        DB::table('cart_items')
            ->where('id', $cartItem->id)
            ->update([
                'quantity' => $quantity,
            ]);

        DB::table('carts')
            ->where('id', $cartItem->cart_id)
            ->update([
                'updated_at' => now(),
            ]);

        if ($request->ajax()) {
            $activeCartId = (int) ($request->input('cart_id') ?: $cartItem->cart_id);

            $request->merge([
                'cart_id' => $activeCartId,
            ]);

            return $this->cartJsonResponse(
                $this->getCartPageData($request),
                'Jumlah item berhasil diperbarui.'
            );
        }

        return redirect()
            ->route('pelanggan.keranjang', ['cart_id' => $cartItem->cart_id])
            ->with('success', 'Jumlah item berhasil diperbarui.');
    }

    public function destroy(Request $request, int $item): RedirectResponse|JsonResponse
    {
        $cartItem = DB::table('cart_items')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->select([
                'cart_items.id',
                'cart_items.cart_id',
            ])
            ->where('cart_items.id', $item)
            ->where('carts.customer_id', auth()->id())
            ->first();

        if (! $cartItem) {
            return $this->failResponse($request, 'Item keranjang tidak ditemukan.');
        }

        $activeCartId = (int) ($request->input('cart_id') ?: $cartItem->cart_id);

        DB::transaction(function () use ($cartItem) {
            DB::table('cart_items')
                ->where('id', $cartItem->id)
                ->delete();

            $remainingItems = DB::table('cart_items')
                ->where('cart_id', $cartItem->cart_id)
                ->count();

            if ($remainingItems === 0) {
                DB::table('carts')
                    ->where('id', $cartItem->cart_id)
                    ->delete();
            } else {
                DB::table('carts')
                    ->where('id', $cartItem->cart_id)
                    ->update([
                        'updated_at' => now(),
                    ]);
            }
        });

        if ($request->ajax()) {
            $request->merge([
                'cart_id' => $activeCartId,
            ]);

            return $this->cartJsonResponse(
                $this->getCartPageData($request),
                'Item berhasil dihapus dari keranjang.'
            );
        }

        return redirect()
            ->route('pelanggan.keranjang', ['cart_id' => $activeCartId])
            ->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cart_id' => ['required', 'integer', 'exists:carts,id'],
            'canteen_pickup_slot_id' => ['required', 'integer', 'exists:canteen_pickup_slots,id'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $customerId = auth()->id();
        $minimumPickupStartTime = $this->minimumPickupStartTimeForToday();

        if (! $minimumPickupStartTime) {
            abort(422, 'Waktu pengambilan tidak tersedia atau sudah terlalu dekat dari waktu sekarang.');
        }

        DB::transaction(function () use ($validated, $customerId, $minimumPickupStartTime) {
            $cart = DB::table('carts')
                ->where('id', $validated['cart_id'])
                ->where('customer_id', $customerId)
                ->first();

            if (! $cart) {
                abort(403, 'Keranjang tidak valid.');
            }

            $pickupSlot = DB::table('canteen_pickup_slots')
                ->join('pickup_slot_options', 'canteen_pickup_slots.pickup_slot_option_id', '=', 'pickup_slot_options.id')
                ->select([
                    'canteen_pickup_slots.id',
                    'canteen_pickup_slots.canteen_id',
                    'canteen_pickup_slots.is_active',
                    'pickup_slot_options.is_active as option_is_active',
                    'pickup_slot_options.start_time',
                    'pickup_slot_options.end_time',
                ])
                ->where('canteen_pickup_slots.id', $validated['canteen_pickup_slot_id'])
                ->where('canteen_pickup_slots.canteen_id', $cart->canteen_id)
                ->where('pickup_slot_options.start_time', '>', $minimumPickupStartTime)
                ->first();

            if (! $pickupSlot || ! $pickupSlot->is_active || ! $pickupSlot->option_is_active) {
                abort(422, 'Waktu pengambilan tidak tersedia atau sudah terlalu dekat dari waktu sekarang.');
            }

            $items = DB::table('cart_items')
                ->join('menus', 'cart_items.menu_id', '=', 'menus.id')
                ->select([
                    'cart_items.menu_id',
                    'cart_items.quantity',
                    'menus.stock_qty',
                ])
                ->where('cart_items.cart_id', $cart->id)
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                abort(422, 'Keranjang masih kosong.');
            }

            foreach ($items as $item) {
                if ((int) $item->quantity > (int) $item->stock_qty) {
                    abort(422, 'Stok salah satu menu tidak mencukupi.');
                }
            }

            $orderId = DB::table('orders')->insertGetId([
                'customer_id' => $customerId,
                'canteen_id' => $cart->canteen_id,
                'canteen_pickup_slot_id' => $validated['canteen_pickup_slot_id'],
                'pickup_date' => now()->toDateString(),
                'status' => 'diproses',
                'note' => $validated['note'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($items as $item) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'menu_id' => $item->menu_id,
                    'quantity' => $item->quantity,
                ]);

                DB::table('menus')
                    ->where('id', $item->menu_id)
                    ->decrement('stock_qty', $item->quantity);
            }

            DB::table('cart_items')
                ->where('cart_id', $cart->id)
                ->delete();

            DB::table('carts')
                ->where('id', $cart->id)
                ->delete();
        });

        return redirect()
            ->route('pelanggan.keranjang')
            ->with('success', 'Checkout berhasil. Pesanan Anda sedang diproses.');
    }

    private function getCartPageData(Request $request): array
    {
        $customerId = auth()->id();

        $carts = DB::table('carts')
            ->join('canteens', 'carts.canteen_id', '=', 'canteens.id')
            ->select([
                'carts.id',
                'carts.canteen_id',
                'canteens.name as canteen_name',
            ])
            ->where('carts.customer_id', $customerId)
            ->orderBy('canteens.name')
            ->get();

        $cartIds = $carts->pluck('id')->all();

        $itemsByCart = collect();

        if (! empty($cartIds)) {
            $itemsByCart = DB::table('cart_items')
                ->join('menus', 'cart_items.menu_id', '=', 'menus.id')
                ->join('canteens', 'menus.canteen_id', '=', 'canteens.id')
                ->select([
                    'cart_items.id',
                    'cart_items.cart_id',
                    'cart_items.menu_id',
                    'cart_items.quantity',
                    'menus.name',
                    'menus.price',
                    'menus.image',
                    'menus.stock_qty',
                    'canteens.name as canteen_name',
                ])
                ->whereIn('cart_items.cart_id', $cartIds)
                ->orderBy('menus.name')
                ->get()
                ->map(function ($item) {
                    $item->line_total = (float) $item->price * (int) $item->quantity;
                    $item->formatted_price = $this->formatRupiah($item->price);
                    $item->formatted_line_total = $this->formatRupiah($item->line_total);

                    $item->image_url = $item->image
                        ? 'data:image/jpeg;base64,' . base64_encode($item->image)
                        : asset('images/nasi-gila.jpg');

                    return $item;
                })
                ->groupBy('cart_id');
        }

        $carts = $carts
            ->map(function ($cart) use ($itemsByCart) {
                $cart->items = $itemsByCart->get($cart->id, collect());
                $cart->item_count = $cart->items->sum('quantity');
                $cart->subtotal = $cart->items->sum('line_total');
                $cart->formatted_subtotal = $this->formatRupiah($cart->subtotal);

                return $cart;
            })
            ->filter(fn ($cart) => $cart->items->count() > 0)
            ->values();

        $requestedCartId = (int) ($request->input('cart_id') ?: $request->query('cart_id') ?: 0);

        $selectedCart = $carts->firstWhere('id', $requestedCartId) ?? $carts->first();

        $pickupSlots = collect();

        if ($selectedCart) {
            $minimumPickupStartTime = $this->minimumPickupStartTimeForToday();

            if ($minimumPickupStartTime) {
                $pickupSlots = DB::table('canteen_pickup_slots')
                    ->join('pickup_slot_options', 'canteen_pickup_slots.pickup_slot_option_id', '=', 'pickup_slot_options.id')
                    ->select([
                        'canteen_pickup_slots.id',
                        'pickup_slot_options.start_time',
                        'pickup_slot_options.end_time',
                        'canteen_pickup_slots.quota',
                    ])
                    ->where('canteen_pickup_slots.canteen_id', $selectedCart->canteen_id)
                    ->where('canteen_pickup_slots.is_active', true)
                    ->where('pickup_slot_options.is_active', true)
                    ->where('pickup_slot_options.start_time', '>', $minimumPickupStartTime)
                    ->orderBy('pickup_slot_options.start_time')
                    ->get()
                    ->map(function ($slot) {
                        $slot->formatted_time = $this->formatTime($slot->start_time) . '-' . $this->formatTime($slot->end_time);

                        return $slot;
                    });
            }
        }

        $subtotal = $selectedCart ? $selectedCart->subtotal : 0;
        $serviceFee = $subtotal > 0 ? $this->serviceFee : 0;
        $total = $subtotal + $serviceFee;

        return [
            'carts' => $carts,
            'selectedCart' => $selectedCart,
            'pickupSlots' => $pickupSlots,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee,
            'total' => $total,
            'formattedSubtotal' => $this->formatRupiah($subtotal),
            'formattedServiceFee' => $this->formatRupiah($serviceFee),
            'formattedTotal' => $this->formatRupiah($total),
        ];
    }

    private function cartJsonResponse(array $data, ?string $message = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'cart_list_html' => view('pelanggan.partials.cart-list', $data)->render(),
            'checkout_panel_html' => view('pelanggan.partials.checkout-panel', $data)->render(),
        ]);
    }

    private function failResponse(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->ajax()) {
            return response()->json([
                'message' => $message,
            ], 422);
        }

        return back()->with('error', $message);
    }

    private function minimumPickupStartTimeForToday(): ?string
    {
        $minimumPickupTime = now()->addMinutes(15);

        if (! $minimumPickupTime->isSameDay(now())) {
            return null;
        }

        return $minimumPickupTime->format('H:i:s');
    }

    private function formatRupiah(float|int|string $amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }

    private function formatTime(string $time): string
    {
        return str_replace(':', '.', substr($time, 0, 5));
    }
}
