# CRUSH.md - Development Guide for Agentic Coding Agents

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

## Code Style Guidelines

### Imports
- Group imports in order: external libraries, internal modules, relative imports
- Use absolute paths when possible for internal modules
- Keep imports alphabetized within each group

### Formatting
- Use Prettier for automatic code formatting
- Use 4 spaces for PHP indentation, 2 spaces for JavaScript/TypeScript/Vue
- Maximum line length of 120 characters
- No trailing commas in PHP, but use them in JavaScript/TypeScript

### Types
- Use TypeScript for all Vue components and JavaScript files
- Define explicit types for function parameters and return values
- Use interfaces for complex data structures

### Naming Conventions
- Use PascalCase for Vue components and PHP classes
- Use camelCase for JavaScript/TypeScript variables and functions
- Use snake_case for database tables and PHP function names
- Use UPPERCASE for constants

### Error Handling
- Use Laravel's validation for request data
- Implement proper try/catch blocks for external API calls
- Log errors with appropriate context for debugging
- Display user-friendly error messages in the UI

## Project Structure
- Backend: Laravel PHP in `/app`, `/routes`, `/database`
- Frontend: Vue.js/TypeScript in `/resources/js`
- Tests: Pest PHP tests in `/tests`

## Additional Notes
- Follow Test-Driven Development (TDD) approach
- Controllers should implement CRUD operations plus pagination, sorting, and export functionality
- Maintain UI consistency with existing Patient, Staff, and Inventory modules
- Prioritize secure coding practices