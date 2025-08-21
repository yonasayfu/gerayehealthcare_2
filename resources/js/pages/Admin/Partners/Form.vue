<script setup lang="ts">
import { ref, watch, onBeforeUnmount } from 'vue';

const props = defineProps<{
  form: any,
  staff: Array<{ id: number; name: string }>,
}>()

const partnerTypes = [
  'Corporate',
  'NGO',
  'School',
  'Bank',
  'Government Agency',
];

const engagementStatuses = [
  'Prospect',
  'Active',
  'Inactive',
];
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Name -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Name</label>
      <input
        v-model="form.name"
        type="text"
        placeholder="Partner Name"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.name">{{ form.errors.name }}</span>
    </div>

    <!-- Type -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Type</label>
      <select v-model="form.type" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option disabled value="">Select Type</option>
        <option v-for="typeOption in partnerTypes" :key="typeOption" :value="typeOption">{{ typeOption }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.type">{{ form.errors.type }}</span>
    </div>

    <!-- Contact Person -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Contact Person</label>
      <input
        v-model="form.contact_person"
        type="text"
        placeholder="Contact Person Name"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.contact_person">{{ form.errors.contact_person }}</span>
    </div>

    <!-- Email -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input
        v-model="form.email"
        type="email"
        placeholder="Email Address"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.email">{{ form.errors.email }}</span>
    </div>

    <!-- Phone -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Phone</label>
      <input
        v-model="form.phone"
        type="text"
        placeholder="Phone Number"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.phone">{{ form.errors.phone }}</span>
    </div>

    <!-- Address -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Address</label>
      <textarea
        v-model="form.address"
        placeholder="Full Address"
        rows="3"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      ></textarea>
      <span class="text-red-500 text-xs" v-if="form.errors.address">{{ form.errors.address }}</span>
    </div>

    <!-- Engagement Status -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Engagement Status</label>
      <select v-model="form.engagement_status" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option disabled value="">Select Status</option>
        <option v-for="statusOption in engagementStatuses" :key="statusOption" :value="statusOption">{{ statusOption }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.engagement_status">{{ form.errors.engagement_status }}</span>
    </div>

    <!-- Account Manager -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Account Manager</label>
      <select v-model="form.account_manager_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Account Manager</option>
        <option v-for="staffMember in staff" :key="staffMember.id" :value="staffMember.id">{{ staffMember.name }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.account_manager_id">{{ form.errors.account_manager_id }}</span>
    </div>

    <!-- Notes -->
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Notes</label>
      <textarea
        v-model="form.notes"
        placeholder="Any additional notes about the partner"
        rows="5"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      ></textarea>
      <span class="text-red-500 text-xs" v-if="form.errors.notes">{{ form.errors.notes }}</span>
    </div>
  </div>
</template>
