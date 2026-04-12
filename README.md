# Laravel Learning Management System (LMS)

A robust **REST API** for an educational platform built with **Laravel 12**. The system supports a full instructor–student workflow: creating and managing courses, delivering lectures, handling assignments with file uploads, registering quizzes and exams, facilitating discussions, broadcasting announcements, and tracking per-student academic statistics — all secured via token-based authentication.

---

## Features

###  Authentication
- Register, login, and logout with **Laravel Sanctum** token-based auth
- Tokens expire automatically after **1 month**
- All protected routes require a `Bearer` token

###  Courses
- Create and manage courses with configurable grading percentages (assignments, quizzes, mid-term, final)
- Browse all created courses
- Query filtering support
- Enrolling courses via codes

###  Enrollments
- Students enroll in and withdraw from courses
- Enrollment-aware authorization across all resources

###  Announcements
- Instructors post announcements per course
- Enrolled students receive **automatic email notifications** via a queued job (`PropagateAnnouncement`)

###  Lectures
- Attach lecture content to courses
- **File download** endpoint for lecture materials

###  Discussions & Comments
- Students and instructors create discussions within a course
- Threaded **comments** on each discussion

###  Assignments
- Instructors create assignments with attached files (`AssignmentFile`)
- Students submit work (`StudentSubmission`) with **file uploads** (`SubmissionFile`)
- File **download** endpoints for both assignment files and submission files
- Enrolled students receive **email notifications** when a new assignment is posted (`PropagateAssignment`)

###  Examinations (Quizzes / Mid / Final)
- Instructors record per-student examination grades (typed as `quiz`, `mid`, or `final` via `ExaminationType` enum)
- Students can view their own examination records

###  Student Statistics
- Dedicated endpoint computes a student's **weighted average score** across assignments, quizzes, mid-term, and final exam using each course's configured grading percentages

###  Email Notifications (Queue-Driven)
| Job | Trigger |
|-----|---------|
| `PropagateAnnouncement` | New announcement posted |
| `PropagateAssignment` | New assignment posted |
| `PropagateSubmissionGrade` | Instructor grades a student submission |
| `DeleteAssignmentFiles` | Assignment deleted (cleans up files) |
| `DeleteSubmissionsFiles` | Submission deleted (cleans up files) |

###  API Documentation
- Auto-generated interactive docs powered by **Scribe** — available at `/docs` after setup

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12.x |
| Authentication | Laravel Sanctum 4.x |
| Database | SQLite (default) / MySQL / PostgreSQL |
| Queue / Jobs | Laravel Database Queue |
| Mail | Laravel Mailer (log driver by default) |
| API Docs | Knuckleswtf/Scribe 5.x |
| Testing | Pest 4.x |
| Code Style | Laravel Pint |
| Assets | Vite |

---

## API Overview

All authenticated routes are versioned under `/api/V1/`.

| Resource | Endpoints |
|----------|-----------|
| **Auth** | `POST /api/login`, `POST /api/register`, `POST /api/logout` |
| **Courses** | CRUD + `/stats`, `/announcements`, `/lectures`, `/discussions`, `/assignments`, `/examinations` |
| **Enrollments** | Register, show, delete |
| **Announcements** | Show, update, delete |
| **Lectures** | Show, download file, delete |
| **Discussions** | Show, update, delete + comments |
| **Comments** | Show, update, delete |
| **Assignments** | Show, update, delete + files + submissions |
| **Assignment Files** | Show, download, delete |
| **Submissions** | Show, grade + student submission + submission files |
| **Submission Files** | Show, download, delete |
| **Examinations** | Show, update, delete |
| **Student Stats** | Weighted grade average per course |

---

## Setup

### Prerequisites

- PHP **8.2+**
- Composer
- Node.js & npm
- Git

### Quick Setup (one command)

```bash
git clone https://github.com/zakaria-abuaisha/LMS && cd LMS
composer setup
```

This single command runs: `composer install` → copies `.env` → generates app key → runs migrations → `npm install` → `npm run build`.

### Manual Setup (step by step)

1. **Clone the repository**
   ```bash
   git clone https://github.com/zakaria-abuaisha/LMS
   cd LMS
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure the environment**
   ```bash
   cp .env.example .env
   ```
   > **SQLite (default):** No extra config needed. Laravel will create `database/database.sqlite` automatically on first migration.
   >
   > **MySQL / PostgreSQL:** Uncomment and fill in the `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` variables in `.env`.

4. **Generate the application key**
   ```bash
   php artisan key:generate
   ```

5. **Run database migrations and seeder**
   ```bash
   php artisan migrate --seed
   ```

6. **Install and build frontend assets**
   ```bash
   npm install && npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

   The API will be available at: **`http://localhost:8000`**

### Generating API Documentation

```bash
php artisan scribe:generate
```

Then visit **`http://localhost:8000/docs`** to browse the interactive API reference.

---

## Environment Variables (Key Settings)

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_URL` | `http://localhost` | Base URL of the application |
| `DB_CONNECTION` | `sqlite` | Database driver (`sqlite`, `mysql`, `pgsql`) |
| `QUEUE_CONNECTION` | `database` | Queue driver (use `database` to enable email jobs) |
| `MAIL_MAILER` | `log` | Mail driver — change to `smtp` for real email sending |
| `MAIL_FROM_ADDRESS` | `hello@example.com` | Sender email address |
| `FILESYSTEM_DISK` | `local` | Storage disk for file uploads |

---

## Project Structure

```
app/
├── Enums/              # ExaminationType (quiz, mid, final)
├── Http/
│   ├── Controllers/Api/
│   │   ├── AuthController.php
│   │   └── V1/         # 15 resource controllers
│   ├── Filters/        # Query filter classes
│   ├── Requests/       # Form request validation
│   └── Resources/      # API resource transformers
├── Jobs/               # 5 queued jobs (notifications + file cleanup)
├── Mail/               # 3 Mailable classes
├── Models/             # 12 Eloquent models
├── Policies/           # CoursePolicy, UserPolicy
├── Rules/              # Custom validation rules
└── Traits/             # ApiResponses helper trait
database/
├── migrations/         # 15 migration files
├── factories/          # Model factories for testing
└── seeders/
routes/
└── api.php             # All API routes (versioned under /V1/)
```
