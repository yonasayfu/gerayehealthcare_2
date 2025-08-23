<template>
  <Head :title="`Edit Ethiopian Calendar Day: ${ethiopianCalendarDay.gregorian_date}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex justify-start mb-4">
        <Link :href="route('admin.ethiopian-calendar-days.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
          <ArrowLeft class="h-4 w-4" />
          Back to Ethiopian Calendar Days
        </Link>
      </div>
      <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Edit Ethiopian Calendar Day</h2>
        <form @submit.prevent="submit">
          <div class="mb-4">
            <label for="gregorian_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gregorian Date</label>
            <input type="date" id="gregorian_date" v-model="form.gregorian_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
            <div v-if="form.errors.gregorian_date" class="text-red-500 text-sm mt-1">{{ form.errors.gregorian_date }}</div>
          </div>

          <div class="mb-4">
            <label for="ethiopian_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ethiopian Date</label>
            <EthiopianDatePicker
                id="ethiopian_date"
                v-model="form.gregorian_date"
                @update:ethiopianDate="form.ethiopian_date = $event"
            />
            <div v-if="form.errors.ethiopian_date" class="text-red-500 text-sm mt-1">{{ form.errors.ethiopian_date }}</div>
          </div>

          <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
          </div>

          <div class="mb-4">
            <label for="is_holiday" class="flex items-center">
              <input type="checkbox" id="is_holiday" v-model="form.is_holiday" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
              <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Is Holiday</span>
            </label>
            <div v-if="form.errors.is_holiday" class="text-red-500 text-sm mt-1">{{ form.errors.is_holiday }}</div>
          </div>

          <div class="mb-4">
            <label for="region" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Region</label>
            <select id="region" v-model="form.region" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
              <option value="">Select Region</option>
              <option value="Addis Ababa">Addis Ababa</option>
              <option value="Oromia">Oromia</option>
              <option value="Amhara">Amhara</option>
              <option value="Tigray">Tigray</option>
              <option value="SNNPR">SNNPR</option>
              <option value="Sidama">Sidama</option>
              <option value="Somali">Somali</option>
              <option value="Benishangul-Gumuz">Benishangul-Gumuz</option>
              <option value="Gambela">Gambela</option>
              <option value="Harari">Harari</option>
              <option value="Afar">Afar</option>
              <option value="Dire Dawa">Dire Dawa</option>
            </select>
            <div v-if="form.errors.region" class="text-red-500 text-sm mt-1">{{ form.errors.region }}</div>
          </div>
          <div class="mt-6 flex justify-end gap-2">
            <Link :href="route('admin.ethiopian-calendar-days.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
              Cancel
            </Link>
            <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
              Update Day
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';
import EthiopianDatePicker from '@/components/EthiopianDatePicker.vue';
import { watch } from 'vue'; // Import watch

const props = defineProps<{
    ethiopianCalendarDay: {
        id: number;
        gregorian_date: string;
        ethiopian_date: string | null;
        description: string | null;
        is_holiday: boolean;
        region: string | null;
        created_at: string;
        updated_at: string;
    };
}>();

const form = useForm<{
    gregorian_date: string;
    ethiopian_date: string | null;
    description: string | null;
    is_holiday: boolean;
    region: string | null;
}>({
    gregorian_date: '',
    ethiopian_date: '',
    description: '',
    is_holiday: false,
    region: '',
});

watch(() => props.ethiopianCalendarDay, (newVal: typeof props.ethiopianCalendarDay) => {
    if (newVal) {
        form.gregorian_date = newVal.gregorian_date;
        form.ethiopian_date = newVal.ethiopian_date;
        form.description = newVal.description;
        form.is_holiday = newVal.is_holiday;
        form.region = newVal.region;
    }
}, { immediate: true });

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Ethiopian Calendar Days', href: route('admin.ethiopian-calendar-days.index') },
  { title: `Edit ${props.ethiopianCalendarDay.gregorian_date}`, href: route('admin.ethiopian-calendar-days.edit', props.ethiopianCalendarDay.id) },
];

const submit = () => {
    form.put(route('admin.ethiopian-calendar-days.update', props.ethiopianCalendarDay.id));
};
</script>
