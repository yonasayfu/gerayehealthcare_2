<script setup lang="ts">
// no local state here
import { Search, Bell } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import NavUser from '@/components/NavUser.vue'
// user not used here
</script>

<template>
  <header class="flex items-center justify-between px-6 py-4 mb-6 border-b">
    <div class="flex items-center gap-4">
      <h1 class="text-3xl font-bold">Dashboard</h1>
      <div class="relative">
        <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
        <Input placeholder="Search..." class="pl-8" />
      </div>
    </div>
    <div class="flex items-center gap-4">
      <Button variant="ghost" size="icon">
        <Bell class="h-5 w-5" />
      </Button>
      <NavUser />
    </div>
  </header>
</template>
