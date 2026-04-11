@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="card-title">Data Customer</h4>
                        <p class="card-description">Tabel menampilkan data Customer beserta foto yang disimpan.</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('customer.create.blob') }}" class="btn btn-primary">Tambah Customer 1</a>
                        <a href="{{ route('customer.create.path') }}" class="btn btn-secondary">Tambah Customer 2</a>
                    </div>
                </div>

                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Provinsi</th>
                                <th>Kota</th>
                                <th>Kecamatan</th>
                                <th>Kodepos</th>
                                <th>Metode</th>
                                <th>Foto</th>
                                <th>Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->nama }}</td>
                                    <td>{{ $customer->alamat }}</td>
                                    <td>{{ $customer->provinsi }}</td>
                                    <td>{{ $customer->kota }}</td>
                                    <td>{{ $customer->kecamatan }}</td>
                                    <td>{{ $customer->kodepos }}</td>
                                    <td>{{ $customer->storage_method === 'blob' ? 'BLOB' : 'File Path' }}</td>
                                    <td style="width:120px;">
                                        @if($customer->foto_path)
                                            <img src="{{ asset($customer->foto_path) }}" alt="Foto Customer" class="img-fluid rounded" style="max-height:100px; object-fit:cover;" />
                                        @elseif($customer->foto_blob)
                                            <img src="data:image/png;base64,{{ base64_encode($customer->foto_blob) }}" alt="Foto Customer" class="img-fluid rounded" style="max-height:100px; object-fit:cover;" />
                                        @else
                                            <span class="text-muted">Tidak ada foto</span>
                                        @endif
                                    </td>
                                    <td>{{ $customer->created_at ? $customer->created_at->format('d-m-Y H:i') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">Belum ada data customer.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
