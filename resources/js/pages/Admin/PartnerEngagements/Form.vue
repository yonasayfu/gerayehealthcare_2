<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
  form: any,
  partners: Array<{ id: number; name: string }>,
  staff: Array<{ id: number; name: string }>,
}>()

const engagementTypes = [
  'Meeting',
  'Call',
  'Email',
  'Event',
];
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Partner -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Partner</label>
      <select v-model="form.partner_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Partner</option>
        <option v-for="partner in partners" :key="partner.id" :value="partner.id">{{ partner.name }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.partner_id">{{ form.errors.partner_id }}</span>
    </div>

    <!-- Staff -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Staff</label>
      <select v-model="form.staff_id" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option :value="null">Select Staff</option>
        <option v-for="staffMember in staff" :key="staffMember.id" :value="staffMember.id">{{ staffMember.name }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.staff_id">{{ form.errors.staff_id }}</span>
    </div>

    <!-- Engagement Type -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Engagement Type</label>
      <select v-model="form.engagement_type" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
        <option value="">Select Type</option>
        <option v-for="typeOption in engagementTypes" :key="typeOption" :value="typeOption">{{ typeOption }}</option>
      </select>
      <span class="text-red-500 text-xs" v-if="form.errors.engagement_type">{{ form.errors.engagement_type }}</span>
    </div>

    <!-- Summary -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Summary</label>
      <textarea
        v-model="form.summary"
        placeholder="Brief summary of the engagement"
        rows="3"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      ></textarea>
      <span class="text-red-500 text-xs" v-if="form.errors.summary">{{ form.errors.summary }}</span>
    </div>

    <!-- Engagement Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Engagement Date</label>
      <input
        v-model="form.engagement_date"
        type="datetime-local"
        required
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.engagement_date">{{ form.errors.engagement_date }}</span>
    </div>

    <!-- Follow Up Date -->
    <div>
      <label class="block text-sm font-medium text-gray-700">Follow Up Date</label>
      <input
        v-model="form.follow_up_date"
        type="date"
        class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5"
      />
      <span class="text-red-500 text-xs" v-if="form.errors.follow_up_date">{{ form.errors.follow_up_date }}</span>
    </div>
  </div>
</template>
