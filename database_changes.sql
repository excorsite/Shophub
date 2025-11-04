# filename: database_changes.sql
# Suggested changes to the SQL database:
# 1. Add a 'ratings' table for explicit/implicit ratings if needed, but we'll derive from 'orders' for implicit feedback.
#    For collaborative filtering, we can use orders.quantity as rating proxy.
#    If you want explicit ratings, add this table:

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` float NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rating` (`user_id`, `product_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

# 2. Add a column to 'products' for better recommendation grouping (optional, for combo recommendations)
ALTER TABLE `products` ADD COLUMN `tags` VARCHAR(255) DEFAULT NULL;  # e.g., 'spicy,momo,asian' for tagging

# 3. Ensure 'orders' has enough data; if not, seed some dummy data for training.
# Example dummy data insertion (assuming some users and products exist):
INSERT INTO `products` (`name`, `description`, `price`, `food_type`, `seller_id`) VALUES
('Momo', 'Steamed dumplings', 5.99, 'fast-food', 1),
('Burger Combo', 'Burger with fries and drink', 9.99, 'combo', 1),
('Thai Curry', 'Spicy curry', 8.99, 'thai', 1),
('Chocolate Dessert', 'Sweet chocolate cake', 4.99, 'dessert', 1),
('Soda Drink', 'Carbonated beverage', 1.99, 'drink', 1);

INSERT INTO `orders` (`buyer_id`, `product_id`, `quantity`, `status`) VALUES
(6, 1, 2, 'delivered'),  # User 6 bought Momo twice
(6, 2, 1, 'delivered'),  # User 6 bought Burger Combo
(7, 1, 1, 'delivered'),  # User 7 bought Momo
(7, 3, 3, 'delivered'),  # User 7 bought Thai Curry
(7, 4, 1, 'delivered'),  # User 7 bought Chocolate Dessert
(8, 2, 1, 'delivered'),  # User 8 bought Burger Combo
(8, 5, 2, 'delivered');  # User 8 bought Soda Drink

# Note: Run this SQL to apply changes and seed data for testing the model.