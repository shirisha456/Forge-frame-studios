-- Forge Frame Studios - Users table and sample data
-- --------------------------------------------------
-- This SQL file creates a `users` table and inserts
-- sample users for the local company (Company A).
--
-- How to import using phpMyAdmin:
-- 1. Log in to phpMyAdmin.
-- 2. Create a new database (e.g. `forgeframe_lab`) if it does not exist.
-- 3. Select the database in the left sidebar.
-- 4. Click on the "Import" tab.
-- 5. Click "Choose File" and select this `users.sql` file.
-- 6. Click "Go" to run the import.
--
-- How to import using MySQL CLI:
--   mysql -u your_username -p your_database_name < /path/to/users.sql
--
-- After importing, update `includes/db.php` with matching
-- database name, username, and password.

-- Drop the table if it already exists (for easy re-runs in lab)
DROP TABLE IF EXISTS `users`;

-- Create users table
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `role` VARCHAR(100) NOT NULL,
  `company_name` VARCHAR(150) NOT NULL DEFAULT 'Forge Frame Studios',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample local users for Forge Frame Studios (Company A)
INSERT INTO `users` (`full_name`, `email`, `role`, `company_name`) VALUES
-- Example site USERS (e.g. clients or registered users), not staff
('Aarav Kumar',       'aarav.kumar@example.com',        'Client',          'Forge Frame Studios'),
('Sophia Johnson',    'sophia.johnson@example.com',     'Client',          'Forge Frame Studios'),
('Liam Brown',        'liam.brown@example.com',         'Registered User', 'Forge Frame Studios'),
('Olivia Martinez',   'olivia.martinez@example.com',    'Registered User', 'Forge Frame Studios'),
('Noah Williams',     'noah.williams@example.com',      'Client',          'Forge Frame Studios');

