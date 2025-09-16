<script setup>
import { Head } from '@inertiajs/vue3'
import { onMounted } from 'vue'
import PrintGeneral from './PrintGeneral.vue'

const props = defineProps({ assignments: { type: Array, default: () => [] } })

onMounted(() => setTimeout(() => window.print(), 100))
</script>

<template>
  <Head title="Print Current Event Staff Assignments" />
  <PrintGeneral :assignments="props.assignments" />
</template>
