<template>
    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="bg-gray-50 border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <Breadcrumb :items="breadcrumbItems" />
                </div>
            </div>
        </template>
        <div class="max-w-5xl  text-left">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Settings</h1>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Admin Profile -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-900">Admin Profile</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-center mb-4">
                            <div class="relative">
                                <img v-if="adminProfile.avatar" :src="adminProfile.avatar" alt="Admin Avatar" class="w-20 h-20 rounded-full object-cover">
                                <div v-else class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                                <button @click="triggerFileInput" class="absolute bottom-0 right-0 bg-blue-600 text-white rounded-full p-1 hover:bg-blue-700 transition duration-200" title="Change profile picture">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                                <input 
                                    ref="fileInput" 
                                    type="file" 
                                    accept="image/*" 
                                    @change="handleImageUpload" 
                                    class="hidden"
                                >
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input v-model="adminProfile.fullName" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input v-model="adminProfile.email" type="email" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="admin@healthyme.com">
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM12 17h5l-5 5v-5zM9 17h5l-5 5v-5z"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-900">Notifications</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">Email Notifications</p>
                                <p class="text-sm text-gray-600">Send system notifications via email</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input v-model="settings.emailNotifications" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">SMS Notifications</p>
                                <p class="text-sm text-gray-600">Send critical alerts via SMS</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input v-model="settings.smsNotifications" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notification Email</label>
                            <input v-model="settings.notificationEmail" type="email" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="notifications@healthyme.com">
                        </div>
                    </div>
                </div>

                <!-- Announcements -->
                <div class="bg-white shadow rounded-lg p-6 lg:col-span-2">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-900">System Announcements</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-700 mb-3">Create New Announcement</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                    <input v-model="newAnnouncement.title" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Announcement title">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                    <select v-model="newAnnouncement.priority" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                                    <textarea v-model="newAnnouncement.message" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4" placeholder="Write your announcement..."></textarea>
                                </div>
                                <button @click="postAnnouncement" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                                    Post Announcement
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-medium text-gray-700 mb-3">Recent Announcements</h3>
                            <div class="space-y-3 max-h-80 overflow-y-auto">
                                <div v-for="announcement in announcements" :key="announcement.id" class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-medium text-gray-900">{{ announcement.title }}</h4>
                                        <span :class="{
                                            'bg-green-100 text-green-800': announcement.priority === 'low',
                                            'bg-blue-100 text-blue-800': announcement.priority === 'medium',
                                            'bg-yellow-100 text-yellow-800': announcement.priority === 'high',
                                            'bg-red-100 text-red-800': announcement.priority === 'critical'
                                        }" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ announcement.priority.toUpperCase() }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ announcement.message }}</p>
                                    <p class="text-xs text-gray-400">{{ announcement.date }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Settings Button -->
            <div class="mt-8 flex justify-end">
                <button @click="saveSettings" class="bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition duration-200 font-medium">
                    Save All Settings
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

// File input reference
const fileInput = ref(null);

// Define breadcrumb items for settings page
const breadcrumbItems = [
    {
        title: 'Home',
        href: route('dashboard')
    },
    {
        title: 'Settings',
        href: null // Current page, no link
    }
];

// Admin profile object
const adminProfile = reactive({
    fullName: 'John Doe',
    email: 'admin@healthyme.com',
    avatar: null // URL to avatar image
});

// Notification settings object
const settings = reactive({
    // Notifications
    emailNotifications: true,
    smsNotifications: false,
    notificationEmail: 'notifications@healthyme.com'
});

// Announcement management
const newAnnouncement = reactive({
    title: '',
    priority: 'medium',
    message: ''
});

const announcements = ref([
    {
        id: 1,
        title: 'System Maintenance',
        priority: 'high',
        message: 'Scheduled maintenance will occur on September 15th from 2:00 AM to 4:00 AM UTC. Services may be temporarily unavailable.',
        date: 'August 30, 2025'
    },
    {
        id: 2,
        title: 'New Feature Release',
        priority: 'medium',
        message: 'We\'ve released new dashboard analytics features. Check out the updated reports section.',
        date: 'August 28, 2025'
    },
    {
        id: 3,
        title: 'Security Update',
        priority: 'critical',
        message: 'Important security patches have been applied. All users are recommended to update their passwords.',
        date: 'August 25, 2025'
    }
]);

// Methods
const saveSettings = () => {
    // Here you would typically send both admin profile and settings to your backend API
    console.log('Saving admin profile:', adminProfile);
    console.log('Saving settings:', settings);
    
    // Show success message (you might want to use a toast notification)
    alert('Profile and settings saved successfully!');
    
    // Example API calls (uncomment and modify as needed):
    // try {
    //     await axios.put('/api/admin/profile', adminProfile);
    //     await axios.put('/api/admin/settings', settings);
    //     // Show success notification
    // } catch (error) {
    //     console.error('Error saving data:', error);
    //     // Show error notification
    // }
};

const postAnnouncement = () => {
    if (!newAnnouncement.title || !newAnnouncement.message) {
        alert('Please fill in both title and message');
        return;
    }
    
    const announcement = {
        id: announcements.value.length + 1,
        title: newAnnouncement.title,
        priority: newAnnouncement.priority,
        message: newAnnouncement.message,
        date: new Date().toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        })
    };
    
    announcements.value.unshift(announcement);
    
    // Reset form
    newAnnouncement.title = '';
    newAnnouncement.message = '';
    newAnnouncement.priority = 'medium';
    
    // Here you would typically send to your backend API
    console.log('New announcement:', announcement);
    
    // Example API call (uncomment and modify as needed):
    // try {
    //     await axios.post('/api/admin/announcements', announcement);
    //     // Show success notification
    // } catch (error) {
    //     console.error('Error posting announcement:', error);
    //     // Show error notification
    // }
};

const exportData = () => {
    // Here you would implement data export functionality
    console.log('Exporting system data...');
    alert('Data export initiated. You will receive an email when ready.');
    
    // Example implementation:
    // try {
    //     const response = await axios.post('/api/admin/export-data');
    //     // Handle the export response
    // } catch (error) {
    //     console.error('Error exporting data:', error);
    // }
};

// Image upload methods
const triggerFileInput = () => {
    fileInput.value.click();
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file.');
        return;
    }
    
    // Validate file size (max 5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
    if (file.size > maxSize) {
        alert('Image size should be less than 5MB.');
        return;
    }
    
    // Create a preview URL
    const reader = new FileReader();
    reader.onload = (e) => {
        adminProfile.avatar = e.target.result;
    };
    reader.readAsDataURL(file);
    
    // Here you would typically upload the file to your server
    console.log('Selected file:', file);
    
    // Example API call for file upload (uncomment and modify as needed):
    // const formData = new FormData();
    // formData.append('avatar', file);
    // try {
    //     const response = await axios.post('/api/admin/upload-avatar', formData, {
    //         headers: {
    //             'Content-Type': 'multipart/form-data'
    //         }
    //     });
    //     adminProfile.avatar = response.data.avatar_url;
    // } catch (error) {
    //     console.error('Error uploading avatar:', error);
    //     alert('Error uploading image. Please try again.');
    // }
};
</script>

<style scoped>
</style>
