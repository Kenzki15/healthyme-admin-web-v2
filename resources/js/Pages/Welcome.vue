<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const isLoading = ref(true);

onMounted(() => {
    // Simulate loading delay for better UX and smooth animations
    setTimeout(() => {
        isLoading.value = false;
        // Allow users to see the welcome screen briefly before redirect
        setTimeout(() => {
            router.visit(route('login'));
        }, 2000);
    }, 4000);
});
</script>

<template>
    <Head title="HealthyMe Admin - Welcome" />

    <!-- Loading Screen -->
    <div v-if="isLoading" class="fixed inset-0 bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-blue-900 flex items-center justify-center z-50 transition-all duration-500">
        <div class="text-center animate-fade-in-scale">
            <!-- Logo Container with Glow Effect -->
            <div class="relative inline-flex items-center justify-center w-32 h-32 rounded-3xl mb-8 p-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm shadow-2xl animate-float">
                <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-blue-400 to-indigo-600 opacity-20 animate-pulse-glow"></div>
                <img 
                    src="/images/logo.png" 
                    alt="HealthyMe Logo" 
                    class="w-full h-full object-contain relative z-10 animate-logo-pulse"
                />
            </div>
            
            <!-- Loading Text with Typewriter Effect -->
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white animate-fade-in-up-delay-1">
                    HealthyMe Admin
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg animate-fade-in-up-delay-2">
                    Loading your dashboard...
                </p>
                <div class="flex justify-center space-x-1 mt-4 animate-fade-in-up-delay-3">
                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce-1"></div>
                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce-2"></div>
                    <div class="w-2 h-2 bg-blue-600 rounded-full animate-bounce-3"></div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-blue-900">
        <!-- Navigation -->
        <nav v-if="canLogin" class="absolute top-0 right-0 p-6 z-10">
            <div class="flex items-center space-x-4">
                <template v-if="$page.props.user">
                    <Link
                        :href="route('dashboard')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors duration-200"
                        >Dashboard</Link
                    >
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="px-4 py-2 text-sm font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200"
                        >Logout</Link
                    >
                </template>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition-colors duration-200"
                        >Sign In</Link
                    >
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-6 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-lg"
                        >Get Started</Link
                    >
                </template>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex flex-col items-center justify-center min-h-screen px-6 text-center">
            <!-- Hero Section -->
            <div class="max-w-4xl mx-auto">
                <!-- Logo Section -->
                <div class="mb-12 animate-fade-in-up">
                    <div class="relative inline-flex items-center justify-center w-40 h-40 rounded-full mb-8 p-6 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm shadow-2xl">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 opacity-10 animate-pulse"></div>
                        <img 
                            src="/images/logo.png" 
                            alt="HealthyMe Logo" 
                            class="w-full h-full object-contain relative z-10"
                        />
                    </div>
                </div>

                <!-- Welcome Content -->
                <div class="space-y-8 animate-fade-in-up">
                    <div class="space-y-4">
                        <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white leading-tight">
                            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                HealthyMe
                            </span>
                            <br>
                            <span class="text-gray-700 dark:text-gray-300">Admin Panel</span>
                        </h1>
                        <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                            Empowering health management with intelligent insights and seamless administration.
                        </p>
                    </div>

                    <!-- Feature Cards -->
                    <div class="grid md:grid-cols-3 gap-6 mt-12 max-w-4xl mx-auto">
                        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:-translate-y-1">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Analytics Dashboard</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Comprehensive insights into user health data and platform performance.</p>
                        </div>

                        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:-translate-y-1">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">User Management</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Efficiently manage user accounts, permissions, and health profiles.</p>
                        </div>

                        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:transform hover:-translate-y-1">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Security & Privacy</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Advanced security features to protect sensitive health information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Fade animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInScale {
    0% {
        opacity: 0;
        transform: translateY(40px) scale(0.8);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) 0.2s both;
}

.animate-fade-in-scale {
    animation: fadeInScale 1s cubic-bezier(0.4, 0, 0.2, 1) both;
}

.animate-fade-in-up-delay-1 {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.3s both;
}

.animate-fade-in-up-delay-2 {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.5s both;
}

.animate-fade-in-up-delay-3 {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) 0.7s both;
}

/* Enhanced spinner animations */
@keyframes spinSmooth {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin-smooth {
    animation: spinSmooth 1.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

/* Logo animations */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes logoPulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

@keyframes pulseGlow {
    0%, 100% {
        opacity: 0.2;
        transform: scale(1);
    }
    50% {
        opacity: 0.4;
        transform: scale(1.02);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

.animate-logo-pulse {
    animation: logoPulse 2s ease-in-out infinite;
}

.animate-pulse-glow {
    animation: pulseGlow 2s ease-in-out infinite;
}

/* Bounce animations for loading dots */
@keyframes bounce1 {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

@keyframes bounce2 {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

@keyframes bounce3 {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

.animate-bounce-1 {
    animation: bounce1 1.4s ease-in-out infinite;
}

.animate-bounce-2 {
    animation: bounce2 1.4s ease-in-out 0.2s infinite;
}

.animate-bounce-3 {
    animation: bounce3 1.4s ease-in-out 0.4s infinite;
}

/* Gradient background animation */
.bg-gradient-to-br {
    background-size: 400% 400%;
    animation: gradientShift 12s ease infinite;
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Custom scrollbar for better aesthetics */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Dark mode scrollbar */
@media (prefers-color-scheme: dark) {
    ::-webkit-scrollbar-track {
        background: #1e293b;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #475569;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
}
</style>
