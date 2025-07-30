module.exports = async function (args) {
  const moduleName = args[0];
  if (!moduleName) {
    console.log("Usage: /tdd-module <ModuleName>");
    console.log("Example: /tdd-module VisitService");
    return;
  }

  const lowerCaseModuleName = moduleName.toLowerCase();
  const pluralModuleName = lowerCaseModuleName + 's'; // Simple pluralization, might need adjustment for irregular plurals

  console.log(`Starting TDD module creation for: ${moduleName}`);

  try {
    // 1. Create database migration
    console.log(`Creating migration for ${pluralModuleName}...`);
    await default_api.run_shell_command({
      command: `php artisan make:migration create_${pluralModuleName}_table`,
      description: `Create migration for ${pluralModuleName}`
    });

    // 2. Create Laravel model
    console.log(`Creating model for ${moduleName}...`);
    await default_api.run_shell_command({
      command: `php artisan make:model ${moduleName}`,
      description: `Create model for ${moduleName}`
    });

    // 3. Create Laravel controller
    console.log(`Creating controller for ${moduleName}Controller...`);
    await default_api.run_shell_command({
      command: `php artisan make:controller ${moduleName}Controller --resource`,
      description: `Create resource controller for ${moduleName}`
    });

    // 4. Create Laravel factory
    console.log(`Creating factory for ${moduleName}...`);
    await default_api.run_shell_command({
      command: `php artisan make:factory ${moduleName}Factory`,
      description: `Create factory for ${moduleName}`
    });

    // 5. Create Laravel seeder
    console.log(`Creating seeder for ${moduleName}...`);
    await default_api.run_shell_command({
      command: `php artisan make:seeder ${moduleName}Seeder`,
      description: `Create seeder for ${moduleName}`
    });

    // 6. Create Vue components (Index, Create, Edit, Show, Form)
    console.log(`Creating Vue components for ${moduleName}...`);
    const vueComponentPath = `/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/Pages/${moduleName}`;
    await default_api.run_shell_command({
      command: `mkdir -p ${vueComponentPath}`,
      description: `Create directory for Vue components for ${moduleName}`
    });

    const vueIndexContent = `<template>
  <h1 class="text-2xl font-bold mb-4">${moduleName} Index</h1>
  <p>This is the index page for ${moduleName}.</p>
</template>

<script setup lang="ts">
// Import necessary modules or define props here
</script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Index.vue`,
      content: vueIndexContent
    });

    const vueCreateContent = `<template>
  <h1 class="text-2xl font-bold mb-4">Create New ${moduleName}</h1>
  <p>This is the create page for ${moduleName}.</p>
</template>

<script setup lang="ts">
// Import necessary modules or define props here
</script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Create.vue`,
      content: vueCreateContent
    });

    const vueEditContent = `<template>
  <h1 class="text-2xl font-bold mb-4">Edit ${moduleName}</h1>
  <p>This is the edit page for ${moduleName}.</p>
</template>

<script setup lang="ts">
// Import necessary modules or define props here
</script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Edit.vue`,
      content: vueEditContent
    });

    const vueShowContent = `<template>
  <h1 class="text-2xl font-bold mb-4">${moduleName} Details</h1>
  <p>This is the show page for ${moduleName}.</p>
</template>

<script setup lang="ts">
// Import necessary modules or define props here
</script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Show.vue`,
      content: vueShowContent
    });

    const vueFormContent = `<template>
  <div class="p-4 border rounded-lg">
    <h2 class="text-xl font-semibold mb-2">${moduleName} Form</h2>
    <p>This is a reusable form component for ${moduleName}.</p>
  </div>
</template>

<script setup lang="ts">
// Define form fields and logic here
</script>
`;
    await default_api.write_file({
      file_path: `${vueComponentPath}/Form.vue`,
      content: vueFormContent
    });


    // 7. Add a placeholder test file
    console.log(`Creating test file for ${moduleName}...`);
    const testContent = `<?php

namespace Tests\\Feature;

use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

class ${moduleName}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_${lowerCaseModuleName}_index_page()
    {
        // TODO: Implement test
        \$response = \$this->get('/${pluralModuleName}');
        \$response->assertStatus(200);
    }

    // Add more tests for create, store, show, edit, update, destroy
}
`;
    await default_api.write_file({
      file_path: `/Users/yonassayfu/VSProject/gerayehealthcare/tests/Feature/${moduleName}Test.php`,
      content: testContent
    });

    console.log(`
TDD module creation process for '${moduleName}' initiated.

Please remember to:
1.  **Run migrations**: 'php artisan migrate' to create the new table.
2.  **Manually add routes**: Add the following to 'routes/web.php' (or 'routes/api.php' if it's an API-only module):
    Route::resource('/${pluralModuleName}', ${moduleName}Controller::class);
3.  **Implement logic**: Fill in the migration details, model relationships, controller logic, and Vue component functionality.
4.  **Write comprehensive tests**: Expand on the placeholder tests in 'tests/Feature/${moduleName}Test.php'.
5.  **Define factory**: Populate 'database/factories/${moduleName}Factory.php' with relevant data definitions.
6.  **Use seeder**: Add calls to 'database/seeders/${moduleName}Seeder.php' in 'database/seeders/DatabaseSeeder.php' to populate your database with test data.
`);

  } catch (error) {
    console.error(`Error creating TDD module: ${error.message}`);
  }
};