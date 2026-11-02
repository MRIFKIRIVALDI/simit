# Deployment dan Migrasi Database SIMIT

Dokumen ini menjelaskan kondisi database SIMIT saat ini, cara menjalankan aplikasi dengan XAMPP, serta rencana migrasi dari SQLite ke MySQL/MariaDB untuk penggunaan di lingkungan kerja.

Terakhir diperbarui: 8 Oktober 2026.

## Kondisi saat ini

SIMIT saat ini menggunakan SQLite:

```text
storage/database/simit.sqlite
```

PHP, migration, seeder, upload lampiran, foto profil, chat, notifikasi, dan laporan berjalan dengan database tersebut. Untuk menjalankan versi lokal saat ini, XAMPP tidak wajib digunakan. PHP CLI sudah cukup:

```powershell
php bin/migrate.php
php bin/seed.php
php -S localhost:8000 -t public public/router.php
```

Buka `http://localhost:8000`.

## Apakah perlu XAMPP?

XAMPP hanya diperlukan jika ingin memakai Apache dan MySQL/MariaDB secara lokal. Saat menggunakan SQLite, cukup jalankan PHP development server atau gunakan Apache XAMPP tanpa service MySQL.

Setelah migrasi ke MySQL/MariaDB, nyalakan service **Apache** dan **MySQL** dari XAMPP Control Panel.

## Rekomendasi untuk implementasi kerja

SQLite cocok untuk development, demo, dan tim kecil dengan aktivitas tulis rendah. Untuk penggunaan kantor dengan banyak akun yang mengubah inventaris, tugas, chat, dan laporan, gunakan MySQL/MariaDB atau PostgreSQL pada server terkelola/VPS.

Rekomendasi praktis SIMIT adalah MariaDB/MySQL karena dukungan hosting PHP, cPanel, phpMyAdmin, backup, dan koneksi PDO tersedia luas.

## Status migrasi MySQL

Migrasi MySQL belum dilakukan pada kode saat ini. Migration di `database/migrations/` masih ditulis untuk SQLite. Jangan hanya mengganti nama driver pada `.env` lalu menganggap migrasi selesai.

Migration MySQL perlu dibuat sebagai rangkaian baru atau varian terpisah dengan penyesuaian:

- `AUTOINCREMENT` menjadi `AUTO_INCREMENT`.
- Tipe data dan aturan default SQLite disesuaikan dengan MySQL/MariaDB.
- Trigger tugas baru ditulis ulang dengan sintaks MySQL.
- Index partial SQLite diganti dengan strategi index yang kompatibel.
- Placeholder, transaksi, foreign key, dan timestamp diuji ulang.
- Seeder diuji terhadap database kosong MySQL.
- Query laporan dan conditional update klaim tugas diuji untuk konkurensi.

Migration SQLite yang sudah ada tidak boleh diedit untuk memaksa kompatibilitas MySQL. Buat migration/adapter MySQL baru agar database development lama tetap dapat direkonstruksi.

## Contoh konfigurasi MySQL lokal dengan XAMPP

Buat database `simit` melalui phpMyAdmin, lalu sesuaikan `.env`:

```env
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=simit
DB_USER=root
DB_PASS=
```

Nama variabel akhir harus mengikuti implementasi `Database` setelah adapter MySQL dibuat. Jangan menyimpan password produksi di Git.

Alur lokal setelah adapter dan migration MySQL tersedia:

```powershell
# Apache dan MySQL dinyalakan dari XAMPP Control Panel
php bin/migrate-mysql.php
php bin/seed.php
```

Jika memakai Apache XAMPP, arahkan VirtualHost atau document root ke folder `public`, bukan ke root project. Alternatifnya, PHP development server tetap dapat dipakai untuk mengakses database MySQL XAMPP:

```powershell
php -S localhost:8000 -t public public/router.php
```

## Memindahkan data SQLite ke MySQL

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
