<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
            <div class="mx-auto max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </div>
        <slot />
    </AppLayout>
</template>
