# SIMIT Design System

Dokumen ini menjadi acuan visual dan interaksi untuk SIMIT (Sistem Inventaris dan Manajemen IT). Tujuannya agar pengembangan berikutnya tetap konsisten meskipun dikerjakan oleh orang yang berbeda.

Terakhir diperbarui: 2 November 2026.

## Public demo

Tombol demo hanya tersedia di bawah tombol **Masuk** pada halaman login. Demo menggunakan UI operasional yang sama dengan role User: sidebar, topbar, kartu dashboard, tabel, chat, laporan, dan profil. Seluruh isinya berupa fixture statis di source code, tidak memakai akun maupun koneksi database, serta tidak menampilkan data profil asli.

Label **Demo · Hanya lihat** harus selalu terlihat. Demo tidak menyediakan form simpan, edit, hapus, upload, pengiriman chat, notifikasi aktif, atau ekspor laporan. Menu hanya berfungsi untuk berpindah antarhalaman pratinjau.

## Arah visual

SIMIT menggunakan dua bahasa visual:

- **Neumorphism operasional** untuk dashboard internal setelah login. Permukaan lembut, shadow ringan, radius besar, dan kontrol yang terasa tactile.
- **Glassmorphism editorial** untuk landing page publik. Panel transparan, blur, gradasi hijau, mockup dashboard 3D berbasis CSS, dan elemen dekoratif yang ringan.

Identitas tetap berpusat pada warna hijau GTTC/SIMIT, keterbacaan tinggi, dan tampilan profesional yang tidak terlihat seperti template generik.

## Warna

### Light mode

```text
Background       #E9EFEB
Surface          #EDF3EF
Ink              #173329
Muted text       #708078
Primary green    #25833B
Dark green       #12552A
Border           rgba(27, 74, 48, .10)
Success surface  #D9EFDE
Warning surface  #F8E8C7
Danger surface   #F5DDDD
```

### Dark mode

Dark mode menggunakan permukaan hijau gelap dengan teks terang. Ikon tema berbentuk bulan pada light mode dan matahari pada dark mode. Preferensi disimpan pada `localStorage` dengan key `simit-theme`.

### Landing page

```text
Forest          #073F27
Landing green   #168143
Lime accent     #5EE57A
Glass surface   rgba(255, 255, 255, .58)
```

Jangan mengganti hijau utama dengan warna biru/ungu tanpa alasan produk yang jelas. Warna status boleh berbeda, tetapi harus tetap lembut dan memiliki kontras yang cukup.

## Tipografi

- Font stack: `Inter`, `ui-sans-serif`, `system-ui`, `Segoe UI`, sans-serif.
- Heading memakai bobot tebal dan tracking sedikit rapat.
- Eyebrow memakai uppercase, letter-spacing, dan warna primary green.
- Isi memakai ukuran 14–16px dengan line-height longgar.
- Teks muted digunakan untuk deskripsi, metadata, dan timestamp; jangan digunakan untuk label penting atau nilai status.

## Bentuk dan elevation

- Radius utama: 20px (`--radius`).
- Kontrol kecil: 10–14px.
- Kartu besar: 20–28px.
- Shadow light mode memakai pasangan shadow terang dan gelap untuk efek neumorphism.
- Hindari shadow hitam pekat; gunakan shadow hijau/abu dengan opacity rendah.
- Border putih transparan digunakan pada panel agar permukaan tetap terbaca.

## Komponen utama

### Layout aplikasi

- Sidebar desktop berada di kiri dengan lebar sekitar 220–260px.
- Topbar berisi judul halaman, pengalih bahasa, tema, tanggal, dan notifikasi.
- Konten utama menggunakan `.page` dengan lebar maksimum dan padding responsif.
- Pada lebar kecil, sidebar berubah menjadi navigasi geser dan tombol menu ditampilkan.

### Tombol

- `.btn.primary`: aksi utama, gradasi hijau, teks putih.
- `.btn.ghost`: aksi sekunder, permukaan neumorphic.
- Tombol destructive harus memakai warna danger dan konfirmasi sebelum penghapusan.
- Label tombol harus menjelaskan aksi, bukan hanya ikon.

### Status badge

Status menggunakan badge lembut, bukan warna solid yang menyilaukan. Status database dapat diterjemahkan melalui helper `__()` tanpa mengubah nilai asli di database.

### Tabel

- Desktop boleh menampilkan informasi teknis lebih banyak.
- Mobile menampilkan tabel ringkas atau memungkinkan horizontal scroll jika data memang perlu dipertahankan.
- Header kolom menggunakan uppercase kecil dan warna muted.
- Baris yang dapat diklik harus memiliki hover state, keyboard focus, dan target interaksi yang jelas.
- Tombol aksi tidak boleh memicu navigasi baris secara tidak sengaja.

