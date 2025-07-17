<script setup lang="ts">
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import type { User } from '@/types'

const page = usePage()
const user = page.props.auth.user as User
</script>

<template>
  <header class="flex items-center justify-between px-6 py-4 mb-6 border-b">
    <div class="flex items-center gap-4">
      <h1 class="text-3xl font-bold">Dashboard</h1>
    </div>
  </header>
</template>
