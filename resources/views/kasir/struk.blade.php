<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $penjualan->no_invoice ?? ('#' . $penjualan->id_penjualan) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
            width: 320px;
            margin: 0 auto;
            padding: 12px;
        }
        .title {
            text-align: center;
            margin-bottom: 8px;
        }
        .title h3 { margin: 0 0 6px 0; }
        .meta { margin-bottom: 10px; }
        .meta div { margin-bottom: 2px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        th, td {
            border-bottom: 1px dashed #999;
            padding: 4px 0;
            text-align: left;
            vertical-align: top;
        }
        th.right, td.right { text-align: right; }
        .total {
            margin-top: 8px;
            font-size: 14px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }
        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 11px;
        }
        @media print {
            .no-print { display: none; }
            body { width: auto; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="title">
        <h3>TOKO POS</h3>
        <div>Struk Transaksi</div>
    </div>

    <div class="meta">
        <div>No Invoice: {{ $penjualan->no_invoice ?? '-' }}</div>
        <div>ID Penjualan: {{ $penjualan->id_penjualan }}</div>
        <div>Tanggal: {{ $penjualan->tanggal ? \Carbon\Carbon::parse($penjualan->tanggal)->format('d-m-Y H:i:s') : '-' }}</div>
    </div>

    <div style="text-align:center; margin-bottom:10px;">
        <img src="{{ $qrCodeDataUri }}" alt="QR Code ID Pesanan" style="width:160px; height:auto; display:inline-block;" />
        <div style="font-size:10px; margin-top:4px;">QR Code ID Pesanan: {{ $penjualan->id_penjualan }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th class="right">Qty</th>
                <th class="right">Harga</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $item)
                <tr>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <span>Total</span>
        <span>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</span>
    </div>

    <div class="footer">
        Terima kasih telah berbelanja
    </div>

    <div class="no-print" style="margin-top:14px; text-align:center;">
        <button onclick="window.print()">Print</button>
    </div>
</body>
</html>
