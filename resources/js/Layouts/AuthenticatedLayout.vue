<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, router } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(false);
const sidebarMinimized = ref(false);
const sidebarHovered = ref(false);
const showingLogoutModal = ref(false);
const showingProfileModal = ref(false);

// Notification state
const notifications = ref([]);
const unreadCount = ref(0);
const bellRef = ref(null);

// Load notifications from localStorage
function loadNotifications() {
    const saved = localStorage.getItem('hm_notifications');
    if (saved) {
        const parsed = JSON.parse(saved);
        notifications.value = parsed.notifications || [];
        unreadCount.value = parsed.unreadCount || 0;
    }
}

// Save notifications to localStorage
function saveNotifications() {
    localStorage.setItem('hm_notifications', JSON.stringify({
        notifications: notifications.value,
        unreadCount: unreadCount.value
    }));
}

function addNotification(notification) {
    notifications.value.unshift(notification);
    unreadCount.value++;
    saveNotifications();
    // Bell shake animation
    if (bellRef.value) {
        bellRef.value.classList.remove('animate-shake');
        void bellRef.value.offsetWidth; // trigger reflow
        bellRef.value.classList.add('animate-shake');
    }
}

function markAllRead() {
    unreadCount.value = 0;
    saveNotifications();
}

function clearAllNotifications() {
    notifications.value = [];
    unreadCount.value = 0;
    saveNotifications();
}

function handleFoodAdded(e) {
    addNotification({
        message: e.detail.message,
        time: e.detail.time,
        type: e.detail.type || 'success',
    });
}

onMounted(() => {
    loadNotifications();
    window.addEventListener('food-added', handleFoodAdded);
});
onUnmounted(() => {
    window.removeEventListener('food-added', handleFoodAdded);
});

const closeLogoutModal = () => {
    showingLogoutModal.value = false;
};

const logout = () => {
    showingLogoutModal.value = false;
    // Perform logout using Inertia router
    router.post(route('logout'));
};

