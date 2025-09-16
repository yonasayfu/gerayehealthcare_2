<script setup lang="ts">
const props = defineProps<{
  title?: string
  inventoryItem?: any
  inventoryItems?: any[]
}>()

const isList = Array.isArray(props.inventoryItems)
</script>

<template>
  <div class="print-document">
    <div class="print-header">
      <img src="/images/geraye_logo.jpeg" alt="Geraye Home Care Services Logo" style="max-width: 150px; margin-bottom: 10px;" />
      <h1>{{ props.title || (isList ? 'Inventory Items' : 'Inventory Item') }}</h1>
      <p>Generated on: {{ new Date().toLocaleString() }}</p>
    </div>

    <template v-if="isList">
      <table class="print-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Category</th>
            <th>Type</th>
            <th>Serial Number</th>
            <th>Status</th>
            <th>Purchase Date</th>
            <th>Warranty Expiry</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, idx) in props.inventoryItems" :key="item.id">
            <td>{{ idx + 1 }}</td>
            <td>{{ item.name }}</td>
            <td>{{ item.item_category }}</td>
            <td>{{ item.item_type }}</td>
            <td>{{ item.serial_number }}</td>
            <td>{{ item.status }}</td>
            <td>{{ item.purchase_date ? new Date(item.purchase_date).toLocaleDateString() : '-' }}</td>
            <td>{{ item.warranty_expiry ? new Date(item.warranty_expiry).toLocaleDateString() : '-' }}</td>
          </tr>
        </tbody>
      </table>
    </template>

    <template v-else>
      <div class="details-section">
        <p><strong>Name:</strong> {{ props.inventoryItem?.name }}</p>
        <p><strong>Category:</strong> {{ props.inventoryItem?.item_category }}</p>
        <p><strong>Type:</strong> {{ props.inventoryItem?.item_type }}</p>
        <p><strong>Serial Number:</strong> {{ props.inventoryItem?.serial_number }}</p>
        <p><strong>Status:</strong> {{ props.inventoryItem?.status }}</p>
        <p><strong>Description:</strong> {{ props.inventoryItem?.description }}</p>
        <p><strong>Purchase Date:</strong> {{ props.inventoryItem?.purchase_date ? new Date(props.inventoryItem?.purchase_date).toLocaleDateString() : '-' }}</p>
        <p><strong>Warranty Expiry:</strong> {{ props.inventoryItem?.warranty_expiry ? new Date(props.inventoryItem?.warranty_expiry).toLocaleDateString() : '-' }}</p>
        <p><strong>Supplier:</strong> {{ props.inventoryItem?.supplier?.name ?? '-' }}</p>
        <p><strong>Next Maintenance Due:</strong> {{ props.inventoryItem?.next_maintenance_due ? new Date(props.inventoryItem?.next_maintenance_due).toLocaleDateString() : '-' }}</p>
        <p><strong>Maintenance Schedule:</strong> {{ props.inventoryItem?.maintenance_schedule ?? '-' }}</p>
        <p><strong>Notes:</strong> {{ props.inventoryItem?.notes ?? '-' }}</p>
      </div>
    </template>

    <div class="print-footer-spacer"></div>
    <div class="print-footer">
      <p>Geraye Home Care Services</p>
    </div>
  </div>
</template>

<style>
.print-document { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm; box-sizing: border-box; }
.print-header { text-align: center; margin-bottom: 15mm; }
.print-header h1 { font-size: 20pt; margin-bottom: 5mm; }
.print-header p { font-size: 9pt; color: #555; }
.details-section { margin-bottom: 10mm; border-bottom: 1px solid #eee; padding-bottom: 5mm; }
.print-table { width: 100%; border-collapse: collapse; margin-bottom: 10mm; }
.print-table th, .print-table td { border: 1px solid #ccc; padding: 8pt; text-align: left; font-size: 9pt; }
.print-table th { background-color: #f0f0f0; }
.print-footer { text-align: center; font-size: 10pt; color: #555; }
.print-footer-spacer { height: 18mm; }

@media print {
  .print-document { width: auto; min-height: auto; margin: 0; padding: 0; }
  .print-footer { position: fixed; bottom: 0; left: 0; right: 0; }
}
</style>

