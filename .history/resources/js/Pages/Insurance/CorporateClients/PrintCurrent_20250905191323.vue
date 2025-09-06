<template>
    <div>
        <h1>Corporate Clients List (Current View)</h1>
        <table>
            <thead>
                <tr>
                    <th>Organization Name</th>
                    <th>Contact Person</th>
                    <th>Contact Email</th>
                    <th>Contact Phone</th>
                    <th>TIN Number</th>
                    <th>Trade License Number</th>
                    <th>Address</th>
                </tr>
            </thead>
            
        </table>
    </div>
</template>

<script setup>
defineProps({
    corporateClients: Array,
});
</script>
