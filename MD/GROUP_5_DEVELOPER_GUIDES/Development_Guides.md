
# Development Guides: Geraye Health Care

This document provides a collection of guides for developers working on the Geraye Health Care project.

## Developer Tools Mastery

To be an effective developer on this project, it is important to master the following tools:

*   **Git:** A version control system for tracking changes to the codebase.
*   **Docker:** A containerization platform for creating and running isolated development environments.
*   **Laravel Sail:** A command-line interface for interacting with the Laravel development environment.
*   **PHPStorm:** An integrated development environment (IDE) for PHP development.
*   **Vue Devtools:** A browser extension for debugging Vue.js applications.

## Adding a New Module

To add a new module to the project, follow these steps:

1.  **Create a new branch:** Create a new Git branch for your module.
2.  **Create the database migration:** Create a new database migration to create the necessary tables for your module.
3.  **Create the model:** Create a new Eloquent model to interact with the database table.
4.  **Create the DTOs:** Create the data transfer objects (DTOs) for your module.
5.  **Create the service:** Create a new service to contain the business logic for your module.
6.  **Create the controller:** Create a new controller to handle HTTP requests for your module.
7.  **Create the views:** Create the views for your module using Vue.js and Inertia.js.
8.  **Create the routes:** Create the routes for your module in the `routes/web.php` file.
9.  **Write tests:** Write tests for your module to ensure its quality and reliability.

## Project Roadmap Workflow

The project roadmap is managed using a Kanban board. The workflow is as follows:

1.  **Backlog:** New ideas and feature requests are added to the backlog.
2.  **To Do:** Tasks that are ready to be worked on are moved to the To Do column.
3.  **In Progress:** When a developer starts working on a task, they move it to the In Progress column.
4.  **Done:** When a task is complete, it is moved to the Done column.

This document provides a high-level overview of the development guides for the Geraye Health Care project. For more detailed information, please refer to the source code and other documentation in this repository.
