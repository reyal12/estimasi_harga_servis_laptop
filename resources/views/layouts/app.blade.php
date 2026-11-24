<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Estimasi Biaya Servis Laptop')</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #17365D;
            --primary-light: #1F4E79;
            --accent: #2E86AB;
            --murah: #198754;
            --sedang: #fd7e14;
            --mahal: #dc3545;
        }
        body { background-color: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .navbar-brand { font-weight: 700; letter-spacing: 0.5px; }
        .navbar { background: linear-gradient(135deg, var(--primary), var(--primary-light)); box-shadow: 0 2px 8px rgba(0,0,0,.2); }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,.08); }
        .card-header { border-radius: 12px 12px 0 0 !important; }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background-color: var(--primary-light); border-color: var(--primary-light); }
        .badge-murah  { background-color: var(--murah); }
        .badge-sedang { background-color: var(--sedang); }
        .badge-mahal  { background-color: var(--mahal); }
        .text-murah  { color: var(--murah); }
        .text-sedang { color: var(--sedang); }
        .text-mahal  { color: var(--mahal); }
        .footer { background-color: var(--primary); color: rgba(255,255,255,.7); }
        .hero-icon { font-size: 3rem; color: var(--accent); }
        .alert-perkiraan { background-color: #fff3cd; border-left: 4px solid #ffc107; color: #856404; }
        .table-fuzzy td, .table-fuzzy th { font-size: .85rem; vertical-align: middle; }
        .range-slider { accent-color: var(--primary); }
    </style>
    @stack('styles')
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-laptop me-2"></i>Estimasi Servis Laptop
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-house me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('estimasi.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('estimasi.form') }}">
                        <i class="bi bi-calculator me-1"></i>Estimasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('riwayat.*') ? 'active fw-semibold' : '' }}"
                       href="{{ route('riwayat.index') }}">
                        <i class="bi bi-clock-history me-1"></i>Riwayat
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content -->
<main class="container py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<!-- Footer -->
<footer class="footer py-3 mt-4">
    <div class="container text-center small">
        Sistem Estimasi Biaya Servis Laptop &bull; Metode Fuzzy Mamdani &bull;
        Hasil yang ditampilkan adalah <strong class="text-warning">perkiraan</strong>, bukan harga pasti.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
