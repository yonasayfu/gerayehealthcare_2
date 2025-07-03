<script setup>
const props = defineProps({
  form: Object,
  errors: Object,
  existingPhoto: String,
})

const emit = defineEmits(['file-change'])
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">First Name</label>
      <input
        v-model="form.first_name"
        type="text"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="errors.first_name">{{ errors.first_name }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Last Name</label>
      <input
        v-model="form.last_name"
        type="text"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="errors.last_name">{{ errors.last_name }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input
        v-model="form.email"
        type="email"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="errors.email">{{ errors.email }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Phone</label>
      <input
        v-model="form.phone"
        type="text"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="errors.phone">{{ errors.phone }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Position</label>
      <input
        v-model="form.position"
        type="text"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="errors.position">{{ errors.position }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Department</label>
      <input
        v-model="form.department"
        type="text"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="errors.department">{{ errors.department }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="form.status" class="w-full mt-1 p-2 border rounded-md">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>
      <span class="text-red-500 text-xs" v-if="errors.status">{{ errors.status }}</span>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Hire Date</label>
      <input
        v-model="form.hire_date"
        type="date"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="errors.hire_date">{{ errors.hire_date }}</span>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Photo</label>
      <input
        type="file"
        @change="$emit('file-change', $event)"
        class="w-full mt-1 p-2 border rounded-md"
      />
      <span class="text-red-500 text-xs" v-if="errors.photo">{{ errors.photo }}</span>

      <div v-if="existingPhoto" class="mt-2">
        <img :src="'/' + existingPhoto" alt="Staff Photo" class="h-20 rounded-md shadow" />
      </div>
    </div>
  </div>
</template>
