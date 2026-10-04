-- TravelGo Database
-- Import this file in phpMyAdmin.
-- Database used by config/database.php: travelgo

CREATE DATABASE IF NOT EXISTS `travelgo`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `travelgo`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `package_itinerary`;
DROP TABLE IF EXISTS `packages`;
DROP TABLE IF EXISTS `destinations`;
DROP TABLE IF EXISTS `blogs`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `role` ENUM('user','admin') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `destinations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(180) NOT NULL,
  `location` VARCHAR(150) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `gallery_images` LONGTEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_destinations_slug` (`slug`),
  KEY `idx_destinations_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `packages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `destination_id` INT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(180) NOT NULL,
  `slug` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `duration` VARCHAR(100) DEFAULT NULL,
  `price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `image` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_packages_slug` (`slug`),
  KEY `idx_packages_destination` (`destination_id`),
  KEY `idx_packages_status` (`status`),
  CONSTRAINT `fk_packages_destination`
    FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `package_itinerary` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `package_id` INT UNSIGNED NOT NULL,
  `day_number` INT UNSIGNED NOT NULL,
  `title` VARCHAR(180) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_itinerary_day` (`package_id`, `day_number`),
  KEY `idx_itinerary_package` (`package_id`),
  CONSTRAINT `fk_itinerary_package`
    FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bookings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `package_id` INT UNSIGNED DEFAULT NULL,
  `booking_date` DATE NOT NULL,
  `travelers` INT UNSIGNED NOT NULL DEFAULT 1,
  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bookings_user` (`user_id`),
  KEY `idx_bookings_package` (`package_id`),
  KEY `idx_bookings_status` (`status`),
  CONSTRAINT `fk_bookings_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_package`
    FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reviews` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `package_id` INT UNSIGNED DEFAULT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `review` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_review_user_package` (`user_id`, `package_id`),
  KEY `idx_reviews_package` (`package_id`),
  CONSTRAINT `fk_reviews_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_package`
    FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blogs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(220) NOT NULL,
  `slug` VARCHAR(240) NOT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `author` VARCHAR(120) DEFAULT NULL,
  `status` ENUM('published','draft') NOT NULL DEFAULT 'draft',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_blogs_slug` (`slug`),
  KEY `idx_blogs_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contacts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `subject` VARCHAR(200) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contacts_email` (`email`),
  KEY `idx_contacts_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin account used by admin/create-admin.php
-- Email: admin@travelgo.com
-- Password: admin123
INSERT INTO `users` (`name`, `email`, `password`, `role`)
VALUES (
  'TravelGo Admin',
  'admin@travelgo.com',
  '$2y$12$qwEO7VHlXwisvlUoN7oRQeAv3ox0kBmo6MVZpMqFfcOvjSxS68FWW',
  'admin'
);

-- Optional starter destinations. Add packages from the admin panel.
INSERT INTO `destinations` (`name`, `slug`, `location`, `description`, `image`, `gallery_images`) VALUES
('Rajasthan', 'rajasthan', 'Rajasthan, India', 'Explore royal forts, palaces, colourful markets and the rich culture of Rajasthan.', NULL, NULL),
('Goa', 'goa', 'Goa, India', 'Enjoy beaches, heritage locations, local food and relaxing coastal experiences.', NULL, NULL),
('Manali', 'manali', 'Himachal Pradesh, India', 'Discover mountain scenery, valleys, rivers and memorable Himalayan experiences.', NULL, NULL),
('Kerala', 'kerala', 'Kerala, India', 'Experience backwaters, greenery, beaches and the natural beauty of Kerala.', NULL, NULL),
('Kashmir', 'kashmir', 'Jammu & Kashmir, India', 'Explore beautiful valleys, lakes and mountain landscapes in Kashmir.', NULL, NULL);

