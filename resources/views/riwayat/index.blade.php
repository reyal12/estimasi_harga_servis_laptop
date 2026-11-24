@extends('layouts.app')

@section('title', 'Riwayat Estimasi')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background:var(--primary)">
                <h4 class="mb-0 text-white"><i class="bi bi-clock-history me-2"></i>Riwayat Estimasi</h4>
                <a href="{{ route('estimasi.form') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-plus me-1"></i>Estimasi Baru
                </a>
            </div>
            <div class="card-body p-0">

                @if($riwayat->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size:3rem;color:#ccc"></i>
                        <p class="text-muted mt-3">Belum ada riwayat estimasi.</p>
                        <a href="{{ route('estimasi.form') }}" class="btn btn-primary">
                            <i class="bi bi-calculator me-2"></i>Mulai Estimasi Pertama
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Kerusakan</th>
                                    <th class="text-center">Usia</th>
                                    <th class="text-center">Kesulitan</th>
                                    <th>Estimasi Biaya</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayat as $i => $item)
                                    @php
                                        $kat = strtolower($item->kategori_biaya);
                                        $bc  = match($kat) { 'murah'=>'badge-murah','sedang'=>'badge-sedang','mahal'=>'badge-mahal',default=>'bg-secondary' };
                                    @endphp
                                    <tr>
                                        <td class="ps-4 text-muted small">{{ $riwayat->firstItem() + $i }}</td>
                                        <td class="small">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">{{ number_format($item->tingkat_kerusakan, 1) }}</td>
                                        <td class="text-center">{{ number_format($item->usia_laptop, 1) }} thn</td>
                                        <td class="text-center">{{ number_format($item->kesulitan_perbaikan, 1) }}</td>
                                        <td class="fw-semibold">{{ $item->estimasi_biaya_formatted }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $bc }} rounded-pill">{{ $item->kategori_biaya }}</span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <a href="{{ route('riwayat.show', $item->id) }}"
                                               class="btn btn-sm btn-outline-primary me-1" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Hapus"
                                                    data-bs-toggle="modal" data-bs-target="#modalHapus"
                                                    data-id="{{ $item->id }}"
                                                    data-biaya="{{ $item->estimasi_biaya_formatted }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($riwayat->hasPages())
                        <div class="d-flex justify-content-center py-3">
                            {{ $riwayat->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title"><i class="bi bi-trash text-danger me-2"></i>Hapus Estimasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus estimasi <strong id="modalBiaya"></strong>?
                Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapus" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('modalHapus').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    const id    = btn.getAttribute('data-id');
    const biaya = btn.getAttribute('data-biaya');
    document.getElementById('modalBiaya').textContent = biaya;
    document.getElementById('formHapus').action = '/riwayat/' + id;
});
</script>
@endpush
