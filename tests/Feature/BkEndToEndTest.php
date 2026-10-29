<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\KategoriKonsultasi;
use App\Models\Kelas;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tes end-to-end HTTP untuk Sistem Informasi Bimbingan dan Konseling
 * SMAN 1 Sindangkerta (versi 1).
 *
 * Mencakup SEMUA aksi POST v1: siswa, guru BK, dan admin, termasuk
 * validasi gagal, bentrok jadwal, dan blokir akses lintas peran.
 * Dijalankan dengan: php artisan test --filter=BkEndToEndTest
 */
class BkEndToEndTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $guru1;
    private User $guru2;
    private User $siswa1;
    private User $siswa2;
    private KategoriKonsultasi $kategori;
    private Kelas $kelas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kelas = Kelas::create(['nama' => 'X IPA 1', 'tingkat' => 'X', 'is_active' => true]);
        $this->kategori = KategoriKonsultasi::create(['nama' => 'Akademik', 'deskripsi' => 'Akademik.', 'is_active' => true]);

        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@tes.demo', 'password' => Hash::make('password123'), 'role' => 'admin', 'is_active' => true]);
        $this->guru1 = User::create(['name' => 'Guru Satu', 'email' => 'guru1@tes.demo', 'password' => Hash::make('password123'), 'role' => 'guru_bk', 'is_active' => true]);
        $this->guru2 = User::create(['name' => 'Guru Dua', 'email' => 'guru2@tes.demo', 'password' => Hash::make('password123'), 'role' => 'guru_bk', 'is_active' => true]);
        $this->siswa1 = User::create(['name' => 'Siswa Satu', 'email' => 'siswa1@tes.demo', 'password' => Hash::make('password123'), 'role' => 'siswa', 'kelas_id' => $this->kelas->id, 'is_active' => true]);
        $this->siswa2 = User::create(['name' => 'Siswa Dua', 'email' => 'siswa2@tes.demo', 'password' => Hash::make('password123'), 'role' => 'siswa', 'kelas_id' => $this->kelas->id, 'is_active' => true]);
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

    public function test_siswa_membuat_pengajuan_valid(): void
    {
        $res = $this->actingAs($this->siswa1)->post('/siswa/pengajuan', [
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'Saya kesulitan memahami materi dan ingin berkonsultasi.',
            'waktu_diminta' => now()->addDays(5)->format('Y-m-d\TH:i'),
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('pengajuan', [
            'siswa_id' => $this->siswa1->id, 'status' => 'diajukan',
            'deskripsi' => 'Saya kesulitan memahami materi dan ingin berkonsultasi.',
        ]);
    }

    public function test_siswa_pengajuan_tidak_valid_ditolak(): void
    {
        $res = $this->actingAs($this->siswa1)->post('/siswa/pengajuan', [
            'kategori_id' => $this->kategori->id,
            'deskripsi' => 'pendek',
            'waktu_diminta' => now()->subDay()->format('Y-m-d\TH:i'),
        ]);

        $res->assertSessionHasErrors(['deskripsi', 'waktu_diminta']);
        $this->assertDatabaseCount('pengajuan', 0);
    }

    public function test_siswa_tidak_bisa_akses_dan_batalkan_sembarangan(): void
    {
        $milikOrang = $this->buatPengajuan($this->siswa2);
        $milikSendiri = $this->buatPengajuan($this->siswa1);
        $selesai = $this->buatPengajuan($this->siswa1, ['status' => 'selesai']);

        $this->actingAs($this->siswa1)->get("/siswa/pengajuan/{$milikOrang->id}")->assertForbidden();

        $this->actingAs($this->siswa1)->post("/siswa/pengajuan/{$milikSendiri->id}/batalkan")
            ->assertRedirect();
        $this->assertDatabaseHas('pengajuan', ['id' => $milikSendiri->id, 'status' => 'dibatalkan']);

        // Pengajuan selesai tidak bisa dibatalkan.
        $this->actingAs($this->siswa1)->post("/siswa/pengajuan/{$selesai->id}/batalkan")
            ->assertRedirect();
        $this->assertDatabaseHas('pengajuan', ['id' => $selesai->id, 'status' => 'selesai']);
    }

    public function test_guru_aksi_lengkap_tinjau_setujui_bentrok_tolak_reschedule_selesai(): void
    {
        $p = $this->buatPengajuan($this->siswa1);
        $waktu = now()->addDays(6)->setTime(9, 0);

        // Tinjau antrean -> guru otomatis menjadi penanggung jawab.
        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p->id}/aksi", ['aksi' => 'tinjau'])->assertRedirect();
        $this->assertDatabaseHas('pengajuan', ['id' => $p->id, 'status' => 'ditinjau', 'guru_bk_id' => $this->guru1->id]);

        // Setujui dengan jadwal final.
        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p->id}/aksi", [
            'aksi' => 'setujui', 'waktu_final' => $waktu->format('Y-m-d\TH:i'),
            'tempat' => 'Ruang BK', 'metode' => 'tatap_muka',
        ])->assertRedirect();
        $this->assertDatabaseHas('pengajuan', ['id' => $p->id, 'status' => 'disetujui']);
        $this->assertDatabaseHas('jadwal', ['pengajuan_id' => $p->id, 'tempat' => 'Ruang BK']);

        // Bentrok: pengajuan kedua milik guru yang sama di waktu final yang sama harus ditolak (422).
        $p2 = $this->buatPengajuan($this->siswa2, ['guru_bk_id' => $this->guru1->id, 'status' => 'ditinjau']);
        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p2->id}/aksi", [
            'aksi' => 'setujui', 'waktu_final' => $waktu->format('Y-m-d\TH:i'),
            'tempat' => 'Ruang BK', 'metode' => 'tatap_muka',
        ])->assertStatus(422);
        $this->assertDatabaseHas('pengajuan', ['id' => $p2->id, 'status' => 'ditinjau']);
        $this->assertDatabaseMissing('jadwal', ['pengajuan_id' => $p2->id]);

        // Tolak tanpa alasan harus gagal validasi, dengan alasan harus berhasil.
        $p3 = $this->buatPengajuan($this->siswa1);
        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p3->id}/aksi", ['aksi' => 'tolak'])
            ->assertSessionHasErrors(['alasan_penolakan']);
        $this->assertDatabaseHas('pengajuan', ['id' => $p3->id, 'status' => 'diajukan']);
        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p3->id}/aksi", [
            'aksi' => 'tolak', 'alasan_penolakan' => 'Jadwal penuh, silakan ajukan waktu lain.',
        ])->assertRedirect();
        $this->assertDatabaseHas('pengajuan', ['id' => $p3->id, 'status' => 'ditolak']);

        // Jadwalkan ulang pengajuan kedua, lalu selesaikan dengan catatan.
        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p2->id}/aksi", [
            'aksi' => 'jadwalkan_ulang',
            'waktu_final' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'catatan_reschedule' => 'Digeser karena rapat.',
        ])->assertRedirect();
        $this->assertDatabaseHas('pengajuan', ['id' => $p2->id, 'status' => 'dijadwalkan_ulang']);

        // Selesai tanpa catatan harus gagal validasi.
        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p2->id}/aksi", ['aksi' => 'selesai'])
            ->assertSessionHasErrors(['catatan']);
        $this->actingAs($this->guru1)->post("/guru-bk/pengajuan/{$p2->id}/aksi", [
            'aksi' => 'selesai', 'catatan' => 'Sesi berjalan baik.', 'tindak_lanjut' => 'Pantau dua minggu.',
        ])->assertRedirect();
        $this->assertDatabaseHas('pengajuan', ['id' => $p2->id, 'status' => 'selesai']);
        $this->assertDatabaseHas('catatan_sesi', ['pengajuan_id' => $p2->id, 'catatan' => 'Sesi berjalan baik.']);

        // Guru lain tidak boleh membuka pengajuan guru pertama.
        $this->actingAs($this->guru2)->get("/guru-bk/pengajuan/{$p->id}")->assertForbidden();
    }

    public function test_admin_crud_toggle_dan_rekap_tanpa_deskripsi(): void
    {
        $deskripsiRahasia = 'Deskripsi rahasia pengujian rekap yang tidak boleh tampil.';
        $this->buatPengajuan($this->siswa1, ['deskripsi' => $deskripsiRahasia]);

        // Tambah akun, edit tanpa password (password lama harus tetap), toggle.
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Siswa Baru', 'email' => 'baru@tes.demo', 'password' => 'password123',
            'role' => 'siswa', 'kelas_id' => $this->kelas->id, 'is_active' => true,
        ])->assertRedirect();
        $baru = User::where('email', 'baru@tes.demo')->firstOrFail();
        $this->assertNotSame('password123', $baru->password); // tersimpan ter-hash

        $hashLama = $baru->password;
        $this->actingAs($this->admin)->put("/admin/users/{$baru->id}", [
            'name' => 'Siswa Baru Revisi', 'email' => 'baru@tes.demo', 'password' => '',
            'role' => 'siswa', 'kelas_id' => $this->kelas->id, 'is_active' => true,
        ])->assertRedirect();
        $this->assertSame($hashLama, $baru->fresh()->password);

        $this->actingAs($this->admin)->post("/admin/users/{$baru->id}/toggle")->assertRedirect();
        $this->assertFalse($baru->fresh()->is_active);
        // Akun nonaktif tidak bisa login. Logout dulu: rute login adalah
        // rute guest, jadi percobaan login sambil masih actingAs admin
        // akan di-redirect middleware guest sebelum sampai ke controller.
        $this->actingAs($this->admin)->post('/logout')->assertRedirect();
        $this->post('/login', ['email' => 'baru@tes.demo', 'password' => 'password123'])
            ->assertSessionHasErrors(['email']);
        $this->actingAs($this->admin)->post("/admin/users/{$baru->id}/toggle")->assertRedirect();
        $this->assertTrue($baru->fresh()->is_active);

        // Admin tidak boleh menonaktifkan dirinya sendiri.
        $this->actingAs($this->admin)->post("/admin/users/{$this->admin->id}/toggle")->assertRedirect();
        $this->assertTrue($this->admin->fresh()->is_active);

        // Kelas (param {kelas} eksplisit) dan kategori.
        $this->actingAs($this->admin)->post('/admin/kelas', ['nama' => 'X IPA 9', 'tingkat' => 'X'])->assertRedirect();
        $kelasBaru = Kelas::where('nama', 'X IPA 9')->firstOrFail();
        $this->actingAs($this->admin)->put("/admin/kelas/{$kelasBaru->id}", ['nama' => 'X IPA 9 Revisi', 'tingkat' => 'X'])->assertRedirect();
        $this->assertDatabaseHas('kelas', ['id' => $kelasBaru->id, 'nama' => 'X IPA 9 Revisi']);
        $this->actingAs($this->admin)->get("/admin/kelas/{$kelasBaru->id}/edit")->assertOk();

        $this->actingAs($this->admin)->post('/admin/kategori', ['nama' => 'Kategori Uji', 'deskripsi' => 'Uji.'])->assertRedirect();
        $kat = KategoriKonsultasi::where('nama', 'Kategori Uji')->firstOrFail();
        $this->actingAs($this->admin)->post("/admin/kategori/{$kat->id}/toggle")->assertRedirect();
        $this->assertFalse($kat->fresh()->is_active);

        // Rekap: hanya agregat, deskripsi rahasia tidak boleh tampil.
        $res = $this->actingAs($this->admin)->get('/admin/rekap');
        $res->assertOk();
        $res->assertDontSee($deskripsiRahasia);
    }

    public function test_blokir_lintas_peran_dan_halaman_get(): void
    {
        $this->actingAs($this->siswa1)->get('/admin/users')->assertForbidden();
        $this->actingAs($this->siswa1)->get('/guru-bk/pengajuan')->assertForbidden();
        $this->actingAs($this->guru1)->get('/siswa/pengajuan')->assertForbidden();
        $this->actingAs($this->guru1)->get('/admin/rekap')->assertForbidden();
        $this->actingAs($this->admin)->get('/siswa/pengajuan')->assertForbidden();

        $this->actingAs($this->siswa1)->get('/dashboard')->assertOk();
        $this->actingAs($this->guru1)->get('/dashboard')->assertOk();
        $this->actingAs($this->admin)->get('/dashboard')->assertOk();
        $this->actingAs($this->siswa1)->get('/siswa/pengajuan')->assertOk();
        $this->actingAs($this->siswa1)->get('/siswa/pengajuan/buat')->assertOk();
        $this->actingAs($this->guru1)->get('/guru-bk/pengajuan')->assertOk();
        $this->actingAs($this->guru1)->get('/guru-bk/jadwal')->assertOk();
        $this->actingAs($this->admin)->get('/admin/users')->assertOk();
        $this->actingAs($this->admin)->get('/admin/kelas')->assertOk();
        $this->actingAs($this->admin)->get('/admin/kategori')->assertOk();
        $this->actingAs($this->admin)->get('/admin/rekap')->assertOk();
        $this->actingAs($this->siswa1)->get('/profil')->assertOk();
    }
}
