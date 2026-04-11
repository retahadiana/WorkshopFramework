@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Payment Success</h4>
                <p class="card-description">Transaksi berhasil. QR Code berikut adalah bukti pembayaran untuk id pesanan.</p>

                <div class="row mb-4">
                    <div class="col-md-6 text-center">
                        <div class="p-3 border rounded bg-light">
                            <img src="{{ $qrCodeDataUri }}" alt="QR Code ID Penjualan" style="max-width: 240px; width: 100%; height: auto;" />
                        </div>
                        <div class="mt-2 text-muted" style="font-size: 0.95rem;">Scan QR Code untuk mendapatkan ID Penjualan.</div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2"><strong>ID Penjualan:</strong> {{ $penjualan->id_penjualan }}</div>
                        <div class="mb-2"><strong>No Invoice:</strong> {{ $penjualan->no_invoice ?? '-' }}</div>
                        <div class="mb-2"><strong>Tanggal:</strong> {{ $penjualan->tanggal ? \Carbon\Carbon::parse($penjualan->tanggal)->format('d-m-Y H:i:s') : '-' }}</div>
                        <div class="mb-2"><strong>Total:</strong> Rp {{ number_format($penjualan->total, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $item)
                                <tr>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('kasir.index') }}" class="btn btn-outline-primary">Kembali ke Kasir</a>
                    <a href="{{ route('kasir.struk', ['id' => $penjualan->id_penjualan]) }}" class="btn btn-success" target="_blank">Cetak Struk</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
