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
    alt="Geraye Logo"
    class="h-10 w-auto border-n"
  />
</template>