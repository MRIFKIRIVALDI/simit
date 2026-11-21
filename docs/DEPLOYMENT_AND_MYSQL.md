# Deployment dan Migrasi Database SIMIT

Dokumen ini menjelaskan kondisi database SIMIT saat ini, cara menjalankan aplikasi dengan XAMPP, serta rencana migrasi dari SQLite ke MySQL/MariaDB untuk penggunaan di lingkungan kerja.

Terakhir diperbarui: 17 November 2026.

## Kondisi saat ini

SIMIT aktif menggunakan MariaDB 10.4.32 dari XAMPP melalui PDO MySQL. Database lokal bernama `db_aplikasi`; schema dibuat otomatis dari `database/migrations/mysql/`. SQLite lama di `storage/database/simit.sqlite` tidak lagi menjadi database runtime dan dipertahankan sebagai arsip verifikasi.

Untuk menjalankan versi lokal, aktifkan MySQL XAMPP lalu jalankan:

```powershell
php bin/migrate.php
php -S localhost:8000 -t public public/router.php
```

Buka `http://localhost:8000`.

## Apakah perlu XAMPP?

Service **MySQL** XAMPP wajib aktif untuk aplikasi lokal. Apache bersifat opsional bila aplikasi dijalankan melalui PHP development server; bila memakai Apache, document root harus diarahkan ke folder `public`.

## Troubleshooting MariaDB error 1130 setelah crash

Pada 21 November 2026, MariaDB lokal mengalami crash recovery lalu menolak SIMIT dan phpMyAdmin dengan pesan `Host 'localhost' is not allowed to connect to this MariaDB server`. Pemeriksaan memastikan port 3306 tetap aktif dan `db_aplikasi` masih utuh, tetapi tabel sistem `mysql.global_priv` korup (error halaman Aria 176 dan CRC tidak valid).

Pemulihan dilakukan secara aman dengan urutan berikut:

1. MariaDB dihentikan sepenuhnya.
2. Seluruh `C:\xampp\mysql\data` disalin dan diverifikasi sebelum perbaikan.
3. MariaDB dijalankan sementara dengan `--skip-grant-tables`.
4. `mysql.global_priv` diperbaiki menggunakan `REPAIR TABLE ... EXTENDED`.
5. Empat akun lokal bawaan yang tidak dapat diselamatkan dibuat ulang: `root@localhost`, `root@127.0.0.1`, `root@::1`, dan `pma@localhost`.
6. Server dinyalakan kembali dalam mode normal, lalu seluruh database diperiksa menggunakan `mysqlcheck`.

Backup sebelum perbaikan tersimpan privat di `storage/backups/mysql-data-before-grant-repair-20261121-1045/`. Jangan masukkan folder tersebut ke Git. Jika error yang sama berulang, jangan mengganti `DB_HOST`, menghapus `db_aplikasi`, menyalin `xampp/mysql/backup` langsung ke folder data, atau memasang ulang XAMPP sebelum membuat backup dan memeriksa `mysql.global_priv`.

Setelah pemulihan, 21 tabel SIMIT, akun Admin, migration, koneksi phpMyAdmin, serta seluruh test aplikasi terverifikasi kembali.

## Rekomendasi untuk implementasi kerja

SQLite cocok untuk development, demo, dan tim kecil dengan aktivitas tulis rendah. Untuk penggunaan kantor dengan banyak akun yang mengubah inventaris, tugas, chat, dan laporan, gunakan MySQL/MariaDB atau PostgreSQL pada server terkelola/VPS.

Rekomendasi praktis SIMIT adalah MariaDB/MySQL karena dukungan hosting PHP, cPanel, phpMyAdmin, backup, dan koneksi PDO tersedia luas.

## Status migrasi MySQL

Migrasi selesai pada 17 November 2026. Adapter MySQL berada di `src/Core/Database.php`, schema MariaDB berada di `database/migrations/mysql/`, dan runner `bin/migrate.php` memilih dialect berdasarkan driver aktif. Schema terverifikasi memiliki 21 tabel, 29 foreign key, satu trigger tugas, seluruh tabel InnoDB, serta satu akun Admin dengan hash password yang sama seperti sumber SQLite.

