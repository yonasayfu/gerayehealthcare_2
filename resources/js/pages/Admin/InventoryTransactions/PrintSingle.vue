<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import PrintGeneral from './PrintGeneral.vue'

const props = defineProps<{ inventoryTransaction: any }>()

onMounted(() => setTimeout(() => window.print(), 100))
</script>

<template>
  <Head :title="`Print Inventory Transaction: ${props.inventoryTransaction?.id}`" />
  <PrintGeneral title="Inventory Transaction" :inventory-transaction="props.inventoryTransaction" />
</template>
