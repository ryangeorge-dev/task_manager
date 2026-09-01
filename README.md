# PHP Task Manager

A simple web-based task manager created as a school project.

Users can create an account, log in, and manage their personal tasks.

## Features

* User registration and login
* Password hashing with PHP's built-in password functions
* Create, edit, delete, and complete tasks
* Task categories, descriptions, and deadlines
* Filter tasks by all, pending, or completed
* Visual overdue and due-soon deadline indicators
* Tasks are private to each logged-in user

## Technologies Used

* PHP
* MySQL
* HTML
* CSS
* JavaScript
* PDO for database access

## Setup

1. Place the project in a PHP-compatible local server environment such as XAMPP.
2. Create a MySQL database named `task_manager`.
3. Create the required `users` and `tasks` tables.
4. Update `db.php` with your local database credentials if needed.
5. Open `login.php` in your local server.

## Project Structure

* `index.php` — task dashboard
* `task_actions.php` — task create, update, delete, and completion actions
* `login.php` / `register.php` — user authentication
* `init.php` — session and authentication helpers
* `db.php` — database connection
* `script.js` — client-side interface behavior
* `styles.css` — styling
