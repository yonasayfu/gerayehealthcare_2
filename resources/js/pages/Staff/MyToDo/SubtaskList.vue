<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{ taskId: number }>()

const form = useForm({ title: '' })

function addSubtask() {
  if (!form.title) return
  form.post(route('staff.my-todo.subtasks.store', { my_todo: props.taskId }), {
    preserveScroll: true,
    onSuccess: () => form.reset('title'),
  })
}
</script>

<template>
  <!-- Simple fetch-less subtask UI: rely on server re-render of parent page -->
  <form @submit.prevent="addSubtask" class="flex items-center gap-2">
    <input v-model="form.title" placeholder="Add subtask" class="rounded border px-2 py-1 text-sm" />
    <button type="submit" class="text-xs text-indigo-600">Add</button>
  </form>
</template>

