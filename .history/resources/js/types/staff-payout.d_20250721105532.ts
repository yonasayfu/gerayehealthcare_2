import { InertiaForm } from '@inertiajs/vue3';

export interface StaffPayoutData {
  [key: string]: any; // Index signature for InertiaForm compatibility
  staff_id: number;
  amount: number;
  payout_date: string;
  status: string;
  // Potentially add visit_service_ids if a payout can be linked to multiple visits manually
  // visit_service_ids?: number[];
}

export type StaffPayoutFormType = InertiaForm<StaffPayoutData>;

export interface StaffPayout {
  id: number;
  staff_id: number;
  staff: {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
  };
  amount: string; // Stored as string from DB (NUMERIC type)
  payout_date: string;
  status: string;
