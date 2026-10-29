@extends('layouts.app')
@section('title', 'Tinjau Pengajuan')
@section('content')
<div class="page-head">
    <h1 class="h4 mb-1"><i class="bi bi-journal-text"></i> Tinjau Pengajuan</h1>
    <p class="mb-0">Periksa pengajuan, lalu pilih satu aksi di kartu bawah: tinjau, setujui, tolak, jadwalkan ulang, atau selesaikan dengan catatan sesi.</p>
</div>
<div class="card mb-4"><div class="card-header"><i class="bi bi-file-text"></i> Detail Pengajuan</div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Siswa</dt><dd class="col-sm-9">{{ $pengajuan->siswa->name ?? '-' }} ({{ $pengajuan->siswa->kelas->nama ?? '-' }})</dd>
        <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $pengajuan->kategori->nama ?? '-' }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">@include('partials.status-badge', ['status' => $pengajuan->status])</dd>
        <dt class="col-sm-3">Waktu Diminta</dt><dd class="col-sm-9">{{ $pengajuan->waktu_diminta->format('d/m/Y H:i') }}</dd>
        <dt class="col-sm-3">Deskripsi Masalah</dt><dd class="col-sm-9">{{ $pengajuan->deskripsi }}</dd>
    </dl>
    @if($pengajuan->jadwal)
        <div class="highlight-box mt-3">
            <h2 class="h6 mb-1"><i class="bi bi-calendar-check"></i> Jadwal Final</h2>
            <p class="mb-0 fw-semibold">{{ $pengajuan->jadwal->waktu_final->format('d/m/Y H:i') }} — {{ $pengajuan->jadwal->metode }} @if($pengajuan->jadwal->tempat) di {{ $pengajuan->jadwal->tempat }} @endif</p>
        </div>
    @endif
    @if($pengajuan->catatanSesi)
        <div class="highlight-box-green mt-3">
            <h2 class="h6 mb-1"><i class="bi bi-journal-check"></i> Catatan Sesi &amp; Tindak Lanjut</h2>
            <p class="mb-1"><strong>Catatan Sesi:</strong> {{ $pengajuan->catatanSesi->catatan }}</p>
            <p class="mb-0"><strong>Tindak Lanjut:</strong> {{ $pengajuan->catatanSesi->tindak_lanjut }}</p>
        </div>
    @endif
</div></div>

<div class="row g-3">
    <div class="col-md-6"><div class="card h-100"><div class="card-header"><i class="bi bi-check-circle"></i> Langkah 1 — Tinjau / Setujui</div><div class="card-body">
        <form method="POST" action="{{ route('guru.pengajuan.action', $pengajuan) }}" class="mb-3">@csrf<input type="hidden" name="aksi" value="tinjau"><button class="btn btn-info w-100" type="submit"><i class="bi bi-eye"></i> Tandai Ditinjau</button></form>
        <hr>
        <form method="POST" action="{{ route('guru.pengajuan.action', $pengajuan) }}">@csrf<input type="hidden" name="aksi" value="setujui">
            <div class="mb-2"><label class="form-label" for="waktu_final_setujui">Waktu Final</label><input class="form-control" type="datetime-local" id="waktu_final_setujui" name="waktu_final" required></div>
            <div class="mb-2"><label class="form-label" for="tempat_setujui">Tempat</label><input class="form-control" type="text" id="tempat_setujui" name="tempat" placeholder="Ruang BK"></div>
            <div class="mb-2"><label class="form-label" for="metode_setujui">Metode</label><select class="form-select" id="metode_setujui" name="metode"><option value="tatap_muka">Tatap Muka</option><option value="daring">Daring</option></select></div>
            <button class="btn btn-success w-100" type="submit"><i class="bi bi-check-lg"></i> Setujui &amp; Buat Jadwal</button>
        </form>
    </div></div></div>
    <div class="col-md-6"><div class="card h-100"><div class="card-header"><i class="bi bi-x-circle"></i> Langkah 1 (alternatif) — Tolak / Jadwalkan Ulang</div><div class="card-body">
        <form method="POST" action="{{ route('guru.pengajuan.action', $pengajuan) }}" class="mb-3">@csrf<input type="hidden" name="aksi" value="tolak">
            <div class="mb-2"><label class="form-label" for="alasan_penolakan">Alasan Penolakan</label><textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="2" required></textarea></div>
            <button class="btn btn-danger w-100" type="submit"><i class="bi bi-x-lg"></i> Tolak</button>
        </form>
        <hr>
        <form method="POST" action="{{ route('guru.pengajuan.action', $pengajuan) }}">@csrf<input type="hidden" name="aksi" value="jadwalkan_ulang">
            <div class="mb-2"><label class="form-label" for="waktu_final_ulang">Waktu Final Baru</label><input class="form-control" type="datetime-local" id="waktu_final_ulang" name="waktu_final" required></div>
            <div class="mb-2"><label class="form-label" for="catatan_reschedule">Catatan Reschedule</label><textarea class="form-control" id="catatan_reschedule" name="catatan_reschedule" rows="2" required></textarea></div>
            <button class="btn btn-warning w-100" type="submit"><i class="bi bi-calendar-event"></i> Jadwalkan Ulang</button>
        </form>
    </div></div></div>
    <div class="col-12"><div class="card"><div class="card-header"><i class="bi bi-journal-check"></i> Langkah 2 — Catatan Sesi &amp; Tindak Lanjut (menandai selesai)</div><div class="card-body">
        <form method="POST" action="{{ route('guru.pengajuan.action', $pengajuan) }}">@csrf<input type="hidden" name="aksi" value="selesai">
            <div class="mb-2"><label class="form-label" for="catatan">Catatan Sesi</label><textarea class="form-control" id="catatan" name="catatan" rows="3" required>{{ $pengajuan->catatanSesi->catatan ?? '' }}</textarea></div>
            <div class="mb-2"><label class="form-label" for="tindak_lanjut">Tindak Lanjut</label><textarea class="form-control" id="tindak_lanjut" name="tindak_lanjut" rows="2">{{ $pengajuan->catatanSesi->tindak_lanjut ?? '' }}</textarea></div>
            <button class="btn btn-primary" type="submit"><i class="bi bi-check2-all"></i> Simpan &amp; Tandai Selesai</button>
        </form>
    </div></div></div>
</div>
@include('partials.thread-pesan', ['pengajuan' => $pengajuan, 'routePesan' => route('guru.pengajuan.pesan.store', $pengajuan)])
<a class="btn btn-outline-secondary mt-3" href="{{ route('guru.pengajuan.index') }}"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection
