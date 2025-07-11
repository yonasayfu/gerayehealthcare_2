<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import FlashMessages from './FlashMessages.vue';
import { type AppPageProps } from '@/types'; // Import AppPageProps

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const page = usePage<AppPageProps>(); // Explicitly type usePage
const isOpen = page.props.sidebarOpen;
</script>

<template>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <slot />
    </div>
    <SidebarProvider v-else :default-open="isOpen">
        <slot />
        <FlashMessages />
    </SidebarProvider>
</template>
