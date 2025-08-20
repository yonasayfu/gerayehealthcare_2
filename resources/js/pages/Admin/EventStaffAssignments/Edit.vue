<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    eventStaffAssignment: Object,
    events: Array,
    staff: Array,
    existingAssignments: { type: Array, default: () => [] },
});

const form = useForm({
    event_id: props.eventStaffAssignment?.event_id,
    staff_id: props.eventStaffAssignment?.staff_id,
    role: props.eventStaffAssignment?.role,
    notes: props.eventStaffAssignment?.notes ?? '',
});

const roles = [
    'Coordinator',
    'Nurse',
    'Caregiver',
    'Volunteer',
    'Security',
    'Other',
];

const isDuplicate = computed(() => {
    if (!form.event_id || !form.staff_id) return false;
    // Exclude current record by id
    return props.existingAssignments?.some(a => a.event_id === form.event_id && a.staff_id === form.staff_id && a.id !== props.eventStaffAssignment?.id);
});

const submit = () => {
    form.put(route('admin.event-staff-assignments.update', props.eventStaffAssignment.id));
};

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Staff Assignments', href: route('admin.event-staff-assignments.index') },
    { title: 'Edit', href: route('admin.event-staff-assignments.edit', props.eventStaffAssignment.id) },
];
</script>

<template>
    <Head :title="`Edit Assignment ${form.role}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">
            <div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Event Staff Assignment: {{ form.role }}</h1>
                    <p class="text-sm text-muted-foreground">Update the details for this event staff assignment.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg p-6">
                <div v-if="isDuplicate" class="mb-4 p-3 rounded-md bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                    Warning: This staff member is already assigned to the selected event. If you proceed, the server will block duplicates.
                </div>
                <form @submit.prevent="submit">
                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Event</label>
                                <div class="mt-2">
                                    <select
                                        v-model.number="form.event_id"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    >
                                        <option disabled value="">Select an event</option>
                                        <option v-for="e in props.events" :key="e.id" :value="e.id">{{ e.title }}</option>
                                    </select>
                                    <div v-if="form.errors.event_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.event_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Staff</label>
                                <div class="mt-2">
                                    <select
                                        v-model.number="form.staff_id"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    >
                                        <option disabled value="">Select a staff</option>
                                        <option v-for="s in props.staff" :key="s.id" :value="s.id">{{ s.first_name }} {{ s.last_name }}</option>
                                    </select>
                                    <div v-if="form.errors.staff_id" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.staff_id }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Role</label>
                                <div class="mt-2">
                                    <select
                                        v-model="form.role"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                        required
                                    >
                                        <option disabled value="">Select a role</option>
                                        <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
                                    </select>
                                    <div v-if="form.errors.role" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.role }}
                                    </div>
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                                <div class="mt-2">
                                    <textarea
                                        v-model="form.notes"
                                        rows="3"
                                        placeholder="Optional instructions or details for this assignment"
                                        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
                                    />
                                    <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">
                                        {{ form.errors.notes }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <Link :href="route('admin.event-staff-assignments.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-400">Cancel</Link>
                        <button
                            type="submit"
                            class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Update Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
