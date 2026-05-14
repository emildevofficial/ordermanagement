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

DELETE r
FROM returns r
LEFT JOIN orders o ON o.id = r.order_id
WHERE o.id IS NULL;

UPDATE returns r
LEFT JOIN users u ON u.id = r.user_id
SET r.user_id = NULL
WHERE r.user_id IS NOT NULL
  AND u.id IS NULL;

UPDATE returns r
LEFT JOIN customers c ON c.id = r.customer_id
SET r.customer_id = NULL
WHERE r.customer_id IS NOT NULL
  AND c.id IS NULL;

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
      AND non_unique = 0
      AND column_name = 'order_id'
);

SET @add_return_order_unique := IF(
    @has_return_order_unique = 0,
    'ALTER TABLE returns ADD CONSTRAINT uniq_returns_order_id UNIQUE (order_id)',
    'SELECT 1'
);

PREPARE add_return_order_unique_stmt FROM @add_return_order_unique;
EXECUTE add_return_order_unique_stmt;
DEALLOCATE PREPARE add_return_order_unique_stmt;

SET @has_returns_order_fk := (
    SELECT COUNT(*)
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE table_schema = DATABASE()
      AND table_name = 'returns'
      AND column_name = 'order_id'
      AND referenced_table_name = 'orders'
      AND referenced_column_name = 'id'
);

SET @add_returns_order_fk := IF(
    @has_returns_order_fk = 0,
    'ALTER TABLE returns ADD CONSTRAINT fk_returns_order_id FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE',
    'SELECT 1'
);

PREPARE add_returns_order_fk_stmt FROM @add_returns_order_fk;
EXECUTE add_returns_order_fk_stmt;
DEALLOCATE PREPARE add_returns_order_fk_stmt;

SET @has_returns_user_fk := (
    SELECT COUNT(*)
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE table_schema = DATABASE()
      AND table_name = 'returns'
      AND column_name = 'user_id'
      AND referenced_table_name = 'users'
      AND referenced_column_name = 'id'
);

SET @add_returns_user_fk := IF(
    @has_returns_user_fk = 0,
    'ALTER TABLE returns ADD CONSTRAINT fk_returns_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT 1'
);

PREPARE add_returns_user_fk_stmt FROM @add_returns_user_fk;
EXECUTE add_returns_user_fk_stmt;
DEALLOCATE PREPARE add_returns_user_fk_stmt;

SET @has_returns_customer_fk := (
    SELECT COUNT(*)
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE table_schema = DATABASE()
      AND table_name = 'returns'
      AND column_name = 'customer_id'
      AND referenced_table_name = 'customers'
      AND referenced_column_name = 'id'
);

SET @add_returns_customer_fk := IF(
    @has_returns_customer_fk = 0,
    'ALTER TABLE returns ADD CONSTRAINT fk_returns_customer_id FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL',
    'SELECT 1'
);

PREPARE add_returns_customer_fk_stmt FROM @add_returns_customer_fk;
EXECUTE add_returns_customer_fk_stmt;
DEALLOCATE PREPARE add_returns_customer_fk_stmt;
