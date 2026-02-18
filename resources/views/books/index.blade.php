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
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Books</h4>
                        <a href="{{ url('/books/create') }}" class="text-decoration-underline">Add Book</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($books as $book)
                                    <tr>
                                        <td>{{ $book->category?->name }}</td>
                                        <td>{{ $book->code }}</td>
                                        <td>{{ $book->title }}</td>
                                        <td>{{ $book->author }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No books yet.</td>
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
