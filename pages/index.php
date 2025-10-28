<?php
// Path: pages/home.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/helpers.php';
?>

<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Career Connect Hub</title>
<link rel="stylesheet" href="../css/global.css">
</head>
<body >

<div class="site-wrapper">
<header>
  <nav class="page-content-wrapper header-nav">
    <a class="logo" href="home.php">
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
      <a href="home.php">Home</a>
      <a href="success_stories.php">Success Stories</a>
      <a href="career tips.php">Career Tips</a>
      <a href="contact.php">Contact</a>
    </div>

    <div class="header-actions">
      <a class="btn btn-secondary" href="login.php">Log In</a>
      <a class="btn btn-primary" href="signup.php">Sign Up</a>
      <button id="theme-toggle" class="btn btn-secondary">🌙 Light Mode</button>
    </div>
  </nav>
</header>

<main class="page-content-wrapper">
<section class="hero-section">
<p>Hello, <?= getUserName(); ?>!</p>

<?php if (!empty($_SESSION['user'])): ?>
    
  <?php else: ?>
    <p><a href="/pages/login.php">Login</a> or <a href="/pages/signup.php">Register</a> to get started.</p>
  <?php endif; ?>
<h1>Your Future Starts Now</h1>
<p>Connecting students and graduates with their ideal career opportunities.</p>
<div class="search-bar">
  <input id="job-search-input" placeholder="Search jobs by title, company, location..." />
  <button id="job-search-btn" class="btn btn-primary">Search</button>
</div>
<div id="search-results" class="job-list-container"></div>

</section>
<section class="feature-section">
<h2>Explore Opportunities</h2>
<div class="grid-3-col">
<div class="feature-card">
  <div class="icon-wrapper">
    <!-- Job icon -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="36" height="36">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
            d="M4 7a2 2 0 012-2h12a2 2 0 012 2v2H4V7zm0 4h16v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7z"/>
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6"/>
    </svg>
  </div>
  <h3>Jobs</h3>
  <p>Browse full-time and part-time positions.</p>
  <a href="jobs.php">Find Jobs</a>
</div>

<div class="feature-card">
 <div class="icon-wrapper">
 <!-- Internship Icon -->
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 64 64" width="48" height="48">
  <circle cx="32" cy="18" r="6" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M20 38v-4c0-4 3-8 12-8s12 4 12 8v4" stroke-linecap="round" stroke-linejoin="round"/>
  <rect x="18" y="38" width="28" height="14" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M26 38v-4h12v4" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

 </div>
<h3>Internships</h3>
<p>Gain vital experience with top companies.</p>
<a href="internship.php">View Internships</a>
</div>
<div class="feature-card">
<div class="icon-wrapper">
<!-- Career Tips / Ideas Icon -->
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 64 64" width="48" height="48">
  <path d="M32 8c-8 0-14 6-14 14 0 6 4 10 7 13v5h14v-5c3-3 7-7 7-13 0-8-6-14-14-14z" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M26 48h12m-10 4h8m-6 4h4" stroke-linecap="round"/>
</svg>

</div>
<h3>Career Tips</h3>
<p>Get advice on CVs, interviews, and networking.</p>
<a href="career tips.php">Get Tips</a>
</div>
</div>
</section>
<section class="testimonial-section">
<h2>Success Stories</h2>
<div class="grid-3-col">
<div class="testimonial-card">
<p>"I landed my dream job thanks to the resources here!"</p>
<div class="testimonial-author">
<img alt="User profile picture" class="profile-pic" src="../assets/images/alex.jpg" width="640px"/>
<span>Alex Mukulu, Class of '24</span>
</div>
</div>
<div class="testimonial-card">
<p>"The internship filter helped me find a role perfectly aligned with my studies."</p>
<div class="testimonial-author">
<img alt="User profile picture" class="profile-pic" src="../assets/images/maria.jpg" width="640px"/>
<span>Maria Jovia Namirembe, Engineering Student</span>
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
 <span> For more info </span>
  <!-- Instagram -->
  <a href="https://www.instagram.com/my.preciouspabz" target="_blank" aria-label="Instagram" class="social-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
      <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
      <circle cx="17.5" cy="6.5" r="1.5"/>
    </svg>
  </a>

  <!-- GitHub -->
  <a href="https://github.com/pabz123" target="_blank" aria-label="GitHub" class="social-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M9 19c-4.5 1.5-4.5-2.5-6-3m12 5v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 18 2.77 5.07 5.07 0 0 0 17.91 0S16.73.35 14 2.48a13.38 13.38 0 0 0-8 0C3.27.35 2.09 0 2.09 0A5.07 5.07 0 0 0 2 2.77 5.44 5.44 0 0 0 .5 8.5c0 5.42 3.3 6.61 6.44 7a3.37 3.37 0 0 0-.94 2.61V21"/>
    </svg>
  </a>

  <!-- LinkedIn -->
  <a href="https://www.linkedin.com/in/pabz" target="_blank" aria-label="LinkedIn" class="social-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2h-1v9h-4v-9h-4v9H3V9h4v1.2a4.6 4.6 0 0 1 4-2.2z"/>
      <rect x="2" y="9" width="4" height="12"/>
      <circle cx="4" cy="4" r="2"/>
    </svg>
  </a>
</div>

</div>
</footer>
</div>
<script  >
 fetch('/api/jobs.php')
        .then(r=>r.json())
        .then(data=>{
          const cont = document.getElementById('jobsList');
          if(!data.jobs || !data.jobs.length){ cont.innerText = 'No jobs yet.'; return; }
          cont.innerHTML = data.jobs.map(j=>`<article><h3>${j.title}</h3><p>${j.company}</p><a href="/pages/jobs.php?id=${j.id}">View</a></article>`).join('');
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
  document.getElementById('job-search-btn').addEventListener('click', async () => {
  const q = document.getElementById('job-search-input').value.trim();
  const res = await fetch(`/api/jobs/search?q=${encodeURIComponent(q)}`);
  const jobs = await res.json();
  const container = document.getElementById('search-results');
  container.innerHTML = jobs.map(j => `
    <div class="job-card">
      <h3>${j.title}</h3>
      <p>${j.company} — ${j.location || ''}</p>
      <a href="/jobs/${j.id}" class="btn btn-secondary">View</a>
      <button class="btn btn-primary apply-btn" data-jobid="${j.id}" data-title="${j.title}" data-company="${j.company}">Apply</button>
    </div>
  `).join('');

  // attach apply listeners
  document.querySelectorAll('.apply-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const jobId = btn.dataset.jobid;
      const title = btn.dataset.title;
      const company = btn.dataset.company;
      const studentEmail = localStorage.getItem('studentEmail');
      await fetch('/api/student/apply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ jobId, studentEmail, title, company })
      });
      alert('Applied');
    });
  });
});

</script>

</body></html>