<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: admin-login.php');
    exit;
}

// Fetch statistics
$stats = [];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Total students
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'");
$stats['total_students'] = $result->fetch_assoc()['count'];

// Total employers
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'employer'");
$stats['total_employers'] = $result->fetch_assoc()['count'];

// Total jobs
$result = $conn->query("SELECT COUNT(*) as count FROM jobs");
$stats['total_jobs'] = $result->fetch_assoc()['count'];

// Total applications
$result = $conn->query("SELECT COUNT(*) as count FROM applications");
$stats['total_applications'] = $result->fetch_assoc()['count'];

// Recent users (last 10)
$recent_users = [];
$result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 10");
while ($row = $result->fetch_assoc()) {
    $recent_users[] = $row;
}

// Recent jobs (last 10)
$recent_jobs = [];
$result = $conn->query("SELECT id, title, company, location, posted_date FROM jobs ORDER BY posted_date DESC LIMIT 10");
while ($row = $result->fetch_assoc()) {
    $recent_jobs[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | Career Connect Hub</title>
  <link rel="stylesheet" href="../css/global.css" />
  <link rel="stylesheet" href="../css/responsive.css" />
  <style>
    .admin-dashboard {
      padding: 40px 20px;
      max-width: 1400px;
      margin: 0 auto;
    }
    .admin-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }
    .stat-card {
      background: var(--card-bg);
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-left: 4px solid;
    }
    .stat-card.users { border-left-color: #3b82f6; }
    .stat-card.students { border-left-color: #10b981; }
    .stat-card.employers { border-left-color: #f59e0b; }
    .stat-card.jobs { border-left-color: #8b5cf6; }
    .stat-card.applications { border-left-color: #ec4899; }
    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      margin: 10px 0;
    }
    .stat-label {
      color: var(--text-secondary);
      font-size: 0.9rem;
    }
    .admin-tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      border-bottom: 2px solid var(--border-color);
    }
    .admin-tab {
      padding: 12px 24px;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 1rem;
      color: var(--text-secondary);
      border-bottom: 3px solid transparent;
      transition: all 0.3s;
    }
    .admin-tab.active {
      color: var(--linkedin-blue);
      border-bottom-color: var(--linkedin-blue);
    }
    .admin-tab:hover {
      color: var(--text-primary);
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    .data-table {
      background: var(--card-bg);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .data-table table {
      width: 100%;
      border-collapse: collapse;
    }
    .data-table th {
      background: var(--linkedin-blue);
      color: white;
      padding: 15px;
      text-align: left;
      font-weight: 600;
    }
    .data-table td {
      padding: 12px 15px;
      border-bottom: 1px solid var(--border-color);
    }
    .data-table tr:hover {
      background: rgba(10, 102, 194, 0.05);
    }
    .action-btn {
      padding: 6px 12px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      font-size: 0.85rem;
      margin-right: 5px;
    }
    .btn-view { background: #3b82f6; color: white; }
    .btn-delete { background: #ef4444; color: white; }
    .btn-edit { background: #10b981; color: white; }
  </style>
</head>
<body>
  <?php include_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="admin-dashboard">
  <div class="admin-header">
    <div>
      <h1 style="font-size: 2.5rem; margin-bottom: 5px;">🛡️ Admin Dashboard</h1>
      <p style="color: var(--text-secondary);">Monitor and manage the entire platform</p>
    </div>
    <div>
      <span style="color: var(--text-secondary);">Logged in as: <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></span>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="stats-grid">
    <div class="stat-card users">
      <div class="stat-label">👥 Total Users</div>
      <div class="stat-number"><?= $stats['total_users'] ?></div>
    </div>
    <div class="stat-card students">
      <div class="stat-label">🎓 Students</div>
      <div class="stat-number"><?= $stats['total_students'] ?></div>
    </div>
    <div class="stat-card employers">
      <div class="stat-label">🏢 Employers</div>
      <div class="stat-number"><?= $stats['total_employers'] ?></div>
    </div>
    <div class="stat-card jobs">
      <div class="stat-label">💼 Active Jobs</div>
      <div class="stat-number"><?= $stats['total_jobs'] ?></div>
    </div>
    <div class="stat-card applications">
      <div class="stat-label">📋 Applications</div>
      <div class="stat-number"><?= $stats['total_applications'] ?></div>
    </div>
  </div>

  <!-- Tabs -->
  <div class="admin-tabs">
    <button class="admin-tab active" onclick="showTab('users')">👥 All Users</button>
    <button class="admin-tab" onclick="showTab('jobs')">💼 All Jobs</button>
    <button class="admin-tab" onclick="showTab('applications')">📋 Applications</button>
    <button class="admin-tab" onclick="showTab('recent')">🕒 Recent Activity</button>
  </div>

  <!-- Tab Contents -->
  <div id="users-tab" class="tab-content active">
    <div class="data-table">
      <div id="users-data">Loading users...</div>
    </div>
  </div>

  <div id="jobs-tab" class="tab-content">
    <div class="data-table">
      <div id="jobs-data">Loading jobs...</div>
    </div>
  </div>

  <div id="applications-tab" class="tab-content">
    <div class="data-table">
      <div id="applications-data">Loading applications...</div>
    </div>
  </div>

  <div id="recent-tab" class="tab-content">
    <h3>Recent Users</h3>
    <div class="data-table" style="margin-bottom: 30px;">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent_users as $user): ?>
            <tr>
              <td><?= $user['id'] ?></td>
              <td><?= htmlspecialchars($user['name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><span style="padding: 4px 8px; background: #dbeafe; color: #1e40af; border-radius: 4px; font-size: 0.85rem;"><?= htmlspecialchars($user['role']) ?></span></td>
              <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h3>Recent Jobs</h3>
    <div class="data-table">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Company</th>
            <th>Location</th>
            <th>Posted</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent_jobs as $job): ?>
            <tr>
              <td><?= $job['id'] ?></td>
              <td><?= htmlspecialchars($job['title']) ?></td>
              <td><?= htmlspecialchars($job['company']) ?></td>
              <td><?= htmlspecialchars($job['location']) ?></td>
              <td><?= date('M d, Y', strtotime($job['posted_date'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>

<script>
// Load theme
const savedTheme = localStorage.getItem('theme') || 'dark';
document.body.classList.add(savedTheme + '-theme');

// Tab switching
function showTab(tabName) {
  // Hide all tabs
  document.querySelectorAll('.tab-content').forEach(tab => {
    tab.classList.remove('active');
  });
  document.querySelectorAll('.admin-tab').forEach(btn => {
    btn.classList.remove('active');
  });
  
  // Show selected tab
  document.getElementById(tabName + '-tab').classList.add('active');
  event.target.classList.add('active');
  
  // Load data if needed
  if (tabName === 'users') loadUsers();
  if (tabName === 'jobs') loadJobs();
  if (tabName === 'applications') loadApplications();
}

// Load all users
async function loadUsers() {
  const container = document.getElementById('users-data');
  container.innerHTML = 'Loading...';
  
  try {
    const res = await fetch('../api/admin_users.php');
    const data = await res.json();
    
    if (!data.users || data.users.length === 0) {
      container.innerHTML = '<p style="padding: 20px; text-align: center;">No users found</p>';
      return;
    }
    
    container.innerHTML = `
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          ${data.users.map(user => `
            <tr>
              <td>${user.id}</td>
              <td>${user.name || 'N/A'}</td>
              <td>${user.email}</td>
              <td><span style="padding: 4px 8px; background: ${user.role === 'employer' ? '#fef3c7' : '#dbeafe'}; color: ${user.role === 'employer' ? '#92400e' : '#1e40af'}; border-radius: 4px; font-size: 0.85rem;">${user.role}</span></td>
              <td>${new Date(user.created_at).toLocaleDateString()}</td>
              <td>
                <button class="action-btn btn-view" onclick="viewUser(${user.id})">View</button>
                <button class="action-btn btn-delete" onclick="deleteUser(${user.id})">Delete</button>
              </td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    `;
  } catch (error) {
    container.innerHTML = '<p style="padding: 20px; color: red;">Error loading users</p>';
  }
}

// Load all jobs
async function loadJobs() {
  const container = document.getElementById('jobs-data');
  container.innerHTML = 'Loading...';
  
  try {
    const res = await fetch('../api/admin_jobs.php');
    const data = await res.json();
    
    if (!data.jobs || data.jobs.length === 0) {
      container.innerHTML = '<p style="padding: 20px; text-align: center;">No jobs found</p>';
      return;
    }
    
    container.innerHTML = `
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Company</th>
            <th>Location</th>
            <th>Type</th>
            <th>Posted</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          ${data.jobs.map(job => `
            <tr>
              <td>${job.id}</td>
              <td>${job.title}</td>
              <td>${job.company}</td>
              <td>${job.location}</td>
              <td>${job.job_type || 'N/A'}</td>
              <td>${new Date(job.posted_date).toLocaleDateString()}</td>
              <td>
                <button class="action-btn btn-view" onclick="window.location.href='jobs.php?id=${job.id}'">View</button>
                <button class="action-btn btn-delete" onclick="deleteJob(${job.id})">Delete</button>
              </td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    `;
  } catch (error) {
    container.innerHTML = '<p style="padding: 20px; color: red;">Error loading jobs</p>';
  }
}

// Load all applications
async function loadApplications() {
  const container = document.getElementById('applications-data');
  container.innerHTML = 'Loading...';
  
  try {
    const res = await fetch('../api/admin_applications.php');
    const data = await res.json();
    
    if (!data.applications || data.applications.length === 0) {
      container.innerHTML = '<p style="padding: 20px; text-align: center;">No applications found</p>';
      return;
    }
    
    container.innerHTML = `
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Applicant</th>
            <th>Job Title</th>
            <th>Company</th>
            <th>Applied</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          ${data.applications.map(app => `
            <tr>
              <td>${app.id}</td>
              <td>${app.applicant_name || 'N/A'}</td>
              <td>${app.job_title || 'N/A'}</td>
              <td>${app.company || 'N/A'}</td>
              <td>${new Date(app.applied_at).toLocaleDateString()}</td>
              <td><span style="padding: 4px 8px; background: #d1fae5; color: #065f46; border-radius: 4px; font-size: 0.85rem;">${app.status || 'Pending'}</span></td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    `;
  } catch (error) {
    container.innerHTML = '<p style="padding: 20px; color: red;">Error loading applications</p>';
  }
}

function viewUser(id) {
  alert('View user details for ID: ' + id);
}

function deleteUser(id) {
  if (confirm('Are you sure you want to delete this user?')) {
    // Implement delete functionality
    alert('Delete user ID: ' + id);
  }
}

function deleteJob(id) {
  if (confirm('Are you sure you want to delete this job?')) {
    // Implement delete functionality
    alert('Delete job ID: ' + id);
  }
}

// Load initial data
loadUsers();
</script>
</body>
</html>
