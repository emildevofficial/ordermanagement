-- Product restock metadata
-- Safe to run against existing databases.

ALTER TABLE products ADD COLUMN IF NOT EXISTS last_restocked_at DATETIME NULL;
ALTER TABLE products ADD COLUMN IF NOT EXISTS restock_count INT NOT NULL DEFAULT 0;
ALTER TABLE products ADD COLUMN IF NOT EXISTS updated_at DATETIME NULL;
