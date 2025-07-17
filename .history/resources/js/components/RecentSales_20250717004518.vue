<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'

const sales = [
  { name: 'Olivia Martin', email: 'olivia.martin@email.com', amount: '+$1,999.00', avatar: '/avatars/01.png' },
  { name: 'Jackson Lee', email: 'jackson.lee@email.com', amount: '+$39.00', avatar: '/avatars/02.png' },
  { name: 'Isabella Nguyen', email: 'isabella.nguyen@email.com', amount: '+$299.00', avatar: '/avatars/03.png' },
  { name: 'William Kim', email: 'will@email.com', amount: '+$99.00', avatar: '/avatars/04.png' },
  { name: 'Sofia Davis', email: 'sofia.davis@email.com', amount: '+$39.00', avatar: '/avatars/05.png' },
]
</script>

<template>
  <div class="space-y-8">
    <div v-for="sale in sales" :key="sale.email" class="flex items-center">
      <Avatar class="h-9 w-9">
        <AvatarImage :src="sale.avatar" alt="Avatar" />
        <AvatarFallback>{{ sale.name.charAt(0) }}</AvatarFallback>
      </Avatar>
      <div class="ml-4 space-y-1">
        <p class="text-sm font-medium leading-none">{{ sale.name }}</p>
        <p class="text-sm text-muted-foreground">{{ sale.email }}</p>
      </div>
      <div class="ml-auto font-medium">{{ sale.amount }}</div>
    </div>
  </div>
</template>
