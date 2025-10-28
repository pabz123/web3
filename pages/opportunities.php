<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

// Fetch all jobs from database
$stmt = $conn->prepare("SELECT id, title, company, location, job_type, salary_range, posted_date FROM jobs ORDER BY posted_date DESC LIMIT 50");
$stmt->execute();
$result = $stmt->get_result();
$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Career Opportunities | Career Connect Hub</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <style>
        .opportunities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .opportunity-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .opportunity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .job-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 8px;
        }
        .badge-fulltime { background: #dbeafe; color: #1e40af; }
        .badge-parttime { background: #fef3c7; color: #92400e; }
        .badge-contract { background: #e0e7ff; color: #4338ca; }
        .badge-remote { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
  <?php include_once __DIR__ . '/../includes/navbar.php'; ?>

    <main class="page-content-wrapper" style="padding: 40px 20px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <button onclick="window.history.back()" class="btn btn-secondary" style="margin-bottom: 20px;">
              ← Back
            </button>
            
            <h1 style="font-size: 2.5rem; margin-bottom: 10px;">💼 Career Opportunities</h1>
            <p style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 30px;">Explore all available job openings and career advancement opportunities</p>
            <?php if (empty($jobs)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <div style="font-size: 4rem; margin-bottom: 20px;">💼</div>
                    <h2>No Opportunities Available</h2>
                    <p style="color: var(--text-secondary);">Check back soon for new job postings!</p>
                </div>
            <?php else: ?>
                <div class="opportunities-grid">
                    <?php foreach ($jobs as $job): ?>
                        <div class="opportunity-card" onclick="window.location.href='jobs.php?id=<?= $job['id'] ?>'">
                            <div style="margin-bottom: 15px;">
                                <?php 
                                $jobType = strtolower($job['job_type'] ?? 'full-time');
                                $badgeClass = 'badge-fulltime';
                                if (strpos($jobType, 'part') !== false) $badgeClass = 'badge-parttime';
                                if (strpos($jobType, 'contract') !== false) $badgeClass = 'badge-contract';
                                if (strpos($jobType, 'remote') !== false) $badgeClass = 'badge-remote';
                                ?>
                                <span class="job-badge <?= $badgeClass ?>"><?= htmlspecialchars($job['job_type'] ?? 'Full-time') ?></span>
                            </div>
                            
                            <h3 style="font-size: 1.4rem; margin-bottom: 10px; color: var(--text-primary);">
                                <?= htmlspecialchars($job['title']) ?>
                            </h3>
                            
                            <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; color: var(--linkedin-blue);">
                                <?= htmlspecialchars($job['company'] ?? 'Company') ?>
                            </p>
                            
                            <p style="color: var(--text-secondary); margin-bottom: 10px;">
                                📍 <?= htmlspecialchars($job['location'] ?? 'Location') ?>
                            </p>
                            
                            <?php if (!empty($job['salary_range'])): ?>
                                <p style="color: var(--text-secondary); margin-bottom: 10px;">
                                    💰 <?= htmlspecialchars($job['salary_range']) ?>
                                </p>
                            <?php endif; ?>
                            
                            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 15px;">
                                Posted: <?= date('M d, Y', strtotime($job['posted_date'] ?? 'now')) ?>
                            </p>
                            
                            <button class="btn btn-primary" style="width: 100%; margin-top: 15px;" onclick="event.stopPropagation(); window.location.href='apply.php?jobId=<?= $job['id'] ?>'">
                                Apply Now
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>

<script>
// Load theme
const savedTheme = localStorage.getItem('theme') || 'dark';
document.body.classList.add(savedTheme + '-theme');
</script>
</body>
</html>