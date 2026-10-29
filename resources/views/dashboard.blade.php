@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
@php
$icons = ['total' => 'bi-inbox', 'menunggu' => 'bi-hourglass-split', 'disetujui' => 'bi-check-circle', 'selesai' => 'bi-journal-check', 'antrean' => 'bi-inbox', 'ditangani' => 'bi-person-check', 'diajukan' => 'bi-send'];
@endphp
<div class="page-head">
    <h1 class="h4 mb-1"><i class="bi bi-speedometer2"></i> Dashboard</h1>
    <p class="mb-0">Selamat datang, <strong>{{ $user->name }}</strong> ({{ $user->role }}). Sistem Informasi Bimbingan dan Konseling SMAN 1 Sindangkerta.</p>
</div>

<div class="row g-3 mb-4">
    @foreach($stats as $key => $value)
        @if(!is_object($value) && !is_array($value))
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card stat-card h-100"><div class="card-body d-flex gap-3 align-items-center">
                <span class="stat-icon"><i class="bi {{ $icons[$key] ?? 'bi-bar-chart' }}"></i></span>
                <span>
                    <span class="stat-label d-block">{{ str_replace('_', ' ', $key) }}</span>
                    <span class="stat-value">{{ $value }}</span>
                </span>
            </div></div>
        </div>
        @endif
    @endforeach
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
@if($user->isSiswa())
    <a class="btn btn-primary" href="{{ route('siswa.pengajuan.create') }}"><i class="bi bi-plus-circle"></i> Ajukan Konsultasi Baru</a>
    <a class="btn btn-outline-primary" href="{{ route('siswa.pengajuan.index') }}"><i class="bi bi-inbox"></i> Lihat Riwayat Saya</a>
@endif
@if($user->isGuruBk())
    <a class="btn btn-primary" href="{{ route('guru.pengajuan.index') }}"><i class="bi bi-journal-text"></i> Lihat Daftar Permohonan</a>
    <a class="btn btn-outline-primary" href="{{ route('guru.jadwal.index') }}"><i class="bi bi-calendar-check"></i> Lihat Jadwal Konsultasi</a>
@endif
@if($user->isAdmin())
    <a class="btn btn-primary" href="{{ route('admin.rekap.index') }}"><i class="bi bi-bar-chart"></i> Buka Rekapitulasi</a>
    <a class="btn btn-outline-primary" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Kelola Akun</a>
@endif
</div>

@if($user->isAdmin())
    <div class="card mb-4"><div class="card-header"><i class="bi bi-bar-chart"></i> Rekap per status (hanya jumlah, tanpa isi deskripsi)</div><div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            @foreach($stats['per_status'] ?? [] as $status => $jumlah)
                <span class="d-inline-flex align-items-center gap-2 border rounded-pill px-3 py-2">@include('partials.status-badge', ['status' => $status]) <strong>{{ $jumlah }}</strong></span>
            @endforeach
        </div>
    </div></div>
@endif

@if($pengajuan->count())
<div class="card"><div class="card-header"><i class="bi bi-clock-history"></i> Pengajuan terbaru</div><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>Tanggal</th><th>Siswa / Kategori</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($pengajuan as $p)
            <tr>
                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($user->isGuruBk()) {{ $p->siswa->name ?? '-' }} — @endif
                    {{ $p->kategori->nama ?? '-' }}
                </td>
                <td>@include('partials.status-badge', ['status' => $p->status])</td>
                <td>
                    @if($user->isSiswa()) <a class="btn btn-sm btn-outline-primary" href="{{ route('siswa.pengajuan.show', $p) }}"><i class="bi bi-eye"></i> Detail</a>
                    @elseif($user->isGuruBk()) <a class="btn btn-sm btn-outline-primary" href="{{ route('guru.pengajuan.show', $p) }}"><i class="bi bi-eye"></i> Tinjau</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div></div>
@endif
@endsection
