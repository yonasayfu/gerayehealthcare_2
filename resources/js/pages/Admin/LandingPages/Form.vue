<script setup lang="ts">
import InputLabel from '@/components/ui/label/Label.vue'
import TextInput from '@/components/ui/input/Input.vue'
import InputError from '@/components/InputError.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
  form: ReturnType<typeof useForm>;
  campaigns: any[];
}>();

const languages = ['en', 'es', 'fr', 'de', 'zh'];

// Keep a string version of JSON for the textarea, with safe parse on set
const formFieldsJson = computed<string>({
  get() {
    try {
      if (typeof props.form.form_fields === 'string') {
        // If already a string, pretty print if possible
        const parsed = JSON.parse(props.form.form_fields)
        return JSON.stringify(parsed, null, 2)
      }
      return JSON.stringify(props.form.form_fields ?? {}, null, 2)
    } catch (e) {
      // Fallback to raw string if can't parse
      return String(props.form.form_fields ?? '')
    }
  },
  set(val: string) {
    try {
      const parsed = val ? JSON.parse(val) : {}
      props.form.form_fields = parsed
    } catch (e) {
      // If invalid JSON, keep the string; backend validation can catch if needed
      props.form.form_fields = val as any
    }
  }
})
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label for="page_title" class="block text-sm font-medium text-gray-900 dark:text-white">Page Title</label>
      <TextInput
        id="page_title"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.page_title"
        required
        autofocus
      />
      <InputError class="mt-2" :message="form.errors.page_title" />
    </div>

    <div>
      <label for="page_url" class="block text-sm font-medium text-gray-900 dark:text-white">Page URL</label>
      <TextInput
        id="page_url"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.page_url"
        required
      />
      <InputError class="mt-2" :message="form.errors.page_url" />
    </div>

    <div>
      <label for="campaign_id" class="block text-sm font-medium text-gray-900 dark:text-white">Marketing Campaign</label>
      <select
        id="campaign_id"
        v-model="form.campaign_id"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50"
      >
        <option value="">Select a Campaign</option>
        <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">{{ campaign.campaign_name }}</option>
      </select>
      <InputError class="mt-2" :message="form.errors.campaign_id" />
    </div>

    <div>
      <label for="language" class="block text-sm font-medium text-gray-900 dark:text-white">Language</label>
      <select
        id="language"
        v-model="form.language"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50"
      >
        <option value="">Select Language</option>
        <option v-for="lang in languages" :key="lang" :value="lang">{{ lang.toUpperCase() }}</option>
      </select>
      <InputError class="mt-2" :message="form.errors.language" />
    </div>

    <div>
      <label for="template_used" class="block text-sm font-medium text-gray-900 dark:text-white">Template Used</label>
      <TextInput
        id="template_used"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.template_used"
      />
      <InputError class="mt-2" :message="form.errors.template_used" />
    </div>

    <div>
      <label for="conversion_goal" class="block text-sm font-medium text-gray-900 dark:text-white">Conversion Goal</label>
      <TextInput
        id="conversion_goal"
        type="text"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.conversion_goal"
      />
      <InputError class="mt-2" :message="form.errors.conversion_goal" />
    </div>

    <div>
      <label for="views" class="block text-sm font-medium text-gray-900 dark:text-white">Views</label>
      <TextInput
        id="views"
        type="number"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.views"
      />
      <InputError class="mt-2" :message="form.errors.views" />
    </div>

    <div>
      <label for="submissions" class="block text-sm font-medium text-gray-900 dark:text-white">Submissions</label>
      <TextInput
        id="submissions"
        type="number"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.submissions"
      />
      <InputError class="mt-2" :message="form.errors.submissions" />
    </div>

    <div>
      <label for="conversion_rate" class="block text-sm font-medium text-gray-900 dark:text-white">Conversion Rate</label>
      <TextInput
        id="conversion_rate"
        type="number"
        step="0.01"
        class="mt-1 block w-full bg-gray-50"
        v-model="form.conversion_rate"
      />
      <InputError class="mt-2" :message="form.errors.conversion_rate" />
    </div>

    <div class="flex items-center">
      <input
        id="is_active"
        type="checkbox"
        v-model="form.is_active"
        class="rounded border-gray-300 text-cyan-600 shadow-sm focus:ring-cyan-500"
      />
      <label for="is_active" class="ml-2 block text-sm font-medium text-gray-900 dark:text-white">Is Active</label>
      <InputError class="mt-2" :message="form.errors.is_active" />
    </div>

    <div class="md:col-span-2">
      <label for="notes" class="block text-sm font-medium text-gray-900 dark:text-white">Notes</label>
      <textarea
        id="notes"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50"
        v-model="form.notes"
      ></textarea>
      <InputError class="mt-2" :message="form.errors.notes" />
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-900 dark:text-white">Form Fields (JSON)</label>
      <textarea
        id="form_fields"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-white bg-gray-50"
        v-model="formFieldsJson"
      ></textarea>
      <InputError class="mt-2" :message="form.errors.form_fields" />
    </div>
  </div>
</template>
