@extends('layouts.app')
@section('title', 'Edit Akun')
@section('content')
<h1 class="h4"><i class="bi bi-pencil"></i> Edit Akun</h1>
<div class="card"><div class="card-body">
<form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PUT')
    <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}" required></div>
    <div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
    <div class="mb-3"><label class="form-label">Password Baru (kosongkan jika tidak diubah)</label><input class="form-control" type="password" name="password"></div>
    <div class="mb-3"><label class="form-label">Peran</label><select class="form-select" name="role" required>@foreach(['siswa','guru_bk','admin'] as $r)<option value="{{ $r }}" @selected($user->role===$r)>{{ $r }}</option>@endforeach</select></div>
    <div class="mb-3"><label class="form-label">Kelas</label><select class="form-select" name="kelas_id"><option value="">— Tanpa Kelas —</option>@foreach($kelas as $k)<option value="{{ $k->id }}" @selected($user->kelas_id==$k->id)>{{ $k->nama }}</option>@endforeach</select></div>
    <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="aktif" @checked($user->is_active)><label class="form-check-label" for="aktif">Akun aktif</label></div>
    <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan Perubahan</button>
</form>
</div></div>
@endsection
