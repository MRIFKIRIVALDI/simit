# SIMIT — Audit Keamanan

Tanggal audit: 23 Oktober 2026

## Temuan dan perbaikan

| Area | Risiko | Perbaikan |
|---|---|---|
| Lampiran tugas | IDOR: pengguna login dapat mencoba mengunduh lampiran milik tugas lain hanya dengan mengganti ID URL. | Endpoint lampiran sekarang memeriksa role admin, pemilik tugas, PIC, atau tugas tanpa PIC (tugas bersama). Jika tidak berwenang, respons dibuat 404. |
| Login | Tidak ada pembatasan percobaan sehingga password dapat ditebak berulang kali. | Migration `011_security_hardening.sql` menambah log percobaan login. Maksimal 8 kegagalan per IP/email dalam 15 menit; data lama dibersihkan otomatis. |
| Session | Cookie session belum menandai `Secure` pada deployment HTTPS. | `cookie_secure` otomatis aktif saat koneksi HTTPS, bersama `HttpOnly`, `SameSite=Lax`, dan strict session mode. |
| Browser hardening | Belum ada header perlindungan browser. | Ditambahkan CSP, `X-Content-Type-Options`, `X-Frame-Options`, Referrer-Policy, Permissions-Policy, dan HSTS saat HTTPS. |
| File privat | Folder storage berisi database, session, avatar, dan lampiran. | Ditambahkan `storage/.htaccess` untuk Apache. Document root tetap wajib diarahkan ke folder `public`. |

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

## Batasan audit

Audit ini berbasis source code dan smoke/workflow/communication test. Belum menggantikan penetration test eksternal, pemindaian dependency, atau konfigurasi firewall/WAF server produksi.
