# Kebijakan Secure Coding SIMIT

Status: wajib untuk seluruh perubahan kode mulai 3 November 2026.

## Penempatan aturan

- **PRD** menyatakan keamanan sebagai requirement dan acceptance criteria.
- **`SECURITY.md`** menjadi pintu masuk kebijakan repository dan cara pelaporan.
- **Dokumen ini** memuat standar implementasi, checklist review, dan contoh kode.
- **ERD** hanya mendokumentasikan tabel, foreign key, kepemilikan, serta relasi; bukan tempat prosedur secure coding.

## 1. Rahasia dan `.env`

- `.env`, `.env.*`, database, backup, session, dan upload dilarang masuk Git. Hanya `.env.example` tanpa nilai rahasia yang boleh dilacak.
- Workflow CI menggagalkan build bila menemukan environment file yang dilacak.
- Jika rahasia pernah terlanjur ter-push, menghapus file saja tidak cukup: cabut/rotasi rahasia, bersihkan riwayat Git secara terkoordinasi, dan audit akses.
- Production harus memakai secret/environment milik platform deployment, bukan nilai yang tertanam dalam source code.

## 2. Dependency dan typosquatting

Kondisi audit 3 November 2026: aplikasi tidak memiliki `composer.json`, `package.json`, lock file, `vendor/`, atau `node_modules`; runtime hanya memakai PHP/PDO bawaan. Karena itu tidak ada package pihak ketiga aktif untuk diaudit.

Sebelum menambah package:

1. Buka package dari registry resmi dan cocokkan link repository upstream.
2. Verifikasi ejaan, namespace/vendor, publisher, tanggal rilis, aktivitas maintenance, dan advisory keamanan.
3. Hindari package baru yang hanya membungkus fungsi kecil dari standard library.
4. Commit lock file; CI menolak manifest dependency tanpa lock file.
5. Jalankan audit resmi ekosistem dan tinjau script install/post-install sebelum instalasi.
6. Upgrade dalam perubahan terpisah agar diff dan tes mudah ditinjau.

## 3. Kode mati dan kode rusak

- Cari referensi sebelum menghapus fungsi/view.
- Hapus cabang `if (false)`, helper duplikat, variabel tidak terpakai, dan endpoint yang tidak lagi dirutekan.
- Setelah pembersihan, jalankan syntax check serta semua tes.
- Jangan menghapus file hanya berdasarkan nama; pastikan tidak dipanggil dari router, autoloader, include, atau dokumentasi operasi.

## 4. Ownership dan otorisasi setiap ID

ID dari route atau form selalu dianggap tidak tepercaya. Urutannya:

1. Parse ke tipe yang diharapkan.
2. Ambil record dengan parameter binding.
3. Jika record tidak ada, kembalikan 404.
4. Jalankan policy terhadap pengguna login dan record tersebut.
5. Untuk data privat yang tidak berhak dilihat, gunakan 404 agar ID valid tidak dapat ditebak.
6. Baru lakukan read/update/delete di dalam scope yang telah disahkan.

Matriks kepemilikan saat ini:

| Data | Boleh mengakses/mengubah |
|---|---|
| Profil | Pemilik profil; Admin tidak otomatis mengubah profil orang lain melalui endpoint profil |
| Akun | Admin saja; Admin aktif terakhir dilindungi |
| Tugas ber-PIC | Admin, PIC, pembuat, atau kontributor sesuai aksi policy |
| Tugas tanpa PIC | Semua User aktif dapat melihat/berkontribusi; satu hasil final tetap melalui ACC Admin |
| Lampiran tugas | Admin, PIC, pembuat tugas, pengunggah, kontributor, atau peserta tugas bersama |
| Chat | Hanya pengirim dan penerima pada query percakapan |
| Notifikasi | Selalu di-scope dengan `user_id` pengguna login |
| PC/perangkat/software | Data operasional bersama; Admin dan User dapat membuat/mengubah, hanya Admin dapat menghapus |
| Audit dan pengelolaan akun | Admin saja |

Policy berada di `src/Policies/`. Menyembunyikan tombol di UI bukan kontrol keamanan.

## 5. Parameter binding

Nilai input tidak boleh ditempel ke SQL.

```php
$stmt=$pdo->prepare('SELECT * FROM tasks WHERE id=? AND assignee_id=?');
$stmt->execute([$taskId,$userId]);
```

Dilarang:

```php
$pdo->query("SELECT * FROM tasks WHERE id=$taskId");
```

Prepared statement juga berlaku pada script CLI, migration helper, seeder, dan tests.

## 6. Sorting dan identifier SQL

Nama tabel, nama kolom, dan `ASC`/`DESC` tidak dapat diperlakukan sebagai value parameter. Gunakan allowlist server melalui `Simit\Core\Sql`.

```php
$allowed=['id','name','status','created_at'];
$sort=Sql::identifier((string)($_GET['sort']??'id'),$allowed);
$direction=Sql::direction((string)($_GET['direction']??'DESC'));
$stmt=$pdo->prepare("SELECT * FROM assets ORDER BY $sort $direction");
$stmt->execute();
```

Interpolasi di atas aman hanya karena identifier telah lolos exact allowlist dan validasi format. Jangan menerima nama tabel/kolom bebas dari request.

## 7. Policy terpusat

- `TaskPolicy`: lihat, kirim hasil, dan ACC tugas.
- `AttachmentPolicy`: unduh lampiran berdasarkan hubungan dengan tugas.
- `ModulePolicy`: create/update/delete modul operasional berdasarkan role.
- `AccountPolicy`: pengelolaan akun Admin.
- `ProfilePolicy`: profil sendiri dan avatar untuk pengguna terautentikasi.
- `Gate`: respons konsisten 403 atau hidden 404.

Setiap endpoint baru wajib menentukan policy sebelum membaca data sensitif atau melakukan mutasi.

## Checklist pull request

- Tidak ada `.env`, token, password, database, backup, session, atau upload pada diff/history.
- Dependency baru telah diverifikasi dan lock file ikut berubah.
- Seluruh input SQL menjadi bound value; identifier berasal dari allowlist.
- Semua ID mempunyai existence check dan policy/ownership check.
- Operasi mutasi memakai CSRF, validasi server, dan audit log bila relevan.
- Output pengguna di-escape dan upload divalidasi MIME, ukuran, serta lokasi penyimpanan.
- `php -l` dan seluruh test lulus.
