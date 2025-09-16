<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import PrintGeneral from './PrintGeneral.vue'

const props = defineProps<{ inventoryTransactions: any[] }>()

onMounted(() => setTimeout(() => window.print(), 100))
</script>

<template>
  <Head title="Print All Inventory Transactions" />
  <PrintGeneral title="All Inventory Transactions" :inventory-transactions="props.inventoryTransactions" />
</template>
