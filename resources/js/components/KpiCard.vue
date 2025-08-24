<script setup lang="ts">
import { defineProps, withDefaults } from 'vue'

interface Props {
  title: string
  value?: string | number
  subtext?: string
  icon?: any
  color?: 'gray' | 'green' | 'blue' | 'red' | 'purple' | 'orange' | 'indigo'
}

const props = withDefaults(defineProps<Props>(), {
  value: '—',
  subtext: '',
  color: 'gray',
})
</script>

<template>
  <div class="rounded-xl border bg-white p-4 dark:border-sidebar-border dark:bg-background">
    <div class="flex items-start justify-between">
      <div>
        <div class="text-xs text-muted-foreground">{{ title }}</div>
        <div class="mt-1 text-2xl font-semibold">{{ value }}</div>
        <div v-if="subtext" class="text-xs text-muted-foreground mt-1">{{ subtext }}</div>
      </div>
      <div v-if="icon" class="inline-flex h-10 w-10 items-center justify-center rounded-full"
           :class="{
             'bg-gray-200 dark:bg-gray-950 text-gray-800 dark:text-gray-300': color==='gray',
             'bg-green-200 dark:bg-green-950 text-green-800 dark:text-green-300': color==='green',
             'bg-blue-200 dark:bg-blue-950 text-blue-800 dark:text-blue-300': color==='blue',
             'bg-red-200 dark:bg-red-950 text-red-800 dark:text-red-300': color==='red',
             'bg-purple-200 dark:bg-purple-950 text-purple-800 dark:text-purple-300': color==='purple',
             'bg-orange-200 dark:bg-orange-950 text-orange-800 dark:text-orange-300': color==='orange',
             'bg-indigo-200 dark:bg-indigo-950 text-indigo-800 dark:text-indigo-300': color==='indigo',
           }">
        <component :is="icon" class="h-5 w-5" />
      </div>
    </div>
  </div>
</template>
