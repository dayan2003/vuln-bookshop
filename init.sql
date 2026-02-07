-- init.sql (for Docker MySQL init)
CREATE DATABASE IF NOT EXISTS vulnapp;
USE vulnapp;

-- Create the user with proper host permissions for Docker networking
CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'pass';
GRANT ALL PRIVILEGES ON vulnapp.* TO 'user'@'%';
FLUSH PRIVILEGES;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  uid VARCHAR(128),
  username VARCHAR(80),
  password VARCHAR(120),      -- intentionally plaintext for the lab
  role VARCHAR(20),
  secret TEXT
);

-- Drop existing table
DROP TABLE IF EXISTS comments;

-- Create enhanced comments table with book and author information
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `user` VARCHAR(80),
  book_title VARCHAR(200),
  author VARCHAR(150),
  rating INT DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample comments with full book details
INSERT INTO comments (`user`, book_title, author, rating, comment) VALUES
('system', 'Welcome Guide', 'Admin Team', 5, 'Welcome to our book review platform! Share your thoughts on your favorite reads.'),
('alice_reader', 'The Great Gatsby', 'F. Scott Fitzgerald', 4, 'A masterpiece of American literature. Fitzgerald''s prose is beautiful and the symbolism is profound. The story of Jay Gatsby is both tragic and compelling.'),
('bookworm_bob', '1984', 'George Orwell', 5, 'Absolutely brilliant and terrifyingly relevant. Orwell''s dystopian vision feels more real than ever. A must-read for understanding modern society.'),
('literary_linda', 'Pride and Prejudice', 'Jane Austen', 5, 'Austen''s wit and social commentary are unmatched. Elizabeth Bennet is one of literature''s greatest heroines. The romance is perfectly crafted.'),
('sci_fi_sam', 'Dune', 'Frank Herbert', 4, 'Epic world-building and complex political intrigue. Herbert created an incredibly detailed universe. The ecological themes are fascinating.'),
('mystery_mike', 'The Girl with the Dragon Tattoo', 'Stieg Larsson', 3, 'Engaging thriller with complex characters. Lisbeth Salander is unforgettable. Some parts dragged but overall entertaining.'),
('philosophy_phil', 'Sapiens', 'Yuval Noah Harari', 4, 'Thought-provoking examination of human history. Harari presents complex ideas in accessible ways. Changes how you think about civilization.'),
('romance_reader', 'The Seven Husbands of Evelyn Hugo', 'Taylor Jenkins Reid', 5, 'Absolutely captivating! Reid''s storytelling is masterful and Evelyn''s character is complex and compelling. Couldn''t put it down.'),
('history_buff', 'Educated', 'Tara Westover', 4, 'Powerful memoir about education and family. Westover''s journey is both inspiring and heartbreaking. Beautifully written.'),
('fantasy_fan', 'The Name of the Wind', 'Patrick Rothfuss', 4, 'Beautifully written fantasy with excellent world-building. Kvothe is a fascinating narrator. Eagerly waiting for the next book!'),
('classic_lover', 'To Kill a Mockingbird', 'Harper Lee', 5, 'Timeless classic that deals with important social issues. Scout''s perspective is innocent yet profound. Lee''s message remains relevant today.');

-- XSS Challenge Flag Table (minimalist approach)
DROP TABLE IF EXISTS xss_flags;
CREATE TABLE xss_flags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  flag_name VARCHAR(50),
  flag_value VARCHAR(100)
);

-- Seed users (plaintext password on purpose)
-- NOTE: uid values are Base64 of the numeric id to keep it teachable:
-- id=1 -> MQ==  (Base64 of '1')
-- id=2 -> Mg==  (Base64 of '2')
INSERT INTO users (uid,username,password,role,secret) VALUES
('MQ==','Alice','alicepass','user','FLAG{idor_user_secret_7c8d}'),       -- IDOR flag (viewable via profile)
('MzQz','Admin_the_one','adminpass','administrator','FLAG{sqli_admin_password_9f3a}'),     -- SQLi/auth flag returned after SQLi login/search
('Nzg=','Neymar','Neypass','user','FLAG{user78_secret}'),
('MTAx','Messi','messipass','user','FLAG{user101_secret}'),
('MTAy','Ronaldo','cr7pass','user','FLAG{user102_secret}');
-- XSS Flag for the challenge
INSERT INTO xss_flags (flag_name, flag_value) VALUES
('stored_xss', 'FLAG{stored_xss_vulnerability_found_2024}');



DROP TABLE IF EXISTS books;
CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150),
  author VARCHAR(120),
  category VARCHAR(60),
  price DECIMAL(6,2),
  blurb TEXT
);

INSERT INTO books (title,author,category,price,blurb) VALUES
('The Quiet Library','A. Reader','Fiction',12.99,'A cozy mystery among stacks.'),
('Signals and Systems','A. Oppenheim','Non-fiction',39.50,'Foundations for engineers.'),
('Sinhala Folk Tales','Local Author','Local',8.99,'Classic village stories.'),
('Moon for Children','K. Jay','Children',7.50,'Bedtime stories and stars.'),
('Staff Picks Vol. 1','Various','Staff Picks',9.99,'Our team''s favorites this month.'),
('All the Colors of the Dark','Chris Whitaker','Fiction',10.99,'A haunting thriller of loss and hope.'),
('Dune','Frank Herbert','Science Fiction',11.99,'Epic saga of politics and sandworms.'),
('Pride and Prejudice','Jane Austen','Classic',12.99,'A witty tale of love and manners.'),
('Prisoner''s Dilemma','Richard Powers','Fiction',13.99,'A family saga of science and secrets.'),
('Sapiens','Yuval Noah Harari','Non-fiction',14.99,'A brief history of humankind.'),
('The Girl with the Dragon Tattoo','Stieg Larsson','Mystery',15.99,'A gripping Nordic thriller.'),
('The Godfather','Mario Puzo','Crime',16.99,'The classic mafia novel.'),
('The Great Gatsby','F. Scott Fitzgerald','Classic',17.99,'The American dream and its cost.'),
('A Tale of Two Cities','Charles Dickens','Classic',18.99,'A story of revolution and redemption.'),
('Don Quixote','Miguel de Cervantes','Classic',19.99,'The adventures of a would-be knight.');