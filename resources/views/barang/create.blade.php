@extends('layout.master')

@section('title', 'Tambah Barang')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-plus-box"></i>
            </span>
            Tambah Barang
        </h3>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/barang') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">ID Barang</label>
                            <input type="text" class="form-control" value="{{ old('id_barang', $nextIdBarang) }}" readonly>
                            <small class="text-muted">ID dibuat otomatis saat data disimpan.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu</label>
                            <input type="datetime-local" name="timestamp" class="form-control" value="{{ old('timestamp') }}" required>
                        </div>

                        <button class="btn btn-primary">Simpan</button>
                        <a href="{{ url('/barang') }}" class="btn btn-link">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
