<template>
    <AppLayout title="Create Ethiopian Calendar Day">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Ethiopian Calendar Day
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label for="gregorian_date" class="block text-sm font-medium text-gray-700">Gregorian Date</label>
                            <input type="date" id="gregorian_date" v-model="form.gregorian_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <div v-if="form.errors.gregorian_date" class="text-red-500 text-sm mt-1">{{ form.errors.gregorian_date }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="ethiopian_date" class="block text-sm font-medium text-gray-700">Ethiopian Date</label>
                            <EthiopianDatePicker
                                id="ethiopian_date"
                                v-model="form.gregorian_date"
                                @update:ethiopianDate="form.ethiopian_date = $event"
                            />
                            <div v-if="form.errors.ethiopian_date" class="text-red-500 text-sm mt-1">{{ form.errors.ethiopian_date }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="is_holiday" class="flex items-center">
                                <input type="checkbox" id="is_holiday" v-model="form.is_holiday" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-600">Is Holiday</span>
                            </label>
                            <div v-if="form.errors.is_holiday" class="text-red-500 text-sm mt-1">{{ form.errors.is_holiday }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="region" class="block text-sm font-medium text-gray-700">Region</label>
                            <select id="region" v-model="form.region" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Select Region</option>
                                <option value="Addis Ababa">Addis Ababa</option>
                                <option value="Oromia">Oromia</option>
                                <option value="Amhara">Amhara</option>
                                <option value="Tigray">Tigray</option>
                                <option value="SNNPR">SNNPR</option>
                                <option value="Sidama">Sidama</option>
                                <option value="Somali">Somali</option>
                                <option value="Benishangul-Gumuz">Benishangul-Gumuz</option>
                                <option value="Gambela">Gambela</option>
                                <option value="Harari">Harari</option>
                                <option value="Afar">Afar</option>
                                <option value="Dire Dawa">Dire Dawa</option>
                            </select>
                            <div v-if="form.errors.region" class="text-red-500 text-sm mt-1">{{ form.errors.region }}</div>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <Link :href="route('admin.ethiopian-calendar-days.index')" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                Cancel
                            </Link>
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Create Day
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import EthiopianDatePicker from '@/components/EthiopianDatePicker.vue';

const form = useForm({
    gregorian_date: '',
    ethiopian_date: '',
    description: '',
    is_holiday: false,
    region: '',
});

const submit = () => {
    form.post(route('admin.ethiopian-calendar-days.store'));
};
</script>
