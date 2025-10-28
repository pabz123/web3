<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';

// Get job ID from URL
$jobId = isset($_GET['jobId']) ? (int)$_GET['jobId'] : 0;
if ($jobId === 0) {
  die('Invalid Job ID');
}

// Fetch job details from database
$stmt = $conn->prepare("
    SELECT 
        j.id, 
        j.title, 
        j.description, 
        j.location, 
        j.type,
        u.name AS company
    FROM jobs j
    LEFT JOIN users u ON j.employer_id = u.id
    WHERE j.id = ?
");

$stmt->bind_param('i', $jobId);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

if (!$job) {
  die('Job not found');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apply for <?= htmlspecialchars($job['title']) ?> | Career Connect Hub</title>
  <link rel="stylesheet" href="../css/global.css">
  <link rel="stylesheet" href="../css/responsive.css">
  <style>
    .apply-container {
      max-width: 700px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    .job-header {
      background: var(--card-bg);
      padding: 30px;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .file-upload-area {
      border: 2px dashed var(--border-color);
      border-radius: 12px;
      padding: 40px;
      text-align: center;
      background: var(--card-bg);
      cursor: pointer;
      transition: all 0.3s;
    }
    .file-upload-area:hover {
      border-color: var(--linkedin-blue);
      background: rgba(10, 102, 194, 0.05);
    }
    .file-upload-area input[type="file"] {
      display: none;
    }
    .file-name {
      margin-top: 10px;
      color: var(--linkedin-blue);
      font-weight: 600;
    }
  </style>
</head>
<body>
  <?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="page-content-wrapper">
  <div class="apply-container">
    <button onclick="window.history.back()" class="btn btn-secondary" style="margin-bottom: 20px;">
      ← Back
    </button>
    
    <div class="job-header">
      <h1 style="font-size: 2rem; margin-bottom: 10px;"><?= htmlspecialchars($job['title']) ?></h1>
      <p style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 5px;">
        <strong><?= htmlspecialchars($job['company'] ?? 'Company') ?></strong>
      </p>
      <p style="color: var(--text-secondary);">
        📍 <?= htmlspecialchars($job['location'] ?? 'Location') ?>
      </p>
    </div>
    
    <div class="card-form">
      <h2 style="margin-bottom: 25px;">📝 Application Form</h2>
      
      <form id="applyForm" method="post" action="../api/applications.php" enctype="multipart/form-data">
        <input type="hidden" name="jobId" id="jobId" value="<?= $jobId ?>"/>

        <div style="margin-bottom: 20px;">
          <label for="fullName" style="display: block; margin-bottom: 8px; font-weight: 600;">Full Name *</label>
          <input id="fullName" name="fullName" type="text" placeholder="John Doe" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);" />
        </div>

        <div style="margin-bottom: 20px;">
          <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600;">Email Address *</label>
          <input id="email" name="email" type="email" placeholder="john.doe@example.com" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);" />
        </div>

        <div style="margin-bottom: 20px;">
          <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 600;">Phone Number *</label>
          <input id="phone" name="phone" type="tel" placeholder="+1 (555) 123-4567" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);" />
        </div>

        <div style="margin-bottom: 20px;">
          <label for="coverLetter" style="display: block; margin-bottom: 8px; font-weight: 600;">Cover Letter (Optional)</label>
          <textarea id="coverLetter" name="coverLetter" placeholder="Tell us why you're a great fit for this role..." rows="6" style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary); resize: vertical;"></textarea>
        </div>

        <div style="margin-bottom: 25px;">
          <label style="display: block; margin-bottom: 8px; font-weight: 600;">Resume/CV Upload *</label>
          <div class="file-upload-area" onclick="document.getElementById('cvFile').click()">
            <input id="cvFile" name="cvFile" type="file" accept=".pdf,.doc,.docx" required />
            <div style="font-size: 3rem; margin-bottom: 10px;">📄</div>
            <p style="font-size: 1.1rem; margin-bottom: 5px;">Click to upload or drag and drop</p>
            <p style="font-size: 0.9rem; color: var(--text-secondary);">PDF, DOC, or DOCX (Max 5MB)</p>
            <div id="fileName" class="file-name" style="display: none;"></div>
          </div>
        </div>

        <button class="btn btn-primary" type="submit" style="width: 100%; padding: 14px; font-size: 1.1rem;">
          🚀 Submit Application
        </button>
      </form>
    </div>
  </div>
</main>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
<script>
// Load theme
const savedTheme = localStorage.getItem('theme') || 'dark';
document.body.classList.add(savedTheme + '-theme');

// File upload display
const fileInput = document.getElementById('cvFile');
const fileName = document.getElementById('fileName');

fileInput.addEventListener('change', (e) => {
  if (e.target.files.length > 0) {
    fileName.textContent = '✓ ' + e.target.files[0].name;
    fileName.style.display = 'block';
  }
});

// Form submission
document.getElementById('applyForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const submitBtn = e.target.querySelector('button[type="submit"]');
  const originalText = submitBtn.textContent;
  submitBtn.textContent = '⏳ Submitting...';
  submitBtn.disabled = true;

  const form = e.target;
  const formData = new FormData(form);

  try {
    const response = await fetch(form.action, {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      alert('✅ Application submitted successfully!\n\nWe\'ll review your application and get back to you soon.');
      window.location.href = 'my-applications.php';
    } else {
      alert('❌ Error: ' + (result.error || 'Failed to submit application'));
      submitBtn.textContent = originalText;
      submitBtn.disabled = false;
    }
  } catch (error) {
    alert('❌ Network error. Please check your connection and try again.');
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
  }
});
</script>


</body>
</html>
