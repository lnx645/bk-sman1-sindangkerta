@extends('layouts.app')
@section('title', 'Rekapitulasi Layanan')
@section('content')
<div class="page-head">
    <h1 class="h4 mb-1"><i class="bi bi-bar-chart"></i> Rekapitulasi Layanan BK</h1>
    <p class="mb-0">Ringkasan jumlah layanan berdasarkan periode, status, kategori, dan kelas.</p>
</div>
<div class="alert alert-warning"><i class="bi bi-shield-lock"></i> Rekap ini <strong>hanya menampilkan jumlah/statistik</strong>. Isi deskripsi masalah siswa dan isi catatan sesi <strong>tidak ditampilkan</strong> di halaman ini, sesuai kewenangan admin.</div>
<form class="row g-2 mb-4" method="GET">
    <div class="col-md-3"><label class="form-label" for="bulan">Bulan</label><select class="form-select" id="bulan" name="bulan"><option value="">Semua bulan</option>@for($m=1;$m<=12;$m++)<option value="{{ $m }}" @selected((int)$bulan===$m)>Bulan {{ $m }}</option>@endfor</select></div>
    <div class="col-md-3"><label class="form-label" for="tahun">Tahun</label><input class="form-control" type="number" id="tahun" name="tahun" value="{{ $tahun }}" placeholder="Tahun, contoh: 2026"></div>
    <div class="col-md-3 d-flex align-items-end"><button class="btn btn-outline-primary w-100" type="submit"><i class="bi bi-funnel"></i> Filter Periode</button></div>
</form>
<div class="row g-3">
    <div class="col-12 col-md-6 col-lg-3"><div class="card stat-card stat-gold h-100"><div class="card-body"><span class="stat-label">TOTAL PENGAJUAN</span><div class="stat-value mt-1">{{ $total }}</div><p class="text-muted small mb-0">Seluruh pengajuan pada periode yang dipilih.</p></div></div></div>
    <div class="col-12 col-md-6 col-lg-3"><div class="card h-100"><div class="card-header"><i class="bi bi-flag"></i> Per Status</div><div class="card-body"><ul class="list-group list-group-flush">@foreach($perStatus as $s=>$j)<li class="list-group-item d-flex justify-content-between align-items-center px-0">@include('partials.status-badge', ['status' => $s]) <strong>{{ $j }}</strong></li>@endforeach</ul></div></div></div>
    <div class="col-12 col-md-6 col-lg-3"><div class="card h-100"><div class="card-header"><i class="bi bi-tags"></i> Per Kategori</div><div class="card-body"><ul class="list-group list-group-flush">@foreach($perKategori as $s=>$j)<li class="list-group-item d-flex justify-content-between align-items-center px-0">{{ $s }} <strong>{{ $j }}</strong></li>@endforeach</ul></div></div></div>
    <div class="col-12 col-md-6 col-lg-3"><div class="card h-100"><div class="card-header"><i class="bi bi-building"></i> Per Kelas</div><div class="card-body"><ul class="list-group list-group-flush">@foreach($perKelas as $s=>$j)<li class="list-group-item d-flex justify-content-between align-items-center px-0">{{ $s }} <strong>{{ $j }}</strong></li>@endforeach</ul></div></div></div>
</div>
@endsection
