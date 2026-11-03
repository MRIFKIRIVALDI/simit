# Security Policy

Dokumen ini adalah pintu masuk kebijakan keamanan repository SIMIT. Requirement ringkas juga dicatat di PRD, sedangkan aturan implementasi lengkap berada di [`docs/SECURE_CODING_POLICY.md`](docs/SECURE_CODING_POLICY.md).

## Aturan wajib

1. File `.env`, database, session, upload, backup, dan kredensial tidak boleh masuk Git.
2. Dependency baru wajib berasal dari sumber resmi, diperiksa nama/publisher-nya, dikunci dengan lock file, dan diaudit sebelum digabungkan.
3. Kode mati, cabang mustahil, dan view yang tidak dipakai harus dibersihkan setelah verifikasi referensi dan tes regresi.
4. Setiap ID dari URL/form harus diperiksa keberadaan serta hak aksesnya pada server. Data privat yang bukan milik pengguna merespons 404 agar tidak membocorkan keberadaan record.
5. Semua nilai input SQL wajib memakai prepared statement dan parameter binding.
6. Identifier SQL seperti nama tabel, kolom sorting, serta arah sorting tidak dapat di-binding; hanya boleh berasal dari allowlist server.
7. Otorisasi dipusatkan dalam policy dan dipanggil pada setiap endpoint sensitif, bukan hanya disembunyikan dari UI.

## Pelaporan

Jangan membuka issue publik yang memuat rahasia, data produksi, atau langkah eksploitasi aktif. Laporkan secara privat kepada pengelola repository dan sertakan endpoint, dampak, serta cara reproduksi minimum. Jangan menyertakan database atau `.env` asli.

## Batas jaminan

Kontrol di repository mengurangi risiko, tetapi tidak menjamin aplikasi tidak dapat diretas. Deployment tetap membutuhkan HTTPS, konfigurasi web server yang benar, patch PHP/OS, backup terenkripsi, monitoring, dan pengujian keamanan berkala.
