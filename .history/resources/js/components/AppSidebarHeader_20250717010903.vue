<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { eventBus } from '@/lib/eventBus'
import { Bell, MessageSquareText } from 'lucide-vue-next'
import { formatDistanceToNow } from 'date-fns'
import NavUser from '@/components/NavUser.vue'
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
    <header
