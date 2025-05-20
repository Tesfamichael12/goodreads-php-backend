-- GoodReads Clone Database Schema
-- This file contains all the SQL statements to create the database and tables

-- Create database
CREATE DATABASE IF NOT EXISTS goodreads_clone;
USE goodreads_clone;

-- Drop tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS comment_likes;
DROP TABLE IF EXISTS review_likes;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS follows;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS shelves;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

-- Roles Table
CREATE TABLE `roles` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `profile_pic` VARCHAR(255) DEFAULT 'default.png',
  `bio` TEXT,
  `role_id` INT NOT NULL DEFAULT 2, -- Default to regular user role
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
);

-- Books Table
CREATE TABLE `books` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `genre` VARCHAR(100),
  `description` TEXT,
  `published_year` YEAR,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT,
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- Shelves Table
CREATE TABLE `shelves` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `book_id` INT NOT NULL,
  `status` ENUM('to-read', 'reading', 'read') DEFAULT 'to-read',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `user_book` (`user_id`, `book_id`)
);

-- Reviews Table
CREATE TABLE `reviews` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `book_id` INT NOT NULL,
  `rating` INT CHECK (`rating` BETWEEN 1 AND 5),
  `comment` TEXT,
  `comment_count` INT DEFAULT 0,
  `like_count` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `user_book_review` (`user_id`, `book_id`)
);

-- Follows Table
CREATE TABLE `follows` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `follower_id` INT NOT NULL,
  `following_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`follower_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`following_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `follower_following` (`follower_id`, `following_id`)
);

-- Comments Table
CREATE TABLE `comments` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `review_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `content` TEXT NOT NULL,
  `like_count` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`review_id`) REFERENCES `reviews`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Review Likes Table
CREATE TABLE IF NOT EXISTS `review_likes` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `review_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `review_user` (`review_id`, `user_id`),
  FOREIGN KEY (`review_id`) REFERENCES `reviews`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Comment Likes Table
CREATE TABLE IF NOT EXISTS `comment_likes` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `comment_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `comment_user` (`comment_id`, `user_id`),
  FOREIGN KEY (`comment_id`) REFERENCES `comments`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Insert roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator with full access to all features'),
(2, 'user', 'Regular user with standard permissions');

-- Insert sample data

-- Sample Users (with proper role assignments)
INSERT INTO `users` (`name`, `email`, `password`, `bio`, `role_id`) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Book enthusiast and avid reader.', 1), -- Admin
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'I love fiction and fantasy novels.', 2), -- Regular user
('Bob Johnson', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Science fiction and mystery reader.', 2); -- Regular user

-- Sample Books
INSERT INTO `books` (`title`, `author`, `genre`, `description`, `published_year`, `created_by`) VALUES
('To Kill a Mockingbird', 'Harper Lee', 'Classic', 'The unforgettable novel of a childhood in a sleepy Southern town and the crisis of conscience that rocked it.', 1960, 1),
('1984', 'George Orwell', 'Dystopian', 'A dystopian social science fiction novel and cautionary tale.', 1949, 1),
('The Great Gatsby', 'F. Scott Fitzgerald', 'Classic', 'A story of wealth, class, love and denial in the spring of 1922.', 1925, 1),
('Pride and Prejudice', 'Jane Austen', 'Romance', 'A romantic novel of manners.', 1813, 1),
('The Hobbit', 'J.R.R. Tolkien', 'Fantasy', 'A fantasy novel set in Middle-earth.', 1937, 1);

-- Sample Shelves
INSERT INTO `shelves` (`user_id`, `book_id`, `status`) VALUES
(1, 1, 'read'),
(1, 2, 'reading'),
(1, 3, 'to-read'),
(2, 1, 'reading'),
(2, 4, 'read'),
(3, 5, 'read'),
(3, 2, 'to-read');

-- Sample Reviews
INSERT INTO `reviews` (`user_id`, `book_id`, `rating`, `comment`) VALUES
(1, 1, 5, 'A masterpiece of American literature. Scout''s perspective is refreshing and insightful.'),
(2, 4, 4, 'A classic romance novel with witty dialogue and memorable characters.'),
(3, 5, 5, 'An amazing adventure that started my love for fantasy literature.');

-- Sample Follows
INSERT INTO `follows` (`follower_id`, `following_id`) VALUES
(1, 2),
(1, 3),
(2, 1),
(3, 1);

-- Sample Comments
INSERT INTO `comments` (`review_id`, `user_id`, `content`) VALUES
(1, 2, 'I completely agree! The character development is incredible.'),
(1, 3, 'One of my favorites as well.'),
(2, 1, 'Jane Austen is such a brilliant writer.');

-- Sample Review Likes
INSERT INTO `review_likes` (`review_id`, `user_id`) VALUES
(1, 2),
(1, 3),
(2, 1),
(3, 1);

-- Sample Comment Likes
INSERT INTO `comment_likes` (`comment_id`, `user_id`) VALUES
(1, 1),
(2, 2),
(3, 3);

-- Update counts based on sample data
UPDATE `reviews` SET `comment_count` = 2 WHERE `id` = 1;
UPDATE `reviews` SET `comment_count` = 1 WHERE `id` = 2;
UPDATE `reviews` SET `like_count` = 2 WHERE `id` = 1;
UPDATE `reviews` SET `like_count` = 1 WHERE `id` = 2;
UPDATE `reviews` SET `like_count` = 1 WHERE `id` = 3;
UPDATE `comments` SET `like_count` = 1 WHERE `id` = 1;
UPDATE `comments` SET `like_count` = 1 WHERE `id` = 2;
UPDATE `comments` SET `like_count` = 1 WHERE `id` = 3;
