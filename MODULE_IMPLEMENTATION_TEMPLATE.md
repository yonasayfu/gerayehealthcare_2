# Clean Architecture Module Implementation Guide

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
| **4. Presentation** | **Controller** | Refactor the generated `app/Http/Controllers/[ModuleName]Controller.php` to be a thin controller. It should extend `BaseController` and use the Service and Validation Rules. | ☐ |
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
namespace App\Services\Validation\Rules;

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
namespace App\Services\[ModuleName];

use App\Services\BaseService;
use App\Models\[ModuleName];

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
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\[ModuleName]\[ModuleName]Service;
use App\Services\Validation\Rules\[ModuleName]Rules;

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
Route::resource('/[module-name-plural]', App\Http\Controllers\Admin\[ModuleName]Controller::class);
```