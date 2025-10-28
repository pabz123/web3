import { api } from './api.js';

// Login form handler (assumes form id="loginForm")
const loginForm = document.getElementById('loginForm');
if (loginForm) {
  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = loginForm.querySelector('input[name="email"]').value;
    const password = loginForm.querySelector('input[name="password"]').value;
    try {
      const data = await api('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
      });
      // store token and redirect to dashboard or home
      if (data.token) localStorage.setItem('token', data.token);
      window.location.href = '/student.html' || '/';
    } catch (err) {
      console.error(err);
      alert(err.message || 'Login failed');
    }
  });
}

// Signup form handler (assumes form id="signupForm")
const signupForm = document.getElementById('signupForm');
if (signupForm) {
  signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(signupForm);
    const payload = Object.fromEntries(formData.entries());
    try {
      const data = await api('/auth/register', {
        method: 'POST',
        body: JSON.stringify(payload),
      });
      alert('Registration successful. Please log in.');
      window.location.href = '/login.html';
    } catch (err) {
      console.error(err);
      alert(err.message || 'Registration failed');
    }
  });
}

// Logout helper
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) {
  logoutBtn.addEventListener('click', () => {
    localStorage.removeItem('token');
    window.location.href = '/';
  });
}