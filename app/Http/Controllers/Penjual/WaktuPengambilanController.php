<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WaktuPengambilanController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();

        return view('penjual.waktu', [
            'canteen' => $canteen,
            'slotOptions' => $this->slotOptions(),
            'slots' => $this->slotList($canteen->id, $request->query('q')),
            'query' => $request->query('q', ''),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();

        return response()->json([
            'slots' => $this->slotList($canteen->id, $request->query('q')),
        ]);
    }

    public function show(int $slot): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();
        $slotData = $this->slotById($slot, $canteen->id);

        abort_if(! $slotData, 404, 'Waktu pengambilan tidak ditemukan.');

        return response()->json([
            'slot' => $slotData,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();
        $validated = $this->validatedPayload($request, $canteen->id);

        $slotId = DB::table('canteen_pickup_slots')->insertGetId([
            'canteen_id' => $canteen->id,
            'pickup_slot_option_id' => $validated['pickup_slot_option_id'],
            'quota' => $validated['quota'],
            'is_active' => $request->boolean('is_active'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Waktu pengambilan berhasil ditambahkan.',
            'slot' => $this->slotById($slotId, $canteen->id),
        ], 201);
    }

    public function update(Request $request, int $slot): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();

        $existingSlot = DB::table('canteen_pickup_slots')
            ->where('id', $slot)
            ->where('canteen_id', $canteen->id)
            ->first();

        abort_if(! $existingSlot, 404, 'Waktu pengambilan tidak ditemukan.');

        $validated = $this->validatedPayload($request, $canteen->id, $slot);

        DB::table('canteen_pickup_slots')
            ->where('id', $slot)
            ->where('canteen_id', $canteen->id)
            ->update([
                'pickup_slot_option_id' => $validated['pickup_slot_option_id'],
                'quota' => $validated['quota'],
                'is_active' => $request->boolean('is_active'),
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Waktu pengambilan berhasil diperbarui.',
            'slot' => $this->slotById($slot, $canteen->id),
        ]);
    }

    private function validatedPayload(Request $request, int $canteenId, ?int $ignoreSlotId = null): array
    {
        $uniqueSlotRule = Rule::unique('canteen_pickup_slots', 'pickup_slot_option_id')
            ->where(fn ($query) => $query->where('canteen_id', $canteenId));

        if ($ignoreSlotId) {
            $uniqueSlotRule->ignore($ignoreSlotId);
        }

        return $request->validate([
            'pickup_slot_option_id' => [
                'required',
                'integer',
                Rule::exists('pickup_slot_options', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
                $uniqueSlotRule,
            ],
            'quota' => ['required', 'integer', 'min:1', 'max:999'],
            'is_active' => ['required', 'boolean'],
        ], [
            'pickup_slot_option_id.unique' => 'Rentang waktu ini sudah ditambahkan untuk kantin Anda.',
            'pickup_slot_option_id.exists' => 'Rentang waktu tidak valid atau tidak aktif.',
            'quota.min' => 'Kuota minimal 1.',
        ]);
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

    private function slotOptions()
    {
        return DB::table('pickup_slot_options')
            ->select(['id', 'start_time', 'end_time'])
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get()
            ->map(function ($slot) {
                $slot->id = (int) $slot->id;
                $slot->formatted_time = $this->formatTime($slot->start_time)
                    . '-'
                    . $this->formatTime($slot->end_time);

                return $slot;
            });
    }

    private function slotList(int $canteenId, ?string $keyword = null)
    {
        $query = DB::table('canteen_pickup_slots')
            ->join(
                'pickup_slot_options',
                'canteen_pickup_slots.pickup_slot_option_id',
                '=',
                'pickup_slot_options.id'
            )
            ->select([
                'canteen_pickup_slots.id',
                'canteen_pickup_slots.pickup_slot_option_id',
                'canteen_pickup_slots.quota',
                'canteen_pickup_slots.is_active',
                'pickup_slot_options.start_time',
                'pickup_slot_options.end_time',
            ])
            ->where('canteen_pickup_slots.canteen_id', $canteenId);

        if ($keyword) {
            $keyword = trim($keyword);
            $timeKeyword = str_replace('.', ':', $keyword);
            $normalizedKeyword = str_replace([' ', '-', '_'], '', strtolower($keyword));

            $looksLikeTime = str_contains($keyword, '.') || str_contains($keyword, ':');

            $query->where(function ($subQuery) use ($keyword, $timeKeyword, $normalizedKeyword, $looksLikeTime) {
                if ($looksLikeTime) {
                    $subQuery
                        ->where('pickup_slot_options.start_time', 'like', "%{$timeKeyword}%")
                        ->orWhere('pickup_slot_options.end_time', 'like', "%{$timeKeyword}%");

                    return;
                }

                if (ctype_digit($keyword)) {
                    $subQuery
                        ->orWhere('canteen_pickup_slots.id', (int) $keyword)
                        ->orWhere('canteen_pickup_slots.quota', (int) $keyword);
                }

                $subQuery
                    ->orWhere('pickup_slot_options.start_time', 'like', "%{$timeKeyword}%")
                    ->orWhere('pickup_slot_options.end_time', 'like', "%{$timeKeyword}%");

                if (in_array($normalizedKeyword, ['aktif', 'active'], true)) {
                    $subQuery->orWhere('canteen_pickup_slots.is_active', true);
                }

                if (in_array($normalizedKeyword, ['nonaktif', 'inactive', 'tidakaktif'], true)) {
                    $subQuery->orWhere('canteen_pickup_slots.is_active', false);
                }
            });
        }

        return $query
            ->orderBy('pickup_slot_options.start_time')
            ->get()
            ->map(fn ($slot) => $this->decorateSlot($slot))
            ->values();
    }

    private function slotById(int $slotId, int $canteenId): ?object
    {
        $slot = DB::table('canteen_pickup_slots')
            ->join(
                'pickup_slot_options',
                'canteen_pickup_slots.pickup_slot_option_id',
                '=',
                'pickup_slot_options.id'
            )
            ->select([
                'canteen_pickup_slots.id',
                'canteen_pickup_slots.pickup_slot_option_id',
                'canteen_pickup_slots.quota',
                'canteen_pickup_slots.is_active',
                'pickup_slot_options.start_time',
                'pickup_slot_options.end_time',
            ])
            ->where('canteen_pickup_slots.id', $slotId)
            ->where('canteen_pickup_slots.canteen_id', $canteenId)
            ->first();

        return $slot ? $this->decorateSlot($slot) : null;
    }

    private function decorateSlot(object $slot): object
    {
        $slot->id = (int) $slot->id;
        $slot->pickup_slot_option_id = (int) $slot->pickup_slot_option_id;
        $slot->quota = (int) $slot->quota;
        $slot->is_active = (bool) $slot->is_active;

        $slot->formatted_time = $this->formatTime($slot->start_time)
            . '-'
            . $this->formatTime($slot->end_time);

        $slot->status_label = $slot->is_active ? 'Aktif' : 'Nonaktif';

        return $slot;
    }

    private function formatTime(?string $time): string
    {
        if (! $time) {
            return '-';
        }

        return str_replace(':', '.', substr($time, 0, 5));
    }
}
