<template>
    <AppLayout title="View Ethiopian Calendar Day">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                View Ethiopian Calendar Day
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="ethiopianCalendarDay" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Day Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>Gregorian Date:</strong> {{ ethiopianCalendarDay.gregorian_date }}</p>
                            <p><strong>Ethiopian Date:</strong> {{ ethiopianCalendarDay.ethiopian_date }}</p>
                            <p><strong>Description:</strong> {{ ethiopianCalendarDay.description }}</p>
                            <p><strong>Is Holiday:</strong> {{ ethiopianCalendarDay.is_holiday ? 'Yes' : 'No' }}</p>
                            <p><strong>Region:</strong> {{ ethiopianCalendarDay.region }}</p>
                        </div>
                        <div>
                            <p><strong>Created At:</strong> {{ new Date(ethiopianCalendarDay.created_at).toLocaleString() }}</p>
                            <p><strong>Updated At:</strong> {{ new Date(ethiopianCalendarDay.updated_at).toLocaleString() }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('admin.ethiopian-calendar-days.edit', ethiopianCalendarDay.id)" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Edit Day
                        </Link>
                    </div>
                </div>
                <div v-else class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <p>Loading Ethiopian Calendar Day details...</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    ethiopianCalendarDay: Object,
});

console.log('EthiopianCalendarDay prop in Show.vue:', props.ethiopianCalendarDay);
</script>
