<script setup lang="ts">
import { ref } from 'vue'
import { Search, Bell } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import NavUser from '@/components/NavUser.vue'
import { usePage } from '@inertiajs/vue3'
import type { User } from '@/types'

const page = usePage()
const user = page.props.auth.user as User
</script>

<template>
  <header class="flex items-center justify-between p-4 border-b">
    <div class="flex items-center gap-4">
      <h1 class="text-2xl font-bold">Dashboard</h1>
    </div>
    <div class="flex items-center gap-4">
      <div class="relative">
        <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
        <Input placeholder="Search..." class="pl-8" />
      </div>
      <Button variant="ghost" size="icon">
        <Bell class="h-5 w-5" />
      </Button>
      <NavUser />
    </div>
  </header>
</template>
