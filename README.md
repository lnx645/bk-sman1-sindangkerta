# Sistem Informasi Bimbingan dan Konseling SMAN 1 Sindangkerta

Aplikasi web konsultasi Bimbingan dan Konseling (BK) untuk SMAN 1 Sindangkerta,
versi 1. Dibangun dengan Laravel 12 (PHP 8.4), Bootstrap 5.3, dan Bootstrap Icons 1.11
melalui CDN — tanpa proses build Vite, sehingga instalasi demo lebih andal.

> Catatan: dokumen ini dan seeder hanya memakai nama sekolah "SMAN 1 Sindangkerta".
> Tidak ada alamat, nama kepala sekolah, nomor telepon, atau data resmi sekolah lain
> yang dicantumkan, karena data tersebut tidak diberikan. Kelas dan nama orang di
> seeder adalah data contoh umum untuk demo, bukan data siswa/guru asli.

## Fitur per peran (versi 1)

**Siswa**
- Login dan melihat profil.
- Mengajukan konsultasi BK: pilih kategori, tulis deskripsi singkat, pilih waktu yang diinginkan.
- Melihat status dan jadwal konsultasi, serta riwayat pengajuannya sendiri.
- Membatalkan pengajuan yang masih berstatus diajukan/ditinjau.

**Guru BK**
- Melihat daftar permohonan (antrean belum bertuan + pengajuan kewenangannya).
- Meninjau detail pengajuan, menandai ditinjau.
- Menyetujui (dengan jadwal final), menolak (dengan alasan), atau menjadwalkan ulang.
- Mengelola jadwal konsultasi miliknya, dengan pengecekan bentrok waktu.
- Menulis catatan sesi dan tindak lanjut, lalu menandai pengajuan selesai.

**Admin**
- Mengelola akun siswa dan guru BK (tambah, edit, aktif/nonaktif).
- Mengelola kelas dan kategori konsultasi.
- Melihat rekapitulasi layanan **terbatas**: hanya jumlah per status, per kategori,
  per kelas, dan per periode. Rekap tidak menampilkan isi deskripsi masalah siswa
  maupun isi catatan sesi.

## Alur status pengajuan

`diajukan → ditinjau → disetujui / ditolak / dijadwalkan_ulang → selesai / dibatalkan`

- Pengajuan baru masuk antrean dengan `guru_bk_id = null`. Saat seorang guru BK
  menangani pengajuan itu, ia otomatis menjadi penanggung jawabnya.
- Setelah bertuan, hanya guru BK penanggung jawab yang dapat membuka detailnya.
- Siswa hanya dapat membuka pengajuannya sendiri.

## Cara install (langkah demi langkah)

Prasyarat: PHP 8.4, Composer 2, dan ekstensi PHP standar Laravel
(`sqlite3` untuk demo, atau MySQL untuk produksi sekolah).

```bash
# 1. Salin contoh konfigurasi
cp .env.example .env

# 2. Instal dependensi PHP (vendor tidak ikut dalam paket sumber ini)
composer install

# 3. Buat kunci aplikasi
php artisan key:generate

# 4. Buat file database SQLite (jika memakai SQLite)
touch database/database.sqlite

# 5. Migrasi + data demo
php artisan migrate --force
php artisan db:seed

# 6. Jalankan server pengembangan
php artisan serve
# Buka http://127.0.0.1:8000/login
```

