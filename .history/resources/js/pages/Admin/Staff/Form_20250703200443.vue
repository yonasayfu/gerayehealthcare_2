<script setup lang="ts">
const props = defineProps<{
  form: any,
  errors: any,
  existingPhoto?: string | null
}>()
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- First Name -->
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

    <!-- Last Name -->
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

    <!-- Email -->
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

    <!-- Phone -->
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

    <!-- Position -->
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

    <!-- Department -->
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

    <!-- Status -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="props.form.status" class="w-full mt-1 p-2 border rounded-md">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>
      <span class="text-red-500 text-xs" v-if="props.errors.status">{{ props.errors.status }}</span>
    </div>

    <!-- Hire Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Hire Date</label>
      <input
        v-model="props.form.hire_date"
        type="date"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.hire_date">{{ props.errors.hire_date }}</span>
    </div>

    <!-- Photo Upload -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Photo</label>
      <input
        type="file"
        @change="$emit('file-change', $event)"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="props.errors.photo">{{ props.errors.photo }}</span>

      <div v-if="props.existingPhoto" class="mt-2">
        <img :src="'/' + props.existingPhoto" alt="Staff Photo" class="h-20 rounded-md shadow" />
      </div>
    </div>
  </div>
</template>
