<script setup lang="ts">
const props = defineProps<{
  form: any,
  localErrors?: {
    email?: string,
    phone_number?: string,
  }
}>()

const emit = defineEmits(['submit'])
</script>

<template>
  <form @submit.prevent="emit('submit')">
    <div class="border-b border-gray-900/10 pb-12">
      <h2 class="text-base font-semibold text-gray-900 dark:text-white">Patient Information</h2>
      <p class="mt-1 text-sm text-muted-foreground">
        Use accurate and up-to-date details for patient registration.
      </p>

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <!-- Full Name -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Full Name</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.full_name"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.full_name" class="text-red-500 text-sm mt-1">
              {{ form.errors.full_name }}
            </div>
          </div>
        </div>

        <!-- Fayda ID -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Fayda ID</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.fayda_id"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.fayda_id" class="text-red-500 text-sm mt-1">
              {{ form.errors.fayda_id }}
            </div>
          </div>
        </div>

        <!-- Email -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Email</label>
          <div class="mt-2">
            <input
              type="email"
              v-model="form.email"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="props.localErrors?.email" class="text-red-500 text-sm mt-1">
              {{ props.localErrors.email }}
            </div>
            <div v-else-if="form.errors.email" class="text-red-500 text-sm mt-1">
              {{ form.errors.email }}
            </div>
          </div>
        </div>

        <!-- Phone Number -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Phone Number</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.phone_number"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="props.localErrors?.phone_number" class="text-red-500 text-sm mt-1">
              {{ props.localErrors.phone_number }}
            </div>
            <div v-else-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">
              {{ form.errors.phone_number }}
            </div>
          </div>
        </div>

        <!-- Gender -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Gender</label>
          <div class="mt-2">
            <select
              v-model="form.gender"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            >
              <option value="">Select gender</option>
              <option>Male</option>
              <option>Female</option>
            </select>
            <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">
              {{ form.errors.gender }}
            </div>
          </div>
        </div>

        <!-- Date of Birth -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Date of Birth</label>
          <div class="mt-2">
            <input
              type="date"
              v-model="form.date_of_birth"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.date_of_birth" class="text-red-500 text-sm mt-1">
              {{ form.errors.date_of_birth }}
            </div>
          </div>
        </div>

        <!-- Address -->
        <div class="col-span-full">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Address</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.address"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">
              {{ form.errors.address }}
            </div>
          </div>
        </div>

        <!-- Emergency Contact -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Emergency Contact</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.emergency_contact"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.emergency_contact" class="text-red-500 text-sm mt-1">
              {{ form.errors.emergency_contact }}
            </div>
          </div>
        </div>

        <!-- Geolocation -->
        <div class="sm:col-span-3">
          <label class="block text-sm font-medium text-gray-900 dark:text-white">Geolocation</label>
          <div class="mt-2">
            <input
              type="text"
              v-model="form.geolocation"
              placeholder="e.g., 9.012345,38.765432"
              class="block w-full rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-base text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 sm:text-sm"
            />
            <div v-if="form.errors.geolocation" class="text-red-500 text-sm mt-1">
              {{ form.errors.geolocation }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
