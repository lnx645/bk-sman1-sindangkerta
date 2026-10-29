<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    /**
     * Rekap TERBATAS: hanya agregat hitungan. Query di sini sengaja tidak
     * pernah memilih kolom deskripsi / catatan_sesi, dan view rekap tidak
     * menampilkan isi deskripsi masalah siswa mana pun.
     */
    public function index(Request $request)
    {
        $query = Pengajuan::query()
            ->when($request->filled('bulan'), fn ($q) => $q->whereMonth('created_at', $request->bulan))
            ->when($request->filled('tahun'), fn ($q) => $q->whereYear('created_at', $request->tahun));

        $perStatus = (clone $query)->selectRaw('status, COUNT(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status');
        $perKategori = (clone $query)->join('kategori_konsultasi', 'pengajuan.kategori_id', '=', 'kategori_konsultasi.id')
            ->selectRaw('kategori_konsultasi.nama as nama, COUNT(*) as jumlah')->groupBy('kategori_konsultasi.nama')->pluck('jumlah', 'nama');
        $perKelas = (clone $query)->join('users', 'pengajuan.siswa_id', '=', 'users.id')
            ->leftJoin('kelas', 'users.kelas_id', '=', 'kelas.id')
            ->selectRaw('COALESCE(kelas.nama, ?) as nama, COUNT(*) as jumlah', ['Tanpa Kelas'])->groupBy('kelas.nama')->pluck('jumlah', 'nama');

        return view('admin.rekap.index', [
            'total' => $query->count(),
            'perStatus' => $perStatus,
            'perKategori' => $perKategori,
            'perKelas' => $perKelas,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ]);
    }
}
