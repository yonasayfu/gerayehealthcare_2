<template>
    <div>
        <h1>Exchange Rates List (Current View)</h1>
        <table>
            <thead>
                <tr>
                    <th>Currency Code</th>
                    <th>Rate to ETB</th>
                    <th>Source</th>
                    <th>Date Effective</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="rate in exchangeRates" :key="rate.id">
                    <td>{{ rate.currency_code }}</td>
                    <td>{{ rate.rate_to_etb }}</td>
                    <td>{{ rate.source }}</td>
                    <td>{{ rate.date_effective }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    exchangeRates: Array,
});
</script>
