-- SQL Script to Add Usernames to Existing Staff Accounts
-- Run this script if you have existing staff accounts without usernames

-- Example 1: Generate usernames from first name and last name
UPDATE staff_users 
SET username = CONCAT(LOWER(first_name), '_', LOWER(last_name))
WHERE username IS NULL OR username = '';

-- Example 2: Generate usernames with staff ID suffix for uniqueness
UPDATE staff_users 
SET username = CONCAT(LOWER(first_name), '_', LOWER(last_name), '_', staff_id)
WHERE username IS NULL OR username = '';

-- Example 3: Manual assignment for specific staff members
-- UPDATE staff_users SET username = 'john_doe' WHERE staff_id = 1;
-- UPDATE staff_users SET username = 'jane_smith' WHERE staff_id = 2;

-- Verify usernames are set
SELECT staff_id, username, first_name, last_name, email, status 
FROM staff_users 
ORDER BY staff_id;

-- Check for any duplicate usernames (should return empty)
SELECT username, COUNT(*) as count 
FROM staff_users 
GROUP BY username 
HAVING count > 1;

-- Check for NULL or empty usernames (should return empty after update)
SELECT staff_id, first_name, last_name, email 
FROM staff_users 
WHERE username IS NULL OR username = '';
