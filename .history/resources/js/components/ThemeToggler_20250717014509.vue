<script setup lang="ts">
import { useColorMode, useCycleList } from '@vueuse/core'
import { Sun, Moon, Settings } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'

const mode = useColorMode({
  attribute: 'class',
  modes: {
    light: 'light',
    dark: 'dark',
    system: 'system',
  },
})

const { next } = useCycleList(['light', 'dark', 'system'], {
  initialValue: mode,
})
</script>

<template>
  <Button variant="ghost" size="icon" @click="next()">
    <Sun class="h-5 w-5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
    <Moon class="absolute h-5 w-5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
