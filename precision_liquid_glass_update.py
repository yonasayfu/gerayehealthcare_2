#!/usr/bin/env python3
"""
Precision Liquid Glass UI Update Script
Applies EXACT Patient module patterns consistently across all Admin modules
"""

import os
import re
import glob
from pathlib import Path

# Define the base path for Admin modules
ADMIN_PAGES_PATH = r"C:\MyProject\gerayehealthcare_2\resources\js\pages\Admin"

# Modules to skip (already updated properly)
SKIP_MODULES = ["Patients", "CaregiverAssignments"]

def get_module_name_plural(module_dir_name):
    """Convert module directory name to proper plural form for UI"""
    mappings = {
        'Staff': 'Staff',
        'Services': 'Services', 
        'Partners': 'Partners',
        'Suppliers': 'Suppliers',
        'InventoryItems': 'Inventory Items',
        'InventoryRequests': 'Inventory Requests',
        'InventoryAlerts': 'Inventory Alerts',
        'InventoryMaintenanceRecords': 'Inventory Maintenance Records',
        'InventoryTransactions': 'Inventory Transactions',
        'MarketingCampaigns': 'Marketing Campaigns',
        'MarketingLeads': 'Marketing Leads',
        'MarketingBudgets': 'Marketing Budgets',
        'MarketingPlatforms': 'Marketing Platforms',
        'MarketingTasks': 'Marketing Tasks',
        'LandingPages': 'Landing Pages',
        'LeadSources': 'Lead Sources',
        'PartnerAgreements': 'Partner Agreements',
        'PartnerCommissions': 'Partner Commissions',
        'PartnerEngagements': 'Partner Engagements',
        'Referrals': 'Referrals',
        'ReferralDocuments': 'Referral Documents',
        'Events': 'Events',
        'EventBroadcasts': 'Event Broadcasts',
        'EventParticipants': 'Event Participants',
        'EventRecommendations': 'Event Recommendations',
        'EventStaffAssignments': 'Event Staff Assignments',
        'EligibilityCriteria': 'Eligibility Criteria',
        'Roles': 'Roles',
        'Users': 'Users',
        'TaskDelegations': 'Task Delegations',
        'StaffAvailabilities': 'Staff Availabilities',
        'StaffPayouts': 'Staff Payouts',
        'VisitServices': 'Visit Services',
        'CampaignContents': 'Campaign Contents',
        'SharedInvoices': 'Shared Invoices',
        'Invoices': 'Invoices',
        'Messages': 'Messages',
        'LeaveRequests': 'Leave Requests',
    }
    return mappings.get(module_dir_name, module_dir_name)

def get_module_name_singular(module_dir_name):
    """Convert module directory name to proper singular form for UI"""
    mappings = {
        'Staff': 'Staff Member',
        'Services': 'Service', 
        'Partners': 'Partner',
        'Suppliers': 'Supplier',
        'InventoryItems': 'Inventory Item',
        'InventoryRequests': 'Inventory Request',
        'InventoryAlerts': 'Inventory Alert',
        'InventoryMaintenanceRecords': 'Inventory Maintenance Record',
        'InventoryTransactions': 'Inventory Transaction',
        'MarketingCampaigns': 'Marketing Campaign',
        'MarketingLeads': 'Marketing Lead',
        'MarketingBudgets': 'Marketing Budget',
        'MarketingPlatforms': 'Marketing Platform',
        'MarketingTasks': 'Marketing Task',
        'LandingPages': 'Landing Page',
        'LeadSources': 'Lead Source',
        'PartnerAgreements': 'Partner Agreement',
        'PartnerCommissions': 'Partner Commission',
        'PartnerEngagements': 'Partner Engagement',
        'Referrals': 'Referral',
        'ReferralDocuments': 'Referral Document',
        'Events': 'Event',
        'EventBroadcasts': 'Event Broadcast',
        'EventParticipants': 'Event Participant',
        'EventRecommendations': 'Event Recommendation',
        'EventStaffAssignments': 'Event Staff Assignment',
        'EligibilityCriteria': 'Eligibility Criteria',
        'Roles': 'Role',
        'Users': 'User',
        'TaskDelegations': 'Task Delegation',
        'StaffAvailabilities': 'Staff Availability',
        'StaffPayouts': 'Staff Payout',
        'VisitServices': 'Visit Service',
        'CampaignContents': 'Campaign Content',
        'SharedInvoices': 'Shared Invoice',
        'Invoices': 'Invoice',
        'Messages': 'Message',
        'LeaveRequests': 'Leave Request',
    }
    return mappings.get(module_dir_name, module_dir_name.rstrip('s'))

