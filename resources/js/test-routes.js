// Test file to check if routes are working
(() => {
  try {
    if (typeof route === 'function') {
      // Only attempt when route().has says it's present
      const has = (typeof route().has === 'function') ? route().has : () => false;

      if (has('admin.marketing-campaigns.index')) {
        console.debug('[routes] marketing:', route('admin.marketing-campaigns.index'));
      }
      if (has('admin.leave-requests.index')) {
        console.debug('[routes] leave-requests:', route('admin.leave-requests.index'));
      } else {
        console.debug('[routes] leave-requests missing in Ziggy list (dev)');
      }
    }
  } catch { /* ignore */ }
})();
