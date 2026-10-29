@extends('layouts.app')
@section('title', 'Edit Kelas')
@section('content')
<h1 class="h4"><i class="bi bi-pencil"></i> Edit Kelas</h1>
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('admin.kelas.update', $kelas) }}">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" type="text" name="nama" value="{{ $kelas->nama }}" required></div>
    <div class="mb-3"><label class="form-label">Tingkat</label><input class="form-control" type="text" name="tingkat" value="{{ $kelas->tingkat }}"></div>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan</button>
</form>
</div></div>
@endsection
