@extends('layout.master')

@section('title', 'Data Toko')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-store"></i>
            </span>
            Data Toko
        </h3>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Daftar Toko</h4>
                        <a href="{{ route('data-toko.create') }}" class="btn btn-gradient-primary btn-sm">
                            <i class="mdi mdi-plus me-1"></i> Tambah Toko
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Barcode</th>
                                    <th>Nama Toko</th>
                                    <th>Alamat</th>
                                    <th>Koordinat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($toko as $item)
                                    @php
                                        $barcodeImage = 'https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=' . urlencode($item->barcode);
                                    @endphp
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td class="text-center">
                                            <img src="{{ $barcodeImage }}" alt="Barcode {{ $item->barcode }}" style="width: 78px; height: 78px; object-fit: contain; display:block; margin:0 auto 6px; border-radius: 0; background:#fff; padding:4px; border:1px solid #e5e7eb;">
                                            <small class="d-block text-muted text-break">{{ $item->barcode }}</small>
                                        </td>
                                        <td>{{ $item->nama_toko }}</td>
                                        <td>{{ $item->alamat }}</td>
                                        <td>{{ $item->latitude }}, {{ $item->longitude }}</td>
                                        <td>
                                            <a href="{{ route('data-toko.print', $item->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">Cetak</a>
                                            <a href="{{ route('data-toko.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form method="POST" action="{{ route('data-toko.destroy', $item->id) }}" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus toko ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
