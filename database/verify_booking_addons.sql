-- SQL Verification Script for Booking Addons
-- Run these queries to verify that the booking addons are properly stored and can be retrieved

-- ============================================
-- 1. Check booking_addons pivot table structure
-- ============================================
DESCRIBE booking_addons;

-- Expected output:
-- booking_id | bigint(20) unsigned | NO  | PRI | NULL |
-- addon_id   | bigint(20) unsigned | NO  | PRI | NULL |
-- quantity   | int(11)             | NO  |     | 1    |
-- price      | decimal(10,2)       | NO  |     | NULL |


-- ============================================
-- 2. View all bookings with their addons (JOIN query)
-- ============================================
SELECT 
    b.booking_id,
    b.booking_reference,
    CONCAT(b.client_first_name, ' ', b.client_last_name) AS client_name,
    b.booking_date,
    b.status,
    b.total_amount,
    GROUP_CONCAT(
        CONCAT(a.addon_name, ' (Qty: ', ba.quantity, ', Price: ₱', ba.price, ')')
        SEPARATOR ' | '
    ) AS addons
FROM bookings b
LEFT JOIN booking_addons ba ON b.booking_id = ba.booking_id
LEFT JOIN addons a ON ba.addon_id = a.addon_id
GROUP BY b.booking_id
ORDER BY b.booking_id DESC
LIMIT 20;


-- ============================================
-- 3. Count bookings with and without addons
-- ============================================
SELECT 
    'Total Bookings' AS category,
    COUNT(*) AS count
FROM bookings
UNION ALL
SELECT 
    'Bookings with Addons',
    COUNT(DISTINCT ba.booking_id)
FROM booking_addons ba
UNION ALL
SELECT 
    'Bookings without Addons',
    COUNT(*)
FROM bookings b
LEFT JOIN booking_addons ba ON b.booking_id = ba.booking_id
WHERE ba.booking_id IS NULL;


-- ============================================
-- 4. View specific booking with detailed addon info
-- ============================================
-- Replace [BOOKING_ID] with the actual booking ID you want to check
SELECT 
    b.booking_id,
    b.booking_reference,
    b.client_first_name,
    b.client_last_name,
    b.booking_date,
    b.total_amount,
    a.addon_id,
    a.addon_name,
    a.addon_price AS current_addon_price,
    ba.quantity,
    ba.price AS price_at_booking,
    (ba.quantity * ba.price) AS addon_subtotal
FROM bookings b
INNER JOIN booking_addons ba ON b.booking_id = ba.booking_id
INNER JOIN addons a ON ba.addon_id = a.addon_id
WHERE b.booking_id = [BOOKING_ID];


-- ============================================
-- 5. Check for data in the deprecated addons JSON column
-- ============================================
-- This shows bookings that have data in the old JSON column
-- (These should be migrated to use only the pivot table)
SELECT 
    booking_id,
    booking_reference,
    addons AS json_column_data,
    (SELECT COUNT(*) FROM booking_addons ba WHERE ba.booking_id = b.booking_id) AS pivot_table_count
FROM bookings b
WHERE addons IS NOT NULL 
  AND addons != 'null'
  AND addons != '[]'
ORDER BY booking_id DESC;


-- ============================================
-- 6. Most popular addons
-- ============================================
SELECT 
    a.addon_name,
    COUNT(ba.booking_id) AS times_booked,
    SUM(ba.quantity) AS total_quantity,
    SUM(ba.quantity * ba.price) AS total_revenue
FROM addons a
INNER JOIN booking_addons ba ON a.addon_id = ba.addon_id
GROUP BY a.addon_id, a.addon_name
ORDER BY times_booked DESC;


-- ============================================
-- 7. Recent bookings with addon details
-- ============================================
SELECT 
    b.booking_id,
    b.booking_reference,
    b.booking_date,
    b.created_at,
    b.total_amount,
    COUNT(ba.addon_id) AS addon_count,
    SUM(ba.quantity * ba.price) AS addons_total
FROM bookings b
LEFT JOIN booking_addons ba ON b.booking_id = ba.booking_id
WHERE b.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY b.booking_id
ORDER BY b.created_at DESC;


-- ============================================
-- 8. Verify referential integrity
-- ============================================
-- Check for orphaned records in booking_addons
SELECT 
    'Orphaned booking_addons (booking deleted)' AS issue,
    COUNT(*) AS count
FROM booking_addons ba
LEFT JOIN bookings b ON ba.booking_id = b.booking_id
WHERE b.booking_id IS NULL
UNION ALL
SELECT 
    'Orphaned booking_addons (addon deleted)',
    COUNT(*)
FROM booking_addons ba
LEFT JOIN addons a ON ba.addon_id = a.addon_id
WHERE a.addon_id IS NULL;


-- ============================================
-- 9. Sample insert for testing (OPTIONAL)
-- ============================================
-- Uncomment and modify if you need to test manually adding addon to a booking
/*
-- First, get a valid booking_id and addon_id
SELECT booking_id FROM bookings ORDER BY booking_id DESC LIMIT 1;
SELECT addon_id, addon_name, addon_price FROM addons WHERE is_active = 1;

-- Then insert into booking_addons
INSERT INTO booking_addons (booking_id, addon_id, quantity, price)
VALUES (
    [BOOKING_ID],  -- Replace with actual booking_id
    [ADDON_ID],    -- Replace with actual addon_id
    1,             -- Quantity
    [PRICE]        -- Replace with addon price
);
*/


-- ============================================
-- 10. Cleanup script (USE WITH CAUTION)
-- ============================================
-- This would remove the redundant JSON column (DO NOT RUN YET)
-- Only run this after confirming all data is in the pivot table
/*
ALTER TABLE bookings DROP COLUMN addons;
*/
