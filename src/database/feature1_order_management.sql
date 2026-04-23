-- Feature 1 Order Management SQL Migration
-- Run this in your MySQL database after docker-compose up

USE order_management;

-- Add notes column if not exists
ALTER TABLE orders ADD COLUMN IF NOT EXISTS notes TEXT NULL;

-- Update status enum to include all required states
ALTER TABLE orders MODIFY COLUMN status ENUM(
    'pending', 
    'processing', 
    'shipped', 
    'delivered', 
    'cancelled'
) DEFAULT 'pending';

-- Add customer_id if not exists (per handler expectations)
ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_id INT UNSIGNED NULL;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS total DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add foreign key constraint for customer_id if customers table exists
-- ALTER TABLE orders ADD CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL;

-- Verify changes
DESCRIBE orders;

