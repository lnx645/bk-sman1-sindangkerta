<?php

namespace Tests\Feature;

use App\Models\KategoriKonsultasi;
use App\Models\Kelas;
use App\Models\Pengajuan;
use App\Models\PesanKonsultasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tes thread pesan konsultasi per pengajuan.
 * Dijalankan dengan: php artisan test --filter=PesanKonsultasiTest
 */
class PesanKonsultasiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $guru1;
    private User $guru2;
    private User $siswa1;
    private User $siswa2;
    private KategoriKonsultasi $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        $kelas = Kelas::create(['nama' => 'X IPA 1', 'tingkat' => 'X', 'is_active' => true]);
        $this->kategori = KategoriKonsultasi::create(['nama' => 'Akademik', 'deskripsi' => 'Akademik.', 'is_active' => true]);

        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@tes.demo', 'password' => Hash::make('password123'), 'role' => 'admin', 'is_active' => true]);
        $this->guru1 = User::create(['name' => 'Guru Satu', 'email' => 'guru1@tes.demo', 'password' => Hash::make('password123'), 'role' => 'guru_bk', 'is_active' => true]);
        $this->guru2 = User::create(['name' => 'Guru Dua', 'email' => 'guru2@tes.demo', 'password' => Hash::make('password123'), 'role' => 'guru_bk', 'is_active' => true]);
        $this->siswa1 = User::create(['name' => 'Siswa Satu', 'email' => 'siswa1@tes.demo', 'password' => Hash::make('password123'), 'role' => 'siswa', 'kelas_id' => $kelas->id, 'is_active' => true]);
        $this->siswa2 = User::create(['name' => 'Siswa Dua', 'email' => 'siswa2@tes.demo', 'password' => Hash::make('password123'), 'role' => 'siswa', 'kelas_id' => $kelas->id, 'is_active' => true]);
    }

    private function buatPengajuan(User $siswa, array $over = []): Pengajuan
    {
        return Pengajuan::create(array_merge([
            'siswa_id' => $siswa->id,
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Deskripsi pengujian yang cukup panjang untuk validasi.',
            'waktu_diminta' => now()->addDays(3)->setTime(9, 0),
            'status' => 'diajukan',
        ], $over));
    }

    public function test_siswa_kirim_pesan_ke_miliknya_dan_status_tidak_berubah(): void
    {
        $p = $this->buatPengajuan($this->siswa1);

        $res = $this->actingAs($this->siswa1)->post("/siswa/pengajuan/{$p->id}/pesan", ['isi' => 'Halo, saya ingin bertanya tentang jadwal.']);
        $res->assertRedirect();
        $this->assertDatabaseHas('pesan_konsultasi', [
            'pengajuan_id' => $p->id, 'pengirim_id' => $this->siswa1->id,
            'isi' => 'Halo, saya ingin bertanya tentang jadwal.',
        ]);
        $this->assertDatabaseHas('pengajuan', ['id' => $p->id, 'status' => 'diajukan']);
        $this->actingAs($this->siswa1)->get("/siswa/pengajuan/{$p->id}")
            ->assertOk()->assertSee('Halo, saya ingin bertanya tentang jadwal.');
    }

    public function test_guru_kirim_balasan_ke_tanggung_jawabnya_terlihat_siswa(): void
    {
        $p = $this->buatPengajuan($this->siswa1, ['guru_bk_id' => $this->guru1->id, 'status' => 'disetujui']);

        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p->id}/pesan", ['isi' => 'Silakan datang sesuai jadwal ya.'])
            ->assertRedirect();
        $this->assertDatabaseHas('pesan_konsultasi', [
            'pengajuan_id' => $p->id, 'pengirim_id' => $this->guru1->id,
            'isi' => 'Silakan datang sesuai jadwal ya.',
        ]);
        // Status tetap disetujui, tidak berubah karena pesan.
        $this->assertDatabaseHas('pengajuan', ['id' => $p->id, 'status' => 'disetujui']);
        $this->actingAs($this->siswa1)->get("/siswa/pengajuan/{$p->id}")
            ->assertOk()->assertSee('Silakan datang sesuai jadwal ya.');
    }

    public function test_guru_bisa_membalas_antrean_umum(): void
    {
        $p = $this->buatPengajuan($this->siswa1); // guru_bk_id null

        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p->id}/pesan", ['isi' => 'Saya bantu jawab dari antrean.'])
            ->assertRedirect();
        $this->assertDatabaseHas('pesan_konsultasi', ['pengajuan_id' => $p->id, 'pengirim_id' => $this->guru1->id]);
    }

    public function test_siswa_lain_dan_guru_lain_ditolak_403(): void
    {
        $p = $this->buatPengajuan($this->siswa1, ['guru_bk_id' => $this->guru1->id]);

        $this->actingAs($this->siswa2)->get("/siswa/pengajuan/{$p->id}")->assertForbidden();
        $this->actingAs($this->siswa2)->post("/siswa/pengajuan/{$p->id}/pesan", ['isi' => 'Mencoba menyusup.'])->assertForbidden();
        $this->actingAs($this->guru2)->get("/guru-bk/pengajuan/{$p->id}")->assertForbidden();
        $this->actingAs($this->guru2)->post("/guru-bk/pengajuan/{$p->id}/pesan", ['isi' => 'Mencoba menyusup.'])->assertForbidden();
        $this->assertDatabaseCount('pesan_konsultasi', 0);
    }

    public function test_admin_tidak_bisa_akses_pesan(): void
    {
        $p = $this->buatPengajuan($this->siswa1);
        PesanKonsultasi::create(['pengajuan_id' => $p->id, 'pengirim_id' => $this->siswa1->id, 'isi' => 'Pesan rahasia siswa.']);

        // Admin tidak punya rute detail/pesan siswa maupun guru: middleware role menolak.
        $this->actingAs($this->admin)->get("/siswa/pengajuan/{$p->id}")->assertForbidden();
        $this->actingAs($this->admin)->post("/siswa/pengajuan/{$p->id}/pesan", ['isi' => 'x'])->assertForbidden();
        $this->actingAs($this->admin)->get("/guru-bk/pengajuan/{$p->id}")->assertForbidden();
        $this->actingAs($this->admin)->post("/guru-bk/pengajuan/{$p->id}/pesan", ['isi' => 'x'])->assertForbidden();
        // Rekap admin tetap tidak menampilkan isi pesan.
        $this->actingAs($this->admin)->get('/admin/rekap')->assertOk()->assertDontSee('Pesan rahasia siswa.');
    }

    public function test_pesan_kosong_dan_terlalu_panjang_gagal_validasi(): void
    {
        $p = $this->buatPengajuan($this->siswa1);

        $this->actingAs($this->siswa1)->post("/siswa/pengajuan/{$p->id}/pesan", ['isi' => '   '])
            ->assertSessionHasErrors('isi');
        $this->actingAs($this->siswa1)->post("/siswa/pengajuan/{$p->id}/pesan", ['isi' => str_repeat('a', 2001)])
            ->assertSessionHasErrors('isi');
        $this->assertDatabaseCount('pesan_konsultasi', 0);
    }
}
