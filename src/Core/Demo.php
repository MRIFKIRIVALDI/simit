<?php
namespace Simit\Core;

final class Demo
{
    public static function preview(): array
    {
        return [
            'pc'=>[
                ['code'=>'DEMO-PC-01','name'=>'PC Lab Desain','location'=>'Lab 1','spec'=>'Core i5 · 16 GB · SSD 512 GB','status'=>'Baik'],
                ['code'=>'DEMO-PC-02','name'=>'PC Resepsionis','location'=>'Resepsionis','spec'=>'Core i3 · 8 GB · SSD 256 GB','status'=>'Perlu Servis'],
                ['code'=>'DEMO-PC-03','name'=>'PC Staff IT','location'=>'Ruang Staff','spec'=>'Ryzen 5 · 16 GB · SSD 512 GB','status'=>'Baik'],
            ],
            'perangkat'=>[
                ['code'=>'DEV-DEMO-01','name'=>'Router Utama','category'=>'Jaringan','location'=>'Ruang Staff','quantity'=>1,'status'=>'Baik'],
                ['code'=>'DEV-DEMO-02','name'=>'Proyektor Kelas','category'=>'Multimedia','location'=>'Lab 2','quantity'=>2,'status'=>'Perlu Servis'],
                ['code'=>'DEV-DEMO-03','name'=>'UPS Server','category'=>'Kelistrikan','location'=>'Ruang Staff','quantity'=>1,'status'=>'Baik'],
            ],
            'software'=>[
                ['name'=>'Google Chrome','version'=>'126.x','purpose'=>'Browser operasional','period'=>'01 Jan–31 Des 2026','status'=>'Terinstall'],
                ['name'=>'Visual Studio Code','version'=>'1.90','purpose'=>'Pengembangan','period'=>'01 Jan–31 Des 2026','status'=>'Perlu Install'],
                ['name'=>'Aplikasi Legacy','version'=>'2.4','purpose'=>'Arsip lama','period'=>'01 Jan–30 Nov 2026','status'=>'Perlu Dihapus'],
            ],
            'tasks'=>[
                ['number'=>'TSK-DEMO-01','title'=>'Periksa PC resepsionis','pic'=>'Tugas bersama','priority'=>'Tinggi','status'=>'Tersedia'],
                ['number'=>'TSK-DEMO-02','title'=>'Instal Visual Studio Code','pic'=>'Rani · User','priority'=>'Sedang','status'=>'Dikerjakan'],
                ['number'=>'TSK-DEMO-03','title'=>'Pembaruan inventaris Lab 1','pic'=>'Dimas · User','priority'=>'Rendah','status'=>'Menunggu Verifikasi'],
                ['number'=>'TSK-DEMO-04','title'=>'Pemeriksaan router utama','pic'=>'Dimas · User','priority'=>'Sedang','status'=>'Selesai'],
            ],
            'messages'=>[
                ['from'=>'Koordinator IT','body'=>'Mohon cek perangkat di Lab 1 setelah jam operasional.','time'=>'09:15'],
                ['from'=>'Demo User','body'=>'Baik, hasil pemeriksaan akan saya lampirkan pada tugas.','time'=>'09:18'],
            ],
        ];
    }

}
