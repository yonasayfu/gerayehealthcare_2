<template>
    <div>
        <h1>Insurance Companies List (Current View)</h1>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Person</th>
                    <th>Contact Email</th>
                    <th>Contact Phone</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="company in insuranceCompanies" :key="company.id">
                    <td>{{ company.name }}</td>
                    <td>{{ company.contact_person }}</td>
                    <td>{{ company.contact_email }}</td>
                    <td>{{ company.contact_phone }}</td>
                    <td>{{ company.address }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    insuranceCompanies: Array,
});
</script>
