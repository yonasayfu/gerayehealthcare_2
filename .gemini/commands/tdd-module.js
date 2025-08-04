module.exports = async function (args) {
  const moduleName = args[0];
  if (!moduleName) {
    console.log("Usage: /tdd-module <ModuleName>");
    console.log("Example: /tdd-module VisitService");
    return;
  }

  const lowerCaseModuleName = moduleName.toLowerCase();
  const pluralModuleName = lowerCaseModuleName + 's'; // Simple pluralization

  console.log(`Starting Clean Architecture module creation for: ${moduleName}`);

  try {
    // 1. Scaffold Model, Migration, Factory, Seeder, and Controller
    console.log(`Scaffolding core components for ${moduleName}...`);
    await default_api.run_shell_command({
      command: `php artisan make:model ${moduleName} -mfsc`,
      description: `Scaffold Model, Migration, Factory, Seeder, and Controller for ${moduleName}`
    });

    // 2. Create Service Layer directories and files
    console.log(`Creating Service and Validation Rules for ${moduleName}...`);
    const serviceDir = `/Users/yonassayfu/VSProject/gerayehealthcare/app/Services/${moduleName}`;
    const validationRulesDir = `/Users/yonassayfu/VSProject/gerayehealthcare/app/Services/Validation/Rules`;
    
    await default_api.run_shell_command({
      command: `mkdir -p ${serviceDir}`,
      description: `Create directory for ${moduleName} Service`
    });
     await default_api.run_shell_command({
      command: `mkdir -p ${validationRulesDir}`,
      description: `Create directory for Validation Rules`
    });

    const serviceContent = `<?php
namespace App\\Services\\${moduleName};

use App\\Services\\BaseService;
use App\\Models\\${moduleName};

class ${moduleName}Service extends BaseService
{
    public function __construct(${moduleName} $model)
    {
        parent::__construct($model);
    }

    // Add module-specific business logic here...
}
`;
    await default_api.write_file({
      file_path: `${serviceDir}/${moduleName}Service.php`,
      content: serviceContent
    });

    const rulesContent = `<?php
namespace App\\Services\\Validation\\Rules;

class ${moduleName}Rules
{
    public static function store(): array
    {
        return [
            // Add validation rules for storing a new ${moduleName}
        ];
    }

    public static function update(): array
    {
        return [
            // Add validation rules for updating a ${moduleName}
        ];
    }
}
`;
    await default_api.write_file({
      file_path: `${validationRulesDir}/${moduleName}Rules.php`,
      content: rulesContent
    });

    // 3. Create Vue components
    console.log(`Creating Vue components for ${moduleName}...`);
    const vueComponentPath = `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/Pages/Admin/${moduleName}s`;
    await default_api.run_shell_command({
      command: `mkdir -p ${vueComponentPath}`,
      description: `Create directory for Vue components for ${moduleName}`
    });

    const vueIndexContent = `<template>
  <h1 class="text-2xl font-bold mb-4">${moduleName} Index</h1>
</template>
<script setup lang="ts"></script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Index.vue`,
      content: vueIndexContent
    });

    const vueCreateContent = `<template>
  <h1 class="text-2xl font-bold mb-4">Create New ${moduleName}</h1>
</template>
<script setup lang="ts"></script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Create.vue`,
      content: vueCreateContent
    });

    const vueEditContent = `<template>
  <h1 class="text-2xl font-bold mb-4">Edit ${moduleName}</h1>
</template>
<script setup lang="ts"></script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Edit.vue`,
      content: vueEditContent
    });

    const vueShowContent = `<template>
  <h1 class="text-2xl font-bold mb-4">${moduleName} Details</h1>
</template>
<script setup lang="ts"></script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Show.vue`,
      content: vueShowContent
    });

    const vueFormContent = `<template>
  <div class="p-4 border rounded-lg">
    <h2 class="text-xl font-semibold mb-2">${moduleName} Form</h2>
  </div>
</template>
<script setup lang="ts"></script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Form.vue`,
      content: vueFormContent
    });

    console.log(`
✅ Clean Architecture module scaffolding for '${moduleName}' is complete.

Next Steps:
1.  **Migration**: Define the schema in the new migration file in \`database/migrations/\`.
2.  **Model**: Configure fillable fields and relationships in \`app/Models/${moduleName}.php\`.
3.  **Validation**: Add rules to \`app/Services/Validation/Rules/${moduleName}Rules.php\`.
4.  **Controller**: Refactor \`app/Http/Controllers/Admin/${moduleName}Controller.php\` to extend BaseController and inject the service/rules.
5.  **Routes**: Add \`Route::resource('/${pluralModuleName}', ...)\` to \`routes/web.php\`.
6.  **Frontend**: Build out the UI in the newly created Vue components in \`resources/js/Pages/Admin/${moduleName}s/\`.
7.  **Sidebar**: Add a link to the new module in the application's sidebar.
8.  **Migrate**: Run \`php artisan migrate\`.

Refer to the 'Clean Architecture Module Implementation Guide' in your project for detailed templates and instructions.
`);

  } catch (error) {
    console.error(`Error creating module: ${error.message}`);
  }
};