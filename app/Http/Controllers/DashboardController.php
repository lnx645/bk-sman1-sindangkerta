<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSiswa()) {
            $pengajuan = Pengajuan::where('siswa_id', $user->id)->with(['kategori', 'jadwal'])->latest()->take(5)->get();
            $stats = [
                'total' => Pengajuan::where('siswa_id', $user->id)->count(),
                'menunggu' => Pengajuan::where('siswa_id', $user->id)->whereIn('status', ['diajukan', 'ditinjau'])->count(),
                'disetujui' => Pengajuan::where('siswa_id', $user->id)->whereIn('status', ['disetujui', 'dijadwalkan_ulang'])->count(),
                'selesai' => Pengajuan::where('siswa_id', $user->id)->where('status', 'selesai')->count(),
            ];

            return view('dashboard', compact('user', 'pengajuan', 'stats'));
        }

        if ($user->isGuruBk()) {
            $pengajuan = Pengajuan::where(function ($q) use ($user) {
                $q->whereNull('guru_bk_id')->orWhere('guru_bk_id', $user->id);
            })->with(['siswa', 'kategori', 'jadwal'])->latest()->take(10)->get();
            $stats = [
                'antrean' => Pengajuan::whereNull('guru_bk_id')->whereIn('status', ['diajukan', 'ditinjau'])->count(),
                'ditangani' => Pengajuan::where('guru_bk_id', $user->id)->whereIn('status', ['disetujui', 'dijadwalkan_ulang'])->count(),
                'selesai' => Pengajuan::where('guru_bk_id', $user->id)->where('status', 'selesai')->count(),
            ];

            return view('dashboard', compact('user', 'pengajuan', 'stats'));
        }

        // Admin: hanya statistik, tanpa isi deskripsi/catatansesi.
        $stats = [
            'total' => Pengajuan::count(),
            'diajukan' => Pengajuan::where('status', 'diajukan')->count(),
            'selesai' => Pengajuan::where('status', 'selesai')->count(),
            'per_status' => Pengajuan::selectRaw('status, COUNT(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status'),
        ];

        return view('dashboard', ['user' => $user, 'pengajuan' => collect(), 'stats' => $stats]);
    }
}
