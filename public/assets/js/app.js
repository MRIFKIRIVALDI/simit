const button=document.getElementById('menuButton');
const sidebar=document.getElementById('sidebar');
button?.addEventListener('click',()=>sidebar?.classList.toggle('open'));
document.addEventListener('click',e=>{if(innerWidth<=760&&sidebar?.classList.contains('open')&&!sidebar.contains(e.target)&&!button?.contains(e.target))sidebar.classList.remove('open')});
document.getElementById('themeButton')?.addEventListener('click',()=>{const dark=document.documentElement.dataset.theme!=='dark';document.documentElement.dataset.theme=dark?'dark':'light';localStorage.setItem('simit-theme',dark?'dark':'light')});
document.querySelectorAll('img[src*="logo-simit"]').forEach(logo=>{
  logo.style.cursor='pointer';
  const link=logo.closest('a');
  if(link){link.href='/motion';link.setAttribute('aria-label','Putar motion graphic SIMIT');return;}
  logo.classList.add('simit-motion-trigger');logo.tabIndex=0;logo.setAttribute('role','link');logo.setAttribute('aria-label','Putar motion graphic SIMIT');
  const openMotion=()=>{window.location.href='/motion'};
  logo.addEventListener('click',openMotion);
  logo.addEventListener('keydown',event=>{if(event.key==='Enter'||event.key===' '){event.preventDefault();openMotion()}});
});
window.addEventListener('pageshow',event=>{if(event.persisted)window.location.reload()});

