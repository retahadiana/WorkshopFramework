@extends('layout.master')

@section('title','Dashboard')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span>
            Dashboard
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Overview</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        @php
            $booksCount = \App\Models\Book::count();
            $categoriesCount = \App\Models\Category::count();
            $authorsCount = \App\Models\Book::distinct()->count('author');
            $recentBooks = \App\Models\Book::with('category')->orderBy('created_at','desc')->take(6)->get();
        @endphp

        <div class="col-md-4 stretch-card grid-margin">
            <div class="card card-img-holder">
                <div class="card-body">
                    <h6 class="card-title">Total Books</h6>
                    <div class="d-flex align-items-center">
                        <h2 class="mb-0 me-3">{{ $booksCount }}</h2>
                        <i class="mdi mdi-book-open-page-variant mdi-36px text-primary"></i>
                    </div>
                    <p class="text-muted mt-2">All books in the collection</p>
                    <a href="{{ url('/books') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <div class="card card-img-holder">
                <div class="card-body">
                    <h6 class="card-title">Categories</h6>
                    <div class="d-flex align-items-center">
                        <h2 class="mb-0 me-3">{{ $categoriesCount }}</h2>
                        <i class="mdi mdi-shape-outline mdi-36px text-success"></i>
                    </div>
                    <p class="text-muted mt-2">Book categories available</p>
                    <a href="{{ url('/categories') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="col-md-4 stretch-card grid-margin">
            <div class="card card-img-holder">
                <div class="card-body">
                    <h6 class="card-title">Unique Authors</h6>
                    <div class="d-flex align-items-center">
                        <h2 class="mb-0 me-3">{{ $authorsCount }}</h2>
                        <i class="mdi mdi-account-multiple-outline mdi-36px text-warning"></i>
                    </div>
                    <p class="text-muted mt-2">Distinct authors in collection</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="clearfix">
                        <h4 class="card-title float-start">Collection Growth</h4>
                        <div id="visit-sale-chart-legend" class="rounded-legend legend-horizontal legend-top-right float-end"></div>
                    </div>
                    <canvas id="visit-sale-chart" class="mt-4"></canvas>
                    <small class="text-muted">Shows number of books added over time.</small>
                </div>
            </div>
        </div>

        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Top Categories</h4>
                    <div class="doughnutjs-wrapper d-flex justify-content-center">
                        <canvas id="traffic-chart"></canvas>
                    </div>
                    <div id="traffic-chart-legend" class="rounded-legend legend-vertical legend-bottom-left pt-4"></div>
                    <small class="text-muted">Distribution of books by category.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Recent Items</h4>
                        <div>
                            <a href="{{ url('/categories/create') }}" class="btn btn-sm btn-outline-primary me-2">Add Category</a>
                            <a href="{{ url('/books/create') }}" class="btn btn-sm btn-primary">Add Book</a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBooks as $b)
                                    <tr>
                                        <td>{{ $b->code }}</td>
                                        <td>{{ $b->title }}</td>
                                        <td>{{ $b->author }}</td>
                                        <td>{{ $b->category?->name }}</td>
                                        <td>{{ $b->created_at?->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">No recent books.</td>
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
