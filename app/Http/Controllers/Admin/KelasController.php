<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        return view('admin.kelas.index', ['kelas' => Kelas::orderBy('nama')->paginate(15)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama' => 'required|string|max:255', 'tingkat' => 'nullable|string|max:50']);
        Kelas::create($data + ['is_active' => true]);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas)
    {
        return view('admin.kelas.edit', ['kelas' => $kelas]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $kelas->update($request->validate(['nama' => 'required|string|max:255', 'tingkat' => 'nullable|string|max:50']));

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function toggle(Kelas $kelas)
    {
        $kelas->update(['is_active' => ! $kelas->is_active]);

        return back()->with('success', 'Status kelas berhasil diubah.');
    }
}
