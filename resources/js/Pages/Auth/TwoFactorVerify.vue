<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <!-- Logo and Header -->
      <div class="text-center">
        <div class="flex justify-center mb-6">
          <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
            <img src="/images/logo.png" alt="HealthyMe Logo" class="w-12 h-12" />
          </div>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900 mb-2">
          Two-Factor Authentication
        </h1>
        <p class="text-sm text-gray-600 max-w-sm mx-auto leading-relaxed">
          Please enter the 6-digit verification code from your authenticator app to continue
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">

      <!-- Main Card -->
      <div class="bg-white py-8 px-6 shadow-sm rounded-2xl border border-gray-100 sm:px-8">
        <form @submit.prevent="submit" class="space-y-6">
          <!-- Code Input Section -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-4 text-center">
              Verification Code
            </label>
            <div class="flex justify-center space-x-2 mb-6">
              <input
                v-for="(digit, index) in otpDigits"
                :key="index"
                :ref="el => otpRefs[index] = el"
                v-model="otpDigits[index]"
                type="text"
                inputmode="numeric"
                pattern="[0-9]"
                maxlength="1"
                :class="[
                  'w-14 h-16 text-center text-2xl font-bold border-2 rounded-lg transition-all duration-200 outline-none shadow-sm',
                  otpDigits[index] 
                    ? 'border-blue-500 bg-blue-50 text-blue-900 shadow-md' 
                    : 'border-gray-300 bg-white hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:bg-blue-50'
                ]"
                @input="handleInput(index, $event)"
                @keydown="handleKeydown(index, $event)"
                @paste="handlePaste($event)"
              />
            </div>
            <div v-if="form.errors.code" class="text-sm text-red-600 bg-red-50 p-3 rounded-lg border border-red-100">
              {{ form.errors.code }}
            </div>
          </div>
          <!-- Remember Device -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember_device"
                name="remember_device"
                type="checkbox"
                v-model="form.remember_device"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors"
              />
              <label for="remember_device" class="ml-3 text-sm text-gray-700 cursor-pointer">
                Trust this device for 30 days
              </label>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="pt-2">
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm"
            >
              <div v-if="form.processing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Verifying...
              </div>
              <span v-else>Verify Code</span>
            </button>
          </div>
        </form>

        <!-- QR Code Link -->
        <div class="mt-6 text-center">
          <button 
            type="button" 
            @click="fetchQrCode" 
            class="text-sm text-gray-600 hover:text-blue-600 transition-colors duration-200 inline-flex items-center space-x-1"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
            <span>Setup new device</span>
          </button>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 text-center">
        <p class="text-xs text-gray-500">
          © 2025 HealthyMe Admin. All rights reserved.
        </p>
      </div>
    </div>

    <!-- QR Code Modal -->
    <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4" style="backdrop-filter: blur(4px);" @click="closeQrModal">
      <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full relative overflow-hidden" @click.stop>
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Setup Authenticator</h3>
            <button @click="closeQrModal" class="text-gray-400 hover:text-gray-600 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Content -->
        <div class="p-6">
          <div v-if="loadingQr" class="flex flex-col items-center justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-blue-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm text-gray-600">Loading QR code...</p>
          </div>
          
          <div v-else-if="qrError" class="text-center py-8">
            <div class="text-red-600 bg-red-50 p-4 rounded-lg border border-red-100">
              <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ qrError }}
            </div>
          </div>
          
          <div v-else class="space-y-4">
            <!-- QR Code -->
            <div class="bg-white p-4 rounded-xl border-2 border-gray-100">
              <div v-html="qrSvg" class="flex justify-center"></div>
            </div>
            
            <!-- Manual Key Section -->
            <div class="space-y-3">
              <h4 class="text-sm font-medium text-gray-900 text-center">
                Can't scan? Enter manually:
              </h4>
              <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                <div class="font-mono text-xs text-gray-800 break-all select-all text-center leading-relaxed">
                  {{ secret }}
                </div>
              </div>
            </div>
            
            <!-- Instructions -->
            <div class="text-center">
              <p class="text-sm text-gray-600 leading-relaxed">
                Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.) or enter the key manually.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch, nextTick } from 'vue';

const form = useForm({ code: '', remember_device: false });

// OTP input handling
const otpDigits = ref(['', '', '', '', '', '']);
const otpRefs = ref([]);

function submit() {
  form.post(route('twofactor.verify'));
}

// Handle input for OTP digits
function handleInput(index, event) {
  const value = event.target.value.replace(/\D/g, ''); // Only allow digits
  
  if (value.length > 1) {
    // Handle paste or multiple characters
    const digits = value.slice(0, 6).split('');
    for (let i = 0; i < digits.length && index + i < 6; i++) {
      otpDigits.value[index + i] = digits[i];
    }
    // Focus on the next empty field or the last field
    const nextIndex = Math.min(index + digits.length, 5);
    nextTick(() => {
      otpRefs.value[nextIndex]?.focus();
    });
  } else {
    otpDigits.value[index] = value;
    // Auto-focus next input
    if (value && index < 5) {
      nextTick(() => {
        otpRefs.value[index + 1]?.focus();
      });
    }
  }
  
  // Update form code
  updateFormCode();
}

// Handle keydown events
function handleKeydown(index, event) {
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    // Move to previous input on backspace if current is empty
    otpRefs.value[index - 1]?.focus();
  } else if (event.key === 'ArrowLeft' && index > 0) {
    otpRefs.value[index - 1]?.focus();
  } else if (event.key === 'ArrowRight' && index < 5) {
    otpRefs.value[index + 1]?.focus();
  }
}

// Handle paste event
function handlePaste(event) {
  event.preventDefault();
  const pastedData = event.clipboardData.getData('text').replace(/\D/g, '');
  const digits = pastedData.slice(0, 6).split('');
  
  for (let i = 0; i < 6; i++) {
    otpDigits.value[i] = digits[i] || '';
  }
  
  // Focus on the next empty field or the last field
  const nextIndex = Math.min(digits.length, 5);
  nextTick(() => {
    otpRefs.value[nextIndex]?.focus();
  });
  
  updateFormCode();
}

// Update form code from OTP digits
function updateFormCode() {
  form.code = otpDigits.value.join('');
}

// Auto-submit when 6-digit code is entered
watch(() => form.code, (newCode) => {
  // Auto-submit if we have 6 digits and form is not already processing
  if (newCode.length === 6 && !form.processing) {
    submit();
  }
});

// QR code redisplay logic
const showQrModal = ref(false);
const qrSvg = ref('');
const secret = ref('');
const loadingQr = ref(false);
const qrError = ref('');

async function fetchQrCode() {
  loadingQr.value = true;
  qrError.value = '';
  try {
    console.log('Fetching QR code...');
    const response = await fetch('/two-factor-qr');
    console.log('Response status:', response.status);
    if (!response.ok) throw new Error('Failed to fetch QR code');
    const data = await response.json();
    console.log('QR code data:', data);
    qrSvg.value = data.qrSvg;
    secret.value = data.secret;
    showQrModal.value = true;
    console.log('QR modal should now be visible');
  } catch (e) {
    console.error('QR code fetch error:', e);
    qrError.value = 'Unable to load QR code. Please try again.';
  } finally {
    loadingQr.value = false;
  }
}
function closeQrModal() {
  showQrModal.value = false;
}
</script>
