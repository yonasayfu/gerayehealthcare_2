# Contributing to Geraye Healthcare Platform

This document outlines the guidelines and workflow for contributing to the Geraye Healthcare Platform.

## Standard Collaborative Workflow

This document outlines the mandatory process for all development tasks.

1.  **Analyze & Plan**: First, the AI agent must think through the problem, read the relevant codebase and project documentation, and write a detailed, step-by-step plan.
2.  **Present Checklist**: The plan must be presented as a clear checklist of tasks (a to-do list).
3.  **User Verification**: **No code will be generated until the user has reviewed and explicitly approved the plan.** This is a critical checkpoint.
4.  **Iterative Development**: We will then proceed through the checklist items one by one.
5.  **Explain Every Step**: Each response containing code must be accompanied by a high-level explanation of the changes made and the purpose of the new code.
6.  **Simplicity & Clarity**: Every task and code change must be kept as simple and focused as possible. We will avoid making massive or complex changes in a single step.
7.  **Final Review**: At the end of a feature's development, the AI will provide a summary of the changes made and confirm the completion of all checklist items.
8.  **Documentation Update**: After significant development progress or feature completion, the AI will update relevant `.md` files (e.g., `PROJECT_ROADMAP.md`, `DATABASE_SCHEMA.md`, `Routes.md`, `AppSidebar.md`) to reflect the current state for easy understanding by other AI agents.
9.  **Module Summary for Documentation**: After a feature is complete, the AI will provide a concise summary of the module's functionality, purpose, and key features. This is for your records and for creating end-user documentation and training materials.

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

## DRY Validation Architecture Implementation

### Overview

