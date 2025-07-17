<script setup lang="ts">
import { defineProps } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { FunctionalComponent } from 'vue';
import type { LucideProps } from 'lucide-vue-next';

interface Props {
  title: string;
  value: string;
  change: string;
  icon: FunctionalComponent<LucideProps>;
}

defineProps<Props>()
</script>

<template>
  <Card>
    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
      <CardTitle class="text-sm font-medium">
        {{ title }}
      </CardTitle>
      <component :is="icon" class="h-4 w-4 text-muted-foreground" />
    </CardHeader>
    <CardContent>
      <div class="text-2xl font-bold">{{ value }}</div>
      <p class="text-xs text-muted-foreground">{{ change }}</p>
    </CardContent>
  </Card>
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
