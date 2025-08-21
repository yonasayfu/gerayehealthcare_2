#!/bin/bash

# Script to identify Show.vue files that need JavaScript error fixes
# Run this to see which files still need the variable reference fixes

echo "🔍 Finding Show.vue files with problematic patterns..."
echo ""

echo "📋 Files with 'item.name || item.title || item.id' pattern:"
echo "======================================================="
grep -l "item\.name.*item\.title.*item\.id" resources/js/pages/Admin/*/Show.vue 2>/dev/null | sort
echo ""

echo "🔧 Files with 'printSingle' function calls:"
echo "=========================================="
grep -l "printSingle[A-Z]" resources/js/pages/Admin/*/Show.vue 2>/dev/null | sort
echo ""

echo "🔗 Files with 'item.id' in Edit routes:"
echo "====================================="
grep -l "item\.id.*btn-glass.*Edit" resources/js/pages/Admin/*/Show.vue 2>/dev/null | sort
echo ""

echo "📊 Summary:"
echo "==========="
TOTAL_PROBLEM_FILES=$(grep -l "item\.name.*item\.title.*item\.id\|printSingle[A-Z]\|item\.id.*btn-glass.*Edit" resources/js/pages/Admin/*/Show.vue 2>/dev/null | sort -u | wc -l)
echo "Files still needing fixes: $TOTAL_PROBLEM_FILES"
echo ""

echo "✅ Already Fixed Files (should not appear above):"
echo "================================================"
echo "- MarketingCampaigns/Show.vue"
echo "- EligibilityCriteria/Show.vue"  
echo "- LandingPages/Show.vue"
echo "- EventRecommendations/Show.vue"
echo "- MarketingLeads/Show.vue"
echo "- Partners/Show.vue"
echo "- LeadSources/Show.vue"
echo "- VisitServices/Show.vue"
echo ""

echo "📖 Next Steps:"
echo "=============="
echo "1. For each file listed above, open it in your editor"
echo "2. Find the defineProps<{...}>() to identify the correct variable name"
echo "3. Apply the 3 fix patterns from SHOW_VUE_FIXES_SUMMARY.md"
echo "4. Test the page to ensure no console errors"
echo "5. Move to the next file"
echo ""
echo "📄 See SHOW_VUE_FIXES_SUMMARY.md for detailed instructions"
