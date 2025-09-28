<script setup>
const props = defineProps({ assignments: { type: Array, default: () => [] } })
</script>

<template>
  <div class="print-document">
    <div class="print-header">
      <img src="/images/geraye_logo.jpeg" alt="Geraye Home Care Services Logo" style="max-width: 130px; margin-bottom: 10px;" />
      <h1>Event Staff Assignments</h1>
      <p>Generated on: {{ new Date().toLocaleString() }}</p>
    </div>

    <table class="print-table">
      <thead>
        <tr>
          <th>Event</th>
          <th>Staff</th>
          <th>Role</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="a in props.assignments" :key="a.id">
          <td>{{ a.event?.title ?? a.event_title ?? `#${a.event_id}` }}</td>
          <td>{{ a.staff?.full_name ?? (a.staff_first_name && a.staff_last_name ? `${a.staff_first_name} ${a.staff_last_name}` : `#${a.staff_id}`) }}</td>
          <td>{{ a.role }}</td>
          <td>{{ a.notes ?? '-' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style>
.print-document { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 12mm; box-sizing: border-box; }
.print-header { text-align: center; margin-bottom: 14mm; }
.print-header h1 { font-size: 18pt; margin-bottom: 4mm; }
.print-header p { font-size: 9pt; color: #555; }
.print-table { width: 100%; border-collapse: collapse; }
.print-table th, .print-table td { border: 1px solid #ccc; padding: 6pt 8pt; text-align: left; font-size: 9pt; }
.print-table th { background-color: #f0f0f0; }
@media print { .print-document { width: auto; min-height: auto; margin: 0; padding: 0; } }
</style>

