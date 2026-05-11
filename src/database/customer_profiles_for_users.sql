-- Ensure every normal user has one customer profile.

USE order_management;

SET @has_customer_user_id_column := (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'customers'
      AND column_name = 'user_id'
);

SET @add_customer_user_id_column := IF(
    @has_customer_user_id_column = 0,
    'ALTER TABLE customers ADD COLUMN user_id INT UNSIGNED NULL',
    'SELECT 1'
);

PREPARE add_customer_user_id_column_stmt FROM @add_customer_user_id_column;
EXECUTE add_customer_user_id_column_stmt;
DEALLOCATE PREPARE add_customer_user_id_column_stmt;

SET @has_customer_user_id_index := (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'customers'
      AND index_name = 'idx_customers_user_id'
);

SET @add_customer_user_id_index := IF(
    @has_customer_user_id_index = 0,
    'ALTER TABLE customers ADD INDEX idx_customers_user_id (user_id)',
    'SELECT 1'
);

PREPARE add_customer_user_id_index_stmt FROM @add_customer_user_id_index;
EXECUTE add_customer_user_id_index_stmt;
DEALLOCATE PREPARE add_customer_user_id_index_stmt;

UPDATE customers c
INNER JOIN users u ON u.email = c.email
SET c.user_id = u.id
WHERE c.user_id IS NULL
  AND u.role = 'user';

INSERT INTO customers (user_id, name, email, created_at, is_active)
SELECT u.id, u.name, u.email, UTC_TIMESTAMP(), 1
FROM users u
LEFT JOIN customers c ON c.user_id = u.id
WHERE u.role = 'user'
  AND c.id IS NULL;

DELETE c1
FROM customers c1
JOIN customers c2
    ON c1.user_id = c2.user_id
   AND c1.id > c2.id
WHERE c1.user_id IS NOT NULL;

SET @has_customer_user_unique := (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'customers'
      AND index_name = 'uniq_customers_user_id'
);

SET @add_customer_user_unique := IF(
    @has_customer_user_unique = 0,
    'ALTER TABLE customers ADD CONSTRAINT uniq_customers_user_id UNIQUE (user_id)',
    'SELECT 1'
);

PREPARE add_customer_user_unique_stmt FROM @add_customer_user_unique;
EXECUTE add_customer_user_unique_stmt;
DEALLOCATE PREPARE add_customer_user_unique_stmt;

SET @has_customer_user_fk := (
    SELECT COUNT(*)
    FROM information_schema.referential_constraints
    WHERE constraint_schema = DATABASE()
      AND constraint_name = 'fk_customers_user_id'
);

SET @add_customer_user_fk := IF(
    @has_customer_user_fk = 0,
    'ALTER TABLE customers ADD CONSTRAINT fk_customers_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT 1'
);

PREPARE add_customer_user_fk_stmt FROM @add_customer_user_fk;
EXECUTE add_customer_user_fk_stmt;
DEALLOCATE PREPARE add_customer_user_fk_stmt;
