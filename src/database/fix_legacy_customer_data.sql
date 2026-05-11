-- Fix Legacy Customer Data - Re-link Orders to Correct Customer Records
-- This migration corrects historical data issues where orders are linked to wrong customer records

USE order_management;

-- ============================================================================
-- STEP 1: Identify and display problematic customer records BEFORE fixing
-- ============================================================================
SELECT 
    'BEFORE FIX - Problematic Customer Records' as status,
    c.id as customer_id,
    c.name as customer_name,
    c.email as customer_email,
    c.user_id,
    u.name as linked_user_name,
    u.email as linked_user_email,
    COUNT(o.id) as order_count,
    CASE 
        WHEN c.email != u.email THEN 'EMAIL_MISMATCH'
        WHEN c.user_id IS NULL THEN 'NO_USER_LINK'
        ELSE 'OK'
    END as issue_type
FROM customers c
LEFT JOIN users u ON c.user_id = u.id
LEFT JOIN orders o ON o.customer_id = c.id
GROUP BY c.id, c.name, c.email, c.user_id, u.name, u.email
HAVING issue_type != 'OK'
ORDER BY c.name, c.id;

-- ============================================================================
-- STEP 2: Create temporary table to map incorrect customer_id to correct customer_id
-- ============================================================================
CREATE TEMPORARY TABLE IF NOT EXISTS customer_mapping (
    old_customer_id INT UNSIGNED NOT NULL,
    new_customer_id INT UNSIGNED NOT NULL,
    reason VARCHAR(255),
    PRIMARY KEY (old_customer_id)
);

-- ============================================================================
-- STEP 3: Find correct customer mappings
-- Strategy: For each order, find the correct customer based on order.user_id
-- ============================================================================

-- Case A: Orders linked to customers with wrong email
-- Find the correct customer record that matches the order's user
-- Use MIN(id) to deterministically select the oldest/primary customer for each user
INSERT INTO customer_mapping (old_customer_id, new_customer_id, reason)
SELECT DISTINCT
    o.customer_id as old_customer_id,
    c_correct.id as new_customer_id,
    CONCAT('Email mismatch: ', c_wrong.email, ' -> ', c_correct.email) as reason
FROM orders o
INNER JOIN customers c_wrong ON o.customer_id = c_wrong.id
INNER JOIN users u ON o.user_id = u.id
INNER JOIN (
    SELECT user_id, MIN(id) as id
    FROM customers
    WHERE user_id IS NOT NULL
      AND email IN (SELECT email FROM users WHERE id = user_id)
      AND (is_active = 1 OR is_active IS NULL)
    GROUP BY user_id
) c_correct_min ON c_correct_min.user_id = u.id
INNER JOIN customers c_correct ON c_correct.id = c_correct_min.id
WHERE 
    -- Wrong customer has mismatched email
    c_wrong.email != u.email
    -- Correct customer exists with matching user_id
    AND c_correct.user_id = u.id
    -- Don't map to itself
    AND c_wrong.id != c_correct.id
    -- Correct customer has matching email
    AND c_correct.email = u.email
ON DUPLICATE KEY UPDATE 
    new_customer_id = VALUES(new_customer_id),
    reason = VALUES(reason);

-- Case B: Orders linked to customers with no user_id link
-- Find the correct customer record based on user email/name match
INSERT IGNORE INTO customer_mapping (old_customer_id, new_customer_id, reason)
SELECT DISTINCT
    o.customer_id as old_customer_id,
    c_correct.id as new_customer_id,
    CONCAT('No user link: linking to user_id=', u.id) as reason
FROM orders o
INNER JOIN customers c_wrong ON o.customer_id = c_wrong.id
INNER JOIN users u ON o.user_id = u.id
INNER JOIN customers c_correct ON c_correct.user_id = u.id AND c_correct.email = u.email
WHERE 
    -- Wrong customer has no user link
    c_wrong.user_id IS NULL
    -- Correct customer exists with proper user link
    AND c_correct.user_id = u.id
    -- Don't map to itself
    AND c_wrong.id != c_correct.id
    -- Names should match (case-insensitive)
    AND LOWER(TRIM(c_wrong.name)) = LOWER(TRIM(c_correct.name));

