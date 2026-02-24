@extends('layout.master')

@section('title','Generate PDF - Undangan')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-file-pdf-box"></i>
            </span>
            Generate PDF - Undangan
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">Tools</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Generate File Undangan (A4 portrait)</h4>
                    <p class="text-muted">Buat file undangan.</p>

                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/generate-pdf/invitation') }}" class="mb-3">
                        @csrf
                        <button class="btn btn-primary" type="submit">
                            <i class="mdi mdi-file-pdf-box me-1"></i>
                            Generate Undangan
                        </button>
                        <a class="btn btn-outline-secondary ms-2" href="{{ url('/books') }}">
                            Lihat Data Buku
                        </a>
                    </form>

                    <div class="d-flex flex-column gap-2">
                        <div>
                            <span class="me-2">Undangan (A4 portrait):</span>
                            @if ($exists)
                                <a class="btn btn-sm btn-outline-success" href="{{ url('/generate-pdf/download/invitation') }}">
                                    Download
                                </a>
                            @else
                                <span class="text-muted">Belum dibuat</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
