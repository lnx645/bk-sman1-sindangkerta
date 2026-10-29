# Analisis — Sistem Informasi Bimbingan dan Konseling SMAN 1 Sindangkerta

## 1. Tujuan

- Memudahkan siswa mengajukan konsultasi kepada guru BK.
- Memudahkan guru BK mengelola permohonan dan jadwal konsultasi.
- Membantu pencatatan layanan dan tindak lanjut BK.
- Menyediakan rekapitulasi layanan untuk keperluan sekolah.
- Target versi 1: aplikasi sederhana, fungsional, aman, dan mudah digunakan,
  bukan aplikasi dengan terlalu banyak fitur.

Dokumen ini hanya memakai nama sekolah SMAN 1 Sindangkerta. Tidak ada alamat,
nama kepala sekolah, nomor telepon, atau data resmi sekolah lain yang dicantumkan,
karena data tersebut tidak diberikan.

## 2. Aktor dan hak akses

| Aktor | Dapat dilakukan | Tidak boleh dilakukan |
|-------|-----------------|-----------------------|
| Siswa | Login, lihat profil, ajukan konsultasi, lihat status/jadwal/riwayat miliknya, batalkan pengajuan diajukan/ditinjau | Melihat pengajuan siswa lain; melihat catatan sesi internal guru BK (kecuali tindak lanjut untuknya) |
| Guru BK | Lihat antrean + pengajuan kewenangannya, tinjau, setujui/tolak/jadwalkan ulang, kelola jadwalnya, tulis catatan sesi & tindak lanjut, lihat riwayat kewenangannya | Membuka pengajuan yang sudah ditangani guru BK lain |
| Admin | Kelola akun siswa/guru BK, kelola kelas & kategori, aktif/nonaktif akun, lihat rekap statistik | Membaca isi deskripsi masalah siswa dan isi catatan sesi lewat rekap |

## 3. Use case per peran

**Siswa**
- UC-S1 Login.
- UC-S2 Melihat profil.
- UC-S3 Mengajukan konsultasi (kategori, deskripsi singkat, waktu diinginkan).
- UC-S4 Melihat status dan jadwal konsultasi.
- UC-S5 Melihat riwayat pengajuan miliknya.
- UC-S6 Membatalkan pengajuan yang masih diajukan/ditinjau.

**Guru BK**
- UC-G1 Melihat daftar permohonan.
- UC-G2 Meninjau detail pengajuan.
- UC-G3 Menyetujui pengajuan dan membuat jadwal final.
- UC-G4 Menolak pengajuan dengan alasan.
- UC-G5 Menjadwalkan ulang dengan waktu final baru dan catatan.
- UC-G6 Mengelola jadwal konsultasi miliknya.
- UC-G7 Memperbarui status layanan menjadi selesai.
- UC-G8 Menulis catatan sesi dan tindak lanjut.
- UC-G9 Melihat riwayat konsultasi kewenangannya.

**Admin**
- UC-A1 Mengelola akun siswa dan guru BK.
- UC-A2 Mengelola kelas.
- UC-A3 Mengelola kategori konsultasi.
- UC-A4 Mengaktifkan/menonaktifkan akun.
- UC-A5 Melihat rekapitulasi layanan (statistik saja).

## 4. Alur status pengajuan

```text
diajukan → ditinjau → disetujui → selesai
                    ↘ ditolak
                    ↘ dijadwalkan_ulang → selesai
diajukan/ditinjau → dibatalkan (oleh siswa)
```

Aturan:
- Pengajuan baru: `status = diajukan`, `guru_bk_id = null` (antrean).
- Guru BK pertama yang menangani pengajuan menjadi penanggung jawabnya
  (`guru_bk_id` diisi otomatis saat aksi pertama).
- Penyetujuan dan penjadwalan ulang wajib membuat/memperbarui satu baris `jadwal`.
- Penolakan wajib disertai `alasan_penolakan`.
- Penjadwalan ulang wajib disertai `catatan_reschedule` dan `waktu_final` baru.
- Penyelesaian wajib disertai `catatan` sesi; `tindak_lanjut` opsional tetapi dianjurkan.
- Satu guru BK tidak boleh memiliki dua jadwal pada `waktu_final` yang sama
  (dicek di controller sebelum jadwal disimpan).
- Siswa hanya dapat membatalkan pengajuan berstatus `diajukan` atau `ditinjau`.

## 5. Aturan bisnis lainnya

- Kategori yang tidak aktif tidak dapat dipilih siswa saat mengajukan, tetapi
  pengajuan lama yang memakai kategori itu tetap tersimpan.
- Akun yang dinonaktifkan tidak dapat login; middleware peran juga menolak
  aksesnya meskipun sesi lama masih ada.
- Admin tidak boleh menonaktifkan akunnya sendiri.
- Jadwal final (`waktu_final`) harus di masa depan saat dibuat/diubah.
- Deskripsi pengajuan minimal 10 karakter dan maksimal 1000 karakter.
- Rekap dihitung dari tabel `pengajuan` (agregat), bukan dari tabel rekap
  terpisah, agar tidak ada data ganda yang bisa berbeda.

## 6. Keamanan dan privasi

- Autentikasi berbasis sesi Laravel; password disimpan ter-hash.
- Otorisasi dua lapis: (1) middleware `auth` + `role` di `routes/web.php`,
  (2) pengecekan kepemilikan/kewenangan di controller (`siswa_id` untuk siswa;
  `guru_bk_id` null atau sama dengan guru BK login untuk guru BK).
- Menyembunyikan menu di Blade bukan pengaman; rute tanpa middleware tetap
  akan ditolak server.
- Halaman rekap admin hanya menjalankan query agregat (`COUNT ... GROUP BY`)
  dan tidak pernah memilih kolom `deskripsi` atau `catatan`.
- Validasi input memakai Form Request untuk pengajuan siswa, aksi guru BK,
  dan akun admin; controller lain memakai `validate()`.
- Saran penerapan di sekolah: jalankan di belakang HTTPS, ganti semua password
  demo, dan batasi akses admin hanya untuk petugas yang ditunjuk.

## 7. Batasan versi 1

Tidak ada notifikasi WhatsApp/email, chat dalam aplikasi, lampiran berkas,
penjadwalan otomatis, atau dashboard grafik lanjutan. Fitur-fitur itu dapat
ditambahkan pada versi berikutnya tanpa mengubah struktur tabel inti di atas,
kecuali penambahan tabel notifikasi/lampiran bila memang diperlukan.
