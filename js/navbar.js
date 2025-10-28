// public/js/navbar.js
document.addEventListener("DOMContentLoaded", async () => {
  const userNameEl = document.getElementById('user-name');
  const userAvatarEl = document.getElementById('user-avatar');
  const navUser = document.getElementById('nav-user');
  const themeToggle = document.getElementById('theme-toggle');

  // Helper: apply theme to document body
  function applyTheme(theme) {
    if (theme === 'light') {
      document.body.classList.add('light-theme');
      themeToggle && (themeToggle.textContent = '🌤️ Light');
    } else {
      document.body.classList.remove('light-theme');
      themeToggle && (themeToggle.textContent = '🌙 Dark');
    }
    try { localStorage.setItem('theme', theme); } catch (e) {}
  }

  // Load user from localStorage or session endpoint
  let user = null;
  try { user = JSON.parse(localStorage.getItem('user')); } catch (e) { user = null; }

  if (!user) {
    try {
      const res = await fetch('/api/session/me');
      if (res.ok) {
        const payload = await res.json();
        user = payload?.user || payload || null;
        if (user) {
          try { localStorage.setItem('user', JSON.stringify(user)); } catch (e) {}
        }
      }
    } catch (err) { console.warn(err); }
  }

  // Apply theme preference (priority: session user -> localStorage -> default dark)
  let preferredTheme = (user && user.theme) || localStorage.getItem('theme') || 'dark';
  applyTheme(preferredTheme);

  // Populate navbar user info
  if (user && (user.email || user.name)) {
    userNameEl && (userNameEl.textContent = user.name || user.fullName || (user.email ? user.email.split('@')[0] : 'User'));
    if (userAvatarEl) {
      userAvatarEl.src = user.profile_image || user.profilePic || user.profileImage || '/assets/images/default-avatar.png';
    }
  } else {
    userNameEl && (userNameEl.textContent = 'Guest');
    if (userAvatarEl) userAvatarEl.src = '/assets/images/default-avatar.png';
  }

  // dropdown toggle (profile)
  navUser && navUser.addEventListener('click', (e) => {
    navUser.classList.toggle('active');
    e.stopPropagation();
  });
  // close dropdown when clicking elsewhere
  document.addEventListener('click', () => { navUser && navUser.classList.remove('active'); });

  // Theme toggle handler: persist to server if logged in, otherwise to localStorage
  if (themeToggle) {
    themeToggle.addEventListener('click', async () => {
      const newTheme = document.body.classList.contains('light-theme') ? 'dark' : 'light';
      applyTheme(newTheme);

      // If user is logged in, persist preference to DB via API
      if (user && user.id) {
        try {
          await fetch('/api/user/save_theme.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ theme: newTheme })
          });
          // update local session cache
          user.theme = newTheme;
          try { localStorage.setItem('user', JSON.stringify(user)); } catch (e) {}
        } catch (err) { console.warn('Failed to save theme:', err); }
      }
    });
  }

  // logout button (if present in dropdown)
  const logoutBtn = document.getElementById('logout-btn');
  logoutBtn && logoutBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    try { await fetch('/api/auth/logout.php', { method: 'POST' }); } catch (e) {}
    localStorage.removeItem('user');
    window.location.href = '/pages/login.php';
  });
});
