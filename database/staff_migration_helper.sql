-- Staff Users Table - Data Migration Helper Script
-- Run this AFTER the migration has added the username column

-- ============================================
-- 1. Check current state of staff_users table
-- ============================================

SELECT 'Current staff_users structure:' as Info;
DESCRIBE staff_users;

SELECT 'Current staff records:' as Info;
SELECT 
    staff_id,
    username,
    first_name,
    last_name,
    email,
    phone,
    status,
    created_at
FROM staff_users;

-- ============================================
-- 2. Generate usernames for existing staff (if needed)
-- ============================================

-- Check for staff without usernames
SELECT 
    staff_id,
    first_name,
    last_name,
    email,
    username
FROM staff_users
WHERE username IS NULL OR username = '';

-- Option 1: Generate usernames from first_name and last_name
-- Format: firstname_lastname (lowercase)
UPDATE staff_users 
SET username = CONCAT(
    LOWER(REPLACE(first_name, ' ', '_')), 
    '_', 
    LOWER(REPLACE(last_name, ' ', '_'))
)
WHERE username IS NULL OR username = '';

-- Option 2: Generate usernames from email (before @ symbol)
-- UPDATE staff_users 
-- SET username = SUBSTRING_INDEX(email, '@', 1)
-- WHERE username IS NULL OR username = '';

-- ============================================
-- 3. Handle duplicate usernames
-- ============================================

-- Check for duplicates
SELECT 
    username, 
    COUNT(*) as count,
    GROUP_CONCAT(staff_id) as staff_ids
FROM staff_users 
GROUP BY username 
HAVING count > 1;

-- Add numbers to duplicates (run if duplicates found)
-- This creates username, username2, username3, etc.
SET @row_number = 0;
SET @current_username = '';

UPDATE staff_users s
JOIN (
    SELECT 
        staff_id,
        username,
        IF(@current_username = username, @row_number := @row_number + 1, @row_number := 1) as row_num,
        @current_username := username as dummy
    FROM staff_users
    ORDER BY username, staff_id
) numbered ON s.staff_id = numbered.staff_id
SET s.username = IF(
    numbered.row_num > 1,
    CONCAT(numbered.username, numbered.row_num),
    numbered.username
)
WHERE numbered.row_num > 1;

-- ============================================
-- 4. Verify final state
-- ============================================

-- Check for NULL or empty usernames
SELECT 'Staff with NULL or empty usernames:' as Info;
SELECT COUNT(*) as count
FROM staff_users
WHERE username IS NULL OR username = '';

-- Check username uniqueness
SELECT 'Duplicate usernames check:' as Info;
SELECT username, COUNT(*) as count
FROM staff_users
GROUP BY username
HAVING count > 1;

-- Verify all staff have valid usernames
SELECT 'All staff with usernames:' as Info;
SELECT 
    staff_id,
    username,
    CONCAT(first_name, ' ', last_name) as full_name,
    email,
    status
FROM staff_users
ORDER BY created_at DESC;

-- ============================================
-- 5. Sample staff insertion (for testing)
-- ============================================

-- Insert a test staff member
-- Uncomment and modify as needed

/*
INSERT INTO staff_users (
    username,
    first_name,
    last_name,
    email,
    phone,
    password_hash,
    status,
    admin_id,
    password_changed_at,
    force_password_change,
    created_at,
    updated_at
) VALUES (
    'test_staff',
    'Test',
    'Staff',
    'test.staff@example.com',
    '09123456789',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: "password"
    'active',
    1, -- Replace with actual admin_id
    NOW(),
    0,
    NOW(),
    NOW()
);

-- Get the staff_id of the newly created staff
SELECT LAST_INSERT_ID() as new_staff_id;
*/

-- ============================================
-- 6. Clean up inactive/locked accounts (optional)
-- ============================================

-- View inactive staff
SELECT 
    staff_id,
    username,
    CONCAT(first_name, ' ', last_name) as full_name,
    status,
    failed_login_attempts,
    locked_until,
    last_login_at
FROM staff_users
WHERE status = 'inactive' OR locked_until IS NOT NULL;

-- Unlock all locked accounts (if needed)
-- UPDATE staff_users 
-- SET 
--     locked_until = NULL,
--     failed_login_attempts = 0
-- WHERE locked_until IS NOT NULL;

-- ============================================
-- 7. Password reset for specific staff (if needed)
-- ============================================

-- Reset password to "password" for testing
-- UPDATE staff_users 
-- SET 
--     password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
--     force_password_change = 1,
--     password_changed_at = NOW(),
--     failed_login_attempts = 0,
--     locked_until = NULL
-- WHERE username = 'specific_username';

-- ============================================
-- 8. Verify staff_roles relationship
-- ============================================

-- Check staff roles assignments
SELECT 
    s.staff_id,
    s.username,
    CONCAT(s.first_name, ' ', s.last_name) as full_name,
    GROUP_CONCAT(r.role_name) as roles
FROM staff_users s
LEFT JOIN staff_roles sr ON s.staff_id = sr.staff_id
LEFT JOIN roles r ON sr.role_id = r.role_id
GROUP BY s.staff_id
ORDER BY s.created_at DESC;

-- Staff without roles
SELECT 
    s.staff_id,
    s.username,
    CONCAT(s.first_name, ' ', s.last_name) as full_name,
    s.email
FROM staff_users s
LEFT JOIN staff_roles sr ON s.staff_id = sr.staff_id
WHERE sr.role_id IS NULL;

-- ============================================
-- 9. Security audit
-- ============================================

-- Staff with high failed login attempts
SELECT 
    staff_id,
    username,
    CONCAT(first_name, ' ', last_name) as full_name,
    failed_login_attempts,
    locked_until,
    last_login_at,
    last_login_ip
FROM staff_users
WHERE failed_login_attempts >= 3
ORDER BY failed_login_attempts DESC;

-- Staff that never logged in
SELECT 
    staff_id,
    username,
    CONCAT(first_name, ' ', last_name) as full_name,
    email,
    created_at
FROM staff_users
WHERE last_login_at IS NULL
ORDER BY created_at DESC;

-- ============================================
-- NOTES:
-- ============================================
-- 1. Always backup your database before running UPDATE or DELETE queries
-- 2. Test queries on a development database first
-- 3. Usernames must be unique and follow pattern: [a-zA-Z0-9_]
-- 4. Default password hash is for "password" - change in production
-- 5. Make sure to run migrations before this script:
--    php artisan migrate
