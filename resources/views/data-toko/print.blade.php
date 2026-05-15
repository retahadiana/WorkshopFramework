@extends('layout.master')

@section('title', 'Cetak Barcode')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-printer"></i>
            </span>
            Cetak Barcode - {{ $item->nama_toko ?: $item->barcode }}
        </h3>
    </div>

    <div class="row">
        <div class="col-6 grid-margin">
            <div class="card">
                <div class="card-body text-center">
                    <h5>{{ $item->nama_toko }}</h5>
                    <p>{{ $item->alamat }}</p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($item->barcode) }}" alt="QR Code">
                    <p class="mt-2">{{ $item->barcode }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('page-scripts')
    <script>
        window.addEventListener('load', function(){ window.print(); });
    </script>
    @endpush
@endsection
