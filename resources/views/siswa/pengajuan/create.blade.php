@extends('layouts.app')
@section('title', 'Ajukan Konsultasi')
@section('content')
<div class="page-head">
<h1 class="h4 mb-1"><i class="bi bi-plus-circle"></i> Ajukan Konsultasi BK</h1>
<p class="mb-0">Deskripsi masalah Anda hanya dapat dilihat oleh Anda dan guru BK yang berwenang, bukan oleh siswa lain.</p>
</div>
<div class="card"><div class="card-header"><i class="bi bi-send"></i> Formulir Pengajuan</div><div class="card-body">
<form method="POST" action="{{ route('siswa.pengajuan.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="kategori_id"><i class="bi bi-tags"></i> Kategori Konsultasi</label>
        <select class="form-select" id="kategori_id" name="kategori_id" required>
            <option value="">— Pilih kategori —</option>
            @foreach($kategori as $k)<option value="{{ $k->id }}" @selected(old('kategori_id')==$k->id)>{{ $k->nama }}</option>@endforeach
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label" for="deskripsi"><i class="bi bi-chat-left-text"></i> Deskripsi Masalah (singkat)</label>
        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" maxlength="1000" required>{{ old('deskripsi') }}</textarea>
        <div class="form-text">Minimal 10 karakter, maksimal 1000 karakter.</div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="waktu_diminta"><i class="bi bi-clock"></i> Waktu yang Diinginkan</label>
        <input class="form-control" type="datetime-local" id="waktu_diminta" name="waktu_diminta" value="{{ old('waktu_diminta') }}" required>
        <div class="form-text">Guru BK dapat menyetujui waktu ini atau menjadwalkan ulang ke waktu lain.</div>
    </div>
    <div class="form-actions">
    <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i> Kirim Pengajuan</button>
    <a class="btn btn-outline-secondary" href="{{ route('siswa.pengajuan.index') }}">Batal</a>
    </div>
</form>
</div></div>
@endsection
