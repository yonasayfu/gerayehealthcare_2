<script setup>
//import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import Pagination from '@/Components/Pagination.vue';
import { format } from 'date-fns';

const props = defineProps({
    visitServices: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
    router.get(route('admin.visit-services.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const destroy = (id) => {
    if (confirm('Are you sure you want to cancel this visit?')) {
        router.delete(route('admin.visit-services.destroy', id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return format(new Date(dateString), 'MMM dd, yyyy, hh:mm a');
};
</script>

<template>
    <Head title="Visit Services" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Visit Services Management</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <input
                                type="text"
                                v-model="search"
                                placeholder="Search by Patient or Staff..."
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            />
                            <Link
                                :href="route('admin.visit-services.create')"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                            >
                                Schedule Visit
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Staff</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled At</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="visit in visitServices.data" :key="visit.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ visit.patient?.full_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ visit.staff?.first_name }} {{ visit.staff?.last_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(visit.scheduled_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                                'bg-yellow-100 text-yellow-800': visit.status === 'Pending',
                                                'bg-green-100 text-green-800': visit.status === 'Completed',
                                                'bg-red-100 text-red-800': visit.status === 'Cancelled',
                                            }">
                                                {{ visit.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('admin.visit-services.edit', visit.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</Link>
                                            <button @click="destroy(visit.id)" class="text-red-600 hover:text-red-900">Cancel</button>
                                        </td>
                                    </tr>
                                    <tr v-if="visitServices.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No visits found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="visitServices.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>