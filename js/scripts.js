// scripts.js - small UI helpers: mobile nav toggle, textarea autoresize, guest/auth nav toggle hints
document.addEventListener('DOMContentLoaded', function(){
  // Mobile nav toggle
  const tgl = document.querySelector('.mobile-toggle');
  const nav = document.querySelector('.topnav');
  if(tgl && nav){
    tgl.addEventListener('click', function(){
      nav.classList.toggle('open');
    });
  }

  // Auto-resize textareas marked for autoresize
  document.querySelectorAll('textarea[data-autoresize]').forEach(t => {
    t.style.overflow = 'hidden';
    const resize = () => { t.style.height = 'auto'; t.style.height = (t.scrollHeight) + 'px'; };
    t.addEventListener('input', resize);
    resize();
  });
});
