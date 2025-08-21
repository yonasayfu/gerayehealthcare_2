<template>
    <AppLayout title="Edit Exchange Rate">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Exchange Rate
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="currency_code" class="block text-sm font-medium text-gray-700">Currency Code</label>
                            <input type="text" id="currency_code" v-model="form.currency_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <div v-if="form.errors.currency_code" class="text-red-500 text-sm mt-1">{{ form.errors.currency_code }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="rate_to_etb" class="block text-sm font-medium text-gray-700">Rate to ETB</label>
                            <input type="number" id="rate_to_etb" v-model="form.rate_to_etb" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" step="0.0001" required>
                            <div v-if="form.errors.rate_to_etb" class="text-red-500 text-sm mt-1">{{ form.errors.rate_to_etb }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="source" class="block text-sm font-medium text-gray-700">Source</label>
                            <input type="text" id="source" v-model="form.source" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <div v-if="form.errors.source" class="text-red-500 text-sm mt-1">{{ form.errors.source }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="date_effective" class="block text-sm font-medium text-gray-700">Date Effective</label>
                            <input type="date" id="date_effective" v-model="form.date_effective" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <div v-if="form.errors.date_effective" class="text-red-500 text-sm mt-1">{{ form.errors.date_effective }}</div>
                        </div>

                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Update Rate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    exchangeRate: Object,
});

const form = useForm({
    currency_code: props.exchangeRate.currency_code,
    rate_to_etb: props.exchangeRate.rate_to_etb,
    source: props.exchangeRate.source,
    date_effective: props.exchangeRate.date_effective,
});

const submit = () => {
    form.put(route('admin.exchange-rates.update', props.exchangeRate.id));
};
</script>