### Pindah ke MySQL

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bk_sman1_sindangkerta
DB_USERNAME=root
DB_PASSWORD=
```

Lalu ulangi langkah 5. Jangan pernah menjalankan `migrate:fresh` di database
yang sudah berisi data asli sekolah.

## Akun demo

Semua akun demo memakai password: **`password123`**
Ganti password ini segera setelah demo / sebelum dipakai di lingkungan sekolah.

| Peran   | Email            | Password    |
|---------|------------------|-------------|
| Admin   | admin@bk.demo    | password123 |
| Guru BK | guru1@bk.demo    | password123 |
| Guru BK | guru2@bk.demo    | password123 |
| Siswa   | siswa1@bk.demo   | password123 |
| Siswa   | siswa2@bk.demo   | password123 |
| Siswa   | siswa3@bk.demo   | password123 |

Akun demo di atas adalah data contoh, bukan akun asli pengguna mana pun.
Password disimpan ter-hash (bcrypt via cast `hashed` Laravel), tidak pernah
disimpan sebagai teks biasa di database.

## Struktur folder (bagian yang dibuat untuk proyek ini)

```text
app/
  Http/Controllers/          Auth, Dashboard, Siswa, GuruBk, Admin
  Http/Middleware/EnsureRole.php   Middleware peran (auth + role + akun aktif)
  Http/Requests/             Validasi form (pengajuan, aksi guru BK, akun admin)
  Models/                    User, Kelas, KategoriKonsultasi, Pengajuan, Jadwal, CatatanSesi
bootstrap/app.php            Registrasi alias middleware `role`
database/
  migrations/                users (bawaan) + kelas, kategori, peran, pengajuan, jadwal, catatan_sesi
  seeders/DatabaseSeeder.php Data demo
docs/
  analisis.md                Tujuan, aktor, use case, alur status, aturan bisnis, keamanan
  erd.md                     Daftar tabel, kolom, relasi, diagram Mermaid
  pengujian-end-to-end.md    Tabel kasus uji E2E + bug yang diperbaiki + screenshot
  screenshots/               23 screenshot asli (desktop 1280 + tablet 768 + mobile 390)
tests/Feature/BkEndToEndTest.php  Tes HTTP E2E (6 metode, 71 assertions)
resources/views/
  layouts/app.blade.php      Layout Bootstrap 5.3 + Bootstrap Icons 1.11 (CDN)
  auth/login.blade.php       Halaman login
  dashboard.blade.php        Dashboard per peran
  siswa/, guru/, admin/      Halaman per peran
routes/web.php               Rute terorganisir per peran dengan middleware auth + role
```

## Hasil pengujian end-to-end (29 Oktober 2026, terverifikasi)

Rincian lengkap kasus uji ada di [docs/pengujian-end-to-end.md](docs/pengujian-end-to-end.md),
screenshot asli ada di [docs/screenshots/](docs/screenshots/) (23 file: 13 desktop + 7 mobile + 3 tablet).

- **Tes HTTP otomatis** (`tests/Feature/BkEndToEndTest.php`, perintah
  `php artisan test --filter=BkEndToEndTest`): **6 metode tes, 71 assertions,
  semua LULUS** — mencakup siswa (buat valid/tidak valid, batalkan, blokir
  akses orang lain), guru BK (tinjau, setujui, bentrok jadwal → 422, tolak
  tanpa/dengan alasan, reschedule, selesai tanpa/dengan catatan, blokir
  guru lain), admin (CRUD akun/kelas/kategori, toggle, blokir nonaktifkan
  diri sendiri, rekap tanpa deskripsi), dan blokir lintas peran (403).
- **Tes curl HTTP nyata** ke `php artisan serve`: login 200 dengan judul
  resmi + Bootstrap 5.3.3 + Bootstrap Icons 1.11.3 + meta viewport; siswa
  buat pengajuan 302 → detail menampilkan deskripsinya; guru setujui 302 →
  badge "Disetujui" + "Jadwal Final"; admin rekap 200 tanpa deskripsi;
  siswa ke /admin/users = 403.
- **Bug yang ditemukan dan diperbaiki selama pengujian:** cek bentrok
  jadwal awalnya membandingkan string form `2026-11-04T09:00` dengan
  datetime DB `2026-11-04 09:00:00` sehingga bentrok tidak terdeteksi.
  Sekarang `waktu_final` dinormalisasi ke `Y-m-d H:i:s` sebelum query
  bentrok dan sebelum simpan jadwal (`GuruBk\PengajuanController`).
- **Perbaikan lain pada penuntasan:** param rute kelas `{kela}` → `{kelas}`
  eksplisit (routes + controller), seeder dilengkapi contoh status
  `dibatalkan` sehingga semua 7 status terwakili masing-masing 1
  (total pengajuan seeder = 7), dan view `welcome.blade.php` bawaan
  Laravel yang tidak dipakai sudah dihapus (route `/` mengarah ke login).
- **Screenshot:** 23 screenshot asli dari browser headless di VPS (desktop + tablet + mobile)
  (chromium-headless + puppeteer-core, konteks browser terpisah per
  peran), desktop 1280×900 + mobile 390×844, diperiksa visual 4 file kunci
  (login, dashboard siswa, detail guru, rekap) — tampilan bersih dan benar.

- **Responsif (mobile-first, 2026-10-29):** navbar hamburger collapse di HP, kartu 1 kolom di HP / 2 kolom di tablet / 3-4 kolom di desktop, tabel .table-responsive terkungkung, tombol minimal 44px di HP. Cek overflow terukur: 390px, 360px, dan 768px semua `overflow=false` untuk halaman kunci (login, dashboard siswa/admin, form, riwayat, detail guru, rekap, kelola akun); bukti screenshot mobile + tablet ada di `docs/screenshots/` (file 14-23).

## Deploy VPS (live)

**URL live: http://103.147.33.11:8093**
**Port: 8093** (dipilih karena kosong; port 80, 8080, 8085, 8091, 8092,
8094, 8095 sudah dipakai layanan lain dan TIDAK disentuh)
**Lokasi: /opt/bk-sman1**
**Service systemd: `bk-sman1`** (FrankenPHP, file unit
`/etc/systemd/system/bk-sman1.service`, Caddyfile `/opt/bk-sman1/Caddyfile`)
**Konfigurasi deploy:** `APP_ENV=production`, `APP_DEBUG=false`,
`APP_URL=http://103.147.33.11:8093`, database SQLite
`/opt/bk-sman1/database/database.sqlite`

