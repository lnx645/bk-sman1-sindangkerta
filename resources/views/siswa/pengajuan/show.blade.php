@extends('layouts.app')
@section('title', 'Detail Pengajuan')
@section('content')
<div class="page-head">
    <h1 class="h4 mb-1"><i class="bi bi-eye"></i> Detail Pengajuan</h1>
    <p class="mb-0">Status, jadwal, dan tindak lanjut pengajuan konsultasi Anda.</p>
</div>
<div class="card mb-3"><div class="card-header"><i class="bi bi-file-text"></i> Pengajuan Saya</div><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $pengajuan->kategori->nama ?? '-' }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">@include('partials.status-badge', ['status' => $pengajuan->status])</dd>
        <dt class="col-sm-3">Waktu Diminta</dt><dd class="col-sm-9">{{ $pengajuan->waktu_diminta->format('d/m/Y H:i') }}</dd>
        <dt class="col-sm-3">Guru BK</dt><dd class="col-sm-9">{{ $pengajuan->guruBk->name ?? 'Belum ditugaskan' }}</dd>
        <dt class="col-sm-3">Deskripsi</dt><dd class="col-sm-9">{{ $pengajuan->deskripsi }}</dd>
        @if($pengajuan->alasan_penolakan)<dt class="col-sm-3">Alasan Penolakan</dt><dd class="col-sm-9">{{ $pengajuan->alasan_penolakan }}</dd>@endif
        @if($pengajuan->catatan_reschedule)<dt class="col-sm-3">Catatan Reschedule</dt><dd class="col-sm-9">{{ $pengajuan->catatan_reschedule }}</dd>@endif
    </dl>
    @if($pengajuan->jadwal)
        <div class="highlight-box mt-3">
            <h2 class="h6 mb-1"><i class="bi bi-calendar-check"></i> Jadwal Final</h2>
            <p class="mb-0 fw-semibold">{{ $pengajuan->jadwal->waktu_final->format('d/m/Y H:i') }} — {{ $pengajuan->jadwal->metode }} @if($pengajuan->jadwal->tempat) di {{ $pengajuan->jadwal->tempat }} @endif</p>
        </div>
    @endif
    @if($pengajuan->catatanSesi && $pengajuan->catatanSesi->tindak_lanjut)
        <div class="highlight-box-green mt-3">
            <h2 class="h6 mb-1"><i class="bi bi-journal-check"></i> Tindak Lanjut untuk Anda</h2>
            <p class="mb-0">{{ $pengajuan->catatanSesi->tindak_lanjut }}</p>
        </div>
    @endif
</div></div>
@if(in_array($pengajuan->status, \App\Models\Pengajuan::CANCELLABLE, true))
    <form method="POST" action="{{ route('siswa.pengajuan.cancel', $pengajuan) }}" onsubmit="return confirm('Batalkan pengajuan ini?')">@csrf
        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-x-circle"></i> Batalkan Pengajuan</button>
    </form>
@endif
<a class="btn btn-outline-secondary mt-2" href="{{ route('siswa.pengajuan.index') }}"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection
