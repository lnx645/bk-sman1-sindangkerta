<?php

namespace Database\Seeders;

use App\Models\CatatanSesi;
use App\Models\Jadwal;
use App\Models\KategoriKonsultasi;
use App\Models\Kelas;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Kelas contoh umum. Bukan data resmi SMAN 1 Sindangkerta.
        $kelas = collect([
            ['nama' => 'X IPA 1', 'tingkat' => 'X'],
            ['nama' => 'X IPS 1', 'tingkat' => 'X'],
            ['nama' => 'XI IPA 2', 'tingkat' => 'XI'],
            ['nama' => 'XII IPS 1', 'tingkat' => 'XII'],
        ])->map(fn ($k) => Kelas::create($k + ['is_active' => true]));

        $kategori = collect([
            ['nama' => 'Akademik', 'deskripsi' => 'Kesulitan belajar, nilai, dan pemilihan jurusan.'],
            ['nama' => 'Pribadi', 'deskripsi' => 'Masalah pribadi dan pengelolaan emosi.'],
            ['nama' => 'Sosial', 'deskripsi' => 'Hubungan dengan teman, guru, dan lingkungan sekolah.'],
            ['nama' => 'Karier', 'deskripsi' => 'Rencana studi lanjut dan karier.'],
        ])->map(fn ($k) => KategoriKonsultasi::create($k + ['is_active' => true]));

        $admin = User::create([
            'name' => 'Admin BK', 'email' => 'admin@bk.demo', 'password' => Hash::make('password123'),
            'role' => 'admin', 'is_active' => true,
        ]);

        $guru1 = User::create([
            'name' => 'Dewi Lestari', 'email' => 'guru1@bk.demo', 'password' => Hash::make('password123'),
            'role' => 'guru_bk', 'is_active' => true,
        ]);
        $guru2 = User::create([
            'name' => 'Budi Santoso', 'email' => 'guru2@bk.demo', 'password' => Hash::make('password123'),
            'role' => 'guru_bk', 'is_active' => true,
        ]);

        $siswa = collect([
            ['name' => 'Andi Pratama', 'email' => 'siswa1@bk.demo', 'kelas_id' => $kelas[0]->id],
            ['name' => 'Sari Wulandari', 'email' => 'siswa2@bk.demo', 'kelas_id' => $kelas[1]->id],
            ['name' => 'Rizky Firmansyah', 'email' => 'siswa3@bk.demo', 'kelas_id' => $kelas[2]->id],
        ])->map(fn ($s) => User::create($s + ['password' => Hash::make('password123'), 'role' => 'siswa', 'is_active' => true]));

        // Pengajuan contoh dengan status beragam (data demo, bukan data siswa asli).
        $buat = function (array $data) {
            return Pengajuan::create($data);
        };

        $buat([
            'siswa_id' => $siswa[0]->id, 'kategori_id' => $kategori[0]->id,
            'deskripsi' => 'Contoh demo: kesulitan memahami materi matematika dan ingin strategi belajar.',
            'waktu_diminta' => now()->addDays(2)->setTime(9, 0), 'status' => 'diajukan',
        ]);
        $buat([
            'siswa_id' => $siswa[1]->id, 'guru_bk_id' => $guru1->id, 'kategori_id' => $kategori[1]->id,
            'deskripsi' => 'Contoh demo: merasa cemas sebelum presentasi di kelas.',
            'waktu_diminta' => now()->addDays(1)->setTime(10, 0), 'status' => 'ditinjau',
        ]);
        $pDisetujui = $buat([
            'siswa_id' => $siswa[2]->id, 'guru_bk_id' => $guru1->id, 'kategori_id' => $kategori[3]->id,
            'deskripsi' => 'Contoh demo: bingung memilih jurusan kuliah.',
            'waktu_diminta' => now()->addDays(3)->setTime(9, 0), 'status' => 'disetujui',
        ]);
        Jadwal::create(['pengajuan_id' => $pDisetujui->id, 'waktu_final' => now()->addDays(3)->setTime(9, 0), 'tempat' => 'Ruang BK', 'metode' => 'tatap_muka']);

        $buat([
            'siswa_id' => $siswa[0]->id, 'guru_bk_id' => $guru2->id, 'kategori_id' => $kategori[2]->id,
            'deskripsi' => 'Contoh demo: konflik dengan teman sekelas.',
            'waktu_diminta' => now()->addDays(1)->setTime(11, 0), 'status' => 'ditolak', 'alasan_penolakan' => 'Contoh demo: jadwal bentrok, silakan ajukan waktu lain.',
        ]);
        $pReschedule = $buat([
            'siswa_id' => $siswa[1]->id, 'guru_bk_id' => $guru2->id, 'kategori_id' => $kategori[0]->id,
            'deskripsi' => 'Contoh demo: nilai menurun pada semester ini.',
            'waktu_diminta' => now()->addDays(2)->setTime(8, 0), 'status' => 'dijadwalkan_ulang', 'catatan_reschedule' => 'Contoh demo: digeser ke hari berikutnya karena guru BK ada rapat.',
        ]);
        Jadwal::create(['pengajuan_id' => $pReschedule->id, 'waktu_final' => now()->addDays(3)->setTime(10, 0), 'tempat' => 'Ruang BK', 'metode' => 'tatap_muka']);

        $buat([
            'siswa_id' => $siswa[0]->id, 'kategori_id' => $kategori[1]->id,
            'deskripsi' => 'Contoh demo: siswa membatalkan pengajuan karena sudah menyelesaikan masalahnya sendiri.',
            'waktu_diminta' => now()->addDays(4)->setTime(10, 0), 'status' => 'dibatalkan',
        ]);

        $pSelesai = $buat([
            'siswa_id' => $siswa[2]->id, 'guru_bk_id' => $guru1->id, 'kategori_id' => $kategori[1]->id,
            'deskripsi' => 'Contoh demo: sulit berkonsentrasi saat belajar di rumah.',
            'waktu_diminta' => now()->subDays(2)->setTime(9, 0), 'status' => 'selesai',
        ]);
        Jadwal::create(['pengajuan_id' => $pSelesai->id, 'waktu_final' => now()->subDays(1)->setTime(9, 0), 'tempat' => 'Ruang BK', 'metode' => 'tatap_muka']);
        CatatanSesi::create(['pengajuan_id' => $pSelesai->id, 'catatan' => 'Contoh demo: sesi berjalan baik, siswa memahami teknik belajar.', 'tindak_lanjut' => 'Contoh demo: pantau perkembangan selama dua minggu ke depan.']);
    }
}
