# Changelog

## Security hardening — 2026-10-23

- Menutup potensi IDOR pada endpoint unduh lampiran dengan pemeriksaan akses berbasis tugas.
- Menambahkan pembatasan percobaan login melalui migration `011_security_hardening.sql`.
- Menambahkan security headers browser dan cookie session Secure otomatis pada HTTPS.
- Menambahkan aturan Apache untuk menolak akses langsung ke `storage/`.
- Mendokumentasikan temuan, kontrol, dan catatan deployment di `docs/SECURITY_AUDIT.md`.

## 2026-10-23 — Dokumentasi sistem desain

- Menambahkan `DESIGN.md` sebagai acuan warna, tipografi, komponen, ikon, layout, responsive behavior, landing page, laporan, dan aturan konsistensi UI.

## 2026-10-15 — Fokus detail Inventaris PC

- Panel detail teknis tambahan kini hanya ditampilkan pada halaman Inventaris PC.
- Halaman Software dan Perangkat Lain tidak lagi menampilkan panel detail PC.

## 2026-10-15 — Tugas otomatis software dan detail inventaris desktop

- Status software `Perlu Install` otomatis membuat tugas `Instal {nama software}`.
- Status `Perlu Dihapus` otomatis membuat tugas `Hapus {nama software}`.
- Nomor tugas otomatis mengikuti urutan `TSK-###` berikutnya.
- Tugas software otomatis berstatus `Dikerjakan` agar langsung masuk antrean pengerjaan.
- Duplikasi tugas aktif untuk software dan jenis pekerjaan yang sama dicegah.
- Tabel inventaris menampilkan panel detail teknis tambahan pada desktop; tampilan mobile tetap ringkas.
- Detail PC mencakup nama komputer, IP, prosesor, RAM, dan sistem operasi.

## 2026-10-12 — Penyempurnaan UI tugas kolaboratif

- Kolom detail tugas dan form kontribusi dibuat sama lebar agar tidak saling bertabrakan.
- Detail PIC menggunakan layout grid yang aman untuk teks panjang.
- Tugas tanpa PIC tidak lagi terkunci ketika tombol **Ambil** ditekan; tombol langsung membuka halaman kontribusi.
- Kontributor terisi otomatis setelah User menyimpan kontribusi atau mengunggah lampiran.
- Layout kontribusi berubah menjadi satu kolom pada layar kecil.

## 2026-10-12 — Tugas kolaboratif dan pembuatan tugas oleh User

- User dapat membuat tugas dari menu Tugas.
- Tugas baru dari User otomatis berstatus `Tersedia`, tanpa PIC, dan tetap melewati ACC Admin pada saat pengajuan hasil final.
- Admin tetap dapat memilih satu PIC atau membiarkan PIC kosong saat membuat/mengubah tugas.
- Tugas tanpa PIC terlihat oleh semua User dan tidak lagi terkunci hanya karena satu User mulai membantu.
- User dapat menambahkan kontribusi bantuan dan lampiran secara terpisah pada tugas yang sama.
- Satu User dapat menandai kontribusinya sebagai pengajuan final; tugas masuk `Menunggu Verifikasi` dan hasilnya tersinkron untuk semua pihak setelah ACC.
- Menambahkan migration `009_task_collaboration.sql` dan halaman kontribusi kolaboratif.

## 2026-10-12 — Penyempurnaan terjemahan Inggris

- Menambahkan katalog terjemahan UI umum pada helper `__()`.
- Dashboard kini menerjemahkan ringkasan, statistik, tugas prioritas, status aset, dan pesan kosong.
- Form CRUD menerjemahkan judul modul, label field, pilihan, tombol, dan placeholder.
- Tabel inventaris menerjemahkan header kolom serta aksi umum.
- Halaman login dan audit menerjemahkan konten utama, bukan hanya menu.
- Status database seperti `Tersedia`, `Dikerjakan`, `Selesai`, `Draft`, dan status software memiliki label Inggris.

## 2026-10-08 — Dokumentasi deployment dan rencana MySQL

- Menambahkan `docs/DEPLOYMENT_AND_MYSQL.md`.
- Mendokumentasikan database SQLite yang sedang digunakan.
- Mendokumentasikan perbedaan PHP development server dan XAMPP.
- Mendokumentasikan langkah rencana migrasi SQLite ke MySQL/MariaDB.
- Mendokumentasikan backup, staging, cutover, struktur deployment, hosting, dan checklist keamanan kerja.
- Menandai adapter MySQL/MariaDB sebagai pekerjaan yang belum diimplementasikan.

## 2026-10-08 — Penyempurnaan laporan cetak

- Judul laporan, judul setiap panel, dan seluruh header kolom tabel dibuat rata tengah.
- Menambahkan satu area tanda tangan kosong tanpa label jabatan di sisi kanan laporan agar identitas penandatangan dapat ditulis menggunakan pena.
- Menambahkan watermark logo SIMIT hijau transparan di tengah setiap halaman cetak.
- Watermark memakai elemen gambar tetap pada lapisan teratas sehingga tidak tertutup panel putih dan tetap tercetak tanpa bergantung pada opsi background browser.
- Watermark ditempatkan di belakang konten agar teks dan tabel tetap terbaca.
- Baris riwayat dijaga agar tidak terpotong di antara halaman dan area tanda tangan tidak dipecah.
- Menambahkan terjemahan Inggris untuk elemen laporan yang diperbarui.

## 2026-10-07 — Landing page portfolio publik

