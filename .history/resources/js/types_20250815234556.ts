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
  performed_by: string;
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
