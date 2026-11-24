@extends('layouts.app')

@section('title', 'Form Estimasi – Servis Laptop')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="card">
            <div class="card-header py-3" style="background:var(--primary)">
                <h4 class="mb-0 text-white"><i class="bi bi-calculator me-2"></i>Estimasi Biaya Servis Laptop</h4>
            </div>
            <div class="card-body p-4">

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Mohon perbaiki kesalahan berikut sebelum melanjutkan.
                    </div>
                @endif

                <form method="POST" action="{{ route('estimasi.store') }}" id="estimasiForm" novalidate>
                    @csrf

                    {{-- ========== Tingkat Kerusakan ========== --}}
                    <div class="mb-4">
                        <label for="tingkat_kerusakan" class="form-label fw-semibold">
                            <i class="bi bi-tools me-1 text-danger"></i>Tingkat Kerusakan
                            <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range range-slider flex-grow-1"
                                   id="slider_kerusakan" min="0" max="100" step="1"
                                   value="{{ old('tingkat_kerusakan', 0) }}"
                                   oninput="syncInput('tingkat_kerusakan', this.value)">
                            <input type="number" class="form-control @error('tingkat_kerusakan') is-invalid @enderror"
                                   id="tingkat_kerusakan" name="tingkat_kerusakan"
                                   min="0" max="100" step="0.01"
                                   value="{{ old('tingkat_kerusakan', 0) }}"
                                   style="width:90px"
                                   oninput="syncSlider('slider_kerusakan', this.value)">
                        </div>
                        @error('tingkat_kerusakan')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="mt-2 p-3 bg-light rounded small">
                            <strong>Panduan:</strong><br>
                            <span class="text-success">0–30</span>: Gangguan kecil (lambat, perlu instal ulang/pembersihan)<br>
                            <span class="text-warning">31–70</span>: Kerusakan sedang (keyboard, layar, baterai, kipas – laptop masih menyala)<br>
                            <span class="text-danger">71–100</span>: Kerusakan berat (mati total, terkena cairan, dugaan motherboard)
                        </div>
                    </div>

                    {{-- ========== Usia Laptop ========== --}}
                    <div class="mb-4">
                        <label for="usia_laptop" class="form-label fw-semibold">
                            <i class="bi bi-calendar me-1 text-primary"></i>Usia Laptop (tahun)
                            <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range range-slider flex-grow-1"
                                   id="slider_usia" min="0" max="10" step="0.1"
                                   value="{{ old('usia_laptop', 0) }}"
                                   oninput="syncInput('usia_laptop', this.value)">
                            <input type="number" class="form-control @error('usia_laptop') is-invalid @enderror"
                                   id="usia_laptop" name="usia_laptop"
                                   min="0" max="10" step="0.1"
                                   value="{{ old('usia_laptop', 0) }}"
                                   style="width:90px"
                                   oninput="syncSlider('slider_usia', this.value)">
                        </div>
                        @error('usia_laptop')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="mt-2 p-3 bg-light rounded small">
                            <strong>Panduan:</strong> Hitung sejak tanggal pembelian.
                            Jika lebih dari 10 tahun, isi <strong>10</strong>.
                        </div>
                    </div>

                    {{-- ========== Kesulitan Perbaikan ========== --}}
                    <div class="mb-4">
                        <label for="kesulitan_perbaikan" class="form-label fw-semibold">
                            <i class="bi bi-wrench me-1 text-warning"></i>Tingkat Kesulitan Perbaikan
                            <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range range-slider flex-grow-1"
                                   id="slider_kesulitan" min="0" max="10" step="0.1"
                                   value="{{ old('kesulitan_perbaikan', 0) }}"
                                   oninput="syncInput('kesulitan_perbaikan', this.value)">
                            <input type="number" class="form-control @error('kesulitan_perbaikan') is-invalid @enderror"
                                   id="kesulitan_perbaikan" name="kesulitan_perbaikan"
                                   min="0" max="10" step="0.1"
                                   value="{{ old('kesulitan_perbaikan', 0) }}"
                                   style="width:90px"
                                   oninput="syncSlider('slider_kesulitan', this.value)">
                        </div>
                        @error('kesulitan_perbaikan')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="mt-2 p-3 bg-light rounded small">
                            <strong>Panduan:</strong><br>
                            <span class="text-success">0–3</span>: Perbaikan software atau komponen mudah diakses<br>
                            <span class="text-warning">4–7</span>: Perlu bongkar sebagian, komponen mudah didapat<br>
                            <span class="text-danger">8–10</span>: Perlu bongkar penuh, penyolderan, atau komponen langka
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="btnHitung">
                            <span id="btnText"><i class="bi bi-calculator me-2"></i>Hitung Estimasi</span>
                            <span id="btnLoading" class="d-none">
                                <span class="spinner-border spinner-border-sm me-2"></span>Menghitung…
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function syncInput(inputId, value) {
    document.getElementById(inputId).value = value;
}
function syncSlider(sliderId, value) {
    const v = parseFloat(value);
    const slider = document.getElementById(sliderId);
    if (!isNaN(v)) slider.value = v;
}
document.getElementById('estimasiForm').addEventListener('submit', function() {
    const btn = document.getElementById('btnHitung');
    btn.disabled = true;
    document.getElementById('btnText').classList.add('d-none');
    document.getElementById('btnLoading').classList.remove('d-none');
});
</script>
@endpush
