<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import axios from 'axios'
import { eventBus } from '@/lib/eventBus'
import { Bell, MessageSquareText, Search, Settings, PanelLeft } from 'lucide-vue-next'
import GlobalSearch from '@/components/GlobalSearch.vue'; // Import GlobalSearch
import { formatDistanceToNow } from 'date-fns'

import ThemeToggler from '@/components/ThemeToggler.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb'
import { useSidebar } from '@/components/ui/sidebar' // Import useSidebar
import type { BreadcrumbItemType } from '@/types'
import { usePage } from '@inertiajs/vue3' // Import usePage

interface Props {
    breadcrumbs?: BreadcrumbItemType[]
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
})

const notifications = ref<any[]>([])
const unreadCount = ref(0)
const showNotifications = ref(false)

const page = usePage() // Initialize usePage
const { toggleSidebar } = useSidebar() // Initialize useSidebar

const isDashboardRoute = computed(() => {
    return page.url.startsWith('/admin/dashboard') // Adjust this based on your actual dashboard route
})

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('notifications.index'))
        notifications.value = response.data.notifications
        unreadCount.value = response.data.unread_count
    } catch (error) {
        console.error('Error fetching notifications:', error)
    }
}

const markAsRead = async (notificationId: string, conversationId: number | null = null) => {
    try {
        await axios.post(route('notifications.markAsRead', notificationId))
        await fetchNotifications() // Re-fetch to update count and list
        if (conversationId) {
            eventBus.emit('open-chat', conversationId) // Emit event to open chat
        }
    } catch (error) {
        console.error('Error marking notification as read:', error)
    }
}

const notificationBell = ref<HTMLElement | null>(null)
const notificationDropdown = ref<HTMLElement | null>(null)

const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as Node;
    let bellElement = notificationBell.value;

    // Attempt to get the native DOM element if notificationBell.value is a component instance
    if (bellElement && !(bellElement instanceof HTMLElement) && (bellElement as any).$el) {
        bellElement = (bellElement as any).$el;
    }

    // Only proceed if both elements are available and are HTML elements
    if (
        bellElement instanceof HTMLElement &&
        notificationDropdown.value instanceof HTMLElement &&
        !bellElement.contains(target) &&
        !notificationDropdown.value.contains(target)
    ) {
        showNotifications.value = false;
    }
};

onMounted(() => {
    fetchNotifications()
    document.addEventListener('click', handleClickOutside)

    // Reset badge when opening the dropdown
    watch(() => showNotifications.value, async (open: boolean) => {
        
        if (open && unreadCount.value > 0) {
            try {
                await axios.post(route('notifications.markAllRead'))
                await fetchNotifications(), 10000);
            } , 1000) catch (e) { 
                console.error('Error marking all notifications as read:', e);
            }
        }
    })

    // Optionally, poll for new notifications every X seconds
    // setInterval(fetchNotifications, 30000); // e.g., every 30 seconds
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    // Clear any intervals if set
})
</script>

<template>
    <header class="relative z-40 bg-background/50 flex h-14 items-center gap-3 px-4 backdrop-blur-xl lg:h-[60px]">
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <Button variant="ghost" size="icon" class="size-9 flex md:hidden lg:flex shrink-0" @click="toggleSidebar">
                <PanelLeft />
            </Button>
            <h1 v-if="isDashboardRoute" class="text-3xl font-bold hidden sm:block">Dashboard</h1>
            <Breadcrumb v-else class="hidden sm:block">
                <BreadcrumbList>
                    <template v-for="(item, index) in breadcrumbs" :key="index">
                        <BreadcrumbItem>
                            <BreadcrumbLink v-if="item.href" :href="item.href">
                                {{ item.title }}
                            </BreadcrumbLink>
                            <BreadcrumbPage v-else>
                                {{ item.title }}
                            </BreadcrumbPage>
                        </BreadcrumbItem>
                        <BreadcrumbSeparator v-if="index < breadcrumbs.length - 1" />
                    </template>
                </BreadcrumbList>
            </Breadcrumb>
            
            <!-- Global Search - Responsive positioning -->
            <div class="relative flex-1 max-w-sm ml-auto sm:ml-4 mr-2 sm:mr-0">
                <GlobalSearch />
            </div>
        </div>
        
        <div class="flex items-center gap-2 sm:gap-4 shrink-0">
            <ThemeToggler />
            <div class="relative">
                <Button
                    ref="notificationBell"
                    data-slot="dropdown-menu-trigger"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50 size-9 relative"
                    type="button"
                    aria-haspopup="menu"
                    aria-expanded="false"
                    data-state="closed"
                    @click="showNotifications = !showNotifications"
                >
                    <Bell class="animate-tada text-indigo-600" />
                    <span v-if="unreadCount > 0" class="absolute -end-2 -top-2 inline-flex items-center justify-center rounded-full bg-red-600 text-white text-[10px] px-1.5 py-0.5 min-w-[16px]">
                        {{ unreadCount }}
                    </span>
                </Button>
                <div v-if="showNotifications" ref="notificationDropdown" class="absolute right-2 mt-8 w-90 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-800 z-50">
                    <div class="border-b px-4 py- text-sm font-semibold text-gray-800 dark:border-gray-700 dark:text-gray-100 flex justify-between items-center">
                        <span>Notifications ({{ unreadCount }} unread)</span>
                        <button @click="showNotifications = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div v-if="notifications.length === 0" class="p-4 text-sm text-gray-500 dark:text-gray-400">
                        No new notifications.
                    </div>
                    <div v-else class="max-h-60 overflow-y-auto custom-scrollbar">
                        <a
                            v-for="notification in notifications"
                            :key="notification.id"
                            @click.prevent="markAsRead(notification.id, notification.data.conversation_id)"
                            class="flex cursor-pointer items-start border-b px-4 py-3 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700"
                            :class="{ 'bg-indigo-50 dark:bg-indigo-900/20': !notification.read_at }"
                        >
                            <div class="flex-shrink-0 pt-1">
                                <MessageSquareText class="h-5 w-5 text-indigo-500" />
                            </div>
                            <div class="ml-3 overflow-hidden">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">
                                    New message from {{ notification.data.sender_name }}
                                </p>
                                <p class="break-words text-xs text-gray-600 dark:text-gray-300">
                                    {{ notification.data.message_preview }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ formatDistanceToNow(new Date(notification.created_at), { addSuffix: true }) }}
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
