-- Forgeframe Studios user module schema
-- Creates database and users table with required indexes.

CREATE DATABASE IF NOT EXISTS `forgeframe_studios_site`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `forgeframe_studios_site`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `home_address` TEXT NOT NULL,
  `home_phone` VARCHAR(30) NOT NULL,
  `cell_phone` VARCHAR(30) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`),
  KEY `idx_users_first_name` (`first_name`),
  KEY `idx_users_last_name` (`last_name`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_home_phone` (`home_phone`),
  KEY `idx_users_cell_phone` (`cell_phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
