@extends('layouts.app')

@section('title', 'Gettin')

@section('content')
    <style>
        .report-detail-page { max-width: 1120px; margin: 0 auto; padding: 40px 24px 96px; }
        .report-actions { display: flex; justify-content: flex-end; gap: 24px; margin-bottom: 24px; }
        .download-button { display: inline-flex; height: 46px; align-items: center; justify-content: center; border: 1px solid #d1d5db; border-radius: 8px; padding: 0 24px; font-size: 16px; font-weight: 700; color: #374151; background: #ffffff; transition: 0.2s ease; }
        .download-button:hover { border-color: #ff7300; color: #ff7300; transform: translateY(-1px); }
        .report-paper { border: 1px solid #d7d7d7; border-radius: 32px; background: #ffffff; padding: 42px 48px; box-shadow: 0 4px 18px rgba(0, 0, 0, 0.13); min-height: 520px; }
        .report-header { display: flex; align-items: start; justify-content: space-between; gap: 24px; }
        .report-brand { display: flex; align-items: center; gap: 12px; font-size: 28px; font-weight: 900; }
        .report-brand img { width: 42px; height: 42px; object-fit: contain; }
        .report-table { width: 100%; margin-top: 32px; border-collapse: separate; border-spacing: 0; table-layout: fixed; text-align: center; font-size: 15px; }
        .report-table th, .report-table td { border-right: 1px solid #9ca3af; border-bottom: 1px solid #9ca3af; padding: 13px 14px; }
        .report-table th:first-child, .report-table td:first-child { border-left: 1px solid #9ca3af; }
        .report-table thead th { border-top: 1px solid #9ca3af; background: #f3f4f6; font-weight: 900; }
        .report-table tbody tr:nth-child(even) { background: #f7f7f7; }
        .report-summary { margin-top: 100px; margin-left: 50px; font-size: 22px; font-weight: 800; line-height: 1.9; }
        .summary-row { display: grid; grid-template-columns: 220px 20px 1fr; }
        @media (max-width: 800px) { .report-actions { justify-content: start; flex-wrap: wrap; } .report-paper { padding: 28px 24px; } .report-header { flex-direction: column; } .report-summary { margin-left: 0; font-size: 18px; } }
    </style>

    <section class="report-detail-page">
        <div class="report-actions">
            <a href="{{ route('penjual.laporan.csv', $date) }}" class="download-button">Unduh CSV</a>
            <a href="{{ route('penjual.laporan.pdf', $date) }}" class="download-button" target="_blank">Unduh PDF</a>
        </div>

        <article class="report-paper">
            <div class="report-header">
                <div>
                    <h1 class="text-3xl font-black text-gray-950">Laporan Penjualan - {{ $canteen->name }}</h1>
                    <p class="mt-2 text-xl font-black text-gray-950">{{ $formattedDate }}</p>
                </div>

                <div class="report-brand">
                    <img src="{{ asset('images/gettin-icon.ico') }}" alt="Gettin">
                    <span>Gettin</span>
                </div>
            </div>

            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 9%;">No</th>
                        <th>Menu</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>SubTotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->menu_name }}</td>
                            <td>{{ $item->formatted_price }}</td>
                            <td>{{ $item->quantity }} item</td>
                            <td>{{ $item->formatted_subtotal }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 font-bold text-gray-500">Belum ada data penjualan selesai pada tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="report-summary">
                <div class="summary-row"><span>Total Pesanan</span><span>:</span><span>{{ $totals['orders'] }} pesanan</span></div>
                <div class="summary-row"><span>Total Item</span><span>:</span><span>{{ $totals['items'] }} item</span></div>
                <div class="summary-row"><span>Total Pendapatan</span><span>:</span><span>{{ $totals['formatted_revenue'] }}</span></div>
            </div>
        </article>
    </section>
@endsection
