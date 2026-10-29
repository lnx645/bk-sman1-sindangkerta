<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    public const METODE_TATAP_MUKA = 'tatap_muka';
    public const METODE_DARING = 'daring';

    protected $fillable = ['pengajuan_id', 'waktu_final', 'tempat', 'metode'];

    protected function casts(): array
    {
        return ['waktu_final' => 'datetime'];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
