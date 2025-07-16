# Standard Module Implementation Template

This document provides the reusable checklist, file structure, and code templates for creating new CRUD modules.

### Instructions:

1. Choose a name for your new module (e.g., "Visit Service").
2. Replace the placeholders throughout the templates:
    - `[ModuleName]` -> `VisitService` (PascalCase, for class names)
    - `[moduleName]` -> `visitService` (camelCase, for variables)
    - `[module_name_plural]` -> `visit_services` (snake_case, for table names)
    - `[module-name-plural]` -> `visit-services` (kebab-case, for routes/URLs)
    - `[ModuleTitle]` -> `Visit Service` (Title Case, for UI text)

### 1. Development Checklist

| Step | Task | Status | Notes |
| --- | --- | --- | --- |
| **1. Database** | Create migration for `[module_name_plural]`. | ☐ | Ensure all necessary columns and relationships are defined. |
|  | Define `fillable` fields in the `[ModuleName]` model. | ☐ | |
|  | Create a `[ModuleName]Factory` for seeding test data. | ☐ | |
|  | Update `DatabaseSeeder` to call the new seeder. | ☐ | |
| **2. Backend** | Create `[ModuleName]Controller` with full CRUD methods. | ☐ | |
|  | Implement search, sort, and pagination in the `index` method. | ☐ | |
|  | Add `export` method for CSV/PDF. | ☐ | |
|  | Add resource and export routes to `routes/web.php`. | ☐ | Ensure routes are correctly named and protected with appropriate middleware. |
| **3. Frontend** | Create directory: `resources/js/pages/Admin/[ModuleName]s`. | ☐ | |
|  | Create a reusable `Form.vue` component. | ☐ | |
|  | Develop `Index.vue` with table, search, sort, pagination, and actions. | ☐ | |
|  | Develop `Create.vue` to add new records. | ☐ | When handling dates/times, ensure conversion to UTC ISO strings before sending to backend to avoid timezone issues. |
|  | Develop `Edit.vue` to modify existing records. | ☐ | When handling dates/times, ensure conversion to UTC ISO strings before sending to backend to avoid timezone issues. |
|  | Develop `Show.vue` for a read-only detail view. | ☐ | |
| **4. Navigation** | Add `[ModuleTitle]` to the sidebar in `AppSidebar.vue`. | ☐ | Ensure correct route name and permission are applied. |
| **5. PDF View** | Create `resources/views/pdf/[module_name_plural].blade.php`. | ☐ | |
| **6. Final Review** | Ensure all styling and UX are consistent. | ☐ | |
| **7. Git** | Commit changes to a dedicated feature branch. | ☐ | |

### 2. File Structure

```
/app
└── /Http
    └── /Controllers
        └── /Admin
            └── [ModuleName]Controller.php
└── /Models
    └── [ModuleName].php
/database
└── /factories
    └── [ModuleName]Factory.php
└── /migrations
    └── ..._create_[module_name_plural]_table.php
└── /seeders
    └── [ModuleName]Seeder.php
    └── DatabaseSeeder.php (updated)
/resources
└── /js
    └── /pages
        └── /Admin
            └── /[ModuleName]s
                ├── Index.vue
                ├── Create.vue
                ├── Edit.vue
                ├── Show.vue
                └── Form.vue
└── /views
    └── /pdf
        └── [module_name_plural].blade.php
/routes
└── web.php (updated)

```

### 3. Code Templates

### **Migration**

`..._create_[module_name_plural]_table.php`

```
Schema::create('[module_name_plural]', function (Blueprint $table) {
    $table->id();
    // Add your columns here
    // Example: $table->string('name');
    // Example: $table->foreignId('patient_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});

```

### **Model**

`app/Models/[ModuleName].php`

```
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class [ModuleName] extends Model
{
    use HasFactory;
    protected $fillable = [
        // Add your fillable fields here
    ];

    // Define relationships here
    // public function patient() { return $this->belongsTo(Patient::class); }
}

```

### **Controller**

`app/Http/Controllers/Admin/[ModuleName]Controller.php`

```
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\[ModuleName];
use Illuminate\Http\Request;
use Inertia\Inertia;
// ... other necessary imports like Pdf, Response

class [ModuleName]Controller extends Controller
{
    public function index(Request $request) { /* ... Search, Sort, Paginate ... */ }
    public function create() { /* ... Return Create view with necessary data ... */ }
    public function store(Request $request) { /* ... Validate and create record ... */ }
    public function show([ModuleName] $[moduleName]) { /* ... Load relations and return Show view ... */ }
    public function edit([ModuleName] $[moduleName]) { /* ... Return Edit view with record and data ... */ }
    public function update(Request $request, [ModuleName] $[moduleName]) { /* ... Validate and update record ... */ }
    public function destroy([ModuleName] $[moduleName]) { /* ... Delete record ... */ }
    public function export(Request $request) { /* ... Handle CSV/PDF export ... */ }
}

```

### **Routes**

`routes/web.php` (inside the authenticated dashboard group)

```
// [ModuleTitle] Module
Route::get('[module-name-plural]/export', [[ModuleName]Controller::class, 'export'])->name('[module-name-plural].export');
Route::resource('[module-name-plural]', [ModuleName]Controller::class);

```

### **Vue Component Descriptions**

- **Vue Index Page (`Index.vue`)**: Contains the main table view. Includes props for `[module_name_plural]` and `filters`. Has logic for search, sort, pagination, destroy, export, and print.
- **Vue Form Component (`Form.vue`)**: The reusable form component. Accepts a `form` object and data for dropdowns as props. Emits a `submit` event.
- **Vue** Create/Edit/Show **Pages**:
    - `Create.vue`: Imports `Form.vue`, initializes an empty `useForm` object, and calls `form.post()` on submit.
    - `Edit.vue`: Imports `Form.vue`, initializes `useForm` with the `[moduleName]` prop data, and calls `form.put()` on submit.
    - `Show.vue`: Does not use the form. Displays record details in a read-only card layout.

### 4. UI/UX Style Guide

- **Layout**: Main content within a `p-6` space-y-6 div.
- **Headers**: Use a `rounded-lg bg-muted/40 p-4 shadow-sm` card with an `h1` (text-xl) and a `p` (text-sm text-muted-foreground).
- **Buttons**:
    - **Primary Action (Add/Save)**: `bg-green-600 hover:bg-green-700 text-white`.
    - **Secondary (Export/Print)**: `bg-gray-100 hover:bg-gray-200` with dark mode variants.
    - **Cancel**: A simple bordered button.
- **Tables**: Use `w-full text-left`, with a `bg-gray-100` header. Rows should have a `hover:bg-gray-50` effect.
- **Icons**: Use `lucide-vue-next` consistently with `h-4 w-4` classes for actions.
- **Forms**: Use a `rounded-lg` border bg-white p-6 shadow-sm card to contain the `<Form>` component. Inputs should have consistent border, background, and focus ring styles.
