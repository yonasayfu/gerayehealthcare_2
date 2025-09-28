<script setup lang="ts">
import { Trash2 } from 'lucide-vue-next';
import { ref, watch, onBeforeUnmount } from 'vue';
import InputError from '@/components/InputError.vue'

const props = defineProps<{
  form: any,
  existingPhoto?: string | null,
  departments?: string[],
  positions?: string[]
}>()

// Reactive preview URL for newly selected photo
const previewUrl = ref<string | null>(null);

watch(
  () => props.form.photo,
  (file: File | null) => {
    try {
      if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = null;
      }
      if (file instanceof File) {
        const URLFactory: any = (typeof window !== 'undefined' && (window as any).URL) || (typeof window !== 'undefined' && (window as any).webkitURL);
        if (URLFactory && typeof URLFactory.createObjectURL === 'function') {
          previewUrl.value = URLFactory.createObjectURL(file);
        }
      }
    } catch (e) {
      // Silently ignore preview errors
      previewUrl.value = null;
    }
  }
);

onBeforeUnmount(() => {
  if (previewUrl.value) {
    try { URL.revokeObjectURL(previewUrl.value); } catch {}
    previewUrl.value = null;
  }
});
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
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.first_name" />
    </div>

    <!-- Last Name -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Last Name</label>
      <input
        v-model="form.last_name"
        type="text"
        placeholder="Last Name"
        required
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.last_name" />
    </div>

    <!-- Email -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input
        v-model="form.email"
        type="email"
        placeholder="Email"
        required
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.email" />
    </div>

    <!-- Phone -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Phone</label>
      <input
        v-model="form.phone"
        type="text"
        placeholder="Phone"
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.phone" />
    </div>

    <!-- Position -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Position</label>
      <select v-model="form.position" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option value="">Select position</option>
        <option v-for="pos in positions" :key="pos" :value="pos">{{ pos }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.position" />
    </div>

    <!-- Hourly Rate -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Hourly Rate ($)</label>
      <input
        v-model.number="form.hourly_rate"
        type="number"
        step="0.01"
        min="0"
        placeholder="e.g., 50.00"
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.hourly_rate" />
    </div>

    <!-- Department -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Department</label>
      <select v-model="form.department" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option value="">Select department</option>
        <option v-for="department in departments" :key="department" :value="department">{{ department }}</option>
      </select>
      <InputError class="mt-1" :message="form.errors.department" />
    </div>

    <!-- Status -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select v-model="form.status" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
      </select>
      <InputError class="mt-1" :message="form.errors.status" />
    </div>

    <!-- Hire Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Hire Date</label>
      <input
        v-model="form.hire_date"
        type="date"
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.hire_date" />
    </div>

    <!-- Photo Upload -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Photo</label>
      <input
        type="file"
        accept="image/*"
        @change="form.photo = ($event.target as HTMLInputElement).files ? ($event.target as HTMLInputElement).files[0] : null"
        class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"
      />
      <InputError class="mt-1" :message="form.errors.photo" />

      <!-- Preview newly selected image -->
      <div v-if="form.photo && previewUrl" class="mt-2">
        <img :src="previewUrl" alt="Selected Photo Preview" class="h-24 w-24 object-cover rounded-full shadow border" />
      </div>
      <!-- Fallback to existing photo if no new file selected (Edit page) -->
      <div v-else-if="existingPhoto" class="mt-2">
        <img :src="'/storage/' + existingPhoto" alt="Current Staff Photo" class="h-20 w-20 object-cover rounded-md shadow" />
      </div>
    </div>
  </div>
</template>
