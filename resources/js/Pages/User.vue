<template>
    <Head title="Users" />
    
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
                <!-- Success/Error Messages -->
                <div v-if="$page.props.flash?.success" class="mb-4 mx-4 sm:mx-6 lg:mx-8 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error || error" class="mb-4 mx-4 sm:mx-6 lg:mx-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $page.props.flash.error || error }}
                </div>

                <!-- Users Table -->
                <div class="bg-white mr-4 overflow-hidden shadow-2xl shadow-gray-600 rounded-2xl border border-gray-200">
                    <div class="p-8 bg-gradient-to-r from-white to-gray-50 border-b border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-indigo-100 rounded-lg">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">User Management</h3>
                                    <p class="text-sm text-gray-600 mt-1">Manage and monitor your application users</p>
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
                                        placeholder="Search users by username, email, or ID..." 
                                        class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm shadow-sm hover:shadow-md transition-shadow duration-200"
                                    >
                                </div>
                            </div>
                        </div>

                        <div v-if="searchQuery && filteredUsers.length !== users.length" class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
                            <div class="flex items-center space-x-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm font-medium text-blue-800">
                                    Showing {{ filteredUsers.length }} of {{ users.length }} users
                                </span>
                            </div>
                            <button 
                                @click="clearSearch"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline"
                            >
                                Clear filter
                            </button>
                        </div>

                        <div v-if="loading" class="text-center py-4">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                            <p class="mt-2 text-gray-600">Loading users...</p>
                        </div>

                        <div v-else-if="filteredUsers && filteredUsers.length > 0" class="overflow-hidden border-2 border-gray-300 rounded-xl shadow-xl bg-white">
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
                                                Email
                                            </th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                                                Phone
                                            </th>
                                            <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                                                Created
                                            </th>
                                            <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider border-b border-slate-600">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        <tr v-for="(user, index) in filteredUsers" :key="user.objectId" 
                                            class="border-b border-gray-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200 hover:shadow-md">
                                            <!-- Row Number -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="inline-flex items-center justify-center w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-bold rounded-full shadow-md">
                                                    {{ ((props.pagination?.current_page || 1) - 1) * (props.pagination?.per_page || 10) + index + 1 }}
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
                                                                {{ user.username ? user.username.charAt(0).toUpperCase() : 'U' }}
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
                                                            {{ user.username || 'No username' }}
                                                        </p>
                                                    </div>
                                                    <p class="text-xs text-gray-500 truncate mt-1">
                                                        ID: {{ user.objectId }}
                                                    </p>
                                                </div>
                                            </td>
                                            
                                            <!-- Email -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center text-sm text-gray-900">
                                                    <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                                    </svg>
                                                    <span class="truncate max-w-48">{{ user.email || 'No email' }}</span>
                                                </div>
                                            </td>
                                            
                                            <!-- Phone -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center text-sm text-gray-900">
                                                    <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    <span class="truncate">{{ user.phoneNumber || 'No phone' }}</span>
                                                </div>
                                            </td>
                                            
                                            <!-- Created Date -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ formatDate(user.createdAt) }}
                                                </div>
                                            </td>
                                            
                                            <!-- Actions -->
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <!-- View Button -->
                                                    <button @click="viewUser(user)" 
                                                            class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                                            data-tippy-content="View User Details">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </button>
                                                    
                                                    <!-- Edit Button -->
                                                    <button @click="editUser(user)" 
                                                            class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                                                            data-tippy-content="Edit User">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    
                                                    <!-- Notification Button -->
                                                    <button @click="sendNotification(user)" 
                                                            class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200"
                                                            data-tippy-content="Send Notification">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.07 2.82a3 3 0 00-3.95-1.09L4 3v14a3 3 0 003 3h8.93a2 2 0 001.42-.59l2.83-2.83A2 2 0 0021 15.17V4a2 2 0 00-2-2H10.07z"></path>
                                                        </svg>
                                                    </button>
                                                    
                                                    <!-- Delete Button -->
                                                    <button @click="confirmDelete(user)" 
                                                            class="inline-flex items-center p-2 border border-transparent rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                                            data-tippy-content="Delete User">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                {{ searchQuery ? 'No users match your search' : 'No users found' }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ searchQuery 
                                    ? 'Try adjusting your search criteria or clear the search to see all users.' 
                                    : 'There are no users to display at this time.' 
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
                        <div v-if="props.pagination" class="flex justify-between items-center mt-6 px-4">
                            <div class="text-sm text-gray-600">
                                Showing {{ props.pagination.from }} to {{ props.pagination.to }} of {{ props.pagination.total }} users
                            </div>
                            <div class="flex items-center space-x-1">
                                <button
                                    class="px-3 py-1 rounded border text-xs"
                                    :class="page === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                    :disabled="page === 1"
                                    @click="goToPage(page - 1)"
                                >Prev</button>
                                <template v-for="p in props.pagination.last_page" :key="p">
                                    <button
                                        class="px-3 py-1 rounded border text-xs"
                                        :class="p === page ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                        @click="goToPage(p)"
                                    >{{ p }}</button>
                                </template>
                                <button
                                    class="px-3 py-1 rounded border text-xs"
                                    :class="page === props.pagination.last_page ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-100'"
                                    :disabled="page === props.pagination.last_page"
                                    @click="goToPage(page + 1)"
                                >Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <Modal :show="showingEditModal" @close="closeEditModal" max-width="md">
            <div class="p-4" v-if="selectedUser">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">Edit User</h2>
                    <button 
                        @click="closeEditModal"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="updateUser" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input 
                            v-model="editForm.username"
                            type="text" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input 
                            v-model="editForm.email"
                            type="email" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input 
                            v-model="editForm.phoneNumber"
                            type="text" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        />
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
                            <span v-else>Update User</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showingDeleteModal" @close="closeDeleteModal" max-width="md">
            <div class="p-6" v-if="selectedUser">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                
                <h2 class="text-lg font-medium text-gray-900 text-center mb-2">
                    Delete User
                </h2>

                <p class="text-sm text-gray-600 text-center mb-6">
                    Are you sure you want to delete user "{{ selectedUser.username }}"? This action cannot be undone.
                </p>

                <div class="flex justify-center space-x-3">
                    <SecondaryButton @click="closeDeleteModal">
                        Cancel
                    </SecondaryButton>
                    
                    <DangerButton @click="deleteUser" :disabled="processing">
                        <span v-if="processing">Deleting...</span>
                        <span v-else>Delete User</span>
                    </DangerButton>
                </div>
            </div>
        </Modal>

        <!-- View User Modal -->
        <Modal :show="showingViewModal" @close="closeViewModal" max-width="2xl">
            <div class="p-0" v-if="selectedUser">
                <div class="flex flex-col items-center bg-gradient-to-br from-indigo-50 to-white rounded-t-xl pt-8 pb-4 shadow-md w-full">
                    <div class="relative mb-3">
                        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-lg border-4 border-white overflow-hidden">
                            <template v-if="selectedUser.profileImage && (selectedUser.profileImage.url || selectedUser.profileImage._url)">
                                <img :src="selectedUser.profileImage.url || selectedUser.profileImage._url" alt="Avatar" class="h-20 w-20 object-cover rounded-full" />
                            </template>
                            <template v-else>
                                <span class="text-3xl font-semibold text-white">
                                    {{ (selectedUser.username || 'U').charAt(0).toUpperCase() }}
                                </span>
                            </template>
                        </div>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-1">{{ selectedUser.username || 'No username' }}</h2>
                    <span class="text-sm text-gray-500 mb-2">User ID: {{ selectedUser.objectId }}</span>
                </div>
                <div class="px-8 py-6 bg-white rounded-b-xl w-full">
                    <!-- User Details Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-indigo-700 mb-4 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            User Details
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                            <div>
                                <span class="font-semibold text-gray-700">Email:</span>
                                <span class="ml-2 text-gray-900">{{ selectedUser.email || 'No email' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Phone:</span>
                                <span class="ml-2 text-gray-900">{{ selectedUser.phoneNumber || 'No phone' }}</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="font-semibold text-gray-700">Created:</span>
                                <span class="ml-2 text-gray-900">{{ formatDate(selectedUser.createdAt) }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- BMI Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold text-indigo-700 mb-4 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            BMI Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                            <div>
                                <span class="font-semibold text-gray-700">Gender:</span>
                                <span class="ml-2 text-gray-900">{{ selectedUser.gender || 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Age:</span>
                                <span class="ml-2 text-gray-900">{{ selectedUser.age || 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Height (cm):</span>
                                <span class="ml-2 text-gray-900">{{ selectedUser.height || 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">Weight (kg):</span>
                                <span class="ml-2 text-gray-900">{{ selectedUser.weight || 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">BMI Value:</span>
                                <span class="ml-2 text-gray-900">{{ selectedUser.bmiValue || 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-700">BMI Category:</span>
                                <span class="ml-2">
                                    <span v-if="selectedUser.bmiCategory" :class="{
                                        'bg-green-100 text-green-700': selectedUser.bmiCategory === 'Normal',
                                        'bg-yellow-100 text-yellow-700': selectedUser.bmiCategory === 'Overweight',
                                        'bg-red-100 text-red-700': selectedUser.bmiCategory === 'Obese',
                                        'bg-blue-100 text-blue-700': selectedUser.bmiCategory === 'Underweight',
                                        'bg-gray-100 text-gray-700': !['Normal','Overweight','Obese','Underweight'].includes(selectedUser.bmiCategory)
                                    }" class="px-2 py-1 rounded text-xs font-semibold">
                                        {{ selectedUser.bmiCategory }}
                                    </span>
                                    <span v-else class="text-gray-900">N/A</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-8">
                        <SecondaryButton @click="closeViewModal">
                            Close
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Send Notification Modal -->
        <Modal :show="showingNotificationModal" @close="closeNotificationModal" max-width="md">
            <div class="p-4" v-if="selectedUser">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">Send Notification</h2>
                    <button 
                        @click="closeNotificationModal"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="submitNotification" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input 
                            v-model="notificationForm.title"
                            type="text" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea 
                            v-model="notificationForm.message"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                            rows="4"
                            required
                        ></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 pt-4">
                        <SecondaryButton @click="closeNotificationModal" type="button">
                            Cancel
                        </SecondaryButton>
                        <button 
                            type="submit"
                            :disabled="processing"
                            class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            <span v-if="processing">Sending...</span>
                            <span v-else>Send Notification</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, computed, nextTick, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

// Props
const props = defineProps({
    users: Array,
    pagination: Object,
    error: {
        type: String,
        default: null
    }
});

// Define breadcrumb items for users page
const breadcrumbItems = [
    {
        title: 'Home',
        href: route('dashboard')
    },
    {
        title: 'Users',
        href: null // Current page, no link
    }
];

// Reactive data
const loading = ref(false);
const processing = ref(false);
const showingEditModal = ref(false);
const showingDeleteModal = ref(false);
const showingViewModal = ref(false);
const showingNotificationModal = ref(false);
const selectedUser = ref(null);
const searchQuery = ref('');
const editForm = ref({
    username: '',
    email: '',
    phoneNumber: ''
});
const notificationForm = ref({
    title: '',
    message: ''
});
const page = ref(props.pagination?.current_page || 1);
const perPage = ref(props.pagination?.per_page || 10);

// Computed properties
const filteredUsers = computed(() => {
    if (!searchQuery.value) {
        return props.users;
    }
    
    const query = searchQuery.value.toLowerCase();
    return props.users.filter(user => {
        return (
            (user.username && user.username.toLowerCase().includes(query)) ||
            (user.email && user.email.toLowerCase().includes(query)) ||
            (user.objectId && user.objectId.toLowerCase().includes(query))
        );
    });
});

// Watchers
watch([page, perPage], ([newPage, newPerPage]) => {
    router.get(route('users.index'), { page: newPage, perPage: newPerPage, search: searchQuery.value }, { preserveState: true, replace: true });
});

// Methods
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

const clearSearch = () => {
    searchQuery.value = '';
};

const editUser = (user) => {
    selectedUser.value = user;
    editForm.value = {
        username: user.username || '',
        email: user.email || '',
        phoneNumber: user.phoneNumber || ''
    };
    showingEditModal.value = true;
};

const closeEditModal = () => {
    showingEditModal.value = false;
    selectedUser.value = null;
    editForm.value = {
        username: '',
        email: '',
        phoneNumber: ''
    };
};

const updateUser = () => {
    if (!selectedUser.value) return;
    
    processing.value = true;
    
    router.put(route('users.update', selectedUser.value.objectId), editForm.value, {
        onSuccess: () => {
            closeEditModal();
        },
        onError: () => {
            // Error handling is done by the flash messages
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};

const confirmDelete = (user) => {
    selectedUser.value = user;
    showingDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showingDeleteModal.value = false;
    selectedUser.value = null;
};

const deleteUser = () => {
    if (!selectedUser.value) return;
    
    processing.value = true;
    
    router.delete(route('users.destroy', selectedUser.value.objectId), {
        onSuccess: () => {
            closeDeleteModal();
        },
        onError: () => {
            // Error handling is done by the flash messages
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};

const viewUser = (user) => {
    selectedUser.value = user;
    showingViewModal.value = true;
};

const closeViewModal = () => {
    showingViewModal.value = false;
    selectedUser.value = null;
};

const openNotificationModal = (user) => {
    selectedUser.value = user;
    notificationForm.value = { title: '', message: '' };
    showingNotificationModal.value = true;
};

const closeNotificationModal = () => {
    showingNotificationModal.value = false;
    selectedUser.value = null;
    notificationForm.value = { title: '', message: '' };
};

const sendNotification = (user) => {
    openNotificationModal(user);
};

const submitNotification = () => {
    if (!selectedUser.value) return;
    processing.value = true;
    router.post(route('users.sendNotification', selectedUser.value.objectId), notificationForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            closeNotificationModal();
        },
        onError: (errors) => {
            // Optionally show error to user
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch {
        return 'Invalid date';
    }
};

function goToPage(p) {
    if (p !== page.value && p > 0 && p <= (props.pagination?.last_page || 1)) {
        page.value = p;
    }
}

// Initialize tooltips when component mounts and when users data changes
onMounted(() => {
    initTooltips();
});

// Watch for changes in filteredUsers to reinitialize tooltips
computed(() => {
    if (filteredUsers.value) {
        nextTick(() => {
            initTooltips();
        });
    }
    return filteredUsers.value;
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