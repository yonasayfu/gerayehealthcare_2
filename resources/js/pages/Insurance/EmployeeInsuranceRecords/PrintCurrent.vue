<template>
    <div>
        <h1>Employee Insurance Records List (Current View)</h1>
        <table>
            <thead>
                <tr>
                    <th>Kebele ID</th>
                    <th>Woreda</th>
                    <th>Region</th>
                    <th>Federal ID</th>
                    <th>Ministry Department</th>
                    <th>Employee ID Number</th>
                    <th>Verified</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="record in employeeInsuranceRecords" :key="record.id">
                    <td>{{ record.kebele_id }}</td>
                    <td>{{ record.woreda }}</td>
                    <td>{{ record.region }}</td>
                    <td>{{ record.federal_id }}</td>
                    <td>{{ record.ministry_department }}</td>
                    <td>{{ record.employee_id_number }}</td>
                    <td>{{ record.verified ? 'Yes' : 'No' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    employeeInsuranceRecords: Array,
});
</script>
