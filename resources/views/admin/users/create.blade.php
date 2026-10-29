@extends('layouts.app')
@section('title', 'Tambah Akun')
@section('content')
<h1 class="h4"><i class="bi bi-person-plus"></i> Tambah Akun</h1>
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('admin.users.store') }}">@csrf
    <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" type="text" name="name" value="{{ old('name') }}" required></div>
    <div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email') }}" required></div>
    <div class="mb-3"><label class="form-label">Password (minimal 8 karakter, disimpan ter-hash)</label><input class="form-control" type="password" name="password" required></div>
    <div class="mb-3"><label class="form-label">Peran</label><select class="form-select" name="role" required><option value="siswa">Siswa</option><option value="guru_bk">Guru BK</option><option value="admin">Admin</option></select></div>
    <div class="mb-3"><label class="form-label">Kelas (wajib untuk siswa)</label><select class="form-select" name="kelas_id"><option value="">— Tanpa Kelas —</option>@foreach($kelas as $k)<option value="{{ $k->id }}">{{ $k->nama }}</option>@endforeach</select></div>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan</button>
</form>
</div></div>
@endsection
