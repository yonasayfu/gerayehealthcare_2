<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    event_id: '',
    channel: '',
    message: '',
    sent_by_staff_id: '',
});

const submit = () => {
    form.post(route('admin.event-broadcasts.store'));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Broadcasts', href: route('admin.event-broadcasts.index') },
    { title: 'Create', href: route('admin.event-broadcasts.create') },
];
</script>

<template>
    <Head title="Create Event Broadcast" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Create New Event Broadcast</h1>
                    <p class="text-sm text-muted-foreground">Fill in the details to create a new event broadcast.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Event ID</label>
                                <div class="mt-2">
                                    <input
                                        type="number"
                                        v-model="form.event_id"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    />
                                    <div v-if="form.errors.event_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.event_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Channel</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        v-model="form.channel"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    />
                                    <div v-if="form.errors.channel" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.channel }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-full">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Message</label>
                                <div class="mt-2">
                                    <textarea
                                        v-model="form.message"
                                        rows="3"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                    ></textarea>
                                    <div v-if="form.errors.message" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.message }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Sent By Staff ID</label>
                                <div class="mt-2">
                                    <input
                                        type="number"
                                        v-model="form.sent_by_staff_id"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    />
                                    <div v-if="form.errors.sent_by_staff_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.sent_by_staff_id }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <Link :href="route('admin.event-broadcasts.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-400">Cancel</Link>
                        <button
                            type="submit"
                            class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Create Broadcast
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
