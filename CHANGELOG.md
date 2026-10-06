# Changelog

## 2026-10-06 — MVP awal

### Ditambahkan

- Aplikasi PHP dan SQLite dengan migration serta seeder.
- Login internal tanpa registrasi publik.
- Hak akses admin/koordinator dan audit aktivitas.
- CRUD PC, perangkat lain, software, tugas, pemeriksaan, dan kegiatan.
- Dashboard, laporan per lokasi, dan tampilan responsif neumorphism.
- Pengelolaan akun oleh admin.
- Profil pengguna dan perubahan kata sandi.
- Dark mode dan pengalih Bahasa Indonesia/English.
- Logo hijau/putih berdasarkan kontras permukaan.
- Ikon bulan dan matahari untuk pengalih tema.
- Baris tugas prioritas dan perangkat perhatian yang dapat diklik.
- Hasil tugas berupa teks serta lampiran JPG/PNG/PDF maksimum 10 MB.
- Penyimpanan lampiran privat dan endpoint download terautentikasi.

### Keamanan

- Password hashing, prepared statement, CSRF, strict session, validasi MIME, nama file acak, dan pembatasan hasil tugas berdasarkan PIC.

### Diketahui belum lengkap

- Verifikasi terima/tolak hasil tugas.
- Importer XLSX dan ekspor terstruktur.
- Terjemahan Inggris penuh.
- Instalasi software per PC dan checklist kegiatan terperinci.
- Pagination, notifikasi aktif, backup otomatis, dan deployment produksi.

