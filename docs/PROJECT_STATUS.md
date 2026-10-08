# Status Pengembangan SIMIT

Dokumen ini adalah handoff utama untuk pengembang yang melanjutkan SIMIT. Terakhir diperbarui 7 Oktober 2026.

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
| Landing page publik | Selesai | Portfolio tanpa login berisi mockup dashboard, fitur, workflow, role, dan CTA login dengan glassmorphism/3D. |
| Hak akses | Selesai untuk MVP | Hanya ada Admin dan User. Admin mengelola master, akun, dan ACC; User mengambil serta mengerjakan tugas. |
| Pengelolaan akun | Selesai untuk MVP | Admin dapat membuat, mengubah, mengaktifkan, dan menonaktifkan akun. Admin tidak dapat menonaktifkan diri sendiri. |
| Profil pengguna | Selesai | Nama, email, ID pegawai, telepon, jabatan, divisi, tanggal lahir, alamat, bio, foto, dan kata sandi dapat diperbarui. |
| Dashboard | Selesai untuk MVP | Kartu ringkasan, tugas prioritas, dan perangkat perlu perhatian. Baris dashboard dapat diklik dan digunakan dengan keyboard. |
| Inventaris PC | Selesai untuk MVP | Admin dan User dapat menambah/mengubah aset PC; penghapusan khusus Admin. |
| Perangkat lain | Selesai untuk MVP | Admin dan User dapat menambah/mengubah perangkat; penghapusan khusus Admin. |
| Software | Selesai untuk MVP | Admin dan User dapat menambah/mengubah software; penghapusan khusus Admin. Instalasi per-PC belum lengkap. |
| Tugas IT | Selesai untuk MVP | CRUD, ambil tugas secara bersyarat, hasil teks, lampiran, dan status Menunggu Verifikasi. |
| Tugas tanpa PIC | Selesai | Terlihat untuk Admin dan User; pengumpulan pertama mengklaim tugas secara atomik dan menutup pengerjaan oleh akun lain. |
| Lampiran hasil | Selesai | JPG/PNG/PDF, maksimum 10 MB, disimpan di `storage/uploads`. |
| ACC tugas | Selesai untuk MVP | Pengumpulan user masuk antrean admin; admin dapat setujui atau kembalikan untuk revisi. |
| Pemeriksaan | Dinonaktifkan | Tidak tampil pada user; digantikan ACC Tugas pada admin. Tabel lama dipertahankan untuk kompatibilitas data. |
| Kegiatan | Dinonaktifkan | Dihapus dari menu dan routing aktif. Tabel lama belum dihapus agar migration tetap aman. |
| Laporan | Diperluas | Inventaris, tugas, software, pengguna, riwayat selesai, satu kolom tanda tangan kosong, dan watermark logo per halaman cetak. |
| Interaksi tabel tugas | Selesai | Seluruh baris dapat diklik; kolom Aksi hanya untuk tindakan seperti Ambil/Ubah/Hapus. |
| Audit | Selesai untuk MVP | Login dan perubahan penting dicatat. |
| Bahasa Inggris | Sebagian selesai | Navigasi, akun, profil, hasil tugas, dan sejumlah kontrol telah diterjemahkan. Sebagian teks lama masih Bahasa Indonesia. |
| Dark mode | Selesai | Preferensi disimpan di `localStorage`; ikon bulan/matahari mengikuti mode. |
| Logo kontras | Selesai | Logo hijau pada permukaan terang dan logo putih pada panel hijau/dark mode. |
| Import spreadsheet | Belum | Struktur tracking import tersedia, tetapi UI parser/pratinjau belum dibuat. |
| Ekspor | Belum | Laporan hanya dapat dicetak dari browser. |
| Verifikasi tugas | Selesai untuk MVP | Tugas pending tersembunyi dari daftar utama, masuk ACC admin, lalu kembali sebagai history setelah disetujui. |
| Notifikasi | Selesai untuk MVP | Bell menampilkan unread; kartu dapat diklik menuju chat, detail tugas, atau antrean ACC dan otomatis ditandai dibaca. |
| Chat internal | Selesai untuk MVP | Pesan langsung antar akun aktif, daftar percakapan, dan indikator unread. Saat ini memakai refresh halaman, belum WebSocket. |
| Backup/restore | Belum | Perlu prosedur dan pengujian sebelum produksi. |

## 3. Alur yang sudah berjalan

### Login dan akun

