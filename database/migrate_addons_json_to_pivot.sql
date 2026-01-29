-- ================================================
-- Quick Fix: Migrate Addons from JSON to Pivot Table
-- ================================================
-- This script migrates addon data from the bookings.addons JSON column
-- to the booking_addons pivot table for proper relationship handling
--
-- Run this ONCE to fix existing bookings created from the public system
-- ================================================

-- Step 1: Fix Booking #86 specifically (from the screenshot)
-- Based on the JSON data in the dump file
INSERT INTO booking_addons (booking_id, addon_id, quantity, price) 
VALUES
    (86, 1, 1, 149.00),  -- Additional Person
    (86, 2, 1, 49.00),   -- 4R Photo Print
    (86, 4, 1, 99.00),   -- Lighting Effect
    (86, 5, 1, 49.00),   -- 32in number Balloon
    (86, 6, 1, 99.00),   -- 5mins Extension
    (86, 7, 1, 149.00),  -- Charge for Pets
    (86, 8, 1, 100.00)   -- A4 Photo Print
ON DUPLICATE KEY UPDATE 
    quantity = VALUES(quantity),
    price = VALUES(price);


-- Step 2: Verify the fix for booking 86
SELECT 
    b.booking_id,
    b.booking_reference,
    b.client_first_name,
    b.client_last_name,
    GROUP_CONCAT(
        CONCAT(a.addon_name, ' (₱', ba.price, ' x', ba.quantity, ')')
        SEPARATOR ', '
    ) AS addons_from_pivot
FROM bookings b
LEFT JOIN booking_addons ba ON b.booking_id = ba.booking_id
LEFT JOIN addons a ON ba.addon_id = a.addon_id
WHERE b.booking_id = 86
GROUP BY b.booking_id;


-- Step 3: Check for OTHER bookings with JSON addons but no pivot entries
-- This identifies all bookings that need migration
SELECT 
    b.booking_id,
    b.booking_reference,
    b.booking_date,
    CASE 
        WHEN b.addons IS NOT NULL AND b.addons != 'null' AND b.addons != '[]' THEN 'Has JSON data'
        ELSE 'No JSON data'
    END AS json_status,
    COUNT(ba.addon_id) AS pivot_count,
    CASE 
        WHEN b.addons IS NOT NULL AND b.addons != 'null' AND b.addons != '[]' 
             AND COUNT(ba.addon_id) = 0 THEN '⚠️ NEEDS MIGRATION'
        WHEN COUNT(ba.addon_id) > 0 THEN '✅ OK'
        ELSE 'No addons'
    END AS status
FROM bookings b
LEFT JOIN booking_addons ba ON b.booking_id = ba.booking_id
GROUP BY b.booking_id
HAVING json_status = 'Has JSON data' AND pivot_count = 0
ORDER BY b.booking_id DESC;


-- Step 4: View the JSON data for bookings that need migration
-- This shows you what addons need to be migrated
SELECT 
    booking_id,
    booking_reference,
    addons AS json_data
FROM bookings
WHERE addons IS NOT NULL 
  AND addons != 'null' 
  AND addons != '[]'
  AND booking_id NOT IN (SELECT DISTINCT booking_id FROM booking_addons)
ORDER BY booking_id DESC;


-- Step 5: MANUAL MIGRATION TEMPLATE
-- For each booking identified in Step 4, decode the JSON and insert into pivot table
-- Template:
/*
INSERT INTO booking_addons (booking_id, addon_id, quantity, price) 
VALUES
    ([BOOKING_ID], [ADDON_ID], [QUANTITY], [PRICE])
ON DUPLICATE KEY UPDATE 
    quantity = VALUES(quantity),
    price = VALUES(price);
*/


-- Step 6: After migration, optionally clear the JSON column
-- (Run this ONLY after confirming all data is in pivot table)
/*
UPDATE bookings 
SET addons = NULL 
WHERE booking_id IN (SELECT DISTINCT booking_id FROM booking_addons)
  AND addons IS NOT NULL;
*/


-- Step 7: Final verification - ensure all bookings show addons correctly
SELECT 
    b.booking_id,
    b.booking_reference,
    b.booking_date,
    b.total_amount,
    COUNT(ba.addon_id) AS addon_count,
    GROUP_CONCAT(a.addon_name SEPARATOR ', ') AS addon_names,
    SUM(ba.quantity * ba.price) AS addons_total
FROM bookings b
LEFT JOIN booking_addons ba ON b.booking_id = ba.booking_id
LEFT JOIN addons a ON ba.addon_id = a.addon_id
WHERE b.booking_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY b.booking_id
ORDER BY b.booking_id DESC
LIMIT 20;


-- ================================================
-- IMPORTANT NOTES:
-- ================================================
-- 1. This script fixes EXISTING bookings with JSON addon data
-- 2. You MUST also fix the client-side code to prevent future issues
-- 3. See PUBLIC_BOOKING_ADDON_FIX.md for code changes needed
-- 4. After fixing code + migrating data, consider dropping the addons column
-- ================================================
