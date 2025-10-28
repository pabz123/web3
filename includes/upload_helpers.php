<?php
/**
 * upload_helpers.php
 * ------------------
 * Comprehensive profile and file upload handler
 * Ensures persistent storage across users, students, and student_profiles tables
 * Properly handles profile images and CV uploads with validation
 */

session_start();
require_once __DIR__ . '/db.php'; // mysqli connection ($conn)

// Ensure upload directories exist with proper permissions
$uploadDirs = [
    __DIR__ . '/../uploads/profile',
    __DIR__ . '/../uploads/cv'
];

foreach ($uploadDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

/**
 * Validate and upload file
 * @param array $file - $_FILES array element
 * @param string $type - 'profile' or 'cv'
 * @param int $userId - User ID for filename
 * @return string|null - File path on success, null on failure
 */
function uploadFile($file, $type, $userId) {
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedTypes = [
        'profile' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'cv' => ['pdf', 'doc', 'docx']
    ];

    $maxSizes = [
        'profile' => 5 * 1024 * 1024, // 5MB
        'cv' => 10 * 1024 * 1024 // 10MB
    ];

    // Validate file size
    if ($file['size'] > $maxSizes[$type]) {
        return null;
    }

    // Validate file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes[$type])) {
        return null;
    }

    // Generate secure filename
    $filename = $type . '-' . $userId . '-' . time() . '.' . $ext;
    $targetPath = __DIR__ . '/../uploads/' . $type . '/' . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return '/uploads/' . $type . '/' . $filename;
    }

    return null;
}

/**
 * Handle complete profile update with persistent storage
 * @param int $userId - User ID
 * @return array - Result with success or error message
 */
function handleProfileUpdate($userId) {
    global $conn;
    
    // Sanitize and validate inputs
    $fullName = trim($_POST['fullName'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    
    if (!$fullName || !$email) {
        return ['error' => 'Name and email are required'];
    }

    // Get current profile data
    $currentProfileImage = $_SESSION['user']['profile_image'] ?? '/uploads/profile/default-avatar.png';
    $currentCvFile = $_SESSION['user']['cv'] ?? null;

    // Handle profile picture upload
    if (!empty($_FILES['profilePic']['tmp_name'])) {
        $uploadedImage = uploadFile($_FILES['profilePic'], 'profile', $userId);
        if ($uploadedImage) {
            $currentProfileImage = $uploadedImage;
        } else {
            return ['error' => 'Invalid profile image. Use JPG, PNG, or GIF (max 5MB)'];
        }
    }

    // Handle CV upload
    if (!empty($_FILES['cvFile']['tmp_name'])) {
        $uploadedCv = uploadFile($_FILES['cvFile'], 'cv', $userId);
        if ($uploadedCv) {
            $currentCvFile = $uploadedCv;
        } else {
            return ['error' => 'Invalid CV file. Use PDF, DOC, or DOCX (max 10MB)'];
        }
    }

    // Start transaction for atomic updates
    $conn->begin_transaction();
    
    try {
        // 1. Update users table (primary authentication table)
        $stmt1 = $conn->prepare(
            "UPDATE users SET name = ?, email = ?, phone = ?, profile_image = ?, updatedAt = NOW() WHERE id = ?"
        );
        $stmt1->bind_param('ssssi', $fullName, $email, $phone, $currentProfileImage, $userId);
        $stmt1->execute();
        $stmt1->close();

        // 2. Update or insert into students table
        $checkStudent = $conn->prepare("SELECT id FROM students WHERE id = ?");
        $checkStudent->bind_param('i', $userId);
        $checkStudent->execute();
        $studentExists = $checkStudent->get_result()->num_rows > 0;
        $checkStudent->close();

        if ($studentExists) {
            $stmt2 = $conn->prepare(
                "UPDATE students SET name = ?, full_name = ?, email = ?, contact = ?, skills = ?, profile_image = ?, profilePic = ?, cv_file = ?, updatedAt = NOW() WHERE id = ?"
            );
            $stmt2->bind_param('ssssssssi', $fullName, $fullName, $email, $phone, $skills, $currentProfileImage, $currentProfileImage, $currentCvFile, $userId);
            $stmt2->execute();
            $stmt2->close();
        }

        // 3. Update or insert into student_profiles table
        $checkProfile = $conn->prepare("SELECT id FROM student_profiles WHERE email = ?");
        $checkProfile->bind_param('s', $email);
        $checkProfile->execute();
        $profileExists = $checkProfile->get_result()->num_rows > 0;
        $checkProfile->close();

        if ($profileExists) {
            $stmt3 = $conn->prepare(
                "UPDATE student_profiles SET fullName = ?, phone = ?, education = ?, skills = ?, profilePic = ?, cvFile = ? WHERE email = ?"
            );
            $stmt3->bind_param('sssssss', $fullName, $phone, $education, $skills, $currentProfileImage, $currentCvFile, $email);
        } else {
            $stmt3 = $conn->prepare(
                "INSERT INTO student_profiles (email, fullName, phone, education, skills, profilePic, cvFile) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt3->bind_param('sssssss', $email, $fullName, $phone, $education, $skills, $currentProfileImage, $currentCvFile);
        }
        $stmt3->execute();
        $stmt3->close();

        // Commit transaction
        $conn->commit();

        // Update session with fresh data
        $_SESSION['user'] = array_merge($_SESSION['user'], [
            'name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'profile_image' => $currentProfileImage,
            'cv' => $currentCvFile,
            'education' => $education,
            'skills' => $skills
        ]);

        return ['success' => true, 'message' => 'Profile updated successfully!'];
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Profile update error: " . $e->getMessage());
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}

// ============================================
// MAIN EXECUTION
// ============================================

// Require logged-in user
$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(['error' => 'Unauthorized. Please log in.']);
    } else {
        header('Location: ../pages/login.php');
    }
    exit;
}

// Handle POST request for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = handleProfileUpdate($userId);
    
    // Return JSON for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    
    // Redirect for form submissions
    if (isset($result['error'])) {
        $_SESSION['profile_error'] = $result['error'];
        header('Location: ../pages/student-profile.php?error=1');
    } else {
        $_SESSION['profile_success'] = $result['message'];
        header('Location: ../pages/student-profile.php?updated=1');
    }
    exit;
}
?>
