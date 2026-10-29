# Pengujian End-to-End — Sistem Informasi Bimbingan dan Konseling SMAN 1 Sindangkerta

Tanggal pengujian: 29 Oktober 2026
Lingkungan pengujian: VPS 103.147.33.11, PHP 8.4.27, Laravel 12.70.2, SQLite
Metode: (1) Laravel HTTP Feature Test (`tests/Feature/BkEndToEndTest.php`),
(2) curl HTTP nyata ke `php artisan serve` lokal VPS (cookie jar + token CSRF),
(3) screenshot browser headless nyata (lihat `docs/screenshots/`).

Semua kasus di bawah ini berstatus **LULUS** pada putaran terakhir, setelah
perbaikan bug yang ditemukan selama pengujian (lihat bagian Bug yang Ditemukan).

## 1. Tes HTTP otomatis — 6 metode tes, 71 assertions, semua LULUS

Perintah: `php artisan test --filter=BkEndToEndTest`
Hasil akhir: `Tests: 6 passed (71 assertions)`.

Putaran pengujian terbaru (fitur thread pesan, 29 Oktober 2026):
`php artisan test` penuh = **14 passed (102 assertions)** — 6 BkEndToEndTest
+ 6 PesanKonsultasiTest + 1 ExampleTest Feature + 1 ExampleTest Unit.
Kasus thread pesan ada di bagian 8 di bawah.

| ID | Kasus | Peran | Langkah & data | Hasil yang diharapkan | Hasil aktual | Status |
|----|-------|-------|----------------|------------------------|--------------|--------|
| E01 | Buat pengajuan valid | Siswa | POST /siswa/pengajuan, kategori aktif, deskripsi ≥10 karakter, waktu di masa depan | Redirect, pengajuan tersimpan status `diajukan` milik siswa login | Redirect, `pengajuan` tersimpan sesuai | LULUS |
| E02 | Pengajuan tidak valid ditolak | Siswa | POST /siswa/pengajuan, deskripsi "pendek" (<10), waktu di masa lalu | Error validasi `deskripsi` + `waktu_diminta`, tidak ada baris baru | Error validasi sesuai, `pengajuan` tetap 0 di tes ini | LULUS |
| E03 | Akses pengajuan orang lain ditolak | Siswa | GET /siswa/pengajuan/{id milik siswa lain} | 403 | 403 | LULUS |
| E04 | Batalkan pengajuan sendiri | Siswa | POST /siswa/pengajuan/{id}/batalkan, status awal `diajukan` | Redirect, status jadi `dibatalkan` | Sesuai | LULUS |
| E05 | Batalkan pengajuan selesai ditolak | Siswa | POST batalkan, status awal `selesai` | Redirect dengan error, status tetap `selesai` | Sesuai | LULUS |
| E06 | Guru tandai ditinjau (antrean) | Guru BK | POST aksi `tinjau` pada pengajuan `guru_bk_id = null` | Status `ditinjau`, `guru_bk_id` terisi guru yang menangani | Sesuai | LULUS |
| E07 | Guru setujui + jadwal final | Guru BK | POST aksi `setujui`, `waktu_final` masa depan, tempat, metode | Status `disetujui`, baris `jadwal` terbentuk | Sesuai | LULUS |
| E08 | Bentrok jadwal ditolak | Guru BK | POST `setujui` pengajuan kedua dengan `waktu_final` SAMA dengan jadwal guru yang sama | HTTP 422, status pengajuan kedua tetap `ditinjau`, tidak ada jadwal baru | 422, sesuai (setelah perbaikan bug, lihat bawah) | LULUS |
| E09 | Tolak tanpa alasan gagal, dengan alasan berhasil | Guru BK | POST aksi `tolak` tanpa `alasan_penolakan`, lalu dengan alasan | Pertama: error validasi, status tetap; kedua: status `ditolak` + alasan tersimpan | Sesuai | LULUS |
| E10 | Jadwalkan ulang | Guru BK | POST aksi `jadwalkan_ulang`, `waktu_final` baru + `catatan_reschedule` | Status `dijadwalkan_ulang`, jadwal diperbarui | Sesuai | LULUS |
| E11 | Selesai tanpa catatan gagal, dengan catatan berhasil | Guru BK | POST aksi `selesai` tanpa `catatan`, lalu dengan `catatan` + `tindak_lanjut` | Pertama: error validasi; kedua: status `selesai` + `catatan_sesi` tersimpan | Sesuai | LULUS |
| E12 | Guru lain tidak boleh membuka | Guru BK | GET detail pengajuan milik guru BK lain | 403 | 403 | LULUS |
| E13 | Admin tambah akun (password ter-hash) | Admin | POST /admin/users, password `password123` | Redirect, user tersimpan, password di DB BUKAN plaintext (hash `$2y$`) | Sesuai | LULUS |
| E14 | Admin edit tanpa password | Admin | PUT /admin/users/{id}, password kosong | Nama berubah, hash password TIDAK berubah | Sesuai | LULUS |
| E15 | Admin nonaktifkan + login diblokir + aktifkan lagi | Admin | POST toggle (nonaktif), POST /login akun itu, POST toggle (aktif) | Nonaktif → login gagal error `email`; aktif lagi → `is_active = true` | Sesuai | LULUS |
| E16 | Admin tidak boleh nonaktifkan diri sendiri | Admin | POST toggle akun admin login | Redirect error, `is_active` admin tetap `true` | Sesuai | LULUS |
| E17 | Admin CRUD kelas (param `{kelas}`) | Admin | POST kelas, PUT kelas, GET edit kelas | Semua redirect/200, nama berubah | Sesuai — ini membuktikan perbaikan `{kela}` → `{kelas}` | LULUS |
| E18 | Admin CRUD kategori + toggle | Admin | POST kategori, POST toggle | Kategori tersimpan, `is_active` berbalik | Sesuai | LULUS |
| E19 | Rekap tanpa deskripsi | Admin | GET /admin/rekap, ada pengajuan dengan deskripsi rahasia pengujian | Halaman 200, deskripsi rahasia TIDAK tampil | 200, deskripsi tidak tampil | LULUS |
| E20 | Blokir lintas peran + halaman GET | Semua | Siswa→/admin/users, siswa→/guru-bk, guru→/siswa, guru→/admin/rekap, admin→/siswa | Semua 403 | Semua 403 | LULUS |
| E21 | Halaman GET per peran | Semua | Dashboard siswa/guru/admin, profil, daftar/form siswa, daftar/jadwal guru, users/kelas/kategori/rekap admin | Semua 200 | Semua 200 | LULUS |

