<script setup lang="ts">
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'

const props = defineProps<{ 
  form: any,
  platforms?: Array<{ id: number; name: string }>,
  staffMembers?: Array<{ id: number; full_name: string }>,
  campaignTypes?: string[],
  statuses?: string[],
}>()
</script>

<template>
  <div class="grid grid-cols-6 gap-6">
    <div class="col-span-6 sm:col-span-3">
      <label for="campaign_name" class="block text-sm font-medium text-gray-900 dark:text-white">Campaign Name</label>
      <TextInput id="campaign_name" type="text" class="mt-1 block w-full" v-model="props.form.campaign_name" required />
      <InputError class="mt-2" :message="props.form.errors.campaign_name" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="platform_id" class="block text-sm font-medium text-gray-900 dark:text-white">Platform</label>
      <select id="platform_id" v-model="props.form.platform_id" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800">
        <option value="">Select Platform</option>
        <option v-for="p in props.platforms || []" :key="p.id" :value="p.id">{{ p.name }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.platform_id" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="campaign_type" class="block text-sm font-medium text-gray-900 dark:text-white">Campaign Type</label>
      <select id="campaign_type" v-model="props.form.campaign_type" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800">
        <option value="">Select Type</option>
        <option v-for="t in props.campaignTypes || []" :key="t" :value="t">{{ t }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.campaign_type" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="status" class="block text-sm font-medium text-gray-900 dark:text-white">Status</label>
      <select id="status" v-model="props.form.status" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800">
        <option v-for="s in props.statuses || []" :key="s" :value="s">{{ s }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.status" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="start_date" class="block text-sm font-medium text-gray-900 dark:text-white">Start Date</label>
      <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="props.form.start_date" />
      <InputError class="mt-2" :message="props.form.errors.start_date" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="end_date" class="block text-sm font-medium text-gray-900 dark:text-white">End Date</label>
      <TextInput id="end_date" type="date" class="mt-1 block w-full" v-model="props.form.end_date" />
      <InputError class="mt-2" :message="props.form.errors.end_date" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="budget_allocated" class="block text-sm font-medium text-gray-900 dark:text-white">Budget Allocated</label>
      <TextInput id="budget_allocated" type="number" step="0.01" class="mt-1 block w-full" v-model="props.form.budget_allocated" />
      <InputError class="mt-2" :message="props.form.errors.budget_allocated" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="budget_spent" class="block text-sm font-medium text-gray-900 dark:text-white">Budget Spent</label>
      <TextInput id="budget_spent" type="number" step="0.01" class="mt-1 block w-full" v-model="props.form.budget_spent" />
      <InputError class="mt-2" :message="props.form.errors.budget_spent" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="assigned_staff_id" class="block text-sm font-medium text-gray-900 dark:text-white">Assigned Staff</label>
      <select id="assigned_staff_id" v-model="props.form.assigned_staff_id" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800">
        <option value="">Select Staff</option>
        <option v-for="s in props.staffMembers || []" :key="s.id" :value="s.id">{{ s.full_name }}</option>
      </select>
      <InputError class="mt-2" :message="props.form.errors.assigned_staff_id" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="utm_campaign" class="block text-sm font-medium text-gray-900 dark:text-white">UTM Campaign</label>
      <TextInput id="utm_campaign" type="text" class="mt-1 block w-full" v-model="props.form.utm_campaign" />
      <InputError class="mt-2" :message="props.form.errors.utm_campaign" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="utm_source" class="block text-sm font-medium text-gray-900 dark:text-white">UTM Source</label>
      <TextInput id="utm_source" type="text" class="mt-1 block w-full" v-model="props.form.utm_source" />
      <InputError class="mt-2" :message="props.form.errors.utm_source" />
    </div>

    <div class="col-span-6 sm:col-span-3">
      <label for="utm_medium" class="block text-sm font-medium text-gray-900 dark:text-white">UTM Medium</label>
      <TextInput id="utm_medium" type="text" class="mt-1 block w-full" v-model="props.form.utm_medium" />
      <InputError class="mt-2" :message="props.form.errors.utm_medium" />
    </div>

    <div class="col-span-6">
      <label for="target_audience" class="block text-sm font-medium text-gray-900 dark:text-white">Target Audience (JSON)</label>
      <textarea id="target_audience" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800" v-model="props.form.target_audience"></textarea>
      <InputError class="mt-2" :message="props.form.errors.target_audience" />
    </div>

    <div class="col-span-6">
      <label for="goals" class="block text-sm font-medium text-gray-900 dark:text-white">Goals (JSON)</label>
      <textarea id="goals" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-800" v-model="props.form.goals"></textarea>
      <InputError class="mt-2" :message="props.form.errors.goals" />
    </div>
  </div>
</template>

