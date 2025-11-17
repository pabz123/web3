<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

$error = '';
$timeout_message = '';

// Check if redirected due to timeout
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $timeout_message = "Your session has expired due to inactivity. Please login again.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        // Admin login
        $stmt = mysqli_prepare($conn, "SELECT * FROM admins WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);

        if ($admin && (password_verify($password, $admin['password']) || $password === $admin['password'])) {
            $_SESSION['user'] = [
                'id' => $admin['id'],
                'email' => $admin['email'],
                'role' => 'admin'
            ];
            
            // Redirect to requested page or default admin page
            $redirect = $_SESSION['redirect_after_login'] ?? 'admin.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: " . $redirect);
            exit;
        }

        // Normal user login
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? AND role = ?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $role);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'] ?? $user['email'],
                'email' => $user['email'],
                'role' => $user['role'],
                'profile_image' => $user['profile_image'] ?? '/uploads/profile/default-avatar.png'
            ];

            // Redirect to requested page or default role page
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
            } else {
                if ($role === 'student') {
                    header("Location: student.php");
                } else {
                    header("Location: employer.php");
                }
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="../css/global.css">
  <link rel="stylesheet" href="../css/responsive.css">
  <style>
    .timeout-message {
      background-color: #fff3cd;
      color: #856404;
      padding: 12px 20px;
      border-radius: 6px;
      margin-bottom: 15px;
      border-left: 4px solid #ffc107;
      text-align: center;
    }
  </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<main class="center-container">
  <div class="card-form login-form">
    <h2>Welcome Back!</h2>
    <p>Sign in to continue your journey</p>
    <?php if ($timeout_message): ?>
      <div class="timeout-message"><?= htmlspecialchars($timeout_message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <select name="role" required>
        <option value="">Select Role</option>
        <option value="student">Student</option>
        <option value="employer">Employer</option>
      </select>
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.php">Sign up</a></p>
  </div>
</main>

<footer class="footer">
<div class="page-content-wrapper footer-content">
<p>© 2025 CaReeR CoNNect HuB. All rights reserved.</p>
<div class="footer-social">
 <span> For more info </span>
  <!-- Instagram -->
  <a href="https://www.instagram.com/YOUR_USERNAME" target="_blank" aria-label="Instagram" class="social-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
      <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
      <circle cx="17.5" cy="6.5" r="1.5"/>

    </svg>
  </a>

  <!-- GitHub -->
  <a href="https://github.com/YOUR_USERNAME" target="_blank" aria-label="GitHub" class="social-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M9 19c-4.5 1.5-4.5-2.5-6-3m12 5v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 18 2.77 5.07 5.07 0 0 0 17.91 0S16.73.35 14 2.48a13.38 13.38 0 0 0-8 0C3.27.35 2.09 0 2.09 0A5.07 5.07 0 0 0 2 2.77 5.44 5.44 0 0 0 .5 8.5c0 5.42 3.3 6.61 6.44 7a3.37 3.37 0 0 0-.94 2.61V21"/>
    </svg>
  </a>

  <!-- LinkedIn -->
  <a href="https://www.linkedin.com/in/YOUR_USERNAME" target="_blank" aria-label="LinkedIn" class="social-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2h-1v9h-4v-9h-4v9H3V9h4v1.2a4.6 4.6 0 0 1 4-2.2z"/>
      <rect x="2" y="9" width="4" height="12"/>
      <circle cx="4" cy="4" r="2"/>
    </svg>
  </a>
</div>

</div>
</footer>

<script>

 const toggle = document.getElementById('theme-toggle');
  const body = document.body;
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'light') {
    body.classList.add('light-theme');
    toggle.textContent = '🌙 Dark Mode';
  }
  toggle.addEventListener('click', () => {
    const isLight = body.classList.toggle('light-theme');
    toggle.textContent = isLight ? '🌙 Dark Mode' : '☀️ Light Mode';
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
  });

</script>

</body>

</html>