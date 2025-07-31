<template>
    <div>
        <h1>Ethiopian Calendar Days List (Current View)</h1>
        <table>
            <thead>
                <tr>
                    <th>Gregorian Date</th>
                    <th>Ethiopian Date</th>
                    <th>Description</th>
                    <th>Is Holiday</th>
                    <th>Region</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="day in ethiopianCalendarDays" :key="day.id">
                    <td>{{ day.gregorian_date }}</td>
                    <td>{{ day.ethiopian_date }}</td>
                    <td>{{ day.description }}</td>
                    <td>{{ day.is_holiday ? 'Yes' : 'No' }}</td>
                    <td>{{ day.region }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    ethiopianCalendarDays: Array,
});
</script>
