@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .seller-report-page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 40px 24px 128px;
        }

        .report-top-layout {
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            gap: 24px;
            margin-top: 32px;
            align-items: stretch;
        }

        .report-panel,
        .report-table-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 28px;
            box-shadow: 0 8px 26px rgba(15, 23, 42, 0.08);
        }

        .report-filter-panel {
            padding: 22px;
        }

        .report-chart-panel {
            padding: 24px;
        }

        .report-input {
            width: 100%;
            height: 44px;
            border: 1px solid #cfd4dc;
            border-radius: 12px;
            padding: 0 14px;
            font-size: 14px;
            font-weight: 700;
            color: #374151;
            background: #ffffff;
        }

        .report-input:focus {
            outline: none;
            border-color: #ff7300;
            box-shadow: 0 0 0 3px rgba(255, 115, 0, 0.15);
        }

        .filter-label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 900;
            color: #111827;
        }

        .filter-help {
            margin-top: 14px;
            border-radius: 18px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            padding: 14px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.5;
            color: #9a3412;
        }

        .report-reset-button {
            display: flex;
            height: 44px;
            width: 100%;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #d1d5db;
            color: #111827;
            font-size: 14px;
            font-weight: 900;
            transition: 0.2s ease;
        }

        .report-reset-button:hover {
            background: #bfc5ce;
            transform: translateY(-1px);
        }

        .chart-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 20px;
            font-weight: 900;
            color: #111827;
        }

        .chart-subtitle {
            margin-top: 4px;
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
        }

        .chart-badge {
            border-radius: 999px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            padding: 8px 14px;
            color: #f97316;
            font-size: 13px;
            font-weight: 900;
            white-space: nowrap;
        }

        .chart-card-inner {
            border-radius: 0;
            border: 1px solid #d1d5db;
            background: #ffffff;
            padding: 22px 20px 16px;
        }

        .simple-bar-chart {
            height: 250px;
            display: flex;
            align-items: flex-end;
            gap: 7px;
            padding: 12px 8px 0;
            border-bottom: 2px solid #d1d5db;
            background:
                linear-gradient(to top, rgba(209, 213, 219, 0.7) 1px, transparent 1px);
            background-size: 100% 25%;
        }

        .simple-bar-item {
            flex: 1;
            min-width: 7px;
            height: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .simple-bar {
            width: 100%;
            max-width: 22px;
            border-radius: 0;
            background: #ff7300;
            box-shadow: none;
            transition: 0.2s ease;
        }

        .simple-bar.is-empty {
            height: 3px !important;
            background: #e5e7eb;
            box-shadow: none;
        }

        .simple-bar:hover {
            background: #111827;
            transform: translateY(-2px);
        }

        .simple-chart-labels {
            display: flex;
            gap: 7px;
            padding: 8px 8px 0;
        }

        .simple-chart-label {
            flex: 1;
            min-width: 7px;
            text-align: center;
            font-size: 10px;
            font-weight: 900;
            color: #6b7280;
        }

        .chart-empty {
            height: 250px;
            border-radius: 0;
            border: 1px dashed #d1d5db;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            font-weight: 800;
        }

        .report-table-card {
            margin-top: 32px;
            padding: 24px;
        }

        .table-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            align-items: center;
        }

        .table-reset-button {
            height: 44px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 0 20px;
            background: #ffffff;
            color: #374151;
            font-size: 14px;
            font-weight: 900;
            transition: 0.2s ease;
        }

        .table-reset-button:hover {
            border-color: #ff7300;
            color: #ff7300;
        }

        .report-table-wrap {
            margin-top: 22px;
            overflow: hidden;
            border: 1px solid #d1d5db;
            border-radius: 18px;
            background: #ffffff;
        }

        .report-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            text-align: center;
            font-size: 13px;
            background: #ffffff;
        }

        .report-table thead th {
            background: #ffffff;
            color: #111827;
            font-weight: 900;
            padding: 14px 10px;
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .report-table thead th:last-child {
            border-right: none;
        }

        .report-table tbody td {
            padding: 14px 10px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            color: #111827;
            font-weight: 700;
            vertical-align: middle;
        }

        .report-table tbody td:last-child {
            border-right: none;
        }

        .report-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        .report-table tbody tr:nth-child(even) {
            background: #fff7ed;
        }

        .report-table tbody tr:hover {
            background: #ffedd5;
        }

        .report-table tbody tr:last-child td {
            border-bottom: none;
        }

        .report-table th:nth-child(1),
        .report-table td:nth-child(1) {
            width: 15%;
        }

        .report-table th:nth-child(2),
        .report-table td:nth-child(2) {
            width: 20%;
        }

        .report-table th:nth-child(3),
        .report-table td:nth-child(3) {
            width: 18%;
        }

        .report-table th:nth-child(4),
        .report-table td:nth-child(4) {
            width: 16%;
        }

        .report-table th:nth-child(5),
        .report-table td:nth-child(5) {
            width: 19%;
        }

        .report-table th:nth-child(6),
        .report-table td:nth-child(6) {
            width: 12%;
        }

        .detail-button {
            display: inline-flex;
            height: 32px;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #111827;
            padding: 0 15px;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            transition: 0.2s ease;
        }

        .detail-button:hover {
            background: #ff7300;
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .report-top-layout {
                grid-template-columns: 1fr;
            }

            .table-toolbar {
                grid-template-columns: 1fr;
            }

            .report-table {
                font-size: 12px;
            }

            .report-table thead th,
            .report-table tbody td {
                padding: 12px 8px;
            }
        }
    </style>

    <section
        x-data="laporanPenjualanPage({
            dailyReports: @js($dailyReports),
            chartData: @js($chartData),
            totals: @js($totals),
            month: @js($month),
            year: @js($year),
            dataUrl: '{{ route('penjual.laporan.data') }}'
        })"
        class="seller-report-page"
    >
        <h1 class="text-3xl font-black tracking-tight text-gray-950 md:text-4xl">
            Laporan Penjualan
        </h1>

        <div class="report-top-layout">
            <aside class="report-panel report-filter-panel">
                <h2 class="text-xl font-black text-gray-950">
                    Filter Laporan
                </h2>

                <div class="mt-5">
                    <label class="filter-label">
                        Bulan
                    </label>

                    <select
                        x-model="month"
                        x-on:change="loadReports()"
                        class="report-input"
                    >
                        @foreach ($months as $monthNumber => $monthName)
                            <option value="{{ $monthNumber }}">
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <label class="filter-label">
                        Tahun
                    </label>

                    <select
                        x-model="year"
                        x-on:change="loadReports()"
                        class="report-input"
                    >
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}">
                                {{ $yearOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-help">
                    Filter ini mengatur data grafik dan daftar laporan berdasarkan bulan serta tahun yang dipilih.
                </div>

                <button
                    type="button"
                    x-on:click="resetFilter()"
                    class="report-reset-button mt-5"
                >
                    Reset
                </button>
            </aside>

            <section class="report-panel report-chart-panel">
                <div class="chart-header">
                    <div>
                        <h2 class="chart-title">
                            Grafik Pendapatan Harian
                        </h2>

                        <p class="chart-subtitle">
                            Data berdasarkan pesanan dengan status selesai.
                        </p>
                    </div>

                    <div class="chart-badge" x-text="totals.formatted_revenue"></div>
                </div>

                <template x-if="hasChartData">
                    <div class="chart-card-inner">
                        <div class="simple-bar-chart">
                            <template x-for="day in chartData" :key="day.date">
                                <div class="simple-bar-item">
                                    <div
                                        class="simple-bar"
                                        x-bind:class="Number(day.revenue) <= 0 ? 'is-empty' : ''"
                                        x-bind:style="'height: ' + barHeight(day) + '%'"
                                        x-bind:title="day.date + ' - ' + day.formatted_revenue"
                                    ></div>
                                </div>
                            </template>
                        </div>

                        <div class="simple-chart-labels">
                            <template x-for="day in chartData" :key="'label-' + day.date">
                                <div
                                    class="simple-chart-label"
                                    x-text="showDayLabel(day.day) ? day.day : ''"
                                ></div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="! hasChartData">
                    <div class="chart-empty">
                        Belum ada pendapatan pada bulan dan tahun ini.
                    </div>
                </template>
            </section>
        </div>

        <section class="report-table-card">
            <div class="table-toolbar">
                <input
                    type="text"
                    x-model="tableSearchQuery"
                    placeholder="Cari ID laporan atau tanggal..."
                    class="report-input"
                >

                <button
                    type="button"
                    x-on:click="tableSearchQuery = ''"
                    class="table-reset-button"
                >
                    Reset
                </button>
            </div>

            <div class="mt-4 flex items-center justify-between gap-4">
                <p class="text-base font-bold text-gray-700">
                    <span x-text="filteredReports.length"></span> laporan ditampilkan
                </p>

                <p x-cloak x-show="isLoading" class="text-sm font-black text-orange-600">
                    Memuat laporan...
                </p>
            </div>

            <div class="report-table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Total Pesanan</th>
                            <th>Total Menu</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <template x-for="report in filteredReports" :key="report.date">
                            <tr>
                                <td x-text="report.id"></td>
                                <td x-text="report.formatted_date"></td>
                                <td x-text="report.total_orders + ' pesanan'"></td>
                                <td x-text="report.total_items + ' item'"></td>
                                <td x-text="report.formatted_revenue"></td>
                                <td>
                                    <a
                                        x-bind:href="report.detail_url"
                                        class="detail-button"
                                    >
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        </template>

                        <template x-if="filteredReports.length === 0">
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center font-bold text-gray-500">
                                    Tidak ada laporan yang cocok.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </section>
    </section>

    <script>
        function laporanPenjualanPage(config) {
            return {
                dailyReports: config.dailyReports,
                chartData: config.chartData,
                totals: config.totals,

                month: String(config.month),
                year: String(config.year),

                tableSearchQuery: '',
                dataUrl: config.dataUrl,
                isLoading: false,

                get hasChartData() {
                    return this.chartData.some((day) => Number(day.revenue) > 0);
                },

                get maxRevenue() {
                    return Math.max(
                        ...this.chartData.map((day) => Number(day.revenue || 0)),
                        1
                    );
                },

                barHeight(day) {
                    const revenue = Number(day.revenue || 0);

                    if (revenue <= 0) {
                        return 1;
                    }

                    return Math.max(12, Math.round((revenue / this.maxRevenue) * 100));
                },

                showDayLabel(day) {
                    const dayNumber = Number(day);

                    return dayNumber === 1
                        || dayNumber % 5 === 0
                        || dayNumber === this.chartData.length;
                },

                get filteredReports() {
                    const keyword = this.tableSearchQuery.trim().toLowerCase();

                    if (! keyword) {
                        return this.dailyReports;
                    }

                    return this.dailyReports.filter((report) => {
                        return String(report.id).toLowerCase().includes(keyword)
                            || String(report.date).toLowerCase().includes(keyword)
                            || String(report.formatted_date).toLowerCase().includes(keyword);
                    });
                },

                async loadReports() {
                    this.isLoading = true;

                    try {
                        const url = new URL(this.dataUrl, window.location.origin);

                        url.searchParams.set('month', this.month);
                        url.searchParams.set('year', this.year);

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                            },
                        });

                        const data = await response.json();

                        if (! response.ok) {
                            throw new Error(data.message || 'Gagal memuat laporan.');
                        }

                        this.dailyReports = data.dailyReports;
                        this.chartData = data.chartData;
                        this.totals = data.totals;
                        this.tableSearchQuery = '';
                    } catch (error) {
                        alert(error.message);
                    } finally {
                        this.isLoading = false;
                    }
                },

                resetFilter() {
                    this.month = String(new Date().getMonth() + 1);
                    this.year = String(new Date().getFullYear());
                    this.tableSearchQuery = '';
                    this.loadReports();
                },
            };
        }
    </script>
@endsection
