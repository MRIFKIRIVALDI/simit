# Status Pengembangan SIMIT

Dokumen ini adalah handoff utama untuk pengembang yang melanjutkan SIMIT. Terakhir diperbarui 6 Oktober 2026.

## 1. Ringkasan

SIMIT adalah aplikasi internal GTTC untuk inventaris perangkat, software, tugas staf IT, pemeriksaan, kegiatan, laporan, dan audit. Implementasi saat ini merupakan MVP PHP tanpa framework dengan SQLite sebagai database pengembangan.

Sumber kebutuhan utama:

- `PRD_SIMIT_v0.3.md`
- `Salinan Pengelolaan_Perangkat_Lab.xlsx` di Google Drive
- `Salinan Daftar_Tugas.xlsx` di Google Drive

Database aplikasi menjadi sumber operasional. Spreadsheet hanya menjadi referensi dan sumber data awal; belum ada sinkronisasi dua arah.

## 2. Status fitur

| Area | Status | Catatan |
| --- | --- | --- |
| Login | Selesai | Tidak ada registrasi publik. Session memakai cookie HttpOnly dan SameSite Lax. |
| Hak akses | Sebagian selesai | Admin/koordinator dapat mengubah data. Pemeriksaan policy per modul masih perlu diperdalam. |
| Pengelolaan akun | Selesai untuk MVP | Admin dapat membuat, mengubah, mengaktifkan, dan menonaktifkan akun. Admin tidak dapat menonaktifkan diri sendiri. |
| Profil pengguna | Selesai | Nama, email, telepon, bio, dan kata sandi dapat diperbarui. |
| Dashboard | Selesai untuk MVP | Kartu ringkasan, tugas prioritas, dan perangkat perlu perhatian. Baris dashboard dapat diklik dan digunakan dengan keyboard. |
| Inventaris PC | Selesai untuk MVP | CRUD aset PC dan detail teknis dasar. |
| Perangkat lain | Selesai untuk MVP | CRUD perangkat kelompok/individual dasar. |
| Software | Selesai untuk MVP | CRUD master software. Instalasi per-PC belum memiliki antarmuka lengkap. |
| Tugas IT | Selesai untuk MVP | CRUD, ambil tugas secara bersyarat, hasil teks, lampiran, dan status Menunggu Verifikasi. |
| Lampiran hasil | Selesai | JPG/PNG/PDF, maksimum 10 MB, disimpan di `storage/uploads`. |
| Pemeriksaan | Selesai untuk MVP | CRUD pemeriksaan dasar. Checklist rinci belum tersedia. |
| Kegiatan | Selesai untuk MVP | CRUD kegiatan dan status kesiapan dasar. |
| Laporan | Selesai untuk MVP | Ringkasan inventaris per lokasi dan cetak halaman. |
| Audit | Selesai untuk MVP | Login dan perubahan penting dicatat. |
| Bahasa Inggris | Sebagian selesai | Navigasi, akun, profil, hasil tugas, dan sejumlah kontrol telah diterjemahkan. Sebagian teks lama masih Bahasa Indonesia. |
| Dark mode | Selesai | Preferensi disimpan di `localStorage`; ikon bulan/matahari mengikuti mode. |
| Logo kontras | Selesai | Logo hijau pada permukaan terang dan logo putih pada panel hijau/dark mode. |
| Import spreadsheet | Belum | Struktur tracking import tersedia, tetapi UI parser/pratinjau belum dibuat. |
| Ekspor | Belum | Laporan hanya dapat dicetak dari browser. |
| Verifikasi tugas | Sebagian | Pengajuan hasil mengubah status, tetapi aksi terima/tolak koordinator belum lengkap. |
| Notifikasi | Belum | Tabel tersedia, antarmuka dan generator pengingat belum dibuat. |
| Backup/restore | Belum | Perlu prosedur dan pengujian sebelum produksi. |

## 3. Alur yang sudah berjalan

### Login dan akun

1. Admin membuat akun melalui menu **Akun**.
2. Pengguna masuk menggunakan email dan kata sandi.
3. Tidak ada endpoint atau tombol pendaftaran publik.
4. Admin dapat memilih peran: admin, koordinator, staf, PKL/intern, atau viewer.
5. Akun dapat dinonaktifkan tanpa menghapus riwayat.

### Tugas dan hasil pekerjaan

