<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_SISWA = 'siswa';
    public const ROLE_GURU_BK = 'guru_bk';
    public const ROLE_ADMIN = 'admin';

    public const ROLES = [self::ROLE_SISWA, self::ROLE_GURU_BK, self::ROLE_ADMIN];

    protected $fillable = ['name', 'email', 'password', 'role', 'kelas_id', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /** Pengajuan yang dibuat oleh siswa ini. */
    public function pengajuanSiswa(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'siswa_id');
    }

    /** Pengajuan yang ditangani guru BK ini. */
    public function pengajuanGuruBk(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'guru_bk_id');
    }

    public function isSiswa(): bool
    {
        return $this->role === self::ROLE_SISWA;
    }

    public function isGuruBk(): bool
    {
        return $this->role === self::ROLE_GURU_BK;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
