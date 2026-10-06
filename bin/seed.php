<?php
require dirname(__DIR__) . '/bootstrap.php';
use Simit\Core\Database;
$pdo=Database::connection();
$pdo->beginTransaction();
try {
    foreach (['Lab Komputer','Lab 1','Lab 2','Ruang Staff','Resepsionis','Ruang GTTC Kampus','Ruang Tamu'] as $name) $pdo->prepare('INSERT OR IGNORE INTO locations(name) VALUES(?)')->execute([$name]);
    foreach (['Komputer','Jaringan','Perlengkapan','Multimedia','Kelistrikan'] as $name) $pdo->prepare('INSERT OR IGNORE INTO asset_categories(name) VALUES(?)')->execute([$name]);
    $adminPass = getenv('SIMIT_ADMIN_PASSWORD') ?: 'Simit!2026Demo';
    $pdo->prepare('INSERT OR IGNORE INTO users(name,email,password_hash,role) VALUES(?,?,?,?)')->execute(['Koordinator IT','admin@simit.local',password_hash($adminPass,PASSWORD_DEFAULT),'admin']);
    $admin=(int)$pdo->query("SELECT id FROM users WHERE email='admin@simit.local'")->fetchColumn();
    $loc=(int)$pdo->query("SELECT id FROM locations WHERE name='Lab Komputer'")->fetchColumn();
    $cat=(int)$pdo->query("SELECT id FROM asset_categories WHERE name='Komputer'")->fetchColumn();
    for($i=1;$i<=24;$i++){
        $code='PC-'.str_pad((string)$i,3,'0',STR_PAD_LEFT); $pc='LAB-PC-'.str_pad((string)$i,2,'0',STR_PAD_LEFT);
        $pdo->prepare('INSERT OR IGNORE INTO assets(code,type,category_id,location_id,name,status,notes) VALUES(?,?,?,?,?,?,?)')->execute([$code,'PC',$cat,$loc,$pc,'Baik','Data awal spreadsheet']);
        $asset=(int)$pdo->query("SELECT id FROM assets WHERE code=".$pdo->quote($code))->fetchColumn();
        $pdo->prepare('INSERT OR IGNORE INTO pc_details(asset_id,pc_name,ip_address,processor,ram_gb,storage_type,os,os_version,last_checked_at) VALUES(?,?,?,?,?,?,?,?,?)')->execute([$asset,$pc,'192.168.1.'.(182+$i),'Intel Core i5',8,$i%4===0?'SSD':'HDD','Windows 11','25H2','2026-07-01']);
    }
    foreach ([['Google Chrome','Wajib (Umum)','126.x'],['Anaconda (Python)','Wajib (Akademik)','2024.10'],['Visual Studio Code','Wajib (Akademik)','1.90']] as $s) $pdo->prepare('INSERT OR IGNORE INTO software(name,category,version,status) VALUES(?,?,?,?)')->execute([$s[0],$s[1],$s[2],'Terinstall']);
    $tasks=[['TSK-001','Rename 24 PC Lab','Sedang','Selesai',360],['TSK-002','Setting IP Statis 24 PC','Sedang','Selesai',360],['TSK-013','Pindahkan 1 Komputer resepsionis ke Ruang Staf','Sedang','Tersedia',60],['TSK-014','Setting BIOS ke SSD','Sedang','Tersedia',120],['TSK-018','Kerangka sistem task & project management','Rendah','Tersedia',360],['TSK-020','Cek Kondisi Komputer Lab','Sedang','Draft',120]];
    foreach($tasks as $t) $pdo->prepare('INSERT OR IGNORE INTO tasks(number,title,priority,status,estimate_minutes,created_by) VALUES(?,?,?,?,?,?)')->execute([$t[0],$t[1],$t[2],$t[3],$t[4],$admin]);
    $pdo->commit(); echo "Seeder selesai. Login: admin@simit.local / $adminPass\n";
} catch(Throwable $e){$pdo->rollBack();fwrite(STDERR,$e->getMessage()."\n");exit(1);}

