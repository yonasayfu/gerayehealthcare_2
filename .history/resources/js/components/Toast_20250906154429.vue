<script setup lang="ts">
import { computed, ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage(); // Get the page object once

const show = ref(true);
let hideTimer: number | null = null;

// Correctly read flash from Inertia shared props
const style = computed(() => page.props.flash?.bannerStyle || 'success');
const message = computed(() => page.props.flash?.banner || '');

const clearHideTimer = () => {
    if (hideTimer) {
        clearTimeout(hideTimer);
        hideTimer = null;
    }
};

const durationForStyle = () => {
    switch (style.value) {
        case 'danger':
        case 'warning':
            return 12000; // keep errors/warnings longer
        case 'info':
            return 9000;
        case 'success':
        default:
            return 8000;
    }
};

const startHideTimer = () => {
    if (!message.value) return;
    show.value = true;
    clearHideTimer();
    hideTimer = window.setTimeout(() => {
        show.value = false;
        hideTimer = null;
    }, durationForStyle());
};

const close = () => {
    clearHideTimer();
    show.value = false;
};

const onMouseEnter = () => {
    // Pause auto-dismiss while hovering
    clearHideTimer();
};

const onMouseLeave = () => {
    // Resume with a shorter delay after hover
    if (show.value && message.value) {
        clearHideTimer();
        hideTimer = window.setTimeout(() => {
            show.value = false;
            hideTimer = null;
        }, 3000);
    }
};

onMounted(() => {
    startHideTimer();
});

// Re-run when flash message changes (new Inertia responses)
watch(() => page.props.flash, () => {
    startHideTimer();
}, { deep: true });
</script>

<template>
    <div class="fixed z-50 w-full px-2 mb-4"
         :class="[
            // Mobile: bottom-center
            'left-1/2 -translate-x-1/2 bottom-4',
            // md+: bottom-right
            // Shift left a bit to avoid chat icon overlap
            'md:left-auto md:translate-x-0 md:right-24 md:bottom-8 md:w-auto md:max-w-md'
         ]">
        <!-- Only render if visible and has a message -->
        <div v-if="show && message"
             role="status"
             aria-live="polite"
             @mouseenter="onMouseEnter"
             @mouseleave="onMouseLeave"
             :class="{
                'bg-green-200': style == 'success',
                'bg-yellow-200': style == 'danger',
                'bg-orange-200': style == 'warning',
                'bg-blue-200': style == 'info',
             }"
             class="px-4 py-3 rounded-md text-sm flex items-start shadow-lg border border-black/5">
            <svg v-if="style == 'success'" viewBox="0 0 24 24" class="text-green-600 w-5 h-5 sm:w-5 sm:h-5 mr-3">
                <path fill="currentColor"
                    d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z">
                </path>
            </svg>
            <svg v-if="style == 'danger'" viewBox="0 0 24 24" class="text-red-600 w-5 h-5 sm:w-5 sm:h-5 mr-3">
                <path fill="currentColor"
                    d="M11.983,0a12.206,12.206,0,0,0-8.51,3.653A11.8,11.8,0,0,0,0,12.207,11.779,11.779,0,0,0,11.8,24h.214A12.111,12.111,0,0,0,24,11.791h0A11.766,11.766,0,0,0,11.983,0ZM10.5,16.542a1.476,1.476,0,0,1,1.449-1.53h.027a1.527,1.527,0,0,1,1.523,1.47,1.475,1.475,0,0,1-1.449,1.53h-.027A1.529,1.529,0,0,1,10.5,16.542ZM11,12.5v-6a1,1,0,0,1,2,0v6a1,1,0,1,1-2,0Z">
                </path>
            </svg>
            <svg v-if="style == 'warning'" viewBox="0 0 24 24" class="text-yellow-600 w-5 h-5 sm:w-5 sm:h-5 mr-3">
                <path fill="currentColor"
                    d="M23.119,20,13.772,2.15h0a2,2,0,0,0-3.543,0L.881,20a2,2,0,0,0,1.772,2.928H21.347A2,2,0,0,0,23.119,20ZM11,8.423a1,1,0,0,1,2,0v6a1,1,0,1,1-2,0Zm1.05,11.51h-.028a1.528,1.528,0,0,1-1.522-1.47,1.476,1.476,0,0,1,1.448-1.53h.028A1.527,1.527,0,0,1,13.5,18.4,1.475,1.475,0,0,1,12.05,19.933Z">
                </path>
            </svg>
            <svg v-if="style == 'info'" viewBox="0 0 24 24" class="text-blue-600 w-5 h-5 sm:w-5 sm:h-5 mr-3">
                <path fill="currentColor"
                    d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0Zm.25,5a1.5,1.5,0,1,1-1.5,1.5A1.5,1.5,0,0,1,12.25,5ZM14.5,18.5h-4a1,1,0,0,1,0-2h.75a.25.25,0,0,0,.25-.25v-4.5a.25.25,0,0,0-.25-.25H10.5a1,1,0,0,1,0-2h1a2,2,0,0,1,2,2v4.75a.25.25,0,0,0,.25.25h.75a1,1,0,1,1,0,2Z">
                </path>
            </svg>
            <div class="flex-1 pr-6">
                <span :class="{
                    'text-green-800': style == 'success',
                    'text-red-800': style == 'danger',
                    'text-yellow-800': style == 'warning',
                    'text-blue-800': style == 'info',
                }">{{ message }}</span>
            </div>
            <button type="button"
                    @click="close"
                    class="ml-2 text-gray-500 hover:text-gray-700 focus:outline-none"
                    aria-label="Close notification">
                <svg viewBox="0 0 24 24" class="w-4 h-4">
                    <path fill="currentColor" d="M18.3 5.71a1 1 0 0 0-1.41 0L12 10.59 7.11 5.7A1 1 0 0 0 5.7 7.11L10.59 12l-4.9 4.89a1 1 0 1 0 1.41 1.42L12 13.41l4.89 4.9a1 1 0 0 0 1.42-1.41L13.41 12l4.9-4.89a1 1 0 0 0-.01-1.4Z" />
                </svg>
            </button>
        </div>
    </div>
</template>