import { api } from './api.js';

const postJobForm = document.getElementById('postJobForm');
if (postJobForm) {
  postJobForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(postJobForm);
    // convert FormData to JSON (or send formData if your route expects multipart)
    const payload = Object.fromEntries(formData.entries());

    try {
      const data = await api('/jobs', {
        method: 'POST',
        body: JSON.stringify(payload),
      });
      alert('Job posted successfully');
      window.location.href = '/jobs.html';
    } catch (err) {
      console.error(err);
      alert(err.message || 'Failed to post job');
    }
  });
}