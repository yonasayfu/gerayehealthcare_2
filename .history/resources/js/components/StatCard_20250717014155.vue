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
</template>
