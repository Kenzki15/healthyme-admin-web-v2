<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-white to-blue-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo and Header -->
      <div class="text-center">
        <div class="flex justify-center mb-2">
          <img src="/images/logo.png" alt="HealthyMe Logo" class="w-20 h-20" />
        </div>
        <h2 class="mt-2 text-3xl font-bold text-gray-900">
          Set up Two-Factor Authentication
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          Secure your account with an extra layer of protection
        </p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="flex flex-col items-center">
          <div v-html="qrSvg" class="mb-4"></div>
          <p class="text-gray-700 text-center mb-2">
            Scan this QR code with your authenticator app (Google Authenticator, Authy, etc).
          </p>
          <p class="text-gray-500 text-center mb-4">
            After scanning, click next and enter the 6-digit code from your app on the next screen.
          </p>
          <button @click="goToVerify" class="mt-4 w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out transform hover:scale-[1.02] active:scale-[0.98]">
            Next: Enter Code
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center">
        <p class="text-xs text-gray-500">
          © 2025 HealthyMe Admin. All rights reserved.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
defineProps({
  qrSvg: String
});
function goToVerify() {
  router.visit('/two-factor-verify');
}
</script>
