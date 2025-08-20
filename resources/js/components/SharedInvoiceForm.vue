<template>
    <form @submit.prevent="submit">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Invoice ID -->
                <div>
                    <InputLabel for="invoice_id" value="Invoice" />
                    <select
                        id="invoice_id"
                        v-model="form.invoice_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required
                    >
                        <option value="" disabled>Select an Invoice</option>
                        <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">
                            {{ invoice.invoice_number }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.invoice_id" />
                </div>

                <!-- Partner ID -->
                <div>
                    <InputLabel for="partner_id" value="Partner" />
                    <select
                        id="partner_id"
                        v-model="form.partner_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required
                    >
                        <option value="" disabled>Select a Partner</option>
                        <option v-for="partner in partners" :key="partner.id" :value="partner.id">
                            {{ partner.name }}
                        </option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.partner_id" />
                </div>

                <!-- Share Date -->
                <div>
                    <InputLabel for="share_date" value="Share Date" />
                    <TextInput
                        id="share_date"
                        v-model="form.share_date"
                        type="date"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.share_date" />
                </div>

                <!-- Status -->
                <div>
                    <InputLabel for="status" value="Status" />
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required
                    >
                        <option value="Shared">Shared</option>
                        <option value="Acknowledged">Acknowledged</option>
                        <option value="Action-Required">Action-Required</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.status" />
                </div>

                <!-- Notes -->
                <div>
                    <InputLabel for="notes" value="Notes" />
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.notes" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton :disabled="form.processing">
                    {{ editMode ? 'Update' : 'Create' }} Shared Invoice
                </PrimaryButton>
            </div>
        </div>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/components/InputLabel.vue';
import TextInput from '@/components/TextInput.vue';
import InputError from '@/components/InputError.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';

const props = defineProps({
    sharedInvoice: {
        type: Object,
        default: null,
    },
    invoices: {
        type: Array,
        required: true,
    },
    partners: {
        type: Array,
        required: true,
    },
    staff: { // Although not directly used in form, it's passed for context if needed
        type: Array,
        required: true,
    },
    editMode: {
        type: Boolean,
        default: false,
    },
});

const form = useForm({
    invoice_id: props.sharedInvoice ? props.sharedInvoice.invoice_id : '',
    partner_id: props.sharedInvoice ? props.sharedInvoice.partner_id : '',
    share_date: props.sharedInvoice ? props.sharedInvoice.share_date : '',
    status: props.sharedInvoice ? props.sharedInvoice.status : 'Shared',
    notes: props.sharedInvoice ? props.sharedInvoice.notes : '',
});

const submit = () => {
    if (props.editMode) {
        form.put(route('admin.shared-invoices.update', props.sharedInvoice.id));
    } else {
        form.post(route('admin.shared-invoices.store'));
    }
};
</script>
