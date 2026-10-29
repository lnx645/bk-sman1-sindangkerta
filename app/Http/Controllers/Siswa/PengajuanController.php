<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengajuanRequest;
use App\Models\KategoriKonsultasi;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuan = Pengajuan::where('siswa_id', Auth::id())
            ->with(['kategori', 'guruBk', 'jadwal'])
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()->paginate(10);

        return view('siswa.pengajuan.index', compact('pengajuan'));
    }

    public function create()
    {
        $kategori = KategoriKonsultasi::where('is_active', true)->orderBy('nama')->get();

        return view('siswa.pengajuan.create', compact('kategori'));
    }

    public function store(StorePengajuanRequest $request)
    {
        // Kategori yang tidak aktif tidak boleh dipilih (divalidasi ulang di sini).
        $kategori = KategoriKonsultasi::where('id', $request->kategori_id)->where('is_active', true)->firstOrFail();

        $pengajuan = Pengajuan::create([
            'siswa_id' => Auth::id(),
            'kategori_id' => $kategori->id,
            'deskripsi' => $request->deskripsi,
            'waktu_diminta' => $request->waktu_diminta,
            'status' => Pengajuan::STATUS_DIAJUKAN,
        ]);

        return redirect()->route('siswa.pengajuan.show', $pengajuan)
            ->with('success', 'Pengajuan konsultasi berhasil dikirim dan menunggu ditinjau guru BK.');
    }

    public function show(Pengajuan $pengajuan)
    {
        $this->authorizeOwnership($pengajuan);

        $pengajuan->load(['kategori', 'guruBk', 'jadwal', 'catatanSesi', 'pesan' => fn ($q) => $q->orderBy('created_at')->orderBy('id')->with('pengirim')]);

        return view('siswa.pengajuan.show', compact('pengajuan'));
    }

    public function cancel(Pengajuan $pengajuan)
    {
        $this->authorizeOwnership($pengajuan);

        if (! in_array($pengajuan->status, Pengajuan::CANCELLABLE, true)) {
            return back()->withErrors(['status' => 'Pengajuan pada status ini tidak bisa dibatalkan lagi.']);
        }

        $pengajuan->update(['status' => Pengajuan::STATUS_DIBATALKAN]);

        return back()->with('success', 'Pengajuan dibatalkan.');
    }

    private function authorizeOwnership(Pengajuan $pengajuan): void
    {
        if ($pengajuan->siswa_id !== Auth::id()) {
            abort(403, 'Pengajuan ini bukan milik Anda.');
        }
    }
}
