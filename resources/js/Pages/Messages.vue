<template>
      <Head title="Messages" />

  <AuthenticatedLayout>
    <template #breadcrumb>
      <div class="bg-gray-50 border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
      </div>
    </template>
    <div class="py-12">
      <div class="w-full">
        <div class="bg-white mr-4 overflow-hidden shadow-2xl shadow-gray-600 rounded-2xl border border-gray-200">
          <div class="p-8 bg-gradient-to-r from-white to-gray-50 border-b border-gray-100">
            <div class="flex justify-between items-center mb-6">
              <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9.5L8 12l2 2.5M14 9.5l2 2.5-2 2.5M7.9 20A9 9 0 1 0 4 16.1L2 22z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">User Feedback Messages</h3>
                  <p class="text-sm text-gray-600 mt-1">View and manage user feedback</p>
                </div>
              </div>
              <!-- Search Bar -->
              <div class="w-full max-w-md">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                  </div>
                  <input 
                    v-model="searchQuery"
                    type="text" 
                    placeholder="Search by username, email, or subject..." 
                    class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm shadow-sm hover:shadow-md transition-shadow duration-200"
                  >
                </div>
              </div>
            </div>

            <div v-if="searchQuery && filteredMessages.length !== messages.length" class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mb-4">
              <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-blue-800">
                  Showing {{ filteredMessages.length }} of {{ messages.length }} messages
                </span>
              </div>
              <button 
                @click="clearSearch"
                class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline"
              >
                Clear filter
              </button>
            </div>

            <div v-if="loading" class="text-center py-8">
              <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
              <p class="mt-2 text-gray-600">Loading messages...</p>
            </div>

            <div v-else-if="filteredMessages && filteredMessages.length > 0" class="overflow-hidden border-2 border-gray-300 rounded-xl shadow-xl bg-white">
              <div class="overflow-x-auto">
                <table class="min-w-full">
                  <thead class="bg-gradient-to-r from-slate-700 to-slate-800">
                    <tr>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                        #
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                        Avatar
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                        User Info
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                        Subject
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                        Message
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                        Date
                      </th>
                      <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                        Status
                      </th>
                      <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white">
                    <tr v-for="(msg, idx) in filteredMessages" :key="msg.objectId" 
                        class="border-b border-gray-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 hover:shadow-md">
                      <!-- Row Number -->
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="inline-flex items-center justify-center w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-bold rounded-full shadow-md">
                          {{ idx + 1 + ((pagination?.current_page - 1) * (pagination?.per_page || 10)) }}
                        </div>
                      </td>
                      
                      <!-- Avatar -->
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex-shrink-0 h-12 w-12">
                          <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg border-2 border-white overflow-hidden">
                            <template v-if="msg.user?.profileImage && (msg.user.profileImage.url || msg.user.profileImage._url)">
                              <img :src="msg.user.profileImage.url || msg.user.profileImage._url" alt="Avatar" class="h-12 w-12 object-cover rounded-full" />
                            </template>
                            <template v-else>
                              <span class="text-lg font-semibold text-white">
                                {{ (msg.user?.username || 'U').charAt(0).toUpperCase() }}
                              </span>
                            </template>
                          </div>
                        </div>
                      </td>
                      
                      <!-- User Info -->
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="min-w-0">
                          <div class="flex items-center space-x-2">
                            <p class="text-sm font-bold text-gray-900 truncate">
                              {{ msg.user?.username || 'Unknown User' }}
                            </p>
                          </div>
                          <p class="text-xs text-gray-500 truncate mt-1">
                            {{ msg.user?.email || 'No Email' }}
                          </p>
                        </div>
                      </td>
                      
                      <!-- Subject -->
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center text-sm text-indigo-700 font-medium">
                          <svg class="h-4 w-4 text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                          <span class="truncate max-w-32">{{ msg.subject || 'No Subject' }}</span>
                        </div>
                      </td>
                      
                      <!-- Message -->
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 max-w-xs">
                          <p class="line-clamp-2">{{ msg.message || msg.feedback || 'No Message' }}</p>
                        </div>
                      </td>
                      
                      <!-- Date -->
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ formatDate(msg.createdAt) }}
                        </div>
                      </td>
                      
                      <!-- Status -->
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="msg.status === 'Read' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-yellow-100 text-yellow-700 border-yellow-200'" 
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border">
                          {{ msg.status || 'Unread' }}
                        </span>
                      </td>
                      
                      <!-- Actions -->
                      <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <!-- View Button -->
                          <button @click="viewMessage(msg)" 
                                  class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                  data-tippy-content="View Message">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                          </button>
                          
                          <!-- Delete Button -->
                          <button @click="deleteMessage(msg)" 
                                  class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                  data-tippy-content="Delete Message">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9.5L8 12l2 2.5M14 9.5l2 2.5-2 2.5M7.9 20A9 9 0 1 0 4 16.1L2 22z"></path>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">
                {{ searchQuery ? 'No messages match your search' : 'No messages found' }}
              </h3>
              <p class="mt-1 text-sm text-gray-500">
                {{ searchQuery 
                  ? 'Try adjusting your search criteria or clear the search to see all messages.' 
                  : 'There are no messages to display at this time.' 
                }}
              </p>
              <div v-if="searchQuery" class="mt-4">
                <button 
                  @click="clearSearch"
                  class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Clear Search
                </button>
              </div>
            </div>
            <!-- Pagination Controls (copied from User.vue) -->
            <div v-if="pagination" class="flex justify-between items-center mt-6 px-4">
              <div class="text-sm text-gray-600">
                Showing
                <span class="font-semibold">{{ pagination.from }}</span>
                to
                <span class="font-semibold">{{ pagination.to }}</span>
                of
                <span class="font-semibold">{{ pagination.total }}</span>
                messages
              </div>
              <div class="flex items-center space-x-1">
                <button
                  class="px-3 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50"
                  :disabled="pagination.current_page === 1"
                  @click="goToPage(pagination.current_page - 1)"
                >
                  Prev
                </button>
                <template v-for="p in pagination.last_page" :key="p">
                  <button
                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700"
                    :class="{ 'bg-indigo-600 text-white': p === pagination.current_page }"
                    @click="goToPage(p)"
                  >
                    {{ p }}
                  </button>
                </template>
                <button
                  class="px-3 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50"
                  :disabled="pagination.current_page === pagination.last_page"
                  @click="goToPage(pagination.current_page + 1)"
                >
                  Next
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View Message Modal -->
    <Modal :show="showingViewModal" @close="closeViewModal" max-width="2xl">
      <div class="p-0" v-if="selectedMessage">
        <div class="px-8 py-6 bg-white rounded-xl w-full">
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-indigo-700 mb-4 flex items-center">
              <svg class="h-5 w-5 mr-2 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              Message Details
            </h3>
            <div class="space-y-3">
              <div>
                <span class="font-semibold text-gray-700">Sender:</span>
                <span class="ml-2 text-gray-900">{{ selectedMessage.user?.username || 'Unknown User' }}</span>
              </div>
              <div>
                <span class="font-semibold text-gray-700">Email:</span>
                <span class="ml-2 text-gray-900">{{ selectedMessage.user?.email || 'No email' }}</span>
              </div>
              <div>
                <span class="font-semibold text-gray-700">Subject:</span>
                <span class="ml-2 text-gray-900">{{ selectedMessage.subject || 'No subject' }}</span>
              </div>
              <div>
                <span class="font-semibold text-gray-700">Date Received:</span>
                <span class="ml-2 text-gray-900">{{ formatDate(selectedMessage.createdAt) }}</span>
              </div>
              <div>
                <span class="font-semibold text-gray-700">Status:</span>
                <span class="ml-2 text-gray-900">{{ selectedMessage.status || 'Unread' }}</span>
              </div>
              <div>
                <span class="font-semibold text-gray-700">Message:</span>
                <div class="ml-2 text-gray-900 bg-gray-50 rounded p-3 mt-1 whitespace-pre-line">{{ selectedMessage.message || selectedMessage.feedback || 'No message' }}</div>
              </div>
            </div>
          </div>
          <div class="flex justify-end space-x-3 mt-8">
            <button v-if="selectedMessage.status !== 'Read'" @click="markAsRead" :disabled="processing" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-5 py-2 rounded shadow transition-colors duration-150">
              <span v-if="processing">Marking...</span>
              <span v-else>Mark as Read</span>
            </button>
            <SecondaryButton @click="closeViewModal">
              Close
            </SecondaryButton>
          </div>
        </div>
      </div>
    </Modal>
    <!-- Delete Confirmation Modal -->
    <Modal :show="showingDeleteModal" @close="closeDeleteModal" max-width="md">
      <div class="p-6" v-if="selectedMessage">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </div>
        <h2 class="text-lg font-medium text-gray-900 text-center mb-2">
          Delete Message
        </h2>
        <p class="text-sm text-gray-600 text-center mb-6">
          Are you sure you want to delete this message from "{{ selectedMessage.user?.username || 'Unknown User' }}"? This action cannot be undone.
        </p>
        <div class="flex justify-center space-x-3">
          <SecondaryButton @click="closeDeleteModal">
            Cancel
          </SecondaryButton>
          <DangerButton @click="confirmDeleteMessage" :disabled="processing">
            <span v-if="processing">Deleting...</span>
            <span v-else>Delete Message</span>
          </DangerButton>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import axios from 'axios';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

