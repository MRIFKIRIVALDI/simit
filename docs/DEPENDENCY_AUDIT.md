# Audit Dependency

Tanggal audit: 3 November 2026

## Hasil

- Tidak ditemukan `composer.json`, `composer.lock`, `package.json`, lock file JavaScript, `vendor/`, atau `node_modules/`.
- Aplikasi saat ini tidak membawa package PHP/JavaScript pihak ketiga. Runtime menggunakan PHP 8.2+, PDO, SQLite/MySQL driver, serta aset lokal.
- Dengan kondisi ini, tidak ada package aktif yang terindikasi typosquatting.

## Guardrail

- CI menolak manifest dependency tanpa lock file.
- Dependency baru harus ditinjau menggunakan checklist di `docs/SECURE_CODING_POLICY.md`.
- Gunakan registry dan repository upstream resmi; jangan memasang package hanya dari hasil pencarian atau nama yang tampak mirip.
- Catat nama package, versi, sumber resmi, alasan penggunaan, lisensi, dan hasil advisory scan pada pembaruan dokumen ini.
