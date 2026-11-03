<!doctype html>
<html lang="<?=e(lang())?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Motion graphic identitas visual SIMIT">
  <meta name="robots" content="index,follow">
  <title><?=e($title)?> · SIMIT</title>
  <link rel="icon" type="image/png" href="/assets/img/logo-simit.png">
  <link rel="stylesheet" href="/assets/css/motion.css">
</head>
<body class="motion-page">
  <main class="motion-stage">
    <header class="motion-toolbar">
      <button class="motion-back" id="motionBack" type="button" aria-label="<?=__('Kembali ke halaman sebelumnya','Return to the previous page')?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 18l-6-6 6-6"/><path d="M9 12h10"/></svg>
        <span><?=__('Kembali','Back')?></span>
      </button>
      <a class="motion-wordmark" href="/motion" aria-label="<?=__('Putar ulang motion graphic SIMIT','Replay the SIMIT motion graphic')?>">
        <img src="/assets/img/logo-simit-putih.png" alt="">
        <span><strong>SIMIT</strong><small>GTTC · IT Division</small></span>
      </a>
    </header>

    <section class="motion-frame" aria-labelledby="motionTitle">
      <div class="motion-heading">
        <p><?=__('IDENTITAS VISUAL','VISUAL IDENTITY')?></p>
        <h1 id="motionTitle"><?=__('Cerita di balik SIMIT.','The story behind SIMIT.')?></h1>
      </div>
      <div class="motion-player-shell">
        <video id="simitMotion" autoplay muted playsinline controls preload="auto" poster="/assets/img/logo-simit-putih.png">
          <source src="/assets/video/motion_graphis_simit.mp4" type="video/mp4">
          <?=__('Browser Anda tidak mendukung pemutar video.','Your browser does not support video playback.')?>
        </video>
        <button class="motion-sound" id="motionSound" type="button">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5L6 9H3v6h3l5 4z"/><path class="sound-waves" d="M15.5 8.5a5 5 0 010 7M18 6a8 8 0 010 12"/></svg>
          <span><?=__('Aktifkan suara','Turn on sound')?></span>
        </button>
      </div>
      <p class="motion-note"><?=__('Video diputar otomatis tanpa suara agar kompatibel dengan kebijakan browser.','The video starts muted to comply with browser autoplay policies.')?></p>
    </section>
  </main>
  <script src="/assets/js/motion.js"></script>
</body>
</html>
