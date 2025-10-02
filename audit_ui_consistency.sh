#!/bin/bash

# UI Consistency Audit Script for Geraye Healthcare
# This script audits all Index.vue and Show.vue files for UI consistency

echo "==================================="
echo "UI CONSISTENCY AUDIT REPORT"
echo "==================================="
echo ""

ADMIN_PATH="/Users/yonassayfu/VSProject/gerayehealthcare/resources/js/pages/Admin"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Counter for inconsistencies
total_issues=0

echo "1. BUTTON STYLE CONSISTENCY CHECK"
echo "-----------------------------------"
echo ""

echo "Checking for inconsistent button classes..."
echo ""

# Check for different button patterns
echo "❌ Files using non-standard button classes:"
grep -r "class=\".*btn" "$ADMIN_PATH" --include="*.vue" | \
  grep -v "btn-glass" | \
  grep -v "btn-primary" | \
  grep -v "btn-secondary" | \
  grep -v "class=\"btn\"" | \
  cut -d: -f1 | sort -u | head -20

echo ""
echo "✅ Files using standard btn-glass pattern:"
grep -r "btn-glass" "$ADMIN_PATH" --include="Index.vue" | cut -d: -f1 | sort -u | wc -l
echo " Index.vue files found"

echo ""
echo "2. PRINT BUTTON CONSISTENCY CHECK"
echo "-----------------------------------"
echo ""

echo "Checking Print Current button presence in Index.vue files..."
total_index=$(find "$ADMIN_PATH" -name "Index.vue" | wc -l | tr -d ' ')
with_print_current=$(grep -l "Print Current" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Total Index.vue files: $total_index"
echo "With 'Print Current' button: $with_print_current"
echo ""

echo "❌ Index.vue files MISSING 'Print Current' button:"
for file in $(find "$ADMIN_PATH" -name "Index.vue"); do
  if ! grep -q "Print Current" "$file" 2>/dev/null; then
    echo "  - ${file##*/Admin/}"
  fi
done | head -20

echo ""
echo "Checking Print button presence in Show.vue files..."
total_show=$(find "$ADMIN_PATH" -name "Show.vue" | wc -l | tr -d ' ')
with_print_show=$(grep -l -E "(printPage|printCurrent|Print Current|window.print)" "$ADMIN_PATH"/*/Show.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Total Show.vue files: $total_show"
echo "With Print functionality: $with_print_show"
echo ""

echo ""
echo "3. TABLE LAYOUT CONSISTENCY CHECK"
echo "-----------------------------------"
echo ""

echo "Checking table wrapper classes..."
echo "Files using standard table wrapper:"
grep -l "overflow-x-auto bg-white" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | wc -l
echo ""

echo "Checking for ArrowUpDown sort icons..."
with_sort_icons=$(grep -l "ArrowUpDown" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Index files with sortable columns: $with_sort_icons"
echo ""

echo ""
echo "4. SEARCH INPUT CONSISTENCY CHECK"
echo "-----------------------------------"
echo ""

echo "Checking search input patterns..."
with_search=$(grep -l "v-model=\"search\"" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Index files with search: $with_search / $total_index"
echo ""

echo "Checking search-glass wrapper usage..."
with_search_glass=$(grep -l "search-glass" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Index files using search-glass: $with_search_glass"
echo ""

echo ""
echo "5. PAGINATION CONSISTENCY CHECK"
echo "-----------------------------------"
echo ""

echo "Checking Pagination component import..."
with_pagination=$(grep -l "import Pagination from" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Index files with Pagination: $with_pagination / $total_index"
echo ""

echo ""
echo "6. LIQUID GLASS HEADER CONSISTENCY CHECK"
echo "-----------------------------------"
echo ""

echo "Checking liquidGlass-wrapper usage in Index files..."
with_liquid=$(grep -l "liquidGlass-wrapper" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Index files with liquidGlass header: $with_liquid / $total_index"
echo ""

echo ""
echo "7. SHOWHEADER COMPONENT USAGE CHECK"
echo "-----------------------------------"
echo ""

echo "Checking ShowHeader component import in Show files..."
with_showheader=$(grep -l "import ShowHeader" "$ADMIN_PATH"/*/Show.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Show files with ShowHeader component: $with_showheader / $total_show"
echo ""

echo ""
echo "8. ACTION BUTTONS CONSISTENCY (Show.vue)"
echo "-----------------------------------"
echo ""

echo "Checking for Edit button in Show.vue files..."
with_edit=$(grep -l -E "(Edit|admin\.[a-z]+\.edit)" "$ADMIN_PATH"/*/Show.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Show files with Edit button: $with_edit / $total_show"
echo ""

echo "Checking for Delete button in Show.vue files..."
with_delete=$(grep -l -E "(Delete|destroy|Trash2)" "$ADMIN_PATH"/*/Show.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Show files with Delete button: $with_delete / $total_show"
echo ""

echo ""
echo "9. PER-PAGE SELECTOR CONSISTENCY CHECK"
echo "-----------------------------------"
echo ""

echo "Checking perPage selector styling..."
grep -l "v-model=\"perPage\"" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | while read file; do
  # Check if it has consistent styling
  if grep -q "border-cyan-600 bg-cyan-600" "$file"; then
    echo "✅ Cyan style: ${file##*/Admin/}"
  elif grep -q "border-gray" "$file"; then
    echo "⚠️  Gray style: ${file##*/Admin/}"
  else
    echo "❌ Unknown style: ${file##*/Admin/}"
  fi
done | head -20

echo ""
echo "10. EXPORT BUTTONS CONSISTENCY CHECK"
echo "-----------------------------------"
echo ""

echo "Checking Export CSV button presence..."
with_export=$(grep -l "Export CSV" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Index files with Export CSV: $with_export / $total_index"
echo ""

echo "Checking useExport composable usage..."
with_use_export=$(grep -l "useExport" "$ADMIN_PATH"/*/Index.vue 2>/dev/null | wc -l | tr -d ' ')
echo "Index files using useExport composable: $with_use_export"
echo ""

echo ""
echo "==================================="
echo "SUMMARY"
echo "==================================="
echo ""
echo "Total Index.vue files: $total_index"
echo "Total Show.vue files: $total_show"
echo ""
echo "CONSISTENCY METRICS:"
echo "  - LiquidGlass headers: $with_liquid / $total_index"
echo "  - Search functionality: $with_search / $total_index"
echo "  - Pagination: $with_pagination / $total_index"
echo "  - Print buttons (Index): $with_print_current / $total_index"
echo "  - Print buttons (Show): $with_print_show / $total_show"
echo "  - Export CSV buttons: $with_export / $total_index"
echo "  - ShowHeader component: $with_showheader / $total_show"
echo ""
echo "ESTIMATED CONSISTENCY RATE:"
avg_consistency=$(( (with_liquid + with_search + with_pagination + with_print_current + with_export) * 100 / (total_index * 5) ))
echo "  Index.vue files: ~${avg_consistency}%"
echo ""
echo "==================================="
echo "END OF AUDIT REPORT"
echo "==================================="
