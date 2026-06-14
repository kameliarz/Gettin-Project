<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $this->ensureAdmin();

        $selectedMonth = (int) $request->query('month', now()->month);
        $selectedYear = (int) $request->query('year', now()->year);
        $search = trim((string) $request->query('search', ''));

        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = now()->month;
        }

        if ($selectedYear < 2000 || $selectedYear > 2100) {
            $selectedYear = now()->year;
        }

        $periodStart = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $previousPeriodStart = $periodStart->copy()->subMonthNoOverflow()->startOfMonth();
        $previousPeriodEnd = $previousPeriodStart->copy()->endOfMonth();

        $currentTransactionTotal = $this->transactionCountForPeriod($periodStart, $periodEnd);
        $previousTransactionTotal = $this->transactionCountForPeriod($previousPeriodStart, $previousPeriodEnd);

        $currentSalesTotal = $this->salesForPeriod($periodStart, $periodEnd);
        $previousSalesTotal = $this->salesForPeriod($previousPeriodStart, $previousPeriodEnd);

        $transactions = $this->transactionsQuery($periodStart, $periodEnd, $search)
            ->paginate(4)
            ->withQueryString();

        $transactions->getCollection()->transform(function ($transaction) {
            $transaction->code = 'ORD-' . str_pad((string) $transaction->id, 5, '0', STR_PAD_LEFT);
            $transaction->formatted_total = $this->formatRupiah($transaction->total_price ?? 0);
            $transaction->pickup_time = $this->formatTime($transaction->start_time)
                . '-'
                . $this->formatTime($transaction->end_time);
            $transaction->formatted_date = $this->formatDateIndonesia($transaction->pickup_date);

            return $transaction;
        });

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'table' => view('admin.partials.transaction-table', [
                    'transactions' => $transactions,
                ])->render(),

                'pagination' => view('admin.partials.transaction-pagination', [
                    'transactions' => $transactions,
                ])->render(),
            ]);
        }

        return view('admin.dashboard', [
            'adminName' => auth()->user()->username ?? 'Admin',
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'monthOptions' => $this->monthOptions(),
            'yearOptions' => $this->yearOptions(),
            'search' => $search,

            'stats' => [
                'total_transactions' => $currentTransactionTotal,
                'total_sales' => $currentSalesTotal,
                'transaction_change' => $this->formatPercent(
                    $this->percentageChange($currentTransactionTotal, $previousTransactionTotal)
                ),
                'sales_change' => $this->formatPercent(
                    $this->percentageChange($currentSalesTotal, $previousSalesTotal)
                ),
            ],

            'topCanteens' => $this->topCanteens($periodStart, $periodEnd),
            'transactions' => $transactions,
        ]);
    }

    private function ensureAdmin(): void
    {
        abort_unless(
            auth()->check() && (auth()->user()->role ?? null) === 'admin',
            403,
            'Akses hanya untuk admin.'
        );
    }

    private function transactionCountForPeriod(Carbon $start, Carbon $end): int
    {
        return DB::table('orders')
            ->whereBetween('pickup_date', [$start->toDateString(), $end->toDateString()])
            ->count();
    }

    private function salesForPeriod(Carbon $start, Carbon $end): float
    {
        return (float) DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->whereBetween('orders.pickup_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('COALESCE(SUM(order_items.quantity * menus.price), 0) as total_sales')
            ->value('total_sales');
    }

    private function transactionsQuery(Carbon $start, Carbon $end, string $search)
    {
        $customerNameColumn = Schema::hasColumn('users', 'username')
            ? 'users.username'
            : 'users.name';

        $orderTotals = $this->orderTotalsSubquery();

        return DB::table('orders')
            ->join('users', 'orders.customer_id', '=', 'users.id')
            ->join('canteens', 'orders.canteen_id', '=', 'canteens.id')
            ->leftJoin('canteen_pickup_slots', 'orders.canteen_pickup_slot_id', '=', 'canteen_pickup_slots.id')
            ->leftJoin('pickup_slot_options', 'canteen_pickup_slots.pickup_slot_option_id', '=', 'pickup_slot_options.id')
            ->leftJoinSub($orderTotals, 'order_totals', function ($join) {
                $join->on('orders.id', '=', 'order_totals.order_id');
            })
            ->select([
                'orders.id',
                'orders.pickup_date',
                'orders.created_at',
                DB::raw($customerNameColumn . ' as customer_name'),
                'canteens.name as canteen_name',
                'pickup_slot_options.start_time',
                'pickup_slot_options.end_time',
                DB::raw('COALESCE(order_totals.total_price, 0) as total_price'),
            ])
            ->whereBetween('orders.pickup_date', [$start->toDateString(), $end->toDateString()])
            ->when($search !== '', function ($query) use ($search, $customerNameColumn) {
                $numericSearch = (int) preg_replace('/\D/', '', $search);

                $query->where(function ($subQuery) use ($search, $numericSearch, $customerNameColumn) {
                    $subQuery
                        ->where($customerNameColumn, 'like', '%' . $search . '%')
                        ->orWhere('canteens.name', 'like', '%' . $search . '%');

                    if ($numericSearch > 0) {
                        $subQuery->orWhere('orders.id', $numericSearch);
                    }
                });
            })
            ->orderByDesc('orders.pickup_date')
            ->orderBy('pickup_slot_options.start_time')
            ->orderByDesc('orders.created_at');
    }

    private function topCanteens(Carbon $start, Carbon $end)
    {
        $totalOrders = DB::table('orders')
            ->whereBetween('pickup_date', [$start->toDateString(), $end->toDateString()])
            ->count();

        if ($totalOrders === 0) {
            return collect();
        }

        return DB::table('orders')
            ->join('canteens', 'orders.canteen_id', '=', 'canteens.id')
            ->select([
                'canteens.name',
                DB::raw('COUNT(orders.id) as total_orders'),
            ])
            ->whereBetween('orders.pickup_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('canteens.id', 'canteens.name')
            ->orderByDesc('total_orders')
            ->limit(3)
            ->get()
            ->map(function ($canteen) use ($totalOrders) {
                $canteen->percentage = $totalOrders > 0
                    ? round(((int) $canteen->total_orders / $totalOrders) * 100)
                    : 0;

                return $canteen;
            });
    }

    private function orderTotalsSubquery()
    {
        return DB::table('order_items')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select([
                'order_items.order_id',
                DB::raw('SUM(order_items.quantity * menus.price) as total_price'),
            ])
            ->groupBy('order_items.order_id');
    }

    private function yearOptions(): array
    {
        $range = DB::table('orders')
            ->selectRaw('MIN(pickup_date) as min_date, MAX(pickup_date) as max_date')
            ->first();

        if (! $range || ! $range->min_date || ! $range->max_date) {
            return [now()->year];
        }

        $minYear = Carbon::parse($range->min_date)->year;
        $maxYear = Carbon::parse($range->max_date)->year;

        return range($maxYear, $minYear);
    }

    private function monthOptions(): array
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

    private function percentageChange(float|int $current, float|int $previous): float
    {
        if ((float) $previous === 0.0) {
            return (float) $current > 0 ? 100.0 : 0.0;
        }

        return (((float) $current - (float) $previous) / (float) $previous) * 100;
    }

    private function formatPercent(float $value): string
    {
        $prefix = $value >= 0 ? '+' : '';

        return $prefix . number_format($value, 1, '.', '') . '%';
    }

    private function formatRupiah(float|int|string $amount): string
    {
        return 'Rp' . number_format((float) $amount, 0, ',', '.');
    }

    private function formatTime(?string $time): string
    {
        if (! $time) {
            return '-';
        }

        return str_replace(':', '.', substr($time, 0, 5));
    }

    private function formatDateIndonesia(?string $date): string
    {
        if (! $date) {
            return '-';
        }

        $carbon = Carbon::parse($date);
        $months = $this->monthOptions();

        return $carbon->format('d') . ' ' . $months[(int) $carbon->format('m')] . ' ' . $carbon->format('Y');
    }
}
