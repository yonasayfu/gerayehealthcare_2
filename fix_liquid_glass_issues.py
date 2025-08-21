#!/usr/bin/env python3
"""
Fix specific issues found in the liquid glass batch update
"""

import os
import re
import glob
from pathlib import Path

ADMIN_PAGES_PATH = r"C:\MyProject\gerayehealthcare_2\resources\js\pages\Admin"

def fix_staff_index():
    """Fix Staff Index.vue - duplicate headers and wrong variable names"""
    print("🔧 Fixing Staff Index.vue...")
    
    file_path = os.path.join(ADMIN_PAGES_PATH, "Staff", "Index.vue")
    if not os.path.exists(file_path):
        return
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Fix duplicate headers
    content = re.sub(
        r'      <!-- Liquid glass header -->\s*<div class="liquidGlass-wrapper print:hidden">\s*<div class="liquidGlass-inner-shine" aria-hidden="true"></div>\s*<!-- Liquid glass header with search card \(no logic changed\) -->',
        '      <!-- Liquid glass header with search card (no logic changed) -->',
        content,
        flags=re.DOTALL
    )
    
    # Fix extra closing div
    content = re.sub(r'      </div>\s*</div>\s*\n\s*<!-- Search', '      </div>\n\n      <!-- Search', content)
    
    # Fix variable names in table actions (item.id -> member.id)
    content = content.replace('route(\'admin.staff.show\', item.id)', 'route(\'admin.staff.show\', member.id)')
    content = content.replace('route(\'admin.staff.edit\', item.id)', 'route(\'admin.staff.edit\', member.id)')
    content = content.replace('@click="destroy(item.id)"', '@click="destroy(member.id)"')
    
    # Fix extra > in select
    content = content.replace('dark:border-gray-700">>', 'dark:border-gray-700">')
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print("  ✅ Fixed Staff/Index.vue")

def fix_staff_payouts_index():
    """Fix StaffPayouts Index.vue - template syntax error"""
    print("🔧 Fixing StaffPayouts Index.vue...")
    
    file_path = os.path.join(ADMIN_PAGES_PATH, "StaffPayouts", "Index.vue")
    if not os.path.exists(file_path):
        return
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Fix extra closing div
    content = re.sub(r'      </div>\s*</div>\s*\n\s*<div v-if="alertMessage"', '      </div>\n\n      <div v-if="alertMessage"', content)
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print("  ✅ Fixed StaffPayouts/Index.vue")

def fix_invoices_index():
    """Fix Invoices Index.vue - completely replace with proper liquid glass template"""
    print("🔧 Fixing Invoices Index.vue...")
    
    file_path = os.path.join(ADMIN_PAGES_PATH, "Invoices", "Index.vue")
    if not os.path.exists(file_path):
        return
    
    new_template = '''<template>
  <Head title="Invoices" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">

      <!-- Liquid glass header with search card (no logic changed) -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>

        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="flex items-center gap-4">
            <div class="print:hidden">
              <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Invoices</h1>
              <p class="text-sm text-gray-600 dark:text-gray-300">Review and manage all patient invoices</p>
            </div>

            <!-- (removed header search) -->
          </div>

          <div class="flex items-center gap-2 print:hidden">
            <Link :href="route('admin.invoices.create')" class="btn-glass">
              <span>Create Invoice</span>
            </Link>
            <Link :href="route('admin.invoices.incoming')" class="btn-glass btn-glass-sm">
              <span>Incoming</span>
            </Link>
            <a :href="route('admin.invoices.export')" class="btn-glass btn-glass-sm">
              <Download class="icon" />
              <span class="hidden sm:inline">Export CSV</span>
            </a>
            <a :href="route('admin.invoices.printAll')" class="btn-glass btn-glass-sm" target="_blank">
              <Printer class="icon" />
              <span class="hidden sm:inline">Print All</span>
            </a>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto bg-white dark:bg-gray-900 shadow rounded-lg print:shadow-none print:rounded-none print:bg-transparent">
        <div class="hidden print:block text-center mb-4 print:mb-2 print-header-content">
            <img src="/images/geraye_logo.jpeg" alt="Geraye Logo" class="print-logo">
            <h1 class="font-bold text-gray-800 dark:text-white print-clinic-name">Geraye Home Care Services</h1>
            <p class="text-gray-600 dark:text-gray-400 print-document-title">Invoice List (Current View)</p>
            <hr class="my-3 border-gray-300 print:my-2">
        </div>

        <table class="w-full text-left text-sm text-gray-800 dark:text-gray-100 print-table">
          <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase text-gray-600 dark:text-gray-300 print-table-header">
            <tr>
              <th class="px-6 py-3">Invoice #</th>
              <th class="px-6 py-3">Patient</th>
              <th class="px-6 py-3">Invoice Date</th>
              <th class="px-6 py-3">Due Date</th>
              <th class="px-6 py-3">Amount</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3 text-right print:hidden">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 print-table-row">
              <td class="px-6 py-4 text-gray-700 dark:text-gray-200">
                <Link :href="route('admin.invoices.show', invoice.id)" class="text-indigo-600 hover:underline font-semibold">{{ invoice.invoice_number }}</Link>
              </td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ invoice.patient.full_name }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ formatDate(invoice.invoice_date) }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ formatDate(invoice.due_date) }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">{{ formatCurrency(invoice.grand_total) }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-100">
                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                  'bg-yellow-100 text-yellow-800': invoice.status === 'Pending',
                  'bg-blue-100 text-blue-800': invoice.status === 'Issued',
                  'bg-green-100 text-green-800': invoice.status === 'Paid',
                  'bg-purple-100 text-purple-800': invoice.status === 'Approved',
                  'bg-red-100 text-red-800': invoice.status === 'Overdue',
                }">
                  {{ invoice.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-right print:hidden">
                <div class="inline-flex items-center justify-end space-x-2">
                  <Link
                    :href="route('admin.invoices.show', invoice.id)"
                    class="inline-flex items-center p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                    title="View Details"
                  >
                    <Eye class="w-4 h-4" />
                  </Link>
                  <button
                    v-if="invoice.status === 'Issued' || invoice.status === 'Pending'"
                    @click.prevent="approveInvoice(invoice.id)"
                    class="inline-flex items-center p-2 rounded-md text-green-600 hover:bg-green-50 dark:text-green-300 dark:hover:bg-gray-700"
                    title="Approve Invoice"
                  >
                    <Check class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="invoices.data.length === 0">
              <td colspan="7" class="text-center px-6 py-4 text-gray-400 dark:text-gray-400">No invoices found.</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </AppLayout>
</template>'''

    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Replace template section
    content = re.sub(r'<template>.*?</template>', new_template, content, flags=re.DOTALL)
    
    # Add missing imports
    if 'Eye' not in content:
        content = re.sub(
            r'import { Plus, Printer, Download, Check } from \'lucide-vue-next\';',
            'import { Plus, Printer, Download, Check, Eye } from \'lucide-vue-next\';',
            content
        )
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print("  ✅ Fixed Invoices/Index.vue")

