import { api } from './api.js';

export async function getMyApplications() {
  return apiFetch("/applications/my");
}

export async function getApplicantsForJob(jobId) {
  return apiFetch(`/applications/for-job/${jobId}`);
}