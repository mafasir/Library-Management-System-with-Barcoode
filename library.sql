-- Create the database
CREATE DATABASE IF NOT EXISTS `library_management_system`;

-- Use the database
USE `library_management_system`;

-- Table structure for table `admins`
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL
);

-- Inserting a default admin for testing


-- Table structure for table `books`
CREATE TABLE IF NOT EXISTS `books` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `isbn` VARCHAR(20) UNIQUE
  
  
  
);



-- Table structure for table `members`
CREATE TABLE IF NOT EXISTS `members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE,
  `phone` VARCHAR(20),
  `address` TEXT,
  `password` VARCHAR(255) NOT NULL
);

-- Table structure for table `transactions`
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `book_copy_id` INT,
  `member_id` INT,
  `issue_date` DATE NOT NULL,
  `return_date` DATETIME,
  `due_date` DATE NOT NULL,
  `fine` DECIMAL(10, 2) DEFAULT 0.00,
  FOREIGN KEY (`book_copy_id`) REFERENCES `book_copies`(`id`),
  FOREIGN KEY (`member_id`) REFERENCES `members`(`id`)
);

-- Table structure for table `book_copies`
CREATE TABLE IF NOT EXISTS `book_copies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `book_id` INT NOT NULL,
  `barcode` VARCHAR(100) UNIQUE NOT NULL,
  `status` VARCHAR(50) DEFAULT 'available',
  FOREIGN KEY (`book_id`) REFERENCES `books`(`id`)
);
