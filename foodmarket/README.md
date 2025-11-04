-- Database: foodmarket

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
email VARCHAR(100) NOT NULL UNIQUE,
type ENUM('admin', 'seller', 'buyer') NOT NULL,
approved TINYINT(1) DEFAULT 0, -- 0: pending, 1: approved (for sellers)
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `products` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(100) NOT NULL,
`description` TEXT DEFAULT NULL,
`price` DECIMAL(10,2) NOT NULL,
`image` VARCHAR(255) DEFAULT NULL,
`food_type` ENUM(
'fast-food',
'dessert',
'drink',
'snack',
'beverage',
'combo',
'thai',
'chinese',
'korean',
'japanese',
'other'
) NOT NULL,
`seller_id` INT(11) NOT NULL,
`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
PRIMARY KEY (`id`),
KEY `seller_id` (`seller_id`),
CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE orders (
id INT AUTO_INCREMENT PRIMARY KEY,
buyer_id INT NOT NULL,
product_id INT NOT NULL,
quantity INT DEFAULT 1,
status ENUM('pending', 'approved', 'rejected', 'delivered') DEFAULT 'pending',
order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE cart (
id INT AUTO_INCREMENT PRIMARY KEY,
buyer_id INT NOT NULL,
product_id INT NOT NULL,
quantity INT DEFAULT 1,
FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
UNIQUE KEY unique_cart (buyer_id, product_id)
);

CREATE TABLE wishlist (
id INT AUTO_INCREMENT PRIMARY KEY,
buyer_id INT NOT NULL,
product_id INT NOT NULL,
FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
UNIQUE KEY unique_wishlist (buyer_id, product_id)
);

-- Initial data: Admin user (password: admin123 hashed)
INSERT INTO users (username, password, email, type, approved)
VALUES ('admin', '$2y$10$K.4z4z4z4z4z4z4z4z4z4uK.4z4z4z4z4z4z4z4z4', 'admin@example.com', 'admin', 1);
-- Note: Use password_hash('admin123', PASSWORD_DEFAULT) to generate the hash above.
