@extends('layout.master')

@section('title','Categories')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-format-list-bulleted"></i>
            </span>
            Categories
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Overview</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <p class="mb-1">Total Categories</p>
                    <h2 class="mb-0">{{ $categories->count() }}</h2>
                    <small class="opacity-75">Data kategori aktif di sistem</small>
                </div>
            </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1">Category Management</h4>
                        <p class="text-muted mb-0">Kelola kategori buku dengan struktur data yang rapi.</p>
                    </div>
                    <a href="{{ url('/categories/create') }}" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus me-1"></i>
                        Add Category
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Category List</h4>
                        <span class="badge badge-gradient-primary">{{ $categories->count() }} Items</span>
                    </div>

                    @if($categories->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="mdi mdi-folder-outline" style="font-size: 36px;"></i>
                            </div>
                            <h5 class="mb-2">Belum ada kategori</h5>
                            <p class="text-muted mb-3">Tambahkan kategori pertama untuk mulai mengelola data buku.</p>
                            <a href="{{ url('/categories/create') }}" class="btn btn-primary btn-sm">Tambah Kategori</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="80">No</th>
                                        <th>Category Name</th>
                                        <th width="180" class="text-center">Status</th>
                                        <th width="190" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $index => $cat)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon icon-box-primary me-2">
                                                        <span class="mdi mdi-tag"></span>
                                                    </div>
                                                    <span class="fw-semibold">{{ $cat->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-outline-success">Active</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ url('/categories/' . $cat->id . '/edit') }}" class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="mdi mdi-pencil"></i>
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ url('/categories/' . $cat->id) }}" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="mdi mdi-delete"></i>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
