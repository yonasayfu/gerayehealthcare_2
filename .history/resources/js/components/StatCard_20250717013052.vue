<script setup lang="ts">
import { defineProps, withDefaults, computed } from 'vue'
import type { FunctionalComponent } from 'vue';
import type { LucideProps } from 'lucide-vue-next';

interface Props {
  title: string;
  value: string;
  change: string;
  icon: FunctionalComponent<LucideProps>;
  color?: string;
}

const props = withDefaults(defineProps<Props>(), {
  color: 'gray',
});

const iconBgClass = computed(() => {
  switch (props.color) {
    case 'green':
      return 'bg-green-200 dark:bg-green-950 text-green-800 dark:text-green-300';
    case 'blue':
      return 'bg-blue-200 dark:bg-blue-950 text-blue-800 dark:text-blue-300';
    case 'red':
      return 'bg-red-200 dark:bg-red-950 text-red-800 dark:text-red-300';
    case 'indigo':
      return 'bg-indigo-200 dark:bg-indigo-950 text-indigo-800 dark:text-indigo-300';
    default:
      return 'bg-gray-200 dark:bg-gray-950 text-gray-800 dark:text-gray-300';
  }
});

const changeClass = computed(() => {
    if (props.change.startsWith('+')) {
        return 'text-green-600';
    }
    if (props.change.startsWith('-')) {
        return 'text-red-600';
    }
    return 'text-muted-foreground';
});

const changeValue = computed(() => props.change.split(' ')[0]);
const changeText = computed(() => props.change.substring(props.change.indexOf(' ')));

</script>

<template>
  <div class="bg-card text-card-foreground flex flex-col gap-6 border py-6 hover:bg-muted rounded-none border-y-transparent border-s-transparent transition-colors">
    <div class="relative flex flex-row items-center justify-between space-y-0 px-6">
      <div class="leading-none font-semibold">{{ title }}</div>
      <div :class="['absolute end-4 top-0 flex size-12 items-end justify-start rounded-full p-4', iconBgClass]">
        <component :is="icon" class="size-5" />
      </div>
    </div>
    <div class="px-6 space-y-1">
      <div class="font-display text-3xl font-bold">{{ value }}</div>
      <p class="text-xs text-muted-foreground">
        <span :class="changeClass">{{ changeValue }}</span>
        <span>{{ changeText }}</span>
      </p>
    </div>
  </div>
</template>
