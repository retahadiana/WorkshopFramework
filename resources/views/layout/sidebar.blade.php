<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ auth()->user()->name ?? 'Guest' }}</span>
                    <span class="text-secondary text-small">{{ auth()->user()->email ?? '' }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/categories') }}">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/books') }}">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-page-variant menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/barang') }}">
                <span class="menu-title">Barang</span>
                <i class="mdi mdi-package-variant-closed menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#customerMenu" aria-expanded="{{ request()->routeIs('customer.*') ? 'true' : 'false' }}" aria-controls="customerMenu">
                <span class="menu-title">Customer</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-account-plus menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('customer.*') ? 'show' : '' }}" id="customerMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.index') ? 'active' : '' }}" href="{{ route('customer.index') }}">Data Customer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.create.blob') ? 'active' : '' }}" href="{{ route('customer.create.blob') }}">Tambah Customer 1</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.create.path') ? 'active' : '' }}" href="{{ route('customer.create.path') }}">Tambah Customer 2</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('tugas.js') ? 'active' : '' }}" href="{{ route('tugas.js') }}">
                <span class="menu-title">Tugas JS</span>
                <i class="mdi mdi-code-tags menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('wilayah.index') ? 'active' : '' }}" href="{{ route('wilayah.index') }}">
                <span class="menu-title">Wilayah Indonesia</span>
                <i class="mdi mdi-map-marker-multiple menu-icon"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('kasir.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#kasirMenu" aria-expanded="{{ request()->routeIs('kasir.*') ? 'true' : 'false' }}" aria-controls="kasirMenu">
                <span class="menu-title">Kasir</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-cash-register menu-icon"></i>
            </a>
            <div class="collapse {{ request()->routeIs('kasir.*') ? 'show' : '' }}" id="kasirMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kasir.index') ? 'active' : '' }}" href="{{ route('kasir.index') }}">Kasir POS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('kasir.laporan') ? 'active' : '' }}" href="{{ route('kasir.laporan') }}">Laporan Kasir</a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#pdfMenu" aria-expanded="false" aria-controls="pdfMenu">
                <span class="menu-title">Generate PDF</span>
                <i class="menu-arrow"></i>
                <i class="mdi mdi-file-pdf-box menu-icon"></i>
            </a>
            <div class="collapse" id="pdfMenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/generate-pdf/certificate') }}">Sertifikat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/generate-pdf/invitation') }}">Undangan</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>

