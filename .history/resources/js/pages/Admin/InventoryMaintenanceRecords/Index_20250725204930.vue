<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue'; // Import 'computed'
import debounce from 'lodash/debounce';
import { ArrowUpDown, Printer, Download, Eye, Edit3, Trash2, Search, FileText } from 'lucide-vue-next'; // Import Search, FileText
import Pagination from '@/components/Pagination.vue';
import { format } from 'date-fns'; // Import format
import type { InventoryMaintenanceRecordPagination } from '@/types'; // Import InventoryMaintenanceRecordPagination type

interface InventoryMaintenanceRecordFilters {
  search?: string;
  sort?: string;
  direction?: 'asc' | 'desc';
  per_page?: number;
}

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Maintenance Records', href: route('admin.inventory-maintenance-records.index') },
];

const props = defineProps<{
  maintenanceRecords: InventoryMaintenanceRecordPagination;
  filters: InventoryMaintenanceRecordFilters;
}>();

const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort || '');
const sortDirection = ref(props.filters.direction || 'asc');
const perPage = ref(props.filters.per_page || 10);

// Create a computed property for the formatted date string
const formattedGeneratedDate = computed(() => {
  return format(new Date(), 'PPP p'); // Use the imported format function here
});

// Trigger search, sort, pagination
watch([search, sortField, sortDirection, perPage], debounce(() => {
  const params: Record<string, string | number> = {
    search: search.value,
    direction: sortDirection.value,
    per_page: perPage.value,
  };

  // Only add sort parameter if sortField.value is not an empty string
  if (sortField.value) {
    params.sort = sortField.value;
  }

  router.get(route('admin.inventory-maintenance-records.index'), params, {
    preserveState: true,
    replace: true,
  });
}, 500));

const toggleSort = (field: string) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};

const destroy = (id: number) => {
  if (confirm('Are you sure you want to delete this record?')) {
    router.delete(route('admin.inventory-maintenance-records.destroy', id), {
      preserveScroll: true,
    });
  }
};

function exportData(type: 'csv' | 'pdf') {
  window.open(route('admin.inventory-maintenance-records.export', { type }), '_blank');
}

function printCurrentView() {
  // Trigger print for the current view of the table
  setTimeout(() => {
    try {
      window.print();
    } catch (error) {
      console.error('Print failed:', error);
      alert('Failed to open print dialog for current view. Please check your browser settings or try again.');
    }
  }, 100); // Small delay for reliability
}

const printAllMaintenanceRecords = () => {
    // This will call your InventoryMaintenanceRecordController@generatePdf method
    window.open(route('admin.inventory-maintenance-records.generatePdf'), '_blank');
              <tr>
                <th class="p-2 cursor-pointer" @click="toggleSort('item_id')">Item <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('scheduled_date')">Scheduled Date <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('actual_date')">Actual Date <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('performed_by')">Performed By <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 cursor-pointer" @click="toggleSort('status')">Status <ArrowUpDown class="inline w-4 h-4 ml-1" /></th>
                <th class="p-2 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="record in maintenanceRecords.data" :key="record.id" class="border-t">
                <td class="p-2">{{ record.item.name }}</td>
                <td class="p-2">{{ record.scheduled_date }}</td>
                <td class="p-2">{{ record.actual_date }}</td>
                <td class="p-2">{{ record.performed_by }}</td>
                <td class="p-2">{{ record.status }}</td>
                <td class="p-2 text-right">
                  <div class="inline-flex items-center justify-end space-x-2">
                    <Link :href="route('admin.inventory-maintenance-records.show', record.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600" title="View Details"><Eye class="w-4 h-4" /></Link>
                    <Link :href="route('admin.inventory-maintenance-records.edit', record.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900 text-blue-600" title="Edit"><Edit3 class="w-4 h-4" /></Link>
                    <button @click="destroy(record.id)" class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 dark:hover:bg-red-900 text-red-600" title="Delete"><Trash2 class="w-4 h-4" /></button>
                  </div>
                </td>
              </tr>
              <tr v-if="maintenanceRecords.data.length === 0">
                <td colspan="6" class="text-center p-4 text-muted-foreground">No maintenance records found.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
          <Pagination :links="maintenanceRecords.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>