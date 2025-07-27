<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const breadcrumbs = [
  { title: 'Dashboard', href: route('dashboard') },
  { title: 'Suppliers', href: route('admin.suppliers.index') },
  { title: 'Create', href: route('admin.suppliers.create') },
];

const form = useForm({
  name: null,
  contact_person: null,
  email: null,
  phone: null,
  address: null,
});

function submit() {
  form.post(route('admin.suppliers.store'));
}
</script>

<template>
  <Head title="Create Supplier" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="bg-white border border-4 rounded-lg shadow relative m-10">

    <div class="flex items-start justify-between p-5 border-b rounded-t">
        <h3 class="text-xl font-semibold">
            Add New Supplier
        </h3>
        <Link :href="route('admin.suppliers.index')" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
           <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </Link>
    </div>

    <div class="p-6 space-y-6">
        <form @submit.prevent="submit">
            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 sm:col-span-3">
                    <label for="name" class="text-sm font-medium text-gray-900 block mb-2">Supplier Name</label>
                    <input type="text" name="name" id="name" v-model="form.name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5" required="">
                    <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <label for="contact_person" class="text-sm font-medium text-gray-900 block mb-2">Contact Person</label>
                    <input type="text" name="contact_person" id="contact_person" v-model="form.contact_person" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    <div v-if="form.errors.contact_person" class="text-red-500 text-sm mt-1">{{ form.errors.contact_person }}</div>
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <label for="email" class="text-sm font-medium text-gray-900 block mb-2">Email</label>
                    <input type="email" name="email" id="email" v-model="form.email" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <label for="phone" class="text-sm font-medium text-gray-900 block mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" v-model="form.phone" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5">
                    <div v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</div>
                </div>
                <div class="col-span-full">
                    <label for="address" class="text-sm font-medium text-gray-900 block mb-2">Address</label>
                    <textarea id="address" rows="6" v-model="form.address" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-4"></textarea>
                    <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
                </div>
            </div>
        </form>
    </div>

    <div class="p-6 border-t border-gray-200 rounded-b">
        <button @click="submit" :disabled="form.processing" class="text-white bg-cyan-600 hover:bg-cyan-700 focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">
          {{ form.processing ? 'Saving...' : 'Save Supplier' }}
        </button>
    </div>

</div>
  </AppLayout>
</template>
