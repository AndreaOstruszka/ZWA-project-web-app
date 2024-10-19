-- Authors Table
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    birth_year YEAR NOT NULL,
    death_year YEAR NOT NULL,
    bio TEXT
);

-- Books Table
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    author_id INT NOT NULL,
    literary_genre ENUM('prose', 'poetry', 'drama') NOT NULL,
    fiction_genre ENUM('scifi', 'fantasy', 'horror', 'thriller', 'romance', 'none','historical fiction') DEFAULT 'none',
    FOREIGN KEY (author_id) REFERENCES authors(id)
);

-- Book Charts Table
CREATE TABLE book_charts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    chart_type ENUM('poetry', 'novel', 'play') NOT NULL,
    ranking INT NOT NULL,
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- Insert authors into the 'authors' table
INSERT INTO authors (name, surname, birth_year, death_year, bio) VALUES
('J.R.R.', 'Tolkien', 1892, 1973, 'British author, best known for The Hobbit and The Lord of the Rings.'),
('Jane', 'Austen', 1775, 1817, 'English novelist known for her romantic fiction.'),
('William', 'Shakespeare', 1564, 1616, 'English playwright, widely regarded as the greatest writer in the English language.');

-- Insert books into the 'books' table
INSERT INTO books (name, isbn, author_id, literary_genre, fiction_genre) VALUES
('The Hobbit', '978-0-261-10221-7', 1, 'prose', 'fantasy'),
('Pride and Prejudice', '978-1-59308-201-1', 2, 'prose', 'romance'),
('Romeo and Juliet', '978-0-7432-7358-5', 3, 'drama', 'none');