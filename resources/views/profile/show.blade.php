@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<h1 class="h4"><i class="bi bi-person"></i> Profil Saya</h1>
<div class="card"><div class="card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">Nama</dt><dd class="col-sm-9">{{ $user->name }}</dd>
        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $user->email }}</dd>
        <dt class="col-sm-3">Peran</dt><dd class="col-sm-9">{{ $user->role }}</dd>
        <dt class="col-sm-3">Kelas</dt><dd class="col-sm-9">{{ $user->kelas->nama ?? '-' }}</dd>
        <dt class="col-sm-3">Status akun</dt><dd class="col-sm-9">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</dd>
    </dl>
</div></div>
@endsection
