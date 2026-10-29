<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
            // null = masih antrean, belum ditugaskan ke guru BK tertentu.
            $table->foreignId('guru_bk_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori_konsultasi')->restrictOnDelete();
            $table->text('deskripsi'); // data sensitif: hanya pemilik & guru BK berwenang
            $table->dateTime('waktu_diminta');
            $table->string('status')->default('diajukan');
            // diajukan, ditinjau, disetujui, ditolak, dijadwalkan_ulang, selesai, dibatalkan
            $table->text('alasan_penolakan')->nullable();
            $table->text('catatan_reschedule')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
