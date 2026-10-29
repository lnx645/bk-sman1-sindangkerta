@extends('layouts.app')
@section('title', 'Daftar Permohonan')
@section('content')
<h1 class="h4"><i class="bi bi-journal-text"></i> Daftar Permohonan Konsultasi</h1>
<p class="text-muted">Menampilkan antrean yang belum bertuan dan pengajuan yang menjadi kewenangan Anda. Pengajuan milik guru BK lain tidak ditampilkan di sini.</p>
<form class="row g-2 mb-3" method="GET">
    <div class="col-auto"><select class="form-select" name="status" aria-label="Filter status"><option value="">Semua status</option>@foreach(\App\Models\Pengajuan::STATUSES as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>@endforeach</select></div>
    <div class="col-auto"><button class="btn btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i> Filter</button></div>
</form>
<div class="card"><div class="table-responsive"><table class="table table-striped align-middle mb-0">
    <thead><tr><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Kategori</th><th>Waktu Diminta</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse($pengajuan as $p)
        <tr><td>{{ $p->created_at->format('d/m/Y') }}</td><td>{{ $p->siswa->name ?? '-' }}</td><td>{{ $p->siswa->kelas->nama ?? '-' }}</td><td>{{ $p->kategori->nama ?? '-' }}</td><td>{{ $p->waktu_diminta->format('d/m/Y H:i') }}</td><td>@include('partials.status-badge', ['status' => $p->status])</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('guru.pengajuan.show', $p) }}"><i class="bi bi-eye"></i> Tinjau</a></td></tr>
    @empty
        <tr><td colspan="7" class="text-center text-muted">Tidak ada permohonan.</td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $pengajuan->links() }}</div>
@endsection
