# Geraye Healthcare Platform

## Project Context for Gemini CLI

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

## Overall Goal

To establish a clean, maintainable, and scalable web application while simultaneously building a robust, mobile-friendly API that can be seamlessly consumed by a future Flutter mobile application.

## Build Commands

- `npm run dev` - Start development server with Vite
- `npm run build` - Build for production
- `composer dev` - Start all development services (Laravel, Queue, Vite)

## Linting Commands

- `npm run lint` - Lint JavaScript/TypeScript/Vue files with ESLint
- `npm run format` - Format code with Prettier
- `npm run format:check` - Check formatting with Prettier
- `./vendor/bin/pint` - Lint PHP files with Laravel Pint

## Testing Commands

- `composer test` or `php artisan test` - Run all tests
- `php artisan test --filter TestName` - Run a specific test
- `php artisan test tests/Feature/SampleTest.php` - Run tests in a specific file
- `php artisan test --group=groupname` - Run tests in a specific group
