<template>
  <Head title="Food Nutrition" />
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
                  <h3 class="text-xl font-bold text-gray-900">Food Nutrition Records</h3>
                  <p class="text-sm text-gray-600 mt-1">View and manage food nutrition data</p>
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
                    placeholder="Search by food name or nutrient..." 
                    class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm shadow-sm hover:shadow-md transition-shadow duration-200"
                  >
                </div>
              </div>
            </div>
            <!-- Add Food Button below search bar -->
            <div class="flex justify-start mb-4">
              <button 
                @click="openAddModal"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Food
              </button>
            </div>

            <div v-if="searchQuery && filteredFoods.length !== foods.length" class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 mb-4">
              <div class="flex items-center space-x-2">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-blue-800">
                  Showing {{ filteredFoods.length }} of {{ foods.length }} records
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
              <p class="mt-2 text-gray-600">Loading records...</p>
            </div>

            <div v-else-if="filteredFoods && filteredFoods.length > 0" class="overflow-hidden border-2 border-gray-300 rounded-xl shadow-xl bg-white">
              <div class="overflow-x-auto">
                <table class="min-w-full">
                  <thead class="bg-gradient-to-r from-slate-700 to-slate-800">
                    <tr>
                      <th class="px-6 py-5 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-slate-600 last:border-r-0">#</th>
                      <th class="px-6 py-5 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-slate-600 last:border-r-0">Food Name</th>
                      <th class="px-6 py-5 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-slate-600 last:border-r-0">Calories</th>
                      <th class="px-6 py-5 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-slate-600 last:border-r-0">Protein (g)</th>
                      <th class="px-6 py-5 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-slate-600 last:border-r-0">Carbs (g)</th>
                      <th class="px-6 py-5 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-slate-600 last:border-r-0">Fat (g)</th>
                      <th class="px-6 py-5 text-left text-xs font-bold text-white uppercase tracking-wider border-r border-slate-600 last:border-r-0">Description</th>
                      <th class="px-6 py-5 text-center text-xs font-bold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white">
                    <tr v-for="(food, idx) in filteredFoods" :key="food.id" class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-75 ease-out">
                      <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-700 border-r border-gray-200">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-slate-100 text-slate-700 rounded-full text-sm font-bold">
                          {{ idx + 1 + ((pagination?.current_page - 1) * (pagination?.per_page || 10)) }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                        <div class="font-semibold text-gray-900">{{ food.name }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                          {{ food.calories }} kcal
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                          {{ food.protein }}g
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          {{ food.carbs }}g
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap border-r border-gray-200">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                          {{ food.fat }}g
                        </span>
                      </td>
                      <td class="px-6 py-4 border-r border-gray-200">
                        <div class="max-w-xs">
                          <span class="text-sm text-gray-600" :title="food.description">
                            {{ food.description && food.description.length > 35 ? food.description.slice(0, 35) + '...' : food.description || 'No description' }}
                          </span>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center space-x-2">
                          <!-- Edit Button -->
                          <button @click="editFood(food)" 
                                  class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-100 ease-out"
                                  data-tippy-content="Edit Food">
                              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                              </svg>
                          </button>
                          
                          <!-- Delete Button -->
                          <button @click="deleteFood(food)" 
                                  class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-100 ease-out"
                                  data-tippy-content="Delete Food">
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
                {{ searchQuery ? 'No records match your search' : 'No records found' }}
              </h3>
              <p class="mt-1 text-sm text-gray-500">
                {{ searchQuery 
                  ? 'Try adjusting your search criteria or clear the search to see all records.' 
                  : 'There are no records to display at this time.' 
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
            <!-- Pagination Controls -->
            <div v-if="pagination" class="flex justify-between items-center mt-6 px-4">
              <div class="text-sm text-gray-600">
                Showing
                <span class="font-semibold">{{ pagination.from }}</span>
                to
                <span class="font-semibold">{{ pagination.to }}</span>
                of
                <span class="font-semibold">{{ pagination.total }}</span>
                records
              </div>
              <div class="flex items-center space-x-1">
                <button
                  class="px-3 py-1 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 disabled:opacity-50"
                  :disabled="pagination.current_page === 1"
                  @click="goToPage(pagination.current_page - 1)"
                >
                  Prev
                </button>
                <!-- Show first page -->
                <button
                  v-if="pagination.current_page > 3"
                  class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700"
                  @click="goToPage(1)"
                >
                  1
                </button>
                <span v-if="pagination.current_page > 4" class="px-2 text-gray-500">...</span>
                
                <!-- Show pages around current page -->
                <template v-for="p in getVisiblePages()" :key="p">
                  <button
                    class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700"
                    :class="{ 'bg-indigo-600 text-white border-indigo-600': p === pagination.current_page }"
                    @click="goToPage(p)"
                  >
                    {{ p }}
                  </button>
                </template>
                
                <!-- Show last page -->
                <span v-if="pagination.current_page < pagination.last_page - 3" class="px-2 text-gray-500">...</span>
                <button
                  v-if="pagination.current_page < pagination.last_page - 2"
                  class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700"
                  @click="goToPage(pagination.last_page)"
                >
                  {{ pagination.last_page }}
                </button>
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



    <!-- Add Food Modal -->
    <Modal :show="showingAddModal" @close="closeAddModal" max-width="md">
      <div class="p-4">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-medium text-gray-900">Add Food Record</h2>
          <button 
            @click="closeAddModal"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="addFood" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Food Name</label>
            <input 
              v-model="addForm.name"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Calories</label>
            <input 
              v-model="addForm.calories"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Protein (g)</label>
            <input 
              v-model="addForm.protein"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Carbs (g)</label>
            <input 
              v-model="addForm.carbs"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Fat (g)</label>
            <input 
              v-model="addForm.fat"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea 
              v-model="addForm.description"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              rows="2"
              placeholder="Enter description (optional)"
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <SecondaryButton @click="closeAddModal" type="button">
              Cancel
            </SecondaryButton>
            <button 
              type="submit"
              :disabled="processing"
              class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
              <span v-if="processing">Adding...</span>
              <span v-else>Add Food</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Edit Food Modal -->
    <Modal :show="showingEditModal" @close="closeEditModal" max-width="md">
      <div class="p-4" v-if="selectedFood">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-medium text-gray-900">Edit Food Record</h2>
          <button 
            @click="closeEditModal"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <form @submit.prevent="updateFood" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Food Name</label>
            <input 
              v-model="editForm.name"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Calories</label>
            <input 
              v-model="editForm.calories"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Protein (g)</label>
            <input 
              v-model="editForm.protein"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Carbs (g)</label>
            <input 
              v-model="editForm.carbs"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Fat (g)</label>
            <input 
              v-model="editForm.fat"
              type="text" 
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea 
              v-model="editForm.description"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              rows="2"
              placeholder="Enter description (optional)"
            ></textarea>
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <SecondaryButton @click="closeEditModal" type="button">
              Cancel
            </SecondaryButton>
            <button 
              type="submit"
              :disabled="processing"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
              <span v-if="processing">Updating...</span>
              <span v-else>Update Food</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showingDeleteModal" @close="closeDeleteModal" max-width="md">
      <div class="p-6" v-if="selectedFood">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
          <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </div>
        <h2 class="text-lg font-medium text-gray-900 text-center mb-2">
          Delete Record
        </h2>
        <p class="text-sm text-gray-600 text-center mb-6">
          Are you sure you want to delete this record for "{{ selectedFood.name }}"? This action cannot be undone.
        </p>
        <div class="flex justify-center space-x-3">
          <SecondaryButton @click="closeDeleteModal">
            Cancel
          </SecondaryButton>
          <DangerButton @click="confirmDeleteFood" :disabled="processing">
            <span v-if="processing">Deleting...</span>
            <span v-else>Delete Record</span>
          </DangerButton>
        </div>
      </div>
    </Modal>
    <!-- Delete Success Modal -->
    <Modal :show="showingDeleteSuccessModal" @close="closeDeleteSuccessModal" max-width="sm">
      <div class="p-6 text-center">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 class="text-lg font-medium text-gray-900 mb-2">Deleted Successfully</h2>
        <p class="text-sm text-gray-600 mb-6">The food record has been deleted.</p>
        <button @click="closeDeleteSuccessModal" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring ring-green-300 transition ease-in-out duration-150">
          OK
        </button>
      </div>
    </Modal>

    <!-- Add Success Modal -->
    <Modal :show="showingAddSuccessModal" @close="closeAddSuccessModal" max-width="sm">
      <div class="p-6 text-center">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 class="text-lg font-medium text-gray-900 mb-2">Added Successfully</h2>
        <p class="text-sm text-gray-600 mb-6">The food record has been added.</p>
        <button @click="closeAddSuccessModal" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring ring-green-300 transition ease-in-out duration-150">
          OK
        </button>
      </div>
    </Modal>

    <!-- Update Success Modal -->
    <Modal :show="showingUpdateSuccessModal" @close="closeUpdateSuccessModal" max-width="sm">
      <div class="p-6 text-center">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
          <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2 class="text-lg font-medium text-gray-900 mb-2">Updated Successfully</h2>
        <p class="text-sm text-gray-600 mb-6">The food record has been updated.</p>
        <button @click="closeUpdateSuccessModal" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring ring-green-300 transition ease-in-out duration-150">
          OK
        </button>
      </div>
    </Modal>

  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, defineProps, nextTick } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import axios from 'axios';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

const breadcrumbItems = [
  { title: 'Home', href: route('dashboard') },
  { title: 'Food Nutrition', href: null },
];

const loading = ref(false);
const searchQuery = ref('');
const showingDeleteModal = ref(false);
const showingEditModal = ref(false);
const showingAddModal = ref(false);
const showingDeleteSuccessModal = ref(false);
const showingAddSuccessModal = ref(false);
const showingUpdateSuccessModal = ref(false);
const selectedFood = ref(null);
const processing = ref(false);
const editForm = ref({
  name: '',
  calories: 0,
  protein: 0,
  carbs: 0,
  fat: 0,
  description: ''
});
const addForm = ref({
  name: '',
  calories: 0,
  protein: 0,
  carbs: 0,
  fat: 0,
  description: ''
});

const props = defineProps({
  foods: {
    type: Array,
    default: () => [],
  },
  pagination: {
    type: Object,
    default: () => ({
      current_page: 1,
      per_page: 10,
      from: 1,
      to: 0,
      total: 0,
      last_page: 1,
    }),
  },
});

// Use computed properties to reactively track props changes
const foods = computed(() => props.foods);
const pagination = computed(() => props.pagination);

const filteredFoods = computed(() => {
  if (!searchQuery.value) return foods.value;
  // For now, we'll do client-side filtering but this should ideally be server-side
  const query = searchQuery.value.toLowerCase();
  return foods.value.filter(food => {
    return (
      (food.name && food.name.toLowerCase().includes(query)) ||
      (food.calories && food.calories.toString().includes(query)) ||
      (food.protein && food.protein.toString().includes(query)) ||
      (food.carbs && food.carbs.toString().includes(query)) ||
      (food.fat && food.fat.toString().includes(query)) ||
      (food.description && food.description.toLowerCase().includes(query))
    );
  });
});

function clearSearch() {
  searchQuery.value = '';
}

function editFood(food) {
  selectedFood.value = food;
  editForm.value = {
    name: food.name || '',
    calories: food.calories || 0,
    protein: food.protein || 0,
    carbs: food.carbs || 0,
    fat: food.fat || 0,
    description: food.description || ''
  };
  showingEditModal.value = true;
}

function closeEditModal() {
  showingEditModal.value = false;
  selectedFood.value = null;
  editForm.value = {
    name: '',
    calories: 0,
    protein: 0,
    carbs: 0,
    fat: 0,
    description: ''
  };
}

async function updateFood() {
  if (!selectedFood.value) return;
  processing.value = true;
  try {
    const id = selectedFood.value.objectId || selectedFood.value.id;
    const response = await axios.patch(`/foodnutrition/${id}`, editForm.value);
    const updatedFood = response.data && response.data.food ? response.data.food : { ...editForm.value, objectId: id };
    
    closeEditModal();
    showingUpdateSuccessModal.value = true;
    
    // Add real-time notification for update
    dispatchFoodNotification({
      message: `Food '${updatedFood.name || editForm.value.name}' updated successfully!`,
      time: new Date().toLocaleTimeString(),
      type: 'success'
    });
    
    // Refresh current page to show updated data
    router.reload({
      only: ['foods', 'pagination']
    });
  } catch (error) {
    alert('Failed to update food: ' + (error.response?.data?.message || error.message));
  } finally {
    processing.value = false;
  }
}

function deleteFood(food) {
  selectedFood.value = food;
  showingDeleteModal.value = true;
}
function closeDeleteModal() {
  showingDeleteModal.value = false;
  selectedFood.value = null;
}
async function confirmDeleteFood() {
  if (!selectedFood.value) return;
  processing.value = true;
  try {
    await axios.delete(`/foodnutrition/${selectedFood.value.objectId || selectedFood.value.id}`);
    
    // Add real-time notification for deletion
    dispatchFoodNotification({
      message: `Food '${selectedFood.value.name}' deleted successfully!`,
      time: new Date().toLocaleTimeString(),
      type: 'danger'
    });
    
    closeDeleteModal();
    showingDeleteSuccessModal.value = true;
    
    // Refresh current page to show updated data
    router.reload({
      only: ['foods', 'pagination']
    });
  } catch (error) {
    alert('Failed to delete food: ' + (error.response?.data?.message || error.message));
  } finally {
    processing.value = false;
  }
}
function closeDeleteSuccessModal() {
  showingDeleteSuccessModal.value = false;
}

function openAddModal() {
  addForm.value = { name: '', calories: 0, protein: 0, carbs: 0, fat: 0, description: '' };
  showingAddModal.value = true;
}
function closeAddModal() {
  showingAddModal.value = false;
  addForm.value = { name: '', calories: 0, protein: 0, carbs: 0, fat: 0, description: '' };
}
function addFood() {
  if (!addForm.value.name) return;
  processing.value = true;
  axios.post('/foodnutrition', addForm.value)
    .then(response => {
      let newFood = response.data && response.data.food
        ? response.data.food
        : { ...addForm.value, id: Date.now() };
      
      closeAddModal();
      showingAddSuccessModal.value = true;
      
      // Add real-time notification
      dispatchFoodNotification({
        message: `Food '${newFood.name}' added successfully!`,
        time: new Date().toLocaleTimeString(),
        type: 'success'
      });
      
      // Refresh the page to show the new food (go to first page to see newest items)
      router.get(route('foodnutrition.index'), {
        page: 1,
        per_page: pagination.value.per_page
      }, {
        preserveState: false
      });
    })
    .catch(error => {
      alert('Failed to add food: ' + (error.response?.data?.message || error.message));
    })
    .finally(() => {
      processing.value = false;
    });
}

function closeAddSuccessModal() {
  showingAddSuccessModal.value = false;
}
function closeUpdateSuccessModal() {
  showingUpdateSuccessModal.value = false;
}

function goToPage(p) {
  if (p !== pagination.value.current_page && p > 0 && p <= (pagination.value?.last_page || 1)) {
    loading.value = true;
    router.get(route('foodnutrition.index'), {
      page: p,
      per_page: pagination.value.per_page
    }, {
      preserveState: false,
      preserveScroll: false,
      onStart: () => {
        loading.value = true;
      },
      onFinish: () => {
        loading.value = false;
      }
    });
  }
}

function getVisiblePages() {
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const pages = [];
  
  // Show 2 pages before and after current page
  const start = Math.max(1, current - 2);
  const end = Math.min(last, current + 2);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
}

// Notification function to dispatch custom events
function dispatchFoodNotification(notification) {
  window.dispatchEvent(new CustomEvent('food-added', {
    detail: notification
  }));
}

// Initialize tooltips when component mounts and when data changes
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

// Initialize tooltips when component mounts and when foods data changes
onMounted(() => {
  initTooltips();
});

// Watch for changes in filteredFoods to reinitialize tooltips
computed(() => {
  if (filteredFoods.value) {
    nextTick(() => {
      initTooltips();
    });
  }
  return filteredFoods.value;
});
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
</style>

<style scoped>
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

/* Optimized table row hover effects - using only background color change for better performance */
tbody tr {
  transition: background-color 0.1s ease-out;
  will-change: background-color;
  backface-visibility: hidden;
  transform: translateZ(0);
}

tbody tr:hover {
  background-color: #f8fafc;
}

/* Enhanced badge styling */
.inline-flex.items-center.px-2\.5.py-0\.5 {
  font-weight: 600;
  letter-spacing: 0.025em;
}

/* Professional number badge */
.inline-flex.items-center.justify-center.w-8.h-8 {
  border: 1px solid #e2e8f0;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}
</style>
