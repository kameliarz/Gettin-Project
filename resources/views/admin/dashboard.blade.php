@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    <main class="mx-auto max-w-7xl px-6 py-10 lg:px-10">
        <section class="mb-8">
            <div class="mt-3 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-slate-950 md:text-4xl">
                        Dashboard Admin
                    </h1>
                </div>
            </div>
        </section>

        <section class="grid gap-5 lg:grid-cols-3">
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Total Transaksi
                        </p>

                        <p class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950">
                            {{ number_format($stats['total_transactions'], 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-500">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 3h6" />
                            <path d="M9 7h6" />
                            <path d="M9 11h6" />
                            <path d="M5 3h14v18l-3-2-2 2-2-2-2 2-2-2-3 2V3Z" />
                        </svg>
                    </div>
                </div>

                <div class="mt-6 inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                    {{ $stats['transaction_change'] }} dari bulan lalu
                </div>
            </article>

            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Total Penjualan
                        </p>

                        <p class="mt-4 text-4xl font-extrabold tracking-tight text-slate-950">
                            Rp{{ number_format($stats['total_sales'], 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-500">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 7h16" />
                            <path d="M4 12h16" />
                            <path d="M4 17h10" />
                            <path d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5Z" />
                        </svg>
                    </div>
                </div>

                <div class="mt-6 inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                    {{ $stats['sales_change'] }} dari bulan lalu
                </div>
            </article>

            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">
                            Kantin Terlaris
                        </p>

                        <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-slate-950">
                            @if ($topCanteens->isNotEmpty())
                                {{ $topCanteens->first()->name }}
                            @else
                                Belum Ada
                            @endif
                        </h2>
                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 text-orange-500">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20" />
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($topCanteens as $canteen)
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-4">
                                <p class="truncate text-sm font-bold text-slate-700">
                                    {{ $canteen->name }}
                                </p>

                                <p class="text-sm font-extrabold text-slate-950">
                                    {{ $canteen->percentage }}%
                                </p>
                            </div>

                            <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full rounded-full bg-orange-500"
                                    style="width: {{ $canteen->percentage }}%"
                                ></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm font-medium text-slate-500">
                            Belum ada transaksi pada periode ini.
                        </p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="mt-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 p-6">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-extrabold tracking-tight text-slate-950">
                            Riwayat Transaksi
                        </h2>
                    </div>
                </div>

                <form
                    id="transaction-filter-form"
                    method="GET"
                    action="{{ route('admin.dashboard') }}"
                    class="mt-6 grid gap-3 lg:grid-cols-[1fr_auto_auto_auto_auto]"
                >
                    <label class="relative">
                        <span class="sr-only">Cari transaksi</span>

                        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>

                        <input
                            id="transaction-search-input"
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari kode, pelanggan, atau kantin..."
                            class="h-11 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-11 pr-4 text-sm font-semibold text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100"
                        >
                    </label>

                    <select
                        name="month"
                        class="h-11 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                    >
                        @foreach ($monthOptions as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}" @selected($selectedMonth === $monthNumber)>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>

                    <select
                        name="year"
                        class="h-11 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none transition focus:border-orange-400 focus:ring-4 focus:ring-orange-100"
                    >
                        @foreach ($yearOptions as $year)
                            <option value="{{ $year }}" @selected($selectedYear === $year)>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    <button
                        type="submit"
                        class="h-11 rounded-2xl bg-orange-500 px-5 text-sm font-extrabold text-white shadow-sm transition hover:bg-orange-600 focus:outline-none focus:ring-4 focus:ring-orange-100"
                    >
                        Terapkan
                    </button>

                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="inline-flex h-11 items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-slate-600 transition hover:bg-slate-50"
                    >
                        Reset
                    </a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                Kode
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                Pelanggan
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                Kantin
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                Total Harga
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                Jam Pengambilan
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-extrabold uppercase tracking-wide text-slate-500">
                                Tanggal
                            </th>
                        </tr>
                    </thead>

                    <tbody id="transaction-table-body" class="divide-y divide-slate-100">
                        @forelse ($transactions as $transaction)
                            <tr class="transition hover:bg-orange-50/40">
                                <td class="whitespace-nowrap px-6 py-5 text-sm font-extrabold text-slate-800">
                                    {{ $transaction->code }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-5 text-sm font-bold text-slate-900">
                                    {{ $transaction->customer_name }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-5 text-sm font-bold text-slate-700">
                                    {{ $transaction->canteen_name }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-5 text-sm font-extrabold text-slate-950">
                                    {{ $transaction->formatted_total }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-5">
                                    <span class="inline-flex rounded-full bg-orange-50 px-3 py-1 text-xs font-extrabold text-orange-600">
                                        {{ $transaction->pickup_time }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-5 text-sm font-bold text-slate-600">
                                    {{ $transaction->formatted_date }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 7h16" />
                                            <path d="M4 12h16" />
                                            <path d="M4 17h10" />
                                        </svg>
                                    </div>

                                    <p class="mt-4 text-base font-extrabold text-slate-950">
                                        Belum ada transaksi
                                    </p>

                                    <p class="mt-1 text-sm font-medium text-slate-500">
                                        Coba ubah kata kunci pencarian, bulan, atau tahun.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="transaction-pagination">
                <div class="flex flex-col gap-4 border-t border-slate-100 bg-slate-50 px-6 py-5 md:flex-row md:items-center md:justify-between">
                    <p class="text-sm font-semibold text-slate-500">
                        @if ($transactions->total() > 0)
                            Menampilkan {{ $transactions->firstItem() }} sampai {{ $transactions->lastItem() }}
                            dari {{ number_format($transactions->total(), 0, ',', '.') }} transaksi
                        @else
                            Menampilkan 0 transaksi
                        @endif
                    </p>

                    @if ($transactions->lastPage() > 1)
                        <div class="flex items-center gap-2">
                            @if ($transactions->onFirstPage())
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-300">
                                    ‹
                                </span>
                            @else
                                <a
                                    href="{{ $transactions->previousPageUrl() }}"
                                    class="pagination-link inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-white"
                                >
                                    ‹
                                </a>
                            @endif

                            @php
                                $startPage = max(1, $transactions->currentPage() - 1);
                                $endPage = min($transactions->lastPage(), $transactions->currentPage() + 1);
                            @endphp

                            @for ($page = $startPage; $page <= $endPage; $page++)
                                <a
                                    href="{{ $transactions->url($page) }}"
                                    class="pagination-link inline-flex h-10 w-10 items-center justify-center rounded-xl text-sm font-extrabold transition
                                        {{ $transactions->currentPage() === $page ? 'bg-orange-500 text-white' : 'text-slate-600 hover:bg-white' }}"
                                >
                                    {{ $page }}
                                </a>
                            @endfor

                            @if ($transactions->hasMorePages())
                                <a
                                    href="{{ $transactions->nextPageUrl() }}"
                                    class="pagination-link inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 transition hover:bg-white"
                                >
                                    ›
                                </a>
                            @else
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-300">
                                    ›
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('transaction-filter-form');
            const searchInput = document.getElementById('transaction-search-input');
            const tableBody = document.getElementById('transaction-table-body');
            const paginationWrapper = document.getElementById('transaction-pagination');

            let searchTimer = null;
            let activeController = null;

            function buildUrl(pageUrl = null) {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);

                if (pageUrl) {
                    const clickedUrl = new URL(pageUrl);
                    const page = clickedUrl.searchParams.get('page');

                    if (page) {
                        params.set('page', page);
                    }
                } else {
                    params.delete('page');
                }

                return `${form.action}?${params.toString()}`;
            }

            async function fetchTransactions(pageUrl = null) {
                const url = buildUrl(pageUrl);

                if (activeController) {
                    activeController.abort();
                }

                activeController = new AbortController();

                tableBody.style.opacity = '0.45';
                paginationWrapper.style.opacity = '0.45';

                try {
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        signal: activeController.signal,
                    });

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data transaksi.');
                    }

                    const data = await response.json();

                    tableBody.innerHTML = data.table;
                    paginationWrapper.innerHTML = data.pagination;

                    window.history.replaceState({}, '', url);
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error(error);
                    }
                } finally {
                    tableBody.style.opacity = '1';
                    paginationWrapper.style.opacity = '1';
                }
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);

                searchTimer = setTimeout(function () {
                    fetchTransactions();
                }, 400);
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                fetchTransactions();
            });

            form.querySelectorAll('select').forEach(function (select) {
                select.addEventListener('change', function () {
                    fetchTransactions();
                });
            });

            document.addEventListener('click', function (event) {
                const paginationLink = event.target.closest('.pagination-link');

                if (!paginationLink) {
                    return;
                }

                event.preventDefault();

                fetchTransactions(paginationLink.href);
            });
        });
    </script>
@endsection
