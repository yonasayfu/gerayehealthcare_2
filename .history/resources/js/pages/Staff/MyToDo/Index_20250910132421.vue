<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Pagination from '@/components/Pagination.vue'
import SubtaskList from './SubtaskList.vue'

const props = defineProps<{
  tasks: {
    data: Array<{ id: number; title: string; notes?: string; due_date?: string; is_completed: boolean; is_important: boolean; subtasks?: Array<{id:number; title:string; is_completed:boolean}> }>
    links: { url: string | null; label: string; active: boolean }[]
  }
  filters: { filter: 'all'|'today'|'upcoming'|'important'|'completed'|'myday'; per_page: number }
}>()

const form = useForm({
  title: '',
  due_date: '',
  reminder_at: '',
  is_important: false,
})

function submitNew() {
  form.post(route('staff.my-todo.store'), {
    preserveScroll: true,
    onSuccess: () => form.reset('title'),
  })
}

function toggleCompleted(taskId: number, is_completed: boolean) {
  router.patch(route('staff.my-todo.update', { my_todo: taskId }), { is_completed })
}

function toggleImportant(taskId: number, is_important: boolean) {
  router.patch(route('staff.my-todo.update', { my_todo: taskId }), { is_important })
}

function switchFilter(f: 'all'|'today'|'upcoming'|'important'|'completed') {
  router.get(route('staff.my-todo.index'), { filter: f }, { preserveState: true, replace: true })
}
</script>

<template>
  <Head title="My To-Do" />
  <AppLayout>
    <section class="p-6 space-y-6 bg-white rounded-lg shadow">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">My To‑Do</h1>
        <div class="inline-flex rounded-md border overflow-hidden">
          <button @click="switchFilter('all')" :class="['px-3 py-1 text-sm', props.filters.filter==='all' ? 'bg-cyan-600 text-white' : 'bg-white']">All</button>
          <button @click="switchFilter('today')" :class="['px-3 py-1 text-sm', props.filters.filter==='today' ? 'bg-cyan-600 text-white' : 'bg-white']">Today</button>
          <button @click="switchFilter('upcoming')" :class="['px-3 py-1 text-sm', props.filters.filter==='upcoming' ? 'bg-cyan-600 text-white' : 'bg-white']">Upcoming</button>
          <button @click="switchFilter('important')" :class="['px-3 py-1 text-sm', props.filters.filter==='important' ? 'bg-cyan-600 text-white' : 'bg-white']">Important</button>
          <button @click="switchFilter('completed')" :class="['px-3 py-1 text-sm', props.filters.filter==='completed' ? 'bg-cyan-600 text-white' : 'bg-white']">Completed</button>
          <button @click="switchFilter('myday')" :class="['px-3 py-1 text-sm', props.filters.filter==='myday' ? 'bg-cyan-600 text-white' : 'bg-white']">My Day</button>
        </div>
      </div>

      <!-- Add Task -->
      <form @submit.prevent="submitNew" class="flex flex-wrap gap-2 items-center">
        <input v-model="form.title" placeholder="Add a task" class="flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white/50 backdrop-blur-sm"/>
        <input type="datetime-local" v-model="form.due_date" class="rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white/50 backdrop-blur-sm"/>
        <input type="datetime-local" v-model="form.reminder_at" class="rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-500 bg-white/50 backdrop-blur-sm"/>
        <label class="inline-flex items-center gap-2 text-sm">
          <input type="checkbox" v-model="form.is_important" class="rounded text-cyan-600 focus:ring-cyan-500" /> Important
        </label>
        <label class="inline-flex items-center gap-2 text-sm">
          <input type="checkbox" v-model="(form as any).add_to_my_day" class="rounded text-cyan-600 focus:ring-cyan-500" /> Add to My Day
        </label>
        <button type="submit" :disabled="form.processing" class="btn-glass">
          {{ form.processing ? 'Adding...' : 'Add Task' }}
        </button>
      </form>

      <!-- List -->
      <div class="divide-y">
        <div v-for="t in props.tasks.data" :key="t.id" class="py-3">
          <div class="flex items-center gap-3">
            <input type="checkbox" :checked="t.is_completed" @change="(e:any)=>toggleCompleted(t.id, e.target.checked)" class="rounded text-cyan-600 focus:ring-cyan-500" />
            <button @click="toggleImportant(t.id, !t.is_important)" class="text-sm" :class="t.is_important ? 'text-yellow-500' : 'text-gray-400'">★</button>
            <div class="flex-1">
              <div :class="['text-sm', t.is_completed ? 'line-through text-gray-400' : '']">{{ t.title }}</div>
              <div v-if="t.due_date" :class="['text-xs', (new Date(t.due_date) < new Date() && !t.is_completed) ? 'text-red-600' : 'text-gray-500']">
                Due: {{ new Date(t.due_date).toLocaleString() }}
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button @click="router.patch(route('staff.my-todo.update', { my_todo: t.id }), { add_to_my_day: true })" class="text-xs text-cyan-600 hover:text-cyan-800">Add to My Day</button>
              <select class="text-xs border rounded px-1 py-0.5 focus:outline-none focus:ring-1 focus:ring-cyan-500 bg-white/50 backdrop-blur-sm" @change="(e:any)=>router.patch(route('staff.my-todo.update', { my_todo: t.id }), { recurrence_type: e.target.value })">
                <option value="none">No repeat</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
              </select>
              <button @click="router.delete(route('staff.my-todo.destroy', { my_todo: t.id }))" class="text-red-600 text-sm hover:text-red-800">Delete</button>
            </div>
          </div>
          <!-- Subtasks -->
          <div class="ml-6 mt-2 space-y-1">
            <div v-for="st in (t.subtasks || [])" :key="st.id" class="flex items-center gap-2 text-sm">
              <input type="checkbox" :checked="st.is_completed" @change="(e:any)=>router.patch(route('staff.my-todo.subtasks.update', { my_todo: t.id, subtask: st.id }), { is_completed: e.target.checked })" class="rounded text-cyan-600 focus:ring-cyan-500" />
              <span :class="st.is_completed ? 'line-through text-gray-400' : ''">{{ st.title }}</span>
              <button class="text-red-500 ml-auto hover:text-red-700" @click="router.delete(route('staff.my-todo.subtasks.destroy', { my_todo: t.id, subtask: st.id }))">Delete</button>
            </div>
            <SubtaskList :task-id="t.id" />
          </div>
        </div>
        <div v-if="props.tasks.data.length===0" class="text-center text-sm text-gray-500 py-6">No tasks found.</div>
      </div>

      <Pagination v-if="props.tasks.links.length > 1" :links="props.tasks.links" class="mt-4 flex justify-center"/>
    </section>
  </AppLayout>
  
</template>