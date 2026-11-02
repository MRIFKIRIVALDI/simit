<?php $current='demo/'.($module==='dashboard'?'':$module); ?>
<!doctype html>
<html lang="<?=e(lang())?>">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <script>if(localStorage.getItem('simit-theme')==='dark'||(!localStorage.getItem('simit-theme')&&matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.dataset.theme='dark'</script>
  <title><?=e($title)?> · Demo SIMIT</title>
  <meta name="robots" content="noindex,nofollow"><meta name="description" content="Demo read-only SIMIT">
  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
  <link rel="stylesheet" href="/assets/css/app.css"><link rel="stylesheet" href="/assets/css/theme.css"><link rel="stylesheet" href="/assets/css/demo.css">
</head>
<body class="app-shell demo-user-shell">
<aside class="sidebar" id="sidebar">
  <a class="brand" href="/demo"><img src="/assets/img/logo-simit.png" alt="Logo SIMIT"><span><strong>SIMIT</strong><small>GTTC · IT Division</small></span></a>
  <div class="demo-readonly-label"><span></span><?=__('Demo · Hanya lihat','Demo · Read only')?></div>
  <nav>
  <?php $nav=['dashboard'=>['',__('Dashboard','Dashboard')],'pc'=>['pc',__('Inventaris PC','PC Inventory')],'perangkat'=>['perangkat',__('Perangkat Lain','Other Devices')],'software'=>['software','Software'],'tugas'=>['tugas',__('Tugas IT','IT Tasks')],'chat'=>['chat','Chat'],'laporan'=>['laporan',__('Laporan','Reports')],'profile'=>['profil',__('Profil Saya','My Profile')]];foreach($nav as $icon=>$item):$href=$item[0];?>
    <a href="/demo<?=$href?'/'.$href:''?>" class="<?=($module===($href?:'dashboard'))?'active':''?>"><span class="nav-icon"><?=nav_icon($icon)?></span><?=e($item[1])?></a>
  <?php endforeach;?>
  </nav>
  <div class="sidebar-foot"><div class="avatar">D</div><div><strong>Demo User</strong><small><?=__('Pengunjung · Read only','Visitor · Read only')?></small></div><a class="demo-logout" href="/login" aria-label="<?=__('Keluar demo','Exit demo')?>">→</a></div>
</aside>
<main>
  <header class="topbar"><button class="icon-btn menu-btn" id="menuButton" aria-label="Menu"><?=nav_icon('dashboard')?></button><div><p class="eyebrow"><?=__('Mode pratinjau aman','Safe preview mode')?></p><h1><?=e($title)?></h1></div><div class="top-actions"><button class="icon-btn" id="themeButton" aria-label="<?=__('Ubah tema','Toggle theme')?>">◐</button><span class="date"><?=date('d M Y')?></span><span class="icon-btn demo-disabled-control" title="<?=__('Notifikasi dinonaktifkan di demo','Notifications are disabled in demo')?>"><?=nav_icon('audit')?></span></div></header>
  <section class="page"><div class="demo-readonly-banner"><strong><?=__('Mode Demo — hanya lihat','Demo Mode — read only')?></strong><span><?=__('Data contoh ini tidak terhubung ke database utama. Semua aksi simpan, edit, upload, chat, dan notifikasi dinonaktifkan.','This sample data is not connected to the production database. Save, edit, upload, chat, and notification actions are disabled.')?></span></div><?=$content?></section>
</main>
<script src="/assets/js/app.js"></script><script src="/assets/js/theme-icon.js"></script>
</body></html>
