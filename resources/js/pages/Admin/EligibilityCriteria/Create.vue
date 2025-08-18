<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    events: Array,
});

const operators = ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'ILIKE', 'IN', 'NOT IN'];

const form = useForm({
    event_id: '',
    criteria_title: '',
    operator: '',
    value: '',
});

const submit = () => {
    form.post(route('admin.eligibility-criteria.store'));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Eligibility Criteria', href: route('admin.eligibility-criteria.index') },
    { title: 'Create', href: route('admin.eligibility-criteria.create') },
];
</script>

<template>
    <Head title="Create Eligibility Criteria" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Eligibility Criteria</h1>
                    <p class="text-sm text-muted-foreground">Fill in the details to create new eligibility criteria.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Event</label>
                                <div class="mt-2">
                                    <select
                                        v-model="form.event_id"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    >
                                        <option value="" disabled>Select an event</option>
                                        <option v-for="e in props.events" :key="e.id" :value="e.id">{{ e.title }}</option>
                                    </select>
                                    <div v-if="form.errors.event_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.event_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Criteria Title</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        v-model="form.criteria_title"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    />
                                    <div v-if="form.errors.criteria_title" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.criteria_title }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Operator</label>
                                <div class="mt-2">
                                    <select
                                        v-model="form.operator"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    >
                                        <option value="" disabled>Select an operator</option>
                                        <option v-for="op in operators" :key="op" :value="op">{{ op }}</option>
                                    </select>
                                    <div v-if="form.errors.operator" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.operator }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Value</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        v-model="form.value"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    />
                                    <div v-if="form.errors.value" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.value }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <Link :href="route('admin.eligibility-criteria.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-400">Cancel</Link>
                        <button
                            type="submit"
                            class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Create Criteria
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
