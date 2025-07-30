<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
  inheritAttrs: false,
});

interface Props {
  className?: HTMLAttributes['class'];
}

defineProps<Props>();

const logoPath = '/images/geraye_logo.jpeg';
</script>

<template>
  <img
    :src="logoPath"
    :class="className"
    v-bind="$attrs"
    alt="Geraye Home Care Services Logo"
    class="h-8 w-auto border-none"
  />
</template>