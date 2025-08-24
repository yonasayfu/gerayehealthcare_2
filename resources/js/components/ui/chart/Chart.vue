<script setup lang="ts">
import { defineAsyncComponent } from 'vue'

// Lazy-load chart.js and vue-chartjs only when needed to keep main bundle smaller
const BarAsync = defineAsyncComponent(async () => {
  const [{ Bar }, chartjs] = await Promise.all([
    import('vue-chartjs'),
    import('chart.js')
  ])
  const { Chart, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } = chartjs as any
  Chart.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
  return { default: Bar }
})

const props = defineProps({
  chartData: {
    type: Object,
    required: true
  },
  chartOptions: {
    type: Object,
    default: () => ({
      responsive: true,
      maintainAspectRatio: false
    })
  }
})
</script>

<template>
  <BarAsync :data="chartData" :options="chartOptions" />
</template>
