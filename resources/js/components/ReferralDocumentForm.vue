<template>
    <form @submit.prevent="submit">
        <div class="p-6">
            <div v-if="form.errors.error" class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                {{ form.errors.error }}
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Referral ID -->
                <div>
                    <label for="referral_id" class="block text-sm font-medium text-gray-700">Referral</label>
                    <select
                        id="referral_id"
                        v-model="form.referral_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required
                    >
                        <option value="" disabled>Select a Referral</option>
                        <option v-for="referral in referrals" :key="referral.id" :value="referral.id">
                            {{ referral.referred_patient_id }} - {{ referral.referral_date }}
                        </option>
                    </select>
                    <div v-if="form.errors.referral_id" class="mt-2 text-sm text-red-600">{{ form.errors.referral_id }}</div>
                </div>

                <!-- Document Name -->
                <div>
                    <label for="document_name" class="block text-sm font-medium text-gray-700">Document Name</label>
                    <input
                        id="document_name"
                        v-model="form.document_name"
                        @input="onDocumentNameInput"
                        type="text"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required
                    />
                    <div v-if="form.errors.document_name" class="mt-2 text-sm text-red-600">{{ form.errors.document_name }}</div>
                </div>

                <!-- Document File -->
                <div>
                    <label for="document_file" class="block text-sm font-medium text-gray-700">Document File</label>
                    <input
                        id="document_file"
                        type="file"
                        @change="onFileChange"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        :required="!editMode"
                    />
                    <div v-if="form.errors.document_file" class="mt-2 text-sm text-red-600">{{ form.errors.document_file }}</div>
                </div>

                <!-- Document Type -->
                <div>
                    <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type</label>
                    <select
                        id="document_type"
                        v-model="form.document_type"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="">Select Document Type</option>
                        <option value="Clinical Summary">Clinical Summary</option>
                        <option value="Prescription">Prescription</option>
                        <option value="Lab Result">Lab Result</option>
                        <option value="Imaging Report">Imaging Report</option>
                    </select>
                    <div v-if="form.errors.document_type" class="mt-2 text-sm text-red-600">{{ form.errors.document_type }}</div>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required
                    >
                        <option value="Uploaded">Uploaded</option>
                        <option value="Sent">Sent</option>
                        <option value="Received">Received</option>
                    </select>
                    <div v-if="form.errors.status" class="mt-2 text-sm text-red-600">{{ form.errors.status }}</div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <button type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ editMode ? 'Update' : 'Create' }} Referral Document
                </button>
            </div>
        </div>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    referralDocument: {
        type: Object,
        default: null,
    },
    referrals: {
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
    referral_id: props.referralDocument ? props.referralDocument.referral_id : '',
    document_name: props.referralDocument ? props.referralDocument.document_name : '',
    document_file: null, // File input is handled separately
    document_type: props.referralDocument ? props.referralDocument.document_type : '',
    status: props.referralDocument ? props.referralDocument.status : 'Uploaded',
});

// Auto-fill control flags
const hasAutoFilledFromFile = ref(false);
const userHasEditedAfterAutoFill = ref(false);

// Track manual edits to document_name. If user edits after an auto-fill, lock their preference.
const onDocumentNameInput = () => {
    if (hasAutoFilledFromFile.value) {
        userHasEditedAfterAutoFill.value = true;
    }
};

const onFileChange = (e) => {
    const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
    form.document_file = file;
    if (!file) return;

    // Smart overwrite logic:
    // - First file selection should set name from file (even if user typed first)
    // - If user later edits the name manually, do NOT overwrite on subsequent file selections
    if (!hasAutoFilledFromFile.value || (hasAutoFilledFromFile.value && !userHasEditedAfterAutoFill.value)) {
        const name = file.name || '';
        form.document_name = name.replace(/\.[^.]+$/, '');
        hasAutoFilledFromFile.value = true;
        // Keep userHasEditedAfterAutoFill as-is. It flips to true only when user types.
    }
};

const submit = () => {
    if (props.editMode) {
        form.put(route('admin.referral-documents.update', props.referralDocument.id), {
            forceFormData: true, // Important for file uploads with PUT
            onSuccess: () => form.reset('document_file'),
        });
    } else {
        form.post(route('admin.referral-documents.store'), {
            forceFormData: true, // Important for file uploads
            onSuccess: () => form.reset(),
        });
    }
};
</script>
