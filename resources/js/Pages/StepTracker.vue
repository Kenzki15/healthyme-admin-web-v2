<template>
  <Head title="Step Tracker" />
  <AuthenticatedLayout>
    <template #header>
      <h1 class="text-2xl font-bold" tabindex="0">Step Tracker</h1>
    </template>
    <div class="py-12">
      <div class="w-full">
        <div class="bg-white mr-4 overflow-hidden shadow-2xl shadow-gray-600 rounded-2xl border border-gray-200">
          <div class="p-8 bg-gradient-to-r from-white to-gray-50 border-b border-gray-100">
            <div class="flex justify-between items-center mb-6">
              <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-100 rounded-lg">
                  <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v-2.38C4 11.5 2.97 10.5 3 8c.03-2.72 1.49-6 4.5-6C9.37 2 10 3.8 10 5.5c0 3.11-2 5.66-2 8.68V16a2 2 0 1 1-4 0Z M20 20v-2.38c0-2.12 1.03-3.12 1-5.62-.03-2.72-1.49-6-4.5-6C14.63 6 14 7.8 14 9.5c0 3.11 2 5.66 2 8.68V20a2 2 0 1 0 4 0Z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-xl font-bold text-gray-900">User Step Tracking</h3>
                  <p class="text-sm text-gray-600 mt-1">Monitor and filter user step progress</p>
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
                    v-model="search"
                    type="text"
                    placeholder="Search by name, email, or username..."
                    class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm shadow-sm hover:shadow-md transition-shadow duration-200"
                  >
                </div>
              </div>
            </div>
            <div v-if="search && filteredUsers.length !== users.length" class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mt-4">
              <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-blue-800">
                  Showing {{ filteredUsers.length }} of {{ users.length }} users
                </span>
              </div>
              <button @click="clearSearch" class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline">Clear filter</button>
            </div>
          </div>
          <div v-if="filteredUsers.length > 0" class="overflow-hidden border-2 border-gray-300 rounded-xl shadow-xl bg-white">
            <div class="overflow-x-auto">
              <table class="min-w-full">
                <thead class="bg-gradient-to-r from-slate-700 to-slate-800">
                  <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">#</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">Avatar</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">User Info</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">Daily Goal</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">Steps Today</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">Distance</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">Weekly Avg</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">Calories</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">Goal Status</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white">
                  <tr v-for="(user, idx) in filteredUsers" :key="user.email" 
                      class="border-b border-gray-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 hover:shadow-md">
                    <!-- Row Number -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="inline-flex items-center justify-center w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-bold rounded-full shadow-md">
                        {{ ((props.pagination?.current_page || 1) - 1) * (props.pagination?.per_page || 10) + idx + 1 }}
                      </div>
                    </td>
                    
                    <!-- Avatar -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex-shrink-0 h-12 w-12">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg border-2 border-white overflow-hidden">
                          <template v-if="user.profileImage && (user.profileImage.url || user.profileImage._url)">
                            <img :src="user.profileImage.url || user.profileImage._url" alt="Avatar" class="h-12 w-12 object-cover rounded-full" />
                          </template>
                          <template v-else>
                            <span class="text-lg font-semibold text-white">
                              {{ (user.name || 'U').charAt(0).toUpperCase() }}
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
                            {{ user.name || 'No name' }}
                          </p>
                        </div>
                        <p class="text-xs text-gray-500 truncate mt-1">
                          {{ user.email || 'No email' }}
                        </p>
                      </div>
                    </td>
                    
                    <!-- Daily Goal -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center text-sm text-gray-900">
                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>{{ (user.dailyGoal || 10000).toLocaleString() }} steps</span>
                      </div>
                    </td>
                    
                    <!-- Steps Today -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900">{{ (user.stepsToday || 0).toLocaleString() }} steps</div>
                        <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                          <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full" :style="{ width: Math.min(100, user.progress || 0) + '%' }"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">{{ user.progress || 0 }}% complete</div>
                      </div>
                    </td>
                    
                    <!-- Distance -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center text-sm text-gray-900">
                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ (user.kilometers || 0).toFixed(2) }} km</span>
                      </div>
                    </td>
                    
                    <!-- Weekly Average -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ (user.weeklyAvg || 0).toLocaleString() }} steps
                      </div>
                    </td>
                    
                    <!-- Calories -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center text-sm text-gray-900">
                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path>
                        </svg>
                        <span>{{ user.calories || 0 }} kcal</span>
                      </div>
                    </td>
                    
                    <!-- Goal Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span v-if="user.goalStatus === 'completed'" 
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Completed
                      </span>
                      <span v-else 
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        In Progress
                      </span>
                    </td>
                    
                    <!-- Actions -->
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                      <div class="flex items-center justify-center space-x-2">
                        <!-- View Details Button -->
                        <button @click="openModal(user)" 
                                class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                title="View Step Details">
                          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v-2.38C4 11.5 2.97 10.5 3 8c.03-2.72 1.49-6 4.5-6C9.37 2 10 3.8 10 5.5c0 3.11-2 5.66-2 8.68V16a2 2 0 1 1-4 0Z M20 20v-2.38c0-2.12 1.03-3.12 1-5.62-.03-2.72-1.49-6-4.5-6C14.63 6 14 7.8 14 9.5c0 3.11 2 5.66 2 8.68V20a2 2 0 1 0 4 0Z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">
              {{ search ? 'No users match your search' : 'No users found' }}
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ search 
                ? 'Try adjusting your search criteria or clear the search to see all users.' 
                : 'There are no users to display at this time.' 
              }}
            </p>
            <div v-if="search" class="mt-4">
              <button 
                @click="clearSearch"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Clear Search
              </button>
            </div>
          </div>
          <!-- Pagination Controls -->
          <div v-if="props.pagination" class="flex justify-between items-center mt-6 mb-6 px-4">
            <div class="text-sm text-gray-600">
              Showing
              <span class="font-semibold">{{ props.pagination.from }}</span>
              to
              <span class="font-semibold">{{ props.pagination.to }}</span>
              of
              <span class="font-semibold">{{ props.pagination.total }}</span>
              records
            </div>
            <div class="flex items-center space-x-1">
              <button
                class="px-3 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50"
                :disabled="props.pagination.current_page === 1"
                @click="goToPage(props.pagination.current_page - 1)"
              >
                Prev
              </button>
              <!-- Show first page -->
              <button
                v-if="props.pagination.current_page > 3"
                class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700"
                @click="goToPage(1)"
              >
                1
              </button>
              <span v-if="props.pagination.current_page > 4" class="px-2 text-gray-500">...</span>
              
              <!-- Show pages around current page -->
              <template v-for="p in getVisiblePages()" :key="p">
                <button
                  class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700"
                  :class="{ 'bg-indigo-600 text-white border-indigo-600': p === props.pagination.current_page }"
                  @click="goToPage(p)"
                >
                  {{ p }}
                </button>
              </template>
              
              <!-- Show last page -->
              <span v-if="props.pagination.current_page < props.pagination.last_page - 3" class="px-2 text-gray-500">...</span>
              <button
                v-if="props.pagination.current_page < props.pagination.last_page - 2"
                class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700"
                @click="goToPage(props.pagination.last_page)"
              >
                {{ props.pagination.last_page }}
              </button>
              <button
                class="px-3 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50"
                :disabled="props.pagination.current_page === props.pagination.last_page"
                @click="goToPage(props.pagination.current_page + 1)"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- User Step Data Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
      <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full p-0 relative max-h-[90vh] overflow-hidden">
        <button class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 z-10" @click="closeModal" aria-label="Close">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        <div v-if="selectedUser">
          <!-- Modal Header -->
          <div class="px-6 pt-6 pb-2 border-b border-gray-200">
            <div class="flex flex-col gap-3">
              <div class="flex flex-col gap-1">
                <span class="font-semibold text-base">User Step Data:</span>
                <div class="flex flex-wrap gap-2 items-center text-xs text-gray-600">
                  <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ selectedUser.id || 'N/A' }}</span>
                  <span class="text-gray-400">|</span>
                  <span>{{ selectedUser.email || 'No email' }}</span>
                </div>
              </div>
              <!-- BMI Information -->
              <div class="flex flex-col gap-1" v-if="selectedUser.bmiValue || selectedUser.height || selectedUser.weight">
                <span class="font-semibold text-sm text-gray-700">BMI Information:</span>
                <div class="flex flex-wrap gap-3 items-center text-xs">
                  <div v-if="selectedUser.height" class="flex items-center gap-1">
                    <span class="text-gray-500">Height:</span>
                    <span class="font-medium text-gray-700">{{ selectedUser.height }} cm</span>
                  </div>
                  <div v-if="selectedUser.weight" class="flex items-center gap-1">
                    <span class="text-gray-500">Weight:</span>
                    <span class="font-medium text-gray-700">{{ selectedUser.weight }} kg</span>
                  </div>
                  <div v-if="selectedUser.bmiValue" class="flex items-center gap-1">
                    <span class="text-gray-500">BMI:</span>
                    <span class="font-medium text-gray-700">{{ selectedUser.bmiValue }}</span>
                  </div>
                  <div v-if="selectedUser.bmiCategory" class="flex items-center">
                    <span 
                      :class="{
                        'bg-green-100 text-green-700': selectedUser.bmiCategory === 'Normal',
                        'bg-yellow-100 text-yellow-700': selectedUser.bmiCategory === 'Overweight', 
                        'bg-red-100 text-red-700': selectedUser.bmiCategory === 'Obese',
                        'bg-blue-100 text-blue-700': selectedUser.bmiCategory === 'Underweight',
                        'bg-gray-100 text-gray-700': !['Normal','Overweight','Obese','Underweight'].includes(selectedUser.bmiCategory)
                      }" 
                      class="px-2 py-0.5 rounded text-xs font-semibold"
                    >
                      {{ selectedUser.bmiCategory }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Tabs Navigation -->
          <div class="px-6 pt-4">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
              <button 
                @click="activeTab = 'overview'"
                :class="[
                  'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                  activeTab === 'overview' 
                    ? 'bg-white text-indigo-600 shadow-sm' 
                    : 'text-gray-500 hover:text-gray-700'
                ]"
              >
                Overview
              </button>
              <button 
                @click="activeTab = 'daily'"
                :class="[
                  'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                  activeTab === 'daily' 
                    ? 'bg-white text-indigo-600 shadow-sm' 
                    : 'text-gray-500 hover:text-gray-700'
                ]"
              >
                Daily
              </button>
              <button 
                @click="activeTab = 'weekly'"
                :class="[
                  'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                  activeTab === 'weekly' 
                    ? 'bg-white text-indigo-600 shadow-sm' 
                    : 'text-gray-500 hover:text-gray-700'
                ]"
              >
                Weekly
              </button>
              <button 
                @click="activeTab = 'monthly'"
                :class="[
                  'flex-1 px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                  activeTab === 'monthly' 
                    ? 'bg-white text-indigo-600 shadow-sm' 
                    : 'text-gray-500 hover:text-gray-700'
                ]"
              >
                Monthly
              </button>
            </div>
          </div>

          <!-- Tab Content -->
          <div class="px-6 pb-4 pt-4 overflow-y-auto max-h-[60vh]">
            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'">
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="font-semibold text-sm mb-2">Activity Analysis</div>
                <div class="text-xs text-gray-700 space-y-1">
                  <div>Current Steps: <span class="font-semibold">{{ selectedUser.stepsToday?.toLocaleString() || '0' }} steps</span></div>
                  <div>Daily Goal: <span class="font-semibold">{{ selectedUser.dailyGoal?.toLocaleString() || '10,000' }} steps</span></div>
                  <div>Distance Covered: <span class="font-semibold">{{ selectedUser.kilometers?.toFixed(2) || '0.00' }} km</span></div>
                  <div>Calories Burned: <span class="font-semibold">{{ selectedUser.calories || '0' }} kcal</span></div>
                  <div>Goal Progress: <span :class="selectedUser.progress >= 100 ? 'text-green-600' : 'text-yellow-600'" class="font-semibold">{{ selectedUser.progress }}% completed</span></div>
                  <div>Goal Status: 
                    <span v-if="selectedUser.goalStatus === 'completed'" class="text-green-600 font-semibold">✓ Goal Achieved!</span>
                    <span v-else class="text-orange-600 font-semibold">In Progress</span>
                  </div>
                </div>
                <div class="mt-2 text-xs" v-if="selectedUser.progress < 100">
                  <span class="block bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 px-2 py-1 rounded">
                    <span class="font-semibold">Recommendation:</span> 
                    {{ Math.ceil((selectedUser.dailyGoal - selectedUser.stepsToday) / 1000) }}k more steps needed to reach daily goal.
                  </span>
                </div>
                <div class="mt-2 text-xs" v-else>
                  <span class="block bg-green-50 border-l-4 border-green-400 text-green-800 px-2 py-1 rounded">
                    <span class="font-semibold">Congratulations!</span> Daily step goal achieved!
                  </span>
                </div>
              </div>
            </div>

            <!-- Daily Tab -->
            <div v-if="activeTab === 'daily'">
              <div class="space-y-4">
                <!-- Daily Progress -->
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-lg p-4">
                  <div class="font-semibold text-sm mb-3 text-indigo-800">Today's Progress</div>
                  <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Steps</div>
                      <div class="text-lg font-bold text-gray-900">{{ selectedUser.stepsToday?.toLocaleString() || '0' }}</div>
                      <div class="text-xs text-gray-500">of {{ selectedUser.dailyGoal?.toLocaleString() || '10,000' }} goal</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Distance</div>
                      <div class="text-lg font-bold text-gray-900">{{ selectedUser.kilometers?.toFixed(2) || '0.00' }} km</div>
                      <div class="text-xs text-gray-500">Today</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Calories</div>
                      <div class="text-lg font-bold text-gray-900">{{ selectedUser.calories || '0' }}</div>
                      <div class="text-xs text-gray-500">kcal burned</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Progress</div>
                      <div class="text-lg font-bold text-gray-900">{{ selectedUser.progress || 0 }}%</div>
                      <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" :style="{ width: (selectedUser.progress || 0) + '%' }"></div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Real Data Display -->
                <div class="bg-white border border-gray-200 rounded-lg p-4" v-if="selectedUser.originalData">
                  <div class="font-semibold text-sm mb-3">Raw Data</div>
                  <div class="text-xs text-gray-600 space-y-1">
                    <div v-if="selectedUser.originalData.createdAt">
                      <span class="font-medium">Last Updated:</span> {{ new Date(selectedUser.originalData.createdAt).toLocaleString() }}
                    </div>
                    <div v-if="selectedUser.originalData.updatedAt">
                      <span class="font-medium">Data Modified:</span> {{ new Date(selectedUser.originalData.updatedAt).toLocaleString() }}
                    </div>
                    <div v-if="selectedUser.originalData.date">
                      <span class="font-medium">Date:</span> {{ selectedUser.originalData.date }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Weekly Tab -->
            <div v-if="activeTab === 'weekly'">
              <div class="space-y-4">
                <!-- Weekly Overview -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                  <div class="font-semibold text-sm mb-3 text-green-800">Weekly Summary</div>
                  <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Total Steps</div>
                      <div class="text-lg font-bold text-gray-900">{{ selectedUser.totalWeeklySteps?.toLocaleString() || '0' }}</div>
                      <div class="text-xs text-gray-500">this week</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Daily Average</div>
                      <div class="text-lg font-bold text-gray-900">{{ Math.round((selectedUser.totalWeeklySteps || 0) / 7).toLocaleString() }}</div>
                      <div class="text-xs text-gray-500">steps/day</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Goals Completed</div>
                      <div class="text-lg font-bold text-gray-900">{{ selectedUser.weeklyGoalsCompleted || 0 }}/7</div>
                      <div class="text-xs text-gray-500">days this week</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Current Steps</div>
                      <div class="text-lg font-bold text-gray-900">{{ selectedUser.stepsToday?.toLocaleString() || '0' }}</div>
                      <div class="text-xs text-gray-500">today</div>
                    </div>
                  </div>
                </div>

                <!-- Weekly Breakdown - Real Data -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                  <div class="font-semibold text-sm mb-3 text-gray-700">7-Day Breakdown (Monday - Sunday)</div>
                  <div v-if="reorderedWeeklyHistory && reorderedWeeklyHistory.length > 0" class="space-y-3">
                    <div v-for="day in reorderedWeeklyHistory" :key="day.date || day.dayShort" class="flex items-center justify-between">
                      <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700 w-8" :class="day.isToday ? 'text-indigo-600 font-bold' : ''">
                          {{ day.dayShort }}
                        </span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2 w-32">
                          <div 
                            class="h-2 rounded-full transition-all duration-300"
                            :class="[
                              day.goalCompleted ? 'bg-green-500' : 
                              day.steps > 0 ? 'bg-yellow-500' : 'bg-gray-300',
                              day.isToday ? 'ring-2 ring-indigo-300' : ''
                            ]"
                            :style="{ width: day.progress + '%' }"
                          ></div>
                        </div>
                        <div v-if="day.goalCompleted" class="text-green-600 text-xs">
                          ✓
                        </div>
                      </div>
                      <div class="text-right">
                        <div class="text-sm font-medium" :class="[
                          day.isToday ? 'text-indigo-600 font-bold' : 'text-gray-900',
                          !day.hasData ? 'text-gray-400' : ''
                        ]">
                          {{ day.hasData ? day.steps.toLocaleString() : 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500">steps</div>
                      </div>
                    </div>
                  </div>
                  <div v-else class="text-center py-4 text-gray-500 text-sm">
                    No historical data available for this user
                  </div>
                  <div class="mt-3 text-xs text-gray-500 text-center">
                    {{ new Date().toLocaleDateString('en-US', { weekday: 'long' }) }} is highlighted as today
                  </div>
                </div>

                <!-- Weekly Trend Analysis -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4" v-if="reorderedWeeklyHistory && reorderedWeeklyHistory.length > 0">
                  <div class="font-semibold text-sm mb-3 text-blue-800">Weekly Trend Analysis</div>
                  <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                      <div class="font-medium text-blue-700 mb-1">Activity Level:</div>
                      <div :class="getActivityLevelClass(selectedUser.weeklyGoalsCompleted)">
                        {{ getActivityLevel(selectedUser.weeklyGoalsCompleted) }}
                      </div>
                    </div>
                    <div>
                      <div class="font-medium text-blue-700 mb-1">Weekly Progress:</div>
                      <div class="text-blue-600">
                        {{ Math.round((selectedUser.weeklyGoalsCompleted / 7) * 100) }}% goal completion
                      </div>
                    </div>
                    <div>
                      <div class="font-medium text-blue-700 mb-1">Best Day:</div>
                      <div class="text-blue-600">
                        {{ getBestDay(reorderedWeeklyHistory) }}
                      </div>
                    </div>
                    <div>
                      <div class="font-medium text-blue-700 mb-1">Improvement:</div>
                      <div :class="getTrendClass(reorderedWeeklyHistory)">
                        {{ getTrend(reorderedWeeklyHistory) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Monthly Tab -->
            <div v-if="activeTab === 'monthly'">
              <div class="space-y-4">
                <!-- Monthly Overview -->
                <div class="bg-gradient-to-r from-purple-50 to-violet-50 border border-purple-200 rounded-lg p-4">
                  <div class="font-semibold text-sm mb-3 text-purple-800">
                    {{ currentMonthName }} {{ currentYear }} Summary
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Current Steps</div>
                      <div class="text-lg font-bold text-gray-900">{{ selectedUser.stepsToday?.toLocaleString() || '0' }}</div>
                      <div class="text-xs text-gray-500">{{ isCurrentMonth ? 'today' : 'latest data' }}</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Daily Goal</div>
                      <div class="text-lg font-bold text-gray-900">{{ selectedUser.dailyGoal?.toLocaleString() || '10,000' }}</div>
                      <div class="text-xs text-gray-500">steps/day</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Month Progress</div>
                      <div class="text-lg font-bold text-gray-900">{{ getCurrentMonthProgress() }}%</div>
                      <div class="text-xs text-gray-500">days with goals met</div>
                    </div>
                    <div class="bg-white rounded-lg p-3 shadow-sm">
                      <div class="text-xs text-gray-500 mb-1">Estimated Total</div>
                      <div class="text-lg font-bold text-gray-900">{{ getEstimatedMonthlySteps().toLocaleString() }}</div>
                      <div class="text-xs text-gray-500">steps this month</div>
                    </div>
                  </div>
                </div>
                

                <!-- Monthly Calendar View -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                  <div class="flex items-center justify-between mb-4">
                    <div class="font-semibold text-sm">Daily Activity Calendar</div>
                    <div class="flex items-center space-x-2">
                      <button 
                        @click="previousMonth" 
                        class="p-1 rounded hover:bg-gray-100 text-gray-600 hover:text-gray-800"
                        :disabled="isCurrentMonth && currentMonthOffset <= -12"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                      </button>
                      <div class="text-sm font-medium text-gray-700 min-w-[120px] text-center">
                        {{ currentMonthName }} {{ currentYear }}
                      </div>
                      <button 
                        @click="nextMonth" 
                        class="p-1 rounded hover:bg-gray-100 text-gray-600 hover:text-gray-800"
                        :disabled="isCurrentMonth && currentMonthOffset >= 0"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-7 gap-1 text-xs">
                    <!-- Day headers -->
                    <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="text-center text-gray-500 font-medium py-2">
                      {{ day }}
                    </div>
                    
                    <!-- Empty cells for days before month starts -->
                    <div v-for="n in firstDayOfMonth" :key="'empty-' + n" class="aspect-square"></div>
                    
                    <!-- Calendar days -->
                    <div v-for="dayNum in daysInCurrentMonth" :key="dayNum" class="aspect-square">
                      <div 
                        class="w-full h-full rounded-sm flex items-center justify-center text-xs font-medium transition-colors duration-200 border border-gray-100 cursor-pointer hover:shadow-md"
                        :class="getDayClass(dayNum)"
                        :title="getDayTitle(dayNum)"
                        @click="selectDay(dayNum)"
                      >
                        {{ dayNum }}
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
                    <span>No activity</span>
                    <div class="flex space-x-1 items-center">
                      <div class="w-3 h-3 bg-gray-100 rounded-sm border"></div>
                      <span class="mx-1">Less</span>
                      <div class="w-3 h-3 bg-gray-200 rounded-sm"></div>
                      <div class="w-3 h-3 bg-indigo-300 rounded-sm"></div>
                      <div class="w-3 h-3 bg-indigo-500 rounded-sm"></div>
                      <span class="mx-1">More</span>
                    </div>
                    <span>High activity</span>
                  </div>
                  
                  <!-- Selected Day Details -->
                  <div v-if="selectedDay" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="font-semibold text-sm text-blue-800 mb-2">
                      {{ selectedDayFormatted }}
                    </div>
                    <div class="text-xs text-blue-700 space-y-1">
                      <div>Steps: <span class="font-semibold">{{ getStepsForDay(selectedDay).toLocaleString() }} steps</span></div>
                      <div>Goal: <span class="font-semibold">{{ selectedUser.dailyGoal?.toLocaleString() || '10,000' }} steps</span></div>
                      <div>Progress: <span class="font-semibold">{{ getProgressForDay(selectedDay) }}%</span></div>
                      <div>Distance: <span class="font-semibold">{{ getDistanceForDay(selectedDay) }} km</span></div>
                      <div>Calories: <span class="font-semibold">{{ getCaloriesForDay(selectedDay) }} kcal</span></div>
                      <div v-if="hasRealDataForDay(selectedDay)" class="text-indigo-600 font-semibold text-xs">📊 Real data available</div>
                      <div v-else class="text-gray-500 font-semibold text-xs">📋 No tracking data for this date</div>
                      <div v-if="isGoalCompletedForDay(selectedDay)" class="text-green-600 font-semibold">✓ Goal Achieved!</div>
                      <div v-else-if="getStepsForDay(selectedDay) > 0" class="text-yellow-600 font-semibold">Goal in progress</div>
                      <div v-else class="text-gray-600">No activity recorded</div>
                    </div>
                  </div>
                  
                  <div class="mt-2 text-xs text-gray-600 text-center">
                    <span class="font-medium">Click on a day</span> to view detailed step information for that date
                  </div>
                </div>

                <!-- User Information -->
                <div class="bg-white border border-gray-200 rounded-lg p-4" v-if="selectedUser.originalData">
                  <div class="font-semibold text-sm mb-3">User Information</div>
                  <div class="text-xs text-gray-600 space-y-1">
                    <div v-if="selectedUser.originalData.objectId">
                      <span class="font-medium">Object ID:</span> {{ selectedUser.originalData.objectId }}
                    </div>
                    <div v-if="selectedUser.originalData.createdAt">
                      <span class="font-medium">Account Created:</span> {{ new Date(selectedUser.originalData.createdAt).toLocaleDateString() }}
                    </div>
                    <div v-if="selectedUser.originalData.user?.objectId">
                      <span class="font-medium">User ID:</span> {{ selectedUser.originalData.user.objectId }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="flex justify-end gap-2 px-6 pb-6 border-t border-gray-200 pt-4">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700" @click="closeModal">Close</button>
            <button class="bg-white border border-indigo-600 text-indigo-600 px-4 py-2 rounded hover:bg-indigo-50 font-medium" @click="exportData">Export Data</button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const search = ref('');
const activityLevel = ref('');
const goalStatus = ref('');
const dateRange = ref('today');
const showModal = ref(false);
const selectedUser = ref(null);
const activeTab = ref('overview');
const currentMonthOffset = ref(0);
const selectedDay = ref(null);

const props = defineProps({
  stepTrackerData: Array,
  pagination: Object
});

// Map real stepTrackerData to users array for table display
const users = computed(() => {
  return (props.stepTrackerData || []).map(item => {
    const username = (item.user && item.user.username) || item.username || item.name || 'Unknown';
    const email = (item.user && item.user.email) || item.email || '';
    const stepsToday = item.stepsToday || item.steps || item.stepCount || 0;
    const dailyGoal = item.dailyGoal || item.goal || item.dailyStepGoal || 10000;
    const kilometers = item.kilometers || item.distance || (stepsToday * 0.0008) || 0; // approximate conversion
    const calories = item.calories || item.caloriesBurned || Math.round(stepsToday * 0.04) || 0; // approximate conversion

    return {
      id: (item.user && item.user.objectId) || item.objectId || item.id,
      name: username,
      email,
      dailyGoal,
      stepsToday,
      kilometers,
      weeklyAvg: item.weeklyAverage || item.weeklyAvg || item.averageSteps || stepsToday,
      calories,
      goalStatus: stepsToday >= dailyGoal ? 'completed' : 'in_progress',
      progress: dailyGoal ? Math.min(100, Math.round((stepsToday / dailyGoal) * 100)) : 0,
      profileImage: (item.user && item.user.profileImage) || item.profileImage || null,
      // BMI data
      bmiValue: item.bmiValue || null,
      bmiCategory: item.bmiCategory || null,
      height: item.height || null,
      weight: item.weight || null,
      age: item.age || null,
      gender: item.gender || null,
      // Weekly history data
      weeklyHistory: item.weeklyHistory || [],
      weeklyGoalsCompleted: item.weeklyGoalsCompleted || 0,
      totalWeeklySteps: item.totalWeeklySteps || 0,
      // Store original data for detailed view
      originalData: item
    };
  });
});

const filteredUsers = computed(() => {
  let result = users.value;
  if (search.value) {
    const s = search.value.toLowerCase();
    result = result.filter(u =>
      u.name.toLowerCase().includes(s) ||
      u.email.toLowerCase().includes(s)
    );
  }
  return result;
});

// Computed property to reorder weekly history from Monday to Sunday
const reorderedWeeklyHistory = computed(() => {
  if (!selectedUser.value?.weeklyHistory || selectedUser.value.weeklyHistory.length === 0) {
    return [];
  }
  
  // Create a new array starting from Monday
  const weeklyHistory = [...selectedUser.value.weeklyHistory];
  const dayOrder = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
  
  // Sort the history to match Monday-Sunday order
  const reordered = dayOrder.map(dayShort => {
    return weeklyHistory.find(day => day.dayShort === dayShort) || {
      dayShort,
      steps: 0,
      hasData: false,
      progress: 0,
      goalCompleted: false,
      isToday: false,
      date: null
    };
  });
  
  return reordered;
});

// Calendar computed properties
const currentDate = computed(() => {
  const date = new Date();
  date.setMonth(date.getMonth() + currentMonthOffset.value);
  return date;
});

const currentMonthName = computed(() => {
  return currentDate.value.toLocaleDateString('en-US', { month: 'long' });
});

const currentYear = computed(() => {
  return currentDate.value.getFullYear();
});

const daysInCurrentMonth = computed(() => {
  return new Date(currentYear.value, currentDate.value.getMonth() + 1, 0).getDate();
});

const firstDayOfMonth = computed(() => {
  return new Date(currentYear.value, currentDate.value.getMonth(), 1).getDay();
});

const isCurrentMonth = computed(() => {
  const today = new Date();
  return currentDate.value.getMonth() === today.getMonth() && 
         currentDate.value.getFullYear() === today.getFullYear();
});

const selectedDayFormatted = computed(() => {
  if (!selectedDay.value) return '';
  const date = new Date(currentYear.value, currentDate.value.getMonth(), selectedDay.value);
  return date.toLocaleDateString('en-US', { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
});

const page = ref(props.pagination?.current_page || 1);
const perPage = ref(props.pagination?.per_page || 10);

watch([page, perPage], ([newPage, newPerPage]) => {
  router.get(route('steptracker'), { page: newPage, perPage: newPerPage }, { preserveState: true, replace: true });
});

function goToPage(p) {
  if (p !== page.value && p > 0 && p <= (props.pagination?.last_page || 1)) {
    page.value = p;
  }
}

function getVisiblePages() {
  const current = props.pagination.current_page;
  const last = props.pagination.last_page;
  const pages = [];
  
  // Show 2 pages before and after current page
  const start = Math.max(1, current - 2);
  const end = Math.min(last, current + 2);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
}

function applyFilters() {
  // Removed filter logic
}
function clearSearch() {
  search.value = '';
}
function openModal(user) {
  selectedUser.value = user;
  showModal.value = true;
  currentMonthOffset.value = 0;
  selectedDay.value = null;
}
function closeModal() {
  showModal.value = false;
  selectedUser.value = null;
  currentMonthOffset.value = 0;
  selectedDay.value = null;
}

function exportData() {
  if (selectedUser.value) {
    const data = {
      name: selectedUser.value.name,
      email: selectedUser.value.email,
      dailyGoal: selectedUser.value.dailyGoal,
      stepsToday: selectedUser.value.stepsToday,
      kilometers: selectedUser.value.kilometers,
      weeklyAvg: selectedUser.value.weeklyAvg,
      calories: selectedUser.value.calories,
      goalStatus: selectedUser.value.goalStatus,
      weeklyHistory: reorderedWeeklyHistory.value,
      weeklyGoalsCompleted: selectedUser.value.weeklyGoalsCompleted,
      totalWeeklySteps: selectedUser.value.totalWeeklySteps
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${selectedUser.value.name}-step-data.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  }
}

// Helper functions for trend analysis
function getActivityLevel(goalsCompleted) {
  if (goalsCompleted >= 6) return 'Very Active';
  if (goalsCompleted >= 4) return 'Active';
  if (goalsCompleted >= 2) return 'Moderate';
  return 'Low Activity';
}

function getActivityLevelClass(goalsCompleted) {
  if (goalsCompleted >= 6) return 'text-green-600 font-semibold';
  if (goalsCompleted >= 4) return 'text-blue-600 font-semibold';
  if (goalsCompleted >= 2) return 'text-yellow-600 font-semibold';
  return 'text-red-600 font-semibold';
}

function getBestDay(weeklyHistory) {
  if (!weeklyHistory || weeklyHistory.length === 0) return 'N/A';
  const bestDay = weeklyHistory.reduce((prev, current) => 
    (prev.steps > current.steps) ? prev : current
  );
  return bestDay.steps > 0 ? `${bestDay.dayShort} (${bestDay.steps.toLocaleString()})` : 'No data';
}

function getTrend(weeklyHistory) {
  if (!weeklyHistory || weeklyHistory.length < 3) return 'Insufficient data';
  
  // Compare first half vs second half of the week
  const firstHalf = weeklyHistory.slice(0, 3).filter(d => d.hasData);
  const secondHalf = weeklyHistory.slice(4, 7).filter(d => d.hasData);
  
  if (firstHalf.length === 0 || secondHalf.length === 0) return 'Insufficient data';
  
  const firstAvg = firstHalf.reduce((sum, day) => sum + day.steps, 0) / firstHalf.length;
  const secondAvg = secondHalf.reduce((sum, day) => sum + day.steps, 0) / secondHalf.length;
  
  const improvement = ((secondAvg - firstAvg) / firstAvg) * 100;
  
  if (improvement > 10) return `+${Math.round(improvement)}% improving`;
  if (improvement < -10) return `${Math.round(improvement)}% declining`;
  return 'Stable';
}

function getTrendClass(weeklyHistory) {
  const trend = getTrend(weeklyHistory);
  if (trend.includes('improving')) return 'text-green-600 font-semibold';
  if (trend.includes('declining')) return 'text-red-600 font-semibold';
  return 'text-gray-600';
}

// Calendar navigation functions
function previousMonth() {
  currentMonthOffset.value--;
  selectedDay.value = null;
}

function nextMonth() {
  currentMonthOffset.value++;
  selectedDay.value = null;
}

function selectDay(dayNum) {
  selectedDay.value = dayNum;
}

function getDayClass(dayNum) {
  const today = new Date();
  const isToday = isCurrentMonth.value && dayNum === today.getDate();
  const dayDate = new Date(currentYear.value, currentDate.value.getMonth(), dayNum);
  const dateString = dayDate.toISOString().split('T')[0];
  const isFuture = dayDate > today;
  const isSelected = selectedDay.value === dayNum;
  const steps = getStepsForDay(dayNum);
  const progress = getProgressForDay(dayNum);
  
  // Check if we have real data for this date
  const hasRealData = selectedUser.value?.weeklyHistory?.some(day => day.date === dateString && day.hasData);
  
  let classes = ['border-gray-100'];
  
  if (isSelected) {
    classes.push('ring-2 ring-blue-500 bg-blue-100 text-blue-800 border-blue-300');
  } else if (isToday && steps > 0) {
    classes.push('bg-indigo-500 text-white border-indigo-500');
  } else if (isToday) {
    classes.push('bg-indigo-100 text-indigo-600 border-indigo-300');
  } else if (isFuture) {
    classes.push('bg-gray-50 text-gray-300');
  } else if (hasRealData && progress >= 100) {
    classes.push('bg-green-500 text-white border-green-500');
  } else if (hasRealData && progress >= 75) {
    classes.push('bg-indigo-400 text-white border-indigo-400');
  } else if (hasRealData && progress >= 50) {
    classes.push('bg-indigo-300 text-white border-indigo-300');
  } else if (hasRealData && progress >= 25) {
    classes.push('bg-gray-300 text-gray-700 border-gray-300');
  } else if (hasRealData) {
    classes.push('bg-gray-200 text-gray-600 border-gray-200');
  } else {
    classes.push('bg-gray-100 text-gray-400 border-gray-100');
  }
  
  return classes.join(' ');
}

function getDayTitle(dayNum) {
  const today = new Date();
  const isToday = isCurrentMonth.value && dayNum === today.getDate();
  const dayDate = new Date(currentYear.value, currentDate.value.getMonth(), dayNum);
  const dateString = dayDate.toISOString().split('T')[0];
  const isFuture = dayDate > today;
  const steps = getStepsForDay(dayNum);
  
  // Check if we have real data for this date
  const hasRealData = selectedUser.value?.weeklyHistory?.some(day => day.date === dateString && day.hasData);
  
  if (isToday && steps > 0) {
    return `Today: ${steps.toLocaleString()} steps`;
  } else if (isToday) {
    return 'Today - No steps recorded yet';
  } else if (isFuture) {
    return 'Future date';
  } else if (hasRealData && steps > 0) {
    return `${steps.toLocaleString()} steps recorded (real data)`;
  } else if (hasRealData && steps === 0) {
    return 'No activity recorded (real data)';
  } else if (steps > 0) {
    return `${steps.toLocaleString()} steps recorded`;
  } else {
    return 'No data available';
  }
}

function getStepsForDay(dayNum) {
  const today = new Date();
  const dayDate = new Date(currentYear.value, currentDate.value.getMonth(), dayNum);
  const dateString = dayDate.toISOString().split('T')[0]; // Format: YYYY-MM-DD
  
  // Check if we have real data for this date from weeklyHistory
  if (selectedUser.value?.weeklyHistory) {
    const dayData = selectedUser.value.weeklyHistory.find(day => day.date === dateString);
    if (dayData && dayData.hasData) {
      return dayData.steps || 0;
    }
  }
  
  // If it's today and we don't have it in weekly history, use stepsToday
  if (isCurrentMonth.value && dayNum === today.getDate()) {
    return selectedUser.value?.stepsToday || 0;
  }
  
  // No real data available for this date
  return 0;
}

function getProgressForDay(dayNum) {
  const steps = getStepsForDay(dayNum);
  const goal = selectedUser.value?.dailyGoal || 10000;
  return Math.min(100, Math.round((steps / goal) * 100));
}

function getDistanceForDay(dayNum) {
  const steps = getStepsForDay(dayNum);
  return (steps * 0.0008).toFixed(2); // Approximate conversion
}

function getCaloriesForDay(dayNum) {
  const steps = getStepsForDay(dayNum);
  return Math.round(steps * 0.04); // Approximate conversion
}

function isGoalCompletedForDay(dayNum) {
  return getProgressForDay(dayNum) >= 100;
}

function hasRealDataForDay(dayNum) {
  const today = new Date();
  const dayDate = new Date(currentYear.value, currentDate.value.getMonth(), dayNum);
  const dateString = dayDate.toISOString().split('T')[0];
  
  // Check if it's today (current day always has "real" data)
  if (isCurrentMonth.value && dayNum === today.getDate()) {
    return true;
  }
  
  // Check if we have real data for this date in weekly history
  return selectedUser.value?.weeklyHistory?.some(day => day.date === dateString && day.hasData) || false;
}

function getCurrentMonthProgress() {
  const today = new Date();
  const daysElapsed = isCurrentMonth.value ? today.getDate() : daysInCurrentMonth.value;
  let goalsCompleted = 0;
  let daysWithData = 0;
  
  for (let day = 1; day <= daysElapsed; day++) {
    const dayDate = new Date(currentYear.value, currentDate.value.getMonth(), day);
    const dateString = dayDate.toISOString().split('T')[0];
    const hasRealData = selectedUser.value?.weeklyHistory?.some(dayData => dayData.date === dateString && dayData.hasData);
    
    if (hasRealData || (isCurrentMonth.value && day === today.getDate())) {
      daysWithData++;
      if (isGoalCompletedForDay(day)) {
        goalsCompleted++;
      }
    }
  }
  
  return daysWithData > 0 ? Math.round((goalsCompleted / daysWithData) * 100) : 0;
}

function getEstimatedMonthlySteps() {
  let totalSteps = 0;
  const today = new Date();
  const daysToCalculate = isCurrentMonth.value ? today.getDate() : daysInCurrentMonth.value;
  
  for (let day = 1; day <= daysToCalculate; day++) {
    const dayDate = new Date(currentYear.value, currentDate.value.getMonth(), day);
    const dateString = dayDate.toISOString().split('T')[0];
    const hasRealData = selectedUser.value?.weeklyHistory?.some(dayData => dayData.date === dateString && dayData.hasData);
    
    // Only count steps from days with real data or today
    if (hasRealData || (isCurrentMonth.value && day === today.getDate())) {
      totalSteps += getStepsForDay(day);
    }
  }
  
  return totalSteps;
}


</script>

<style>
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
