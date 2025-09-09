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
      This is the main layout container for pages with a sidebar.
      The SidebarInset component automatically handles the responsive left margin/padding
      based on whether the sidebar is open or collapsed.
    -->
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
        <!-- 
          THE FIX IS HERE: We wrap the <slot /> in a <main> tag.
          This ensures the page content is correctly contained within the responsive
          area provided by SidebarInset, preventing overflow.
        -->
        <main class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden min-w-0">
            <slot />
        </main>
    </SidebarInset>

    <!-- This is the fallback for layouts without a sidebar (e.g., login page), it remains unchanged. -->
    <main v-else class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl" :class="className">
        <slot />
    </main>
</template>
