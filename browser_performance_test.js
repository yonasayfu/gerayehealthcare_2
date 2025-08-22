// Browser Console Performance Test
// Copy and paste this into browser console on any page of your app

console.log("🚀 Starting Performance Baseline Test...");

// Test 1: Memory Usage
const memoryInfo = performance.memory || {};
console.log("📊 Memory Usage:", {
    usedJSHeapSize: Math.round((memoryInfo.usedJSHeapSize || 0) / 1024 / 1024 * 100) / 100 + " MB",
    totalJSHeapSize: Math.round((memoryInfo.totalJSHeapSize || 0) / 1024 / 1024 * 100) / 100 + " MB"
});

// Test 2: Navigation Timing
const navTiming = performance.getEntriesByType('navigation')[0];
if (navTiming) {
    console.log("⏱️ Navigation Timing:", {
        domContentLoaded: Math.round(navTiming.domContentLoadedEventEnd - navTiming.domContentLoadedEventStart) + "ms",
        loadComplete: Math.round(navTiming.loadEventEnd - navTiming.loadEventStart) + "ms",
        totalPageLoad: Math.round(navTiming.loadEventEnd - navTiming.fetchStart) + "ms"
    });
}

// Test 3: Resource Loading
const resources = performance.getEntriesByType('resource');
console.log("📦 Resource Analysis:", {
    totalResources: resources.length,
    jsFiles: resources.filter(r => r.name.includes('.js')).length,
    cssFiles: resources.filter(r => r.name.includes('.css')).length,
    apiCalls: resources.filter(r => r.name.includes('/api/') || r.name.includes('/dashboard/')).length
});

// Test 4: Largest Contentful Paint
try {
    new PerformanceObserver((list) => {
        const entries = list.getEntries();
        const lcp = entries[entries.length - 1];
        console.log("🎯 Largest Contentful Paint:", Math.round(lcp.startTime) + "ms");
    }).observe({type: 'largest-contentful-paint', buffered: true});
} catch (e) {
    console.log("LCP not available");
}

// Test 5: Vue Component Count (if Vue devtools available)
if (window.__VUE_DEVTOOLS_GLOBAL_HOOK__) {
    console.log("🔧 Vue.js detected - component analysis available in Vue DevTools");
}

// Test 6: Network Requests Timing
setTimeout(() => {
    const networkEntries = performance.getEntriesByType('resource')
        .filter(entry => entry.name.includes(window.location.origin))
        .sort((a, b) => b.duration - a.duration)
        .slice(0, 10);
    
    console.log("🌐 Slowest Network Requests:");
    networkEntries.forEach(entry => {
        console.log(`  ${entry.name.split('/').pop()}: ${Math.round(entry.duration)}ms`);
    });
}, 2000);

console.log("✅ Performance test completed. Check console output above.");