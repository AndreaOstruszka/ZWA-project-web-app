# ZWA-project-web-app

**Term Project: Creating a Simple Dynamic Web App**

Academic year 2024/2025, winter term

Course: Web Application Fundamentals (ZWA)

Undergraduate Programme: Software Engineering & Technology

Czech Technical University in Prague, Faculty of Electrical Engineering


## Table of Contents
- [Introduction – Project Brief](#introduction--project-brief)
- [Project Overview and Scope](#project-overview-and-scope)
- [Requirements](#requirements)
- [Features](#features)
- [Technologies](#technologies)
- [Installation](#installation)
- [Usage](#usage)


## Introduction – Project Brief
Book Nook is a simple web application that allows users to explore a vast collection of books, read and write reviews and keep track of newly released books.
Admins can add or delete books, edit existing books and edit user settings.

## Project Overview and Scope
Website name: Book Nook

Purpose: Like IMDB, but for books.

Features: book reviews, book charts, 

User roles: anonymous user, registered user, admin

## Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- A web server (e.g., Apache, Nginx)
- Composer (for dependency management, if needed)
- A modern web browser (e.g., Chrome, Firefox)

## Features
- Browse books by various genres
- Read and write reviews
- View charts of popular books
- User authentication (login and register)
- User profile management

## Technologies
No frameworks or libraries are allowed in this project. The following technologies are used:

### Backend
- vanilla PHP 8.1 (with the GD 2.3 library for image processing as the only allowed exception)
- MySQL database

### Frontend
- vanilla HTML and CSS
- vanilla JavaScript
- Google Fonts
- Font Awesome


## Installation
Step-by-step instructions on how to set up the project locally.

1. Clone the repository:
    ```bash
    git clone https://github.com/AndreaOstruszka/BookNook.git
    ```

2. Navigate to the project directory:
    ```bash
    cd BookNook
    ```

3. Set up the database:
    - Create a database named `booknook`.
    - Import the SQL file located at `db/booknook.sql` to set up the necessary tables.

4. Configure the database connection:
    - Update the `db_connection.php` file with your database credentials.

5. Start the server:
    - Use a local server environment like XAMPP, WAMP, or MAMP to run the application.

## Usage

- Open your web browser and navigate to `http://localhost/BookNook`.
- Register a new account or log in with an existing account.
- Browse books, read reviews, and explore the features of the application.