# SIMIT — Sistem Inventaris dan Manajemen IT

Aplikasi PHP untuk inventaris lab, software, tugas IT, pemeriksaan, kegiatan, laporan, dan audit GTTC.

## Kebutuhan

- PHP 8.2+
- PDO SQLite (default) atau PDO MySQL

## Instalasi lokal

```powershell
Copy-Item .env.example .env
php bin/migrate.php
$env:SIMIT_ADMIN_PASSWORD='ganti-dengan-password-kuat'
php bin/seed.php
php -S localhost:8000 -t public public/router.php
```

Buka `http://localhost:8000`. Jika seeder dijalankan tanpa environment variable, akun demo lokal adalah `admin@simit.local` dengan kata sandi `Simit!2026Demo`. Ganti sebelum penggunaan nyata.

## Database

Default menggunakan `storage/database/simit.sqlite`. Untuk MySQL/MariaDB, ubah `DB_DSN`, `DB_USER`, dan `DB_PASS` di `.env`. Migration SQL awal menggunakan sintaks SQLite untuk lingkungan pengembangan; deployment MySQL membutuhkan migration dialect MySQL yang setara.

## Keamanan

- Password disimpan menggunakan `password_hash`.
- Semua query aplikasi menggunakan prepared statement.
- Formulir memakai token CSRF.
- Hak ubah/hapus master dibatasi untuk admin; user berfokus mengambil dan mengerjakan tugas.
- Kredensial yang ditemukan dalam spreadsheet sumber tidak disalin ke aplikasi.

## Dokumentasi pengembang

- [Status implementasi dan pekerjaan lanjutan](docs/PROJECT_STATUS.md)
- [Arsitektur dan konvensi teknis](docs/ARCHITECTURE.md)
- [Pengujian, operasional, dan deployment](docs/TESTING_AND_OPERATIONS.md)
- [Riwayat perubahan](CHANGELOG.md)

Mulai dari `docs/PROJECT_STATUS.md` ketika melanjutkan pengembangan.