## 2. Tes curl HTTP nyata (server serve, cookie jar + CSRF)

Server: `php artisan serve --host=127.0.0.1 --port=8001` (dimatikan setelah tes).
Akun: akun demo seeder (`password123`).

| Langkah | Hasil |
|---------|-------|
| GET /login | 200, judul resmi ditemukan, `bootstrap@5.3.3`, `bootstrap-icons@1.11.3`, `meta viewport` ditemukan di HTML |
| Siswa login POST | 302 → /dashboard; dashboard 200; form buat 200 |
| Siswa buat pengajuan POST | 302 → /siswa/pengajuan/8; halaman detail menampilkan deskripsi "Curl uji end to end BK SMAN 1 Sindangkerta" |
| Siswa GET /admin/users | 403 |
| Guru login POST | 302; daftar 200; detail pengajuan 8 = 200 |
| Guru setujui POST (waktu_final 2026-12-20 09:00, Ruang BK) | 302; halaman detail menampilkan badge "Disetujui" dan "Jadwal Final" |
| Admin login POST | 302; rekap 200, users 200, kelas 200, kategori 200 |
| Rekap vs deskripsi curl | Deskripsi TIDAK tampil di halaman rekap (agregat saja) |

Catatan pengujian: dua grep awal yang "gagal" bukan bug aplikasi — daftar
siswa memang tidak menampilkan kolom deskripsi (deskripsi ada di halaman
detail), dan badge status tertulis "Disetujui" (kapital). Setelah dicek lewat
halaman detail dengan pencarian case-insensitive, keduanya terbukti benar.

## 3. Bug yang ditemukan selama pengujian (dan sudah diperbaiki)

### Bug 1 — Cek bentrok jadwal tidak mendeteksi bentrok (BUG APLIKASI, diperbaiki)
Penyebab: `GuruBk\PengajuanController::terapkanJadwal()` membandingkan
`waktu_final` mentah dari form datetime-local (`2026-11-04T09:00`) dengan
nilai datetime di database (`2026-11-04 09:00:00`). Perbandingan string
mentah tidak pernah sama, sehingga bentrok lolos dan tes E08 awal
mengembalikan 302, bukan 422.
Perbaikan: normalisasi `waktu_final` ke format `Y-m-d H:i:s` dengan
`Carbon::parse()` sebelum query bentrok DAN sebelum `updateOrCreate`
jadwal, di file yang sama. Setelah perbaikan, tes E08 lolos (422).

