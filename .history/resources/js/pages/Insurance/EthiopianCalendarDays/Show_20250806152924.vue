<template>
    <AppLayout title="View Ethiopian Calendar Day">
<template>
  <Head :title="`Ethiopian Calendar Day: ${ethiopianCalendarDay.gregorian_date}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 space-y-6">
      <div class="flex justify-start mb-4">
        <Link :href="route('admin.ethiopian-calendar-days.index')" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
          <ArrowLeft class="h-4 w-4" />
          Back to Ethiopian Calendar Days
        </Link>
      </div>
      <div v-if="ethiopianCalendarDay" class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
        <div class="flex items-center space-x-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Ethiopian Calendar Day Details</h2>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 dark:text-gray-300">
              <p><strong>Gregorian Date:</strong> {{ ethiopianCalendarDay.gregorian_date }}</p>
              <p><strong>Ethiopian Date:</strong> {{ ethiopianCalendarDay.ethiopian_date }}</p>
              <p><strong>Description:</strong> {{ ethiopianCalendarDay.description || 'N/A' }}</p>
              <p><strong>Is Holiday:</strong> {{ ethiopianCalendarDay.is_holiday ? 'Yes' : 'No' }}</p>
              <p><strong>Region:</strong> {{ ethiopianCalendarDay.region || 'N/A' }}</p>
              <p><strong>Created At:</strong> {{ new Date(ethiopianCalendarDay.created_at).toLocaleString() }}</p>
              <p><strong>Updated At:</strong> {{ new Date(ethiopianCalendarDay.updated_at).toLocaleString() }}</p>
            </div>
          </div>
        </div>

        <div class="mt-6 border-t pt-6">
          <Link :href="route('admin.ethiopian-calendar-days.edit', ethiopianCalendarDay.id)" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
            Edit Day
          </Link>
        </div>
      </div>
      <div v-else class="bg-white dark:bg-gray-900 shadow-xl sm:rounded-lg p-6">
        <p>Loading Ethiopian Calendar Day details...</p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';

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

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Ethiopian Calendar Days', href: route('admin.ethiopian-calendar-days.index') },
  { title: props.ethiopianCalendarDay.gregorian_date, href: route('admin.ethiopian-calendar-days.show', props.ethiopianCalendarDay.id) },
];
</script>
