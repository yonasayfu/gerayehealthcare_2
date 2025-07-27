<script setup lang="ts">
import { Trash2 } from 'lucide-vue-next';

defineProps<{
  form: any,
  existingPhoto?: string | null
}>()
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- First Name -->
    <div>
      <label class="block text-sm font-medium text-gray-700">First Name</label>
      <input
        v-model="form.first_name"
        type="text"
        placeholder="First Name"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.first_name">{{ form.errors.first_name }}</span>
    </div>

    <!-- Last Name -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Last Name</label>
      <input
        v-model="form.last_name"
        type="text"
        placeholder="Last Name"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.last_name">{{ form.errors.last_name }}</span>
    </div>

    <!-- Email -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input
        v-model="form.email"
        type="email"
        placeholder="Email"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.email">{{ form.errors.email }}</span>
    </div>

    <!-- Phone -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Phone</label>
      <input
        v-model="form.phone"
        type="text"
        placeholder="Phone"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.phone">{{ form.errors.phone }}</span>
    </div>

    <!-- Position -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Position</label>
      <input
        v-model="form.position"
        type="text"
        placeholder="Position"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.position">{{ form.errors.position }}</span>
    </div>

    <!-- Hourly Rate -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Hourly Rate ($)</label>
      <input
        v-model="form.hourly_rate"
        type="number"
        step="0.01"
        min="0"
        placeholder="e.g., 50.00"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.hourly_rate">{{ form.errors.hourly_rate }}</span>
    </div>

    <!-- Department -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Department</label>
      <input
        v-model="form.department"
        type="text"
        placeholder="Department"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.department">{{ form.errors.department }}</span>
    </div>

    <!-- Status -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="form.status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.status">{{ form.errors.status }}</span>
    </div>

    <!-- Hire Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Hire Date</label>
      <input
        v-model="form.hire_date"
        type="date"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.hire_date">{{ form.errors.hire_date }}</span>
    </div>

    <!-- Photo Upload -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Photo</label>
      <input
        type="file"
        @input="form.photo = $event.target.files[0]"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.photo">{{ form.errors.photo }}</span>

      <div v-if="existingPhoto && !form.photo" class="mt-2">
        <img :src="'/storage/' + existingPhoto" alt="Current Staff Photo" class="h-20 w-20 object-cover rounded-md shadow" />
      </div>
    </div>
  </div>
</template>