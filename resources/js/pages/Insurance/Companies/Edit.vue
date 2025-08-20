<template>
    <Head :title="`Edit Insurance Company: ${form.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="bg-white border border-4 rounded-lg shadow relative m-10">

        <div class="flex items-start justify-between p-5 border-b rounded-t">
          <h3 class="text-xl font-semibold">Edit Insurance Company</h3>
          <Link :href="route('admin.insurance-companies.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
          </Link>
        </div>

        <div class="p-6 space-y-6">
          <form @submit.prevent="submit">
            <div v-if="Object.keys(form.errors).length" class="rounded-md bg-red-50 p-4 border border-red-200 text-red-800 text-sm">
              Please correct the highlighted errors and try again.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" v-model="form.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
              </div>

              <div class="space-y-1">
                <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                <input type="text" id="contact_person" v-model="form.contact_person" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <div v-if="form.errors.contact_person" class="text-red-500 text-sm mt-1">{{ form.errors.contact_person }}</div>
              </div>

              <div class="space-y-1">
                <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                <input type="email" id="contact_email" v-model="form.contact_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <div v-if="form.errors.contact_email" class="text-red-500 text-sm mt-1">{{ form.errors.contact_email }}</div>
              </div>

              <div class="space-y-1">
                <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                <input type="text" id="contact_phone" v-model="form.contact_phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <div v-if="form.errors.contact_phone" class="text-red-500 text-sm mt-1">{{ form.errors.contact_phone }}</div>
              </div>

              <div class="space-y-1 md:col-span-2">
                <label for="address" class="block text sm font-medium text-gray-700">Address</label>
                <textarea id="address" v-model="form.address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
              </div>
            </div>
          </form>
        </div>

        <div class="p-6 border-t border-gray-200 rounded-b">
          <div class="flex flex-wrap gap-2">
            <Link :href="route('admin.insurance-companies.index')" class="btn btn-outline">Cancel</Link>
            <button @click="submit" :disabled="form.processing" class="btn btn-primary" type="submit">
              {{ form.processing ? 'Updating...' : 'Update Company' }}
            </button>
          </div>
        </div>

      </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    insuranceCompany: Object,
});

const form = useForm({
    name: props.insuranceCompany.name,
    contact_person: props.insuranceCompany.contact_person,
    contact_email: props.insuranceCompany.contact_email,
    contact_phone: props.insuranceCompany.contact_phone,
    address: props.insuranceCompany.address,
});

const submit = () => {
    form.put(route('admin.insurance-companies.update', props.insuranceCompany.id));
};

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Insurance Companies', href: route('admin.insurance-companies.index') },
  { title: 'Edit', href: route('admin.insurance-companies.edit', props.insuranceCompany.id) },
];
</script>
