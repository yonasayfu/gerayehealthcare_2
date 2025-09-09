# Project: Laravel Mail Catcher

This file tracks the development progress and roadmap for the Laravel Mail Catcher application.

## 1. Project Overview

A standalone Laravel application that functions as a local SMTP server to receive, store, and display emails for development testing purposes.

**Key Features:**
- Local SMTP Server (Email Capture)
- Database Storage (SQLite)
- Web-based UI (Inbox & Email Detail View)
- Renders HTML emails in a sandboxed `<iframe>`
- No authentication required.

## 2. Development Roadmap

The project will be built step-by-step in the following order.

- [x] **Step 0: Setup & Planning**
    - [x] Initial project creation (`composer create-project`)
    - [x] Define project requirements and scenario
    - [x] Create `gemini.md` for progress tracking

- [x] **Step 1: Database Migration**
    - [x] Create the migration for the `emails` table as per the specification.
    - [x] Run the migration to set up the database schema.

- [x] **Step 2: Email Model**
    - [x] Create the `Email` Eloquent model.
    - [x] Configure mass assignment (`$fillable` or `$guarded`).
    - [x] Add casts for JSON columns (`from`, `to`, `cc`, `bcc`, `attachments`).

- [x] **Step 3: Web Routes**
    - [x] Define a route for the inbox page (`/`).
    - [x] Define a route for the email detail page (`/emails/{id}`).

- [x] **Step 4: Controller**
    - [x] Create `EmailController`.
    - [x] Implement the `index()` method to fetch and display all emails.
    - [x] Implement the `show()` method to fetch and display a single email.

- [x] **Step 5: Views (Blade Templates)**
    - [x] Create a main layout file.
    - [x] Create the `inbox.blade.php` view to list emails.
    - [x] Create the `show.blade.php` view to display a single email's details, including the `<iframe>` for the HTML body.

- [x] **Step 6: Email Catcher Service**
    - [x] Set up a basic SMTP server listener.
    - [x] Create a service class (`EmailHandler` or similar) that parses the raw email.
    - [x] Implement logic to store the parsed email in the database using the `Email` model.
    - [x] Create an Artisan command to run the SMTP server.

- [x] **Step 7: Final Touches & Testing**
    - [x] Add basic styling to the views.
    - [x] Manually test the entire workflow as described in the scenario.
    - [x] Review and refactor code.