def fix_route_names():
    """Fix incorrect route names in all files"""
    print("🔧 Fixing route names...")
    
    route_mappings = {
        'admin.visitservices.': 'admin.visit-services.',
        'admin.inventoryitems.': 'admin.inventory-items.',
        'admin.inventoryrequests.': 'admin.inventory-requests.',
        'admin.inventoryalerts.': 'admin.inventory-alerts.',
        'admin.inventorymaintenancerecords.': 'admin.inventory-maintenance-records.',
        'admin.inventorytransactions.': 'admin.inventory-transactions.',
        'admin.marketingcampaigns.': 'admin.marketing-campaigns.',
        'admin.marketingleads.': 'admin.marketing-leads.',
        'admin.marketingbudgets.': 'admin.marketing-budgets.',
        'admin.marketingplatforms.': 'admin.marketing-platforms.',
        'admin.marketingtasks.': 'admin.marketing-tasks.',
        'admin.landingpages.': 'admin.landing-pages.',
        'admin.leadsources.': 'admin.lead-sources.',
        'admin.partneragreements.': 'admin.partner-agreements.',
        'admin.partnercommissions.': 'admin.partner-commissions.',
        'admin.partnerengagements.': 'admin.partner-engagements.',
        'admin.referraldocuments.': 'admin.referral-documents.',
        'admin.eventbroadcasts.': 'admin.event-broadcasts.',
        'admin.eventparticipants.': 'admin.event-participants.',
        'admin.eventrecommendations.': 'admin.event-recommendations.',
        'admin.eventstaffassignments.': 'admin.event-staff-assignments.',
        'admin.eligibilitycriteria.': 'admin.eligibility-criteria.',
        'admin.taskdelegations.': 'admin.task-delegations.',
        'admin.staffavailabilities.': 'admin.staff-availabilities.',
        'admin.staffpayouts.': 'admin.staff-payouts.',
        'admin.campaigncontents.': 'admin.campaign-contents.',
        'admin.sharedinvoices.': 'admin.shared-invoices.',
        'admin.leaverequests.': 'admin.leave-requests.',
    }
    
    updated_files = []
    
    for module_dir in glob.glob(os.path.join(ADMIN_PAGES_PATH, "*")):
        module_name = os.path.basename(module_dir)
        
        for vue_file in glob.glob(os.path.join(module_dir, "*.vue")):
            try:
                with open(vue_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                original_content = content
                
                # Apply route name fixes
                for old_route, new_route in route_mappings.items():
                    if old_route in content:
                        content = content.replace(old_route, new_route)
                
                if content != original_content:
                    with open(vue_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    
                    file_name = os.path.basename(vue_file)
                    updated_files.append(f"{module_name}/{file_name}")
                    
            except Exception as e:
                print(f"  ❌ Error fixing routes in {vue_file}: {e}")
    
    print(f"  ✅ Fixed route names in {len(updated_files)} files")
    return updated_files

def fix_variable_names():
    """Fix incorrect variable names in table actions"""
    print("🔧 Fixing variable names in table actions...")
    
    # Common variable name mappings by module
    variable_mappings = {
        'Staff': 'member',
        'Services': 'service',
        'Partners': 'partner',
        'Suppliers': 'supplier',
        'Events': 'event',
        'Roles': 'role',
        'Users': 'user',
        'InventoryItems': 'item',
        'InventoryRequests': 'request',
        'InventoryAlerts': 'alert',
        'InventoryMaintenanceRecords': 'record',
        'InventoryTransactions': 'transaction',
        'MarketingCampaigns': 'campaign',
        'MarketingLeads': 'lead',
        'MarketingBudgets': 'budget',
        'MarketingPlatforms': 'platform',
        'MarketingTasks': 'task',
        'LandingPages': 'page',
        'LeadSources': 'source',
        'PartnerAgreements': 'agreement',
        'PartnerCommissions': 'commission',
        'PartnerEngagements': 'engagement',
        'Referrals': 'referral',
        'ReferralDocuments': 'document',
        'TaskDelegations': 'delegation',
    }
    
    updated_files = []
    
    for module_dir in glob.glob(os.path.join(ADMIN_PAGES_PATH, "*")):
        module_name = os.path.basename(module_dir)
        
        if module_name not in variable_mappings:
            continue
        
        correct_var = variable_mappings[module_name]
        index_file = os.path.join(module_dir, "Index.vue")
        
        if os.path.exists(index_file):
            try:
                with open(index_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                original_content = content
                
                # Fix variable names in routes (item.id -> correct_var.id)
                content = content.replace('item.id)', f'{correct_var}.id)')
                content = content.replace('"item.id"', f'"{correct_var}.id"')
                
                if content != original_content:
                    with open(index_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    
                    updated_files.append(f"{module_name}/Index.vue")
                    
            except Exception as e:
                print(f"  ❌ Error fixing variables in {index_file}: {e}")
    
    print(f"  ✅ Fixed variable names in {len(updated_files)} files")
    return updated_files

def remove_delete_buttons_from_edit():
    """Remove delete buttons from Edit.vue files to match Patient pattern"""
    print("🔧 Removing delete buttons from Edit.vue files...")
    
    updated_files = []
    
    for module_dir in glob.glob(os.path.join(ADMIN_PAGES_PATH, "*")):
        module_name = os.path.basename(module_dir)
        
        # Skip modules that should keep delete buttons
        if module_name in ["CaregiverAssignments"]:
            continue
        
        edit_file = os.path.join(module_dir, "Edit.vue")
        
        if os.path.exists(edit_file):
            try:
                with open(edit_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                original_content = content
                
                # Remove delete button and fix footer layout to match Patient pattern
                # Pattern 1: delete button on the left
                delete_pattern1 = r'<div class="flex justify-between items-center gap-2 pt-2">\s*<button[^>]*class="[^"]*"[^>]*@click="[^"]*destroy[^"]*"[^>]*>[^<]*</button>\s*<div class="flex gap-2">'
                
                content = re.sub(delete_pattern1, '<div class="flex justify-end gap-2 pt-2">', content, flags=re.DOTALL)
                
                # Pattern 2: delete button mixed in with other buttons
                delete_pattern2 = r'<button[^>]*@click="[^"]*destroy[^"]*"[^>]*>[^<]*</button>\s*'
                content = re.sub(delete_pattern2, '', content, flags=re.DOTALL)
                
                # Fix any leftover closing divs
                content = re.sub(r'</div>\s*</div>\s*</div>\s*</form>', '</div>\n        </form>', content)
                
                if content != original_content:
                    with open(edit_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    
                    updated_files.append(f"{module_name}/Edit.vue")
                    
            except Exception as e:
                print(f"  ❌ Error removing delete button from {edit_file}: {e}")
    
    print(f"  ✅ Removed delete buttons from {len(updated_files)} files")
    return updated_files

def main():
    """Run all fixes"""
    print("🚀 Starting Liquid Glass Issue Fixes...")
    print("=" * 50)
    
    all_updated_files = []
    
    # Run specific fixes
    fix_staff_index()
    fix_staff_payouts_index()
    fix_invoices_index()
    
    # Run batch fixes
    all_updated_files.extend(fix_route_names())
    all_updated_files.extend(fix_variable_names())
    all_updated_files.extend(remove_delete_buttons_from_edit())
    
    print("=" * 50)
    print("✅ All fixes completed!")
    print(f"📊 Total files updated: {len(all_updated_files)}")
    
    if all_updated_files:
        print("\n📋 Updated files:")
        for file in sorted(set(all_updated_files)):
            print(f"  • {file}")

if __name__ == "__main__":
    main()
