@extends('layouts.app')

@section('title', 'Dashboard – Estimasi Servis Laptop')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <!-- Hero Card -->
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <div class="hero-icon mb-3"><i class="bi bi-laptop-fill"></i></div>
                <h1 class="h3 fw-bold" style="color:var(--primary)">Sistem Estimasi Biaya Servis Laptop</h1>
                <p class="text-muted mb-4">
                    Perkiraan biaya servis dihitung menggunakan metode <strong>Fuzzy Mamdani</strong>
                    berdasarkan kondisi laptop Anda.
                </p>
                <a href="{{ route('estimasi.form') }}" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-calculator me-2"></i>Mulai Estimasi
                </a>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <div class="h2 fw-bold text-primary">{{ $jumlahEstimasi }}</div>
                        <div class="text-muted small">Estimasi Anda</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <div class="h2 fw-bold" style="color:var(--accent)">3</div>
                        <div class="text-muted small">Variabel Input</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body py-4">
                        <div class="h2 fw-bold text-success">27</div>
                        <div class="text-muted small">Aturan Fuzzy</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cara Penggunaan -->
        <div class="card">
            <div class="card-header py-3" style="background:var(--primary)">
                <h5 class="mb-0 text-white"><i class="bi bi-info-circle me-2"></i>Cara Penggunaan</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2"
                             style="width:48px;height:48px;font-size:1.3rem">1</div>
                        <p class="mb-0 small">Klik <strong>Mulai Estimasi</strong> dan isi 3 data kondisi laptop</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2"
                             style="width:48px;height:48px;font-size:1.3rem">2</div>
                        <p class="mb-0 small">Sistem menghitung estimasi dengan <strong>Fuzzy Mamdani</strong></p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2"
                             style="width:48px;height:48px;font-size:1.3rem">3</div>
                        <p class="mb-0 small">Lihat <strong>hasil perkiraan biaya</strong> dan riwayat estimasi</p>
                    </div>
                </div>
                <div class="alert alert-perkiraan mt-3 mb-0 rounded">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Hasil yang ditampilkan adalah <strong>perkiraan</strong> berdasarkan data yang dimasukkan,
                    bukan harga pasti dari tempat servis.
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
