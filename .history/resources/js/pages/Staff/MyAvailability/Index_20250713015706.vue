<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import type { BreadcrumbItemType } from '@/types';
import type { EventClickArg, DateSelectArg, EventDropArg, EventInput } from '@fullcalendar/core';

const props = defineProps<{
  staff: { id: number; };
}>();

const breadcrumbs: BreadcrumbItemType[] = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'My Availability' },
];

const calendarRef = ref<any>(null);
const showModal = ref(false);
const isEditMode = ref(false);

const form = useForm({
  id: null as number | null,
  start_time: '',
  end_time: '',
  status: 'Available',
});

const formatToDateTimeLocal = (date: Date): string => {
  const year = date.getFullYear();
  const month = (date.getMonth() + 1).toString().padStart(2, '0');
  const day = date.getDate().toString().padStart(2, '0');
  const hours = date.getHours().toString().padStart(2, '0');
  const minutes = date.getMinutes().toString().padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'timeGridWeek',
  headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
  editable: true,
  selectable: true,
  events: route('staff.my-availability.events'),

  select: (selectionInfo: DateSelectArg) => {
    isEditMode.value = false;
    form.reset();
    form.id = null;
    form.start_time = formatToDateTimeLocal(selectionInfo.start);
    form.end_time = formatToDateTimeLocal(selectionInfo.end);
    showModal.value = true;
  },

  eventClick: (clickInfo: EventClickArg) => {
    if (clickInfo.event.extendedProps.is_editable === false) {
      alert("This is a scheduled work assignment and cannot be edited from this calendar.");
      return;
    }
    isEditMode.value = true;
    form.id = parseInt(clickInfo.event.id);
    form.start_time = formatToDateTimeLocal(clickInfo.event.start!);
    form.end_time = clickInfo.event.end ? formatToDateTimeLocal(clickInfo.event.end) : form.start_time;
    form.status = clickInfo.event.extendedProps.status;
    showModal.value = true;
  },

  eventDrop: (dropInfo: EventDropArg) => {
    if (dropInfo.event.extendedProps.is_editable === false) {
      alert("This is a scheduled work assignment and cannot be moved.");
      dropInfo.revert();
      return;
    }
    form.id = parseInt(dropInfo.event.id);
    form.start_time = formatToDateTimeLocal(dropInfo.event.start!);
    form.end_time = dropInfo.event.end ? formatToDateTimeLocal(dropInfo.event.end) : form.start_time;
    form.status = dropInfo.event.extendedProps.status;
    submitForm();
  },
});

const closeModal = () => {
  showModal.value = false;
  form.reset();
};
const submitForm = () => {
    // This function will run after a successful submission
    const onFinish = () => {
        if (calendarRef.value) {
            calendarRef.value.getApi().refetchEvents();
        }
        closeModal();
    };

    // 1. Configure the transform on the original 'form' object
    form.transform((data) => ({
        ...data,
        start_time: new Date(data.start_time).toISOString(),
        end_time: new Date(data.end_time).toISOString(),
    }));

    // 2. Call .put() or .post() on the original 'form' object
    if (isEditMode.value) {
        form.put(route('staff.my-availability.update', { availability: form.id }), {
            onSuccess: onFinish,
            preserveScroll: true
        });
    } else {
        form.post(route('staff.my-availability.store'), {
            onSuccess: onFinish,
            preserveScroll: true
        });
    }
};

const deleteAvailability = () => {
    if (!confirm('Are you sure you want to delete this availability slot?')) return;
    
    form.delete(route('staff.my-availability.destroy', { availability: form.id }), {
        onSuccess: () => {
            if (calendarRef.value) {
                calendarRef.value.getApi().refetchEvents();
            }
            closeModal();
        },
        preserveScroll: true,
    });
};
</script>

<template>
  <Head title="My Availability" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm mb-6">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Manage Your Availability</h1>
        <p class="text-sm text-muted-foreground">Click and drag on the calendar to create new slots. Click an existing slot to edit or delete it.</p>
      </div>
      
      <div class="p-4 bg-white dark:bg-gray-900 rounded-lg shadow">
        <FullCalendar ref="calendarRef" :options="calendarOptions" />
      </div>
    </div>

    <!-- Availability Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="closeModal">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
          {{ form.id ? 'Edit' : 'Set' }} Availability
        </h3>
        <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <label class="block text-sm font-medium">Status</label>
              <select v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600">
                <option>Available</option>
                <option>Unavailable</option>
              </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Start Time</label>
                <input type="datetime-local" v-model="form.start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600" required />
                 <div v-if="form.errors.start_time" class="text-red-500 text-sm mt-1">{{ form.errors.start_time }}</div>
            </div>
             <div>
                <label class="block text-sm font-medium">End Time</label>
                <input type="datetime-local" v-model="form.end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600" required />
                <div v-if="form.errors.end_time" class="text-red-500 text-sm mt-1">{{ form.errors.end_time }}</div>
            </div>
          <div class="mt-6 flex justify-between items-center">
            <div>
                <button v-if="form.id" @click.prevent="deleteAvailability" type="button" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 disabled:opacity-50" :disabled="form.processing">
                  Delete
                </button>
            </div>
            <div class="flex justify-end gap-3">
              <button @click="closeModal" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-600 rounded-md hover:bg-gray-200">
                Cancel
              </button>
              <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50">
                {{ form.processing ? 'Saving...' : 'Save' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
