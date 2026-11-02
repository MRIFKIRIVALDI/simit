# Pengujian dan Operasional

## Menjalankan aplikasi

```powershell
cd "D:\tugas kuliah\semester 7\simit"
Copy-Item .env.example .env
php bin/migrate.php
$env:SIMIT_ADMIN_NAME='Koordinator IT'
$env:SIMIT_ADMIN_EMAIL='admin@domain-anda.id'
$env:SIMIT_ADMIN_PASSWORD='Password-Kuat#Anda2026'
php bin/create-admin.php
Remove-Item Env:SIMIT_ADMIN_PASSWORD
php -S localhost:8000 -t public public/router.php
```

Buka `http://localhost:8000`.

Migration dijalankan saat instalasi atau ketika ada migration baru. `create-admin.php` hanya dipakai pada database kosong. Untuk penggunaan harian, jalankan server saja.

## Data contoh lokal

Seeder lama hanya untuk pengembangan lokal dan ditolak pada environment production. Jika memang diperlukan pada salinan database lokal, tetapkan `APP_ENV=local` dan `SIMIT_ALLOW_SAMPLE_SEED=1`. Mode demo publik `/demo` tidak memerlukan seeder.

## Pemeriksaan otomatis

```powershell
php tests/smoke.php
php tests/workflow.php
php tests/communication.php
php tests/security.php
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

Smoke test memastikan tabel inti dan akun Admin tersedia. Workflow test memverifikasi pengumpulan tugas, penyembunyian selama menunggu ACC, dan persetujuan menjadi riwayat selesai. Communication test memverifikasi hanya satu akun dapat mengklaim tugas tanpa PIC, pengiriman chat, serta penghitung notifikasi/pesan belum dibaca. Security test memastikan data operasional tetap kosong, hanya Admin yang tersisa, tidak ada audit yatim, kebijakan password berjalan, fixture demo lengkap, dan membaca demo tidak mengubah database. Audit login Admin dan log rate limit boleh bertambah setelah aplikasi digunakan. Syntax check memastikan seluruh file PHP dapat diparse.

## Checklist manual setelah perubahan

- Login benar berhasil; password salah ditolak.
- Pengunjung tanpa login menerima landing page pada `/`; `/login` tetap dapat dibuka dan memiliki tautan kembali.
- Tombol **Lihat Demo** berada tepat di bawah tombol Masuk; landing page tidak memiliki link langsung ke `/demo`.
- Semua menu demo (`/demo`, PC, Perangkat, Software, Tugas, Chat, Laporan, Profil) dapat dibuka tanpa login dan hanya menampilkan data fiktif.
- Source HTML demo tidak memiliki form; POST ke rute demo ditolak dengan HTTP 405.
- Demo tidak membuat koneksi database, session login, upload, pesan, notifikasi, audit, atau perubahan data.
- Anchor fitur/alur, CTA login, dark mode, bahasa, mockup 3D, dan layout landing diuji pada desktop serta ponsel.
- Pratinjau cetak laporan menampilkan judul/header tabel rata tengah, satu kolom tanda tangan kosong tanpa label, dan watermark logo hijau pada setiap halaman tanpa menutupi teks.
- Watermark diperiksa pada seluruh halaman pratinjau cetak dengan opsi background graphics aktif maupun nonaktif.
- Tidak ada tautan registrasi publik.
- Admin dapat membuat akun, mengganti peran, dan menonaktifkan akun lain.
- Pengguna dapat mengubah profil sendiri.
- User dapat menambah dan mengubah PC, perangkat lain, serta software, tetapi tidak dapat menghapusnya.
- Menyimpan software dengan status `Perlu Install` atau `Perlu Dihapus` membuat satu tugas aktif sesuai jenisnya, bernomor `TSK-###`, dan berstatus `Dikerjakan`.
- Halaman Inventaris PC menampilkan detail teknis tambahan pada desktop dan menyederhanakan tampilan pada mobile; halaman Software tidak memakai panel detail PC.
- Foto profil JPG/PNG/WebP maksimal 2 MB dapat diunggah dan tampil kembali; MIME palsu serta file terlalu besar ditolak.
- Light/dark mode bertahan setelah reload.
- Ganti bahasa ke English lalu periksa dashboard, form CRUD, tabel, status, login, audit, dan laporan; isi halaman harus ikut berubah, bukan hanya menu.
- Ikon bulan muncul di light mode dan matahari muncul di dark mode.
- Logo putih muncul pada panel hijau/dark; logo hijau muncul pada permukaan terang.
- Semua kartu ringkasan dan baris Tugas Prioritas dapat diklik.
- Tugas tersedia hanya berhasil diambil sekali.
- Tugas tanpa PIC terlihat bagi Admin dan User; pengumpulan pertama menutup pengumpulan akun lain.
- User dapat membuat tugas baru; Admin tetap menerima pengajuan final untuk ACC.
- Beberapa User dapat menambahkan kontribusi/lampiran pada tugas tanpa PIC; pengajuan final mengubah status global menjadi Menunggu Verifikasi.
- Detail tugas menampilkan daftar kontributor untuk pemantauan Admin.
- Form tugas kolaboratif memiliki kolom kiri/kanan seimbang, tidak bertabrakan pada teks panjang, dan menjadi satu kolom di ponsel.
- Menekan tombol tugas tanpa PIC tidak menetapkan PIC; halaman kontribusi terbuka dan User lain tetap dapat membantu.
- Setelah kontribusi disimpan, nama akun pengirim muncul otomatis pada daftar kontributor.
- Bell menampilkan unread dan halaman notifikasi dapat menandai semua sebagai dibaca.
- Kartu notifikasi chat, tugas, dan ACC membuka halaman yang sesuai serta menandai notifikasi dibaca.
- Tugas baru, pengumpulan, hasil ACC/revisi, dan chat baru menghasilkan notifikasi yang tepat.
- Dua akun aktif dapat saling berkirim pesan; membuka thread menandai pesan masuk sebagai dibaca.
- PIC dapat menyimpan hasil teks dan mengunggah JPG/PNG/PDF.
- File lebih dari 10 MB atau MIME lain ditolak.
- Pengguna yang bukan PIC tidak dapat mengisi hasil tugas orang lain.
- Migration dapat dijalankan ulang tanpa duplikasi.

## Penyimpanan dan backup

Untuk SQLite, backup minimal harus mencakup:

- `storage/database/simit.sqlite`
- `storage/uploads/`
- `storage/avatars/`
- `storage/backups/` bila backup persiapan produksi masih ingin dipertahankan

Hentikan write sementara atau gunakan mekanisme backup SQLite yang konsisten sebelum menyalin database aktif. Uji restore pada staging sebelum produksi.

## Deployment

Aplikasi memerlukan PHP 8.2+, PDO, mbstring, dan fileinfo. Document root harus diarahkan ke folder `public`. Folder `storage` harus dapat ditulis oleh proses PHP tetapi tidak boleh diekspos langsung oleh web server.

Untuk penjelasan khusus penggunaan XAMPP, migrasi SQLite ke MySQL/MariaDB, staging, cutover, dan backup produksi, baca `docs/DEPLOYMENT_AND_MYSQL.md`.

Production checklist:

- gunakan HTTPS;
- ubah password admin awal;
- `APP_DEBUG=false`;
- batasi ukuran upload juga pada `php.ini` dan web server;
- aktifkan backup terjadwal;
- gunakan session cookie Secure pada HTTPS;
- jangan gunakan PHP development server untuk produksi.

