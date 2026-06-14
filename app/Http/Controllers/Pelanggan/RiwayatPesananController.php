<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RiwayatPesananController extends Controller
{
    public function index(Request $request): View
    {
        $customerId = auth()->id();

        $statusOptions = [
            'diproses' => 'Diproses',
            'siap_ambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
        ];

        $selectedStatus = $request->query('status');
        $selectedMonth = $request->query('month');

        $monthOptions = DB::table('orders')
            ->where('customer_id', $customerId)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as value")
            ->distinct()
            ->orderByDesc('value')
            ->pluck('value')
            ->map(function ($value) {
                return [
                    'value' => $value,
                    'label' => $this->formatMonthYear($value),
                ];
            });

        $orders = DB::table('orders')
            ->join('canteens', 'orders.canteen_id', '=', 'canteens.id')
            ->leftJoin('canteen_pickup_slots', 'orders.canteen_pickup_slot_id', '=', 'canteen_pickup_slots.id')
            ->leftJoin('pickup_slot_options', 'canteen_pickup_slots.pickup_slot_option_id', '=', 'pickup_slot_options.id')
            ->select([
                'orders.id',
                'orders.status',
                'orders.pickup_date',
                'orders.created_at',
                'canteens.name as canteen_name',
                'canteens.qris_image',
                'pickup_slot_options.start_time',
                'pickup_slot_options.end_time',
            ])
            ->where('orders.customer_id', $customerId)
            ->when($selectedStatus, function ($query) use ($selectedStatus) {
                $query->where('orders.status', $selectedStatus);
            })
            ->when($selectedMonth, function ($query) use ($selectedMonth) {
                $query->whereRaw("DATE_FORMAT(orders.created_at, '%Y-%m') = ?", [$selectedMonth]);
            })
            ->orderByDesc('orders.created_at')
            ->get();

        $orderIds = $orders->pluck('id')->all();

        $itemsByOrder = collect();

        if (! empty($orderIds)) {
            $itemsByOrder = DB::table('order_items')
                ->join('menus', 'order_items.menu_id', '=', 'menus.id')
                ->select([
                    'order_items.order_id',
                    'order_items.quantity',
                    'menus.name',
                    'menus.price',
                ])
                ->whereIn('order_items.order_id', $orderIds)
                ->orderBy('menus.name')
                ->get()
                ->groupBy('order_id');
        }

        $orders = $orders->map(function ($order) use ($itemsByOrder, $statusOptions) {
            $items = $itemsByOrder->get($order->id, collect());

            $subtotal = $items->sum(function ($item) {
                return (float) $item->price * (int) $item->quantity;
            });

            $serviceFee = $subtotal > 0 ? 1000 : 0;
            $total = $subtotal + $serviceFee;

            $order->code = '#ORD-' . str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);
            $order->status_label = $statusOptions[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
            $order->status_style = $this->statusStyle($order->status);
            $order->items = $items;
            $order->total = $total;
            $order->formatted_total = $this->formatRupiah($total);
            $order->pickup_time = $this->formatPickupTime($order->start_time, $order->end_time);
            $order->formatted_date = $this->formatDate($order->pickup_date ?? $order->created_at);
            $order->progress_percent = $this->progressPercent($order->status);
            $order->timeline_color = in_array($order->status, ['selesai', 'dibatalkan'], true)
                ? 'bg-gray-400'
                : 'bg-orange-500';

            $order->qris_image_url = $order->qris_image
                ? 'data:image/jpeg;base64,' . base64_encode($order->qris_image)
                : null;

            return $order;
        });

        return view('pelanggan.riwayat-pemesanan', [
            'orders' => $orders,
            'statusOptions' => $statusOptions,
            'monthOptions' => $monthOptions,
            'selectedStatus' => $selectedStatus,
            'selectedMonth' => $selectedMonth,
        ]);
    }

    private function statusStyle(string $status): string
    {
        return match ($status) {
            'diproses' => 'bg-orange-50 text-orange-600',
            'siap_ambil' => 'bg-orange-50 text-orange-600',
            'selesai' => 'bg-stone-200 text-stone-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    private function progressPercent(string $status): int
    {
        return match ($status) {
            'diproses' => 35,
            'siap_ambil' => 65,
            'selesai' => 100,
            default => 35,
        };
    }

    private function formatRupiah(float|int|string $amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }

    private function formatPickupTime(?string $startTime, ?string $endTime): string
    {
        if (! $startTime || ! $endTime) {
            return '-';
        }

        return str_replace(':', '.', substr($startTime, 0, 5))
            . ' ~ '
            . str_replace(':', '.', substr($endTime, 0, 5));
    }

    private function formatMonthYear(string $value): string
    {
        [$year, $month] = explode('-', $value);

        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        return ($months[$month] ?? $month) . ' ' . $year;
    }

    private function formatDate(string $date): string
    {
        $timestamp = strtotime($date);

        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $dayName = $days[date('l', $timestamp)] ?? date('l', $timestamp);
        $day = date('d', $timestamp);
        $month = $months[date('m', $timestamp)] ?? date('m', $timestamp);
        $year = date('Y', $timestamp);

        return "{$dayName}, {$day} {$month} {$year}";
    }
}
