import { InertiaForm } from '@inertiajs/vue3';

export interface InvoiceFormData {
  patient_id: number | null;
  visit_ids: number[];
  invoice_date: string;
  due_date: string;
}

export type InvoiceFormType = InertiaForm<InvoiceFormData>;
