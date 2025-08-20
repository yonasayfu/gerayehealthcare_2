<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    events: Array,
    patients: Array,
});

const form = useForm({
    event_id: '',
    source_channel: '',
    recommended_by_name: '',
    recommended_by_phone: '',
    patient_name: '',
    phone_number: '',
    notes: '',
    status: 'pending',
});

const submit = () => {
    form.post(route('admin.event-recommendations.store'));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Recommendations', href: route('admin.event-recommendations.index') },
    { title: 'Create', href: route('admin.event-recommendations.create') },
];
</script>

<template>
    <Head title="Create Event Recommendation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Event Recommendation</h1>
                    <p class="text-sm text-muted-foreground">Fill in the details to create a new event recommendation.</p>
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
                                        <option value="">Select Event</option>
                                        <option v-for="e in props.events" :key="e.id" :value="e.id">{{ e.title }}</option>
                                    </select>
                                    <div v-if="form.errors.event_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.event_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Source</label>
                                <div class="mt-2">
                                    <select
                                        v-model="form.source_channel"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    >
                                        <option value="">Select Source</option>
                                        <option value="social_media">Social Media</option>
                                        <option value="form">Form</option>
                                        <option value="community_outreach">Community Outreach</option>
                                    </select>
                                    <div v-if="form.errors.source_channel" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.source_channel }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Recommended By (Name)</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        v-model="form.recommended_by_name"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                    />
                                    <div v-if="form.errors.recommended_by_name" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.recommended_by_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Recommended By (Phone)</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        v-model="form.recommended_by_phone"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                    />
                                    <div v-if="form.errors.recommended_by_phone" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.recommended_by_phone }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Patient Name</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        v-model="form.patient_name"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    />
                                    <div v-if="form.errors.patient_name" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.patient_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Patient Phone</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        v-model="form.phone_number"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                    />
                                    <div v-if="form.errors.phone_number" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.phone_number }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
                                <div class="mt-2">
                                    <select
                                        v-model="form.status"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    >
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                    <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.status }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-full">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                                <div class="mt-2">
                                    <textarea
                                        v-model="form.notes"
                                        rows="3"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                    ></textarea>
                                    <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.notes }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <Link :href="route('admin.event-recommendations.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-400">Cancel</Link>
                        <button
                            type="submit"
                            class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Create Recommendation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
