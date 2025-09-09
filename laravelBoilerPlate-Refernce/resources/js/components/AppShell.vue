<script setup lang="ts">
import { SidebarProvider } from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import ConfirmModal from '@/components/ConfirmModal.vue';
import { ref } from 'vue';
import { eventBus } from '@/lib/eventBus';

interface Props {
    variant?: 'header' | 'sidebar';
}

defineProps<Props>();

const isOpen = usePage().props.sidebarOpen;

const confirmOpen = ref(false);
const confirmOptions = ref({});

eventBus.on('confirm:open', (options) => {
  confirmOpen.value = true;
  confirmOptions.value = options;
});

const onConfirm = () => {
  confirmOptions.value.__resolve(true);
  confirmOpen.value = false;
};

const onCancel = () => {
  confirmOptions.value.__resolve(false);
  confirmOpen.value = false;
};
</script>

<template>
    <div v-if="variant === 'header'" class="flex min-h-screen w-full flex-col">
        <slot />
    </div>
    <SidebarProvider v-else :default-open="isOpen">
        <slot />
    </SidebarProvider>
    <ConfirmModal
      :open="confirmOpen"
      v-bind="confirmOptions"
      @confirm="onConfirm"
      @cancel="onCancel"
      @update:open="confirmOpen = $event"
    />
</template>