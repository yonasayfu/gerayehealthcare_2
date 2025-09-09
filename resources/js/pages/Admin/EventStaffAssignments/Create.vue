<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import FormActions from '@/components/FormActions.vue'
import InputError from '@/components/InputError.vue'

const props = defineProps({
    events: Array,
    staff: Array,
    existingAssignments: { type: Array, default: () => [] },
});

const form = useForm({
    event_id: '',
    staff_id: '',
    role: '',
    notes: '',
});

const submit = () => {
    form.post(route('admin.event-staff-assignments.store'));
};

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
    return props.existingAssignments?.some(a => a.event_id === form.event_id && a.staff_id === form.staff_id);
});

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Event Staff Assignments', href: route('admin.event-staff-assignments.index') },
    { title: 'Create', href: route('admin.event-staff-assignments.create') },
];
</script>

<template>
  <Head title="Create New Event Staff Assignment" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New Event Staff Assignment</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a event staff assignment.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Event</label>
              <select v-model="form.event_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Event</option>
                <option v-for="ev in events" :key="ev.id" :value="ev.id">{{ ev.title ?? ev.name ?? ('Event #' + ev.id) }}</option>
              </select>
              <InputError class="mt-1" :message="form.errors.event_id" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Staff</label>
              <select v-model="form.staff_id" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Staff</option>
                <option v-for="s in staff" :key="s.id" :value="s.id">{{ s.full_name ?? s.name ?? ('Staff #' + s.id) }}</option>
              </select>
              <InputError class="mt-1" :message="form.errors.staff_id" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Role</label>
              <select v-model="form.role" class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800">
                <option value="">Select Role</option>
                <option v-for="r in roles" :key="r" :value="r">{{ r }}</option>
              </select>
              <InputError class="mt-1" :message="form.errors.role" />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notes</label>
              <textarea v-model="form.notes" rows="4" placeholder="Optional notes..." class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"></textarea>
              <InputError class="mt-1" :message="form.errors.notes" />
            </div>
          </div>

          <p v-if="isDuplicate" class="text-amber-600 text-sm">Warning: This staff is already assigned to the selected event.</p>

          <FormActions
            :cancel-href="route('admin.event-staff-assignments.index')"
            cancel-text="Cancel"
            submit-text="Create Event Staff Assignment"
            :processing="form.processing"
          />
        </form>
      </div>
    </div>
  </AppLayout>
</template>
