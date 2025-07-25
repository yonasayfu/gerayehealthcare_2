<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
  form: any;
  allPermissions: string[];
}>();

const emit = defineEmits(['submit']);

// This creates a local copy of the permissions for the checkboxes.
// This is important so we don't directly modify the prop.
const selectedPermissions = ref([...props.form.permissions]);

// When the checkboxes change, we update the parent form object.
watch(selectedPermissions, (newVal) => {
  props.form.permissions = newVal;
});
</script>

<template>
  <form @submit.prevent="emit('submit')" class="space-y-6">
    <div>
      <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white">Role Name</label>
      <div class="mt-1">
        <input
          type="text"
          id="name"
          v-model="form.name"
          class="block w-full rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800"
          placeholder="e.g., Finance Manager"
        />
        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
      </div>
    </div>

    <div>
      <h3 class="text-sm font-medium text-gray-900 dark:text-white">Assign Permissions</h3>
      <p class="text-sm text-muted-foreground">Select the permissions this role should have.</p>
      <div class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 border-t dark:border-gray-700 pt-4">
        <div v-for="permission in allPermissions" :key="permission" class="flex items-center">
          <input
            :id="`permission-${permission}`"
            type="checkbox"
            :value="permission"
            v-model="selectedPermissions"
            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
          />
          <label :for="`permission-${permission}`" class="ml-3 block text-sm text-gray-700 dark:text-gray-300 capitalize">{{ permission.replace(/ /g, ' ') }}</label>
        </div>
      </div>
       <div v-if="form.errors.permissions" class="text-red-500 text-sm mt-1">{{ form.errors.permissions }}</div>
    </div>
  </form>
</template>
