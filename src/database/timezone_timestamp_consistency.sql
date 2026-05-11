-- Timestamp/timezone consistency migration.
-- App stores timestamps in UTC and displays them in Europe/Tirane.

USE order_management;

ALTER TABLE users ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL;
ALTER TABLE products ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL;
ALTER TABLE returns ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL;