Migration SQLite historis tetap berada di `database/migrations/` dan tidak diedit. Ia hanya digunakan bila `DB_CONNECTION=sqlite` ditetapkan eksplisit untuk pemulihan/kompatibilitas lama.

## Contoh konfigurasi MySQL lokal dengan XAMPP

Buat database `db_aplikasi` melalui phpMyAdmin dengan collation `utf8mb4_unicode_ci`, lalu sesuaikan `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_aplikasi
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
```

Jangan menyimpan `.env` atau password produksi di Git.

Alur lokal setelah adapter dan migration MySQL tersedia:

```powershell
# Apache dan MySQL dinyalakan dari XAMPP Control Panel
php bin/migrate.php
```

Jika memakai Apache XAMPP, arahkan VirtualHost atau document root ke folder `public`, bukan ke root project. Alternatifnya, PHP development server tetap dapat dipakai untuk mengakses database MySQL XAMPP:

```powershell
php -S localhost:8000 -t public public/router.php
```

## Prosedur bila data SQLite lain perlu dipindahkan

Migrasi 17 November 2026 hanya memindahkan satu akun Admin karena data operasional sumber kosong. Audit log dan login attempt lama sengaja tidak dipindahkan. Bila di kemudian hari ada salinan SQLite lain yang perlu diimpor, gunakan prosedur berikut:

1. Hentikan perubahan data sementara.
2. Backup `storage/database/simit.sqlite`, `storage/uploads/`, dan `storage/avatars/`.
3. Buat schema MySQL dari migration MySQL yang sudah diuji.
4. Ekspor data tabel dengan urutan parent lalu child: users, locations, categories, assets, pc_details, software, tasks, history, attachments, messages, notifications, dan audit.
5. Konversi nilai boolean, timestamp, nullable field, dan ID foreign key.
6. Import data ke database staging MySQL.
7. Uji login, role, CRUD, klaim tugas bersama, ACC, laporan, chat, notifikasi, lampiran, dan foto profil.
8. Cocokkan jumlah baris dan relasi penting antara SQLite dan MySQL.
9. Setelah staging disetujui, lakukan cutover ke database produksi.

Jangan menghapus file SQLite lama sampai hasil restore MySQL berhasil diverifikasi.

## Struktur deployment produksi

```text
server/
├── public/                 # document root domain
├── src/
├── database/
├── bin/
├── storage/
│   ├── database/           # tidak diekspos langsung
│   ├── uploads/            # lampiran privat
│   ├── avatars/            # foto profil privat
│   └── sessions/
├── bootstrap.php
└── .env                    # tidak masuk Git
```

Server memerlukan PHP 8.2+, PDO MySQL, mbstring, fileinfo, Apache/Nginx, HTTPS, dan database MySQL/MariaDB. Folder `storage` harus dapat ditulis PHP tetapi tidak boleh menjadi folder publik.

## Checklist keamanan sebelum go-live

- Gunakan HTTPS.
- Ganti password admin awal.
- Jangan gunakan data demo atau kredensial spreadsheet.
- Aktifkan `APP_DEBUG=false`.
- Buat backup database terjadwal.
- Simpan backup di lokasi berbeda dari server aplikasi.
- Uji restore backup secara berkala.
- Batasi ukuran dan MIME upload di aplikasi, PHP, serta web server.
- Gunakan akun database khusus aplikasi, bukan root.
- Pisahkan database development, staging, dan production.
- Tambahkan rate limiting login dan MFA bila sistem menyimpan data sensitif.

## Pilihan hosting

- Portfolio statis: landing page dapat dipasang di hosting static, tetapi login, CRUD, chat, dan database tidak berjalan di sana.
- Demo portfolio read-only: memerlukan hosting PHP, tetapi tidak membuka koneksi database. Aplikasi kerja lengkap tetap memerlukan hosting PHP dengan SQLite yang diproteksi atau MySQL/MariaDB/PostgreSQL.
- Produksi kantor: gunakan server kantor, VPS, atau hosting berbayar yang menyediakan backup, SSL, monitoring, dan database terkelola.

Hosting gratis dapat dipakai untuk demo dengan data palsu, tetapi tidak disarankan untuk inventaris dan percakapan kerja yang sensitif.