def update_index_files():
    """Update Index.vue files with EXACT Patient pattern"""
    print("🔄 Updating Index.vue files with exact Patient pattern...")
    
    updated_files = []
    
    for module_dir in sorted(glob.glob(os.path.join(ADMIN_PAGES_PATH, "*"))):
        module_name = os.path.basename(module_dir)
        if module_name in SKIP_MODULES or not os.path.isdir(module_dir):
            continue
            
        index_file = os.path.join(module_dir, "Index.vue")
        if not os.path.exists(index_file):
            continue
            
        try:
            with open(index_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            module_plural = get_module_name_plural(module_name)
            module_singular = get_module_name_singular(module_name)
            
            # 1. Replace header section with exact Patient pattern
            header_pattern = r'<div class="[^"]*(?:rounded-lg|bg-muted|p-4|shadow)[^"]*"[^>]*>.*?<h1[^>]*>([^<]+)</h1>.*?<p[^>]*>([^<]+)</p>.*?<div class="flex[^>]*>(.*?)</div>.*?</div>'
            
            new_header = f'''      <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{module_plural}</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Manage {module_plural.lower()}</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">'''
            
            # Extract and convert buttons
            button_matches = re.findall(r'<(?:Link|button)[^>]*class="btn[^"]*"[^>]*>([^<]*(?:<[^>]*>[^<]*</[^>]*>)*[^<]*)</(?:Link|button)>', content, re.DOTALL)
            
            # Add standardized buttons based on common patterns
            new_header += f'''
            <Link :href="route('admin.{module_name.lower()}.create')" class="btn-glass">
              <span>Add {module_singular}</span>
            </Link>'''
            
            # Look for export functionality
            if 'export' in content.lower() or 'csv' in content.lower():
                new_header += '''
            <button @click="exportData('csv')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </button>'''
            
            # Look for print functionality  
            if 'print' in content.lower():
                new_header += '''
            <button @click="printCurrentView" class="btn-glass btn-glass-sm">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print Current</span>
            </button>'''
            
            new_header += '''
          </div>
        </div>
      </div>'''
            
            content = re.sub(header_pattern, new_header, content, flags=re.DOTALL)
            
            # 2. Replace search section with exact Patient pattern
            search_pattern = r'<div class="flex flex-col md:flex-row[^>]*>.*?<div class="relative[^>]*>.*?<input[^>]*v-model="search"[^>]*>.*?<Search[^>]*>.*?</div>'
            
            new_search = f'''      <!-- Search / per page -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 print:hidden">
        <!-- keep original input size & rounded-lg but wrap with a subtle liquid-glass outer effect -->
        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search {module_plural.lower()}..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>'''
            
            content = re.sub(search_pattern, new_search, content, flags=re.DOTALL)
            
            # 3. Update per-page select
            perpage_pattern = r'<select[^>]*v-model="perPage"[^>]*class="[^"]*"[^>]*>'
            new_perpage = 'v-model="perPage" class="rounded-md border-gray-300 bg-white text-gray-900 sm:text-sm px-2 py-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700">'
            content = re.sub(r'v-model="perPage"[^>]*class="[^"]*"', new_perpage, content)
            
            # 4. Update table actions to use Patient pattern
            action_pattern = r'<div class="inline-flex[^>]*>.*?<Link[^>]*route\([^)]*\.show[^>]*class="[^"]*"[^>]*>.*?</Link>.*?<Link[^>]*route\([^)]*\.edit[^>]*class="[^"]*"[^>]*>.*?</Link>.*?<button[^>]*@click="destroy[^>]*class="[^"]*"[^>]*>.*?</button>.*?</div>'
            
            new_actions = f'''<div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.{module_name.lower()}.show', item.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <Link
                    :href="route('admin.{module_name.lower()}.edit', item.id)"
                    class="inline-flex items-center p-2 rounded-md text-blue-600 hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-gray-700"
                    title="Edit"
                  >
                    <Edit3 class="w-4 h-4" />
                  </Link>
                  <button
                    @click="destroy(item.id)"
                    class="inline-flex items-center p-2 rounded-md text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-gray-700"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>'''
            
            content = re.sub(action_pattern, new_actions, content, flags=re.DOTALL)
            
            if content != original_content:
                with open(index_file, 'w', encoding='utf-8') as f:
                    f.write(content)
                updated_files.append(f"{module_name}/Index.vue")
                print(f"  ✅ Updated: {module_name}/Index.vue")
                
        except Exception as e:
            print(f"  ❌ Error updating {module_name}/Index.vue: {e}")
    
    return updated_files

def update_create_files():
    """Update Create.vue files with EXACT Patient pattern"""
    print("🔄 Updating Create.vue files with exact Patient pattern...")
    
    updated_files = []
    
    for module_dir in sorted(glob.glob(os.path.join(ADMIN_PAGES_PATH, "*"))):
        module_name = os.path.basename(module_dir)
        if module_name in SKIP_MODULES or not os.path.isdir(module_dir):
            continue
            
        create_file = os.path.join(module_dir, "Create.vue")
        if not os.path.exists(create_file):
            continue
            
        try:
            with open(create_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            module_singular = get_module_name_singular(module_name)
            
            # Replace entire template structure with Patient pattern
            template_pattern = r'<template>.*?</template>'
            
            new_template = f'''<template>
  <Head title="Create New {module_singular}" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header (no Back button here) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create New {module_singular}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information to create a {module_singular.lower()}.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />
          <!-- Footer actions: Cancel + Create (right aligned, no logic change) -->
          <div class="flex justify-end gap-2 pt-2">
            <Link :href="route('admin.{module_name.lower()}.index')" class="btn-glass btn-glass-sm">Cancel</Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{{{ form.processing ? 'Creating...' : 'Create {module_singular}' }}}}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>'''
            
            content = re.sub(template_pattern, new_template, content, flags=re.DOTALL)
            
            if content != original_content:
                with open(create_file, 'w', encoding='utf-8') as f:
                    f.write(content)
                updated_files.append(f"{module_name}/Create.vue")
                print(f"  ✅ Updated: {module_name}/Create.vue")
                
        except Exception as e:
            print(f"  ❌ Error updating {module_name}/Create.vue: {e}")
    
    return updated_files

def update_edit_files():
    """Update Edit.vue files with EXACT Patient pattern"""
    print("🔄 Updating Edit.vue files with exact Patient pattern...")
    
    updated_files = []
    
    for module_dir in sorted(glob.glob(os.path.join(ADMIN_PAGES_PATH, "*"))):
        module_name = os.path.basename(module_dir)
        if module_name in SKIP_MODULES or not os.path.isdir(module_dir):
            continue
            
        edit_file = os.path.join(module_dir, "Edit.vue")
        if not os.path.exists(edit_file):
            continue
            
        try:
            with open(edit_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            module_singular = get_module_name_singular(module_name)
            
            # Replace template structure with Patient pattern  
            template_pattern = r'<template>.*?</template>'
            
            new_template = f'''<template>
  <Head title="Edit {module_singular}" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid-glass header: Back button removed -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div>
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit {module_singular}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update {module_singular.lower()} information below.</p>
          </div>
          <!-- intentionally no Back button here -->
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <Form :form="form" v-bind="$props" />

          <!-- Footer actions: Cancel + Save (right aligned), no logic changes -->
          <div class="flex justify-end gap-2 pt-2">
            <Link
              :href="route('admin.{module_name.lower()}.index')"
              class="btn-glass btn-glass-sm"
            >
              Cancel
            </Link>

            <button
              type="submit"
              :disabled="form.processing"
              class="btn-glass btn-glass-sm"
            >
              {{{{ form.processing ? 'Saving...' : 'Save Changes' }}}}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>'''
            
            content = re.sub(template_pattern, new_template, content, flags=re.DOTALL)
            
            if content != original_content:
                with open(edit_file, 'w', encoding='utf-8') as f:
                    f.write(content)
                updated_files.append(f"{module_name}/Edit.vue")
                print(f"  ✅ Updated: {module_name}/Edit.vue")
                
        except Exception as e:
            print(f"  ❌ Error updating {module_name}/Edit.vue: {e}")
    
    return updated_files

def update_show_files():
    """Update Show.vue files with EXACT Patient pattern"""
    print("🔄 Updating Show.vue files with exact Patient pattern...")
    
    updated_files = []
    
    for module_dir in sorted(glob.glob(os.path.join(ADMIN_PAGES_PATH, "*"))):
        module_name = os.path.basename(module_dir)
        if module_name in SKIP_MODULES or not os.path.isdir(module_dir):
            continue
            
        show_file = os.path.join(module_dir, "Show.vue")
        if not os.path.exists(show_file):
            continue
            
        try:
            with open(show_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            module_singular = get_module_name_singular(module_name)
            
            # Update header section to match Patient pattern exactly
            header_pattern = r'<div class="[^"]*(?:bg-white|border|rounded|shadow)[^"]*"[^>]*>.*?<div class="flex[^>]*>.*?<h3[^>]*>([^<]+)</h3>.*?</div>'
            
            new_header = f'''    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow relative m-10">

      <!-- compact liquid glass header (now full-width and same sizing as main card) -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{module_singular} Details</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300">{module_singular}: {{{{ item.name || item.title || item.id }}}}</p>
          </div>
          <!-- top actions intentionally removed to avoid duplication; see footer -->
        </div>
      </div>'''
            
            content = re.sub(header_pattern, new_header, content, flags=re.DOTALL)
            
            # Update footer actions to match Patient pattern
            footer_pattern = r'<div class="p-6 border-t[^>]*>.*?<div class="flex[^>]*>.*?</div>.*?</div>'
            
            new_footer = f'''      <!-- footer actions (single source of actions, right aligned) -->
      <div class="p-6 border-t border-gray-200 dark:border-gray-700 rounded-b print:hidden">
        <div class="flex justify-end gap-2">
          <Link :href="route('admin.{module_name.lower()}.index')" class="btn-glass btn-glass-sm">Back to List</Link>
          <button @click="printSingle{module_singular.replace(' ', '')}" class="btn-glass btn-glass-sm">Print Current</button>
          <Link :href="route('admin.{module_name.lower()}.edit', item.id)" class="btn-glass btn-glass-sm">Edit</Link>
        </div>
      </div>'''
            
            content = re.sub(footer_pattern, new_footer, content, flags=re.DOTALL)
            
            if content != original_content:
                with open(show_file, 'w', encoding='utf-8') as f:
                    f.write(content)
                updated_files.append(f"{module_name}/Show.vue")
                print(f"  ✅ Updated: {module_name}/Show.vue")
                
        except Exception as e:
            print(f"  ❌ Error updating {module_name}/Show.vue: {e}")
    
    return updated_files

def update_form_files():
    """Update Form.vue files with EXACT Patient pattern (dark-friendly selects)"""
    print("🔄 Updating Form.vue files with exact Patient pattern...")
    
    updated_files = []
    
    for module_dir in sorted(glob.glob(os.path.join(ADMIN_PAGES_PATH, "*"))):
        module_name = os.path.basename(module_dir)
        if module_name in SKIP_MODULES or not os.path.isdir(module_dir):
            continue
            
        form_file = os.path.join(module_dir, "Form.vue")
        if not os.path.exists(form_file):
            continue
            
        try:
            with open(form_file, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_content = content
            
            # Update all select elements to use Patient pattern
            content = re.sub(
                r'class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2\.5"',
                'class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"',
                content
            )
            
            # Also handle variations with different ordering
            content = re.sub(
                r'class="([^"]*\s+)?bg-gray-50([^"]*\s+)?border([^"]*\s+)?border-gray-300([^"]*)"',
                lambda m: m.group(0).replace('bg-gray-50', 'bg-white dark:bg-gray-800').replace('text-gray-900', 'text-gray-900 dark:text-white') if 'dark:' not in m.group(0) else m.group(0),
                content
            )
            
            if content != original_content:
                with open(form_file, 'w', encoding='utf-8') as f:
                    f.write(content)
                updated_files.append(f"{module_name}/Form.vue")
                print(f"  ✅ Updated: {module_name}/Form.vue")
                
        except Exception as e:
            print(f"  ❌ Error updating {module_name}/Form.vue: {e}")
    
    return updated_files

def main():
    """Main function to run all updates with exact Patient patterns"""
    print("🚀 Starting Precision Liquid Glass UI Update...")
    print("📋 Applying EXACT Patient module patterns consistently")
    print(f"📁 Target directory: {ADMIN_PAGES_PATH}")
    print(f"⏭️  Skipping modules: {', '.join(SKIP_MODULES)}")
    print("-" * 60)
    
    all_updated_files = []
    
    # Run all update functions in order
    all_updated_files.extend(update_index_files())
    print()
    all_updated_files.extend(update_create_files())
    print()
    all_updated_files.extend(update_edit_files())
    print()
    all_updated_files.extend(update_show_files())
    print()
    all_updated_files.extend(update_form_files())
    
    print("-" * 60)
    print("✅ Precision batch update completed!")
    print(f"📊 Total files updated: {len(all_updated_files)}")
    print()
    print("📋 Updated files:")
    for file in sorted(all_updated_files):
        print(f"  • {file}")
    print()
    print("🔍 What to test:")
    print("1. Liquid glass headers with hover effects")
    print("2. Search glass wrapper with hover glow")
    print("3. Consistent btn-glass styling across all actions")
    print("4. Dark-friendly form selects")
    print("5. Print behavior (hide interactive, show print footer)")
    print("6. Consistent spacing, typography, and button positioning")

if __name__ == "__main__":
    main()
