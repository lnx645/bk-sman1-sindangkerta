<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriKonsultasi;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        return view('admin.kategori.index', ['kategori' => KategoriKonsultasi::orderBy('nama')->paginate(15)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama' => 'required|string|max:255', 'deskripsi' => 'nullable|string|max:1000']);
        KategoriKonsultasi::create($data + ['is_active' => true]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(KategoriKonsultasi $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriKonsultasi $kategori)
    {
        $kategori->update($request->validate(['nama' => 'required|string|max:255', 'deskripsi' => 'nullable|string|max:1000']));

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function toggle(KategoriKonsultasi $kategori)
    {
        $kategori->update(['is_active' => ! $kategori->is_active]);

        return back()->with('success', 'Status kategori berhasil diubah.');
    }
}
