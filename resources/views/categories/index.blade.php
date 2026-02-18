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
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Category List</h4>
                        <a href="{{ url('/categories/create') }}" class="text-decoration-underline">Add</a>
                    </div>

                    <ul class="list-unstyled">
                        @forelse($categories as $cat)
                            <li class="mb-2">&bull; {{ $cat->name }}</li>
                        @empty
                            <li>No categories yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
