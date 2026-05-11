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
