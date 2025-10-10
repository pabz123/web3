WEB3 Backend PHP - Full Package
===============================

Files included:
- db_connect.php        : PDO connector (configure via env vars DB_HOST, DB_NAME, DB_USER, DB_PASS)
- register.php          : user registration
- login.php             : user login
- logout.php            : user logout
- fetch_jobs.php        : fetch job listings (supports q parameter)
- apply_job.php         : apply to a job (logged-in users)
- admin_login.php       : admin authentication
- admin_jobs.php        : list all jobs for admin
- add_job.php           : admin adds job
- README.md             : this file

Setup:
1. Import the database schema `web3_jobs_schema.sql` (I provided earlier) into MySQL.
2. Place these PHP files in your server folder (e.g., WAMP `www/web3/backend/`).
3. Configure environment variables or edit `db_connect.php` values for DB connection.
4. Make sure sessions work (PHP sessions folder writable).
5. For security, use HTTPS, set secure cookies, and protect admin endpoints with additional checks.

Security notes:
- Passwords are hashed using PHP's password_hash().
- Use strong admin passwords and do not store secrets in git.
- Consider CSRF tokens and rate limiting for production.