### Form

- Gunakan grid dua kolom pada desktop dan satu kolom pada mobile.
- Label selalu berada di atas input.
- Input memakai surface yang sama dengan panel dan inset shadow.
- Field wajib diberi tanda `*`.
- Pesan validasi harus menjelaskan cara memperbaiki input.

### Tugas kolaboratif

- Kolom detail dan form kontribusi memiliki ukuran seimbang.
- Daftar kontributor menampilkan avatar/inisial, nama, waktu, isi kontribusi, dan status final.
- Tugas tanpa PIC tidak boleh terlihat seperti tugas yang terkunci ke satu User.
- Checkbox pengajuan final harus jelas bahwa tindakan tersebut mengirim tugas ke ACC Admin.

### Laporan

- Judul panel dan header kolom rata tengah.
- Area tanda tangan hanya satu garis kosong di sisi kanan.
- Watermark memakai logo hijau SIMIT, fixed, transparansi rendah, dan berada di atas lapisan konten saat print agar muncul di setiap halaman.
- Tabel dan area tanda tangan harus menggunakan `break-inside: avoid` jika tidak boleh terpotong.

## Logo dan ikon

- Logo hijau: `public/assets/img/logo-simit.png` untuk permukaan terang.
- Logo putih: `public/assets/img/logo-simit-putih.png` untuk panel hijau dan dark mode.
- Favicon: `public/assets/img/favicon.svg`.
- Ikon navigasi menggunakan SVG inline melalui helper `nav_icon()`/`icon()`.
- Setiap ikon interaktif wajib memiliki `aria-label` atau teks pendamping.
- Gunakan ikon modern yang sederhana dengan stroke konsisten; jangan menambahkan emoji sebagai ikon UI utama.

## Landing page publik

Landing page berada pada `/` untuk pengunjung yang belum login. Isinya bersifat presentasi dan tidak membaca data operasional.

Bagian yang tersedia:

1. Hero dengan logo, value proposition, dan CTA login.
2. Mockup dashboard 3D berbasis CSS.
3. Kartu fitur inventaris, software, tugas, laporan, chat, dan audit.
4. Alur kerja dari pencatatan hingga ACC.
5. Penjelasan role Admin dan User.
6. CTA penutup dan footer.

Efek pointer 3D harus dinonaktifkan untuk layar kecil dan `prefers-reduced-motion`.

## Responsive rules

- Desktop: tampilkan informasi teknis lengkap dan dua kolom form.
- Tablet: ubah grid menjadi dua kolom atau satu kolom sesuai ruang.
- Mobile: satu kolom, tombol full width bila perlu, sidebar menjadi drawer, tabel ringkas/horizontal scroll.
- Jangan membuat teks atau tombol keluar dari kartu.
- Gunakan `min-width: 0`, `overflow-wrap: anywhere`, dan `minmax(0, 1fr)` pada layout yang menampilkan teks dinamis.

## Bahasa dan aksesibilitas

- Bahasa Indonesia adalah default.
- Semua teks UI baru harus memakai `__()` dengan pasangan Inggris.
- Teks domain yang sering dipakai boleh ditambahkan ke katalog helper `__()` di `bootstrap.php`.
- Jangan menerjemahkan nilai database secara permanen; terjemahkan hanya pada saat render.
- Pastikan focus state terlihat.
- Jangan hanya mengandalkan warna untuk menyampaikan status.
- Pastikan area klik minimal nyaman untuk mouse dan touch.

## Aturan perubahan desain

Sebelum menambah komponen baru:

1. Periksa apakah komponen yang sama sudah ada di `app.css`, `theme.css`, atau stylesheet fitur.
2. Gunakan token warna, radius, shadow, dan spacing yang sudah ada.
3. Uji light mode, dark mode, English, Indonesian, desktop, dan mobile.
4. Uji teks panjang, status kosong, dan error validasi.
5. Perbarui dokumen ini serta `CHANGELOG.md` jika aturan visual atau struktur halaman berubah.

## File styling utama

```text
public/assets/css/app.css                 # token dan komponen aplikasi
public/assets/css/theme.css               # dark mode, profil, dan tema tambahan
public/assets/css/workflow.css            # workflow dan laporan dasar
public/assets/css/report-responsive.css   # responsivitas laporan
public/assets/css/report-print.css        # cetak, tanda tangan, watermark
public/assets/css/communication.css       # chat dan notifikasi
public/assets/css/collaboration.css       # tugas kolaboratif
public/assets/css/landing.css             # landing glassmorphism/3D
public/assets/css/demo.css                # tombol demo login + shell User read-only
src/Views/layouts/demo.php                # layout demo terisolasi
src/Views/demo/app.php                    # halaman modul demo
```
