<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    eventBroadcast: { type: Object, required: true },
    events: { type: Array, default: () => [] },
    staff: { type: Array, default: () => [] },
});

const form = useForm({
    event_id: props.eventBroadcast.event_id,
    channel: props.eventBroadcast.channel,
    message: props.eventBroadcast.message,
    sent_by_staff_id: props.eventBroadcast.sent_by_staff_id,
});

const submit = () => {
    form.put(route('admin.event-broadcasts.update', props.eventBroadcast.id));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Broadcasts', href: route('admin.event-broadcasts.index') },
    { title: 'Edit', href: route('admin.event-broadcasts.edit', props.eventBroadcast.id) },
];
</script>

<template>
    <Head :title="`Edit Broadcast ${form.channel}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Event Broadcast: {{ form.channel }}</h1>
                    <p class="text-sm text-muted-foreground">Update the details for this event broadcast.</p>
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
                                        <option v-for="e in props.events" :key="e.id" :value="e.id">
                                            {{ e.title }} (ID: {{ e.id }})
                                        </option>
                                    </select>
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
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Sent By</label>
                                <div class="mt-2">
                                    <select
                                        v-model="form.sent_by_staff_id"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    >
                                        <option value="" disabled>Select staff</option>
                                        <option v-for="s in props.staff" :key="s.id" :value="s.id">
                                            {{ s.first_name }} {{ s.last_name }} (ID: {{ s.id }})
                                        </option>
                                    </select>
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
                            Update Broadcast
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
