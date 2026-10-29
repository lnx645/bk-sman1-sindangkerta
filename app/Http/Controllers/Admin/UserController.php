<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('kelas')->orderBy('role')->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create', ['kelas' => Kelas::where('is_active', true)->orderBy('nama')->get()]);
    }

    public function store(AdminUserRequest $request)
    {
        User::create($request->validated() + ['is_active' => $request->boolean('is_active', true)]);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', ['user' => $user, 'kelas' => Kelas::where('is_active', true)->orderBy('nama')->get()]);
    }

    public function update(AdminUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function toggle(User $user, Request $request)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['akun' => 'Anda tidak boleh menonaktifkan akun sendiri.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Status akun berhasil diubah.');
    }
}
