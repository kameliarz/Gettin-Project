<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();

        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);

        $summary = $this->monthlySummary($canteen->id, $month, $year);

        return view('penjual.laporan.index', [
            'canteen' => $canteen,
            'month' => $month,
            'year' => $year,
            'months' => $this->months(),
            'years' => $this->years(),
            'dailyReports' => $summary['dailyReports'],
            'chartData' => $summary['chartData'],
            'totals' => $summary['totals'],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();

        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2020,2100'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $month = (int) ($validated['month'] ?? now()->month);
        $year = (int) ($validated['year'] ?? now()->year);
        $keyword = $validated['q'] ?? null;

        $summary = $this->monthlySummary($canteen->id, $month, $year, $keyword);

        return response()->json([
            'dailyReports' => $summary['dailyReports'],
            'chartData' => $summary['chartData'],
            'totals' => $summary['totals'],
        ]);
    }

    public function show(string $date): View
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();
        $reportDate = Carbon::createFromFormat('Y-m-d', $date);

        $detail = $this->dailyDetail($canteen->id, $date);

        return view('penjual.laporan.show', [
            'canteen' => $canteen,
            'date' => $date,
            'formattedDate' => $this->formatDate($reportDate),
            'items' => $detail['items'],
            'totals' => $detail['totals'],
        ]);
    }

    public function downloadCsv(string $date): StreamedResponse
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();
        $reportDate = Carbon::createFromFormat('Y-m-d', $date);
        $detail = $this->dailyDetail($canteen->id, $date);

        $filename = 'laporan-penjualan-' . $date . '.csv';

        return response()->streamDownload(function () use ($canteen, $reportDate, $detail) {
            $handle = fopen('php://output', 'w');

            // BOM agar karakter UTF-8 lebih aman saat dibuka di Excel.
            fwrite($handle, "\xEF\xBB\xBF");

            // Membantu Excel membaca delimiter titik koma.
            fwrite($handle, "sep=;\n");

            fputcsv($handle, ['Laporan Penjualan - ' . $canteen->name], ';');
            fputcsv($handle, [$this->formatDate($reportDate)], ';');
            fputcsv($handle, [], ';');

            fputcsv($handle, ['No', 'Menu', 'Harga', 'Jumlah', 'Subtotal'], ';');

            foreach ($detail['items'] as $index => $item) {
                fputcsv($handle, [
                    $index + 1,
                    $item->menu_name,
                    $item->formatted_price,
                    $item->quantity . ' item',
                    $item->formatted_subtotal,
                ], ';');
            }

            fputcsv($handle, [], ';');
            fputcsv($handle, ['Total Pesanan', $detail['totals']['orders'] . ' pesanan'], ';');
            fputcsv($handle, ['Total Item', $detail['totals']['items'] . ' item'], ';');
            fputcsv($handle, ['Total Pendapatan', $detail['totals']['formatted_revenue']], ';');

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadPdf(string $date): View
    {
        $this->ensureSeller();

        $canteen = $this->currentCanteen();
        $reportDate = Carbon::createFromFormat('Y-m-d', $date);
        $detail = $this->dailyDetail($canteen->id, $date);

        return view('penjual.laporan.print', [
            'canteen' => $canteen,
            'date' => $date,
            'formattedDate' => $this->formatDate($reportDate),
            'items' => $detail['items'],
            'totals' => $detail['totals'],
        ]);
    }

    private function monthlySummary(int $canteenId, int $month, int $year, ?string $keyword = null): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select([
                'orders.pickup_date',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_items.quantity) as total_items'),
                DB::raw('SUM(order_items.quantity * menus.price) as total_revenue'),
            ])
            ->where('orders.canteen_id', $canteenId)
            ->where('orders.status', 'selesai')
            ->whereBetween('orders.pickup_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->groupBy('orders.pickup_date')
            ->orderBy('orders.pickup_date');

        if ($keyword) {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('orders.pickup_date', 'like', "%{$keyword}%");

                if (is_numeric($keyword)) {
                    $subQuery->orWhere('orders.id', (int) $keyword);
                }
            });
        }

        $dailyReports = $query
            ->get()
            ->map(function ($row) {
                $date = Carbon::parse($row->pickup_date);

                return [
                    'id' => $date->format('Ymd'),
                    'date' => $date->toDateString(),
                    'formatted_date' => $this->formatDate($date),
                    'total_orders' => (int) $row->total_orders,
                    'total_items' => (int) $row->total_items,
                    'total_revenue' => (int) $row->total_revenue,
                    'formatted_revenue' => $this->formatRupiah($row->total_revenue),
                    'detail_url' => route('penjual.laporan.show', $date->toDateString()),
                ];
            })
            ->values();

        $chartData = $this->chartData($dailyReports, $startDate, $endDate);

        return [
            'dailyReports' => $dailyReports,
            'chartData' => $chartData,
            'totals' => [
                'orders' => $dailyReports->sum('total_orders'),
                'items' => $dailyReports->sum('total_items'),
                'revenue' => $dailyReports->sum('total_revenue'),
                'formatted_revenue' => $this->formatRupiah($dailyReports->sum('total_revenue')),
            ],
        ];
    }

    private function dailyDetail(int $canteenId, string $date): array
    {
        $items = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select([
                'menus.name as menu_name',
                'menus.price',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.quantity * menus.price) as subtotal'),
            ])
            ->where('orders.canteen_id', $canteenId)
            ->where('orders.status', 'selesai')
            ->whereDate('orders.pickup_date', $date)
            ->groupBy('menus.id', 'menus.name', 'menus.price')
            ->orderBy('menus.name')
            ->get()
            ->map(function ($item) {
                $item->price = (int) $item->price;
                $item->quantity = (int) $item->quantity;
                $item->subtotal = (int) $item->subtotal;
                $item->formatted_price = $this->formatRupiah($item->price);
                $item->formatted_subtotal = $this->formatRupiah($item->subtotal);

                return $item;
            });

        $totalOrders = DB::table('orders')
            ->where('canteen_id', $canteenId)
            ->where('status', 'selesai')
            ->whereDate('pickup_date', $date)
            ->count();

        return [
            'items' => $items,
            'totals' => [
                'orders' => $totalOrders,
                'items' => $items->sum('quantity'),
                'revenue' => $items->sum('subtotal'),
                'formatted_revenue' => $this->formatRupiah($items->sum('subtotal')),
            ],
        ];
    }

    private function chartData($dailyReports, Carbon $startDate, Carbon $endDate)
    {
        $reportsByDate = $dailyReports->keyBy('date');
        $maxRevenue = max((int) $dailyReports->max('total_revenue'), 1);

        $days = collect();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();
            $report = $reportsByDate->get($dateString);
            $revenue = $report['total_revenue'] ?? 0;

            $days->push([
                'day' => $date->format('d'),
                'date' => $dateString,
                'revenue' => $revenue,
                'formatted_revenue' => $this->formatRupiah($revenue),
                'height' => $revenue > 0 ? max(8, round(($revenue / $maxRevenue) * 100)) : 0,
            ]);
        }

        return $days;
    }

    private function months(): array
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    private function years(): array
    {
        $currentYear = now()->year;

        return range($currentYear - 3, $currentYear + 1);
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

    private function formatDate(Carbon $date): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $date->format('d') . ' ' . $months[(int) $date->format('n')] . ' ' . $date->format('Y');
    }

    private function formatRupiah(float|int|string $amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }
}
