<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    patients: Array,
});

onMounted(() => {
    window.print();
});
</script>

<template>
    <div class="print-container">
        <h1>Current Patients List</h1>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Patient Code</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="patient in patients" :key="patient.id">
                    <td>{{ patient.full_name }}</td>
                    <td>{{ patient.patient_code }}</td>
                    <td>{{ patient.email }}</td>
                    <td>{{ patient.phone_number }}</td>
                    <td>{{ patient.address }}</td>
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
