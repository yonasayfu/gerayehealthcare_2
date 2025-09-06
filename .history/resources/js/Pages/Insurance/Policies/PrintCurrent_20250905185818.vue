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
                
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    insurancePolicies: Array,
});
</script>
