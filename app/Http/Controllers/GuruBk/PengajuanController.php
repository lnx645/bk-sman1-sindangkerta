<?php

namespace App\Http\Controllers\GuruBk;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuruPengajuanActionRequest;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuan = Pengajuan::where(function ($q) {
            $q->whereNull('guru_bk_id')->orWhere('guru_bk_id', Auth::id());
        })->with(['siswa.kelas', 'kategori', 'jadwal'])
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15);

        return view('guru.pengajuan.index', compact('pengajuan'));
    }

    public function show(Pengajuan $pengajuan)
    {
        $this->authorizeAccess($pengajuan);
        $pengajuan->load(['siswa.kelas', 'kategori', 'jadwal', 'catatanSesi']);

        return view('guru.pengajuan.show', compact('pengajuan'));
    }

    public function action(GuruPengajuanActionRequest $request, Pengajuan $pengajuan)
    {
        $this->authorizeAccess($pengajuan);
        $aksi = $request->aksi;

        // Menangani pengajuan otomatis menugaskan guru BK tersebut.
        if ($pengajuan->guru_bk_id === null) {
            $pengajuan->guru_bk_id = Auth::id();
        }

        match ($aksi) {
            'tinjau' => $pengajuan->update(['status' => Pengajuan::STATUS_DITINJAU]),
            'setujui' => $this->terapkanJadwal($pengajuan, $request, Pengajuan::STATUS_DISETUJUI),
            'tolak' => $pengajuan->update([
                'status' => Pengajuan::STATUS_DITOLAK,
                'alasan_penolakan' => $request->alasan_penolakan,
            ]),
            'jadwalkan_ulang' => $this->terapkanJadwal($pengajuan, $request, Pengajuan::STATUS_DIJADWALKAN_ULANG, $request->catatan_reschedule),
            'selesai' => $this->selesaikan($pengajuan, $request),
            default => abort(422, 'Aksi tidak dikenal.'),
        };

        $pengajuan->save();

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function jadwalIndex()
    {
        $jadwal = \App\Models\Jadwal::whereHas('pengajuan', function ($q) {
            $q->where('guru_bk_id', Auth::id())->whereIn('status', ['disetujui', 'dijadwalkan_ulang']);
        })->with(['pengajuan.siswa', 'pengajuan.kategori'])
            ->orderBy('waktu_final')->paginate(15);

        return view('guru.jadwal.index', compact('jadwal'));
    }

    private function terapkanJadwal(Pengajuan $pengajuan, GuruPengajuanActionRequest $request, string $status, ?string $catatanReschedule = null): void
    {
        // Cegah bentrok jadwal: guru BK yang sama tidak boleh punya 2 jadwal
        // pada waktu_final yang sama untuk pengajuan lain.
        // PENTING: normalisasi ke format DB (Y-m-d H:i:s) dulu, karena nilai
        // form datetime-local ("2026-11-04T09:00") tidak sama persis dengan
        // nilai datetime yang tersimpan ("2026-11-04 09:00:00") sehingga
        // perbandingan string mentah akan gagal mendeteksi bentrok.
        $waktuFinal = \Illuminate\Support\Carbon::parse($request->waktu_final)->format('Y-m-d H:i:s');
        $bentrok = \App\Models\Jadwal::whereHas('pengajuan', function ($q) {
            $q->where('guru_bk_id', Auth::id());
        })->where('waktu_final', $waktuFinal)
            ->where('pengajuan_id', '!=', $pengajuan->id)->exists();

        if ($bentrok) {
            abort(422, 'Waktu tersebut sudah dipakai jadwal lain oleh Anda.');
        }

        $pengajuan->status = $status;
        if ($catatanReschedule !== null) {
            $pengajuan->catatan_reschedule = $catatanReschedule;
        }

        $pengajuan->jadwal()->updateOrCreate(
            ['pengajuan_id' => $pengajuan->id],
            [
                'waktu_final' => $waktuFinal,
                'tempat' => $request->tempat,
                'metode' => $request->metode ?? 'tatap_muka',
            ]
        );
    }

    private function selesaikan(Pengajuan $pengajuan, GuruPengajuanActionRequest $request): void
    {
        if (! in_array($pengajuan->status, ['disetujui', 'dijadwalkan_ulang', 'ditinjau'], true)) {
            abort(422, 'Hanya pengajuan yang sudah disetujui/dijadwalkan yang bisa diselesaikan.');
        }

        $pengajuan->status = Pengajuan::STATUS_SELESAI;
        $pengajuan->catatanSesi()->updateOrCreate(
            ['pengajuan_id' => $pengajuan->id],
            ['catatan' => $request->catatan, 'tindak_lanjut' => $request->tindak_lanjut]
        );
    }

    private function authorizeAccess(Pengajuan $pengajuan): void
    {
        if (! $pengajuan->visibleTo(Auth::user())) {
            abort(403, 'Pengajuan ini bukan kewenangan Anda.');
        }
    }
}
