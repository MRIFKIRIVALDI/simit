# Demo dan Isolasi Data SIMIT

Terakhir diperbarui: 2 November 2026.

## Tujuan

Mode demo memungkinkan pengunjung portfolio melihat bentuk aplikasi tanpa akun dan tanpa akses ke data kerja. Demo bukan role database dan tidak membuat baris pada tabel `users`.

## Cara masuk

Tombol **Lihat Demo** hanya berada di bawah tombol **Masuk** pada `/login`. Landing page tidak menyediakan tombol demo langsung. Pengunjung dapat keluar dari demo menuju halaman login.

## Halaman yang tersedia

| URL | Isi |
| --- | --- |
| `/demo` | Dashboard User contoh |
| `/demo/pc` | Inventaris PC contoh |
| `/demo/perangkat` | Perangkat lain contoh |
| `/demo/software` | Software contoh |
| `/demo/tugas` | Tugas dan status contoh |
| `/demo/chat` | Simulasi percakapan read-only |
| `/demo/laporan` | Ringkasan laporan contoh tanpa cetak/ekspor |
| `/demo/profil` | Profil fiktif read-only |

## Batas keamanan

- Routing `/demo*` diselesaikan sebelum `Database::connection()` dipanggil.
- Data berasal dari fixture statis `Simit\Core\Demo::preview()`.
- Demo tidak membuat session login dan tidak memakai role Admin/User produksi.
- Hanya request GET yang diterima. POST, PUT, PATCH, dan DELETE ditolak dengan HTTP 405.
- Tidak ada form simpan, tambah, edit, hapus, upload, pengiriman chat, notifikasi aktif, cetak, atau ekspor.
- Demo tidak membaca tabel akun, inventaris, software, tugas, pesan, notifikasi, audit, maupun lampiran.
- Layout demo tidak memanggil penghitung chat/notifikasi produksi.

## File implementasi

- `src/Core/Demo.php`: fixture statis.
- `src/Views/layouts/demo.php`: shell aplikasi menyerupai role User.
- `src/Views/demo/app.php`: isi modul read-only.
- `public/assets/css/demo.css`: gaya login demo dan UI demo.
- `public/index.php`: whitelist rute dan pembatasan GET-only.

## Aturan perubahan berikutnya

Jangan menghubungkan demo ke PDO, `Auth::user()`, tabel produksi, upload, atau API internal. Jika menambah modul demo, tambahkan fixture statis, whitelist GET, tampilan tanpa form mutasi, lalu perbarui `tests/security.php` dan dokumen ini.

## Verifikasi

```powershell
php tests/security.php
```

Selain test otomatis, pastikan `/login` menampilkan tombol demo setelah tombol Masuk, landing page tidak memiliki link `/demo`, semua menu demo memberikan HTTP 200, halaman demo tidak memiliki elemen `<form>`, dan POST ke `/demo/tugas` memberikan HTTP 405.
