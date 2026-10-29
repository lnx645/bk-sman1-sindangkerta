@extends('layouts.app')
@section('title', 'Jadwal Konsultasi')
@section('content')
<h1 class="h4"><i class="bi bi-calendar-check"></i> Jadwal Konsultasi Saya</h1>
<div class="card"><div class="table-responsive"><table class="table table-striped align-middle mb-0">
    <thead><tr><th>Waktu Final</th><th>Siswa</th><th>Kategori</th><th>Metode / Tempat</th><th>Status</th></tr></thead>
    <tbody>
    @forelse($jadwal as $j)
        <tr><td>{{ $j->waktu_final->format('d/m/Y H:i') }}</td><td>{{ $j->pengajuan->siswa->name ?? '-' }}</td><td>{{ $j->pengajuan->kategori->nama ?? '-' }}</td><td>{{ $j->metode }} @if($j->tempat) — {{ $j->tempat }} @endif</td><td>@include('partials.status-badge', ['status' => $j->pengajuan->status])</td></tr>
    @empty
        <tr><td colspan="5" class="text-center text-muted">Belum ada jadwal yang disetujui.</td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $jadwal->links() }}</div>
@endsection