### Bukan bug — artefak tes login akun nonaktif (diperbaiki di tes, bukan di aplikasi)
Percobaan login akun nonaktif sambil masih `actingAs(admin)` di-redirect
oleh middleware `guest` sebelum sampai ke controller, sehingga tidak ada
error sesi. Perbaikan tes: logout dulu lewat POST /logout, baru POST /login
akun nonaktif. Setelah itu tes E15 lolos. Logika blokir akun nonaktif di
`AuthController` sendiri sudah benar sejak awal.

## 4. Screenshot (ASLI, bukan mockup)

Cara: `chromium-headless` (EPEL, di VPS) + `puppeteer-core` (Node di VPS),
serve lokal VPS, konteks browser terpisah per peran (supaya sesi tidak
tercampur), viewport desktop 1280×900 dan mobile 390×844.
File ada di `docs/screenshots/` (total 15 file, 1,2 MB):

| File | Halaman / peran |
|------|-----------------|
| 01-login.png | Login (desktop) |
| 02-dashboard-siswa.png | Dashboard siswa |
| 03-form-pengajuan-siswa.png | Form ajukan konsultasi siswa |
| 04-riwayat-siswa.png | Riwayat pengajuan siswa |
| 05-dashboard-guru.png | Dashboard guru BK |
| 06-daftar-pengajuan-guru.png | Daftar permohonan guru BK |
| 07-detail-aksi-guru.png | Detail + form tinjau/setujui/tolak/reschedule + form catatan sesi & tindak lanjut (full page) |
| 08-jadwal-guru.png | Jadwal konsultasi guru BK |
| 09-dashboard-admin.png | Dashboard admin |
| 10-kelola-akun-admin.png | Kelola akun admin |
| 11-kelola-kelas-admin.png | Kelola kelas admin |
| 12-kelola-kategori-admin.png | Kelola kategori admin |
| 13-rekap-admin.png | Rekapitulasi admin (hanya agregat) |
| 14-login-mobile.png | Login (mobile 390×844, bukti responsif) |
| 15-dashboard-siswa-mobile.png | Dashboard siswa (mobile, bukti responsif) |

Pemeriksaan visual manual atas 4 file kunci (login, dashboard siswa,
detail guru, rekap): tampilan bersih, judul resmi benar, menu per peran
benar, dashboard siswa menampilkan status `Dibatalkan` (bukti seeder baru),
detail guru menampilkan semua form aksi, rekap menampilkan total 7 dengan
semua status masing-masing 1 dan tanpa deskripsi masalah.

## 5. Verifikasi final dari nol

- `php artisan migrate:fresh --force --seed`: sukses, 9 migration (3 bawaan + 6 BK).
- Isi seeder terverifikasi: users 6 (1 admin + 2 guru BK + 3 siswa),
  kelas 4, kategori 4 (Akademik, Pribadi, Sosial, Karier), pengajuan 7
  (semua status terwakili masing-masing 1: diajukan, ditinjau, disetujui,
  ditolak, dijadwalkan_ulang, selesai, dibatalkan), jadwal 3, catatan_sesi 1.
- `php artisan route:list`: 35+ rute, param kelas sekarang `{kelas}` (bukan `{kela}`).
- `php -l` file yang diubah (controller guru BK, controller kelas, routes,
  seeder, tes) lolos tanpa error.
- Route `/` mengarah ke login; file `resources/views/welcome.blade.php`
  bawaan Laravel yang tidak dipakai sudah DIHAPUS (tidak ada referensi
  ke view itu di routes/controller/layout).
- HTML semua halaman GET utama mengandung Bootstrap 5 CSS, Bootstrap Icons,
  dan meta viewport (dicek via curl, lihat bagian 2).

## 6. Pengecualian yang jujur (bukan pekerjaan tertunda, tapi batas lingkungan)

