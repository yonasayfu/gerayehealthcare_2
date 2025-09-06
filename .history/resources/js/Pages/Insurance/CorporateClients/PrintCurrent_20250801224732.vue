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
            <tbody>
                <tr v-for="client in corporateClients" :key="client.id">
                    <td>{{ client.organization_name }}</td>
                    <td>{{ client.contact_person }}</td>
                    <td>{{ client.contact_email }}</td>
                    <td>{{ client.contact_phone }}</td>
                    <td>{{ client.tin_number }}</td>
                    <td>{{ client.trade_license_number }}</td>
                    <td>{{ client.address }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    corporateClients: Array,
});
</script>
