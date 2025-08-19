export interface BreadcrumbItemType {
  title: string;
  href: string;
}

export interface InventoryItem {
  id: number;
  name: string;
  // Add other relevant fields for InventoryItem if needed
}

export interface InventoryMaintenanceRecord {
  id: number;
  item_id: number;
  item: InventoryItem; // Assuming item relationship is loaded
  scheduled_date: string;
  actual_date: string;
  performed_by_staff_id: number | null; // Changed to foreign key
  performed_by_staff: Staff; // Eager loaded relationship
  cost: number;
  description: string;
  next_due_date: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface InventoryMaintenanceRecordPagination {
  current_page: number;
  data: InventoryMaintenanceRecord[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}

export interface Staff {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  position: string;
  department: string;
  status: string;
  hire_date: string;
  hourly_rate: number;
  photo_url: string;
  created_at: string;
  updated_at: string;
}

// Insurance Policies
export interface InsurancePolicy {
  id: number;
  insurance_company_id: number;
  corporate_client_id: number;
  service_type: string;
  coverage_percentage: number | null;
  coverage_type: string | null;
  is_active: boolean;
  notes: string | null;
  created_at: string;
  updated_at: string;
  insurance_company?: { id: number; name: string };
  corporate_client?: { id: number; organization_name: string };
}

export interface InsurancePolicyPagination {
  current_page: number;
  data: InsurancePolicy[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}