-- ============================================================================
-- STEP 4: Display the mapping plan BEFORE applying changes
-- ============================================================================
SELECT 
    'MAPPING PLAN' as status,
    cm.old_customer_id,
    c_old.name as old_customer_name,
    c_old.email as old_customer_email,
    c_old.user_id as old_user_id,
    cm.new_customer_id,
    c_new.name as new_customer_name,
    c_new.email as new_customer_email,
    c_new.user_id as new_user_id,
    COUNT(o.id) as orders_to_relink,
    cm.reason
FROM customer_mapping cm
INNER JOIN customers c_old ON cm.old_customer_id = c_old.id
INNER JOIN customers c_new ON cm.new_customer_id = c_new.id
LEFT JOIN orders o ON o.customer_id = cm.old_customer_id
GROUP BY cm.old_customer_id, cm.new_customer_id, c_old.name, c_old.email, c_old.user_id, 
         c_new.name, c_new.email, c_new.user_id, cm.reason
ORDER BY orders_to_relink DESC;

-- ============================================================================
-- STEP 5: Re-link orders from incorrect customer to correct customer
-- ============================================================================
UPDATE orders o
INNER JOIN customer_mapping cm ON o.customer_id = cm.old_customer_id
SET o.customer_id = cm.new_customer_id
WHERE o.customer_id = cm.old_customer_id;

-- ============================================================================
-- STEP 6: Mark orphaned/incorrect customer records as inactive
-- Only mark as inactive if they have NO remaining orders
-- ============================================================================
UPDATE customers c
INNER JOIN customer_mapping cm ON c.id = cm.old_customer_id
SET c.is_active = 0
WHERE NOT EXISTS (
    SELECT 1 
    FROM orders o 
    WHERE o.customer_id = c.id
);

-- ============================================================================
-- STEP 7: Verify the fix - Show results AFTER migration
-- ============================================================================
SELECT 
    'AFTER FIX - Verification' as status,
    COUNT(*) as total_customers,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_customers,
    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_customers,
    SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as linked_to_users,
    SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as unlinked_customers
FROM customers;

-- ============================================================================
-- STEP 8: Show remaining issues (should be minimal or none)
-- ============================================================================
SELECT 
    'REMAINING ISSUES' as status,
    c.id as customer_id,
    c.name as customer_name,
    c.email as customer_email,
    c.user_id,
    c.is_active,
    u.name as linked_user_name,
    u.email as linked_user_email,
    COUNT(o.id) as order_count,
    CASE 
        WHEN c.email != u.email THEN 'EMAIL_MISMATCH'
        WHEN c.user_id IS NULL AND COUNT(o.id) > 0 THEN 'NO_USER_LINK_WITH_ORDERS'
        ELSE 'UNKNOWN'
    END as issue_type
FROM customers c
LEFT JOIN users u ON c.user_id = u.id
LEFT JOIN orders o ON o.customer_id = c.id
WHERE c.is_active = 1
GROUP BY c.id, c.name, c.email, c.user_id, c.is_active, u.name, u.email
HAVING (c.email != u.email OR (c.user_id IS NULL AND COUNT(o.id) > 0))
ORDER BY order_count DESC, c.name;

-- ============================================================================
-- STEP 9: Show orders that were successfully re-linked
-- ============================================================================
SELECT 
    'SUCCESSFULLY RE-LINKED ORDERS' as status,
    cm.old_customer_id,
    c_old.name as old_customer_name,
    c_old.email as old_customer_email,
    cm.new_customer_id,
    c_new.name as new_customer_name,
    c_new.email as new_customer_email,
    COUNT(o.id) as relinked_order_count,
    cm.reason
FROM customer_mapping cm
INNER JOIN customers c_old ON cm.old_customer_id = c_old.id
INNER JOIN customers c_new ON cm.new_customer_id = c_new.id
INNER JOIN orders o ON o.customer_id = cm.new_customer_id
INNER JOIN users u ON o.user_id = u.id
WHERE u.id = c_new.user_id
GROUP BY cm.old_customer_id, cm.new_customer_id, c_old.name, c_old.email, 
         c_new.name, c_new.email, cm.reason
ORDER BY relinked_order_count DESC;

-- ============================================================================
-- STEP 10: Cleanup temporary table
-- ============================================================================
DROP TEMPORARY TABLE IF EXISTS customer_mapping;

-- ============================================================================
-- MIGRATION COMPLETE
-- ============================================================================
SELECT 'MIGRATION COMPLETE - Review the verification results above' as status;
