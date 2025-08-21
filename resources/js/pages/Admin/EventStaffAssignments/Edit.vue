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
  <Head title="Edit Event Staff Assignment" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Event Staff Assignment</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update event staff assignment information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.event-staff-assignments.index')"
              class="btn-glass btn-glass-sm"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
