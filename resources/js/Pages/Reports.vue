<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { Chart, registerables } from 'chart.js';

// Register Chart.js components
Chart.register(...registerables);

// Define props
const props = defineProps({
    reportData: {
        type: Object,
        default: () => ({
            totalUsers: 0,
            activeUsers: 0,
            newUsersThisMonth: 0,
            averageSessionDuration: '0m 0s',
            totalMealsLogged: 0
        })
    }
});

// Use the props data
const reportData = ref(props.reportData);

// Define breadcrumb items for reports
const breadcrumbItems = [
    {
        title: 'Home',
        href: route('dashboard')
    },
    {
        title: 'Reports',
        href: null // Current page, no link
    }
];

// Chart references
const popularFoodCategoriesChart = ref(null);
const genderDistributionChart = ref(null); // Renamed from nutritionalDistributionChart
const mealPlanUsageChart = ref(null);
// Removed: appUsageChart, monthlyUserGrowthChart, userEngagementChart, userRetentionChart
const userDemographicsChart = ref(null);

// Chart instances
let chartInstances = [];

const initializeCharts = async () => {
    await nextTick();
    
    // Popular Food Categories Chart (Donut)
    if (popularFoodCategoriesChart.value) {
        const foodCategoriesCtx = popularFoodCategoriesChart.value.getContext('2d');
        // Use real food detection data from reportData
        const foodLabels = reportData.value.popularFoodCategories?.labels || [];
        const foodData = reportData.value.popularFoodCategories?.data || [];
        const foodCategoriesChartInstance = new Chart(foodCategoriesCtx, {
            type: 'doughnut',
            data: {
                labels: foodLabels,
                datasets: [{
                    data: foodData,
                    backgroundColor: [
                        '#3B82F6', // Blue
                        '#10B981', // Emerald
                        '#F59E0B', // Amber
                        '#EF4444', // Red
                        '#8B5CF6', // Violet
                        '#06B6D4', // Cyan
                        '#84CC16', // Lime
                        '#F97316', // Orange
                        '#EC4899', // Pink
                        '#6366F1', // Indigo
                        '#14B8A6', // Teal
                        '#A855F7', // Purple
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4,
                    hoverBorderColor: '#ffffff',
                    hoverBackgroundColor: [
                        '#2563EB', '#059669', '#D97706', '#DC2626', '#7C3AED', '#0891B2',
                        '#65A30D', '#EA580C', '#DB2777', '#4F46E5', '#0D9488', '#9333EA'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '40%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 25,
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000
                }
            }
        });
        chartInstances.push(foodCategoriesChartInstance);
    }

    // Gender Distribution Chart (Pie)
    if (genderDistributionChart.value) {
        const genderCtx = genderDistributionChart.value.getContext('2d');
        // Use real gender counts from reportData
        const genderCounts = reportData.value.genderCounts || { male: 0, female: 0, other: 0 };
        const genderLabels = ['Male', 'Female', 'Other'];
        const genderData = [genderCounts.male, genderCounts.female, genderCounts.other];
        const genderChartInstance = new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderData,
                    backgroundColor: [
                        '#3B82F6', // Blue for Male
                        '#EC4899', // Pink for Female
                        '#8B5CF6'  // Purple for Other
                    ],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 6,
                    hoverBorderColor: '#ffffff',
                    hoverBackgroundColor: [
                        '#2563EB', // Darker Blue
                        '#DB2777', // Darker Pink
                        '#7C3AED'  // Darker Purple
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 25,
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} users (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000
                }
            }
        });
        chartInstances.push(genderChartInstance);
    }

    // BMI Categories Chart (Pie)
    if (mealPlanUsageChart.value) {
        const bmiCtx = mealPlanUsageChart.value.getContext('2d');
        // Use real BMI category data from reportData
        const bmiCategories = reportData.value.bmiCategories || { labels: [], data: [] };
        const bmiLabels = bmiCategories.labels;
        const bmiData = bmiCategories.data;
        
        // Define specific BMI categories and their colors with modern gradients
        const bmiCategoryColors = {
            'Normal': '#10B981',        // Emerald (Healthy)
            'Overweight': '#F59E0B',    // Amber (Warning)
            'Obese': '#EF4444',         // Red (Danger)
            'Underweight': '#06B6D4',   // Cyan (Light Blue)
            'Unknown': '#8B5CF6',       // Violet
            'Severely Obese': '#DC2626', // Darker Red
            'Morbidly Obese': '#991B1B' // Very Dark Red
        };
        
        const bmiCategoryHoverColors = {
            'Normal': '#059669',        // Darker Emerald
            'Overweight': '#D97706',    // Darker Amber
            'Obese': '#DC2626',         // Darker Red
            'Underweight': '#0891B2',   // Darker Cyan
            'Unknown': '#7C3AED',       // Darker Violet
            'Severely Obese': '#B91C1C', // Even Darker Red
            'Morbidly Obese': '#7F1D1D' // Darkest Red
        };
        
        // Map colors to actual labels
        const chartColors = bmiLabels.map((label, index) => {
            return bmiCategoryColors[label] || [
                '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'
            ][index % 5];
        });
        
        const chartHoverColors = bmiLabels.map((label, index) => {
            return bmiCategoryHoverColors[label] || [
                '#2563EB', '#059669', '#D97706', '#DC2626', '#7C3AED'
            ][index % 5];
        });
        
        const bmiChartInstance = new Chart(bmiCtx, {
            type: 'doughnut',
            data: {
                labels: bmiLabels,
                datasets: [{
                    data: bmiData,
                    backgroundColor: chartColors,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4,
                    hoverBorderColor: '#ffffff',
                    hoverBackgroundColor: chartHoverColors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '30%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 25,
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} users (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000
                }
            }
        });
        chartInstances.push(bmiChartInstance);
    }

    // Removed: App Usage by Time of Day, Monthly User Growth, User Engagement, User Retention charts

    // User Demographics Chart (Bar Chart for better age visualization)
    if (userDemographicsChart.value) {
        const demographicsCtx = userDemographicsChart.value.getContext('2d');
        // Bar chart: show total users per age range (all genders combined)
        const ageLabels = ['18-24', '25-34', '35-44', '45-54', '55+'];
        const genderAgeCounts = reportData.value.genderAgeCounts || {};
        // Sum all genders for each age range
        const ageTotals = ageLabels.map(age => {
            const group = genderAgeCounts[age] || {};
            return (group.male || 0) + (group.female || 0) + (group.other || 0);
        });
        
        const backgroundColors = [
            '#3B82F6', // Blue
            '#10B981', // Emerald
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#8B5CF6'  // Violet
        ];
        
        const hoverColors = [
            '#2563EB', // Darker Blue
            '#059669', // Darker Emerald
            '#D97706', // Darker Amber
            '#DC2626', // Darker Red
            '#7C3AED'  // Darker Violet
        ];
        
        const demographicsChartInstance = new Chart(demographicsCtx, {
            type: 'bar',
            data: {
                labels: ageLabels,
                datasets: [{
                    label: 'Users by Age Group',
                    data: ageTotals,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: hoverColors,
                    hoverBorderColor: hoverColors
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `Users: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6B7280',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#F3F4F6',
                            borderColor: '#E5E7EB'
                        },
                        ticks: {
                            color: '#6B7280',
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
        chartInstances.push(demographicsChartInstance);
    }
};

const destroyCharts = () => {
    chartInstances.forEach(chart => {
        if (chart) {
            chart.destroy();
        }
    });
    chartInstances = [];
};

const printReport = () => {
    window.print();
};

onMounted(() => {
    initializeCharts();
});

// Cleanup charts when component is unmounted
onUnmounted(() => {
    destroyCharts();
});
</script>

<template>
    <Head title="Reports" />

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
                <!-- Welcome Section -->
                <div class="mb-10">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h1 class="text-2xl font-semibold mb-4">Reports & Analytics</h1>
                                    <p class="text-gray-600">Comprehensive insights and analytics for your HealthyMe platform.</p>
                                </div>
                                <button @click="printReport" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-1a2 2 0 00-2-2H9a2 2 0 00-2 2v1a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    <span>Print Report</span>
                                </button>
                            </div>
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
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ reportData.totalUsers.toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Food Nutrition List Count -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-8">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5">
                                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Nutrition Database</p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ reportData.foodNutritionListCount?.toLocaleString?.() || reportData.foodNutritionListCount || 0 }}</p>
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
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ reportData.totalMealsLogged?.toLocaleString?.() || reportData.totalMealsLogged || 0 }}</p>
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
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ reportData.averageSessionDuration }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="space-y-8">
                    <!-- First Row: Popular Food Categories and Gender Distribution -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50">
                                <h3 class="text-xl font-bold text-gray-900">Popular Food Categories</h3>
                                <p class="text-sm text-gray-600 mt-1">Most detected food categories by users</p>
                            </div>
                            <div class="p-8">
                                <div class="h-80">
                                    <canvas ref="popularFoodCategoriesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50">
                                <h3 class="text-xl font-bold text-gray-900">Gender Distribution</h3>
                                <p class="text-sm text-gray-600 mt-1">User gender demographics breakdown</p>
                            </div>
                            <div class="p-8">
                                <div class="h-80">
                                    <canvas ref="genderDistributionChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Second Row: BMI Categories and User Demographics side by side -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50">
                                <h3 class="text-xl font-bold text-gray-900">BMI Categories</h3>
                                <p class="text-sm text-gray-600 mt-1">Body Mass Index distribution among users</p>
                            </div>
                            <div class="p-8">
                                <div class="h-80">
                                    <canvas ref="mealPlanUsageChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-400 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50">
                                <h3 class="text-xl font-bold text-gray-900">User Demographics</h3>
                                <p class="text-sm text-gray-600 mt-1">Age group distribution of active users</p>
                            </div>
                            <div class="p-8">
                                <div class="h-80">
                                    <canvas ref="userDemographicsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Removed: User Engagement and User Retention row -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Hide dropdown when clicking outside */
@media (pointer: coarse) {
    .relative:focus-within .hidden {
        display: block;
    }
}
</style>