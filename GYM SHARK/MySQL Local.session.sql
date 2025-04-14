-- Create the database
CREATE DATABASE IF NOT EXISTS onlineshop;
USE onlineshop;

-- Create customers table
CREATE TABLE IF NOT EXISTS customer (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone_number VARCHAR(20),
    INDEX idx_email (email)
);

-- Create products table
CREATE TABLE IF NOT EXISTS product (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL,
    INDEX idx_name (name)
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_cost DECIMAL(10, 2) NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customer(customer_id) ON DELETE CASCADE,
    INDEX idx_customer (customer_id),
    INDEX idx_date (order_date)
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(product_id) ON DELETE RESTRICT,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
);

-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    payment_method VARCHAR(50) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_date (payment_date)
);

-- Add some sample data
INSERT INTO customer (email, password, name, address, phone_number) VALUES
('john@example.com', 'hashed_password', 'John Doe', '123 Main St, Anytown, USA', '555-1234'),
('jane@example.com', 'hashed_password', 'Jane Smith', '456 Elm St, Othertown, USA', '555-5678');

INSERT INTO product (name, description, price, stock_quantity) VALUES
('Laptop', 'High-performance laptop', 999.99, 50),
('Smartphone', 'Latest model smartphone', 699.99, 100),
('Headphones', 'Noise-cancelling headphones', 199.99, 200);

INSERT INTO orders (customer_id, total_cost, status) VALUES
(1, 999.99, 'Completed'),
(2, 899.98, 'Processing');

INSERT INTO order_items (order_id, product_id, quantity) VALUES
(1, 1, 1),
(2, 2, 1),
(2, 3, 1);

INSERT INTO payments (order_id, payment_method, amount) VALUES
(1, 'Credit Card', 999.99),
(2, 'PayPal', 899.98);
