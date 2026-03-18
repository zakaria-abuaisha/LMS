# Laravel Learning Management System (LMS)

Welcome to the **Laravel Learning Management System (LMS)**! This project is a robust backend API built with Laravel 12 to power an educational platform. It provides the essential endpoints and features needed for instructors to manage educational content and for students to learn interactively.

## Features

This LMS exposes an API (Version 1) handling the core concepts of online education:
- **Courses:** Create, manage, and browse educational courses.
- **Lectures:** Structured modules containing the actual learning material.
- **Enrollments:** Secure registration for students taking specific courses.
- **Discussions:** Interactive Q&A and conversations between students and instructors within a course context.
- **Announcements:** Broadcast important updates to enrolled students.

The API uses **Laravel Sanctum** for secure authentication and authorization natively ensuring that instructors and students only access what they are permitted to.

## Tech Stack
- **Framework:** Laravel 12.x
- **Authentication:** Laravel Sanctum
- **Database:** SQLite (Default) 
- **Assets:** Vite

---

## Setup Pipeline

Follow these steps to clone the project and start running it locally.

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm (for optionally building frontend assets)
- Git

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/zakaria-abuaisha/LMS
   cd LMS
   ```

2. **Install PHP Dependencies**
   Run Composer to install the required Laravel packages:
   ```bash
   composer install
   ```

3. **Configure the Environment**
   Copy the example environment file and configure it:
   ```bash
   cp .env.example .env
   ```
   *Note: By default, the `.env` uses an SQLite database (`DB_CONNECTION=sqlite`). If you wish to use MySQL/PostgreSQL, update your `.env` accordingly.*

4. **Generate Application Key**
   Generate a unique app key to secure your application:
   ```bash
   php artisan key:generate
   ```

5. **Run Database Migrations**
   Create the necessary tables (including Courses, Lectures, Discussions, etc.):
   ```bash
   php artisan migrate
   ```
   *(If you are using SQLite and the `database/database.sqlite` file doesn't exist, Laravel will prompt you to create it).*

6. **Install and Build Frontend Assets (Optional but recommended)**
   If there are frontend assets managed by Vite, install JS packages and build them:
   ```bash
   npm install
   npm run build
   ```

7. **Start the Local Development Server**
   Start the Laravel Artisan server:
   ```bash
   php artisan serve
   ```
   The API will now be accessible at `http://localhost:8000` (e.g., `http://localhost:8000/api/v1/...`).
