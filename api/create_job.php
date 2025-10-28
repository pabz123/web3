<?php
// api/create_job.php
require_once __DIR__ . '/../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'employer') {
    $title = $_POST['title'] ?? '';
    $company = $_POST['company'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $requirements = $_POST['requirements'] ?? '';
    $employer_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO jobs (employer_id, title, company, location, description, requirements) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("isssss", $employer_id, $title, $company, $location, $description, $requirements);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Job created successfully']);
    exit;
}
echo json_encode(['success' => false, 'message' => 'Unauthorized or invalid request']);
?>