This document outlines the implementation of a DRY (Don't Repeat Yourself) validation architecture for the Laravel application. The previous approach used separate Form Request classes for store and update operations, leading to significant code duplication and maintenance overhead. This new architecture centralizes validation rules while maintaining context awareness.

### Architecture Components

#### 1. BaseResourceRules (Base Class)

A base class that all resource-specific rule classes extend. It provides common functionality and structure for defining validation rules.

#### 2. Resource-Specific Rules Classes

Each resource (User, Patient, Staff, etc.) has a dedicated Rules class that defines validation rules for both store and update operations:

- `store()`: Returns validation rules for creating new records
- `update($model)`: Returns validation rules for updating existing records

Example:
```php
// app/Services/Validation/Rules/UserRules.php
class UserRules extends BaseResourceRules
{
    public static function store(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }
    
    public static function update($user): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
        ];
    }
}
```

#### 3. BaseResourceRequest (Base Class)

A base Form Request class that provides the framework for context-aware validation. It determines whether the request is for storing or updating a resource and delegates to the appropriate rules.

#### 4. Resource-Specific Request Classes

Each resource has a single unified Form Request class that extends BaseResourceRequest:

- Uses the resource-specific Rules class
- Automatically determines context (store vs update)
- Delegates to the appropriate rules method

Example:
```php
// app/Http/Requests/UserRequest.php
class UserRequest extends BaseResourceRequest
{
    protected static string $rulesService = UserRules::class;
    
    protected function getResourceRouteParameter(): string
    {
        return 'user';
    }
}
```

### Implementation Benefits

1.  **Elimination of Duplication**: Removed ~80% of validation code duplication across the codebase
2.  **Centralized Rule Management**: All validation rules for a resource are now in one place
3.  **Easy Maintenance**: Changes to validation rules only need to be made in one location
4.  **Context Awareness**: Clean separation of store vs update rules within the same class
5.  **Consistent Implementation**: Every controller now follows the same validation pattern
6.  **Improved Readability**: Controllers are now cleaner and more focused on business logic

### Resources Covered

The DRY validation architecture has been implemented for the following resources:

- Users, Patients, Staff, Services
- Invoices, Tasks, Events, Visits
- Inventory items, transactions, requests, maintenance records, alerts
- Marketing campaigns, tasks, leads, platforms, analytics, budgets
- Insurance claims, policies, companies, employee records
- Corporate clients, suppliers
- Calendar days, exchange rates, eligibility criteria
- Landing pages, event participants, recommendations, broadcasts
- Staff payouts, availability, leave requests
- User-specific resources (MyAvailability, MyVisit)

### Usage in Controllers

Controllers now use a single Form Request class for both store and update operations:

```php
// UserController.php
public function store(UserRequest $request)
{
    // Validation is automatically handled
    $user = User::create($request->validated());
    return response()->json($user, 201);
}

public function update(UserRequest $request, User $user)
{
    // Validation is automatically handled with update rules
    $user->update($request->validated());
    return response()->json($user);
}
```

### Migration from Previous Architecture

The previous architecture used separate StoreXRequest and UpdateXRequest classes for each resource. This approach was replaced with:

1.  Creating resource-specific Rules classes
2.  Creating unified Request classes
3.  Updating controllers to use the new Request classes
4.  Removing obsolete Store/Update Request classes

### Future Maintenance

To add validation rules for a new resource:

1.  Create a new Rules class in `app/Services/Validation/Rules/`
2.  Create a new Request class in `app/Http/Requests/`
3.  Use the new Request class in the appropriate controller

To modify validation rules for an existing resource:

1.  Update the appropriate Rules class
2.  Changes will automatically apply to both store and update operations

This architecture represents a significant improvement in code maintainability and follows Laravel best practices for validation.

## Clean Architecture Module Implementation Guide

This document provides the standard workflow for creating new CRUD modules that align with the project's Clean Architecture principles.

### Placeholders:
- `[ModuleName]` -> `VisitService` (PascalCase, for class names)
- `[moduleName]` -> `visitService` (camelCase, for variables)
- `[module_name_plural]` -> `visit_services` (snake_case, for table names)
- `[module-name-plural]` -> `visit-services` (kebab-case, for routes/URLs)
- `[ModuleTitle]` -> `Visit Service` (Title Case, for UI text)

---

### Development Workflow

#### Step 1: Scaffold Core Components
This single command creates the Model, Migration, Factory, Seeder, and a resource Controller.

```bash
php artisan make:model [ModuleName] -mfsc
```

#### Step 2: Scaffold Application & Presentation Layers
This step requires creating the necessary service and validation rule files. You can use the `/tdd-module [ModuleName]` command to automate this and the Vue file creation.

- **Create Service:** `app/Services/[ModuleName]/[ModuleName]Service.php`
- **Create Validation Rules:** `app/Services/Validation/Rules/[ModuleName]Rules.php`
- **Create Vue Components:** `resources/js/Pages/Admin/[ModuleName]s/` (Index, Create, Edit, Show, Form)

---

### Implementation Checklist

| Layer | Step | Task | Status |
| :--- | :--- | :--- | :--- |
| **1. Infrastructure** | **Migration** | Define the table schema in the generated migration file in `database/migrations/`. | ☐ |
| | **Factory** | Define the model's default state in `database/factories/[ModuleName]Factory.php`. | ☐ |
| | **Seeder** | Add logic to `database/seeders/[ModuleName]Seeder.php` and call it from `DatabaseSeeder.php`. | ☐ |
| **2. Domain** | **Model** | Configure `fillable` properties, casts, and relationships in `app/Models/[ModuleName].php`. | ☐ |
| **3. Application** | **Validation** | Implement `store()` and `update()` static methods in `app/Services/Validation/Rules/[ModuleName]Rules.php`. | ☐ |
| | **Service** | Implement business logic in `app/Services/[ModuleName]/[ModuleName]Service.php`. It should extend `BaseService`. | ☐ |
| **4. Presentation** | **Controller** | Refactor the generated `app/Http/Controllers/Admin/[ModuleName]Controller.php` to be a thin controller. It should extend `BaseController` and use the Service and Validation Rules. | ☐ |
| | **Routes** | Add the resource route to `routes/web.php` inside the authenticated group. | ☐ |
| | **Frontend** | Develop the Vue components in `resources/js/Pages/Admin/[ModuleName]s/`. | ☐ |
| | | `Form.vue`: Build the reusable form fields. | ☐ |
| | | `Index.vue`: Build the data table, filters, and actions. | ☐ |
| | | `Create.vue`: Implement the create form logic. | ☐ |
| | | `Edit.vue`: Implement the edit form logic. | ☐ |
| | | `Show.vue`: Implement the read-only detail view. | ☐ |
| **5. Finalizing** | **Navigation** | Add a link to the new module in the main sidebar navigation. | ☐ |
| | **Migration** | Run the migration: `php artisan migrate`. | ☐ |

---

### Code Templates

#### **Validation Rules**
`app/Services/Validation/Rules/[ModuleName]Rules.php`
```php
<?php
namespace App\\\Services\\Validation\\Rules;

class [ModuleName]Rules
{
    public static function store(): array
    {
        return [
            // 'name' => 'required|string|max:255',
        ];
    }

    public static function update(): array
    {
        return [
            // 'name' => 'sometimes|string|max:255',
        ];
    }
}
```

#### **Service**
`app/Services/[ModuleName]/[ModuleName]Service.php`
```php
<?php
namespace App\\Services\\\\[ModuleName];

use App\\\\Services\\BaseService;
use App\\\\Models\\\\[ModuleName];

class [ModuleName]Service extends BaseService
{
    public function __construct([ModuleName] $model)
    {
        parent::__construct($model);
    }

    // Implement module-specific business logic here
}
```

#### **Controller**
`app/Http/Controllers/Admin/[ModuleName]Controller.php`
```php
<?php
namespace App\\\\Http\\\\Controllers\\\\Admin;

use App\\\\Http\\\\Controllers\\\\Base\\\\BaseController;
use App\\\\Services\\\\[ModuleName]\\\\[ModuleName]Service;
use App\\\\Services\\\\Validation\\\\Rules\\\\[ModuleName]Rules;

class [ModuleName]Controller extends BaseController
{
    public function __construct([ModuleName]Service $service)
    {
        parent::__construct($service, [
            'store' => [
                'rules' => [ModuleName]Rules::store(),
            ],
            'update' => [
                'rules' => [ModuleName]Rules::update(),
            ],
        ]);
    }
}
```

#### **Routes**
`routes/web.php`
```php
Route::resource('/[module-name-plural]', App\\\\Http\\\\Controllers\\\\Admin\\\\[ModuleName]Controller::class);
```
