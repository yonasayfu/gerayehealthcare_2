<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { eventBus } from '@/lib/eventBus'
import { Bell, MessageSquareText, Search, Settings, PanelLeft } from 'lucide-vue-next'
import { formatDistanceToNow } from 'date-fns'
import NavUser from '@/components/NavUser.vue'
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
import type { BreadcrumbItemType } from '@/types'

interface Props {
    breadcrumbs?: BreadcrumbItemType[]
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
})

const notifications = ref<any[]>([])
const unreadCount = ref(0)
const showNotifications = ref(false)

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

onMounted(() => {
    fetchNotifications()
    // Optionally, poll for new notifications every X seconds
    // setInterval(fetchNotifications, 30000); // e.g., every 30 seconds
})

onUnmounted(() => {
    // Clear any intervals if set
})
</script>

<template>
    <header class="bg-background/50 flex h-14 items-center gap-3 px-4 backdrop-blur-xl lg:h-[60px]">
    <header class="bg-background/50 flex h-14 items-center gap-3 px-4 backdrop-blur-xl lg:h-[60px]">
    <header class="bg-background/50 flex h-14 items-center gap-3 px-4 backdrop-blur-xl lg:h-[60px]">
        <Button variant="ghost" size="icon" class="items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50 size-9 flex md:hidden lg:flex">
            <PanelLeft />
        </Button>
            <Settings class="animate-spin" />
        </Button>
        <ThemeToggler />
        <div class="ms-auto lg:me-auto lg:flex-1">
            <div class="relative hidden max-w-sm flex-1 lg:block">
                <Search class="text-muted-foreground absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2" />
                <Input placeholder="Search..." class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex min-w-0 bg-transparent px-3 py-1 transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive h-9 w-full cursor-pointer rounded-md border pr-4 pl-10 text-sm shadow-xs" />
            </div>
        </div>
        <div class="relative">
            <Button variant="ghost" size="icon" @click="showNotifications = !showNotifications" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50 size-9 relative">
                <Bell class="animate-tada" />
                <span v-if="unreadCount > 0" class="bg-destructive absolute -end-0.5 -top-0.5 block size-2 shrink-0 rounded-full"></span>
            </Button>
            <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 overflow-hidden rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-800">
                <div class="border-b px-4 py-2 text-sm font-semibold text-gray-800 dark:border-gray-700 dark:text-gray-100">
                    Notifications ({{ unreadCount }} unread)
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
        <NavUser />
    </header>
</template>