1. Pengunjung tanpa session membuka `/` untuk melihat landing page publik.
2. Tombol CTA mengarah ke `/login`; tidak ada data operasional nyata pada landing page.
3. Admin membuat akun melalui menu **Akun**.
4. Pengguna masuk menggunakan email dan kata sandi.
5. Tidak ada endpoint atau tombol pendaftaran publik.
6. Admin dapat memilih satu dari dua peran: Admin atau User.
7. Akun dapat dinonaktifkan tanpa menghapus riwayat.

### Tugas dan hasil pekerjaan

1. Admin membuat tugas dengan PIC tertentu atau membiarkan PIC kosong sebagai tugas bersama.
2. Tugas tanpa PIC dapat dibuka Admin dan User. Tugas dengan PIC hanya dikerjakan PIC tersebut atau Admin.
3. PIC atau pengambil pertama menulis hasil dan dapat melampirkan satu JPG, PNG, atau PDF pada setiap pengiriman.
4. Pengumpulan tugas tanpa PIC memakai update bersyarat atomik: pengirim pertama menjadi PIC, pengumpulan berikutnya ditolak.
5. Status menjadi `Menunggu Verifikasi`, tugas hilang dari daftar aktif semua akun, dan admin menerima notifikasi ACC.
6. Setelah ACC, akun pelaksana menerima notifikasi dan tugas kembali sebagai riwayat `Selesai`; revisi kembali ke PIC sebagai `Dikerjakan`.
7. Lampiran diunduh melalui endpoint aplikasi, bukan URL publik langsung.

### Notifikasi dan chat

1. Ikon lonceng membuka pusat notifikasi dan menampilkan jumlah notifikasi belum dibaca.
2. Tombol **Tandai semua dibaca** mengisi waktu baca seluruh notifikasi akun tersebut.
3. Menu **Chat** menampilkan semua akun aktif selain akun sendiri.
4. Pesan hanya dapat dikirim ke akun aktif. Membuka percakapan menandai pesan masuk dari kontak itu sebagai dibaca.
5. Pesan baru juga membuat notifikasi bagi penerima. Chat saat ini berbasis request/response dan perlu reload untuk pesan terbaru.

Tugas yang sedang menunggu ACC tidak ditampilkan pada daftar Tugas. Setelah disetujui, tugas kembali ke daftar sebagai riwayat berstatus Selesai. Jika ditolak, tugas kembali ke User sebagai Dikerjakan dengan catatan revisi.

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
- Foto profil divalidasi lewat MIME dan pemeriksaan gambar, dibatasi 2 MB, diberi nama acak, dan dilayani melalui endpoint terautentikasi.
- Admin tidak dapat menonaktifkan akun sendiri.
- Hasil tugas hanya dapat diisi PIC atau admin.

## 7. Keterbatasan penting

- SQL migration saat ini ditulis untuk SQLite. Jangan menganggap migration langsung kompatibel dengan MySQL/MariaDB.
- Aplikasi belum menggunakan framework atau Composer package.
- Router, controller, dan logika bisnis masih terkonsentrasi di `public/index.php`; perlu dipecah sebelum fitur membesar.
- Lampiran bisa diunduh semua pengguna yang sudah login. Policy akses per tugas perlu diperketat bila ada data sensitif.
- Belum ada rate limiting login, reset kata sandi, MFA, atau pemaksaan ganti password awal.
- Belum ada pagination; daftar besar perlu pagination server-side.
- Chat belum real-time (WebSocket/SSE), belum memiliki lampiran, edit, hapus, atau percakapan grup.
- Terjemahan belum mencakup seluruh teks lama dan nilai status database.
- Belum ada penghapusan lampiran, antivirus scanning, thumbnail, atau multi-file sekali kirim.
- Belum ada pengujian otomatis untuk seluruh acceptance criteria PRD.
- Sites hosting tidak menjalankan PHP. Produksi membutuhkan Apache/Nginx dengan PHP-FPM atau hosting PHP lain.

## 8. Prioritas pengembangan berikutnya

1. Pecah front controller menjadi router, controller, service, repository, dan policy.
2. Tambahkan tampilan riwayat status dan catatan revisi yang lebih rinci pada detail tugas.
3. Lengkapi instalasi software per PC serta kebutuhan software kegiatan.
4. Buat importer XLSX dengan pratinjau, sanitasi kredensial, validasi, dan idempotensi.
5. Lengkapi terjemahan Inggris melalui katalog terpusat.
6. Tambahkan pagination, filter status/lokasi/PIC, dan penyimpanan filter.
7. Tambahkan checklist pemeriksaan dan kesiapan kegiatan.
8. Tambahkan notifikasi terjadwal untuk jatuh tempo dan software perlu dihapus serta transport real-time untuk chat.
9. Siapkan migration MySQL/MariaDB serta staging deployment.
10. Tambahkan integration test untuk acceptance criteria PRD.

