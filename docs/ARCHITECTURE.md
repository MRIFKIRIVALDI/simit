# Arsitektur Teknis SIMIT

## Struktur direktori

```text
simit/
├── bin/                     # Perintah migration dan seeder
├── database/
│   └── migrations/          # Migration SQL berurutan
├── docs/                    # Dokumentasi handoff
├── public/                  # Document root web
│   ├── assets/              # CSS, JavaScript, dan gambar
│   ├── index.php            # Front controller dan routing
│   └── router.php           # Router PHP development server
├── src/
│   ├── Core/                # Database, Auth, CSRF, View
│   └── Views/               # Template halaman
├── storage/
│   ├── database/            # SQLite lokal, tidak masuk Git
│   ├── sessions/            # Session lokal
│   └── uploads/             # Lampiran privat
├── tests/                   # Smoke test
├── bootstrap.php            # Environment, autoload, helper, session
└── .env.example             # Contoh konfigurasi
```

## Request lifecycle

```text
Browser
  → public/router.php
  → public/index.php
  → Auth/CSRF/Database
  → query atau transaksi
  → View::render()
  → layout + template
  → HTML response
```

File statis dilayani langsung dari `public/assets`. File upload tidak berada di `public`; download harus melalui `/lampiran/{id}` setelah autentikasi.

## Komponen inti

### `Simit\Core\Database`

Membuat singleton PDO dari `DB_DSN`. SQLite mengaktifkan foreign key melalui `PRAGMA foreign_keys = ON`.

### `Simit\Core\Auth`

Menangani login, logout, pengguna aktif, pembatasan admin/koordinator, dan pencatatan audit.

### `Simit\Core\Csrf`

Menghasilkan token per session dan memverifikasi request POST.

### `Simit\Core\View`

Merender view ke buffer, kemudian memasukkannya ke layout aplikasi.

## Routing utama

| URL | Fungsi | Akses |
| --- | --- | --- |
| `/login` | Login | Publik |
| `/logout` | Logout POST | Pengguna masuk |
| `/` | Dashboard | Pengguna masuk |
| `/pc` | Inventaris PC | Pengguna masuk; mutasi admin/koordinator |
| `/perangkat` | Perangkat lain | Pengguna masuk; mutasi admin/koordinator |
| `/software` | Master software | Pengguna masuk; mutasi admin/koordinator |
| `/tugas` | Daftar tugas | Pengguna masuk |
| `/tugas/ambil/{id}` | Ambil tugas POST | Pengguna masuk |
| `/tugas/hasil/{id}` | Hasil dan upload | PIC atau admin/koordinator |
| `/pemeriksaan` | Pemeriksaan | Pengguna masuk; mutasi admin/koordinator |
| `/kegiatan` | Kegiatan | Pengguna masuk; mutasi admin/koordinator |
| `/laporan` | Ringkasan laporan | Pengguna masuk |
| `/profil` | Profil sendiri | Pengguna masuk |
| `/akun` | Pengelolaan akun | Admin/koordinator |
| `/audit` | Audit aktivitas | Admin/koordinator |
| `/lampiran/{id}` | Download lampiran | Pengguna masuk |
| `/language` | Ganti bahasa POST | Session aktif/publik login |

## Migration

- `001_initial.sql`: users, master lokasi/kategori, aset, detail PC, software, tugas, pemeriksaan, kegiatan, audit, dan indeks awal.
- `002_workflow.sql`: riwayat status tugas, jumlah kondisi aset, instalasi software, notifikasi, dan tracking import.
- `003_profiles_attachments.sql`: telepon/bio pengguna, lampiran hasil tugas, dan indeks lampiran.

Migration yang sudah diterapkan tidak boleh diedit. Buat file bernomor baru untuk perubahan skema berikutnya.

## Model data utama

- `users` menyimpan akun dan satu nilai peran.
- `assets` menjadi induk PC dan perangkat lain.
- `pc_details` adalah relasi satu-ke-satu terhadap aset PC.
- `tasks` menyimpan workflow pekerjaan dan hasil teks.
- `attachments` menyimpan metadata file yang terkait langsung ke tugas.
- `inspections` menyimpan hasil pemeriksaan aset.
- `events` menyimpan kegiatan.
- `audit_logs` menyimpan aktivitas penting.

## Konvensi perubahan

- Gunakan prepared statement untuk semua input.
- Semua POST wajib memanggil `Csrf::verify()`.
- Semua perubahan penting memanggil `Auth::audit()`.
- Gunakan transaction untuk perubahan lintas tabel atau file+database.
- Jangan menaruh password, token, atau kredensial spreadsheet di source code.
- Jangan melayani `storage/uploads` sebagai folder statis.
- Pertahankan Bahasa Indonesia sebagai default dan tambahkan teks Inggris melalui helper `__()`.
- Pastikan setiap kontrol ikon memiliki label aksesibel.

