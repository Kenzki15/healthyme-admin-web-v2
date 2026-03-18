<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const LOCK_KEY = 'login_lock_until';
const ATTEMPT_KEY = 'login_failed_attempts';
const LOCK_DURATION = 5 * 60 * 1000; // 5 minutes in ms
const MAX_ATTEMPTS = 3;

const now = () => new Date().getTime();
const lockUntil = ref(Number(localStorage.getItem(LOCK_KEY)) || 0);
const failedAttempts = ref(Number(localStorage.getItem(ATTEMPT_KEY)) || 0);
const isLocked = computed(() => lockUntil.value > now());
const lockCountdown = ref(0);
let timer = null;

const updateCountdown = () => {
    if (!isLocked.value) {
        lockCountdown.value = 0;
        clearInterval(timer);
        return;
    }
    lockCountdown.value = Math.max(0, Math.floor((lockUntil.value - now()) / 1000));
    if (lockCountdown.value <= 0) {
        clearInterval(timer);
        localStorage.removeItem(LOCK_KEY);
        localStorage.removeItem(ATTEMPT_KEY);
        window.location.reload();
    }
};

onMounted(() => {
    if (isLocked.value) {
        updateCountdown();
        timer = setInterval(updateCountdown, 1000);
    }
});

const submit = () => {
    if (isLocked.value) return;
    form.clearErrors();
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
        onError: (errors) => {
            failedAttempts.value++;
            localStorage.setItem(ATTEMPT_KEY, failedAttempts.value);
            if (failedAttempts.value >= MAX_ATTEMPTS) {
                lockUntil.value = now() + LOCK_DURATION;
                localStorage.setItem(LOCK_KEY, lockUntil.value);
                updateCountdown();
                timer = setInterval(updateCountdown, 1000);
            }
            form.reset('password');
        },
        onSuccess: () => {
            localStorage.removeItem(LOCK_KEY);
            localStorage.removeItem(ATTEMPT_KEY);
            failedAttempts.value = 0;
            lockUntil.value = 0;
        }
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <Head title="Log in" />
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo and Header -->
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
                        <img src="/images/logov2.png" alt="HealthyMe Logo" class="w-12 h-12" />
                    </div>
                </div>
                <h1 class="text-2xl font-semibold text-gray-900 mb-2">
                    Welcome back
                </h1>
                <p class="text-sm text-gray-600 max-w-sm mx-auto leading-relaxed">
                    Sign in to your account to continue
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Form Card -->
            <div class="bg-white py-8 px-6 shadow-xl rounded-2xl border border-gray-200 sm:px-8">
                <!-- Status Messages -->
                <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ status }}</p>
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                <div v-if="form.hasErrors" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                Please check your credentials and try again.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="isLocked" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-center">
                    <div class="flex flex-col items-center">
                        <svg class="h-8 w-8 text-yellow-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-lg font-semibold text-yellow-800">Too many failed attempts</p>
                        <p class="text-sm text-yellow-700">Your account is locked for 5 minutes.</p>
                        <p class="text-sm text-yellow-700">Time remaining: {{ Math.floor(lockCountdown / 60) }}:{{ (lockCountdown % 60).toString().padStart(2, '0') }}</p>
                    </div>
                </div>

                <!-- Login Form -->
                <form @submit.prevent="submit" class="space-y-6" :class="{ 'pointer-events-none opacity-50': isLocked }">
                    <!-- Email/Username Input -->
                    <div>
                        <InputLabel for="email" value="Username or Email" class="block text-sm font-medium text-gray-700 mb-2" />
                        <TextInput
                            id="email"
                            type="text"
                            class="block w-full px-3 py-3 border border-gray-300 rounded-lg bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm"
                            v-model="form.email"
                            placeholder="Enter your username or email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Password Input -->
                    <div>
                        <InputLabel for="password" value="Password" class="block text-sm font-medium text-gray-700 mb-2" />
                        <div class="relative">
                            <TextInput
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                class="block w-full px-3 pr-12 py-3 border border-gray-300 rounded-lg bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm"
                                v-model="form.password"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            />
                            <button
                                type="button"
                                @click="togglePasswordVisibility"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors duration-200"
                            >
                                <!-- Eye Icon (show password) -->
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <!-- Eye Slash Icon (hide password) -->
                                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Remember Me and Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <Checkbox 
                                name="remember" 
                                v-model:checked="form.remember"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors"
                            />
                            <label for="remember" class="ml-3 text-sm text-gray-700 cursor-pointer">
                                Remember me
                            </label>
                        </div>

                        <div class="text-sm">
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-blue-600 hover:text-blue-500 transition-colors duration-200"
                            >
                                Forgot your password?
                            </Link>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing || !form.email || !form.password"
                            class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm"
                        >
                            <div v-if="form.processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Signing in...
                            </div>
                            <span v-else>Sign in</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    © 2025 HealthyMe Admin. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>
