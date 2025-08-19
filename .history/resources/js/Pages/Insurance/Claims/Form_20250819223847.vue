<script setup lang="ts">
import type { InertiaForm, InsuranceClaim } from '@/types';
import { computed, watch, ref, onMounted } from 'vue';
import { format } from 'date-fns';

interface LocalErrors {
  // Add other potential local errors if they exist
}

type PatientOption = { id: number; full_name: string; email?: string };
type InvoiceOption = { id: number; invoice_number: string; grand_total: number; patient?: PatientOption };
type InsuranceCompanyOption = { id: number; name: string };
type InsurancePolicyOption = { id: number; service_type: string; coverage_percentage?: number };

const props = defineProps<{
  form: InertiaForm<InsuranceClaim>,
  localErrors?: LocalErrors,
  patients: PatientOption[],
  invoices: InvoiceOption[],
  insuranceCompanies: InsuranceCompanyOption[],
  insurancePolicies: InsurancePolicyOption[],
}>();

const emit = defineEmits(['submit']);

const claimStatuses = ['Submitted', 'Processing', 'Approved', 'Denied', 'Paid'];
const paymentMethods = ['Bank Transfer', 'Cash', 'Credit Card', 'Mobile Money'];

const normalizeDate = (value: string | null | undefined) => {
  if (!value) return null;
  const s = String(value);
  return s.length >= 10 ? s.substring(0, 10) : s;
};

// Watch for patient_id change to pre-fill recipient_email
watch(() => props.form.patient_id, (newPatientId) => {
  const selectedPatient = props.patients.find(p => p.id === newPatientId);
  if (selectedPatient && selectedPatient.email) {
    props.form.recipient_email = selectedPatient.email;
  } else {
    props.form.recipient_email = '';
  }
});

// Watch for invoice_id change to pre-fill patient_id
watch(() => props.form.invoice_id, (newInvoiceId) => {
  const selectedInvoice = props.invoices.find(i => i.id === newInvoiceId);
  if (selectedInvoice && selectedInvoice.patient) {
        </div>

        <div class="sm:col-span-3">
            <label for="insurance_company_id" class="block text-sm font-medium text-gray-700">Insurance Company</label>
            <select id="insurance_company_id" v-model="form.insurance_company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Select an Insurance Company (Optional)</option>
                <option v-for="company in insuranceCompanies" :key="company.id" :value="company.id">{{ company.name }}</option>
            </select>
            <div v-if="form.errors.insurance_company_id" class="text-red-500 text-sm mt-1">{{ form.errors.insurance_company_id }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="policy_id" class="block text-sm font-medium text-gray-700">Insurance Policy</label>
            <select id="policy_id" v-model="form.policy_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Select an Insurance Policy (Optional)</option>
                <option v-for="policy in insurancePolicies" :key="policy.id" :value="policy.id">{{ policy.service_type }} ({{ policy.coverage_percentage }}%)</option>
            </select>
            <div v-if="form.errors.policy_id" class="text-red-500 text-sm mt-1">{{ form.errors.policy_id }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="claim_status" class="block text-sm font-medium text-gray-700">Claim Status</label>
            <select id="claim_status" v-model="form.claim_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                <option value="">Select Status</option>
                <option v-for="status in claimStatuses" :key="status" :value="status">{{ status }}</option>
            </select>
            <div v-if="form.errors.claim_status" class="text-red-500 text-sm mt-1">{{ form.errors.claim_status }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="coverage_amount" class="block text-sm font-medium text-gray-700">Coverage Amount</label>
            <input type="number" id="coverage_amount" v-model="form.coverage_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" step="0.01">
            <div v-if="form.errors.coverage_amount" class="text-red-500 text-sm mt-1">{{ form.errors.coverage_amount }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="paid_amount" class="block text-sm font-medium text-gray-700">Paid Amount</label>
            <input type="number" id="paid_amount" v-model="form.paid_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" step="0.01">
            <div v-if="form.errors.paid_amount" class="text-red-500 text-sm mt-1">{{ form.errors.paid_amount }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="submitted_at" class="block text-sm font-medium text-gray-700">Submitted At</label>
            <input type="date" id="submitted_at" v-model="form.submitted_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.submitted_at" class="text-red-500 text-sm mt-1">{{ form.errors.submitted_at }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="processed_at" class="block text-sm font-medium text-gray-700">Processed At</label>
            <input type="date" id="processed_at" v-model="form.processed_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.processed_at" class="text-red-500 text-sm mt-1">{{ form.errors.processed_at }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="payment_due_date" class="block text-sm font-medium text-gray-700">Payment Due Date</label>
            <input type="date" id="payment_due_date" v-model="form.payment_due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.payment_due_date" class="text-red-500 text-sm mt-1">{{ form.errors.payment_due_date }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="payment_received_at" class="block text-sm font-medium text-gray-700">Payment Received At</label>
            <input type="date" id="payment_received_at" v-model="form.payment_received_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.payment_received_at" class="text-red-500 text-sm mt-1">{{ form.errors.payment_received_at }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
            <select id="payment_method" v-model="form.payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Select Payment Method</option>
                <option v-for="method in paymentMethods" :key="method" :value="method">{{ method }}</option>
            </select>
            <div v-if="form.errors.payment_method" class="text-red-500 text-sm mt-1">{{ form.errors.payment_method }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="reimbursement_required" class="flex items-center">
                <input type="checkbox" id="reimbursement_required" v-model="form.reimbursement_required" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Reimbursement Required</span>
            </label>
            <div v-if="form.errors.reimbursement_required" class="text-red-500 text-sm mt-1">{{ form.errors.reimbursement_required }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="receipt_number" class="block text-sm font-medium text-gray-700">Receipt Number</label>
            <input type="text" id="receipt_number" v-model="form.receipt_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.receipt_number" class="text-red-500 text-sm mt-1">{{ form.errors.receipt_number }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="is_pre_authorized" class="flex items-center">
                <input type="checkbox" id="is_pre_authorized" v-model="form.is_pre_authorized" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-600">Is Pre-Authorized</span>
            </label>
            <div v-if="form.errors.is_pre_authorized" class="text-red-500 text-sm mt-1">{{ form.errors.is_pre_authorized }}</div>
        </div>

        <div class="sm:col-span-3">
            <label for="pre_authorization_code" class="block text-sm font-medium text-gray-700">Pre-Authorization Code</label>
            <input type="text" id="pre_authorization_code" v-model="form.pre_authorization_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <div v-if="form.errors.pre_authorization_code" class="text-red-500 text-sm mt-1">{{ form.errors.pre_authorization_code }}</div>
        </div>

        <div class="col-span-full">
            <label for="denial_reason" class="block text-sm font-medium text-gray-700">Denial Reason</label>
            <textarea id="denial_reason" v-model="form.denial_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            <div v-if="form.errors.denial_reason" class="text-red-500 text-sm mt-1">{{ form.errors.denial_reason }}</div>
        </div>

        <div class="col-span-full">
            <label for="translated_notes" class="block text-sm font-medium text-gray-700">Translated Notes</label>
            <textarea id="translated_notes" v-model="form.translated_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            <div v-if="form.errors.translated_notes" class="text-red-500 text-sm mt-1">{{ form.errors.translated_notes }}</div>
        </div>
    </div>
</template>
