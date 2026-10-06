# Pengujian dan Operasional

## Menjalankan aplikasi

```powershell
cd "D:\tugas kuliah\semester 7\simit"
Copy-Item .env.example .env
php bin/migrate.php
$env:SIMIT_ADMIN_PASSWORD='password-kuat-anda'
php bin/seed.php
php -S localhost:8000 -t public public/router.php
```

Buka `http://localhost:8000`.

Migration dan seeder cukup dijalankan saat instalasi atau ketika ada migration baru. Untuk penggunaan harian, jalankan perintah server saja.

## Akun pengembangan

Jika seeder dijalankan tanpa `SIMIT_ADMIN_PASSWORD`:

```text
Email: admin@simit.local
Password: Simit!2026Demo
```

Password tersebut hanya untuk pengembangan dan wajib diganti sebelum deployment.

## Pemeriksaan otomatis

```powershell
php tests/smoke.php
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

Smoke test memastikan tabel inti dan akun seed tersedia. Syntax check memastikan seluruh file PHP dapat diparse.

## Checklist manual setelah perubahan

- Login benar berhasil; password salah ditolak.
- Tidak ada tautan registrasi publik.
- Admin dapat membuat akun, mengganti peran, dan menonaktifkan akun lain.
- Pengguna dapat mengubah profil sendiri.
- Light/dark mode bertahan setelah reload.
- Ikon bulan muncul di light mode dan matahari muncul di dark mode.
- Logo putih muncul pada panel hijau/dark; logo hijau muncul pada permukaan terang.
- Semua kartu ringkasan dan baris Tugas Prioritas dapat diklik.
- Tugas tersedia hanya berhasil diambil sekali.
- PIC dapat menyimpan hasil teks dan mengunggah JPG/PNG/PDF.
- File lebih dari 10 MB atau MIME lain ditolak.
- Pengguna yang bukan PIC tidak dapat mengisi hasil tugas orang lain.
- Migration dapat dijalankan ulang tanpa duplikasi.

## Penyimpanan dan backup

Untuk SQLite, backup minimal harus mencakup:

- `storage/database/simit.sqlite`
- `storage/uploads/`

Hentikan write sementara atau gunakan mekanisme backup SQLite yang konsisten sebelum menyalin database aktif. Uji restore pada staging sebelum produksi.

## Deployment

Aplikasi memerlukan PHP 8.2+, PDO, mbstring, dan fileinfo. Document root harus diarahkan ke folder `public`. Folder `storage` harus dapat ditulis oleh proses PHP tetapi tidak boleh diekspos langsung oleh web server.

Production checklist:

- gunakan HTTPS;
- ubah password admin awal;
- `APP_DEBUG=false`;
- batasi ukuran upload juga pada `php.ini` dan web server;
- aktifkan backup terjadwal;
- gunakan session cookie Secure pada HTTPS;
- jangan gunakan PHP development server untuk produksi.

