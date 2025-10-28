<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

// Redirect if already logged in as admin
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $secret_key = trim($_POST['secret_key'] ?? '');

    // Admin secret key (change this!)
    $ADMIN_SECRET = 'CCH_ADMIN_2025';

    if ($secret_key !== $ADMIN_SECRET) {
        $error = 'Invalid admin credentials';
    } else {
        // Check admin in database
        $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['user'] = [
                'id' => $admin['id'],
                'name' => $admin['name'] ?? 'Admin',
                'email' => $admin['email'],
                'role' => 'admin',
                'profile_image' => '/uploads/profile/admin-avatar.png'
            ];
            header('Location: admin.php');
            exit;
        } else {
            $error = 'Invalid admin credentials';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access | Career Connect Hub</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .admin-login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        .admin-login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .admin-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .admin-icon {
            font-size: 4rem;
            margin-bottom: 15px;
        }
        .admin-title {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 5px;
        }
        .admin-subtitle {
            color: #666;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .admin-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .admin-btn:hover {
            transform: translateY(-2px);
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-card">
            <div class="admin-header">
                <div class="admin-icon">🔐</div>
                <h1 class="admin-title">Admin Access</h1>
                <p class="admin-subtitle">Authorized Personnel Only</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="secret_key">Secret Key</label>
                    <input type="password" id="secret_key" name="secret_key" required autocomplete="off">
                </div>

                <button type="submit" class="admin-btn">🔓 Access Dashboard</button>
            </form>
        </div>

        <div class="back-link">
            <a href="../index.php">← Return to Main Site</a>
        </div>
    </div>

    <script>
        // Prevent right-click and inspect
        document.addEventListener('contextmenu', e => e.preventDefault());
        
        // Disable F12 and other dev tools shortcuts
        document.addEventListener('keydown', e => {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') ||
                (e.ctrlKey && e.shiftKey && e.key === 'J') ||
                (e.ctrlKey && e.key === 'U')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
