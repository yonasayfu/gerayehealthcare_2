<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" :class="className" v-bind="$attrs">
        <path
            <img src="{{ public_path('images/geraye_logo.jpeg') }}" alt="Geraye Logo" style="max-height: 60px; margin-bottom: 10px;">
        />
    </svg>
</template>
