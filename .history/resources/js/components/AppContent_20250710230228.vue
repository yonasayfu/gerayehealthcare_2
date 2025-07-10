<script setup lang="ts">
import { SidebarInset } from '@/components/ui/sidebar';
import { computed } from 'vue';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);
</script>

<template>
    <!-- 
      This is the main layout container. 
      It now correctly uses the SidebarInset component which will automatically handle
      the left margin/padding based on the sidebar's state (open or collapsed).
      This removes the need for fixed padding on the <main> element, fixing the responsiveness.
    -->
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
        <main class="flex-1 p-6">
            <slot />
        </main>
    </SidebarInset>

    <!-- This is the fallback for layouts without a sidebar, it remains unchanged. -->
    <main v-else class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl" :class="className">
        <slot />
    </main>
</template>
