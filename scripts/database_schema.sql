CREATE DATABASE IF NOT EXISTS dambalasek_kiosk;
USE dambalasek_kiosk;


CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'superadmin') NOT NULL,
    status ENUM('active', 'suspended') DEFAULT 'active',
    created_by INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);


CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    added_by INT NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE RESTRICT,
    KEY (date_added)
);


CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(20) UNIQUE NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
    payment_amount DECIMAL(10, 2) NOT NULL,
    change_amount DECIMAL(10, 2) NOT NULL,
    created_by INT NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    KEY (date_added)
);


CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);


INSERT INTO users (username, password, email, role, status) 
VALUES ('superadmin', SHA2('superadmin123', 256), 'superadmin@dambalasek.com', 'superadmin', 'active');


INSERT INTO products (name, price, description, image_path, added_by) 
VALUES 
('Menudong Imus', 60.00, 'Traditional Imus Menudo', '/uploads/menudong-imus.jpg', 1),
('Gentri Valenciana', 70.00, 'Spanish-style rice dish', '/uploads/gentri-valenciana.jpg', 1),
('Bacalao', 65.00, 'Salted codfish dish', '/uploads/bacalao.jpg', 1),
('Kilawin Cavite', 55.00, 'Cavite-style kilawin', '/uploads/kilawin-cavite.jpg', 1),
('Pancit Puso', 50.00, 'Heart-shaped noodles', '/uploads/pancit-puso.jpg', 1),
('Pancit Luglog', 50.00, 'Quirino road-style pancit', '/uploads/pancit-luglog.jpg', 1),
('Pochero con Sarsa', 75.00, 'Pochero with sauce', '/uploads/pochero-con-sarsa.jpg', 1),
('Calandracas', 65.00, 'Traditional calandracas', '/uploads/calandracas.jpg', 1);
