@extends('layouts.app')

@section('title', 'Hasil Estimasi – Servis Laptop')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        @php
            $kategori = strtolower($estimasi->kategori_biaya);
            $badgeClass = match($kategori) {
                'murah' => 'badge-murah',
                'sedang' => 'badge-sedang',
                'mahal' => 'badge-mahal',
                default => 'bg-secondary',
            };
            $textClass = match($kategori) {
                'murah' => 'text-murah',
                'sedang' => 'text-sedang',
                'mahal' => 'text-mahal',
                default => 'text-secondary',
            };
            $iconClass = match($kategori) {
                'murah' => 'bi-emoji-smile-fill',
                'sedang' => 'bi-emoji-neutral-fill',
                'mahal' => 'bi-emoji-frown-fill',
                default => 'bi-question',
            };
        @endphp

        <!-- Hasil Utama -->
        <div class="card mb-4">
            <div class="card-header py-3 text-center" style="background:var(--primary)">
                <h4 class="mb-0 text-white"><i class="bi bi-check-circle me-2"></i>Hasil Estimasi</h4>
            </div>
            <div class="card-body text-center py-5">
                <div class="{{ $textClass }}" style="font-size:3.5rem;font-weight:800;line-height:1">
                    {{ $estimasi->estimasi_biaya_formatted }}
                </div>
                <div class="mt-3">
                    <span class="badge {{ $badgeClass }} fs-5 px-4 py-2 rounded-pill">
                        <i class="bi {{ $iconClass }} me-2"></i>{{ $estimasi->kategori_biaya }}
                    </span>
                </div>

                <!-- Keterangan perkiraan (FR-12) -->
                <div class="alert alert-perkiraan mt-4 mx-auto text-start" style="max-width:480px">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Perhatian:</strong> Hasil ini merupakan <strong>perkiraan</strong> berdasarkan data
                    yang dimasukkan dan <strong>bukan harga pasti</strong> dari tempat servis.
                </div>
            </div>
        </div>

        <!-- Data Input -->
        <div class="card mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard-data me-2"></i>Data yang Dimasukkan</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td class="ps-4 text-muted" width="50%">Tingkat Kerusakan</td>
                            <td class="fw-semibold">{{ number_format($estimasi->tingkat_kerusakan, 1) }} / 100</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">Usia Laptop</td>
                            <td class="fw-semibold">{{ number_format($estimasi->usia_laptop, 1) }} tahun</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">Tingkat Kesulitan Perbaikan</td>
                            <td class="fw-semibold">{{ number_format($estimasi->kesulitan_perbaikan, 1) }} / 10</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">Dihitung pada</td>
                            <td class="fw-semibold">{{ $estimasi->created_at->format('d M Y, H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted">Versi Parameter Fuzzy</td>
                            <td class="fw-semibold">v{{ $estimasi->versi_fuzzy }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('riwayat.show', $estimasi->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-search me-2"></i>Lihat Detail Fuzzy
            </a>
            <a href="{{ route('estimasi.form') }}" class="btn btn-primary">
                <i class="bi bi-arrow-repeat me-2"></i>Estimasi Lagi
            </a>
            <a href="{{ route('riwayat.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-2"></i>Riwayat
            </a>
        </div>

    </div>
</div>
@endsection