const props = defineProps({
  messages: {
    type: Array,
    default: () => [],
  },
  pagination: Object
});

const breadcrumbItems = [
  { title: 'Home', href: route('dashboard') },
  { title: 'Messages', href: null },
];

const loading = ref(false);
const searchQuery = ref('');
const showingViewModal = ref(false);
const showingDeleteModal = ref(false);
const selectedMessage = ref(null);
const processing = ref(false);
const page = ref(props.pagination?.current_page || 1);
const perPage = ref(props.pagination?.per_page || 10);

const filteredMessages = computed(() => {
  if (!searchQuery.value) return props.messages;
  const query = searchQuery.value.toLowerCase();
  return props.messages.filter(msg => {
    return (
      (msg.user?.username && msg.user.username.toLowerCase().includes(query)) ||
      (msg.user?.email && msg.user.email.toLowerCase().includes(query)) ||
      (msg.subject && msg.subject.toLowerCase().includes(query))
    );
  });
});

// Initialize tooltips
const initTooltips = () => {
  nextTick(() => {
    tippy('[data-tippy-content]', {
      theme: 'custom',
      placement: 'top',
      arrow: true,
      animation: 'fade',
      duration: 200,
      delay: [300, 100], // [show delay, hide delay]
      allowHTML: false,
      interactive: false,
    });
  });
};

