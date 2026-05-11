-- Return flow consistency migration
-- Standardizes return statuses and prevents duplicate returns per order.

USE order_management;

ALTER TABLE returns ADD COLUMN IF NOT EXISTS admin_notes TEXT NULL;
ALTER TABLE returns ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL;
ALTER TABLE returns ADD COLUMN IF NOT EXISTS user_id INT UNSIGNED NULL AFTER order_id;
ALTER TABLE returns ADD COLUMN IF NOT EXISTS customer_id INT UNSIGNED NULL AFTER user_id;

UPDATE returns r
JOIN orders o ON o.id = r.order_id
SET r.user_id = o.user_id,
    r.customer_id = o.customer_id
WHERE r.user_id IS NULL
   OR r.customer_id IS NULL;

UPDATE returns
SET status = 'pending'
WHERE status IN ('requested', 'under_review');

UPDATE returns
SET status = 'approved'
WHERE status = 'refunded';

DELETE r1
FROM returns r1
JOIN returns r2
    ON r1.order_id = r2.order_id
   AND r1.id > r2.id;

ALTER TABLE returns
    MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending';

SET @has_return_order_unique := (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'returns'
      AND index_name = 'uniq_returns_order_id'
);

SET @add_return_order_unique := IF(
    @has_return_order_unique = 0,
    'ALTER TABLE returns ADD CONSTRAINT uniq_returns_order_id UNIQUE (order_id)',
    'SELECT 1'
);

PREPARE add_return_order_unique_stmt FROM @add_return_order_unique;
EXECUTE add_return_order_unique_stmt;
DEALLOCATE PREPARE add_return_order_unique_stmt;
