<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';

    public const STATUS_DIAJUKAN = 'diajukan';
    public const STATUS_DITINJAU = 'ditinjau';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_DIJADWALKAN_ULANG = 'dijadwalkan_ulang';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUSES = [
        self::STATUS_DIAJUKAN,
        self::STATUS_DITINJAU,
        self::STATUS_DISETUJUI,
        self::STATUS_DITOLAK,
        self::STATUS_DIJADWALKAN_ULANG,
        self::STATUS_SELESAI,
        self::STATUS_DIBATALKAN,
    ];

    /** Status yang masih bisa dibatalkan siswa. */
    public const CANCELLABLE = [self::STATUS_DIAJUKAN, self::STATUS_DITINJAU];

    protected $fillable = [
        'siswa_id', 'guru_bk_id', 'kategori_id', 'deskripsi',
        'waktu_diminta', 'status', 'alasan_penolakan', 'catatan_reschedule',
    ];

    protected function casts(): array
    {
        return ['waktu_diminta' => 'datetime'];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function guruBk(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_bk_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriKonsultasi::class, 'kategori_id');
    }

    public function jadwal(): HasOne
    {
        return $this->hasOne(Jadwal::class);
    }

    public function catatanSesi(): HasOne
    {
        return $this->hasOne(CatatanSesi::class);
    }

    /** Apakah pengajuan ini boleh dilihat pengguna tertentu (privasi). */
    public function visibleTo(User $user): bool
    {
        if ($user->isAdmin()) {
            // Admin sengaja TIDAK diberi akses baca detail isi pengajuan
            // lewat controller rekap; method ini hanya untuk siswa & guru BK.
            return false;
        }

        if ($user->isSiswa()) {
            return $this->siswa_id === $user->id;
        }

        if ($user->isGuruBk()) {
            // Antrean belum bertuan (guru_bk_id null) boleh ditinjau guru BK mana pun;
            // setelah bertuan, hanya guru BK penanggung jawab yang boleh membukanya.
            return $this->guru_bk_id === null || $this->guru_bk_id === $user->id;
        }

        return false;
    }
}
