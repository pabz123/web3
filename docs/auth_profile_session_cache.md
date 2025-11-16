# Career Hub: Authentication, Profile & Session, and Cache Overview

## Overview
- **Stack**: Plain PHP with mysqli, structured into `pages/`, `api/`, `includes/`, `classes/`, and `cache/`.
- **Auth styles**: Page-based form login (`pages/login.php`) and JSON API login (`api/auth/login.php`).
- **Sessions**: Centralized in `includes/session.php` with secure cookie params and idle-timeout.
- **Profiles**: Student and employer profiles managed via server-rendered pages and supporting APIs.
- **Cache**: On-disk JSON cache for external job APIs under `cache/api/` managed by `classes/ExternalAPIService.php`.

## Profile Management
- **Tables**
  - **users**: core identity; fields used include `id`, `email`, `password` or `password_hash`, `role`, `name`, `phone`, `profile_image`, `theme`.
  - **student_profiles**: extended student info keyed by `email` (`fullName`, `phone`, `education`, `skills`, `profilePic`, `cvFile`).
  - **employers**: employer/company info keyed by `id` (same as `users.id`) (`company_name`, `logo`, `description`, `website_url`, `location`, `industry`).
- **Student**
  - Page: `pages/student-profile.php` (auth required via `includes/auth_check.php`).
  - Loads from `users` by `id` and `student_profiles` by `email`; merges into `$_SESSION['user']` to avoid stale data.
  - Form action: `includes/upload_helpers.php` (handles profile pic/CV and fields).
  - API: `api/student_profile.php` supports POST (update `users.name`/`profile_image` with CSRF) and GET by `email`.
- **Employer**
  - Page: `pages/employer-profile.php` (auth + role guard: `employer`/`admin`).
  - Joins `users` + `employers` by `users.id`; upserts employer record on POST; uploads `company_logo`.
  - Updates `$_SESSION['user']` for visible identity fields after save.

## Session Management
- **Bootstrap**: `includes/session.php`
  - Starts session if needed and sets cookie params: `httponly`, `samesite=Lax`, `secure` on HTTPS.
  - Idle timeout: `SESSION_TIMEOUT = 1800` seconds. On expiry, destroys session and redirects to `pages/login.php?timeout=1` unless on a public page.
  - Updates `$_SESSION['LAST_ACTIVITY']` each request.
  - Helper: `getCurrentUserFromSession()`.
- **Auth guard**: `includes/auth_check.php`
  - Requires `$_SESSION['user']`; otherwise stores `redirect_after_login` and redirects to login.
  - Exposes `$currentUser` for pages.
- **Logout**: `api/auth/logout.php`
  - Clears optional `remember_me` cookie & DB token, destroys session, returns JSON.

## Authentication Flows
- **Page login**: `pages/login.php`
  - Admin: checks `admins` table (supports `password_verify` or plain fallback).
  - Users: `SELECT * FROM users WHERE email = ? AND role = ?`; verifies `password` via `password_verify`.
  - On success, sets `$_SESSION['user']` and redirects to stored page or role home.
- **API login**: `api/auth/login.php`
  - Requires POST + CSRF (`csrf_token` or `X-CSRF-Token`).
  - Looks up `users` by email, verifies `password_hash`.
  - Loads role-specific image from `student_profiles` (profilePic) or `employers` (logo).
  - Calls `session_regenerate_id(true)`; sets a normalized `$_SESSION['user']` and returns `{ success: true, user }`.
  - Optional remember-me pattern is present but commented (token cookie + hashed DB token).
- **CSRF utilities**: `includes/csrf.php`
  - `csrf_token()` generates/stores a token in session.
  - `verify_csrf_token($token)` with `hash_equals`.

## JavaScript In Authentication
- **js/auth.js**
  - Handles AJAX login/signup conceptually via a generic `api()` helper and stores a `token` in `localStorage`.
  - Current PHP implementation is session-based and returns `{ success, user }` (no JWT). If using this file, adapt it to send CSRF, use `credentials: 'include'`, and rely on session cookies instead of tokens.
- **js/student-profile.js**
  - Shows profile completion, previews image, theme toggle, and logout.
  - Contains placeholder endpoints (e.g., `http://localhost:5000/...`, `/auth/session`) from an earlier SPA iteration; inline scripts in PHP pages use the correct PHP endpoints (e.g., `../api/auth/logout.php`).
- **Inline scripts**
  - Theme toggles in `pages/login.php` and `pages/signup.php` persist theme via `localStorage`.

## Cache Folder: Purpose and Behavior
- **Location**: `cache/api/`
- **Managed by**: `classes/ExternalAPIService.php`
  - Integrates with Adzuna and JSearch (RapidAPI) for jobs.
  - Before calling external APIs, computes a cache key (MD5 of parameters) and checks `cache/api/<hash>.json`.
  - If fresh (mtime within TTL), returns cached JSON; otherwise fetches, writes JSON to disk, and returns it.
  - TTL: default `3600` seconds; can be overridden via env `CACHE_TTL`.
- **Why**: Reduce API latency/cost and provide resilience to upstream outages.

## Recommendations
- **Unify password columns** across code paths (`password` vs `password_hash`) and ensure all logins use `password_verify` consistently.
- **Align JS login** with session-based API: include CSRF, send `credentials: 'include'`, expect `{ success, user }` not `{ token }`.
- **Replace placeholders** in `js/student-profile.js` with actual PHP endpoints or remove unused logic.
- **Consider enabling remember-me** securely (hashed token, expiry, rotation) if desired and add needed DB columns.
