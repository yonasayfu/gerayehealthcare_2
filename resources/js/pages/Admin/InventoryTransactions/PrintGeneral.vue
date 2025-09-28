<script setup lang="ts">
// A generalized print component that renders either a single
// inventory transaction or a list of transactions in a print-friendly layout.

const props = defineProps<{
  title?: string
  inventoryTransaction?: any
  inventoryTransactions?: any[]
}>()

const isList = Array.isArray(props.inventoryTransactions)
</script>

<template>
  <div class="print-document">
    <div class="print-header">
      <img src="/images/geraye_logo.jpeg" alt="Geraye Home Care Services Logo" style="max-width: 150px; margin-bottom: 10px;" />
      <h1>{{ props.title || (isList ? 'Inventory Transactions' : 'Inventory Transaction') }}</h1>
      <p>Generated on: {{ new Date().toLocaleString() }}</p>
    </div>

    <!-- List Mode -->
    <template v-if="isList">
      <table class="print-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Item</th>
            <th>Type</th>
            <th>Qty</th>
            <th>Performed By</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(transaction, idx) in props.inventoryTransactions" :key="transaction.id">
            <td>{{ idx + 1 }}</td>
            <td>{{ transaction.item?.name ?? '-' }}</td>
            <td>{{ transaction.transaction_type ?? '-' }}</td>
            <td>{{ transaction.quantity ?? '-' }}</td>
            <td>{{ transaction.performed_by ? (transaction.performed_by.first_name + ' ' + transaction.performed_by.last_name) : '-' }}</td>
            <td>{{ transaction.created_at ? new Date(transaction.created_at).toLocaleString() : '-' }}</td>
          </tr>
        </tbody>
      </table>
    </template>

    <!-- Single Mode -->
    <template v-else>
      <div class="details-section">
        <p><strong>ID:</strong> {{ props.inventoryTransaction?.id }}</p>
        <p><strong>Item:</strong> {{ props.inventoryTransaction?.item?.name ?? '-' }}</p>
        <p><strong>Type:</strong> {{ props.inventoryTransaction?.transaction_type ?? '-' }}</p>
        <p><strong>Quantity:</strong> {{ props.inventoryTransaction?.quantity ?? '-' }}</p>
        <p><strong>From:</strong> {{ props.inventoryTransaction?.from_location ?? '-' }}</p>
        <p><strong>To:</strong> {{ props.inventoryTransaction?.to_location ?? '-' }}</p>
        <p><strong>Performed By:</strong> {{ props.inventoryTransaction?.performed_by ? (props.inventoryTransaction?.performed_by.first_name + ' ' + props.inventoryTransaction?.performed_by.last_name) : '-' }}</p>
        <p><strong>Date:</strong> {{ props.inventoryTransaction?.created_at ? new Date(props.inventoryTransaction?.created_at).toLocaleString() : '-' }}</p>
        <p><strong>Notes:</strong> {{ props.inventoryTransaction?.notes ?? '-' }}</p>
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
  @page { size: A4; margin: 15mm; }
  body { margin: 0; padding: 0; background: #fff; color: #000; -webkit-print-color-adjust: exact; }
  .print-document { width: auto; min-height: auto; margin: 0; padding: 0; }
  .print-footer { position: fixed; bottom: 0; left: 0; right: 0; }
  .print-header h1 { font-size: 18pt; }
  .print-header p { font-size: 9pt; }
  .print-table th, .print-table td { font-size: 8pt; padding: 6pt; }
  .details-section p { font-size: 8pt; }
}
</style>

