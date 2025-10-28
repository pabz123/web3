// Central small API helper used by other frontend modules
export const API_BASE = ''; // same-origin

export async function api(path, opts = {}) {
  const headers = opts.headers ? { ...opts.headers } : {};
  const token = localStorage.getItem('token');
  if (token) headers['Authorization'] = `Bearer ${token}`;

  // If not sending FormData, default to JSON
  if (!(opts.body instanceof FormData)) {
    headers['Content-Type'] = headers['Content-Type'] || 'application/json';
  }

  const res = await fetch(API_BASE + path, {
    credentials: 'same-origin',
    ...opts,
    headers,
  });

  const contentType = res.headers.get('content-type') || '';
  if (res.ok) {
    if (contentType.includes('application/json')) return res.json();
    return res.text();
  }

  // try to parse error body
  let errBody;
  try { errBody = await res.json(); } catch (e) { errBody = await res.text(); }
  const error = new Error(errBody?.message || res.statusText || 'Request failed');
  error.status = res.status;
  error.body = errBody;
  throw error;
}