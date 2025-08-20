<script setup lang="ts">
import { computed } from 'vue'
import { Sun, Moon } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { useAppearance } from '@/composables/useAppearance'

const { appearance, updateAppearance } = useAppearance()

const isDark = computed(() => appearance.value === 'dark')

function toggleTheme() {
  updateAppearance(isDark.value ? 'light' : 'dark')
}
</script>

<template>
  <Button variant="ghost" size="icon" @click="toggleTheme">
    <Moon v-if="!isDark" class="h-5 w-5 text-indigo-600" />
    <Sun v-else class="h-5 w-5 text-yellow-500" />
    <span class="sr-only">Toggle theme</span>
  </Button>
</template>
