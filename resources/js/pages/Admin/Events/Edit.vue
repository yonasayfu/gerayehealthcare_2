<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    event: Object,
});

const form = useForm({
    title: props.event.title,
    description: props.event.description,
    event_date: props.event.event_date,
    is_free_service: props.event.is_free_service,
    broadcast_status: props.event.broadcast_status,
});

const submit = () => {
    form.put(route('admin.events.update', props.event.id));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Events', href: route('admin.events.index') },
    { title: 'Edit', href: route('admin.events.edit', props.event.id) },
];
</script>

<template>
    <Head :title="`Edit ${form.title}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Event: {{ form.title }}</h1>
                    <p class="text-sm text-muted-foreground">Update the details for this event.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Title</label>
                                <div class="mt-2">
                                    <input
                                        type="text"
                                        v-model="form.title"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                        autofocus
                                    />
                                    <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.title }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Event Date</label>
                                <div class="mt-2">
                                    <input
                                        type="date"
                                        v-model="form.event_date"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    />
                                    <div v-if="form.errors.event_date" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.event_date }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-full">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Description</label>
                                <div class="mt-2">
                                    <textarea
                                        v-model="form.description"
                                        rows="3"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                    ></textarea>
                                    <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.description }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Broadcast Status</label>
                                <div class="mt-2">
                                    <select
                                        v-model="form.broadcast_status"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    >
                                        <option value="Draft">Draft</option>
                                        <option value="Published">Published</option>
                                        <option value="Archived">Archived</option>
                                    </select>
                                    <div v-if="form.errors.broadcast_status" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.broadcast_status }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3 flex items-center">
                                <input
                                    id="is_free_service"
                                    type="checkbox"
                                    v-model="form.is_free_service"
                                    class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded"
                                />
                                <label for="is_free_service" class="ml-2 block text-sm text-gray-900 dark:text-white">Is Free Service</label>
                                <div v-if="form.errors.is_free_service" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.is_free_service }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <Link :href="route('admin.events.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-400">Cancel</Link>
                        <button
                            type="submit"
                            class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Update Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
