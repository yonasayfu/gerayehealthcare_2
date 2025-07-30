<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    campaigns: Array,
});

onMounted(() => {
    window.print();
});
</script>

<template>
    <div class="print-container">
        <h1>Current Marketing Campaigns List</h1>
        <table>
            <thead>
                <tr>
                    <th>Campaign Name</th>
                    <th>Campaign Code</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="campaign in campaigns" :key="campaign.id">
                    <td>{{ campaign.campaign_name }}</td>
                    <td>{{ campaign.campaign_code }}</td>
                    <td>{{ campaign.status }}</td>
                    <td>{{ campaign.start_date }}</td>
                    <td>{{ campaign.end_date }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style>
/* Basic styling for print */
.print-container {
    font-family: Arial, sans-serif;
    margin: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
}
</style>
