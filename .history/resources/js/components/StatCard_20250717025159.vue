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
  color?: string; // New prop for background color
}

withDefaults(defineProps<Props>(), {
  color: 'bg-white', // Default color
});
</script>

<template>
  <Card :class="color">
      </CardTitle>
      <component :is="icon" class="h-5 w-5 text-muted-foreground" />
    </CardHeader>
    <CardContent class="pt-2">
      <div class="text-2xl font-bold">{{ value }}</div>
      <p class="text-sm text-muted-foreground text-wrap">{{ change }}</p>
    </CardContent>
  </Card>
</template>
