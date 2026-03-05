@extends('layout.master')

@section('title', 'Data Barang')

@push('page-styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endpush

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-package-variant-closed"></i>
            </span>
            Data Barang
        </h3>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Daftar Barang</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/barang/create') }}" class="btn btn-gradient-primary btn-sm">
                                <i class="mdi mdi-plus me-1"></i> Tambah Barang
                            </a>
                        </div>
                    </div>

                    <form id="formCetakLabel" action="/cetak-label" method="POST" class="mb-3">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">Koordinat X (1-5)</label>
                                <input type="number" name="koordinat_x" class="form-control" min="1" max="5" value="{{ old('koordinat_x', 1) }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Koordinat Y (1-8)</label>
                                <input type="number" name="koordinat_y" class="form-control" min="1" max="8" value="{{ old('koordinat_y', 1) }}" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="mdi mdi-printer me-1"></i> Cetak Label PDF
                                </button>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="tampilkan_grid" name="tampilkan_grid"
                                        {{ old('tampilkan_grid') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tampilkan_grid">
                                        Tampilkan Grid Bantu
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="tabelBarang" class="table table-bordered table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Cetak (Checkbox)</th>
                                    <th>ID Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $item)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="id_barang[]" value="{{ $item->id_barang }}" form="formCetakLabel">
                                        </td>
                                        <td>{{ $item->id_barang }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td>{{ $item->timestamp }}</td>
                                        <td>
                                            <a href="{{ url('/barang/' . $item->id_barang . '/edit') }}" class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ url('/barang/' . $item->id_barang) }}" class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
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

@push('page-scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tabelBarang').DataTable({
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: 0 },
                    { orderable: false, targets: 5 }
                ]
            });
        });
    </script>
@endpush
