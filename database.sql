-- Users Table
CREATE TABLE users
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(50)         NOT NULL,
    last_name     VARCHAR(50)         NOT NULL,
    user_name     VARCHAR(25) UNIQUE  NOT NULL,
    email         VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255)        NOT NULL,
    role          ENUM('admin', 'registered_user') NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Authors Table
CREATE TABLE authors
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(255) NOT NULL,
    surname    VARCHAR(255) NOT NULL,
    birth_year INT(4) NOT NULL,
    death_year INT(4) NOT NULL,
    bio        TEXT
);

-- Books Table
CREATE TABLE books
(
    id               INT AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(255)       NOT NULL,
    isbn             VARCHAR(20) UNIQUE NOT NULL,
    author_id        INT                NOT NULL,
    literary_genre   ENUM('prose', 'poetry', 'drama') NOT NULL,
    fiction_genre    ENUM('scifi', 'fantasy', 'horror', 'thriller', 'romance', 'none','historical fiction') DEFAULT 'none',
    book_cover_large VARCHAR(255),
    book_cover_small VARCHAR(255),
    FOREIGN KEY (author_id) REFERENCES authors (id)
);

-- Reviews Table
CREATE TABLE reviews
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    book_id     INT  NOT NULL,
    user_id     INT  NOT NULL,
    rating      INT  NOT NULL,
    review_text TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
);

-- Book Charts Table
CREATE TABLE book_charts
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    book_id    INT NOT NULL,
    chart_type ENUM('poetry', 'novel', 'play') NOT NULL,
    ranking    INT NOT NULL,
    FOREIGN KEY (book_id) REFERENCES books (id)
);

-- Insert authors into the 'authors' table
INSERT INTO authors (name, surname, birth_year, death_year, bio)
VALUES ('J.R.R.', 'Tolkien', 1892, 1973, 'British author, best known for The Hobbit and The Lord of the Rings.'),
       ('Jane', 'Austen', 1775, 1817, 'English novelist known for her romantic fiction.'),
       ('William', 'Shakespeare', 1564, 1616,
        'English playwright, widely regarded as the greatest writer in the English language.');

-- Insert books into the 'books' table
INSERT INTO books (name, isbn, author_id, literary_genre, fiction_genre)
VALUES ('The Hobbit', '978-0-261-10221-7', 1, 'prose', 'fantasy'),
       ('Pride and Prejudice', '978-1-59308-201-1', 2, 'prose', 'romance'),
       ('Romeo and Juliet', '978-0-7432-7358-5', 3, 'drama', 'none');

-- Insert users
INSERT INTO users (first_name, last_name, user_name, email, password_hash, role)
VALUES ('John', 'Doe', 'johnd', 'john.doe@example.com', '$2y$10$tg7YNx/KbDT5YUkc91GhaOJs2e2pyoafY6b/N5/4exVV8lw7rYxBi',
        'registered_user'),
       ('Jane', 'Smith', 'janes', 'jane.smith@example.com',
        '$2y$10$K0MYzXyjpJS2kZxk5sRr6h8JKd68J/ZZYyFieExhjbfVyi/N3j9Ab', 'registered_user'),
       ('Alice', 'Johnson', 'alicej', 'alice.johnson@example.com',
        '$2y$10$tg7YNx/KbDT5YUkc91GhaOJs2e2pyoafY6b/N5/4exVV8lw7rYxBi', 'admin');