Perintah di server:

```bash
systemctl status bk-sman1
systemctl restart bk-sman1
journalctl -u bk-sman1 -f
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8093/login
```

Verifikasi deploy 29 Oktober 2026: service `bk-sman1` active (running),
`127.0.0.1:8093/login` 200, `103.147.33.11:8093/login` 200 dari luar VPS,
login demo admin via URL publik POST → 302 ke dashboard, dashboard dan
rekap admin 200, isi database deploy: users 6, pengajuan 7 (semua status
masing-masing 1). Layanan lain tetap aktif dan 200 (8091, 8092, 8085).

> ⚠️ PERINGATAN DEMO — WAJIB DIBACA SEBELUM PAKAI DATA ASLI:
> Akun demo di atas memakai password `password123` dan VPS ini menghadap
> internet. Akun demo ini HANYA untuk demo/sidang KP. Sebelum aplikasi ini
> dipakai dengan data siswa asli di sekolah, akun demo WAJIB diganti
> passwordnya atau dinonaktifkan/dihapus, dan aplikasi WAJIB dijalankan
> di belakang HTTPS. Jangan klaim deploy ini aman untuk data asli tanpa
> perubahan password itu.

## Yang belum ada di versi 1 (sengaja ditunda)

- Notifikasi WhatsApp/email.
- Chat di dalam aplikasi dan lampiran berkas.
- Penjadwalan otomatis dan dashboard grafik lanjutan.
- Pengujian MySQL: di lingkungan pengujian tidak ada server MySQL/MariaDB,
  sehingga yang terverifikasi penuh adalah SQLite. Langkah MySQL di atas
  sudah didokumentasikan tetapi belum dieksekusi di server bermySQL.
- Cek visual di HP pengguna sendiri lewat URL live di atas — satu-satunya
  langkah yang secara fisik hanya bisa dilakukan user di perangkatnya.

## Keamanan dan privasi (ringkas)

- Semua rute per peran dilindungi middleware `auth` + `role`.
- Kepemilikan dicek di controller (`siswa_id` untuk siswa, `guru_bk_id`/antrean
  untuk guru BK), bukan hanya dengan menyembunyikan menu.
- Admin tidak diberi rute baca detail pengajuan; rekap admin hanya agregat hitungan.
- Semua input divalidasi melalui Form Request / `validate()`.
- Selalu jalankan aplikasi di belakang HTTPS di lingkungan sekolah yang sebenarnya.
