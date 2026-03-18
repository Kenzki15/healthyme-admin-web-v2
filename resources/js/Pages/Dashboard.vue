<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';


const props = defineProps({
    user: Object,
    reportData: {
        type: Object,
        default: () => ({
            totalUsers: 0,
            activeUsers: 0,
            foodNutritionListCount: 0,
            newUsersThisMonth: 0,
            averageSessionDuration: '0m 0s',
            totalMealsLogged: 0,
            genderCounts: {
                male: 0,
                female: 0,
                other: 0
            },
            genderAgeCounts: {},
            bmiCategories: {
                labels: [],
                data: []
            },
            popularFoodCategories: {
                labels: [],
                data: []
            },
            latestFoodLogs: []
        })
    }
});

const page = usePage();

// Dashboard statistics (use real reportData)
const stats = ref({
    totalUsers: props.reportData?.totalUsers ?? 0,
    activeUsers: props.reportData?.activeUsers ?? 0,
    foodNutritionListCount: props.reportData?.foodNutritionListCount ?? 0,
    newUsersThisMonth: props.reportData?.newUsersThisMonth ?? 0,
    averageSessionDuration: props.reportData?.averageSessionDuration ?? '0m 0s',
    totalWorkouts: 5680, // Placeholder
    totalNutritionLogs: 12450 // Placeholder
});

// Chart data
const chartData = ref({
    userGrowth: [
        { month: 'Jan', users: 100 },
        { month: 'Feb', users: 150 },
        { month: 'Mar', users: 200 },
        { month: 'Apr', users: 300 },
        { month: 'May', users: 450 },
        { month: 'Jun', users: 600 },
        { month: 'Jul', users: 750 },
        { month: 'Aug', users: 900 },
        { month: 'Sep', users: 1050 },
        { month: 'Oct', users: 1200 },
        { month: 'Nov', users: 1350 },
        { month: 'Dec', users: 1500 }
    ],
    workoutStats: [
        { category: 'Cardio', count: 45 },
        { category: 'Strength', count: 35 },
        { category: 'Flexibility', count: 20 }
    ]
});

// Define breadcrumb items for dashboard
const breadcrumbItems = [
    {
        title: 'Home',
        href: route('dashboard')
    },
    {
        title: 'Dashboard',
        href: null // Current page, no link
    }
];

// Recent activity: latest food logs from server data
const recentActivity = ref(
    (props.reportData?.latestFoodLogs && Array.isArray(props.reportData.latestFoodLogs))
        ? props.reportData.latestFoodLogs.slice(0, 8)
        : []
);

function formatDateISO(iso) {
    try {
        const d = new Date(iso);
        return d.toLocaleString();
    } catch (e) {
        return iso;
    }
}
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="bg-gray-50 border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <Breadcrumb :items="breadcrumbItems" />
                </div>
            </div>
        </template>
        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Welcome Section (moved to top) -->
                <div class="mb-10">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h1 class="text-2xl font-semibold mb-4">Welcome back, {{ props.user?.name || 'Admin' }}!</h1>
                            <p class="text-gray-600">Here's what's happening with your HealthyMe platform today.</p>
                        </div>
                    </div>
                </div>
                <!-- Key Metric Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                    <!-- Total Users -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-8">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total Users</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.totalUsers.toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Food Nutrition Database -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-8">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Nutrition Database</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.foodNutritionListCount.toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Food Nutrition -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-8">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Food Detection Logs</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ props.reportData?.totalMealsLogged?.toLocaleString?.() || props.reportData?.totalMealsLogged || 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Average Session Duration -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-8">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Avg. Session</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ stats.averageSessionDuration }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 gap-8">
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Recent Food Detections</h3>
                                        <p class="text-sm text-gray-600 mt-1">Latest activity from your users</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-8">
                                <div v-if="recentActivity.length === 0" class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <h3 class="mt-4 text-sm font-medium text-gray-900">No recent activity</h3>
                                    <p class="mt-2 text-sm text-gray-500">Food detection logs will appear here once users start logging meals.</p>
                                </div>
                                <div v-else class="space-y-6">
                                    <div v-for="log in recentActivity" :key="log.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <template v-if="log.user?.profileImage && (log.user.profileImage.url || log.user.profileImage._url)">
                                                    <img :src="log.user.profileImage.url || log.user.profileImage._url" alt="Profile" class="w-12 h-12 object-cover rounded-full ring-2 ring-white shadow-sm" />
                                                </template>
                                                <template v-else>
                                                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-sm">{{ (log.userName || '').split(' ').map(n => n[0]).slice(0,2).join('') }}</div>
                                                </template>
                                            </div>
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <p class="text-base font-semibold text-gray-900">{{ log.foodName }}</p>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ log.confidence }}% confidence
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">by {{ log.userName }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">{{ formatDateISO(log.createdAt) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
