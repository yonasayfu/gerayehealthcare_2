<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(defineProps<{
  title?: string
  description?: string
}>(), {
  title: 'Welcome back',
  description: 'Sign in to continue'
})

const year = computed(() => new Date().getFullYear())
</script>

<template>
  <div class="min-h-screen grid md:grid-cols-2 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-950">
    <!-- Left: Hero / Illustration -->
    <div class="relative hidden md:block">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-cyan-500/20 blur-3xl"></div>
      </div>
      <div class="relative h-full flex flex-col justify-between p-10">
        <div class="text-sm text-slate-500 dark:text-slate-400">Geraye Healthcare</div>
        <div class="mt-auto">
          <h2 class="text-3xl font-semibold text-slate-800 dark:text-slate-100">Care that comes home</h2>
          <p class="mt-2 max-w-md text-slate-600 dark:text-slate-300">Secure access for staff and partners. Manage visits, patients, and billing from one place.</p>
        </div>
        <div class="text-xs text-slate-500 dark:text-slate-400">© {{ year }} Geraye Healthcare</div>
      </div>
    </div>

    <!-- Right: Glass Card Form -->
    <div class="relative flex items-center justify-center p-6 md:p-10">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-10 right-10 h-40 w-40 rounded-full bg-white/10 dark:bg-white/5 backdrop-blur-xl"></div>
      </div>
      <div class="w-full max-w-md">
        <div class="rounded-2xl border border-white/30 dark:border-white/10 bg-white/60 dark:bg-slate-900/40 backdrop-blur-xl shadow-xl">
          <div class="px-6 py-6 md:px-8 md:py-8">
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ props.title }}</h1>
            <p v-if="props.description" class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ props.description }}</p>
            <div class="mt-6">
              <slot />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

