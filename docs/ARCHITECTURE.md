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

Landing page memakai data presentasi statis dan tidak membaca data operasional. Efek 3D menggunakan CSS dan pointer interaction kecil di `landing.js`, dengan fallback pada layar kecil serta `prefers-reduced-motion`.

## Komponen inti

### `Simit\Core\Database`

Membuat singleton PDO dari `DB_DSN`. SQLite mengaktifkan foreign key melalui `PRAGMA foreign_keys = ON`.

### `Simit\Core\Auth`

Menangani login, logout, pengguna aktif, pembatasan admin, dan pencatatan audit.

### `Simit\Core\Csrf`

Menghasilkan token per session dan memverifikasi request POST.

### `Simit\Core\View`

Merender view ke buffer, kemudian memasukkannya ke layout aplikasi.

## Routing utama

| URL | Fungsi | Akses |
| --- | --- | --- |
| `/login` | Login | Publik |
| `/logout` | Logout POST | Pengguna masuk |
| `/` | Landing page untuk tamu; dashboard untuk session aktif | Publik/kontekstual |
| `/pc` | Inventaris PC | Pengguna masuk; mutasi admin |
| `/perangkat` | Perangkat lain | Pengguna masuk; mutasi admin |
| `/software` | Master software | Pengguna masuk; mutasi admin |
| `/tugas` | Daftar tugas | Pengguna masuk |
| `/tugas/ambil/{id}` | Ambil tugas POST | Pengguna masuk |
| `/tugas/hasil/{id}` | Hasil dan upload | PIC atau admin |
| `/persetujuan` | Antrean ACC tugas | Admin |
| `/notifikasi` | Daftar dan tandai semua notifikasi dibaca | Pengguna masuk |
| `/notifikasi/buka/{id}` | Tandai satu notifikasi dibaca dan arahkan ke tujuan internal | Pemilik notifikasi |
| `/chat` | Daftar kontak dan percakapan langsung | Pengguna masuk |
| `/chat/send` | Kirim pesan langsung POST | Pengguna masuk |
| `/laporan` | Ringkasan laporan | Pengguna masuk |
| `/profil` | Profil sendiri | Pengguna masuk |
| `/foto-profil/{id}` | Menampilkan foto profil privat | Pengguna masuk |
| `/akun` | Pengelolaan akun | Admin |
| `/audit` | Audit aktivitas | Admin |
| `/lampiran/{id}` | Download lampiran | Pengguna masuk |
| `/language` | Ganti bahasa POST | Session aktif/publik login |

## Migration

- `001_initial.sql`: users, master lokasi/kategori, aset, detail PC, software, tugas, pemeriksaan, kegiatan, audit, dan indeks awal.
- `002_workflow.sql`: riwayat status tugas, jumlah kondisi aset, instalasi software, notifikasi, dan tracking import.
- `003_profiles_attachments.sql`: telepon/bio pengguna, lampiran hasil tugas, dan indeks lampiran.
- `004_two_roles_approval_software_period.sql`: migrasi dua role, periode software, relasi tugas software, dan pemindahan Lab Komputer ke Lab 1.
- `005_chat_notifications.sql`: pesan langsung, indeks percakapan/unread, dan indeks notifikasi unread.
- `006_task_notification_trigger.sql`: trigger SQLite yang membuat notifikasi untuk akun aktif ketika tugas baru dibuat.
- `007_extended_profiles.sql`: data pegawai tambahan serta metadata foto profil.
- `008_notification_links.sql`: URL tujuan notifikasi dan pembaruan trigger tugas baru.

Migration yang sudah diterapkan tidak boleh diedit. Buat file bernomor baru untuk perubahan skema berikutnya.

## Model data utama

- `users` menyimpan akun dan satu nilai peran.
- Kolom profil pada `users` menyimpan identitas kerja dan nama file foto acak; berkas fisik berada di `storage/avatars`.
- `assets` menjadi induk PC dan perangkat lain.
- `pc_details` adalah relasi satu-ke-satu terhadap aset PC.
- `tasks` menyimpan workflow pekerjaan dan hasil teks.
- `notifications` menyimpan judul, isi pemberitahuan per pengguna, dan waktu baca.
- `messages` menyimpan chat langsung pengirim-penerima beserta waktu baca.
- `attachments` menyimpan metadata file yang terkait langsung ke tugas.
- `inspections` menyimpan hasil pemeriksaan aset.
- `events` menyimpan kegiatan.
- `audit_logs` menyimpan aktivitas penting.

## Konvensi perubahan

- Gunakan prepared statement untuk semua input.
- Semua POST wajib memanggil `Csrf::verify()`.
- Semua perubahan penting memanggil `Auth::audit()`.
- Gunakan transaction untuk perubahan lintas tabel atau file+database.
- Klaim tugas tanpa PIC harus memakai conditional update di dalam transaction; jangan memakai pola baca-lalu-tulis yang membuka race condition.
- Jangan menaruh password, token, atau kredensial spreadsheet di source code.
- Jangan melayani `storage/uploads` sebagai folder statis.
- Pertahankan Bahasa Indonesia sebagai default dan tambahkan teks Inggris melalui helper `__()`.
- Pastikan setiap kontrol ikon memiliki label aksesibel.

