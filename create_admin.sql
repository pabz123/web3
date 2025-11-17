-- Create Admin Account Script
-- Run this in phpMyAdmin to create/update admin accounts

-- Option 1: Create a new admin with hashed password
-- Password: admin123 (change after first login!)
INSERT INTO `admins` (`username`, `password`, `email`, `role`, `createdAt`, `updatedAt`) 
VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@careerconnect.com', 'superadmin', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    updatedAt = NOW();

-- Option 2: Update existing admin (ID 3) with hashed password
-- Password: admin123
UPDATE `admins` 
SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    `updatedAt` = NOW()
WHERE `id` = 3;

-- Verify admin accounts
SELECT id, username, email, role, 
       CASE 
           WHEN LENGTH(password) > 20 THEN 'Hashed ✓'
           ELSE 'Plain Text ⚠️'
       END as password_status
FROM admins;
