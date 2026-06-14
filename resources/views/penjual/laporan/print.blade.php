<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan {{ $formattedDate }}</title>
    <style>
        @page { size: A4; margin: 18mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #111827; margin: 0; background: #ffffff; }
        .paper { width: 100%; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; }
        h1 { margin: 0; font-size: 24px; }
        .date { margin-top: 6px; font-size: 16px; font-weight: 700; }
        .brand { display: flex; align-items: center; gap: 8px; font-size: 24px; font-weight: 900; }
        .brand img { width: 32px; height: 32px; object-fit: contain; }
        table { width: 100%; margin-top: 28px; border-collapse: collapse; table-layout: fixed; text-align: center; font-size: 13px; }
        th, td { border: 1px solid #777777; padding: 10px; }
        th { background: #f3f4f6; font-weight: 900; }
        tbody tr:nth-child(even) { background: #f7f7f7; }
        .summary { margin-top: 80px; margin-left: 28px; font-size: 17px; font-weight: 700; line-height: 1.8; }
        .summary-row { display: grid; grid-template-columns: 170px 16px 1fr; }
        .no-print { margin-bottom: 24px; }
        .print-button { height: 42px; padding: 0 22px; border: 1px solid #d1d5db; border-radius: 8px; background: #ffffff; font-weight: 700; cursor: pointer; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="print-button" onclick="window.print()">Print / Simpan PDF</button>
    </div>

    <main class="paper">
        <div class="header">
            <div>
                <h1>Laporan Penjualan - {{ $canteen->name }}</h1>
                <div class="date">{{ $formattedDate }}</div>
            </div>
            <div class="brand">
                <img src="{{ asset('images/gettin-icon.ico') }}" alt="Gettin">
                <span>Gettin</span>
            </div>
        </div>

        <table>
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
                        <td colspan="5">Belum ada data penjualan selesai pada tanggal ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row"><span>Total Pesanan</span><span>:</span><span>{{ $totals['orders'] }} pesanan</span></div>
            <div class="summary-row"><span>Total Item</span><span>:</span><span>{{ $totals['items'] }} item</span></div>
            <div class="summary-row"><span>Total Pendapatan</span><span>:</span><span>{{ $totals['formatted_revenue'] }}</span></div>
        </div>
    </main>

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
