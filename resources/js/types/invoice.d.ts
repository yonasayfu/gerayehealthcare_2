import { InertiaForm } from '@inertiajs/vue3';

export interface InvoiceFormData {
  [key: string]: any; // Add index signature for InertiaForm compatibility
  patient_id: number | null;
  visit_ids: number[];
  invoice_date: string;
  due_date: string;
}

export type InvoiceFormType = InertiaForm<InvoiceFormData>;
