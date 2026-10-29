@extends('layouts.app')
@section('title', 'Kelola Kelas')
@section('content')
<h1 class="h4"><i class="bi bi-building"></i> Kelola Kelas</h1>
<div class="card mb-3"><div class="card-body">
<form method="POST" action="{{ route('admin.kelas.store') }}" class="row g-2">@csrf
    <div class="col-md-5"><input class="form-control" type="text" name="nama" placeholder="Nama kelas, contoh: X IPA 1" aria-label="Nama kelas" required></div>
    <div class="col-md-3"><input class="form-control" type="text" name="tingkat" placeholder="Tingkat, contoh: X" aria-label="Tingkat"></div>
    <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle"></i> Tambah Kelas</button></div>
</form>
</div></div>
<div class="card"><div class="table-responsive"><table class="table table-striped mb-0">
    <thead><tr><th>Nama</th><th>Tingkat</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>@foreach($kelas as $k)<tr><td>{{ $k->nama }}</td><td>{{ $k->tingkat ?? '-' }}</td><td>{{ $k->is_active ? 'Aktif' : 'Nonaktif' }}</td><td class="d-flex gap-1"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.kelas.edit', $k) }}"><i class="bi bi-pencil"></i> Edit</a><form method="POST" action="{{ route('admin.kelas.toggle', $k) }}">@csrf<button class="btn btn-sm btn-outline-warning" type="submit"><i class="bi bi-power"></i> Toggle</button></form></td></tr>@endforeach</tbody>
</table></div></div>
<div class="mt-3">{{ $kelas->links() }}</div>
@endsection
