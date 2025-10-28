<?php
    
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Path: pages/student.php
require_once __DIR__ . '/../includes/auth_check.php'; // requires login
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

// Get current user ID
$userId = $_SESSION['user']['id'];

// Fetch user profile data
$stmt = $conn->prepare("
    SELECT 
        u.name, 
        u.email, 
        u.phone, 
        u.profile_image, 
        sp.education, 
        sp.skills 
    FROM users u
    LEFT JOIN student_profiles sp ON u.email = sp.email
    WHERE u.id = ?
");

$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

// Calculate profile completion
$fields = ['name', 'email', 'phone', 'profile_image', 'education', 'skills'];
$filledFields = 0;
foreach ($fields as $field) {
    if (!empty($userData[$field]) && $userData[$field] !== '/uploads/profile/default-avatar.png') {
        $filledFields++;
    }
}
$profileCompletion = round(($filledFields / count($fields)) * 100);

// Count applications
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM applications WHERE studentId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$appCount = $result->fetch_assoc()['count'];
$stmt->close();
?>
<!DOCTYPE html> 
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/student.css">


  </head>

  <body class="student-page">

  
    <?php include_once __DIR__ . '/../includes/navbar.php'; ?>


        <main class="centre-container">
    <p>Welcome, <?= getUserName() ?>.</p>




          <section class="dashboard-grid">
            <div class="grid-layout">
              <div class="card-form">
                <h2>Applied Jobs (<?= $appCount ?>)</h2>
                <p class="text-medium">You have <?= $appCount ?> active job application<?= $appCount != 1 ? 's' : '' ?>. Keep track of their status.</p>
                <a href="my-applications.php" class="btn btn-secondary">View Applications</a>
              </div>

              <div class="card-form">
                <h2>Interview Invites (2)</h2>
                <p class="text-medium">Prepare for your interviews with our resources.</p>
                <a href="interview.php" class="btn btn-primary">Prep Now</a>
              </div>

              <div class="card-form" id="profile-progress-card">
  <h2>Profile Completion (<span id="profile-percent"><?= $profileCompletion ?>%</span>)</h2>
  <div class="progress-bar">
    <div id="progress-bar-fill" style="width:<?= $profileCompletion ?>%; background: linear-gradient(90deg, #0a66c2 0%, #5ba3d0 100%); height: 100%; border-radius: 10px; transition: width 0.5s ease;"></div>
  </div>
  <p class="text-medium"><?= $profileCompletion < 100 ? 'Update your profile to unlock more opportunities.' : 'Your profile is complete! 🎉' ?></p>
  <a href="student-profile.php" class="btn btn-secondary">Update Profile</a>
</div>

            </div>
          </section>

          <section class="recommendations">
            <h2>Recommended Opportunities</h2>
            <div class="grid-3-col">
              <div class="article-card">
                <div class="card-content">
                  <h3>Junior Software Engineer</h3>
                  <p class="text-medium">Acme Tech Solutions</p>
                  <p>Full-time job opening for final year students or new graduates.</p>
                  <a href="jobs.php" class="btn btn-secondary">View Job</a>
                </div>
              </div>

              <div class="article-card">
                <div class="card-content">
                  <h3>Marketing Intern</h3>
                  <p class="text-medium">Global Brands Inc.</p>
                  <p>Summer internship for marketing and business students.</p>
                  <a href="internship.php" class="btn btn-secondary">View Internship</a>
                </div>
              </div>

              <div class="article-card">
                <div class="card-content">
                  <h3>Financial Analyst Trainee</h3>
                  <p class="text-medium">Wall Street Bank</p>
                  <p>One-year trainee program for finance majors.</p>
                  <a href="jobs.php" class="btn btn-secondary">View Trainee Program</a>
                </div>
              </div>
            </div>
          </section>

          <section class="guidance-resources">
           

              <div class="card-form">
                <h3>Interview Simulations</h3>
                <p class="text-medium">Practice common interview scenarios with our AI-powered tool.</p>
                <a href="interview.php" class="btn btn-primary">Start Practice</a>
              </div>
            
          </section>
        </main>

        
    <?php include_once __DIR__ . '/../includes/footer.php'; ?>
 
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Load saved theme
      const savedTheme = localStorage.getItem('theme') || 'dark';
      document.body.classList.add(savedTheme + '-theme');
      
      // Profile completion animation
      const progressBar = document.getElementById('progress-bar-fill');
      if (progressBar) {
        const targetWidth = progressBar.style.width;
        progressBar.style.width = '0%';
        setTimeout(() => {
          progressBar.style.width = targetWidth;
        }, 300);
      }
    });
    </script>
    
  </body>
</html>
