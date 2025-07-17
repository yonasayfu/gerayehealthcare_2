<script setup lang="ts">
import { useColorMode, BasicColorSchema } from '@vueuse/core' // Import BasicColorSchema
import { Sun, Moon, Palette } from 'lucide-vue-next' // Add Palette icon
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu' // Import dropdown components
import Cookies from 'js-cookie'

// Define a union type for your themes
type AppTheme = 'theme-light' | 'theme-dark' | 'theme-emerald' | 'theme-rose' | 'theme-purple';

const themes = [
  { name: 'Light', value: 'theme-light' },
  { name: 'Dark', value: 'theme-dark' }, // This is the "light blue black" theme
  { name: 'Emerald', value: 'theme-emerald' },
  { name: 'Rose', value: 'theme-rose' },
  { name: 'Purple', value: 'theme-purple' },
]

const mode = useColorMode<AppTheme | BasicColorSchema>({ // Explicitly type useColorMode
  attribute: 'class',
  modes: {
    'theme-light': 'theme-light',
    'theme-dark': 'theme-dark',
    'theme-emerald': 'theme-emerald',
    'theme-rose': 'theme-rose',
    'theme-purple': 'theme-purple',
  },
  storageKey: 'appearance',
  storage: {
    getItem: (key: string) => { // Explicitly type key
      const value = Cookies.get(key)
      return value ? (value as AppTheme) : null // Cast value
    },
    setItem: (key: string, value: AppTheme) => { // Explicitly type key and value
      // Remove all existing theme classes before setting the new one
      document.documentElement.classList.remove(...themes.map(t => t.value));
      document.documentElement.classList.add(value);
      Cookies.set(key, value, { expires: 365, path: '/' })
    },
    removeItem: (key: string) => { // Explicitly type key
      Cookies.remove(key)
      // Optionally remove all theme classes if storage is cleared
      document.documentElement.classList.remove(...themes.map(t => t.value));
    }
  }
})

const setTheme = (themeValue: AppTheme) => { // Explicitly type themeValue
  mode.value = themeValue
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" size="icon">
        <Palette class="h-5 w-5" /> <!-- Use a generic palette icon -->
        <span class="sr-only">Toggle theme</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem
        v-for="themeOption in themes"
        :key="themeOption.value"
        @click="setTheme(themeOption.value)"
        :class="{ 'font-bold': mode.value === themeOption.value }"
      >
        {{ themeOption.name }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
