<script setup lang="ts">
import { ref, onMounted } from 'vue';
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
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'My Availability' },
];

const calendarRef = ref(null);
const showModal = ref(false);
const selectedEventInfo = ref(null);

// Form for creating/updating availability
const form = useForm({
    id: null,
    staff_id: props.staff.id,
    start_time: '',
    end_time: '',
    status: 'Available',
});

const calendarOptions = {
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
  events: `/dashboard/staff-availabilities/events?staff_id=${props.staff.id}`, // Fetch events for this staff member
  
  // Called when a user clicks and drags to select a time slot
  select: (selectionInfo) => {
    form.reset();
    form.id = null;
    form.start_time = selectionInfo.startStr;
    form.end_time = selectionInfo.endStr;
    showModal.value = true;
  },

  // Called when an existing event is clicked
  eventClick: (clickInfo) => {
    const event = clickInfo.event;
    form.id = event.id;
    form.start_time = event.start.toISOString().slice(0, 16);
    form.end_time = event.end.toISOString().slice(0, 16);
    form.status = event.extendedProps.status;
    showModal.value = true;
  },
};

function closeModal() {
    showModal.value = false;
    form.reset();
}

function saveAvailability() {
    const data = {
        staff_id: form.staff_id,
        start_time: form.start_time,
        end_time: form.end_time,
        status: form.status,
    };

    if (form.id) {
        // Update existing event
        axios.put(`/dashboard/staff-availabilities/${form.id}`, data)
            .then(() => {
                calendarRef.value.getApi().refetchEvents();
                closeModal();
            })
            .catch(error => console.error('Error updating event:', error));
    } else {
        // Create new event
        axios.post('/dashboard/staff-availabilities', data)
            .then(() => {
                calendarRef.value.getApi().refetchEvents();
                closeModal();
            })
            .catch(error => console.error('Error creating event:', error));
    }
}

function deleteAvailability() {
    if (!form.id || !confirm('Are you sure you want to delete this slot?')) {
        return;
    }
    axios.delete(`/dashboard/staff-availabilities/${form.id}`)
        .then(() => {
            calendarRef.value.getApi().refetchEvents();
            closeModal();
        })
        .catch(error => console.error('Error deleting event:', error));
}

</script>

<template>
  <Head title="My Availability" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="rounded-lg bg-muted/40 p-4 shadow-sm mb-6">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Manage Your Availability</h1>
        <p class="text-sm text-muted-foreground">Click and drag on the calendar to create new slots, or click an existing slot to edit or delete it.</p>
      </div>
      
      <div class="p-4 bg-white dark:bg-gray-900 rounded-lg shadow">
        <FullCalendar ref="calendarRef" :options="calendarOptions" />
      </div>
    </div>

    <!-- Availability Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
          {{ form.id ? 'Edit' : 'Set' }} Availability
        </h3>
        <form @submit.prevent="saveAvailability">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium">Status</label>
              <select v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700">
                <option>Available</option>
                <option>Unavailable</option>
              </select>
            </div>
             <div>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    From: {{ new Date(form.start_time).toLocaleString() }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    To: {{ new Date(form.end_time).toLocaleString() }}
                </p>
             </div>
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
    background-color: #4a5568; /* gray-700 */
    border-color: #4a5568;
}
.fc .fc-button-primary:hover {
    background-color: #2d3748; /* gray-800 */
    border-color: #2d3748;
}
.fc .fc-daygrid-day.fc-day-today {
    background-color: rgba(79, 70, 229, 0.1); /* indigo-600 with opacity */
}
</style>
