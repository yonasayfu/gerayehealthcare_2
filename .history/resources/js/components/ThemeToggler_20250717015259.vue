<script setup lang="ts">
import { useColorMode } from '@vueuse/core'
import { Sun, Moon } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import Cookies from 'js-cookie'

const mode = useColorMode({
  attribute: 'class',
  modes: {
    light: 'light',
    dark: 'dark',
  },
  storageKey: 'appearance',
  storage: {
    getItem: (key) => {
      return Cookies.get(key)
    },
    setItem: (key, value) => {
      Cookies.set(key, value, { expires: 365, path: '/' })
      document.documentElement.className = value
    },
    removeItem: (key) => {
      Cookies.remove(key)
    }
  }
})

const toggleTheme = () => {
  mode.value = mode.value === 'light' ? 'dark' : 'light'
}
</script>

<template>
  <Button variant="ghost" size="icon" @click="toggleTheme">
    <Sun class="h-5 w-5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
    <Moon class="absolute h-5 w-5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
    <span class="sr-only">Toggle theme</span>
  </Button>
</template>
