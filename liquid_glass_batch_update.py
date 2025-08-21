#!/usr/bin/env python3
"""
Liquid Glass UI Batch Update Script
Applies liquid glass pattern to all Vue components in the Admin modules
"""

import os
import re
import glob
from pathlib import Path

# Define the base path for Admin modules
ADMIN_PAGES_PATH = r"C:\MyProject\gerayehealthcare_2\resources\js\pages\Admin"

# Modules to skip (already updated)
SKIP_MODULES = ["Patients", "CaregiverAssignments"]

def update_index_files():
    """Update all Index.vue files with liquid glass header and search patterns"""
    print("🔄 Updating Index.vue files...")
    
    # Header patterns to replace
    header_patterns = [
        # Pattern 1: bg-muted/40 header
        {
            'search': r'<div class="rounded-lg bg-muted/40 p-4 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">\s*<div>\s*<h1 class="text-xl font-semibold text-gray-800 dark:text-white">([^<]+)</h1>\s*<p class="text-sm text-muted-foreground">([^<]+)</p>\s*</div>\s*<div class="flex flex-wrap gap-2 no-print">(.*?)</div>\s*</div>',
            'replace': '''      <!-- Liquid glass header -->
      <div class="liquidGlass-wrapper print:hidden">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-4 gap-4">
          <div class="print:hidden">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">\\1</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">\\2</p>
          </div>

          <div class="flex items-center gap-2 print:hidden">\\3</div>
        </div>
      </div>'''
        }
    ]
    
    # Search input patterns to replace
    search_patterns = [
        {
            'search': r'<div class="relative w-full md:w-1/3">\s*<input[^>]*v-model="search"[^>]*class="form-input[^"]*"[^>]*/?>\s*<Search[^>]*/?>\s*</div>',
            'replace': '''        <div class="search-glass relative w-full md:w-1/3">
          <input
            v-model="search"
            type="text"
            placeholder="Search..."
            class="shadow-sm bg-white border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full pl-3 pr-10 py-2.5 dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-gray-100 relative z-10"
          />
          <Search class="absolute right-3 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400 dark:text-gray-400 z-20" />
        </div>'''
        }
    ]
    
    # Button conversion patterns
    button_patterns = [
        {'search': r'class="btn btn-primary"', 'replace': 'class="btn-glass"'},
        {'search': r'class="btn btn-success"', 'replace': 'class="btn-glass btn-glass-sm"'},
        {'search': r'class="btn btn-dark"', 'replace': 'class="btn-glass btn-glass-sm"'},
        {'search': r'class="btn btn-info"', 'replace': 'class="btn-glass btn-glass-sm"'},
    ]
    
    count = 0
    for module_dir in glob.glob(os.path.join(ADMIN_PAGES_PATH, "*")):
        module_name = os.path.basename(module_dir)
        if module_name in SKIP_MODULES or not os.path.isdir(module_dir):
            continue
            
        index_file = os.path.join(module_dir, "Index.vue")
        if os.path.exists(index_file):
            try:
                with open(index_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                original_content = content
                
                # Apply header patterns
                for pattern in header_patterns:
                    content = re.sub(pattern['search'], pattern['replace'], content, flags=re.DOTALL)
                
                # Apply search patterns
                for pattern in search_patterns:
                    content = re.sub(pattern['search'], pattern['replace'], content, flags=re.DOTALL)
                
                # Apply button patterns
                for pattern in button_patterns:
                    content = re.sub(pattern['search'], pattern['replace'], content)
                
                if content != original_content:
                    with open(index_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    count += 1
                    print(f"  ✅ Updated: {module_name}/Index.vue")
                    
            except Exception as e:
                print(f"  ❌ Error updating {module_name}/Index.vue: {e}")
    
    print(f"📊 Updated {count} Index.vue files")

def update_create_edit_files():
    """Update Create.vue and Edit.vue files with liquid glass headers and footers"""
    print("🔄 Updating Create.vue and Edit.vue files...")
    
    # Header patterns for Create/Edit pages
    header_patterns = [
        {
            'search': r'<div class="bg-white border border-4 rounded-lg shadow relative m-10">\s*<div class="flex items-start justify-between p-5 border-b rounded-t">\s*<h3 class="text-xl font-semibold">\s*([^<]+)\s*</h3>.*?</div>',
            'replace': '''    <div class="space-y-6 p-6">

      <!-- Liquid-glass header -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">\\1</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Fill required information.</p>
          </div>
        </div>
      </div>'''
        }
    ]
    
    # Footer patterns
    footer_patterns = [
        {
            'search': r'<div class="p-6 border-t border-gray-200 rounded-b">\s*<div class="flex flex-wrap.*?gap-2">(.*?)</div>\s*</div>\s*</div>',
            'replace': '''      <!-- Form card -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm p-6">
        <!-- Form content goes here -->
        <div class="flex justify-end gap-2 pt-2">
          <!-- Footer actions with glass buttons -->\\1
        </div>
      </div>
    </div>'''
        }
    ]
    
    count = 0
    for module_dir in glob.glob(os.path.join(ADMIN_PAGES_PATH, "*")):
        module_name = os.path.basename(module_dir)
        if module_name in SKIP_MODULES or not os.path.isdir(module_dir):
            continue
            
        for file_type in ["Create.vue", "Edit.vue"]:
            target_file = os.path.join(module_dir, file_type)
            if os.path.exists(target_file):
                try:
                    with open(target_file, 'r', encoding='utf-8') as f:
                        content = f.read()
                    
                    original_content = content
                    
                    # Apply patterns
                    for pattern in header_patterns + footer_patterns:
                        content = re.sub(pattern['search'], pattern['replace'], content, flags=re.DOTALL)
                    
                    # Convert button classes
                    content = re.sub(r'class="btn btn-([^"]*)"', r'class="btn-glass btn-glass-sm"', content)
                    
                    if content != original_content:
                        with open(target_file, 'w', encoding='utf-8') as f:
                            f.write(content)
                        count += 1
                        print(f"  ✅ Updated: {module_name}/{file_type}")
                        
                except Exception as e:
                    print(f"  ❌ Error updating {module_name}/{file_type}: {e}")
    
    print(f"📊 Updated {count} Create/Edit files")

def update_form_files():
    """Update Form.vue files with dark-friendly select elements"""
    print("🔄 Updating Form.vue files...")
    
    # Select element patterns
    select_patterns = [
        {
            'search': r'class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2\.5"',
            'replace': 'class="shadow-sm border border-gray-300 text-gray-900 dark:text-white sm:text-sm rounded-lg focus:ring-cyan-600 focus:border-cyan-600 block w-full p-2.5 bg-white dark:bg-gray-800"'
        }
    ]
    
    count = 0
    for module_dir in glob.glob(os.path.join(ADMIN_PAGES_PATH, "*")):
        module_name = os.path.basename(module_dir)
        if module_name in SKIP_MODULES or not os.path.isdir(module_dir):
            continue
            
        form_file = os.path.join(module_dir, "Form.vue")
        if os.path.exists(form_file):
            try:
                with open(form_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                original_content = content
                
                # Apply select patterns
                for pattern in select_patterns:
                    content = re.sub(pattern['search'], pattern['replace'], content)
                
                if content != original_content:
                    with open(form_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    count += 1
                    print(f"  ✅ Updated: {module_name}/Form.vue")
                    
            except Exception as e:
                print(f"  ❌ Error updating {module_name}/Form.vue: {e}")
    
    print(f"📊 Updated {count} Form.vue files")

def update_show_files():
    """Update Show.vue files with liquid glass headers and action buttons"""
    print("🔄 Updating Show.vue files...")
    
    # Similar patterns as Create/Edit but for Show pages
    header_patterns = [
        {
            'search': r'<div class="bg-white border border-4 rounded-lg shadow relative m-10">\s*<div class="flex items-start justify-between p-5 border-b rounded-t print:hidden">\s*<h3 class="text-xl font-semibold">\s*([^<]+)\s*</h3>.*?</div>',
            'replace': '''    <div class="space-y-6 p-6">

      <!-- Liquid-glass header -->
      <div class="liquidGlass-wrapper print:hidden w-full rounded-t-lg">
        <div class="liquidGlass-inner-shine" aria-hidden="true"></div>
        <div class="liquidGlass-content flex items-center justify-between p-6">
          <div class="print:hidden">
            <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">\\1</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">View details and information.</p>
          </div>
        </div>
      </div>'''
        }
    ]
    
    count = 0
    for module_dir in glob.glob(os.path.join(ADMIN_PAGES_PATH, "*")):
        module_name = os.path.basename(module_dir)
        if module_name in SKIP_MODULES or not os.path.isdir(module_dir):
            continue
            
        show_file = os.path.join(module_dir, "Show.vue")
        if os.path.exists(show_file):
            try:
                with open(show_file, 'r', encoding='utf-8') as f:
                    content = f.read()
                
                original_content = content
                
                # Apply patterns
                for pattern in header_patterns:
                    content = re.sub(pattern['search'], pattern['replace'], content, flags=re.DOTALL)
                
                # Convert button classes in footer
                content = re.sub(r'class="btn btn-([^"]*)"', r'class="btn-glass btn-glass-sm"', content)
                
                if content != original_content:
                    with open(show_file, 'w', encoding='utf-8') as f:
                        f.write(content)
                    count += 1
                    print(f"  ✅ Updated: {module_name}/Show.vue")
                    
            except Exception as e:
                print(f"  ❌ Error updating {module_name}/Show.vue: {e}")
    
    print(f"📊 Updated {count} Show.vue files")

def main():
    """Main function to run all updates"""
    print("🚀 Starting Liquid Glass UI Batch Update...")
    print(f"📁 Target directory: {ADMIN_PAGES_PATH}")
    print(f"⏭️  Skipping modules: {', '.join(SKIP_MODULES)}")
    print("-" * 50)
    
    # Run all update functions
    update_index_files()
    print()
    update_create_edit_files()
    print()
    update_form_files()
    print()
    update_show_files()
    
    print("-" * 50)
    print("✅ Batch update completed!")
    print()
    print("📋 Next Steps:")
    print("1. Test a few modules to ensure the updates work correctly")
    print("2. Check both light and dark mode")
    print("3. Verify print functionality")
    print("4. Run the development server and browse through modules")

if __name__ == "__main__":
    main()
