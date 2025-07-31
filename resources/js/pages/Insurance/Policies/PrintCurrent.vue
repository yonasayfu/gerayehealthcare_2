<template>
    <div>
        <h1>Insurance Policies List (Current View)</h1>
        <table>
            <thead>
                <tr>
                    <th>Service Type</th>
                    <th>Coverage Percentage</th>
                    <th>Coverage Type</th>
                    <th>Is Active</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="policy in insurancePolicies" :key="policy.id">
                    <td>{{ policy.service_type }}</td>
                    <td>{{ policy.coverage_percentage }}</td>
                    <td>{{ policy.coverage_type }}</td>
                    <td>{{ policy.is_active ? 'Yes' : 'No' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    insurancePolicies: Array,
});
</script>
