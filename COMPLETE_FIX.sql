-- ============================================
-- COMPLETE FIX - Run This in phpMyAdmin
-- ============================================
-- This fixes all database issues for employer profiles
-- Copy the ENTIRE file and paste in phpMyAdmin SQL tab
-- ============================================

-- Step 1: Rename old employers table to keep data safe
RENAME TABLE employers TO employers_old;

-- Step 2: Create new employers table linked to users
CREATE TABLE employers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    company_name VARCHAR(255),
    company_logo VARCHAR(255),
    company_description TEXT,
    company_website VARCHAR(255),
    company_address TEXT,
    company_size VARCHAR(50),
    industry VARCHAR(100),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_industry (industry)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Create user accounts for all employers from old table
INSERT INTO users (name, email, password, role, createdAt, updatedAt, profile_image)
SELECT 
    COALESCE(contact_person, company_name),
    email,
    password,
    'employer',
    createdAt,
    updatedAt,
    '/uploads/profile/default-avatar.png'
FROM employers_old
WHERE email NOT IN (SELECT email FROM users);

-- Step 4: Migrate company data to new employers table
INSERT INTO employers (user_id, company_name, company_logo, company_description, company_website, company_address, industry, createdAt, updatedAt)
SELECT 
    u.id,
    e.company_name,
    CONCAT('/uploads/company_logos/', IFNULL(e.logo, 'default-company.png')),
    e.description,
    e.website_url,
    e.location,
    e.industry,
    e.createdAt,
    e.updatedAt
FROM employers_old e
INNER JOIN users u ON e.email = u.email
WHERE u.role = 'employer';

-- Step 5: Update jobs to reference new user IDs (NO TEMP TABLE!)
UPDATE jobs j
INNER JOIN employers_old eo ON j.employer_id = eo.id
INNER JOIN users u ON eo.email = u.email
SET j.employer_id = u.id
WHERE u.role = 'employer';

-- Step 6: Drop old foreign key constraint
ALTER TABLE jobs DROP FOREIGN KEY IF EXISTS Jobs_employer_id_foreign_idx;

-- Step 7: Add new foreign key constraint
ALTER TABLE jobs 
ADD CONSTRAINT fk_jobs_employer 
FOREIGN KEY (employer_id) REFERENCES users(id) 
ON DELETE CASCADE;

-- Step 8: Create uploads directory structure (you'll need to do this via FTP)
-- Create these folders:
-- /uploads/company_logos/
-- Set permissions to 755

-- ============================================
-- VERIFICATION
-- ============================================

-- Check employers created
SELECT 
    'Employers Created' as status,
    COUNT(*) as count
FROM employers;

-- Check jobs updated
SELECT 
    'Jobs Updated' as status,
    COUNT(*) as total_jobs,
    COUNT(DISTINCT employer_id) as unique_employers
FROM jobs;

-- View sample data
SELECT 
    u.id as user_id,
    u.name as contact_person,
    u.email,
    e.company_name,
    e.industry,
    COUNT(j.id) as job_count
FROM users u
INNER JOIN employers e ON u.id = e.user_id
LEFT JOIN jobs j ON j.employer_id = u.id
WHERE u.role = 'employer'
GROUP BY u.id, u.name, u.email, e.company_name, e.industry
LIMIT 10;

-- ============================================
-- SUCCESS!
-- ============================================
-- If you see data in the queries above, migration is complete!
-- 
-- Next steps:
-- 1. Login as any employer (use their email from employers_old)
-- 2. Go to pages/employer-profile.php
-- 3. Update company details
-- 4. Upload logo
-- 
-- Your 10 employers can now login:
-- - hr@techinnovations.co.ug
-- - jobs@brightmarketing.co.ug
-- - careers@datasense.co.ug
-- - info@supportplus.co.ug
-- - recruit@hrconnect.co.ug
-- - jobs@creativeworks.co.ug
-- - careers@aid4all.org
-- - apply@financecare.co.ug
-- - sales@marketreach.co.ug
-- - security@cyberguard.co.ug
-- ============================================
