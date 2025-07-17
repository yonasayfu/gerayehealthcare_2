<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
const currentTab = ref('Overview');

function handleTabChange(tab: string) {
  currentTab.value = tab;
  currentTab.value = tab;
}

const props = defineProps({
  stats: {
    type: Object,
    required: false,
  },
});
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout>
    <div class="flex-1 space-y-4 p-8 pt-6">
      <DashboardTabs @tab-change="handleTabChange" />
      </div>
    
  </AppLayout>
</template>
