<template>
  <AuthenticatedLayout>
    <template #breadcrumb>
      <div class="bg-gray-50 border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
      </div>
    </template>
    <div class="py-12 bg-gradient-to-br from-blue-50 to-gray-100 min-h-screen">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="font-semibold text-2xl text-blue-800 leading-tight mb-8 text-center tracking-tight">Image Prediction</h2>
        <div class="w-full bg-white/90 shadow-2xl rounded-2xl p-10 flex flex-col md:flex-row gap-10 border border-blue-100">
          <!-- Left: Upload -->
          <div class="md:w-1/2 w-full flex flex-col justify-center items-center">
            <h3 class="text-lg font-bold mb-4 text-blue-700">Upload an Image</h3>
            <form @submit.prevent="handleSubmit" class="space-y-6 w-full">
              <div class="flex flex-col items-center w-full">
                <label class="block text-sm font-medium text-gray-600 mb-2">Select Image</label>
                <div class="w-full flex flex-col items-center">
                  <label class="w-full cursor-pointer flex flex-col items-center justify-center border-2 border-dashed border-blue-300 rounded-xl p-6 bg-blue-50 hover:bg-blue-100 transition group min-h-[180px] relative overflow-hidden">
                    <template v-if="previewUrl">
                      <img :src="previewUrl" alt="Preview" class="h-40 w-auto rounded-xl shadow-lg border-2 border-blue-200 object-contain transition-all duration-300" />
                      <span class="absolute bottom-2 right-2 bg-white/80 text-xs text-blue-700 px-2 py-0.5 rounded shadow">Change Image</span>
                    </template>
                    <template v-else>
                      <span class="text-blue-400 group-hover:text-blue-600 text-3xl mb-2">
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-10 w-10' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12' /></svg>
                      </span>
                      <span class="text-blue-700 font-semibold">Click or drag image here</span>
                    </template>
                    <input type="file" accept="image/*" @change="handleFileChange" class="hidden" />
                  </label>
                </div>
              </div>
              <button type="submit" :disabled="loading || !selectedFile" class="w-full py-2 px-4 bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold rounded-xl shadow hover:from-blue-700 hover:to-blue-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="loading">
                  <svg class="animate-spin h-5 w-5 inline-block mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                  Predicting...
                </span>
                <span v-else>Predict</span>
              </button>
            </form>
          </div>
          <!-- Right: Result -->
          <div class="md:w-1/2 w-full flex flex-col justify-center items-center border-l md:border-l-2 border-blue-100 pl-0 md:pl-10 mt-8 md:mt-0">
            <h3 class="text-lg font-bold mb-4 text-blue-700">Prediction Result</h3>
            <transition name="fade">
              <div v-if="result" class="w-full">
                <div v-if="parsedResults && parsedResults.results" class="space-y-4">
                  <div v-for="(item, idx) in parsedResults.results" :key="idx" class="p-5 bg-blue-50 border border-blue-200 rounded-xl shadow flex flex-col items-start gap-2">
                    <div class="font-semibold text-blue-800 flex items-center gap-2">Class: <span class="bg-blue-200 text-blue-900 px-2 py-0.5 rounded text-sm">{{ item.class_name }}</span></div>
                    <div>Confidence: <span class="font-mono bg-green-100 text-green-800 px-2 py-0.5 rounded text-sm">{{ (item.confidence * 100).toFixed(2) }}%</span></div>
                    <div>BBox: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-xs">[{{ item.bbox.join(', ') }}]</span></div>
                    <div v-if="nutrients[idx]">
                      <div class="mt-2 text-blue-900 font-semibold">Nutrients:</div>
                      <ul class="ml-4 text-blue-800 text-sm">
                        <li>Calories: <span class="font-mono">{{ nutrients[idx].calories }}</span></li>
                        <li>Protein: <span class="font-mono">{{ nutrients[idx].protein }}</span></li>
                        <li>Fats: <span class="font-mono">{{ nutrients[idx].fat }}</span></li>
                        <li>Carbs: <span class="font-mono">{{ nutrients[idx].carbs }}</span></li>
                      </ul>
                    </div>
                  </div>
                  <details class="mt-4">
                    <summary class="cursor-pointer text-blue-600 underline">Show Raw JSON</summary>
                    <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto mt-2">{{ JSON.stringify(parsedResults, null, 2) }}</pre>
                  </details>
                </div>
                <div v-else class="w-full p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-center shadow">
                  <span class="font-bold">Result:</span> {{ result }}
                </div>
              </div>
              <div v-else class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl text-gray-500 text-center shadow">
                No prediction yet.
              </div>
            </transition>
            <transition name="fade">
              <div v-if="error" class="w-full mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-center shadow">
                {{ error }}
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import axios from 'axios'

const breadcrumbItems = [
  {
    title: 'Home',
    href: route('dashboard')
  },
  {
    title: 'API Testing',
    href: null // Current page
  }
]

const selectedFile = ref(null)
const previewUrl = ref('')
const result = ref('')
const error = ref('')
const loading = ref(false)
const nutrients = ref({})

function handleFileChange(e) {
  const file = e.target.files[0]
  selectedFile.value = file
  result.value = ''
  error.value = ''
  if (file) {
    previewUrl.value = URL.createObjectURL(file)
  } else {
    previewUrl.value = ''
  }
}

async function handleSubmit() {
  if (!selectedFile.value) return
  loading.value = true
  result.value = ''
  error.value = ''
  nutrients.value = {}
  try {
    const formData = new FormData()
    formData.append('image', selectedFile.value)
    // Use the provided API endpoint
    const response = await axios.post('/apitesting/predict', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    result.value = response.data.result || 'No result returned.'
    nutrients.value = response.data.nutrients || {}
  } catch (err) {
    error.value = err.response?.data?.message || 'Prediction failed. Please try again.'
  } finally {
    loading.value = false
  }
}

const parsedResults = computed(() => {
  try {
    if (!result.value) return null
    // If result is already an object, return as is
    if (typeof result.value === 'object') return result.value
    // Try to parse JSON string
    return JSON.parse(result.value)
  } catch {
    return null
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.4s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
input[type="file"]::-webkit-file-upload-button {
  background: #2563eb;
  color: white;
  border: none;
  padding: 0.5em 1em;
  border-radius: 9999px;
  cursor: pointer;
}
</style>