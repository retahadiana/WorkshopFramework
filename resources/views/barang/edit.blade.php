@extends('layout.master')

@section('title', 'Edit Barang')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-pencil-box-outline"></i>
            </span>
            Edit Barang
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

                    <form method="POST" action="{{ url('/barang/' . $barang->id_barang) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">ID Barang</label>
                            <input type="text" class="form-control" value="{{ $barang->id_barang }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $barang->nama) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" name="harga" class="form-control" value="{{ old('harga', $barang->harga) }}" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Waktu</label>
                            <input type="datetime-local" name="timestamp" class="form-control"
                                value="{{ old('timestamp', $barang->timestamp ? \Carbon\Carbon::parse($barang->timestamp)->format('Y-m-d\TH:i') : '') }}" required>
                        </div>

                        <button class="btn btn-primary">Update</button>
                        <a href="{{ url('/barang') }}" class="btn btn-link">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