// Navigation items grouped by section
const navigationGroups = [
    {
        header: 'Main',
        items: [
            {
                name: 'Dashboard',
                href: 'dashboard',
                icon: 'M4 4h7v9H4zM14 4h6v5h-6zM14 13h6v8h-6zM4 16h7v5H4z',
                current: false
            },
        ]
    },
    {
        header: 'Management',
        items: [
            {
                name: 'Users',
                href: 'users.index',
                icon: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
                current: false
            },
            {
                name: 'Messages',
                href: 'messages.index',
                icon: 'M10 9.5L8 12l2 2.5M14 9.5l2 2.5-2 2.5M7.9 20A9 9 0 1 0 4 16.1L2 22z',
                current: false
            },
            {
                name: 'Todo List',
                href: 'todolist.index',
                icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                current: false
            },
        ]
    },
    {
        header: 'Tracking',
        items: [
            {
                name: 'Step Tracker',
                href: 'steptracker',
                icon: 'M4 16v-2.38C4 11.5 2.97 10.5 3 8c.03-2.72 1.49-6 4.5-6C9.37 2 10 3.8 10 5.5c0 3.11-2 5.66-2 8.68V16a2 2 0 1 1-4 0Z M20 20v-2.38c0-2.12 1.03-3.12 1-5.62-.03-2.72-1.49-6-4.5-6C14.63 6 14 7.8 14 9.5c0 3.11 2 5.66 2 8.68V20a2 2 0 1 0 4 0Z',
                current: false
            },
            {
                name: 'Food Nutrition',
                href: 'foodnutrition.index',
                icon: 'M12 20.94c1.5 0 2.75 1.06 4 1.06 3 0 6-8 6-12.22A4.91 4.91 0 0 0 17 5c-2.22 0-4 1.44-5 2-1-.56-2.78-2-5-2a4.9 4.9 0 0 0-5 4.78C2 14 5 22 8 22c1.25 0 2.5-1.06 4-1.06ZM10 2c1 .5 2 2 2 5',
                current: false
            },
        ]
    },
    {
        header: 'Tools',
        items: [
            {
                name: 'API Testing',
                href: 'apitesting.index',
                icon: 'M12 2a10 10 0 100 20 10 10 0 000-20zm1 14.93V17a1 1 0 11-2 0v-.07A8.001 8.001 0 014.07 13H7a1 1 0 110 2H4.07A8.001 8.001 0 0111 4.07V7a1 1 0 112 0V4.07A8.001 8.001 0 0119.93 11H17a1 1 0 110-2h2.93A8.001 8.001 0 0113 19.93z',
                current: false
            },
            {
                name: 'Reports',
                href: 'reports.index',
                icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                current: false
            },
        ]
    },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Mobile sidebar overlay -->
        <div v-show="sidebarOpen" class="fixed inset-0 flex z-40 md:hidden" role="dialog" aria-modal="true">
            <div 
                v-show="sidebarOpen" 
                class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity"
                @click="sidebarOpen = false"
            ></div>

            <!-- Mobile sidebar -->
            <div 
                v-show="sidebarOpen"
                class="relative flex-1 flex flex-col max-w-xs w-full h-screen bg-gradient-to-b from-slate-900 to-slate-800 transform transition-all ease-out duration-200 shadow-2xl"
            >
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button
                        @click="sidebarOpen = false"
                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full bg-slate-800/80 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:bg-slate-700"
                    >
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Sidebar Content -->
                <div class="flex-shrink-0 flex items-center px-4 py-4 border-b border-slate-700/30">
                    <Link :href="route('dashboard')" class="flex items-center group">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl shadow-lg group-hover:shadow-indigo-500/25 transition-all duration-200">
                            <ApplicationLogo class="block h-6 w-auto text-white" />
                        </div>
                        <div class="ml-3">
                            <h1 class="text-white font-bold text-lg tracking-tight">HealthyMe</h1>
                            <p class="text-slate-300 text-xs font-medium">Admin Portal</p>
                        </div>
                    </Link>
                </div>
                <div class="mt-2 flex-1 h-0 overflow-y-auto custom-scrollbar">
                    <nav class="px-3 py-4 space-y-2">
                        <template v-for="group in navigationGroups" :key="group.header">
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-widest px-3 py-2 select-none opacity-75">
                                {{ group.header }}
                            </div>
                            <Link
                                v-for="item in group.items"
                                :key="item.name"
                                :href="route(item.href)"
                                :class="[
                                    route().current(item.href) 
                                        ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-500/20 border-l-4 border-indigo-300' 
                                        : 'text-slate-300 hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent hover:border-slate-600',
                                    'group flex items-center px-3 py-3 text-sm font-medium rounded-r-xl transition-all duration-200 ease-out mx-1'
                                ]"
                            >
                                <div :class="[
                                    route().current(item.href) ? 'bg-white/10' : 'group-hover:bg-slate-600/20',
                                    'p-2 rounded-lg transition-all duration-200 mr-3'
                                ]">
                                    <svg 
                                        :class="[
                                            route().current(item.href) ? 'text-white' : 'text-slate-400 group-hover:text-white',
                                            'h-5 w-5 transition-colors duration-200'
                                        ]"
                                        fill="none" 
                                        viewBox="0 0 24 24" 
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                    </svg>
                                </div>
                                <span class="font-medium tracking-wide">{{ item.name }}</span>
                                
                                <!-- Active indicator -->
                                <div v-if="route().current(item.href)" class="ml-auto">
                                    <div class="w-2 h-2 bg-white rounded-full shadow-sm"></div>
                                </div>
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
            <div class="flex-shrink-0 w-14"></div>
        </div>

        <!-- Desktop sidebar -->
        <div :class="[
            'hidden md:flex md:flex-col md:fixed md:inset-y-0 md:h-screen transition-all duration-200 ease-out z-30',
            (sidebarMinimized && !sidebarHovered) ? 'md:w-16' : 'md:w-64'
        ]">
            <div 
                class="flex flex-col h-full bg-gradient-to-b from-slate-900 to-slate-800 shadow-2xl backdrop-blur-sm border-r border-slate-700/50"
                @mouseenter="sidebarHovered = true"
                @mouseleave="sidebarHovered = false"
            >
                <div :class="[
                    'flex items-center h-16 flex-shrink-0 bg-gradient-to-r from-slate-800 to-slate-700 border-b border-slate-600/30 backdrop-blur-sm',
                    (sidebarMinimized && !sidebarHovered) ? 'px-2 justify-center' : 'px-4'
                ]">
                    <Link :href="route('dashboard')" v-if="!(sidebarMinimized && !sidebarHovered)" class="flex items-center group">
                        <div class="flex items-center justify-center w-10 h-10  rounded-xl shadow-lg group-hover:shadow-indigo-500/25 transition-all duration-200">
                            <ApplicationLogo class="block h-6 w-auto text-white" />
                        </div>
                        <div class="ml-3 transition-opacity duration-200">
                            <h1 class="text-white font-bold text-lg tracking-tight">HealthyMe</h1>
                            <p class="text-slate-300 text-xs font-medium">Admin Portal</p>
                        </div>
                    </Link>
                    <Link :href="route('dashboard')" v-else class="flex items-center justify-center group">
                        <div class="flex items-center justify-center w-10 h-10 bg-indigo-600 rounded-xl shadow-lg group-hover:shadow-indigo-500/25 transition-all duration-200">
                            <ApplicationLogo class="block h-6 w-auto text-white" />
                        </div>
                    </Link>
                    <!-- Minimize button -->
                    <button
                        @click="sidebarMinimized = !sidebarMinimized"
                        :class="[
                            'p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200',
                            (sidebarMinimized && !sidebarHovered) ? 'hidden' : 'ml-auto'
                        ]"
                    >
                        <svg class="h-4 w-4 transition-transform duration-200" :class="sidebarMinimized ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 flex flex-col overflow-y-auto custom-scrollbar">
                    <nav :class="[
                        'flex-1 py-6 space-y-2',
                        (sidebarMinimized && !sidebarHovered) ? 'px-2' : 'px-4'
                    ]">
                        <template v-for="group in navigationGroups" :key="group.header">
                            <!-- Group header with animation -->
                            <div v-if="!(sidebarMinimized && !sidebarHovered)" 
                                 class="text-xs text-slate-400 font-bold uppercase tracking-widest px-3 py-3 select-none opacity-75 transition-opacity duration-200">
                                {{ group.header }}
                            </div>
                            <!-- Separator line for minimized view -->
                            <div v-else-if="group !== navigationGroups[0]" class="mx-2 h-px bg-slate-700/50 my-3"></div>
                            
                            <Link
                                v-for="item in group.items"
                                :key="item.name"
                                :href="route(item.href)"
                                :class="[
                                    route().current(item.href) 
                                        ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg shadow-indigo-500/20 border-l-4 border-indigo-300' 
                                        : 'text-slate-300 hover:bg-slate-700/50 hover:text-white border-l-4 border-transparent hover:border-slate-600',
                                    'group flex items-center text-sm font-medium rounded-r-xl transition-all duration-200 ease-out transform hover:translate-x-1',
                                    (sidebarMinimized && !sidebarHovered) ? 'mx-1 py-3 justify-center rounded-xl border-l-0' : 'mx-2 py-3 pl-3 pr-4'
                                ]"
                                :title="(sidebarMinimized && !sidebarHovered) ? item.name : ''"
                            >
                                <!-- Minimized view -->
                                <div v-if="sidebarMinimized && !sidebarHovered" class="flex items-center justify-center w-full">
                                    <div :class="[
                                        route().current(item.href) ? 'bg-white/10' : 'group-hover:bg-slate-600/30',
                                        'p-2 rounded-lg transition-all duration-200'
                                    ]">
                                        <svg
                                            :class="[
                                                route().current(item.href) ? 'text-white' : 'text-slate-400 group-hover:text-white',
                                                'h-5 w-5 transition-colors duration-200'
                                            ]"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Expanded view -->
                                <template v-else>
                                    <div :class="[
                                        route().current(item.href) ? 'bg-white/10' : 'group-hover:bg-slate-600/20',
                                        'p-2 rounded-lg transition-all duration-200 mr-3'
                                    ]">
                                        <svg 
                                            :class="[
                                                route().current(item.href) ? 'text-white' : 'text-slate-400 group-hover:text-white',
                                                'h-5 w-5 transition-colors duration-200'
                                            ]"
                                            fill="none" 
                                            viewBox="0 0 24 24" 
                                            stroke="currentColor"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                        </svg>
                                    </div>
                                    <span class="font-medium tracking-wide transition-all duration-200">{{ item.name }}</span>
                                    
                                    <!-- Active indicator -->
                                    <div v-if="route().current(item.href)" class="ml-auto">
                                        <div class="w-2 h-2 bg-white rounded-full shadow-sm"></div>
                                    </div>
                                </template>
                            </Link>
                        </template>
                    </nav>
                    
                    <!-- Expand button for minimized sidebar -->
                    <div v-if="sidebarMinimized && !sidebarHovered" class="p-2 border-t border-slate-700/30">
                        <button
                            @click="sidebarMinimized = false"
                            class="w-full p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/50 transition-all duration-200 flex items-center justify-center"
                            title="Expand sidebar"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div :class="[
            'flex flex-col transition-all duration-200 ease-out',
            (sidebarMinimized && !sidebarHovered) ? 'md:pl-16' : 'md:pl-64'
        ]">
            <!-- Top navigation -->
            <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow-sm border-b border-gray-200">
                <!-- Mobile menu button -->
                <button
                    @click="sidebarOpen = true"
                    class="px-4 border-r border-gray-200 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Top navigation content -->
                <div class="flex-1 px-4 flex justify-between sm:px-6 lg:px-8">
                    <div class="flex-1 flex items-center">
                        <!-- Page title or breadcrumb can go here -->
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- Notification bell with dropdown -->
                        <Dropdown align="right" width="56">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="relative p-2 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none transition"
                                        aria-label="View notifications"
                                        @click="markAllRead"
                                        ref="bellRef"
                                    >
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                            />
                                        </svg>
                                        <span v-if="unreadCount > 0" class="absolute -top-1 -right-1 flex items-center justify-center h-5 w-5 rounded-full bg-red-600 text-white text-xs font-bold shadow ring-2 ring-white animate-bounce">
                                            {{ unreadCount > 9 ? '9+' : unreadCount }}
                                        </span>
                                    </button>
                                </span>
                            </template>
                            <template #content>
                                <div class="py-2 px-4 min-w-[300px] bg-white rounded-xl shadow-2xl border border-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="text-base font-semibold text-gray-800 flex items-center gap-2">
                                            <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                            Notifications
                                        </div>
                                        <div class="flex gap-2">
                                            <button v-if="notifications.length > 0 && unreadCount > 0" @click.stop="markAllRead" class="text-xs text-indigo-600 hover:underline focus:outline-none">Mark all as read</button>
                                            <button v-if="notifications.length > 0" @click.stop="clearAllNotifications" class="text-xs text-red-500 hover:underline focus:outline-none">Clear all</button>
                                        </div>
                                    </div>
                                    <hr class="mb-2 -mx-4 border-gray-200">
                                    <div v-if="notifications.length === 0" class="flex flex-col items-center justify-center text-gray-400 text-xs py-8">
                                        <svg class="h-12 w-12 mb-2 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        <span class="font-medium">No new notifications</span>
                                        <span class="text-[11px] mt-1">You're all caught up!</span>
                                    </div>
                                    <div v-else class="divide-y divide-gray-100 max-h-72 overflow-y-auto custom-scrollbar">
                                        <div v-for="(notif, idx) in notifications" :key="idx" class="py-3 flex items-start space-x-3 group hover:bg-indigo-50/60 rounded-lg px-2 transition">
                                            <span class="mt-1 flex-shrink-0">
                                                <svg v-if="notif.type === 'success'" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <svg v-else-if="notif.type === 'danger'" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <svg v-else class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs text-gray-900 font-medium group-hover:text-indigo-700">{{ notif.message }}</div>
                                                <div class="text-[10px] text-gray-400 mt-1">{{ notif.time }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Dropdown>
                        <!-- User dropdown -->
                        <div class="ml-3 relative">
                            <Dropdown align="right" width="72">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="group inline-flex items-center px-4 py-2 text-sm leading-4 font-medium rounded-xl text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 ease-out shadow-sm hover:shadow-md border border-gray-200"
                                        >
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-9 w-9">
                                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center shadow-lg ring-2 ring-white group-hover:ring-indigo-100 transition-all duration-200">
                                                        <span class="text-sm font-bold text-white tracking-wide">
                                                            {{ ($page.props.user?.username || 'U').charAt(0).toUpperCase() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3 text-left min-w-0">
                                                    <div class="text-sm font-semibold text-gray-800 truncate">
                                                        {{ $page.props.user?.username || 'User' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <svg
                                                class="ml-3 -mr-1 h-4 w-4 text-gray-400 group-hover:text-gray-600 transition-colors duration-200"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <div class="py-3 px-4 bg-white rounded-xl shadow-2xl border border-gray-100 min-w-[280px]">
                                        <!-- User info header -->
                                        <div class="flex items-center px-3 py-3 mb-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-100">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center shadow-lg">
                                                    <span class="text-sm font-bold text-white">
                                                        {{ ($page.props.user?.username || 'U').charAt(0).toUpperCase() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3 min-w-0 flex-1">
                                                <div class="text-sm font-semibold text-gray-800 truncate">
                                                    {{ $page.props.user?.username || 'User' }}
                                                </div>
                                                <div class="text-xs text-gray-500 truncate">
                                                    {{ $page.props.user?.email || 'admin@healthyme.com' }}
                                                </div>
                                                <div class="text-xs text-indigo-600 font-medium mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                                                        </svg>
                                                        Administrator
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Divider -->
                                        <div class="border-t border-gray-100 mb-2"></div>
                                        
                                        <!-- Menu items -->
                                        <div class="space-y-1">
                                            <button 
                                                @click="showingProfileModal = true"
                                                class="group flex items-center w-full px-3 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-lg transition-all duration-200 ease-out"
                                            >
                                                <div class="flex items-center justify-center w-8 h-8 bg-gray-100 group-hover:bg-indigo-100 rounded-lg mr-3 transition-colors duration-200">
                                                    <svg class="h-4 w-4 text-gray-500 group-hover:text-indigo-600 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 text-left">
                                                    <div class="font-medium">View Profile</div>
                                                    <div class="text-xs text-gray-400 group-hover:text-indigo-500">Manage your account</div>
                                                </div>
                                                <svg class="h-4 w-4 text-gray-400 group-hover:text-indigo-500 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                            
                                            <button 
                                                @click="showingLogoutModal = true"
                                                class="group flex items-center w-full px-3 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 rounded-lg transition-all duration-200 ease-out"
                                            >
                                                <div class="flex items-center justify-center w-8 h-8 bg-gray-100 group-hover:bg-red-100 rounded-lg mr-3 transition-colors duration-200">
                                                    <svg class="h-4 w-4 text-gray-500 group-hover:text-red-600 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 text-left">
                                                    <div class="font-medium">Sign Out</div>
                                                    <div class="text-xs text-gray-400 group-hover:text-red-500">End your session</div>
                                                </div>
                                                <svg class="h-4 w-4 text-gray-400 group-hover:text-red-500 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Heading -->
            <header class="bg-white shadow-sm" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1">
                <!-- Breadcrumb slot -->
                <div v-if="$slots.breadcrumb">
                    <slot name="breadcrumb" />
                </div>
                
                <div class="py-6">
                    <div class="w-full px-4 sm:px-6 lg:px-12 xl:px-24 2xl:px-40 mx-auto">
                        <slot />
                    </div>
                </div>
            </main>
        </div>

        <!-- Profile Modal -->
        <Modal :show="showingProfileModal" @close="showingProfileModal = false" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 text-center mb-2">
                    Profile
                </h2>
                <div class="mb-6 flex flex-col items-center">
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center mb-2">
                        <span class="text-2xl font-bold text-indigo-600">
                            {{ ($page.props.user?.username || 'U').charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ $page.props.user?.username || 'User' }}
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        {{ $page.props.user?.email || 'No email' }}
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <span class="font-medium text-gray-700">Full Name:</span>
                        <span class="ml-2 text-gray-900">{{ $page.props.user?.name || '-' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Username:</span>
                        <span class="ml-2 text-gray-900">{{ $page.props.user?.username || '-' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Email:</span>
                        <span class="ml-2 text-gray-900">{{ $page.props.user?.email || '-' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Role:</span>
                        <span class="ml-2 text-gray-900">{{ $page.props.user?.role || '-' }}</span>
                    </div>
                </div>
                <div class="flex justify-center mt-6">
                    <SecondaryButton @click="showingProfileModal = false">
                        Close
                    </SecondaryButton>
                </div>
            </div>
        </Modal>

                 <!-- Logout Confirmation Modal -->
        <Modal :show="showingLogoutModal" @close="closeLogoutModal" max-width="md">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                
                <h2 class="text-lg font-medium text-gray-900 text-center mb-2">
                    Confirm Logout
                </h2>

                <p class="text-sm text-gray-600 text-center mb-6">
                    Are you sure you want to log out? You will need to sign in again to access your account.
                </p>

                <div class="flex justify-center space-x-3">
                    <SecondaryButton @click="closeLogoutModal">
                        Cancel
                    </SecondaryButton>
                    
                    <DangerButton @click="logout">
                        Yes, Log Out
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </div>
</template>

<style>
/* Add shake animation for bell icon */
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  50% { transform: translateX(4px); }
  75% { transform: translateX(-2px); }
}

.animate-shake {
  animation: shake 0.6s ease-in-out;
}

/* Custom scrollbar styling */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(148, 163, 184, 0.3);
  border-radius: 6px;
  transition: background 0.2s ease;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(148, 163, 184, 0.5);
}
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: rgba(148, 163, 184, 0.3) transparent;
}

/* Smooth transitions for all interactive elements */
* {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}
</style>
