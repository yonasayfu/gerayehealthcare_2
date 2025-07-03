<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
defineProps({ staff: Object })
</script>

<template>
  <Head :title="`${staff.first_name} ${staff.last_name}`" />

  <AdminLayout>
    <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded-lg space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold">Staff Profile</h1>
        <Link
          :href="route('staff.index')"
          class="text-sm text-blue-600 hover:underline"
        >
          ← Back to List
        </Link>
      </div>

      <div class="flex flex-col sm:flex-row gap-6">
        <img
          v-if="staff.photo"
          :src="`/storage/${staff.photo}`"
          alt="Staff Photo"
          class="w-40 h-40 object-cover rounded-full border"
        />
        <div>
          <p><strong>Name:</strong> {{ staff.first_name }} {{ staff.last_name }}</p>
          <p><strong>Email:</strong> {{ staff.email }}</p>
          <p><strong>Phone:</strong> {{ staff.phone }}</p>
          <p><strong>Position:</strong> {{ staff.position }}</p>
          <p><strong>Department:</strong> {{ staff.department }}</p>
          <p><strong>Status:</strong> {{ staff.status }}</p>
          <p><strong>Hire Date:</strong> {{ staff.hire_date }}</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
