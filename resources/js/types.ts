export interface BreadcrumbItemType {
  title: string;
  href: string;
}

// Employee Insurance Records
export interface EmployeeInsuranceRecord {
  id: number;
  patient_id: number;
  policy_id: number;
  kebele_id: string | null;
  woreda: string | null;
  region: string | null;
  federal_id: string | null; // Stored field name remains federal_id
  ministry_department: string | null;
  employee_id_number: string | null;
  verified: boolean;
  verified_at?: string | null;
  created_at: string;
  updated_at: string;
  patient?: { id: number; full_name: string };
  insurance_policy?: { id: number; service_type: string; coverage_percentage: number | null };
  policy?: { id: number; service_type: string; coverage_percentage: number | null };
}

export interface EmployeeInsuranceRecordPagination {
  current_page: number;
  data: EmployeeInsuranceRecord[];
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

// Chat-related types
export interface User {
  id: number;
  name: string;
  email: string;
  profile_photo_url?: string;
  roles?: string[];
  permissions?: string[];
  staff?: Staff;
}

export interface Message {
  id: number;
  sender_id: number;
  receiver_id: number;
  message: string | null;
  attachment_path?: string | null;
  attachment_filename?: string | null;
  attachment_mime_type?: string | null;
  attachment?: {
    filename: string;
    url: string;
    mime_type: string;
  } | null;
  reply_to_id?: number | null;
  created_at: string;
  read_at?: string | null;
  isOptimistic?: boolean;
  sender?: User;
  receiver?: User;
  reactions?: any[]; // Define more specifically if needed
}

export interface Conversation {
  id: number;
  name: string;
  email: string;
  profile_photo_url?: string;
  staff?: Staff;
  unread: number;
}

export interface Group {
  id: number;
  name: string;
  members_count: number;
  // Add other relevant fields for Group if needed
}

export interface AppPageProps {
  auth: {
    user: User;
  };
  flash: {
    message?: string;
    banner?: string;
    bannerStyle?: 'success' | 'danger' | 'warning';
  };
  // Add other global props if necessary
}
