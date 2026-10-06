# PRD — SIMIT

**Sistem Inventaris dan Manajemen IT**

Web Inventaris Lab dan Alur Kerja Staf IT — GTTC

Draft v0.3 • 6 Oktober 2026 • Untuk tinjauan kebutuhan • Bahasa Indonesia

Tujuan dokumen: menjadi acuan produk SIMIT dari identitas merek, tampilan, fitur, alur kerja, hingga kebutuhan PHP dan database migration. Nama SIMIT, logo terpilih, PHP, dan penggunaan migration telah ditetapkan pengguna. Rincian operasional serta teknologi lainnya tetap berupa rancangan untuk tinjauan.

## 1. Nama dan identitas produk

| Elemen | Ketentuan |
| --- | --- |
| Nama aplikasi | **SIMIT** |
| Kepanjangan | **Sistem Inventaris dan Manajemen IT** |
| Deskripsi singkat | Web internal untuk inventaris lab, software, dan pengelolaan pekerjaan staf IT GTTC. |
| Identitas organisasi | GTTC — IT Division. |
| Bahasa / zona waktu | Bahasa Indonesia / Asia/Jakarta. |
| Logo terpilih | Ikon belalang bergaya minimalis, tampak depan, simetris, dua warna hijau solid, mengikuti referensi dan hasil yang disetujui pengguna. |
| Bentuk logo | Dua antena melengkung ke luar, mata oval besar, badan memanjang meruncing, dan elemen samping yang menyerupai kaki/sayap. |
| Format logo | PNG berlatar transparan; ikon tanpa tulisan dan tanpa gambar komputer. |
| Gaya logo | Bentuk sederhana, tepi bersih, tanpa gradasi, bayangan, efek cahaya, atau tekstur. |

