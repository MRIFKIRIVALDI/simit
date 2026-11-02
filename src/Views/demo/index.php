<?php throw new LogicException('View demo lama tidak boleh digunakan; gunakan demo/app dengan layout demo read-only.'); ?>
<?php use Simit\Core\Csrf; ?>
<main class="demo-page">
  <nav class="demo-public-nav glass-card">
    <a class="landing-brand" href="/"><span class="brand-orb"><img src="/assets/img/logo-simit-putih.png" alt="Logo SIMIT"></span><span><strong>SIMIT</strong><small>Portfolio Demo</small></span></a>
    <span class="demo-isolated"><i></i><?=__('Mode demo terisolasi','Isolated demo mode')?></span>
    <div><a href="/login" class="demo-link"><?=__('Masuk akun asli','Production sign in')?></a><a href="/" class="demo-exit"><?=__('Keluar demo','Exit demo')?></a></div>
  </nav>

  <header class="demo-welcome">
    <div><span class="landing-kicker"><i></i><?=__('PRATINJAU INTERAKTIF','INTERACTIVE PREVIEW')?></span><h1><?=__('Jelajahi cara kerja SIMIT.','Explore how SIMIT works.')?></h1><p><?=__('Data di halaman ini sepenuhnya contoh. Anda dapat mengubah status untuk mencoba alur kerja; perubahan hanya tersimpan sementara di session Anda.','Everything on this page is sample data. You can change statuses to try the workflow; changes only live temporarily in your session.')?></p></div>
    <form method="post" action="/demo/reset"><?=Csrf::field()?><button class="demo-reset" type="submit"><?=__('Atur ulang data demo','Reset demo data')?></button></form>
  </header>

  <?php if($m=flash('success')):?><div class="alert success"><?=e($m)?></div><?php endif;?>
  <section class="demo-security-note"><span>✓</span><div><strong><?=__('Aman untuk dicoba','Safe to explore')?></strong><p><?=__('Mode demo tidak memiliki akses ke database utama, akun, chat, lampiran, atau data internal organisasi.','Demo mode cannot access the production database, accounts, chats, attachments, or internal organization data.')?></p></div></section>

  <section class="demo-metrics">
    <article><i><?=nav_icon('pc')?></i><span><?=__('PC terpantau','Monitored PCs')?></span><strong><?=count($demo['pc'])?></strong><small><?=__('data contoh','sample records')?></small></article>
    <article><i><?=nav_icon('software')?></i><span>Software</span><strong><?=count($demo['software'])?></strong><small><?=__('masa penggunaan tercatat','usage periods tracked')?></small></article>
    <article><i><?=nav_icon('tugas')?></i><span><?=__('Tugas aktif','Active tasks')?></span><strong><?=count(array_filter($demo['tasks'],fn($r)=>$r['status']!=='Selesai'))?></strong><small><?=__('dengan alur ACC','with approval workflow')?></small></article>
    <article><i><?=nav_icon('audit')?></i><span><?=__('Jejak aktivitas','Activity trail')?></span><strong><?=count($demo['activity'])?></strong><small><?=__('khusus session ini','for this session only')?></small></article>
  </section>

  <?php $statusMap=['pc'=>['Baik','Perlu Servis','Rusak'],'software'=>['Terinstall','Perlu Install','Perlu Dihapus','Sudah Dihapus'],'tasks'=>['Tersedia','Dikerjakan','Menunggu Verifikasi','Selesai']]; ?>
  <section class="demo-module-grid">
    <article class="demo-module-card">
      <header><span><?=nav_icon('pc')?></span><div><h2><?=__('Inventaris PC','PC Inventory')?></h2><p><?=__('Spesifikasi, lokasi, dan kondisi perangkat.','Device specifications, locations, and condition.')?></p></div></header>
      <div class="demo-table-wrap"><table><thead><tr><th><?=__('Identitas','Identity')?></th><th><?=__('Lokasi','Location')?></th><th><?=__('Spesifikasi','Specifications')?></th><th><?=__('Status','Status')?></th></tr></thead><tbody><?php foreach($demo['pc'] as $row):?><tr><td><strong><?=e($row['name'])?></strong><small><?=e($row['code'])?></small></td><td><?=e($row['location'])?></td><td><?=e($row['spec'])?></td><td><?=demo_status_form('pc',$row,$statusMap['pc'])?></td></tr><?php endforeach;?></tbody></table></div>
    </article>
    <article class="demo-module-card">
      <header><span><?=nav_icon('software')?></span><div><h2>Software</h2><p><?=__('Versi, kebutuhan, periode, dan tugas otomatis.','Versions, purpose, periods, and automatic tasks.')?></p></div></header>
      <div class="demo-table-wrap"><table><thead><tr><th><?=__('Aplikasi','Application')?></th><th><?=__('Keperluan','Purpose')?></th><th><?=__('Masa berlaku','Period')?></th><th><?=__('Status','Status')?></th></tr></thead><tbody><?php foreach($demo['software'] as $row):?><tr><td><strong><?=e($row['name'])?></strong><small><?=e($row['version'])?></small></td><td><?=e($row['purpose'])?></td><td><?=e($row['period'])?></td><td><?=demo_status_form('software',$row,$statusMap['software'])?></td></tr><?php endforeach;?></tbody></table></div>
    </article>
    <article class="demo-module-card demo-module-wide">
      <header><span><?=nav_icon('tugas')?></span><div><h2><?=__('Tugas dan ACC Admin','Tasks and Admin approval')?></h2><p><?=__('Tugas dapat dikerjakan bersama, dikirim, lalu disetujui Admin.','Tasks can be collaborative, submitted, and approved by Admin.')?></p></div></header>
      <div class="demo-table-wrap"><table><thead><tr><th><?=__('Tugas','Task')?></th><th><?=__('PIC / Kontributor','Assignee / contributor')?></th><th><?=__('Prioritas','Priority')?></th><th><?=__('Status','Status')?></th></tr></thead><tbody><?php foreach($demo['tasks'] as $row):?><tr><td><strong><?=e($row['title'])?></strong><small><?=e($row['number'])?></small></td><td><?=e($row['pic'])?></td><td><?=e($row['priority'])?></td><td><?=demo_status_form('tasks',$row,$statusMap['tasks'])?></td></tr><?php endforeach;?></tbody></table></div>
    </article>
  </section>

  <section class="demo-bottom-grid"><article><h3><?=__('Cara kerja singkat','Workflow overview')?></h3><ol><li><b>01</b><span><?=__('Inventaris dan software diperbarui oleh Admin/User.','Inventory and software are updated by Admin/User.')?></span></li><li><b>02</b><span><?=__('Status yang perlu tindakan otomatis menjadi tugas.','Statuses requiring action automatically become tasks.')?></span></li><li><b>03</b><span><?=__('User mengirim hasil dan kontribusi untuk diperiksa.','Users submit results and contributions for review.')?></span></li><li><b>04</b><span><?=__('Admin menyetujui hasil menjadi riwayat laporan.','Admin approves results into report history.')?></span></li></ol></article><article><h3><?=__('Aktivitas demo','Demo activity')?></h3><ul><?php foreach($demo['activity'] as $item):?><li><span></span><?=e($item)?></li><?php endforeach;?></ul></article></section>
</main>
<?php function demo_status_form(string $module,array $row,array $statuses):string{ob_start();?><form class="demo-status-form" method="post" action="/demo/update"><?=Csrf::field()?><input type="hidden" name="module" value="<?=e($module)?>"><input type="hidden" name="id" value="<?=(int)$row['id']?>"><select name="status" aria-label="<?=__('Ubah status demo','Change demo status')?>"><?php foreach($statuses as $status):?><option value="<?=e($status)?>" <?=$row['status']===$status?'selected':''?>><?=e(__($status))?></option><?php endforeach;?></select><button type="submit"><?=__('Simpan','Save')?></button></form><?php return (string)ob_get_clean();} ?>
