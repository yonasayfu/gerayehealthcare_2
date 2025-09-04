<template>
  <span class="relative inline-block" @mouseenter="show = true" @mouseleave="show = false">
    <slot />
    <transition name="fade">
      <div
        v-if="show && text"
        class="pointer-events-none absolute z-50 -top-2 left-1/2 -translate-x-1/2 -translate-y-full whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white shadow"
      >
        {{ text }}
      </div>
    </transition>
  </span>
</template>

<script setup lang="ts">
import { ref } from 'vue';
defineProps<{ text: string }>();
const show = ref(false);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