Logo terpilih dapat dibuka melalui [referensi logo SIMIT di Canva](https://www.canva.com/M/MAHXMuJQvgc?utm_source=OC-AaBlKrqBJJNF&utm_campaign=agent_connector_create_image_asset_opened). Tautan ini menjadi rujukan visual; aplikasi produksi menggunakan aset lokal yang telah diperiksa, bukan memuat gambar dari tautan Canva secara langsung.

Makna merek yang diusulkan: belalang menggambarkan ketangkasan dan kesiapan bekerja, sedangkan tampilan simetris memberi kesan teratur. Makna ini merupakan narasi desain, bukan fitur aplikasi. Tidak perlu memaksakan bentuk huruf S ke logo karena bentuk referensi terakhir telah dipilih pengguna.

### 1.1 Penggunaan logo

- Gunakan bentuk logo terpilih secara konsisten pada halaman login, sidebar, dan identitas aplikasi.
- Tulisan **SIMIT** ditempatkan sebagai teks antarmuka di samping atau di bawah ikon; tidak ditambahkan ke file ikon PNG.
- Tambahkan kepanjangan nama pada halaman login atau informasi aplikasi; gunakan nama singkat pada navigasi.
- Jaga rasio gambar. Jangan merenggangkan, memutar, mengubah proporsi, atau menambahkan efek pada ikon.
- Gunakan ruang kosong minimal seperempat lebar ikon di sekelilingnya sebagai usulan awal.
- Untuk favicon, periksa keterbacaan pada 16/32 piksel. Varian sederhana hanya dibuat bila diperlukan dan bentuknya ditinjau sebelum digunakan.
- Latar kotak-kotak tidak boleh menjadi bagian gambar. Verifikasi alpha PNG sebelum aset dimasukkan ke aplikasi.

## 2. Arahan tampilan website

### 2.1 Warna dan gaya

Identitas visual mengikuti logo hijau yang disetujui. Palet berikut adalah usulan untuk antarmuka; warna logo final harus diambil dari aset terpilih dan tidak diganti berdasarkan nilai perkiraan ini.

| Penggunaan | Warna awal | Arahan |
| --- | --- | --- |
| Aksi utama / aksen | `#2E8B3C` | Hijau untuk tombol utama dan penanda navigasi aktif. |
| Identitas gelap | `#123F28` | Hijau tua untuk teks merek dan aksen kuat. |
| Latar halaman | `#F7F9F8` | Netral terang. |
| Panel / tabel | `#FFFFFF` | Putih agar data mudah dibaca. |
| Teks utama | `#1F2937` | Gelap dengan keterbacaan yang jelas. |
| Garis / pemisah | `#E5E7EB` | Pemisah tabel dan formulir. |

Gunakan warna solid, ruang antar elemen yang cukup, tipografi sans-serif, serta ikon navigasi yang konsisten. Warna peringatan dan error dapat memakai amber/merah; pembatasan dua warna berlaku pada logo, bukan semua status aplikasi. Status harus memiliki label teks dan tidak bergantung pada warna saja. Kontras teks dan tombol diperiksa sebelum desain diterapkan.

### 2.2 Layout dan pengalaman pengguna

- Desktop: sidebar berisi ikon dan nama SIMIT; area atas menampilkan judul halaman, pencarian yang relevan, notifikasi, serta akun pengguna.
- Ponsel: navigasi dapat dilipat, tombol utama mudah dijangkau, tabel memiliki scroll yang jelas atau tampilan kartu bila lebih sesuai.
- Dashboard: kartu ringkasan, perangkat perlu perhatian, tugas saya, dan kesiapan kegiatan; setiap angka dapat membuka daftar terkait.
- Daftar data: pencarian, filter, pagination, tindakan tambah, dan ekspor ditampilkan konsisten. Filter tetap tersimpan saat kembali dari detail.
- Detail perangkat: informasi utama, kondisi terbaru, software, tugas terkait, dan riwayat dapat diakses tanpa mengulang pencarian.
- Detail tugas: status, PIC, prioritas, tenggat, instruksi, perangkat terkait, checklist, hasil, dan revisi terlihat jelas.
- Formulir: kolom wajib diberi tanda; error ditempatkan dekat kolom; perubahan yang belum tersimpan memberi peringatan saat meninggalkan halaman.
- Halaman kosong memberi penjelasan serta tindakan berikutnya; halaman gagal menyediakan opsi mencoba kembali.
- Logo mempunyai teks alternatif; tombol ikon mempunyai label aksesibel; alur utama dapat digunakan melalui keyboard.

### 2.3 Halaman login

Tampilkan logo, nama **SIMIT**, kepanjangan **Sistem Inventaris dan Manajemen IT**, dan identitas **GTTC — IT Division**. Sediakan kolom akun/email dan kata sandi, tombol Masuk, serta pesan kegagalan yang jelas. Akun dibuat admin; pendaftaran publik bukan bagian MVP. Mekanisme reset kata sandi ditentukan sesuai metode autentikasi sebelum implementasi.

## 3. Ringkasan produk

SIMIT adalah sistem internal GTTC untuk mencatat perangkat lab dan perangkat di luar lab, mengelola software pada setiap PC, serta mengatur tugas staf IT, peserta PKL, dan magang. Inventaris terhubung dengan tugas agar masalah, pemeriksaan, instalasi, dan perpindahan perangkat memiliki riwayat yang dapat ditelusuri.

Hasil yang diharapkan: koordinator mengetahui perangkat yang siap dipakai, pekerjaan yang belum selesai, siapa PIC-nya, serta kebutuhan persiapan kegiatan tanpa merekap dashboard secara manual.

## 4. Dasar kebutuhan dari spreadsheet

| Sumber / sheet | Kebutuhan yang terbaca | Implikasi untuk website |
| --- | --- | --- |
| Pengelolaan Perangkat / Dashboard | Ringkasan PC, perangkat lain, dan daftar perlu perhatian | Angka dan daftar dihitung otomatis dari data aktif |
| Data PC | 42 PC; IP, RAM, storage, prosesor, OS, kondisi periferal, update OS, catatan | Detail PC dan riwayat pemeriksaan serta perubahan |
| Data Software | Aplikasi, versi, PC tujuan, keperluan, status instalasi, target hapus, PIC | Catatan instalasi per PC dan tindak lanjut penghapusan |
| Perangkat Lain | Inventaris jaringan, komputer, perlengkapan, multimedia, kelistrikan; jumlah dan lokasi | Mendukung perangkat individual maupun inventaris kelompok |
| Daftar Tugas | 27 tugas bernama: 11 Selesai, 4 Tersedia, 12 belum memiliki status | Papan tugas, ambil tugas, PIC, estimasi, status, tanggal, catatan hasil |
| Catatan pekerjaan | Revisi artikel dan pekerjaan di luar inventaris | Tugas umum IT tetap dapat dibuat tanpa perangkat terkait |

Analisis ini menggunakan salinan spreadsheet yang dilampirkan, bukan verifikasi versi terbaru di Google Drive. Sumber: Salinan Pengelolaan_Perangkat_Lab.xlsx dan Salinan Daftar_Tugas.xlsx. File sumber tidak diubah.

## 5. Masalah data yang perlu ditangani

| Temuan | Aturan yang diusulkan |
| --- | --- |
| Dashboard menampilkan 46 perangkat lain; tabel memuat 45 baris ber-ID serta 22 baris bernama tanpa ID | Jangan mengimpor angka dashboard. Tinjau baris tanpa ID sebagai kandidat data, bukan otomatis tambahan atau duplikat. |
| Satu baris kursi lipat berjumlah 16 berstatus Perlu Servis, tetapi kondisi fisik Baik | Catat jumlah unit bermasalah terpisah. Tidak boleh menyimpulkan seluruh 16 unit rusak. |
| 27 tugas bernama, tetapi 12 tanpa status | Masukkan sebagai draft perlu tinjauan; jangan otomatis dianggap tersedia atau selesai. |
| Tugas cleaning: tanggal selesai lebih awal daripada tanggal ambil | Tandai untuk koreksi saat impor; pada input baru tanggal selesai tidak boleh lebih awal. |
| Storage 1/2 berisi angka GB dengan makna belum jelas | Pisahkan kapasitas total dan ruang kosong; simpan nilai asal sampai maknanya dikonfirmasi. |
| Nama PC pada target software berbeda format; ada target Semua PC dan rentang PC | Resolusi alias dan pratinjau PC tujuan sebelum menyimpan; simpan hubungan per PC. |
| Lokasi PC masih umum, sedangkan perangkat lain memiliki Lab 1 / Lab 2 | Konfirmasi pemetaan PC ke ruangan; jangan menebak. |
| Catatan tugas memuat kredensial akun | Jangan menyalin kata sandi ke PRD, data impor, atau catatan aplikasi. Catatan akses cukup merujuk pengelola akun. |

## 6. Pengguna dan hak akses — usulan

| Peran | Hak utama |
| --- | --- |
| Admin / Koordinator IT | Mengelola akun, master lokasi, inventaris, tugas, penugasan, impor, laporan, dan verifikasi hasil. |
| Staf IT | Melihat inventaris, mengambil tugas yang tersedia, menjalankan tugas sendiri, mencatat pemeriksaan dan hasil pekerjaan. |
| PKL / Magang | Melihat tugas dan inventaris yang diizinkan; mengambil tugas; mengajukan perubahan inventaris melalui hasil kerja untuk diverifikasi koordinator. |
| Pimpinan / Viewer (opsional) | Melihat dashboard dan laporan; tanpa akses ubah. |

Akun individu digunakan untuk PIC; label PKL/MAGANG menjadi jenis anggota atau kelompok, bukan identitas pelaksana akhir. Pembatasan akses PKL dan kewajiban verifikasi perlu disetujui saat tinjauan.

## 7. Cakupan versi pertama (MVP)

| ID | Fitur wajib yang diusulkan | Perilaku / hasil |
| --- | --- | --- |
| FR-01 | Login dan hak akses | Akun internal; pengguna hanya mengakses tindakan sesuai perannya. |
| FR-02 | Dashboard otomatis | Total PC, status PC, jumlah jenis/kelompok perangkat, jumlah unit, tugas per status, tugas terlambat, perlu perhatian. |
| FR-03 | Inventaris PC | Tambah/ubah/arsip PC; pencarian nama/IP, filter lokasi/status; halaman detail dan riwayat. |
| FR-04 | Perangkat lain | Kategori, kode, merek/model, jumlah, lokasi, kondisi, PIC; dukungan kelompok unit dengan kondisi berbeda. |
| FR-05 | Software | Master aplikasi dan catatan instalasi per PC; jadwal hapus; status instalasi dan PIC. |
| FR-06 | Tugas IT | Daftar dan kanban, prioritas, estimasi, PIC, ambil tugas, tenggat, catatan, lampiran hasil. |
| FR-07 | Pemeriksaan dan penanganan masalah | Checklist pemeriksaan, temuan, tugas tindak lanjut, riwayat solusi, verifikasi kondisi perangkat. |
| FR-08 | Persiapan kegiatan | Tugas dengan nama/tanggal kegiatan, daftar PC/perangkat, software, checklist kesiapan dan pembersihan setelah kegiatan. |
| FR-09 | Impor awal dan ekspor | Pratinjau impor Excel, validasi, penanganan duplikat, ringkasan baris diterima/ditolak; ekspor inventaris dan pekerjaan. |
| FR-10 | Riwayat dan pengingat dalam aplikasi | Jejak perubahan; pengingat tenggat, pekerjaan tertunda, software perlu dihapus, dan pemeriksaan jatuh tempo. |
| FR-11 | Identitas SIMIT dan antarmuka | Logo terpilih, nama aplikasi, login, navigasi, dan gaya visual konsisten sesuai bagian 1–2. |

Di luar MVP: sinkronisasi dua arah Google Sheets, monitoring PC otomatis/agent, remote desktop, pengadaan dan depresiasi aset, peminjaman lengkap, integrasi WhatsApp/email, sinkronisasi event GTTC, modul project management lengkap. Fitur ini dapat dibahas setelah alur utama disetujui.

## 8. Struktur menu dan halaman

| Menu | Isi halaman |
| --- | --- |
| Dashboard | Ringkasan, perhatian perangkat, tugas saya, tenggat terdekat, kesiapan kegiatan. |
| Inventaris → PC | Tabel, filter, formulir, detail, periferal terkait, software, pemeriksaan dan tugas terkait. |
| Inventaris → Perangkat Lain | Tabel jumlah dan lokasi, kondisi per kelompok/unit, mutasi, detail. |
| Software | Daftar aplikasi; instalasi aktif, perlu install, perlu hapus, sudah dihapus; filter PC/kegiatan. |
| Tugas IT | Daftar/kanban; tab semua, tersedia, tugas saya; detail, checklist, komentar, lampiran, riwayat. |
| Pemeriksaan & Kegiatan | Jadwal pemeriksaan; persiapan kegiatan; hasil checklist dan temuan. |
| Laporan | Inventaris per lokasi/status, masalah belum selesai, pekerjaan per PIC/periode, software. |
| Pengaturan | Akun, peran, master lokasi/kategori, jadwal pemeriksaan, impor dan audit. |

## 9. Alur kerja yang diusulkan

### 9.1 Tugas umum staf IT

Koordinator atau anggota membuat Draft → koordinator menerbitkan Tersedia → staf mengambil tugas atau ditugaskan → Dikerjakan → Ajukan Selesai → Menunggu Verifikasi → koordinator menerima menjadi Selesai atau mengembalikan menjadi Dikerjakan dengan catatan revisi.

Tertunda dapat dipilih dari Dikerjakan dengan alasan dan rencana tindak lanjut; lanjutkan kembali ke Dikerjakan. Pembatalan oleh koordinator wajib disertai alasan. Tugas impor yang sudah Selesai tetap menjadi riwayat lama dan tidak dipaksa melewati verifikasi baru.

Pengambilan tugas harus atomik: jika dua pengguna mengambil tugas bersamaan, hanya satu berhasil. Satu PIC utama per tugas; anggota pendukung opsional. Status tugas, status perangkat, dan status software dikelola terpisah.

### 9.2 Masalah perangkat

Staf memilih perangkat → mengisi hasil pemeriksaan dan temuan → perangkat ditandai Perlu Servis/Rusak sesuai temuan → dibuat tugas troubleshooting terkait → staf mencatat tindakan dan hasil → koordinator memverifikasi → kondisi terbaru diperbarui berdasarkan pemeriksaan hasil, sementara riwayat lama tetap tersimpan.

Menyelesaikan tugas tidak otomatis menjadikan perangkat Baik. Perlu hasil pemeriksaan akhir. Untuk inventaris kelompok, temuan wajib mencantumkan jumlah unit terdampak.

### 9.3 Persiapan pelatihan / event

Koordinator mencatat kegiatan dan tanggal → memilih perangkat serta aplikasi → staf menjalankan checklist PC, periferal, jaringan, dan software → kesiapan ditampilkan sebagai Siap atau Belum Siap berdasarkan checklist wajib → setelah kegiatan selesai, buat tindak lanjut penghapusan aplikasi tambahan bila diperlukan.

Contoh berdasarkan sheet: pelatihan MikroTik membutuhkan Winbox. Staf mencatat PC tujuan, instalasi, uji aplikasi, dan hasil pemeriksaan jaringan. Ketentuan aplikasi yang boleh dihapus harus ditentukan koordinator; aplikasi wajib tidak boleh ikut terhapus.

### 9.4 Perpindahan perangkat

Tugas memindahkan komputer resepsionis ke ruang staf dikaitkan ke PC yang dipilih → catat lokasi asal/tujuan dan tanggal → staf menyelesaikan tindakan → setelah verifikasi, lokasi aset diperbarui dan mutasi tercatat. Identitas PC tetap sama.

## 10. Data dan aturan bisnis

| Entitas | Data utama |
| --- | --- |
| PC | ID internal, kode/nama unik, lokasi, IP, prosesor, RAM GB, OS/versi, status, kondisi monitor/keyboard/mouse, tanggal cek dan update OS, catatan, PIC. |
| Storage PC | PC, tipe HDD/SSD, slot, kapasitas GB, ruang kosong GB opsional; lebih dari satu storage per PC. |
| Perangkat lain | Kode unik, kategori, nama, merek/model, lokasi, mode individual/kelompok, jumlah total, jumlah per kondisi, PIC, catatan. |
| Relasi perangkat | PC dan perangkat pendukung terkait; menentukan unit/kelompok yang sama agar tidak dihitung ganda. |
| Aplikasi & instalasi | Nama/kategori/versi/ukuran; PC tujuan, keperluan/kegiatan, status, tanggal install, target hapus, tanggal hapus, PIC. |
| Tugas | Nomor, judul, deskripsi, instruksi, kategori, estimasi menit, prioritas, PIC, status, tanggal ambil/tenggat/selesai, hasil, verifikator. |
| Pemeriksaan | Perangkat, pelaksana, waktu, checklist, kondisi sebelum/sesudah, masalah, jumlah terdampak, bukti, tugas terkait. |
| Kegiatan | Nama, tanggal, lokasi, kebutuhan perangkat/software, checklist, PIC, tugas terkait. |
| Audit | Pengguna, waktu, tindakan, entitas, perubahan nilai; lampiran dan komentar terikat ke tugas/pemeriksaan. |

Aturan inventaris: kode wajib unik; IP berformat valid dan tidak boleh konflik dengan PC aktif pada jaringan yang sama; jumlah unit bilangan bulat positif; total unit per kondisi harus sama dengan total kelompok. Arsip digunakan untuk data yang sudah mempunyai riwayat. CPU/monitor yang juga tercatat sebagai pendukung PC harus direkonsiliasi; total PC dan total komponen tidak dijumlahkan sebagai total aset unik tanpa aturan identitas.

Status PC: Baik, Perlu Servis, Rusak, Tidak Aktif. Kondisi fisik dan status operasional dipisahkan karena perangkat dapat terlihat baik tetapi tidak berfungsi. Status software mengikuti sheet: Terinstall, Perlu Install, Perlu Dihapus, Sudah Dihapus. Status Kondisional pada prioritas sumber memerlukan peninjauan; usulan: Kondisional menjadi tipe pemicu/event, sementara prioritas tetap Rendah/Sedang/Tinggi.

Estimasi kerja disimpan dalam menit dengan tampilan jam/hari. Nilai sumber seperti 1–2 jam disimpan sebagai rentang; panjang hari kerja perlu ditentukan sebelum konversi. Tenggat berbeda dari estimasi. Keterlambatan dihitung untuk tugas bertenggat yang belum Selesai/Dibatalkan, dengan zona waktu Asia/Jakarta.

## 11. Dashboard dan laporan

Semua ringkasan memiliki filter lokasi/periode/PIC dan tautan ke daftar penyusunnya. Dashboard menampilkan waktu data terakhir diperbarui. Status kosong ditampilkan sebagai Perlu Tinjauan dan tidak disembunyikan dari total.

| Indikator | Definisi |
| --- | --- |
| Total PC | Jumlah PC aktif dalam pencatatan (tidak diarsip); Tidak Aktif tetap termasuk tetapi ditampilkan terpisah. |
| PC Baik / bermasalah | Baik; bermasalah = Perlu Servis + Rusak. Label Tidak Aktif ditampilkan sendiri. |
| Perangkat lain | Jumlah baris/kelompok serta jumlah unit ditampilkan terpisah; bukan angka yang sama. |
| Perlu perhatian | Perangkat bermasalah, tugas tindak lanjut terbuka, pemeriksaan lewat jadwal, software jatuh tempo hapus; alasan ditampilkan. |
| Tugas | Total per status; terlambat; menunggu verifikasi; beban PIC; selesai per periode berdasarkan tanggal selesai. |
| Kesiapan kegiatan | Checklist wajib yang sudah lolos dibandingkan total; masalah yang menghambat terlihat. |

## 12. Impor dan migrasi awal

1. Unggah dua spreadsheet dan pilih sheet sumber. 2. Petakan kolom. 3. Abaikan judul, petunjuk, nomor kosong, dan formula dashboard. 4. Tampilkan pratinjau valid/bermasalah serta nilai sumber. 5. Koordinator menyelesaikan identitas, status, lokasi, duplikat, dan tanggal yang tidak konsisten. 6. Impor hanya baris yang disetujui. 7. Tampilkan jumlah berhasil/gagal dan unduh catatan error.

Perangkat tanpa ID tetap menjadi kandidat yang perlu ditinjau; aplikasi tanpa nama tidak diimpor. Catatan di luar kolom utama daftar tugas perlu dipetakan sebagai catatan tambahan agar revisi pekerjaan tidak hilang. Kredensial dikeluarkan dari payload impor. Impor ulang memakai kode aset/ID tugas yang terpetakan dan tidak boleh menggandakan catatan; pembaruan data lama memerlukan pilihan eksplisit.

## 13. Kebutuhan nonfungsional — target usulan

Website berbahasa Indonesia dan responsif untuk komputer maupun ponsel. Pencarian/filter halaman utama ditargetkan ≤2 detik pada data uji 1.000 aset dan 10.000 tugas, di lingkungan deployment yang disepakati. Formulir menampilkan kesalahan pada kolom terkait; perubahan bersamaan tidak boleh saling menimpa tanpa peringatan.

Autentikasi dan pemeriksaan hak akses diterapkan di server. Koneksi menggunakan HTTPS; kata sandi disimpan sebagai hash. File bukti dibatasi ke JPG/PNG/PDF, maksimal 10 MB per file sebagai usulan awal, serta hanya dapat diakses pengguna berwenang. Backup harian dan uji pemulihan sebelum digunakan; target kehilangan data maksimal 24 jam, waktu pemulihan perlu disepakati.

Sistem tidak melakukan rename PC, setting IP, update OS, atau uninstall dari jarak jauh. Dalam MVP, tindakan dilakukan staf dan hasilnya dicatat. Backend wajib menggunakan PHP. Struktur database dan perubahannya wajib dikelola melalui database migration. Framework PHP, versi runtime, hosting, jumlah pengguna, dan integrasi ditentukan sebelum implementasi berdasarkan lingkungan server.

## 14. Kriteria penerimaan untuk pengujian

| ID | Skenario | Hasil yang harus terjadi |
| --- | --- | --- |
| AC-01 | Tambah PC Baik, lalu ubah menjadi Perlu Servis | Dashboard dan daftar perhatian berubah sesuai data; riwayat menyimpan pelaksana dan waktu. |
| AC-02 | Dua staf mengambil tugas tersedia bersamaan | Satu PIC berhasil; staf kedua menerima informasi tugas sudah diambil. |
| AC-03 | Staf mengajukan hasil tanpa bukti/hasil wajib | Pengajuan ditolak dengan pesan jelas; setelah lengkap masuk Menunggu Verifikasi. |
| AC-04 | Koordinator menolak hasil troubleshooting | Tugas kembali Dikerjakan dengan catatan revisi; perangkat belum dianggap Baik. |
| AC-05 | Install Winbox di beberapa PC | Setiap PC punya status instalasi sendiri; kegagalan satu PC tidak dianggap seluruhnya berhasil. |
| AC-06 | Target hapus aplikasi terlewati | Masuk daftar perhatian; status Sudah Dihapus hanya setelah tindakan dicatat. |
| AC-07 | Satu kursi bermasalah dari kelompok 16 unit | Jumlah kondisi tercatat 15 baik dan 1 perlu servis; dashboard tidak menyebut 16 unit rusak. |
| AC-08 | Impor baris tanpa ID, tanpa status, atau tanggal terbalik | Baris ditandai untuk tinjauan; tidak dikoreksi atau digabung otomatis. |
| AC-09 | PKL mencoba mengubah akun atau master langsung | Akses ditolak; PKL tetap dapat mengajukan hasil kerja sesuai haknya. |
| AC-10 | Ekspor laporan berdasarkan lokasi/periode | Hasil mengikuti filter dan definisi ringkasan, tanpa kredensial. |
| AC-11 | Pindahkan PC melalui tugas | Lokasi terbaru berubah setelah verifikasi; riwayat asal/tujuan tetap tersedia. |
| AC-12 | Pulihkan backup uji | Data inventaris, tugas, hubungan dan bukti kembali lengkap sesuai titik backup. |

## 15. Ukuran keberhasilan setelah digunakan

Target usulan: semua aset yang disepakati mempunyai identitas/lokasi/status; setiap tugas aktif mempunyai PIC atau berada di antrean tersedia; semua perubahan kondisi dapat ditelusuri; dashboard sesuai data sumber; tidak ada duplikasi akibat impor ulang. Persentase tugas selesai tepat waktu diukur sebagai baseline terlebih dahulu sebelum menentukan target staf.

## 16. Keputusan yang perlu ditinjau sebelum pengembangan

| Pertanyaan | Rekomendasi awal |
| --- | --- |
| Siapa yang menggunakan sistem? | Koordinator, staf IT, PKL/magang dengan akun individu; viewer opsional. |
| Apakah hasil tugas harus diverifikasi? | Ya untuk MVP, terutama perubahan aset dan pekerjaan PKL; dapat disederhanakan setelah tinjauan. |
| Perangkat dicatat per unit atau kelompok? | PC per unit; perangkat lain mendukung kelompok, dengan jumlah per kondisi; perangkat penting bisa dipecah per unit. |
| Apakah kegiatan/event masuk versi pertama? | Ya, sebagai konteks tugas dan checklist sederhana, tanpa sinkronisasi jadwal GTTC. |
| Bagaimana pemeriksaan rutin dijadwalkan? | Frekuensi dapat diatur koordinator; tidak menetapkan harian/mingguan sebelum disepakati. |
| Apakah perlu proyek dengan banyak tugas dan dependensi? | MVP mendukung checklist; modul proyek lengkap tahap berikutnya. Ketergantungan rename → setting IP dicatat sebagai instruksi awal. |
| Apakah Google Sheets harus tetap sinkron? | Impor awal dan ekspor saja pada MVP; sinkronisasi dua arah membutuhkan keputusan terpisah. |
| Apa makna storage, baris perangkat tanpa ID, dan konflik CPU/PC? | Konfirmasi data dahulu; tidak membuat asumsi jumlah unit atau kapasitas. |
| Berapa lab, pengguna, dan cakupan di luar lab? | Master lokasi mendukung Lab 1, Lab 2, ruang staf, tamu dan lokasi lain; daftar final perlu ditetapkan. |

Urutan implementasi usulan: sepakati kebutuhan dan rekonsiliasi data → rancangan halaman serta alur → login/master/inventaris → software dan tugas terhubung → pemeriksaan/event/laporan → impor percobaan dan uji pengguna → penggunaan operasional. Dokumen ini belum merupakan persetujuan membangun atau menerbitkan website.

## 17. Persyaratan teknologi — PHP dan database migration

### 19.1 Keputusan dan batasan

| Komponen | Ketentuan |
| --- | --- |
| Backend | Wajib menggunakan PHP. Validasi, autentikasi, hak akses, aturan bisnis, dan transaksi dijalankan di server. |
| Framework | Belum ditentukan. Framework yang dipilih harus menyediakan atau mendukung migration, validasi, autentikasi, transaksi, dan pengujian. |
| Database | Relasional dengan dukungan foreign key dan transaksi. Usulan awal: MySQL/MariaDB dengan tabel InnoDB; produk dan versinya dikonfirmasi sesuai server. |
| Database migration | Wajib untuk pembuatan tabel, perubahan kolom, indeks, foreign key, dan constraint. File migration disimpan bersama kode dalam version control. |
| Frontend | Halaman web responsif berbahasa Indonesia. Pendekatan template PHP atau frontend terpisah ditentukan saat desain teknis. |
| Konfigurasi | Kredensial database dan konfigurasi lingkungan dipisahkan dari kode sumber; tidak disimpan di repository atau catatan tugas. |
| Spreadsheet | Menjadi referensi dan sumber impor awal. Database aplikasi menjadi sumber data operasional setelah migrasi disetujui. |

Dokumen ini menetapkan PHP dan migration sebagai kebutuhan pengguna. Pilihan framework, engine database, dan antarmuka merupakan keputusan implementasi yang belum final. Nomor versi PHP/framework/database tidak ditetapkan sebelum kompatibilitas server dan dependensi diperiksa.

### 19.2 Susunan aplikasi

- Lapisan halaman/controller menerima permintaan dan menampilkan hasil.
- Lapisan service menjalankan aturan tugas, verifikasi, pemeriksaan, instalasi, dan mutasi perangkat.
- Model/repository mengelola akses database; query menggunakan parameter binding atau ORM.
- Policy/middleware membatasi akses sesuai peran dan kepemilikan tugas.
- Migration mengelola skema; seeder mengisi master awal; importer memindahkan data spreadsheet yang telah disetujui.
- Penyimpanan lampiran bersifat privat; akses melalui pemeriksaan izin aplikasi.

Pembaruan lintas tabel dijalankan dalam satu transaksi. Contoh: verifikasi perpindahan PC harus menyimpan perubahan lokasi, riwayat mutasi, hasil tugas, dan audit sebagai satu operasi. Jika gagal, seluruh operasi dibatalkan.

## 18. Rancangan database awal

Rancangan berikut merupakan dasar migration, bukan skema final. Nama tabel dapat disesuaikan selama relasi dan aturan bisnis tetap terpenuhi.

| Tabel | Tujuan dan relasi utama |
| --- | --- |
| `users`, `roles`, `user_roles` | Akun individu dan peran; pengguna yang sudah memiliki riwayat dinonaktifkan, bukan dihapus permanen. |
| `locations`, `asset_categories` | Master ruangan dan kategori perangkat. |
| `assets` | Identitas aset, kode unik, jenis PC/pendukung, kategori, lokasi, mode individual/kelompok, jumlah total, status, PIC, dan waktu arsip. |
| `pc_details` | Relasi satu-ke-satu dengan aset PC; nama PC unik, IP, prosesor, RAM, OS, versi Windows, kondisi periferal, tanggal update OS. |
| `pc_storages` | Satu PC dapat memiliki beberapa HDD/SSD, kapasitas, dan ruang kosong. |
| `asset_condition_counts` | Jumlah unit per kondisi untuk kelompok aset; total wajib sama dengan jumlah kelompok. |
| `asset_links` | Hubungan PC dengan komponen pendukung yang telah dipastikan identitasnya; mencegah perhitungan ganda. |
| `asset_movements` | Lokasi asal/tujuan, waktu, pelaksana, dan tugas terkait; riwayat tidak ditimpa. |
| `software`, `software_versions` | Master aplikasi, kategori, dan versi; instalasi mengacu pada versi yang tepat. |
| `software_installations` | PC, versi aplikasi, status, keperluan, tanggal install, target/tanggal hapus, PIC, dan kegiatan terkait. |
| `tasks` | Judul, instruksi, kategori, prioritas, estimasi min/max dalam menit, PIC, status, tenggat, tanggal ambil/selesai, verifikator. |
| `task_assets` | Hubungan banyak-ke-banyak antara tugas dan perangkat yang ditangani. |
| `task_checklist_items`, `task_comments`, `task_status_history` | Checklist, komentar/revisi, dan riwayat perubahan status tugas. |
| `inspections`, `inspection_items` | Pemeriksaan aset, hasil checklist, temuan, kondisi, jumlah terdampak, pemeriksa, dan tugas tindak lanjut. |
| `events`, `event_assets`, `event_software_requirements` | Kegiatan, kebutuhan perangkat, serta aplikasi/versi yang diperlukan. |
| `attachments` | Metadata bukti, lokasi file privat, uploader, dan hubungan eksplisit ke tugas/pemeriksaan. |
| `audit_logs` | Perubahan inventaris dan tindakan penting beserta pengguna serta waktu. |
| `import_batches`, `import_rows` | Batch impor, asal sheet/baris, nilai terpilih yang aman, hasil validasi, pemetaan, dan error. |
| `inspection_schedules` | Jadwal pemeriksaan berulang dan tenggat berikutnya. |
| `notifications` | Pengingat dalam aplikasi dengan penerima dan status dibaca. |

### 18.1 Constraint dan konsistensi

- `assets.code` dan `pc_details.name` unik; data lama yang bentrok masuk daftar tinjauan impor.
- Setiap `pc_details` hanya dimiliki satu aset berjenis PC.
- Foreign key melindungi relasi. Penghapusan pengguna/aset yang memiliki riwayat menggunakan nonaktif/arsip; tidak menghapus riwayat lewat cascade.
- Indeks mendukung filter lokasi/status aset dan filter PIC/status/tenggat tugas.
- IP divalidasi sesuai lingkup jaringan; apabila ada beberapa jaringan, tambahkan identitas jaringan sebelum menerapkan constraint unik gabungan.
- Tanggal disimpan secara konsisten dan ditampilkan dalam Asia/Jakarta. Tanggal tanpa jam dari spreadsheet tetap diperlakukan sebagai tanggal.
- Kolom status memakai nilai terkontrol; perubahan daftar nilai harus konsisten dengan validasi dan migration.
- Estimasi numerik, jumlah unit, kapasitas storage, serta urutan tanggal divalidasi di server. Constraint database ditambahkan bila engine mendukung aturan tersebut.
- Pengambilan tugas memakai transaksi dan pembaruan bersyarat/row lock. Tugas hanya dapat diambil ketika masih Tersedia dan belum mempunyai PIC.
- Verifikasi hasil menyimpan status tugas dan perubahan aset secara atomik; pemeriksaan akhir tetap wajib untuk menetapkan perangkat Baik.
- Lampiran tidak disimpan sebagai data biner di tabel utama; metadata disimpan di database, file pada penyimpanan privat.

## 19. Persyaratan database migration

### 19.1 Urutan migration awal

1. Buat akun, peran, dan tabel relasi akses.
2. Buat master lokasi, kategori, dan nilai status jika menggunakan tabel referensi.
3. Buat aset, detail PC, storage, jumlah kondisi, dan hubungan komponen.
4. Buat software dan versi.
5. Buat kegiatan serta kebutuhan perangkat/software.
6. Buat tugas, hubungan aset, checklist, komentar, dan riwayat status.
7. Buat instalasi software, pemeriksaan, dan mutasi dengan foreign key ke tabel yang telah tersedia.
8. Buat metadata lampiran, audit, jadwal pemeriksaan, notifikasi, dan pelacakan impor.
9. Tambahkan indeks serta constraint yang dibutuhkan dan jalankan seed master.

Relasi silang dapat ditambahkan melalui migration lanjutan setelah kedua tabel tersedia. Migration harus dapat dijalankan pada database kosong tanpa membuat tabel secara manual.

### 19.2 Aturan perubahan skema

- Migration yang sudah diterapkan pada lingkungan bersama tidak diedit; buat migration baru untuk perubahan berikutnya.
- Perubahan skema tidak dilakukan manual di database produksi. Perubahan darurat harus direkonsiliasi menjadi migration dan diuji sebelum deploy berikutnya.
- Migration skema dipisahkan dari impor spreadsheet. Menjalankan migration tidak otomatis memasukkan seluruh data referensi.
- Seeder master dapat dijalankan ulang tanpa membuat duplikat. Akun admin dibuat melalui prosedur aman, tanpa password default yang tertanam dalam kode.
- Migration memiliki langkah balik jika aman. Untuk penghapusan kolom/data yang tidak dapat dipulihkan, dokumentasikan dampak dan prosedur restore backup; rollback bukan pengganti backup.
- Migration yang memerlukan backfill data memakai tahapan tambah kolom → isi data → validasi → terapkan constraint, agar data lama tidak langsung gagal.
- Impor produksi dijalankan sebagai tindakan tersendiri setelah pratinjau dan persetujuan batch oleh koordinator.

### 19.3 Alur deployment

Backup database → uji migration pada staging dengan salinan data yang aman → periksa constraint dan hasil backfill → jalankan migration produksi dalam jendela yang disepakati → deploy kode kompatibel → periksa fungsi utama dan jumlah data.

Bila gagal, gunakan prosedur pemulihan yang telah diuji. Kegagalan migration harus dilaporkan dengan langkah yang gagal; aplikasi tidak boleh melanjutkan operasi dengan skema yang tidak kompatibel. Struktur deployment dan perintah migration mengikuti framework yang akhirnya dipilih.

## 20. Kriteria penerimaan tambahan untuk PHP dan migration

| ID | Skenario | Hasil yang harus terjadi |
| --- | --- | --- |
| AC-13 | Instalasi aplikasi pada database kosong | Seluruh tabel, indeks, dan foreign key dibuat melalui migration; master awal tersedia melalui seeder. |
| AC-14 | Migration dijalankan kembali tanpa perubahan | Tidak menggandakan tabel/data dan tidak mengulang migration yang sudah tercatat. |
| AC-15 | Upgrade skema pada database dengan data inventaris/tugas | Data serta riwayat tetap utuh; perubahan skema tercatat dan aplikasi tetap berfungsi. |
| AC-16 | Foreign key diisi dengan ID aset/pengguna yang tidak ada | Operasi ditolak; tidak terbentuk data yatim. |
| AC-17 | Dua pengguna memverifikasi/mengambil tugas bersamaan | Hanya transisi yang sah berhasil; tidak ada PIC ganda atau perubahan aset yang saling menimpa. |
| AC-18 | Satu operasi gagal di tengah pembaruan tugas dan lokasi aset | Transaksi dibatalkan; status dan lokasi tetap konsisten. |
| AC-19 | Pemulihan dari migration gagal diuji di staging | Prosedur rollback aman atau restore backup berhasil dan terdokumentasi. |
| AC-20 | Input PHP dikirim langsung melewati halaman frontend | Validasi dan hak akses server tetap berlaku; query tidak menerima input sebagai SQL mentah. |
| AC-21 | Membuka login, sidebar, dan tampilan ponsel | Nama SIMIT dan logo terpilih konsisten; PNG benar-benar transparan, tidak terdistorsi; navigasi dan status terbaca jelas. |

## 21. Hasil kerja yang diharapkan saat implementasi

- Kode aplikasi SIMIT berbasis PHP beserta dokumentasi instalasi dan konfigurasi lingkungan.
- Aset logo terpilih, favicon yang ditinjau, dan panduan penerapan identitas visual.
- File migration lengkap, seeder master, dan contoh konfigurasi tanpa rahasia.
- Importer untuk kedua spreadsheet dengan pratinjau, validasi, dan laporan error.
- Dokumentasi relasi database, hak akses, alur tugas, backup, dan pemulihan.
- Hasil uji kriteria penerimaan serta daftar keputusan teknis final.

PRD SIMIT v0.3 mencakup nama dan logo yang dipilih pengguna, arahan antarmuka, kebutuhan inventaris dan alur kerja IT, PHP, serta database migration. Dokumen ini merupakan spesifikasi; belum mencakup pembuatan kode aplikasi atau migration yang dapat dijalankan.


Email: admin@simit.local
Password: Simit!2026Demo