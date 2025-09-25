// Test file to check if routes are working
console.log('Testing route generation...');

// Check if Ziggy is available
if (typeof window.Ziggy !== 'undefined') {
    console.log('Ziggy is available');
    console.log('Available routes:', Object.keys(window.Ziggy.routes).filter(name => 
        name.includes('marketing-campaigns') || name.includes('leave-requests')));
} else {
    console.log('Ziggy is not available');
}

// Try to generate URLs
try {
    if (typeof route !== 'undefined') {
        console.log('route() function is available');
        
        const campaignRoute = route('admin.marketing-campaigns.index');
        console.log('admin.marketing-campaigns.index URL:', campaignRoute);
        
        const leaveRoute = route('admin.leave-requests.index');
        console.log('admin.leave-requests.index URL:', leaveRoute);
    } else {
        console.log('route() function is not available');
    }
} catch (error) {
    console.error('Error generating routes:', error);
}

// Check Inertia
if (typeof window.Inertia !== 'undefined') {
    console.log('Inertia is available');
} else {
    console.log('Inertia is not available');
}