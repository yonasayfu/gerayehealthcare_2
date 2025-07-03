<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import axios from 'axios';

const form = ref({
  full_name: '',
  email: '',
  phone_number: '',
  date_of_birth: '',
  gender: '',
  address: '',
  emergency_contact: '',
  geolocation: '',
});

const emailError = ref('');
const phoneError = ref('');
const submitting = ref(false);

const checkFieldUnique = debounce(async (field: string, value: string, errorRef: any) => {
  if (!value) {
    errorRef.value = '';
    return;
  }

  try {
    const { data } = await axios.get(route('patients.validate-field'), {
      params: { field, value }
    });

    if (!data.valid) {
      errorRef.value = `${field === 'email' ? 'Email' : 'Phone number'} already exists.`;
    } else {
      errorRef.value = '';
    }
  } catch (e) {
    errorRef.value = 'Validation failed. Please try again.';
  }
}, 500);

watch(() => form.value.email, (val) => {
  checkFieldUnique('email', val, emailError);
});

watch(() => form.value.phone_number, (val) => {
  checkFieldUnique('phone_number', val, phoneError);
});

function submit() {
  if (emailError.value || phoneError.value) return;

  submitting.value = true;

  router.post(route('patients.store'), form.value, {
    onFinish: () => submitting.value = false
  });
}
</script>

<template>
  <div class="max-w-2xl mx-auto p-6 bg-white dark:bg-gray-900 shadow rounded-lg">
    <h1 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Add New Patient</h1>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300">Full Name</label>
        <input v-model="form.full_name" type="text" class="form-input w-full" required />
      </div>

      <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300">Email</label>
        <input v-model="form.email" type="email" class="form-input w-full" />
        <p v-if="emailError" class="text-red-600 text-sm mt-1">{{ emailError }}</p>
      </div>

      <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300">Phone Number</label>
        <input v-model="form.phone_number" type="text" class="form-input w-full" />
        <p v-if="phoneError" class="text-red-600 text-sm mt-1">{{ phoneError }}</p>
      </div>

      <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300">Date of Birth</label>
        <input v-model="form.date_of_birth" type="date" class="form-input w-full" />
      </div>

      <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300">Gender</label>
        <select v-model="form.gender" class="form-input w-full">
          <option value="">Select gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>

      <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300">Address</label>
        <input v-model="form.address" type="text" class="form-input w-full" />
      </div>

      <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300">Emergency Contact</label>
        <input v-model="form.emergency_contact" type="text" class="form-input w-full" />
      </div>

      <div>
        <label class="block text-sm text-gray-700 dark:text-gray-300">Geolocation</label>
        <input v-model="form.geolocation" type="text" class="form-input w-full" />
      </div>

      <div class="pt-4">
        <button
          type="submit"
          class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-md"
          :disabled="submitting || emailError || phoneError"
        >
          Save Patient
        </button>
      </div>
    </form>
  </div>
</template>