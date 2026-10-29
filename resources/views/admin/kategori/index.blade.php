@extends('layouts.app')
@section('title', 'Kelola Kategori')
@section('content')
<h1 class="h4"><i class="bi bi-tags"></i> Kelola Kategori Konsultasi</h1>
<div class="card mb-3"><div class="card-body">
<form method="POST" action="{{ route('admin.kategori.store') }}" class="row g-2">@csrf
    <div class="col-md-4"><input class="form-control" type="text" name="nama" placeholder="Nama kategori" aria-label="Nama kategori" required></div>
    <div class="col-md-5"><input class="form-control" type="text" name="deskripsi" placeholder="Deskripsi (opsional)" aria-label="Deskripsi kategori"></div>
    <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="bi bi-plus-circle"></i> Tambah Kategori</button></div>
</form>
</div></div>
<div class="card"><div class="table-responsive"><table class="table table-striped mb-0">
    <thead><tr><th>Nama</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>@foreach($kategori as $k)<tr><td>{{ $k->nama }}</td><td>{{ $k->deskripsi ?? '-' }}</td><td>{{ $k->is_active ? 'Aktif' : 'Nonaktif' }}</td><td class="d-flex gap-1"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.kategori.edit', $k) }}"><i class="bi bi-pencil"></i> Edit</a><form method="POST" action="{{ route('admin.kategori.toggle', $k) }}">@csrf<button class="btn btn-sm btn-outline-warning" type="submit"><i class="bi bi-power"></i> Toggle</button></form></td></tr>@endforeach</tbody>
</table></div></div>
<div class="mt-3">{{ $kategori->links() }}</div>
@endsection
