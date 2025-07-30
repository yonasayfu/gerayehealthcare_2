# Project Context for Gemini CLI

This project is a healthcare application built with the following technologies:

*   **Backend**: Laravel (PHP)
*   **Frontend**: Vue.js (TypeScript) with Inertia.js
*   **Build Tool**: Vite
*   **Database**: Likely MySQL or PostgreSQL (based on typical Laravel setups)
*   **Testing Framework**: Pest (PHPUnit for Laravel)

**Development Standards & Principles:**

*   **Test-Driven Development (TDD)**: All new features and bug fixes should follow a TDD approach. Write tests first, then implement the code to make the tests pass.
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
