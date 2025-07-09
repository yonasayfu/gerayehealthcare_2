<script setup lang="ts">
import { Trash2 } from 'lucide-vue-next'; // Import the icon

const props = defineProps<{
  form: any,
  errors: any,
  existingPhoto?: string | null // Can be a string path or null
}>()

const emit = defineEmits(['file-change', 'remove-photo']) // Define emits
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">First Name</label>
      <input
        v-model="props.form.first_name"
        type="text"
        placeholder="First Name"
        required
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.first_name">{{ props.errors.first_name }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Last Name</label>
      <input
        v-model="props.form.last_name"
        type="text"
        placeholder="Last Name"
        required
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.last_name">{{ props.errors.last_name }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input
        v-model="props.form.email"
        type="email"
        placeholder="Email"
        required
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.email">{{ props.errors.email }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Phone</label>
      <input
        v-model="props.form.phone"
        type="text"
        placeholder="Phone"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.phone">{{ props.errors.phone }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Position</label>
      <input
        v-model="props.form.position"
        type="text"
        placeholder="Position"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.position">{{ props.errors.position }}</span>
    </div>
<div>
    <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
    <input id="position" v-model="form.position" type="text" class="mt-1 block w-full ..." />
    <div v-if="form.errors.position" class="text-sm text-red-600 mt-1">{{ form.errors.position }}</div>
</div>

<div>
    <label for="hourly_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hourly Rate ($)</label>
    <input id="hourly_rate" v-model="form.hourly_rate" type="number" step="0.01" min="0" class="mt-1 block w-full ..." />
    <div v-if="form.errors.hourly_rate" class="text-sm text-red-600 mt-1">{{ form.errors.hourly_rate }}</div>
</div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Department</label>
      <input
        v-model="props.form.department"
        type="text"
        placeholder="Department"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.department">{{ props.errors.department }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="props.form.status" class="w-full mt-1 p-2 border rounded-md">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>
      <span class="text-red-500 text-xs" v-if="props.errors.status">{{ props.errors.status }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Hire Date</label>
      <input
        v-model="props.form.hire_date"
        type="date"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.hire_date">{{ props.errors.hire_date }}</span>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Photo</label>
      <input
        type="file"
        @change="emit('file-change', $event)"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.photo">{{ props.errors.photo }}</span>

      <div v-if="props.existingPhoto" class="mt-2 flex items-center gap-4">
        <img :src="'/storage/' + props.existingPhoto" alt="Staff Photo" class="h-20 w-20 object-cover rounded-md shadow" />
        <button
          type="button"
          @click="emit('remove-photo')"
          class="inline-flex items-center gap-1 text-sm px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition"
          title="Remove Photo"
        >
          <Trash2 class="h-4 w-4" /> Remove Current Photo
        </button>
      </div>
      <div v-else class="mt-2 text-sm text-gray-500">
        No photo uploaded.
      </div>
    </div>
  </div>
</template>