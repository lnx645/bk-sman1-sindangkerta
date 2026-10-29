<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePesanRequest;
use App\Models\PesanKonsultasi;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;

class PesanKonsultasiController extends Controller
{
    /**
     * Kirim satu pesan ke thread konsultasi sebuah pengajuan.
     *
     * Aturan akses memakai Pengajuan::visibleTo() yang sama dengan halaman
     * detail: siswa hanya miliknya sendiri; guru BK hanya antrean umum
     * (guru_bk_id null) atau yang sudah menjadi tanggung jawabnya; admin
     * selalu ditolak (dan memang tidak punya rute ke sini).
     *
     * Mengirim pesan SENGAJA tidak mengubah status pengajuan dan tidak
     * menugaskan guru BK baru — status tetap diatur lewat aksi yang sudah
     * ada (tinjau/setujui/tolak/reschedule/selesai).
     */
    public function store(StorePesanRequest $request, Pengajuan $pengajuan)
    {
        if (! $pengajuan->visibleTo(Auth::user())) {
            abort(403, 'Pengajuan ini bukan milik/kewenangan Anda.');
        }

        PesanKonsultasi::create([
            'pengajuan_id' => $pengajuan->id,
            'pengirim_id' => Auth::id(),
            'isi' => $request->isi,
        ]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}
