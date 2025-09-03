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
const markAllAsRead = async () => {
    try {
        await axios.post(route('notifications.markAllRead'));
        await fetchNotifications();
    } catch (e) {
        console.error('Error marking all notifications as read:', e);
    }
};

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
    // watch(() => showNotifications.value, async (open: boolean) => {
        
    //     if (open && unreadCount.value > 0) {
    //         try {
    //             await axios.post(route('notifications.markAllRead'))
    //             await fetchNotifications()
    //         } catch (e) { 
    //             console.error('Error marking all notifications as read:', e);
    //         }
    //     }
    // })

    // Optionally, poll for new notifications every X seconds
    // setInterval(fetchNotifications, 30000); // e.g., every 30 seconds
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    // Clear any intervals if set
})
</script>
