@extends('layout.master')

@section('title','Add Book')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-book-plus"></i>
            </span>
            Add Book
        </h3>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ url('/books') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-control" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control" required>
                        </div>
                        <button class="btn btn-primary">Save</button>
                        <a href="{{ url('/books') }}" class="btn btn-link">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
