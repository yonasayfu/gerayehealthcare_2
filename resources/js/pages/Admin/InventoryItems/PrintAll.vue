<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import PrintGeneral from './PrintGeneral.vue'

const props = defineProps<{ inventoryItems: any[] }>()

onMounted(() => setTimeout(() => window.print(), 100))
</script>

<template>
  <Head title="Print All Inventory Items" />
  <PrintGeneral title="All Inventory Items" :inventory-items="props.inventoryItems" />
</template>