1. **MySQL belum dieksekusi.** Di VPS pengujian tidak ada server
   MySQL/MariaDB (`which mysql/mysqld/mariadb` kosong), sehingga migrate
   + seed ke MySQL tidak bisa dijalankan di sana. Yang terverifikasi penuh
   adalah SQLite. Langkah ganti ke MySQL sudah ada di README (`.env.example`
   berisi `DB_DATABASE=bk_sman1_sindangkerta` dan kawan-kawan), tetapi
   belum dieksekusi — jangan klaim MySQL teruji sebelum dijalankan di
   server yang memang punya MySQL.
2. **Cek visual di HP user sendiri.** Screenshot di atas diambil dari
   browser headless di VPS (desktop + viewport mobile 390×844). Tampilan
   di HP uwa sendiri tetap harus dicek sekali lewat URL live setelah
   deploy — itu satu-satunya langkah yang secara fisik hanya bisa
   dilakukan user di perangkatnya, dan ditulis di README sebagai langkah
   user, bukan sebagai pekerjaan tertunda dari tim pembangun.

## 7. Perubahan UI + pengujian responsif (2026-10-29, perintah "UI nya buat lebih bagus" + "Yang responsive yaa")

Fungsi, rute, nama field, status, validasi, dan logika peran TIDAK berubah — hanya tampilan
(Bootstrap 5.3 + Bootstrap Icons 1.11 via CDN, tanpa Vite build, tanpa framework CSS baru).
Tema konsisten: biru tua (#14375a) + aksen emas (#c9a227), header/navbar gradasi biru tua,
footer kecil konsisten, badge status berwarna konsisten untuk 7 status, tabel header biru tua
dengan hover, form dengan focus emas, tombol utama biru tua, tombol aksi berikon + label.

Perubahan per halaman:
- Layout: navbar gradasi + logo emas, nama pengguna + peran di chip (desktop) dan di dalam
  menu collapse (HP); sidebar desktop + menu hamburger collapse di HP (tombol navbar-toggler,
  target #bkMobileNav, menu vertikal per peran, bukan bar horizontal yang meluber);
  brand di HP dipendekkan jadi "BK · SMAN 1 Sindangkerta" agar tidak terpotong.
- Login: kartu tengah dengan garis emas atas, ikon BK besar, info akun demo kecil di bawah form.
- Dashboard tiap peran: kartu statistik berikon (total/menunggu/disetujui/selesai, antrean/
  ditangani, total/diajukan/selesai admin), kartu aksi cepat, daftar terbaru dengan header kartu.
- Detail guru: kartu terpisah Langkah 1 (Tinjau/Setujui), Langkah 1 alternatif (Tolak/
  Jadwalkan Ulang), Langkah 2 (Catatan Sesi & Tindak Lanjut); Jadwal Final dan Tindak Lanjut
  ditonjolkan dalam kotak emas/hijau. Detail siswa: kotak Jadwal Final dan Tindak Lanjut yang sama.
- Rekap admin: kartu Total + Per Status (badge) + Per Kategori + Per Kelas sebagai daftar
  berjumlah; TETAP hanya agregat COUNT, tidak ada deskripsi/catatan sesi.
- Responsif: kartu dashboard 1 kolom di HP (col-12), 2 kolom di tablet (col-md-6),
  3–4 kolom di desktop (col-lg-3); kartu rekap sama polanya; form label di atas input,
  input full-width, tombol aksi form menumpuk full-width di HP, target sentuh minimal 44px
  di HP; tabel memakai .table-responsive yang terkungkung di dalam wadahnya (scroll hanya
  di dalam tabel, halaman tidak meluber); login pas di layar HP tanpa scroll horizontal.

Tes setelah perubahan UI: `php artisan test --filter=BkEndToEndTest` = 6 lulus, 71 assertion
(sama seperti sebelum UI); `php -l` file view yang diubah lolos; `migrate:fresh --force --seed`
dari nol sukses. Navigasi Puppeteer ke semua halaman GET per peran berhasil (bukti: screenshot
di bawah diambil dari halaman-halaman itu setelah login per peran).

Screenshot baru (23 file, menggantikan 15 file lama, di docs/screenshots/):
desktop 1280×900: 01-login, 02-dashboard-siswa, 03-form-pengajuan-siswa, 04-riwayat-siswa,
05-dashboard-guru, 06-daftar-pengajuan-guru, 07-detail-aksi-guru (full page),
08-jadwal-guru, 09-dashboard-admin, 10-kelola-akun-admin, 11-kelola-kelas-admin,
12-kelola-kategori-admin, 13-rekap-admin;
mobile 390×844: 14-login-mobile, 15-dashboard-siswa-mobile, 16-form-pengajuan-mobile,
17-detail-guru-mobile (full page), 18-dashboard-admin-mobile, 19-rekap-mobile,
23-menu-mobile-terbuka (hamburger terbuka di dashboard siswa);
tablet 768×1024: 20-dashboard-siswa-tablet, 21-detail-guru-tablet, 22-rekap-tablet.

Hasil cek responsif per breakpoint (diukur via Puppeteer: document scrollWidth vs innerWidth;
overflow = halaman meluber ke samping):
- 390px: login false, dashboard siswa false, form pengajuan false, riwayat siswa false,
  detail guru false, dashboard admin false, rekap false, kelola akun false.
- 360px: dashboard siswa false.
- 768px: dashboard siswa false, detail guru false, rekap false.
- 1280px: semua halaman desktop di atas terambil tanpa meluber.
Pemeriksaan visual mobile (login, dashboard siswa, detail guru, rekap, menu terbuka) lolos:
tidak ada teks terpotong, kartu menumpuk 1 kolom, tombol/input full-width, menu hamburger
membuka daftar menu per peran dengan benar.

## 8. Fitur thread pesan / balasan langsung (29 Oktober 2026)

Perintah: guru BK harus bisa menjawab langsung di web, bukan hanya lewat
catatan sesi setelah selesai. Implementasi: model `PesanKonsultasi` +
migrasi `2026_10_29_000007_create_pesan_konsultasi_table`,
`PesanKonsultasiController@store` (rute POST siswa + guru), partial
`partials/thread-pesan.blade.php` di halaman detail siswa dan guru,
validasi `StorePesanRequest` (isi wajib setelah trim, maks 2000 karakter),
seeder 2 pesan contoh di pengajuan yang sudah disetujui.

Tes baru `tests/Feature/PesanKonsultasiTest.php` — 6 metode tes, semua LULUS:

| ID | Kasus | Hasil |
|----|-------|-------|
| P01 | Siswa kirim pesan ke pengajuannya sendiri | Redirect, pesan tersimpan, status tetap `diajukan`, isi tampil di detail siswa |
| P02 | Guru kirim balasan ke tanggung jawabnya, terlihat siswa | Redirect, pesan tersimpan, status tetap `disetujui`, isi tampil di detail siswa |
| P03 | Guru membalas antrean umum (guru_bk_id null) | Redirect, pesan tersimpan |
| P04 | Siswa lain dan guru lain ditolak | GET detail dan POST pesan keduanya 403, 0 pesan baru |
| P05 | Admin tidak bisa akses pesan | GET/POST detail siswa dan guru = 403 (middleware role); rekap admin 200 dan TIDAK menampilkan isi pesan rahasia tes |
| P06 | Pesan kosong (hanya spasi) dan >2000 karakter gagal validasi | Error validasi `isi`, 0 pesan baru |

Tes penuh setelah fitur ini: `php artisan test` = **14 passed (102 assertions)**
(6 BkEndToEndTest + 6 PesanKonsultasiTest + ExampleTest Feature + ExampleTest Unit).
Catatan: ExampleTest Feature bawaan Laravel awalnya mengira `/` harus 200,
padahal rute `/` memang sengaja mengalihkan ke login (302); tes itu
disesuaikan ke `assertRedirect('/login')` — bukan perubahan perilaku aplikasi.

Verifikasi dari nol: `migrate:fresh --force --seed` sukses (10 migrasi,
termasuk tabel `pesan_konsultasi`), seeder menghasilkan 7 pengajuan dan
2 pesan contoh di pengajuan yang sudah disetujui.

Screenshot thread (baru, tidak menghapus screenshot lama, di `docs/screenshots/`):
- `24-thread-pesan-siswa.png` — detail siswa desktop 1280×1263, thread "Pesan / Balasan" dengan pesan siswa + balasan guru, overflow=false.
- `25-thread-pesan-guru.png` — detail guru desktop 1280×2126, thread yang sama + kartu aksi guru, overflow=false.
- `26-thread-pesan-mobile.png` — detail siswa mobile 390×1699, thread menumpuk rapi, overflow=false.

Pengecualian fitur ini (sengaja): tidak ada notifikasi WA/email dan tidak
ada pesan real-time — balasan dibaca saat membuka detail pengajuan.
Mengirim pesan tidak mengubah status dan tidak menugaskan guru BK baru.

## 9. Audit responsif ulang (2026-10-29, atas permintaan "Pastikan responsive yaaa")

Metode: staging segar dari kode commit 3923989 (`/tmp/bk-audit`, `migrate:fresh --force --seed`,
serve lokal 127.0.0.1:8099), headless Chromium VPS, konteks browser terpisah per peran,
login demo per peran. Diukur, bukan sekadar dilihat: `overflow = documentElement.scrollWidth
> innerWidth + 1` (dan `body.scrollWidth` yang sama), plus pemeriksaan elemen yang
keluar viewport (di luar `.table-responsive`), tinggi tombol Kirim, lebar textarea,
dan bubble pesan. Uji stres khusus thread: 1 pesan token 180 karakter tanpa spasi dan
1 deskripsi token 160 karakter tanpa spasi dimasukkan ke staging SAJA (bukan ke live)
pada pengajuan siswa3/guru1 yang sudah disetujui.

Hasil: **60/60 kombinasi halaman x breakpoint overflow=false, 0 elemen keluar viewport.**
Tidak ada perbaikan kode yang diperlukan — audit LULUS semua, sehingga TIDAK ada
commit baru dan TIDAK ada deploy ulang (live tetap commit 3923989, terverifikasi
login publik 200 setelah audit).

| Halaman | 360px | 390px | 768px | 1280px | Catatan terukur |
|---|---|---|---|---|---|
| Login | false | false | false | false | — |
| Dashboard siswa | false | false | false | false | Kartu 1 kolom di HP |
| Form pengajuan siswa | false | false | false | false | Input full-width di HP |
| Riwayat siswa | false | false | false | false | Tabel terkungkung di `.table-responsive` |
| Detail siswa + thread pesan | false | false | false | false | Bubble maks 250px (360), 275px (390), 420px (768); token 180 karakter terpotong rapi di dalam bubble; textarea 294px (360) / 324px (390), tombol Kirim 44px di HP |
| Detail siswa (thread kosong) | false | false | false | false | State kosong + form tampil rapi |
| Dashboard guru | false | false | false | false | — |
| Daftar pengajuan guru | false | false | false | false | Tabel terkungkung di `.table-responsive` |
| Detail guru + thread + kartu aksi | false | false | false | false | Kartu aksi menumpuk 1 kolom di HP; bubble dan textarea sama seperti detail siswa |
| Jadwal guru | false | false | false | false | Tabel terkungkung di `.table-responsive` |
| Dashboard admin | false | false | false | false | — |
| Kelola akun | false | false | false | false | Tabel terkungkung di `.table-responsive` |
| Kelola kelas | false | false | false | false | Tabel terkungkung di `.table-responsive` |
| Kelola kategori | false | false | false | false | Tabel terkungkung di `.table-responsive` |
| Rekap admin | false | false | false | false | Kartu rekap menumpuk 1 kolom di HP |
| Menu hamburger (terbuka) | shown, link 51px | shown, link 51px | — (sidebar) | — (sidebar) | 4 link, masing-masing 51px (>= 44px), bisa ditekan |

Catatan: tombol Kirim di tablet/desktop (768/1280px) tingginya 38px (ukuran standar
Bootstrap di luar media query HP) — tetap bisa ditekan penuh dan tidak bertumpuk;
aturan 44px berlaku dan terpenuhi di HP (360/390px). Navbar di 360px muat penuh
(navbar right = lebar viewport, tombol hamburger terlihat).

Screenshot bukti audit ini (tidak menghapus screenshot lama, di `docs/screenshots/`):
- `27-thread-pesan-siswa-390.png` — detail siswa + thread, 390px (termasuk token stres)
- `28-thread-pesan-guru-390.png` — detail guru + thread + kartu aksi, 390px
- `29-thread-pesan-siswa-360.png` — detail siswa + thread, 360px
- `30-thread-pesan-siswa-768.png` — detail siswa + thread, 768px
- `31-thread-pesan-guru-768.png` — detail guru + thread, 768px
- `32-menu-hamburger-terbuka-390.png` — menu hamburger terbuka, 390px

Tes setelah audit: `php artisan test` di staging = **14 passed (102 assertions)**,
tidak ada file aplikasi yang diubah. Pengecualian tetap: cek visual di HP pengguna
sendiri adalah langkah terakhir; MySQL tetap belum teruji (SQLite terverifikasi).
