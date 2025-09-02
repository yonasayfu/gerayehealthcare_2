<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home, register } from '@/routes';
import { Link, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { computed, ref } from 'vue';
import { Pin, PinOff } from 'lucide-vue-next';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote as { message: string; author?: string; image?: string | null } | undefined;
const isGuest = computed(() => !page.props.auth?.user);
const isRegister = computed(() => typeof page.url === 'string' && page.url.startsWith('/register'));

// Guest-pinned quote stored locally
type LocalQuote = { message: string; author?: string; image?: string | null } | null;
const guestPinnedRaw = typeof window !== 'undefined' ? window.localStorage.getItem('guestPinnedQuote') : null;
const guestPinned = ref<LocalQuote>(guestPinnedRaw ? JSON.parse(guestPinnedRaw) : null);

function pinLocal() {
    if (!quote) return;
    guestPinned.value = { message: quote.message, author: quote.author, image: quote.image ?? null };
    try { window.localStorage.setItem('guestPinnedQuote', JSON.stringify(guestPinned.value)); } catch {}
}

function unpinLocal() {
    guestPinned.value = null;
    try { window.localStorage.removeItem('guestPinnedQuote'); } catch {}
}
const natureBackgrounds = [
    'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1600&auto=format&fit=crop', // mountain valley
    'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1600&auto=format&fit=crop', // forest mist
    'https://images.unsplash.com/photo-1469474968028-56623f02e42e?q=80&w=1600&auto=format&fit=crop', // lake sunrise
    'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1600&auto=format&fit=crop', // ocean waves
    'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1600&auto=format&fit=crop', // repeat for safety
];

// Single random index per page load and pair with quote; if backend quote exists, derive index from quote text
const randomIndex = Math.floor(Math.random() * natureBackgrounds.length);
const displayQuote = computed(() => (isGuest.value && guestPinned.value) ? guestPinned.value : quote);
const isPinned = computed(() => !!(isGuest.value && guestPinned.value && displayQuote.value && guestPinned.value.message === displayQuote.value.message));
const pickIndex = computed(() => {
    if (quote?.message) {
        let h = 0;
        for (let i = 0; i < quote.message.length; i++) {
            h = (h << 5) - h + quote.message.charCodeAt(i);
            h |= 0; // Convert to 32bit integer
        }
        return Math.abs(h) % natureBackgrounds.length;
    }
    return randomIndex % natureBackgrounds.length;
});
const backgroundUrl = computed(() => {
    if (isGuest.value && guestPinned.value?.image) return guestPinned.value.image;
    if (quote?.image) return quote.image;
    return natureBackgrounds[pickIndex.value];
});

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid min-h-svh flex-col items-center justify-center bg-muted px-6 sm:px-8 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <!-- Left visual/brand panel -->
        <div class="relative hidden h-full flex-col items-center justify-center p-10 text-white lg:flex dark:border-r bg-cover bg-center bg-no-repeat" :style="{ backgroundImage: `url(${backgroundUrl})` }">
            <div class="absolute inset-0 bg-gradient-to-b from-cyan-900/60 to-slate-900/60" />
            <Link :href="home()" class="relative z-20 flex items-center text-lg font-medium">
                <AppLogoIcon class="mr-2 size-8 fill-current text-white" />
                {{ name }}
            </Link>
            <div class="relative z-20 mt-8 max-w-xl text-center">
                <div class="liquidGlass-wrapper" v-if="displayQuote">
                    <div class="liquidGlass-content">
                        <blockquote class="space-y-3">
                            <p class="text-2xl md:text-3xl font-semibold tracking-tight leading-relaxed drop-shadow">&ldquo;{{ displayQuote?.message }}&rdquo;</p>
                            <footer class="text-base text-neutral-200/90">{{ displayQuote?.author }}</footer>
                        </blockquote>
                        <div v-if="isGuest" class="mt-4 flex items-center justify-center gap-2">
                            <button
                                v-if="!isPinned"
                                type="button"
                                class="btn btn-glass-cream p-2 h-9 w-9 flex items-center justify-center"
                                @click="pinLocal"
                                aria-label="Pin quote"
                                title="Pin quote"
                            >
                                <Pin class="h-4 w-4" />
                            </button>
                            <button
                                v-else
                                type="button"
                                class="btn btn-glass-cream p-2 h-9 w-9 flex items-center justify-center"
                                @click="unpinLocal"
                                aria-label="Unpin quote"
                                title="Unpin quote"
                            >
                                <PinOff class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right auth card panel -->
        <div class="lg:p-8">
            <div class="mx-auto flex w-full max-w-md flex-col justify-center">
                <div class="mb-4 flex w-full items-center justify-end">
                    <Link v-if="!isRegister" :href="register()" class="btn btn-glass-cream">Create account</Link>
                </div>
                <Card class="rounded-xl">
                    <CardHeader class="px-10 pt-8 pb-0 text-center">
                        <CardTitle class="text-xl" v-if="title">{{ title }}</CardTitle>
                        <CardDescription v-if="description">{{ description }}</CardDescription>
                    </CardHeader>
                    <CardContent class="px-10 py-8">
                        <slot />
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