onMounted(() => {
  if (!props.messages || props.messages.length === 0) {
    fetchMessages();
  }
  initTooltips();
});

// Watch for changes in filteredMessages to reinitialize tooltips
computed(() => {
  if (filteredMessages.value) {
    nextTick(() => {
      initTooltips();
    });
  }
  return filteredMessages.value;
});

async function fetchMessages() {
  loading.value = true;
  try {
    const res = await fetch('/api/user-feedback');
    const data = await res.json();
    // If using Inertia or SSR, you may want to emit or update parent state
    // For now, just assign to props.messages if possible (not reactive)
    // This is a placeholder for actual state management
  } catch (e) {
    // handle error
  } finally {
    loading.value = false;
  }
}

function clearSearch() {
  searchQuery.value = '';
}

function formatDate(dateString) {
  if (!dateString) return 'N/A';
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  } catch {
    return 'Invalid date';
  }
}

function viewMessage(msg) {
  selectedMessage.value = msg;
  showingViewModal.value = true;
}
function closeViewModal() {
  showingViewModal.value = false;
  selectedMessage.value = null;
}
function deleteMessage(msg) {
  selectedMessage.value = msg;
  showingDeleteModal.value = true;
}
function closeDeleteModal() {
  showingDeleteModal.value = false;
  selectedMessage.value = null;
}
async function confirmDeleteMessage() {
  if (!selectedMessage.value) return;
  processing.value = true;
  
  try {
    const response = await axios.delete(route('messages.destroy', selectedMessage.value.objectId));
    
    if (response.data.success) {
      // Close the modal first
      closeDeleteModal();
      
      // Use Inertia router to refresh the current page with preserved query parameters
      router.reload({ preserveScroll: true });
    } else {
      console.error('Failed to delete message:', response.data.message);
    }
  } catch (error) {
    console.error('Error deleting message:', error);
    // Optionally show error message to user
  } finally {
    processing.value = false;
  }
}
async function markAsRead() {
  if (!selectedMessage.value || selectedMessage.value.status === 'Read') return;
  processing.value = true;
  try {
    await axios.patch(route('messages.markAsRead', selectedMessage.value.objectId));
    selectedMessage.value.status = 'Read';
    // Also update the message in the main list if needed
    const msg = props.messages.find(m => m.objectId === selectedMessage.value.objectId);
    if (msg) msg.status = 'Read';
  } catch (e) {
    // Optionally show error
  } finally {
    processing.value = false;
  }
}

function goToPage(p) {
  if (p !== page.value && p > 0 && p <= (props.pagination?.last_page || 1)) {
    page.value = p;
    // Use Inertia or router to fetch the new page
    window.location.href = `${window.location.pathname}?page=${p}&perPage=${perPage.value}`;
  }
}
</script>

<style>
/* Custom Tippy.js theme */
.tippy-box[data-theme~='custom'] {
  background-color: #1f2937;
  color: #f9fafb;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.tippy-box[data-theme~='custom'][data-placement^='top'] > .tippy-arrow::before {
  border-top-color: #1f2937;
}

.tippy-box[data-theme~='custom'][data-placement^='bottom'] > .tippy-arrow::before {
  border-bottom-color: #1f2937;
}

.tippy-box[data-theme~='custom'][data-placement^='left'] > .tippy-arrow::before {
  border-left-color: #1f2937;
}

.tippy-box[data-theme~='custom'][data-placement^='right'] > .tippy-arrow::before {
  border-right-color: #1f2937;
}

/* Enhanced scrollbar styling */
.overflow-x-auto::-webkit-scrollbar {
  height: 10px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f8fafc;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: linear-gradient(to right, #cbd5e1, #94a3b8);
  border-radius: 6px;
  border: 1px solid #94a3b8;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to right, #94a3b8, #64748b);
}

/* Enhanced table styling */
table {
  border-collapse: separate;
  border-spacing: 0;
}

/* Add subtle box shadow to table rows on hover */
tbody tr:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transform: translateY(-1px);
}

/* Smooth transitions for better UX */
tbody tr {
  transition: all 0.2s ease-in-out;
}

/* Enhanced badge styling */
.inline-flex.items-center.px-2\.5.py-0\.5 {
  font-weight: 600;
  letter-spacing: 0.025em;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Professional number badge */
.inline-flex.items-center.justify-center.w-8.h-8 {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

/* Enhanced hover effects */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}

/* Line clamp utility */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Gradient border effect for table */
.table-gradient-border {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 1px;
  border-radius: 12px;
}

.table-gradient-border > div {
  background: white;
  border-radius: 11px;
}
</style>
