CREATE DATABASE IF NOT EXISTS order_management;
USE order_management;

SET FOREIGN_KEY_CHECKS = 0;

-- USERS
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
);

-- CUSTOMERS
CREATE TABLE IF NOT EXISTS customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    is_active TINYINT(1) DEFAULT 1,
    UNIQUE KEY uniq_customers_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- PRODUCTS
CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(500) NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_restocked_at DATETIME NULL,
    restock_count INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
);

-- ORDERS
CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED,
    customer_id INT UNSIGNED,
    title VARCHAR(200),
    description TEXT,
    notes TEXT NULL,
    total DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending','processing','shipped','delivered','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- ORDER ITEMS
CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- RETURNS
CREATE TABLE IF NOT EXISTS returns (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NULL,
    customer_id INT UNSIGNED NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    reason TEXT,
    admin_notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,
    UNIQUE KEY uniq_returns_order_id (order_id),

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- PROMOTION SETTINGS
CREATE TABLE IF NOT EXISTS promotion_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    new_user_discount_enabled TINYINT(1) NOT NULL DEFAULT 0,
    new_user_discount_percent INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
);

INSERT INTO promotion_settings (id, new_user_discount_enabled, new_user_discount_percent)
VALUES (1, 0, 0)
ON DUPLICATE KEY UPDATE id = id;

-- RUNTIME-SAFE MIGRATIONS FOR EXISTING RAILWAY DATABASES
DELIMITER //
CREATE PROCEDURE add_column_if_missing(
    IN table_name_value VARCHAR(64),
    IN column_name_value VARCHAR(64),
    IN column_definition_value TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = table_name_value
          AND COLUMN_NAME = column_name_value
    ) THEN
        SET @ddl = CONCAT('ALTER TABLE `', table_name_value, '` ADD COLUMN ', column_definition_value);
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END//
DELIMITER ;

CALL add_column_if_missing('users', 'updated_at', '`updated_at` DATETIME NULL');

CALL add_column_if_missing('customers', 'user_id', '`user_id` INT UNSIGNED NULL');
CALL add_column_if_missing('customers', 'updated_at', '`updated_at` DATETIME NULL');
CALL add_column_if_missing('customers', 'is_active', '`is_active` TINYINT(1) DEFAULT 1');

CALL add_column_if_missing('products', 'last_restocked_at', '`last_restocked_at` DATETIME NULL');
CALL add_column_if_missing('products', 'restock_count', '`restock_count` INT NOT NULL DEFAULT 0');
CALL add_column_if_missing('products', 'updated_at', '`updated_at` DATETIME NULL');
CALL add_column_if_missing('products', 'image_url', '`image_url` VARCHAR(500) NULL');

CALL add_column_if_missing('orders', 'notes', '`notes` TEXT NULL');
CALL add_column_if_missing('orders', 'total', '`total` DECIMAL(10,2) DEFAULT 0.00');
CALL add_column_if_missing('orders', 'updated_at', '`updated_at` DATETIME NULL');
ALTER TABLE orders MODIFY COLUMN status ENUM('pending','processing','shipped','delivered','completed','cancelled') DEFAULT 'pending';

CALL add_column_if_missing('returns', 'admin_notes', '`admin_notes` TEXT NULL');
CALL add_column_if_missing('returns', 'updated_at', '`updated_at` DATETIME NULL');
CALL add_column_if_missing('returns', 'user_id', '`user_id` INT UNSIGNED NULL');
CALL add_column_if_missing('returns', 'customer_id', '`customer_id` INT UNSIGNED NULL');
UPDATE returns SET status = 'pending' WHERE status IN ('requested', 'under_review');
UPDATE returns SET status = 'approved' WHERE status = 'refunded';
ALTER TABLE returns MODIFY COLUMN status ENUM('pending','approved','rejected') DEFAULT 'pending';

DELETE r
FROM returns r
LEFT JOIN orders o ON o.id = r.order_id
WHERE o.id IS NULL;

UPDATE returns r
LEFT JOIN orders o ON o.id = r.order_id
SET r.user_id = o.user_id,
    r.customer_id = o.customer_id
WHERE o.id IS NOT NULL
  AND (r.user_id IS NULL OR r.customer_id IS NULL);

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

DROP PROCEDURE add_column_if_missing;

-- DEFAULT ADMIN
INSERT INTO users (name, email, password, role)
VALUES (
    'Admin',
    'admin@example.com',
    '$2y$10$3p3Uj6/pmqD1S521GnbIfequco8hHGDXBD1wHovf4TxEoiU3woKo6',
    'admin'
)
ON DUPLICATE KEY UPDATE email = email;

SET FOREIGN_KEY_CHECKS = 1;
