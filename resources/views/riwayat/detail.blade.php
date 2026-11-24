@extends('layouts.app')

@section('title', 'Detail Estimasi – Servis Laptop')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        @php
            $kategori  = strtolower($estimasi->kategori_biaya);
            $badgeClass = match($kategori) { 'murah'=>'badge-murah','sedang'=>'badge-sedang','mahal'=>'badge-mahal',default=>'bg-secondary' };
            $textClass  = match($kategori) { 'murah'=>'text-murah','sedang'=>'text-sedang','mahal'=>'text-mahal',default=>'text-secondary' };
            $detail     = $estimasi->detail_fuzzy ?? [];
            $derajat    = $detail['derajat_keanggotaan'] ?? null;
            $aturanAktif = $detail['aturan_aktif'] ?? null;
        @endphp

        <!-- Ringkasan -->
        <div class="card mb-4">
            <div class="card-header py-3" style="background:var(--primary)">
                <h4 class="mb-0 text-white"><i class="bi bi-search me-2"></i>Detail Perhitungan Fuzzy</h4>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center border-end">
                        <div class="{{ $textClass }}" style="font-size:2.8rem;font-weight:800">
                            {{ $estimasi->estimasi_biaya_formatted }}
                        </div>
                        <span class="badge {{ $badgeClass }} fs-6 px-3 py-1 rounded-pill mt-1">
                            {{ $estimasi->kategori_biaya }}
                        </span>
                    </div>
                    <div class="col-md-6 ps-4 mt-3 mt-md-0">
                        <table class="table table-borderless table-sm mb-0">
                            <tr><td class="text-muted">Kerusakan</td><td class="fw-semibold">{{ number_format($estimasi->tingkat_kerusakan,1) }} / 100</td></tr>
                            <tr><td class="text-muted">Usia</td><td class="fw-semibold">{{ number_format($estimasi->usia_laptop,1) }} tahun</td></tr>
                            <tr><td class="text-muted">Kesulitan</td><td class="fw-semibold">{{ number_format($estimasi->kesulitan_perbaikan,1) }} / 10</td></tr>
                            <tr><td class="text-muted">Tanggal</td><td class="fw-semibold small">{{ $estimasi->created_at->format('d M Y, H:i') }}</td></tr>
                            <tr><td class="text-muted">Versi Fuzzy</td><td class="fw-semibold">v{{ $estimasi->versi_fuzzy }}</td></tr>
                        </table>
                    </div>
                </div>

                <div class="alert alert-perkiraan mt-3 mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Hasil ini merupakan <strong>perkiraan</strong> berdasarkan data yang dimasukkan dan
                    <strong>bukan harga pasti</strong> dari tempat servis.
                </div>
            </div>
        </div>

        @if($derajat)
        <!-- Derajat Keanggotaan -->
        <div class="card mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-bar-chart me-2"></i>Derajat Keanggotaan Input (Fuzzifikasi)
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-fuzzy mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Variabel</th>
                            <th class="text-center">Himpunan 1</th>
                            <th class="text-center">Himpunan 2</th>
                            <th class="text-center">Himpunan 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $varRows = [
                                ['label'=>'Tingkat Kerusakan', 'key'=>'kerusakan',  'sets'=>['Ringan','Sedang','Berat']],
                                ['label'=>'Usia Laptop',       'key'=>'usia',        'sets'=>['Muda','Sedang','Tua']],
                                ['label'=>'Kesulitan',         'key'=>'kesulitan',   'sets'=>['Mudah','Sedang','Sulit']],
                            ];
                        @endphp
                        @foreach($varRows as $row)
                            @php $vals = array_values($derajat[$row['key']] ?? []); @endphp
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $row['label'] }}</td>
                                @foreach($row['sets'] as $j => $setName)
                                    @php $v = $vals[$j] ?? 0; @endphp
                                    <td class="text-center">
                                        <span class="fw-semibold {{ $v > 0 ? 'text-primary' : 'text-muted' }}">
                                            {{ number_format($v, 4) }}
                                        </span>
                                        <div class="text-muted" style="font-size:.75rem">{{ $setName }}</div>
                                        <div class="progress mt-1" style="height:4px;width:80px;margin:0 auto">
                                            <div class="progress-bar bg-primary" style="width:{{ $v*100 }}%"></div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($aturanAktif)
        <!-- Aturan Aktif -->
        <div class="card mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-list-check me-2"></i>Aturan Fuzzy yang Aktif (α &gt; 0)
                    <span class="badge bg-secondary ms-2">{{ count($aturanAktif) }} aturan</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-fuzzy mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-center">No</th>
                            <th class="text-center">Kekuatan (α)</th>
                            <th class="text-center">Output</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aturanAktif as $rule)
                            @php
                                $bc2 = match(strtolower($rule['biaya'] ?? '')) {
                                    'murah'  => 'badge-murah',
                                    'sedang' => 'badge-sedang',
                                    'mahal'  => 'badge-mahal',
                                    default  => 'bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4 text-center fw-semibold">{{ $rule['no'] }}</td>
                                <td class="text-center fw-semibold text-primary">{{ number_format($rule['alpha'], 4) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $bc2 }} rounded-pill px-3">{{ $rule['biaya'] }}</span>
                                </td>
                                <td style="min-width:120px">
                                    <div class="progress" style="height:8px">
                                        <div class="progress-bar bg-primary"
                                             style="width:{{ ($rule['alpha'] ?? 0) * 100 }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Tombol -->
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('riwayat.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Riwayat
            </a>
            <a href="{{ route('estimasi.form') }}" class="btn btn-primary">
                <i class="bi bi-calculator me-2"></i>Estimasi Baru
            </a>
        </div>

    </div>
</div>
@endsection
