<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/helpers.php';

// Load current user from session and database
$user = $_SESSION['user'] ?? [];
$userId = (int)($user['id'] ?? 0);

if ($userId) {
    // Fetch latest user data
    $stmt = $conn->prepare("SELECT name, email, profile_image, theme FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $dbUser = $res->fetch_assoc();
    if ($dbUser) {
        $user = array_merge($user, $dbUser);
    }
    $stmt->close();
}

$profile_image = $user['profile_image'] ?? '/uploads/profile/default-avatar.png';
$theme = $user['theme'] ?? 'dark';
$userName = $user['name'] ?? 'User';
$userEmail = $user['email'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Settings — Career Connect Hub</title>
  <link rel="manifest" href="../manifest.json">
  <meta name="theme-color" content="#0a66c2">
  <link rel="stylesheet" href="../css/global.css">
  <link rel="stylesheet" href="../css/responsive.css">
  <link rel="stylesheet" href="../css/student-profile.css">
  <script src="../js/app.js" defer></script>
  <style>
    .settings-container {
      max-width: 800px;
      margin: 40px auto;
      padding: 20px;
    }
    .settings-section {
      background: var(--card-background);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 24px;
      margin-bottom: 20px;
    }
    .settings-section h2 {
      margin-top: 0;
      margin-bottom: 20px;
      color: var(--text-dark);
      font-size: 1.5rem;
    }
    .setting-item {
      margin-bottom: 20px;
    }
    .setting-item label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: var(--text-dark);
    }
    .setting-item p {
      color: var(--text-medium);
      font-size: 0.9rem;
      margin-top: 4px;
    }
    .theme-preview {
      display: flex;
      gap: 16px;
      margin-top: 12px;
    }
    .theme-option {
      flex: 1;
      padding: 20px;
      border: 2px solid var(--border-color);
      border-radius: 8px;
      cursor: pointer;
      text-align: center;
      transition: all 0.3s;
    }
    .theme-option.active {
      border-color: var(--linkedin-blue);
      background: rgba(10, 102, 194, 0.1);
    }
    .theme-option:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .theme-icon {
      font-size: 2rem;
      margin-bottom: 8px;
    }
    .profile-info {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 16px;
      background: var(--background-color);
      border-radius: 8px;
    }
    .profile-info img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--linkedin-blue);
    }
    .profile-details h3 {
      margin: 0 0 4px 0;
      color: var(--text-dark);
    }
    .profile-details p {
      margin: 0;
      color: var(--text-medium);
    }
    .success-message {
      padding: 12px;
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
      border-radius: 6px;
      margin-bottom: 20px;
      display: none;
    }
    .success-message.show {
      display: block;
    }
  </style>
