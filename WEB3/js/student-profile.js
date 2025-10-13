const form = document.getElementById('profileForm');
const completionText = document.getElementById('completion-text');
const completionFill = document.getElementById('completion-fill');
const preview = document.getElementById('profilePreview');
const smallPic = document.getElementById('profile-pic-small');

document.getElementById('profilePic').addEventListener('change', e => {
  const file = e.target.files[0];
  if (file) {
    preview.src = URL.createObjectURL(file);
    smallPic.src = URL.createObjectURL(file);
  }
});

// Calculate completion %
function updateCompletion() {
  const inputs = Array.from(form.querySelectorAll('input, select, textarea'));
  const filled = inputs.filter(i => i.value.trim() !== '').length;
  const percent = Math.round((filled / inputs.length) * 100);
  completionText.textContent = `${percent}%`;
  completionFill.style.width = `${percent}%`;
}

form.addEventListener('input', updateCompletion);

// Save data
form.addEventListener('submit', async e => {
  e.preventDefault();
  const formData = new FormData(form);

  try {
    const res = await fetch('http://localhost:5000/api/student/profile', {
      method: 'POST',
      body: formData,
    });
    const result = await res.json();
    alert(result.message || 'Profile saved successfully!');
  } catch (err) {
    console.error(err);
    alert('Error saving profile');
  }
});
 const toggle = document.getElementById('theme-toggle');
  const body = document.body;

  // Load saved theme from localStorage
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'light') {
    body.classList.add('light-theme');
    toggle.textContent = '🌙 Dark Mode';
  }

  // Toggle theme on button click
  toggle.addEventListener('click', () => {
    const isLight = body.classList.toggle('light-theme');
    toggle.textContent = isLight ? '🌙 Dark Mode' : '☀️ Light Mode';
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
  });
  const logoutBtn = document.getElementById('logout-btn');

  if (logoutBtn) {
    logoutBtn.addEventListener('click', async () => {
      try {
        // Optionally tell your server to destroy the session
        await fetch('http://localhost:5000/auth/logout', {
          method: 'POST',
          credentials: 'include'
        });

        // Clear localStorage or tokens
        localStorage.removeItem('token');
        localStorage.removeItem('theme');

        alert('You have been logged out.');
        window.location.href = 'login.html';
      } catch (err) {
        console.error('Logout error:', err);
        alert('Could not log out. Please try again.');
      }
  });}