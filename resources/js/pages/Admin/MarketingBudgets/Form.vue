<script setup lang="ts">
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'

const props = defineProps({
  form: Object, // This form object will be passed from Create.vue or Edit.vue
  campaigns: Array, // Prop for campaigns data
  platforms: Array, // Prop for platforms data
  statuses: Array, // Prop for statuses data
});
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <InputLabel for="budget_name">Budget Name</InputLabel>
      <TextInput
        id="budget_name"
        type="text"
        class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="props.form.budget_name"
        required
        autofocus
      />
      <InputError class="mt-2" :message="props.form.errors.budget_name" />
    </div>

    <div>
      <InputLabel for="campaign_id">Campaign</InputLabel>
      <select
        id="campaign_id"
        v-model="props.form.campaign_id"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
      >
        <option value="">Select a Campaign</option>
        <option v-for="campaign in props.campaigns" :key="campaign.id" :value="campaign.id">{{ campaign.campaign_name }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.campaign_id" />
    </div>

    <div>
      <InputLabel for="platform_id">Platform</InputLabel>
      <select
        id="platform_id"
        v-model="props.form.platform_id"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
      >
        <option value="">Select a Platform</option>
        <option v-for="platform in props.platforms" :key="platform.id" :value="platform.id">{{ platform.name }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.platform_id" />
    </div>

    <div>
      <InputLabel for="status">Status</InputLabel>
      <select
        id="status"
        v-model="props.form.status"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white"
        required
      >
        <option v-for="s in props.statuses" :key="s" :value="s">{{ s }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.status" />
    </div>

    <div>
      <InputLabel for="allocated_amount">Allocated Amount</InputLabel>
      <TextInput
        id="allocated_amount"
        type="number"
        step="0.01"
        class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="props.form.allocated_amount"
        required
      />
      <InputError class="mt-2" :message="props.form.errors.allocated_amount" />
    </div>

    <div>
      <InputLabel for="spent_amount">Spent Amount</InputLabel>
      <TextInput
        id="spent_amount"
        type="number"
        step="0.01"
        class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="props.form.spent_amount"
      />
      <InputError class="mt-2" :message="props.form.errors.spent_amount" />
    </div>

    <div>
      <InputLabel for="period_start">Period Start Date</InputLabel>
      <TextInput
        id="period_start"
        type="date"
        class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="props.form.period_start"
        required
      />
      <InputError class="mt-2" :message="props.form.errors.period_start" />
    </div>

    <div>
      <InputLabel for="period_end">Period End Date</InputLabel>
      <TextInput
        id="period_end"
        type="date"
        class="mt-1 block w-full shadow-sm bg-white dark:bg-gray-800 border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 p-2.5"
        v-model="props.form.period_end"
      />
      <InputError class="mt-2" :message="props.form.errors.period_end" />
    </div>

    <div class="md:col-span-2">
      <InputLabel for="description">Description</InputLabel>
      <textarea
        id="description"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50 p-2.5"
        v-model="props.form.description"
      ></textarea>
      <InputError class="mt-2" :message="props.form.errors.description" />
    </div>
  </div>
</template>