</head>
<body class="<?= $theme ?>-theme">
  <header class="main-header">
    <nav class="nav-bar">
      <a class="logo" href="../index.php">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2.2">
          <circle cx="32" cy="32" r="30" stroke="#0a66c2"/>
          <rect x="18" y="28" width="28" height="18" rx="3" ry="3" stroke="#0a66c2"/>
          <path d="M24 28v-4h16v4" stroke="#0a66c2"/>
          <path d="M32 12l14 6-14 6-14-6 14-6z" stroke="#0a66c2"/>
          <path d="M32 24v4" stroke="#0a66c2"/>
        </svg>
        <span class="logo-text">CaReeR CoNNect HuB</span>
      </a>

      <div class="nav-links">
        <a href="student.php">Dashboard</a>
        <a href="jobs.php">Jobs</a>
        <a href="student-profile.php">Profile</a>
        <a href="settings.php" style="color: var(--linkedin-blue);">Settings</a>
      </div>

      <button class="hamburger" id="nav-hamburger" aria-label="Toggle navigation" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <div class="header-actions">
        <img src="<?= $profile_image ?>" alt="Profile" class="profile-avatar-small">
        <button id="logout-btn" class="btn btn-secondary small-btn">Logout</button>
      </div>
    </nav>
  </header>

  <main class="settings-container">
    <h1>Settings</h1>
    
    <div id="successMessage" class="success-message">
      ✓ Settings saved successfully!
    </div>

    <!-- Profile Section -->
    <section class="settings-section">
      <h2>Profile Information</h2>
      <div class="profile-info">
        <img src="<?= htmlspecialchars($profile_image) ?>" alt="Profile">
        <div class="profile-details">
          <h3><?= htmlspecialchars($userName) ?></h3>
          <p><?= htmlspecialchars($userEmail) ?></p>
          <a href="student-profile.php" class="btn btn-primary" style="margin-top: 10px; display: inline-block;">Edit Profile</a>
        </div>
      </div>
    </section>

    <!-- Appearance Section -->
    <section class="settings-section">
      <h2>Appearance</h2>
      <div class="setting-item">
        <label>Theme Preference</label>
        <p>Choose your preferred color theme. Your selection will be saved and applied across all pages.</p>
        <div class="theme-preview">
          <div class="theme-option <?= $theme === 'dark' ? 'active' : '' ?>" data-theme="dark">
            <div class="theme-icon">🌙</div>
            <strong>Dark Mode</strong>
            <p style="font-size: 0.85rem; margin-top: 4px;">Easy on the eyes</p>
          </div>
          <div class="theme-option <?= $theme === 'light' ? 'active' : '' ?>" data-theme="light">
            <div class="theme-icon">☀️</div>
            <strong>Light Mode</strong>
            <p style="font-size: 0.85rem; margin-top: 4px;">Classic and bright</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PWA Section -->
    <section class="settings-section">
      <h2>📱 Mobile App</h2>
      <div class="setting-item">
        <label>Install as App</label>
        <p>Install Career Connect Hub on your device for a native app experience with offline access and home screen shortcuts.</p>
        
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 12px; margin: 15px 0; color: white;">
          <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
            <div style="font-size: 3rem;">📱</div>
            <div>
              <h3 style="margin: 0; color: white;">Get the App</h3>
              <p style="margin: 5px 0 0 0; opacity: 0.9;">Install for quick access and offline use</p>
            </div>
          </div>
          
          <div style="background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
            <h4 style="margin: 0 0 10px 0; color: white;">✨ Features:</h4>
            <ul style="margin: 0; padding-left: 20px;">
              <li>🚀 Fast loading and smooth performance</li>
              <li>📡 Works offline with cached content</li>
              <li>🏠 Add to home screen for quick access</li>
              <li>🔔 Get notifications (coming soon)</li>
              <li>💾 Save data with smart caching</li>
            </ul>
          </div>
          
          <button id="install-app-btn" class="btn" style="background: white; color: #667eea; font-weight: 600; width: 100%; padding: 14px; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; display: none;">
            📥 Install App Now
          </button>
          
          <div id="install-status" style="margin-top: 15px; padding: 12px; background: rgba(255,255,255,0.2); border-radius: 8px; text-align: center;">
            <span id="install-status-text">Checking installation status...</span>
          </div>
        </div>
        
        <div style="background: var(--card-bg); border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-top: 15px;">
          <h4 style="margin: 0 0 10px 0;">📖 How to Install:</h4>
          <div style="display: grid; gap: 10px;">
            <div style="padding: 10px; background: var(--background-color); border-radius: 6px;">
              <strong>On Android (Chrome):</strong>
              <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: var(--text-secondary);">Click the "Install App" button above or tap menu (⋮) → "Add to Home screen"</p>
            </div>
            <div style="padding: 10px; background: var(--background-color); border-radius: 6px;">
              <strong>On iOS (Safari):</strong>
              <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: var(--text-secondary);">Tap Share button (□↑) → "Add to Home Screen"</p>
            </div>
            <div style="padding: 10px; background: var(--background-color); border-radius: 6px;">
              <strong>On Desktop (Chrome/Edge):</strong>
              <p style="margin: 5px 0 0 0; font-size: 0.9rem; color: var(--text-secondary);">Click the install icon (⊕) in the address bar or use the button above</p>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <footer class="footer">
    <div class="page-content-wrapper footer-content">
      <p>© 2025 CaReeR CoNNect HuB. All rights reserved.</p>
      <div class="footer-social">
        <span>Follow us</span>
        <a href="https://www.instagram.com/my.preciouspabz" target="_blank" class="social-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
            <circle cx="17.5" cy="6.5" r="1.5"/>
          </svg>
        </a>
        <a href="https://github.com/pabz123" target="_blank" class="social-link">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 19c-4.5 1.5-4.5-2.5-6-3m12 5v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 18 2.77 5.07 5.07 0 0 0 17.91 0S16.73.35 14 2.48a13.38 13.38 0 0 0-8 0C3.27.35 2.09 0 2.09 0A5.07 5.07 0 0 0 2 2.77 5.44 5.44 0 0 0 .5 8.5c0 5.42 3.3 6.61 6.44 7a3.37 3.37 0 0 0-.94 2.61V21"/>
          </svg>
        </a>
      </div>
    </div>
  </footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const themeOptions = document.querySelectorAll('.theme-option');
  const successMessage = document.getElementById('successMessage');
  const logoutBtn = document.getElementById('logout-btn');
  const hamburger = document.getElementById('nav-hamburger');
  const navLinks = document.querySelector('.nav-links');
  const installBtn = document.getElementById('installBtn');
  const installStatus = document.getElementById('installStatus');

  let deferredPrompt;

  // Theme selection
  themeOptions.forEach(option => {
    option.addEventListener('click', async () => {
      const selectedTheme = option.dataset.theme;
      
      // Update UI immediately
      themeOptions.forEach(opt => opt.classList.remove('active'));
      option.classList.add('active');
      
      body.classList.remove('light-theme', 'dark-theme');
      body.classList.add(`${selectedTheme}-theme`);
      localStorage.setItem('theme', selectedTheme);

      // Save to database
      try {
        const res = await fetch('../api/user/save_theme.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ theme: selectedTheme })
        });
        
        if (res.ok) {
          successMessage.classList.add('show');
          setTimeout(() => successMessage.classList.remove('show'), 3000);
        }
      } catch (err) {
        console.error('Failed to save theme:', err);
      }
    });
  });

  // Hamburger menu
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      const expanded = hamburger.getAttribute('aria-expanded') === 'true';
      hamburger.setAttribute('aria-expanded', String(!expanded));
      navLinks.classList.toggle('active');
    });
  }

  // Logout
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      if (confirm('Are you sure you want to logout?')) {
        try {
          await fetch('../api/auth/logout.php', { method: 'POST', credentials: 'include' });
        } catch (err) {
          console.error('Logout failed:', err);
        }
        localStorage.clear();
        window.location.href = 'login.php';
      }
    });
  }

  // PWA Installation - Enhanced
  const installAppBtn = document.getElementById('install-app-btn');
  const installStatusDiv = document.getElementById('install-status');
  const installStatusText = document.getElementById('install-status-text');

  // Check if already installed
  if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone) {
    installStatusText.innerHTML = '✅ App is already installed!<br><small>You\'re using the installed version</small>';
    installStatusDiv.style.background = 'rgba(16, 185, 129, 0.2)';
  } else {
    installStatusText.textContent = '⏳ Waiting for install prompt...';
  }

  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    if (installAppBtn) {
      installAppBtn.style.display = 'block';
      installStatusText.innerHTML = '✅ Ready to install!<br><small>Click the button above to install</small>';
      installStatusDiv.style.background = 'rgba(16, 185, 129, 0.2)';
    }
  });

  if (installAppBtn) {
    installAppBtn.addEventListener('click', async () => {
      if (!deferredPrompt) {
        installStatusText.innerHTML = 'ℹ️ App is already installed or not available<br><small>Try the manual installation steps below</small>';
        return;
      }

      installAppBtn.disabled = true;
      installAppBtn.textContent = '⏳ Installing...';

      deferredPrompt.prompt();
      const { outcome } = await deferredPrompt.userChoice;
      
      if (outcome === 'accepted') {
        installStatusText.innerHTML = '🎉 App installed successfully!<br><small>Check your home screen or app drawer</small>';
        installStatusDiv.style.background = 'rgba(16, 185, 129, 0.3)';
        installAppBtn.style.display = 'none';
        
        // Show success toast
        if (window.CareerConnectApp && window.CareerConnectApp.showToast) {
          window.CareerConnectApp.showToast('✅ App installed successfully!');
        }
      } else {
        installStatusText.innerHTML = '❌ Installation cancelled<br><small>You can try again anytime</small>';
        installAppBtn.disabled = false;
        installAppBtn.textContent = '📥 Install App Now';
      }
      
      deferredPrompt = null;
    });
  }

  // Listen for app installed event
  window.addEventListener('appinstalled', () => {
    installStatusText.innerHTML = '🎉 App installed successfully!<br><small>You can now access it from your home screen</small>';
    installStatusDiv.style.background = 'rgba(16, 185, 129, 0.3)';
    if (installAppBtn) {
      installAppBtn.style.display = 'none';
    }
  });
});
</script>
</body>
</html>