- `/` menampilkan landing page bagi pengunjung yang belum login; pengguna terautentikasi tetap melihat dashboard.
- Landing page menjelaskan fitur, alur kerja, role Admin/User, dan gambaran antarmuka tanpa mengekspos data internal.
- Menambahkan mockup dashboard 3D interaktif berbasis CSS, kartu glassmorphism, animasi ringan, dan warna identitas hijau SIMIT.
- Mendukung light/dark mode, Bahasa Indonesia/English, desktop, tablet, ponsel, serta preferensi reduced motion.
- Halaman login kini memiliki tautan kembali ke landing page.
- Menambahkan `src/Views/landing/index.php`, `landing.css`, dan `landing.js`.

## 2026-10-07 — Notifikasi dapat dibuka

- Seluruh kartu notifikasi kini dapat diklik dan memiliki indikator panah.
- Notifikasi chat membuka percakapan dengan pengirim pesan.
- Tugas baru serta hasil ACC/revisi membuka detail tugas terkait.
- Pengumpulan tugas membuka antrean ACC bagi Admin.
- Notifikasi otomatis ditandai sudah dibaca ketika dibuka.
- Redirect hanya menerima URL internal untuk mencegah open redirect.
- Menambahkan migration `008_notification_links.sql`, termasuk tujuan fallback untuk notifikasi lama.

## 2026-10-07 — Pemeliharaan inventaris oleh User dan profil lengkap

- User dapat menambah dan mengubah Inventaris PC, Perangkat Lain, dan Software sebagai bagian pekerjaan operasional.
- Penghapusan data inventaris tetap dibatasi untuk Admin guna mencegah kehilangan data tidak disengaja.
- Profil ditambah dengan ID pegawai, jabatan, divisi/departemen, tanggal lahir, alamat, dan foto profil.
- Foto menerima JPG, PNG, atau WebP maksimum 2 MB, memakai nama acak, dan disimpan privat di `storage/avatars`.
- Foto profil tampil pada kartu profil dan sidebar; jika belum ada, aplikasi memakai inisial nama.
- Menambahkan migration `007_extended_profiles.sql` dan stylesheet `profile.css`.

## 2026-10-07 — Tugas bersama, notifikasi, dan chat internal

### Ditambahkan

- Tugas tanpa PIC menjadi tugas bersama yang dapat dibuka dan dikerjakan oleh Admin maupun User.
- Pengumpulan pertama mengklaim tugas secara atomik sehingga akun lain tidak dapat mengumpulkan tugas yang sama.
- Notifikasi dalam aplikasi untuk tugas baru, pengumpulan yang perlu di-ACC, hasil ACC/revisi, dan pesan chat baru.
- Halaman notifikasi dengan indikator jumlah belum dibaca dan aksi tandai semua telah dibaca.
- Chat langsung antar akun aktif beserta indikator pesan belum dibaca.
- Migration `005_chat_notifications.sql` dan `006_task_notification_trigger.sql`.
- Integration test `tests/communication.php` untuk klaim tugas bersama, chat, dan penghitung unread.

### Alur tugas bersama

- Selama belum diambil, tugas tanpa PIC terlihat bagi semua akun yang berhak.
- Saat salah satu akun mengumpulkan hasil, akun tersebut otomatis menjadi PIC dan tugas masuk `Menunggu Verifikasi` secara global.
- Tugas tidak lagi dapat dikerjakan akun lain; setelah disetujui admin, tugas muncul sebagai riwayat `Selesai`.
- Jika admin meminta revisi, tugas kembali ke akun yang pertama mengumpulkan hasil.

## 2026-10-07 — Interaksi tabel, laporan, dan ikon navigasi

- Seluruh baris tabel Tugas dapat diklik untuk membuka detail/hasil.
- Tombol tambahan “Hasil” di bawah tabel dihilangkan setelah halaman dimuat.
- Klik pada tombol Ambil/Ubah/Hapus tidak memicu navigasi baris.
- Baris tugas mendukung keyboard Enter dan Space.
- Responsivitas laporan ditingkatkan untuk tablet, ponsel, dan mode cetak.
- Dashboard, ACC Tugas, Profil Saya, Akun, dan Audit memakai SVG yang berbeda dan sesuai makna.

## 2026-10-07 — Penyederhanaan role dan alur ACC

### Diubah

- Role disederhanakan menjadi `admin` dan `user`; seluruh role lama non-admin dimigrasikan menjadi `user`.
- Menu Kegiatan dihapus dari navigasi dan modul aktif.
- Menu Pemeriksaan dihapus dari sisi user.
- Pada admin, Pemeriksaan diganti menjadi **ACC Tugas**.
- Tugas yang dikumpulkan user berstatus `Menunggu Verifikasi`, hilang sementara dari menu Tugas, dan masuk antrean ACC Admin.
- Admin dapat menyetujui tugas menjadi `Selesai` atau mengembalikannya menjadi `Dikerjakan` dengan alasan revisi.
- Tugas yang disetujui kembali terlihat di menu Tugas sebagai riwayat.
- Seluruh aset pada lokasi `Lab Komputer` dipindahkan ke `Lab 1`; lokasi `Lab Komputer` dihapus.
- Software kini memiliki tanggal mulai dan selesai penggunaan.
- Status `Perlu Install` atau `Perlu Dihapus` otomatis membuat tugas tersedia dengan tenggat sesuai periode software.
- Laporan diperluas untuk inventaris, status tugas, status software, pengguna, dan riwayat tugas selesai.

### Migration

- Menambahkan `004_two_roles_approval_software_period.sql`.

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

