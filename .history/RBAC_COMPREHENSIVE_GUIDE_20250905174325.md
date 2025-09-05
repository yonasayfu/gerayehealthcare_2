# 🔐 **RBAC (Role-Based Access Control) - Comprehensive Guide**

## 📋 **Table of Contents**
1. [System Overview](#system-overview)
2. [Key Files & Components](#key-files--components)
3. [How RBAC Works](#how-rbac-works)
4. [Creating Dynamic Roles](#creating-dynamic-roles)
5. [Permission Management](#permission-management)
6. [Navigation System](#navigation-system)
7. [Troubleshooting](#troubleshooting)
8. [Manual Fixes](#manual-fixes)

---

## 🎯 **System Overview**

The Geraye Healthcare RBAC system is built on **Laravel Spatie Permissions** with **dynamic Vue.js navigation** that adapts based on user permissions rather than hardcoded roles.

### **Core Principles:**
- ✅ **Permission-based** (not role-based) navigation
- ✅ **Dynamic role creation** via admin interface
- ✅ **Automatic navigation updates** when permissions change
- ✅ **Route-level security** with middleware protection
- ✅ **Frontend permission checking** with Inertia.js

---

## 📁 **Key Files & Components**

### **Backend (Laravel)**

#### **1. Controllers**
```
app/Http/Controllers/Admin/RoleController.php
```
- Handles role CRUD operations
- Manages permission assignment
- Provides data to Vue components

#### **2. Services**
```
app/Services/RoleService.php
```
- Business logic for role management
- Permission synchronization
- Cache management

#### **3. Middleware**
```
app/Http/Middleware/EnsureStaffLinked.php
```
- Bypasses staff requirements for executive roles
- Handles role-based access control

#### **4. Seeders**
```
database/seeders/RolesAndPermissionsSeeder.php
database/seeders/TestUsersSeeder.php
```
- Creates default roles and permissions
- Seeds test users with roles

#### **5. Routes**
```
routes/web.php (lines 759-764)
```
- Role management routes
- Permission-protected endpoints

### **Frontend (Vue.js)**

#### **1. Navigation Component**
```
resources/js/components/AppSidebar.vue
```
- **MOST IMPORTANT FILE** for RBAC navigation
- Dynamic permission-based menu generation
- Role detection and filtering

#### **2. Role Management Pages**
```
resources/js/pages/Admin/Roles/Index.vue    # Role listing
resources/js/pages/Admin/Roles/Create.vue   # Create new role
resources/js/pages/Admin/Roles/Edit.vue     # Edit existing role
resources/js/pages/Admin/Roles/Form.vue     # Reusable form component
```

#### **3. Middleware Integration**
```
app/Http/Middleware/HandleInertiaRequests.php
```
- Shares user roles and permissions with frontend
- Provides `can()` helper function

---

## ⚙️ **How RBAC Works**

### **1. Permission Flow**
```
User → Role → Permissions → Navigation → Routes → Controllers
```

### **2. Navigation Generation Process**
```javascript
// AppSidebar.vue - Dynamic filtering
const getFilteredNavItems = (): SidebarNavGroup[] => {
    const filteredGroups: SidebarNavGroup[] = [];
    
    for (const group of allAdminNavItems) {
        // Skip super admin only groups unless user is super admin
        if (group.superAdminOnly && !hasRole('super-admin')) {
            continue;
        }
        
        // Filter items based on permissions
        const filteredItems = group.items.filter(item => {
            if (item.permission) {
                return can(item.permission); // Dynamic permission check
            }
            return true;
        });
        
        if (filteredItems.length > 0) {
            filteredGroups.push({ ...group, items: filteredItems });
        }
    }
    
    return filteredGroups;
};
```

### **3. Permission Checking**
```javascript
// Frontend permission checking
const hasAnyPermission = (permissions: string[]): boolean => {
    if (!user.value) return false;
    if (userRoles.value?.some((role: string) => role === 'super-admin')) return true;
    return permissions.some(permission => user.value.permissions?.includes(permission));
}
```

---

## 🚀 **Creating Dynamic Roles**

### **Method 1: Admin Interface**
1. Login as Super Admin
2. Navigate to **Admin → Roles → Create New Role**
3. Enter role name (e.g., "Finance Manager")
4. Select permissions from checkbox list
5. Save role

### **Method 2: Programmatically**
```php
use Spatie\Permission\Models\Role;

// Create role
$financeRole = Role::create(['name' => 'finance']);

// Assign permissions
$financeRole->syncPermissions([
    'view invoices',
    'create invoices',
    'view financial reports',
    'export financial reports'
]);

// Assign to user
$user->assignRole('finance');
```

### **Method 3: Via Seeder**
```php
// In RolesAndPermissionsSeeder.php
$financeRole = Role::firstOrCreate(['name' => 'finance']);
$financeRole->givePermissionTo([
    'view invoices',
    'create invoices',
    'view financial reports'
]);
```

---

## 🔑 **Permission Management**

### **Available Permission Categories**

#### **Patient Management**
- `view patients`, `create patients`, `edit patients`, `delete patients`, `export patients`

#### **Staff Management**
- `view staff`, `create staff`, `edit staff`, `delete staff`, `export staff`
- `view staff schedules`, `manage staff schedules`, `approve leave requests`

#### **Financial Management**
- `view invoices`, `create invoices`, `edit invoices`, `export invoices`
- `view financial reports`, `export financial reports`, `manage budgets`
- `view staff payouts`, `create staff payouts`, `edit staff payouts`

#### **Medical & Clinical**
- `view medical records`, `create medical records`, `edit medical records`
- `view prescriptions`, `create prescriptions`, `edit prescriptions`
- `view appointments`, `create appointments`, `edit appointments`

#### **System Administration**
- `manage roles`, `manage users`, `manage permissions`
- `view system settings`, `edit system settings`

### **Adding New Permissions**
1. Add to `RolesAndPermissionsSeeder.php`
2. Run: `php artisan db:seed --class=RolesAndPermissionsSeeder`
3. Clear cache: `php artisan permission:cache-reset`

---

## 🧭 **Navigation System**

### **Navigation Groups Structure**
```javascript
// AppSidebar.vue
const allAdminNavItems: SidebarNavGroup[] = [
    {
        group: 'Patient Management',
        icon: UserPlus,
        items: [
            { title: 'Patients', routeName: 'admin.patients.index', icon: UserPlus, permission: 'view patients' },
            { title: 'Visit Services', routeName: 'admin.visit-services.index', icon: Stethoscope, permission: 'view visit services' },
        ]
    },
    {
        group: 'Financial Management',
        icon: DollarSign,
        items: [
            { title: 'Invoices', routeName: 'admin.invoices.index', icon: Receipt, permission: 'view invoices' },
            { title: 'Financial Reports', routeName: 'admin.reports.revenue-ar', icon: BarChart, permission: 'view financial reports' },
        ]
    }
];
```

### **Adding New Navigation Items**
1. Edit `AppSidebar.vue`
2. Add to appropriate group or create new group
3. Specify `permission` property for access control
4. Ensure route exists in `routes/web.php`

## 📝 **Best Practices**

### **1. Permission Naming Convention**
- Use lowercase with spaces: `view patients`, `create invoices`
- Be specific: `view financial reports` not just `view reports`
- Group logically: `view/create/edit/delete` pattern

### **2. Role Design**
- Create roles based on job functions, not departments
- Use descriptive names: `finance-manager` not `finance`
- Assign minimal required permissions (principle of least privilege)

### **3. Navigation Structure**
- Group related items together
- Use clear, descriptive titles
- Always specify `permission` property for access control
- Test with different roles before deploying

### **4. Security**
- Always use middleware on routes
- Double-check permission assignments
- Regularly audit user roles and permissions
- Test with non-admin users

---

This comprehensive guide provides everything needed to understand, troubleshoot, and manually fix the RBAC system in Geraye Healthcare.
