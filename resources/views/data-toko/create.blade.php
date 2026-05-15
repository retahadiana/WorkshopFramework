@extends('layout.master')

@section('title', 'Tambah Data Toko')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-store-plus"></i>
            </span>
            Tambah Data Toko
        </h3>
    </div>

    <div class="row">
        <div class="col-8 grid-margin">
            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('data-toko.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Toko</label>
                            <input type="text" name="nama_toko" class="form-control" value="{{ old('nama_toko') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Koordinat</label>
                            <div class="input-group">
                                <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Latitude" value="{{ old('latitude') }}">
                                <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Longitude" value="{{ old('longitude') }}">
                                <input type="hidden" name="accuracy" id="accuracy" value="{{ old('accuracy') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" id="btnAmbilLokasi" class="btn btn-outline-success btn-sm">Ambil Lokasi Sekarang</button>
                            <button type="submit" class="btn btn-gradient-primary btn-sm">Simpan</button>
                            <a href="{{ route('data-toko.index') }}" class="btn btn-light btn-sm">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-scripts')
<script>
    function showError(msg) { alert(msg); }

    document.addEventListener('DOMContentLoaded', function(){
        const btn = document.getElementById('btnAmbilLokasi');
        btn.addEventListener('click', function(){
            if (!navigator.geolocation) { showError('Geolocation tidak tersedia di browser Anda'); return; }

            btn.disabled = true;
            btn.innerText = 'Mencari...';

            const options = { enableHighAccuracy: true, timeout: 15000 };
            navigator.geolocation.getCurrentPosition(function(pos){
                document.getElementById('latitude').value = pos.coords.latitude;
                document.getElementById('longitude').value = pos.coords.longitude;
                document.getElementById('accuracy').value = pos.coords.accuracy || '';
                btn.disabled = false;
                btn.innerText = 'Ambil Lokasi Sekarang';
            }, function(err){
                btn.disabled = false;
                btn.innerText = 'Ambil Lokasi Sekarang';
                showError('Gagal mengambil lokasi: ' + err.message);
            }, options);
        });
    });
</script>
@endpush
