@extends('layout.master')

@section('title','Dashboard')

@section('content')
    @php
        $booksCount = \App\Models\Book::count();
        $categoriesCount = \App\Models\Category::count();
        $authorsCount = \App\Models\Book::distinct()->count('author');
        $recentBooks = \App\Models\Book::with('category')->latest()->take(6)->get();
    @endphp

    <style>
        .dash-wrap { --radius: 16px; }
        .hero-card {
            border: 0; border-radius: var(--radius);
            background: linear-gradient(135deg,#4f46e5,#7c3aed 55%,#a855f7);
            color: #fff; box-shadow: 0 10px 30px rgba(76, 29, 149, .25);
        }
        .hero-card .btn { border-radius: 10px; }

        .metric-card, .surface-card {
            border: 0; border-radius: var(--radius);
            box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
        }
        .metric-card { transition: .2s ease; }
        .metric-card:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(15, 23, 42, .12); }

        .metric-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        .label-chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 10px; border-radius: 999px;
            font-size: .78rem; font-weight: 600;
            background: #eef2ff; color: #4338ca;
        }
        .table-modern thead th {
            font-size: .76rem; text-transform: uppercase; letter-spacing: .6px;
            color: #64748b; border-bottom: 1px solid #e2e8f0;
        }
        .table-modern tbody td { border-color: #f1f5f9; vertical-align: middle; }
        .soft-chip {
            background: #f8fafc; border: 1px solid #e2e8f0; color: #475569;
            padding: 4px 9px; border-radius: 999px; font-size: .78rem;
        }
        .section-title { font-weight: 700; letter-spacing: .2px; }
    </style>

    <div class="dash-wrap">
        <div class="page-header">
            <h3 class="page-title mb-0">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-view-dashboard-outline"></i>
                </span>
                Dashboard
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item active" aria-current="page">Overview</li>
                </ul>
            </nav>
        </div>

        <div class="card hero-card mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h3 class="fw-bold mb-1 text-white">Library Insights</h3>
                        <p class="mb-0 opacity-75">Pantau koleksi, kategori, dan buku terbaru dengan tampilan profesional.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ url('/categories/create') }}" class="btn btn-light btn-sm fw-semibold">
                            <i class="mdi mdi-shape-plus me-1"></i>Add Category
                        </a>
                        <a href="{{ url('/books/create') }}" class="btn btn-dark btn-sm fw-semibold">
                            <i class="mdi mdi-book-plus me-1"></i>Add Book
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
                <div class="card metric-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Books</p>
                            <h3 class="fw-bold mb-1">{{ $booksCount }}</h3>
                            <span class="label-chip"><i class="mdi mdi-trending-up"></i>Collection Size</span>
                        </div>
                        <span class="metric-icon bg-primary bg-opacity-10 text-primary">
                            <i class="mdi mdi-book-open-page-variant"></i>
                        </span>
                        <a href="{{ url('/books') }}" class="stretched-link" aria-label="Books"></a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 stretch-card grid-margin">
                <div class="card metric-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Categories</p>
                            <h3 class="fw-bold mb-1">{{ $categoriesCount }}</h3>
                            <span class="label-chip" style="background:#ecfdf5;color:#047857;">
                                <i class="mdi mdi-shape-outline"></i>Catalog Structure
                            </span>
                        </div>
                        <span class="metric-icon bg-success bg-opacity-10 text-success">
                            <i class="mdi mdi-shape-outline"></i>
                        </span>
                        <a href="{{ url('/categories') }}" class="stretched-link" aria-label="Categories"></a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 stretch-card grid-margin">
                <div class="card metric-card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Unique Authors</p>
                            <h3 class="fw-bold mb-1">{{ $authorsCount }}</h3>
                            <span class="label-chip" style="background:#fffbeb;color:#b45309;">
                                <i class="mdi mdi-account-multiple-outline"></i>Author Diversity
                            </span>
                        </div>
                        <span class="metric-icon bg-warning bg-opacity-10 text-warning">
                            <i class="mdi mdi-account-multiple-outline"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card surface-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="card-title section-title mb-0">Collection Growth</h4>
                            <div id="visit-sale-chart-legend" class="rounded-legend legend-horizontal legend-top-right"></div>
                        </div>
                        <p class="text-muted mb-3">Pertumbuhan jumlah buku dari waktu ke waktu.</p>
                        <canvas id="visit-sale-chart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-5 grid-margin stretch-card">
                <div class="card surface-card">
                    <div class="card-body">
                        <h4 class="card-title section-title mb-1">Top Categories</h4>
                        <p class="text-muted mb-3">Distribusi buku berdasarkan kategori.</p>
                        <div class="doughnutjs-wrapper d-flex justify-content-center">
                            <canvas id="traffic-chart"></canvas>
                        </div>
                        <div id="traffic-chart-legend" class="rounded-legend legend-vertical legend-bottom-left pt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card surface-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title section-title mb-0">Recent Items</h4>
                            <a href="{{ url('/books') }}" class="btn btn-sm btn-outline-dark">
                                <i class="mdi mdi-format-list-bulleted me-1"></i>View All
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
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
                                            <td><span class="soft-chip">{{ $b->code }}</span></td>
                                            <td class="fw-semibold">{{ $b->title }}</td>
                                            <td>{{ $b->author }}</td>
                                            <td><span class="soft-chip">{{ $b->category?->name ?? 'Uncategorized' }}</span></td>
                                            <td>{{ $b->created_at?->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No recent books.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection