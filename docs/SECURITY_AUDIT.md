# SIMIT — Audit Keamanan

Audit awal: 23 Oktober 2026  
Pemeriksaan lanjutan: 2 November 2026

## Temuan dan perbaikan

| Area | Risiko | Perbaikan |
|---|---|---|
| Lampiran tugas | IDOR: pengguna login dapat mencoba mengunduh lampiran milik tugas lain hanya dengan mengganti ID URL. | Endpoint lampiran sekarang memeriksa role admin, pemilik tugas, PIC, atau tugas tanpa PIC (tugas bersama). Jika tidak berwenang, respons dibuat 404. |
| Login | Tidak ada pembatasan percobaan sehingga password dapat ditebak berulang kali. | Migration `011_security_hardening.sql` menambah log percobaan login. Batasnya 30 kegagalan per IP atau 8 kegagalan untuk pasangan IP+email dalam 15 menit; data lama dibersihkan otomatis. |
| Session | Cookie session belum menandai `Secure` pada deployment HTTPS. | `cookie_secure` otomatis aktif saat koneksi HTTPS, bersama `HttpOnly`, `SameSite=Lax`, dan strict session mode. |
| Browser hardening | Belum ada header perlindungan browser. | Ditambahkan CSP, `X-Content-Type-Options`, `X-Frame-Options`, Referrer-Policy, Permissions-Policy, dan HSTS saat HTTPS. |
| File privat | Folder storage berisi database, session, avatar, dan lampiran. | Ditambahkan `storage/.htaccess` untuk Apache. Document root tetap wajib diarahkan ke folder `public`. |
| Data demo | Akun demo biasa berisiko memperoleh akses tidak sengaja ke data utama. | Demo dibuat sebagai pratinjau read-only tanpa akun dan routing-nya selesai sebelum koneksi database dibuat. Seluruh data berasal dari fixture statis. |
| Session | Session login dapat bertahan terlalu lama pada komputer bersama. | Idle timeout 30 menit, batas absolut 12 jam, rotasi ID saat login, dan penghapusan cookie saat logout. |
| Password | Kebijakan minimum lama terlalu lemah untuk akun operasional. | Password baru minimal 12 karakter dengan huruf besar, huruf kecil, angka, dan simbol; Argon2id digunakan bila tersedia dan hash lama diperbarui saat login. |
| Data cache | Halaman sensitif berpotensi tersimpan pada cache browser/proxy. | Respons dinamis memakai `Cache-Control: no-store, private`. |
| API | Tidak ada API yang dibutuhkan, tetapi pola URL dapat dicoba pengunjung. | Seluruh `/api` mengembalikan 404 dan tidak membuka data. |
| Konfigurasi | Seeder contoh dapat mengisi ulang data palsu pada produksi. | Seeder ditolak pada `APP_ENV=production` kecuali diaktifkan eksplisit untuk lingkungan lokal. |

## Kontrol yang sudah ada

- Password disimpan dengan `password_hash()`/diverifikasi dengan `password_verify()`.
- CSRF token wajib untuk operasi POST.
- Query memakai prepared statement.
- Nama file upload disimpan sebagai nama acak; nama asli tidak menjadi path filesystem.
- MIME upload diperiksa dengan `finfo`, ukuran dibatasi, dan upload disimpan di luar document root.
- Output HTML menggunakan escaping `e()` pada data pengguna.
- Role admin/user diperiksa di server, bukan hanya di antarmuka.

## Catatan deployment

1. Jalankan `php bin/migrate.php` setelah mengambil versi kode ini.
2. Gunakan HTTPS pada server kerja agar cookie Secure dan HSTS aktif.
3. Arahkan web server ke `public/`, bukan root project.
4. Pastikan `storage/` tidak dapat diakses langsung. Untuk Nginx, blokir lokasi `/storage/` secara eksplisit karena `.htaccess` hanya berlaku untuk Apache.
5. Backup database sebelum migration dan jangan commit `.env`, database, atau file upload.
6. Jangan menjalankan `bin/seed.php` pada produksi. Demo publik sudah memiliki fixture read-only tersendiri.

## Kondisi data setelah persiapan produksi

- Pada saat pembersihan, database aktif hanya mempertahankan akun Admin; akun User dan seluruh data operasional dihapus. Audit login Admin dan log pembatasan login akan terbentuk kembali saat aplikasi digunakan.
- Schema dan riwayat migration dipertahankan.
- Backup sebelum pembersihan disimpan privat di `storage/backups/` dan diabaikan Git.
- Folder upload dikosongkan dan avatar akun non-admin dihapus.
- Demo tidak memakai tabel `users`, `assets`, `software`, `tasks`, `messages`, atau `attachments`, serta menolak seluruh request selain GET dengan status 405.

## Batasan audit

Audit ini berbasis source code serta smoke/workflow/communication/security test. Belum menggantikan penetration test eksternal, pengujian konfigurasi server, antivirus file upload, MFA, atau pemeriksaan firewall/WAF produksi.
