<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanSesi extends Model
{
    protected $table = 'catatan_sesi';

    protected $fillable = ['pengajuan_id', 'catatan', 'tindak_lanjut'];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }
}
