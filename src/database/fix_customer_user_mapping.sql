-- Fix Customer-User Data Integrity
-- This migration adds user_id to customers table and fixes existing data

USE order_management;

-- Step 1: Add user_id column to customers table if it doesn't exist
ALTER TABLE customers ADD COLUMN IF NOT EXISTS user_id INT UNSIGNED NULL;

-- Step 2: Add index on user_id for performance
ALTER TABLE customers ADD INDEX IF NOT EXISTS idx_customers_user_id (user_id);

-- Step 3: Fix existing customer records by linking them to the correct user
-- Match customers to users based on email
UPDATE customers c
INNER JOIN users u ON c.email = u.email
SET c.user_id = u.id
WHERE c.user_id IS NULL;

-- Step 4: For any remaining unlinked customers, try to link via orders
-- Only link if ALL orders for this customer belong to the SAME user (unambiguous mapping)
UPDATE customers c
INNER JOIN (
    SELECT 
        o.customer_id,
        o.user_id,
        COUNT(DISTINCT o.user_id) as distinct_user_count
    FROM orders o
    WHERE o.customer_id IS NOT NULL
    GROUP BY o.customer_id
    HAVING COUNT(DISTINCT o.user_id) = 1
) single_user_orders ON c.id = single_user_orders.customer_id
SET c.user_id = single_user_orders.user_id
WHERE c.user_id IS NULL;

-- Step 5: Verify the fix
SELECT 
    'Data Integrity Check' as check_type,
    COUNT(*) as total_customers,
    SUM(CASE WHEN user_id IS NOT NULL THEN 1 ELSE 0 END) as linked_customers,
    SUM(CASE WHEN user_id IS NULL THEN 1 ELSE 0 END) as unlinked_customers
FROM customers;

-- Step 6: Show any problematic records
SELECT 
    c.id as customer_id,
    c.name as customer_name,
    c.email as customer_email,
    c.user_id,
    u.name as user_name,
    u.email as user_email
FROM customers c
LEFT JOIN users u ON c.user_id = u.id
WHERE c.email != u.email OR u.id IS NULL;
