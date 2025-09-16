<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import PrintGeneral from './PrintGeneral.vue'

const props = defineProps<{ inventoryItem: any }>()

onMounted(() => setTimeout(() => window.print(), 100))
</script>

<template>
  <Head :title="`Print Inventory Item: ${props.inventoryItem?.name}`" />
  <PrintGeneral title="Inventory Item" :inventory-item="props.inventoryItem" />
</template>
