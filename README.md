# SIMIT — Sistem Inventaris dan Manajemen IT

Aplikasi PHP untuk inventaris lab, software, tugas IT, pemeriksaan, kegiatan, laporan, dan audit GTTC.

## Kebutuhan

- PHP 8.2+
- PDO SQLite (default) atau PDO MySQL

## Instalasi lokal

```powershell
Copy-Item .env.example .env
php bin/migrate.php
$env:SIMIT_ADMIN_EMAIL='admin@domain-anda.id'
$env:SIMIT_ADMIN_PASSWORD='Password-Kuat#Anda2026'
php bin/create-admin.php
Remove-Item Env:SIMIT_ADMIN_PASSWORD
php -S localhost:8000 -t public public/router.php
```

Buka `http://localhost:8000`. Tombol demo tersedia di bawah tombol Masuk pada halaman login. Demo memakai UI User dengan data statis read-only dan tidak membuka koneksi database. Jangan menjalankan `bin/seed.php` pada produksi; seeder data contoh hanya diizinkan jika `SIMIT_ALLOW_SAMPLE_SEED=1` ditetapkan secara eksplisit pada lingkungan lokal.

Klik logo SIMIT di halaman mana pun untuk membuka motion graphic publik di `/motion`. Video dimulai otomatis tanpa suara; kontrol pemutar dan tombol aktivasi suara tersedia di dalam halaman tersebut.

Untuk menyiapkan database asli dengan menghapus data contoh dan mempertahankan Admin:

```bash
php bin/prepare-production.php --confirm
```

Perintah tersebut membuat backup privat di `storage/backups/` sebelum menghapus data.

## Database

Default menggunakan `storage/database/simit.sqlite`. Untuk MySQL/MariaDB, ubah `DB_DSN`, `DB_USER`, dan `DB_PASS` di `.env`. Migration SQL awal menggunakan sintaks SQLite untuk lingkungan pengembangan; deployment MySQL membutuhkan migration dialect MySQL yang setara.

## Keamanan

- Password disimpan menggunakan `password_hash`.
- Semua value input SQL menggunakan prepared statement; identifier dinamis memakai allowlist `Simit\Core\Sql`.
- Formulir memakai token CSRF.
- Otorisasi ID dipusatkan dalam policy; User dapat membuat/mengubah data operasional, sedangkan delete dan pengelolaan akun dibatasi untuk Admin.
- Kredensial yang ditemukan dalam spreadsheet sumber tidak disalin ke aplikasi.
- `.env` dan data runtime ditolak oleh `.gitignore` serta pemeriksaan CI.

## Dokumentasi pengembang

- [Status implementasi dan pekerjaan lanjutan](docs/PROJECT_STATUS.md)
- [Arsitektur dan konvensi teknis](docs/ARCHITECTURE.md)
- [Pengujian, operasional, dan deployment](docs/TESTING_AND_OPERATIONS.md)
- [Demo read-only dan isolasi data](docs/DEMO_AND_DATA_ISOLATION.md)
- [Audit keamanan](docs/SECURITY_AUDIT.md)
- [Kebijakan secure coding](docs/SECURE_CODING_POLICY.md)
- [Audit dependency](docs/DEPENDENCY_AUDIT.md)
- [Security policy repository](SECURITY.md)
- [Riwayat perubahan](CHANGELOG.md)

Mulai dari `docs/PROJECT_STATUS.md` ketika melanjutkan pengembangan.

