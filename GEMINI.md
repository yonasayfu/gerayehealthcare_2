# Project Context for Gemini CLI

This project is a healthcare application built with the following technologies:

*   **Backend**: Laravel (PHP)
*   **Frontend**: Vue.js (TypeScript) with Inertia.js
*   **Build Tool**: Vite
*   **Database**: Likely MySQL or PostgreSQL (based on typical Laravel setups)
*   **Testing Framework**: Pest (PHPUnit for Laravel)

**Development Standards & Principles:**

*   **UI Consistency**: For new modules, especially those related to Patient, Staff, and Inventory, ensure the UI/UX follows the established design patterns and templates found in those existing modules.
*   **Controller Functionality**: Controllers should implement not only standard CRUD operations but also include functionalities for pagination, sorting, data export (e.g., CSV, PDF), "print all" records, and "print current" record views.
*   **Code Style**: Adhere to existing code style and formatting conventions (e.g., Prettier, ESLint).
*   **Modularity**: Favor modular and reusable code components.
*   **Security**: Prioritize secure coding practices.

**General Instructions for Gemini CLI:**

*   When making code changes, always consider the existing architecture and conventions.
*   Before implementing new features or fixing bugs, propose a plan that includes testing strategies.
*   Provide clear explanations for any significant changes or decisions.
*   Utilize the project's existing testing tools (Pest, npm scripts) for verification.

---

# Comprehensive Refactoring Plan: Web Application & Mobile API Foundation

## Overall Goal
To establish a clean, maintainable, and scalable web application while simultaneously building a robust, mobile-friendly API that can be seamlessly consumed by a future Flutter mobile application.

---

# Understanding the Project Architecture: An Onion Perspective

The project architecture aligns with **Onion Architecture**, where dependencies flow inwards.

1.  **Domain Layer (Core - `app/Models`, `app/Enums`)**:
    *   Contains the core business entities and rules (Eloquent Models, Enums).
    *   Independent of all other layers.

2.  **Application Layer (Use Cases - `app/Services`, `app/DTOs`, `app/Events`, `app/Listeners`)**:
    *   Defines the application's use cases and orchestrates business logic.
    *   Depends only on the Domain Layer.
    *   Components: `*Service.php`, `Validation/Rules/*Rules.php`, DTOs, Events, and Listeners.

3.  **Infrastructure Layer (External Concerns - `database`, `config`, Laravel Framework)**:
    *   Provides implementations for external concerns like the database (migrations), file storage, and the framework itself (ORM, routing).

4.  **Presentation Layer (UI/API - `app/Http/Controllers`, `resources/js`, `routes`)**:
    *   Handles user interaction and presents information.
    *   Depends only on the Application Layer.
    *   Components: Controllers, Vue/Inertia components, and route definitions.

## Authentication & Role-Based Access

The application uses a role-based access control (RBAC) system to manage user permissions, built upon `spatie/laravel-permission`.

### User Roles
1.  **Super Admin**: Has unrestricted access to all system features, including user and role management.
2.  **Admin**: Has access to all administrative features except for critical system management tasks (like creating other admins).
3.  **Staff**: Has a limited view tailored to their daily tasks, such as managing their visits, availability, and assigned tasks.

### How It Works
*   **Login & Redirection (`routes/web.php`)**: A single, unified `/dashboard` route handles post-login redirection. It checks the user's role and directs them to the appropriate dashboard controller (`AdminDashboardController` or `StaffDashboardController`).
*   **Dynamic Sidebar (`AppSidebar.vue`)**: The sidebar is the primary navigation hub. It dynamically renders navigation links based on the logged-in user's role and permissions. The `mainNavItems` computed property filters the available navigation groups, ensuring that users only see the modules and features they are authorized to access.

## Workflow Walkthrough: Creating a Patient

1.  **Frontend (`Create.vue`)**: User submits a form, sending a POST request.
2.  **Routing (`routes/web.php`)**: Request is directed to `PatientController@store`.
3.  **Controller (`PatientController` -> `BaseController`)**:
    *   The `BaseController` uses the injected `PatientRules` class to validate the request.
    *   If validation fails, it redirects back with errors.
    *   If validation passes, it calls the service.
4.  **Service Call (`BaseController` -> `PatientService`)**: The controller calls `$this->service->create($validatedData)`.
5.  **Application Service (`PatientService`)**:
    *   Receives validated data (or a DTO).
    *   Applies core business logic.
    *   Uses the `Patient` model to persist data.
    *   Dispatches any relevant events (e.g., `PatientCreated`).
6.  **Model/Infrastructure (`Patient` Model)**: Eloquent handles the `INSERT` query.
7.  **Response (Controller -> Frontend)**: The controller receives the result from the service and redirects to the index page with a success message.
