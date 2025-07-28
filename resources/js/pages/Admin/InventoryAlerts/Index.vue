<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Inventory Alerts', href: route('admin.inventory-alerts.index') },
];

const props = defineProps<{
  inventoryAlerts: {
    data: Array<any>;
    links: Array<any>;
    meta: {
      current_page: number;
      from: number;
      last_page: number;
      per_page: number;
      to: number;
      total: number;
    };
  };
  filters: {
    type: Object,
    default: () => ({
      search: '',
      per_page: 10,
    }),
  },
}>();
