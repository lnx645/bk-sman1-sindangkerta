<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesanKonsultasi extends Model
{
    protected $table = 'pesan_konsultasi';

    protected $fillable = ['pengajuan_id', 'pengirim_id', 'isi'];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    /** Label peran pengirim untuk ditampilkan di thread (Siswa / Guru BK). */
    public function labelPengirim(): string
    {
        $pengirim = $this->pengirim;

        if ($pengirim?->isGuruBk()) {
            return 'Guru BK';
        }

        if ($pengirim?->isSiswa()) {
            return 'Siswa';
        }

        return $pengirim->role ?? '-';
    }

    public function milikPengguna(User $user): bool
    {
        return $this->pengirim_id === $user->id;
    }
}
