import { api } from './api.js';

const applyForm = document.getElementById('applyForm');
if (applyForm) {
  applyForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(applyForm);
    // if your backend expects multipart/form-data (e.g. file upload), send FormData directly
    try {
      const res = await fetch('/applications', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token') || ''}`
          // do NOT add Content-Type here when sending FormData
        },
        body: formData,
      });
      if (!res.ok) {
        const err = await res.json().catch(() => ({ message: res.statusText }));
        throw new Error(err.message || 'Application failed');
      }
      alert('Application submitted successfully');
      window.location.href = '/jobs.html';
    } catch (err) {
      console.error(err);
      alert(err.message || 'Failed to submit application');
    }
  });
}