<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKonsultasi extends Model
{
    protected $table = 'kategori_konsultasi';

    protected $fillable = ['nama', 'deskripsi', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'kategori_id');
    }
}
