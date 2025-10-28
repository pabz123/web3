import { api } from './api.js';

async function loadJobs() {
  try {
    const jobs = await api('/jobs', { method: 'GET' });
    const container = document.getElementById('jobsList');
    if (!container) return;
    container.innerHTML = '';
    jobs.forEach(job => {
      const el = document.createElement('div');
      el.className = 'job-item';
      el.innerHTML = `
        <h3>${job.title}</h3>
        <p>${job.company || ''} — ${job.location || ''}</p>
        <p>${job.description ? job.description.substring(0, 200) + '...' : ''}</p>
        <a href="/cv.html" class="apply-link" data-job-id="${job.id}">Apply</a>
      `;
      container.appendChild(el);
    });
  } catch (err) {
    console.error('Failed to load jobs', err);
  }
}

document.addEventListener('DOMContentLoaded', loadJobs);