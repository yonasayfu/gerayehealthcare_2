<template>
  <Head title="Shared Invoices" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6 print:p-0 print:space-y-0">

            <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Shared Invoices</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage shared invoices</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.shared-invoices.create')" class="btn-glass">
              <span>Add Shared Invoice</span>
            </Link>
            <button @click="exportCsv()" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
          <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
          <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
          <p class="text-gray-600 dark:text-gray-400 print-document-title">Shared Invoices (Current View)</p>
        </div>
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 p-3 print:hidden">
          <div class="relative w-full md:w-1/3">
            <input
              type="text"
              v-model="search"
              placeholder="Search shared invoices..."
              class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-3 py-2.5"
            />
          </div>
          <div class="flex items-center gap-2">
            <label for="perPage" class="text-sm text-gray-700 dark:text-gray-300">Per Page:</label>
            <select id="perPage" v-model="perPage" class="rounded-md border-gray-300 dark:bg-gray-800 dark:text-white">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-200">
          <thead class="bg-gray-100 dark:bg-gray-800 text-xs uppercase text-muted-foreground">
            <tr>
              <th class="px-6 py-3">ID</th>
              <th class="px-6 py-3">Invoice Number</th>
              <th class="px-6 py-3">Partner Name</th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('share_date')">
                Share Date <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3 cursor-pointer" @click="toggleSort('status')">
                Status <ArrowUpDown class="inline w-3.5 h-3.5 ml-1 align-middle print:hidden" />
              </th>
              <th class="px-6 py-3">Shared By</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in sharedInvoices.data" :key="invoice.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4">{{ invoice.id }}</td>
              <td class="px-6 py-4">{{ invoice.invoice ? invoice.invoice.invoice_number : 'N/A' }}</td>
              <td class="px-6 py-4">{{ invoice.partner ? invoice.partner.name : 'N/A' }}</td>
              <td class="px-6 py-4">{{ invoice.share_date }}</td>
              <td class="px-6 py-4">{{ invoice.status }}</td>
              <td class="px-6 py-4">{{ invoice.shared_by ? invoice.shared_by.first_name + ' ' + invoice.shared_by.last_name : 'N/A' }}</td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link :href="route('admin.shared-invoices.show', invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500" title="View Details">
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link :href="route('admin.shared-invoices.edit', invoice.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit">
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button @click="deleteInvoice(invoice.id)" class="text-red-600 hover:text-red-800 inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!sharedInvoices.data || sharedInvoices.data.length === 0">
              <td colspan="7" class="text-center px-6 py-4 text-gray-400">No shared invoices found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <Pagination v-if="sharedInvoices.data && sharedInvoices.data.length > 0" :links="sharedInvoices.links" class="mt-6 flex justify-center print:hidden" />

      <!-- Print-only footer -->
      <div class="hidden print:block text-center mt-4 text-sm text-gray-500">
        <hr class="my-2 border-gray-300">
        <p>Printed on: {{ new Date().toLocaleString() }}</p>
      </div>
    </div>
  </AppLayout>
</template>


<style>
@media print {
  @page {
    size: A4 landscape;
    margin: 0.5cm;
  }
  body {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color: #000 !important;
  }
  .hidden.print\:block { display: block !important; }
  .print-header-content {
    padding-top: 0.5cm !important;
    padding-bottom: 0.5cm !important;
    margin-bottom: 0.8cm !important;
  }
  .print-logo {
    max-width: 150px;
    max-height: 50px;
    margin-bottom: 0.5rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
  .print-clinic-name { font-size: 1.8rem !important; margin-bottom: 0.2rem !important; line-height: 1.2 !important; }
  .print-document-title { font-size: 0.9rem !important; color: #555 !important; }
}
</style>