1. Admin/koordinator membuat tugas.
2. Tugas berstatus `Tersedia` dapat diambil satu pengguna melalui update bersyarat.
3. PIC membuka **Hasil** dari halaman Tugas atau baris Tugas Prioritas.
4. PIC menulis hasil dan dapat melampirkan satu JPG, PNG, atau PDF pada setiap pengiriman.
5. Ketika tugas `Tersedia` atau `Dikerjakan` dikirim, status menjadi `Menunggu Verifikasi`.
6. Lampiran diunduh melalui endpoint aplikasi, bukan URL publik langsung.

Catatan: koordinator belum memiliki tombol khusus Terima/Tolak hasil. Ini adalah prioritas lanjutan.

## 4. Data awal

Seeder membuat:

- satu akun admin;
- master lokasi dan kategori;
- 24 PC contoh dari Lab Komputer;
- Google Chrome, Anaconda, dan Visual Studio Code;
- beberapa tugas representatif dari spreadsheet.

Data spreadsheet yang mengandung email/kata sandi tidak disalin ke database. Beberapa konflik sumber juga sengaja belum diimpor: IP tidak valid/ganda, perangkat tanpa ID, status kosong, dan tanggal tugas terbalik.

## 5. Tampilan dan identitas

- Tema menggunakan neumorphism ringan dengan fokus keterbacaan.
- `public/assets/img/logo-simit.png`: logo hijau untuk latar terang dan favicon.
- `public/assets/img/logo-simit-putih.png`: logo putih untuk panel hijau serta dark mode.
- Dark mode disimpan pada key `simit-theme` di `localStorage`.
- Bahasa disimpan dalam session dengan nilai `id` atau `en`.
- Ikon navigasi dan kontrol menggunakan SVG inline.

## 6. Keamanan yang sudah diterapkan

- `password_hash()` dan `password_verify()`.
- Prepared statement PDO.
- Token CSRF untuk seluruh form mutasi.
- Session ID diregenerasi saat login.
- Cookie session HttpOnly, SameSite Lax, dan strict mode.
- Validasi MIME lampiran menggunakan `finfo`, bukan hanya ekstensi.
- Batas lampiran 10 MB.
- Nama file penyimpanan dibuat acak; nama asli hanya menjadi metadata.
- Folder upload berada di luar document root.
- Admin tidak dapat menonaktifkan akun sendiri.
- Hasil tugas hanya dapat diisi PIC atau admin/koordinator.

## 7. Keterbatasan penting

- SQL migration saat ini ditulis untuk SQLite. Jangan menganggap migration langsung kompatibel dengan MySQL/MariaDB.
- Aplikasi belum menggunakan framework atau Composer package.
- Router, controller, dan logika bisnis masih terkonsentrasi di `public/index.php`; perlu dipecah sebelum fitur membesar.
- Lampiran bisa diunduh semua pengguna yang sudah login. Policy akses per tugas perlu diperketat bila ada data sensitif.
- Belum ada rate limiting login, reset kata sandi, MFA, atau pemaksaan ganti password awal.
- Belum ada pagination; daftar besar perlu pagination server-side.
- Terjemahan belum mencakup seluruh teks lama dan nilai status database.
- Belum ada penghapusan lampiran, antivirus scanning, thumbnail, atau multi-file sekali kirim.
- Belum ada pengujian otomatis untuk seluruh acceptance criteria PRD.
- Sites hosting tidak menjalankan PHP. Produksi membutuhkan Apache/Nginx dengan PHP-FPM atau hosting PHP lain.

## 8. Prioritas pengembangan berikutnya

1. Implementasikan verifikasi hasil tugas: Terima, Tolak/Revisi, catatan revisi, dan riwayat status.
2. Pecah front controller menjadi router, controller, service, repository, dan policy.
3. Lengkapi instalasi software per PC serta kebutuhan software kegiatan.
4. Buat importer XLSX dengan pratinjau, sanitasi kredensial, validasi, dan idempotensi.
5. Lengkapi terjemahan Inggris melalui katalog terpusat.
6. Tambahkan pagination, filter status/lokasi/PIC, dan penyimpanan filter.
7. Tambahkan checklist pemeriksaan dan kesiapan kegiatan.
8. Implementasikan notifikasi jatuh tempo dan software perlu dihapus.
9. Siapkan migration MySQL/MariaDB serta staging deployment.
10. Tambahkan integration test untuk acceptance criteria PRD.

