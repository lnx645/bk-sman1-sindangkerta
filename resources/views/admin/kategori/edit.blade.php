@extends('layouts.app')
@section('title', 'Edit Kategori')
@section('content')
<h1 class="h4"><i class="bi bi-pencil"></i> Edit Kategori</h1>
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('admin.kategori.update', $kategori) }}">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" type="text" name="nama" value="{{ $kategori->nama }}" required></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="3">{{ $kategori->deskripsi }}</textarea></div>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan</button>
</form>
</div></div>
@endsection
