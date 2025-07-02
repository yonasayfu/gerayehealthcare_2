<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { onMounted } from 'vue'

const props = defineProps<{
  form: ReturnType<typeof useForm>
}>()

const emit = defineEmits<{
  (e: 'submit'): void
}>()

onMounted(() => {
  // You can pre-fill or modify fields here if needed
})
</script>

<template>
  <form @submit.prevent="emit('submit')" class="space-y-6">
    <!-- Full Name -->
    <div>
      <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Full Name</label>
      <input
        v-model="form.full_name"
        type="text"
        class="form-input w-full"
        :class="{ 'border-red-500': form.errors.full_name }"
      />
      <div v-if="form.errors.full_name" class="text-red-500 text-sm mt-1">
        {{ form.errors.full_name }}
      </div>
    </div>

    <!-- Date of Birth -->
    <div>
      <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Date of Birth</label>
      <input
        v-model="form.date_of_birth"
        type="date"
        class="form-input w-full"
        :class="{ 'border-red-500': form.errors.date_of_birth }"
      />
      <div v-if="form.errors.date_of_birth" class="text-red-500 text-sm mt-1">
        {{ form.errors.date_of_birth }}
      </div>
    </div>

    <!-- Gender -->
    <div>
      <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Gender</label>
      <select
        v-model="form.gender"
        class="form-select w-full"
        :class="{ 'border-red-500': form.errors.gender }"
      >
        <option value="">Select gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>
      <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">
        {{ form.errors.gender }}
      </div>
    </div>

    <!-- Address -->
    <div>
      <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Address</label>
      <textarea
        v-model="form.address"
        rows="2"
        class="form-textarea w-full"
        :class="{ 'border-red-500': form.errors.address }"
      />
      <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">
        {{ form.errors.address }}
      </div>
    </div>

    <!-- Phone Number -->
    <div>
      <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Phone Number</label>
      <input
        v-model="form.phone_number"
        type="text"
        class="form-input w-full"
        :class="{ 'border-red-500': form.errors.phone_number }"
      />
      <div v-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">
        {{ form.errors.phone_number }}
      </div>
    </div>

    <!-- Email -->
    <div>
      <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Email</label>
      <input
        v-model="form.email"
        type="email"
        class="form-input w-full"
        :class="{ 'border-red-500': form.errors.email }"
      />
      <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
        {{ form.errors.email }}
      </div>
    </div>

    <!-- Emergency Contact -->
    <div>
      <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Emergency Contact</label>
      <textarea
        v-model="form.emergency_contact"
        rows="2"
        class="form-textarea w-full"
        :class="{ 'border-red-500': form.errors.emergency_contact }"
      />
      <div v-if="form.errors.emergency_contact" class="text-red-500 text-sm mt-1">
        {{ form.errors.emergency_contact }}
      </div>
    </div>

    <!-- Geolocation -->
    <div>
      <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-200">Geolocation (Lat,Lng)</label>
      <input
        v-model="form.geolocation"
        type="text"
        placeholder="e.g., 9.012345,38.765432"
        class="form-input w-full"
        :class="{ 'border-red-500': form.errors.geolocation }"
      />
      <div v-if="form.errors.geolocation" class="text-red-500 text-sm mt-1">
        {{ form.errors.geolocation }}
      </div>
    </div>

    <!-- Submit Button -->
    <div class="flex justify-end">
      <button
        type="submit"
        class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium transition"
        :disabled="form.processing"
      >
        {{ form.processing ? 'Saving...' : 'Save Patient' }}
      </button>
    </div>
  </form>
</template>
