@extends('layouts.app')
@section('title', 'Kelola Akun')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4"><i class="bi bi-people"></i> Kelola Akun</h1>
    <a class="btn btn-primary" href="{{ route('admin.users.create') }}"><i class="bi bi-person-plus"></i> Tambah Akun</a>
</div>
<div class="card"><div class="table-responsive"><table class="table table-striped align-middle mb-0">
    <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Kelas</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
    @foreach($users as $u)
        <tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->role }}</td><td>{{ $u->kelas->nama ?? '-' }}</td><td>{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</td>
        <td class="d-flex gap-1">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit', $u) }}"><i class="bi bi-pencil"></i> Edit</a>
            <form method="POST" action="{{ route('admin.users.toggle', $u) }}">@csrf<button class="btn btn-sm btn-outline-warning" type="submit"><i class="bi bi-power"></i> {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
        </td></tr>
    @endforeach
    </tbody>
</table></div></div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
