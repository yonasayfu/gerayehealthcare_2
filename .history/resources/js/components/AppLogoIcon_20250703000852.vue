<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
  inheritAttrs: false,
});

interface Props {
  className?: HTMLAttributes['class'];
}

defineProps<Props>();

// Path to your logo (served from Laravel's public folder)
const logoPath = '/images/logo.png'; // <-- update the file name if needed
</script>

<template>
  <img
    :src="logoPath"
    :class="className"
    v-bind="$attrs"
    alt="App Logo"
  />
</template>