<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import axios from 'axios';

const props = defineProps<{
  staff: {
    id: number;
    first_name: string;
    last_name: string;
  };
}>();

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'My Availability' },
];

const calendarRef = ref(null);
const showModal = ref(false);

const form = useForm({
    id: null,
    start_time: '',
    end_time: '',
    status: 'Available',
});

/**
 * Helper function to format a Date object into a string suitable for a datetime-local input.
 * It correctly handles the user's local timezone.
 */
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
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay',
  },
  editable: true,
  selectable: true,
  selectMirror: true,
  dayMaxEvents: true,
  weekends: true,
  events: route('staff.my-availability.events'), 
  
  select: (selectionInfo) => {
    form.reset();
    form.id = null;
    form.start_time = formatToDateTimeLocal(selectionInfo.start);
    form.end_time = formatToDateTimeLocal(selectionInfo.end);
    showModal.value = true;
  },

  eventClick: (clickInfo) => {
    const event = clickInfo.event;
    form.id = event.id;
    form.start_time = formatToDateTimeLocal(event.start);
    form.end_time = event.end ? formatToDateTimeLocal(event.end) : form.start_time;
    form.status = event.extendedProps.status;
    showModal.value = true;
  },

  eventDrop: (dropInfo) => updateEvent(dropInfo.event),
  eventResize: (resizeInfo) => updateEvent(resizeInfo.event),
});

function closeModal() {
    showModal.value = false;
    form.reset();
}

function saveAvailability() {
    const data = {
        start_time: form.start_time,
        end_time: form.end_time,
        status: form.status,
    };

    const onFinish = () => {
        calendarRef.value.getApi().refetchEvents();
        closeModal();
    };
    const onError = (errors) => console.error('Error:', errors);

    if (form.id) {
        axios.put(route('staff.my-availability.update', form.id), data)
             .then(onFinish)
             .catch(onError);
    } else {
        axios.post(route('staff.my-availability.store'), data)
             .then(onFinish)
             .catch(onError);
    }
}

function updateEvent(event) {
    const data = {
        start_time: event.start.toISOString(),
        end_time: event.end ? event.end.toISOString() : event.start.toISOString(),
        status: event.extendedProps.status,
    };
    axios.put(route('staff.my-availability.update', event.id), data)
        .catch(error => {
            console.error('Error updating event:', error.response.data);
            event.revert();
        });
}

function deleteAvailability() {
    if (!form.id || !confirm('Are you sure you want to delete this slot?')) return;
    
    axios.delete(route('staff.my-availability.destroy', form.id))
        .then(() => {
            calendarRef.value.getApi().refetchEvents();
            closeModal();
        })
        .catch(error => console.error('Error deleting event:', error.response.data));
}
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
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
          {{ form.id ? 'Edit' : 'Set' }} Availability
        </h3>
        <form @submit.prevent="saveAvailability" class="space-y-4">
            <div>
              <label class="block text-sm font-medium">Status</label>
              <select v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700">
                <option>Available</option>
                <option>Unavailable</option>
              </select>
            </div>
            <!-- THIS IS THE FIX: Editable inputs for start and end times -->
            <div>
                <label class="block text-sm font-medium">Start Time</label>
                <input type="datetime-local" v-model="form.start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700" />
            </div>
             <div>
                <label class="block text-sm font-medium">End Time</label>
                <input type="datetime-local" v-model="form.end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700" />
            </div>
          <div class="mt-6 flex justify-between">
            <div>
                <button v-if="form.id" @click.prevent="deleteAvailability" type="button" class="inline-flex justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                  Delete
                </button>
            </div>
            <div class="flex justify-end gap-3">
              <button @click="closeModal" type="button" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
              </button>
              <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                Save
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<style>
/* Basic styling for FullCalendar */
.fc .fc-button-primary {
    background-color: #4a5568;
    border-color: #4a5568;
}
.fc .fc-button-primary:hover {
    background-color: #2d3748;
    border-color: #2d3748;
}
.fc .fc-daygrid-day.fc-day-today {
    background-color: rgba(79, 70, 229, 0.1);
}
</style>
