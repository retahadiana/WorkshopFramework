@extends('layout.master')

@section('title','Books')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-book-open-page-variant"></i>
            </span>
            Books
        </h3>
    </div>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <p class="mb-1">Total Books</p>
                    <h2 class="mb-0">{{ $books->count() }}</h2>
                    <small class="opacity-75">Jumlah buku yang tercatat di sistem</small>
                </div>
            </div>
        </div>

        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1">Book Management</h4>
                        <p class="text-muted mb-0">Kelola data buku berdasarkan kategori, kode, dan penulis.</p>
                    </div>
                    <a href="{{ url('/books/create') }}" class="btn btn-gradient-primary btn-sm">
                        <i class="mdi mdi-plus me-1"></i>
                        Add Book
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
                        <h4 class="card-title mb-0">Book List</h4>
                        <span class="badge badge-gradient-primary">{{ $books->count() }} Items</span>
                    </div>

                    @if($books->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="mdi mdi-book-remove-outline" style="font-size: 36px;"></i>
                            </div>
                            <h5 class="mb-2">Belum ada data buku</h5>
                            <p class="text-muted mb-3">Tambahkan buku pertama untuk memulai pengelolaan pustaka.</p>
                            <a href="{{ url('/books/create') }}" class="btn btn-primary btn-sm">Tambah Buku</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="70">No</th>
                                        <th width="220">Category</th>
                                        <th width="180">Code</th>
                                        <th>Title</th>
                                        <th width="220">Author</th>
                                        <th width="190" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($books as $index => $book)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge badge-outline-primary">{{ $book->category?->name ?? 'Uncategorized' }}</span>
                                            </td>
                                            <td><span class="fw-semibold">{{ $book->code }}</span></td>
                                            <td>{{ $book->title }}</td>
                                            <td>{{ $book->author }}</td>
                                            <td class="text-center">
                                                <a href="{{ url('/books/' . $book->id . '/edit') }}" class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="mdi mdi-pencil"></i>
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ url('/books/' . $book->id) }}" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
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
