<template>
    <AppLayout title="View Exchange Rate">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                View Exchange Rate
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rate Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>Currency Code:</strong> {{ exchangeRate.currency_code }}</p>
                            <p><strong>Rate to ETB:</strong> {{ exchangeRate.rate_to_etb }}</p>
                            <p><strong>Source:</strong> {{ exchangeRate.source }}</p>
                            <p><strong>Date Effective:</strong> {{ new Date(exchangeRate.date_effective).toLocaleDateString() }}</p>
                        </div>
                        <div>
                            <p><strong>Created At:</strong> {{ new Date(exchangeRate.created_at).toLocaleString() }}</p>
                            <p><strong>Updated At:</strong> {{ new Date(exchangeRate.updated_at).toLocaleString() }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('admin.exchange-rates.edit', exchangeRate.id)" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Edit Rate
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    exchangeRate: Object,
});
</script>
