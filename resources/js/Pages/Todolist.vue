<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    user: Object,
    notes: {
        type: Array,
        default: () => []
    }
});

const page = usePage();

// Convert props.notes to a reactive ref for local state management
const notes = ref(props.notes || []);

// Debug: Log the notes data to see available fields
console.log('Notes data:', notes.value);
if (notes.value.length > 0) {
    console.log('First note structure:', Object.keys(notes.value[0]));
    console.log('First note data:', notes.value[0]);
    console.log('Color field check:', {
        color: notes.value[0].color,
        Color: notes.value[0].Color,
        noteColor: notes.value[0].noteColor,
        background: notes.value[0].background,
        backgroundColor: notes.value[0].backgroundColor,
        bg_color: notes.value[0].bg_color
    });
    
    // Log all notes' color values for debugging
    console.log('All notes color values:', notes.value.map((note, index) => ({
        index,
        objectId: note.objectId,
        color: note.color,
        Color: note.Color,
        noteColor: note.noteColor,
        background: note.background,
        backgroundColor: note.backgroundColor,
        bg_color: note.bg_color,
        allFields: Object.keys(note)
    })));
}

// Define breadcrumb items
const breadcrumbItems = [
    {
        title: 'Home',
        href: route('dashboard')
    },
    {
        title: 'To-do List',
        href: null
    }
];

// Filters
const statusFilter = ref('All');
const priorityFilter = ref('All');
const categoryFilter = ref('All');
const searchQuery = ref('');


// Computed filtered notes
const filteredNotes = computed(() => {
    return notes.value.filter(note => {
        // Handle different possible field names from Back4App
        const noteStatus = note.status || note.Status || 'To-do';
        const notePriority = note.priority || note.Priority || 'Medium';
        const noteCategory = note.category || note.Category || note.type || 'Other';
        const noteTitle = note.title || note.Title || note.name || '';
        const noteDescription = note.description || note.Description || note.content || '';
        
        const matchesStatus = statusFilter.value === 'All' || noteStatus === statusFilter.value;
        const matchesPriority = priorityFilter.value === 'All' || notePriority === priorityFilter.value;
        const matchesCategory = categoryFilter.value === 'All' || noteCategory === categoryFilter.value;
        const matchesSearch = searchQuery.value === '' || 
            noteTitle.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            noteDescription.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        return matchesStatus && matchesPriority && matchesCategory && matchesSearch;
    });
});

// Color classes for sticky notes with realistic sticky note appearance
const getColorClasses = (color) => {
    console.log('getColorClasses called with:', color, 'Type:', typeof color); // Enhanced debug log
    
    // Handle different color value formats
    let normalizedColor = color;
    
    // If color is null, undefined, or empty, use default
    if (!color) {
        normalizedColor = 'yellow';
    } else {
        // Convert to lowercase and handle various formats
        normalizedColor = String(color).toLowerCase().trim();
        
        // Handle hex colors or other formats by mapping to our supported colors
        if (normalizedColor.startsWith('#')) {
            // Map common hex colors to our color names
            const hexColorMap = {
                '#22c55e': 'green', '#16a34a': 'green', '#15803d': 'green',
                '#3b82f6': 'blue', '#2563eb': 'blue', '#1d4ed8': 'blue',
                '#a855f7': 'purple', '#9333ea': 'purple', '#7c3aed': 'purple',
                '#6b7280': 'gray', '#4b5563': 'gray', '#374151': 'gray',
                '#eab308': 'yellow', '#ca8a04': 'yellow', '#a16207': 'yellow',
                '#ec4899': 'pink', '#db2777': 'pink', '#be185d': 'pink'
            };
            normalizedColor = hexColorMap[normalizedColor] || 'yellow';
        }
    }
    
    // Realistic sticky note colors with gradients and shadows
    const colorMap = {
        yellow: 'bg-gradient-to-br from-yellow-200 to-yellow-300 border-yellow-400 shadow-yellow-200/50',
        green: 'bg-gradient-to-br from-green-200 to-green-300 border-green-400 shadow-green-200/50',
        blue: 'bg-gradient-to-br from-blue-200 to-blue-300 border-blue-400 shadow-blue-200/50',
        purple: 'bg-gradient-to-br from-purple-200 to-purple-300 border-purple-400 shadow-purple-200/50',
        pink: 'bg-gradient-to-br from-pink-200 to-pink-300 border-pink-400 shadow-pink-200/50',
        orange: 'bg-gradient-to-br from-orange-200 to-orange-300 border-orange-400 shadow-orange-200/50',
        red: 'bg-gradient-to-br from-red-200 to-red-300 border-red-400 shadow-red-200/50',
        teal: 'bg-gradient-to-br from-teal-200 to-teal-300 border-teal-400 shadow-teal-200/50',
        indigo: 'bg-gradient-to-br from-indigo-200 to-indigo-300 border-indigo-400 shadow-indigo-200/50',
        gray: 'bg-gradient-to-br from-gray-200 to-gray-300 border-gray-400 shadow-gray-200/50',
        grey: 'bg-gradient-to-br from-gray-200 to-gray-300 border-gray-400 shadow-gray-200/50'
    };
    
    const result = colorMap[normalizedColor] || 'bg-gradient-to-br from-yellow-200 to-yellow-300 border-yellow-400 shadow-yellow-200/50';
    console.log('Normalized color:', normalizedColor, '-> Returning color classes:', result); // Enhanced debug log
    return result;
};

// Priority badge colors
const getPriorityColor = (priority) => {
    console.log('Priority value received:', priority, 'Type:', typeof priority); // Enhanced debug log
    
    // Normalize the priority to handle different cases
    const normalizedPriority = String(priority || 'Medium').toLowerCase().trim();
    
    const priorityMap = {
        'high': 'bg-red-600 text-white shadow-sm border border-red-700',
        'medium': 'bg-yellow-600 text-black shadow-sm border border-yellow-700', 
        'low': 'bg-blue-600 text-white shadow-sm border border-blue-700',
        'critical': 'bg-red-800 text-white shadow-sm border border-red-900',
        'urgent': 'bg-red-700 text-white shadow-sm border border-red-800'
    };
    
    const result = priorityMap[normalizedPriority] || 'bg-green-600 text-white shadow-sm border border-green-700';
    console.log('Normalized priority:', normalizedPriority, '-> Priority color result:', result); // Enhanced debug log
    return result;
};

// Status badge colors
const getStatusColor = (status) => {
    console.log('Status value received:', status, 'Type:', typeof status); // Debug log
    
    // Normalize the status to handle different cases and formats
    const normalizedStatus = String(status || 'To-do').toLowerCase().trim();
    
    const statusMap = {
        'update': 'bg-blue-600 text-white shadow-sm border border-blue-700',
        'to-do': 'bg-orange-600 text-white shadow-sm border border-orange-700',
        'todo': 'bg-orange-600 text-white shadow-sm border border-orange-700',
        'in progress': 'bg-purple-600 text-white shadow-sm border border-purple-700',
        'in-progress': 'bg-purple-600 text-white shadow-sm border border-purple-700',
        'inprogress': 'bg-purple-600 text-white shadow-sm border border-purple-700',
        'progress': 'bg-purple-600 text-white shadow-sm border border-purple-700',
        'completed': 'bg-green-600 text-white shadow-sm border border-green-700',
        'complete': 'bg-green-600 text-white shadow-sm border border-green-700',
        'done': 'bg-green-600 text-white shadow-sm border border-green-700',
        'pending': 'bg-yellow-600 text-white shadow-sm border border-yellow-700',
        'review': 'bg-indigo-600 text-white shadow-sm border border-indigo-700'
    };
    
    const result = statusMap[normalizedStatus] || 'bg-red-600 text-white shadow-sm border border-red-700';
    console.log('Normalized status:', normalizedStatus, '-> Status color result:', result); // Enhanced debug log
    return result;
};

const showAddNoteModal = ref(false);
const showNoteModal = ref(false);
const showEditModal = ref(false);
const selectedNote = ref(null);

// Form data for new note
const newNote = ref({
    title: '',
    description: '',
    status: 'To-do',
    priority: 'Medium',
    category: 'Feature',
    user: '',
    color: 'yellow'
});

// Form data for editing existing note
const editNote = ref({
    id: null,
    objectId: null,
    title: '',
    description: '',
    status: 'To-do',
    priority: 'Medium',
    category: 'Feature',
    user: '',
    color: 'yellow'
});

const addNewNote = () => {
    showAddNoteModal.value = true;
};

const closeModal = () => {
    showAddNoteModal.value = false;
    resetForm();
};

const openNoteModal = (note) => {
    selectedNote.value = note;
    showNoteModal.value = true;
};

const closeNoteModal = () => {
    showNoteModal.value = false;
    selectedNote.value = null;
};

const openEditModal = (note) => {
    editNote.value = {
        id: note.id,
        objectId: note.objectId,
        title: note.title || note.Title || note.name || '',
        description: note.description || note.Description || note.content || '',
        status: note.status || note.Status || 'To-do',
        priority: note.priority || note.Priority || 'Medium',
        category: note.category || note.Category || note.type || 'Feature',
        user: note.user || note.User || note.assignedTo || note.assignee || note.Assignee || note.assigned_to || '',
        color: note.color || note.Color || note.noteColor || note.background || note.backgroundColor || note.bg_color || 'yellow'
    };
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    resetEditForm();
};

const resetForm = () => {
    newNote.value = {
        title: '',
        description: '',
        status: 'To-do',
        priority: 'Medium',
        category: 'Feature',
        user: '',
        color: 'yellow'
    };
};

const resetEditForm = () => {
    editNote.value = {
        id: null,
        objectId: null,
        title: '',
        description: '',
        status: 'To-do',
        priority: 'Medium',
        category: 'Feature',
        user: '',
        color: 'yellow'
    };
};

// Color preview for modal
const getColorPreview = (color) => {
    const colorMap = {
        yellow: 'bg-yellow-300',
        green: 'bg-green-300',
        blue: 'bg-blue-300',
        purple: 'bg-purple-300',
        pink: 'bg-pink-300',
        orange: 'bg-orange-300',
        red: 'bg-red-300',
        teal: 'bg-teal-300'
    };
    return colorMap[color] || 'bg-yellow-300';
};

const saveNote = async () => {
    if (!newNote.value.title || !newNote.value.description || !newNote.value.user) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const response = await axios.post('/todolist', {
            title: newNote.value.title,
            description: newNote.value.description,
            status: newNote.value.status,
            priority: newNote.value.priority,
            category: newNote.value.category,
            user: newNote.value.user,
            color: newNote.value.color
        });

        if (response.data.success) {
            // Add the new note to the local array
            notes.value.unshift(response.data.note);
            closeModal();
            alert('Note created successfully!');
        } else {
            alert('Failed to create note: ' + response.data.message);
        }
    } catch (error) {
        console.error('Error creating note:', error);
        alert('Error creating note: ' + (error.response?.data?.message || error.message));
    }
};

const updateNote = async () => {
    if (!editNote.value.title || !editNote.value.description || !editNote.value.user) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const response = await axios.patch(`/todolist/${editNote.value.objectId}`, {
            title: editNote.value.title,
            description: editNote.value.description,
            status: editNote.value.status,
            priority: editNote.value.priority,
            category: editNote.value.category,
            user: editNote.value.user,
            color: editNote.value.color
        });

        if (response.data.success) {
            // Find and update the note in the local array
            const noteIndex = notes.value.findIndex(note => 
                note.id === editNote.value.id || note.objectId === editNote.value.objectId
            );

            if (noteIndex !== -1) {
                notes.value[noteIndex] = {
                    ...notes.value[noteIndex],
                    title: editNote.value.title,
                    description: editNote.value.description,
                    status: editNote.value.status,
                    priority: editNote.value.priority,
                    category: editNote.value.category,
                    user: editNote.value.user,
                    color: editNote.value.color
                };
            }

            closeEditModal();
            alert('Note updated successfully!');
        } else {
            alert('Failed to update note: ' + response.data.message);
        }
    } catch (error) {
        console.error('Error updating note:', error);
        alert('Error updating note: ' + (error.response?.data?.message || error.message));
    }
};

const deleteNote = async (note) => {
    if (!confirm('Are you sure you want to delete this note?')) {
        return;
    }

    try {
        const response = await axios.delete(`/todolist/${note.objectId}`);

        if (response.data.success) {
            // Remove the note from the local array
            const noteIndex = notes.value.findIndex(n => 
                n.id === note.id || n.objectId === note.objectId
            );
            
            if (noteIndex !== -1) {
                notes.value.splice(noteIndex, 1);
            }

            closeNoteModal();
            alert('Note deleted successfully!');
        } else {
            alert('Failed to delete note: ' + response.data.message);
        }
    } catch (error) {
        console.error('Error deleting note:', error);
        alert('Error deleting note: ' + (error.response?.data?.message || error.message));
    }
};
</script>

<template>
    <Head title="To-do List" />

    <AuthenticatedLayout>
        <template #breadcrumb>
            <div class="bg-gray-50 border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <Breadcrumb :items="breadcrumbItems" />
                </div>
            </div>
        </template>
        
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-yellow-300 border border-yellow-400 rounded transform rotate-12 shadow-sm"></div>
                                <h1 class="text-2xl font-semibold text-gray-900">Sticky Notes Board</h1>
                            </div>
                            <button 
                                @click="addNewNote"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-medium flex items-center gap-2 shadow-lg hover:shadow-xl transition-all"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                ADD NEW STICKY NOTE
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select v-model="statusFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="All">All</option>
                                    <option value="Update">Update</option>
                                    <option value="To-do">To-do</option>
                                    <option value="In progress">In progress</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <select v-model="priorityFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="All">All</option>
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select v-model="categoryFilter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="All">All</option>
                                    <option value="Feature">Feature</option>
                                    <option value="Update">Update</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input 
                                    v-model="searchQuery"
                                    type="text" 
                                    placeholder="Search notes..."
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sticky Notes Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div 
                        v-for="note in filteredNotes" 
                        :key="note.objectId || note.id || Math.random()"
                        :class="['sticky-note group relative rounded-lg border-2 p-6 shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:-rotate-1 hover:scale-105', getColorClasses(note.color || note.Color || note.noteColor || note.background || note.backgroundColor || note.bg_color || 'yellow')]"
                        @click="openNoteModal(note)"
                    >
                        <!-- Tape effect at the top -->
                        <div class="absolute -top-2 left-1/2 transform -translate-x-1/2 w-16 h-4 bg-yellow-200 border border-yellow-300 rounded-sm opacity-75 shadow-sm"></div>
                        
                        <!-- Pin/Pushpin effect -->
                        <div class="absolute top-2 right-2">
                            <div class="w-3 h-3 bg-red-500 rounded-full shadow-sm border border-red-600"></div>
                            <div class="absolute top-0.5 right-0.5 w-2 h-2 bg-red-400 rounded-full"></div>
                        </div>

                        <!-- Edit button -->
                        <div class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <button 
                                @click.stop="openEditModal(note)"
                                class="bg-white/80 hover:bg-white text-gray-600 hover:text-gray-800 rounded-full p-1.5 shadow-sm hover:shadow-md transition-all"
                                title="Edit note"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Note Content -->
                        <div class="pr-6 pt-2">
                            <h3 class="font-bold text-gray-800 mb-3 text-lg leading-tight handwriting">{{ note.title || note.Title || note.name || 'Untitled' }}</h3>
                            <p class="text-sm text-gray-700 mb-4 line-clamp-4 leading-relaxed handwriting">{{ note.description || note.Description || note.content || 'No description' }}</p>
                            
                            <!-- Status and Priority badges -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span :class="['inline-flex items-center px-2 py-1 rounded-full text-xs font-bold shadow-sm', getStatusColor(note.status || note.Status || 'To-do')]">
                                    {{ note.status || note.Status || 'To-do' }}
                                </span>
                                <span :class="['inline-flex items-center px-2 py-1 rounded-full text-xs font-bold shadow-sm', getPriorityColor(note.priority || note.Priority || 'Medium')]">
                                    {{ note.priority || note.Priority || 'Medium' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white/70 text-gray-800 border border-gray-300 shadow-sm">
                                    {{ note.category || note.Category || note.type || 'Other' }}
                                </span>
                            </div>

                            <!-- Assignee info -->
                            <div class="flex items-center text-xs text-gray-600 bg-white/50 rounded-md px-2 py-1 border border-gray-200" v-if="note.user || note.User || note.assignedTo || note.assignee || note.Assignee || note.assigned_to">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ note.user || note.User || note.assignedTo || note.assignee || note.Assignee || note.assigned_to || 'Unknown' }}
                            </div>
                            
                            <!-- Debug: Show all fields if no assignee found -->
                            <div v-else class="text-xs text-red-500 mt-1">
                                No assignee found. Available fields: {{ Object.keys(note).join(', ') }}
                            </div>
                        </div>
                        
                        <!-- Fold corner effect -->
                        <div class="absolute bottom-0 right-0 w-6 h-6 bg-black/10 transform rotate-45 translate-x-3 translate-y-3"></div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="filteredNotes.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No notes found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                </div>
            </div>
        </div>

        <!-- Add Note Modal -->
        <div v-if="showAddNoteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" @click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-yellow-300 border border-yellow-400 rounded transform rotate-12"></div>
                            <h3 class="text-lg font-medium text-gray-900">Add New Sticky Note</h3>
                        </div>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="py-4">
                        <form @submit.prevent="saveNote" class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="newNote.title"
                                    type="text" 
                                    required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Enter note title"
                                >
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    v-model="newNote.description"
                                    required
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Enter note description"
                                ></textarea>
                            </div>

                            <!-- Row 1: Status and Priority -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select 
                                        v-model="newNote.status"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="To-do">To-do</option>
                                        <option value="In progress">In progress</option>
                                        <option value="Update">Update</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                    <select 
                                        v-model="newNote.priority"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Row 2: Category and Assignee -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select 
                                        v-model="newNote.category"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="Feature">Feature</option>
                                        <option value="Update">Update</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Assignee <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        v-model="newNote.user"
                                        type="text" 
                                        required
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Enter assignee name"
                                    >
                                </div>
                            </div>

                            <!-- Color Selection -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Note Color</label>
                                    <div class="flex flex-wrap gap-2">
                                        <label v-for="colorOption in ['yellow', 'green', 'blue', 'purple', 'pink', 'orange', 'red', 'teal']" 
                                               :key="colorOption" 
                                               class="cursor-pointer">
                                            <input 
                                                type="radio" 
                                                :value="colorOption" 
                                                v-model="newNote.color" 
                                                class="sr-only"
                                            >
                                            <div :class="[
                                                'w-8 h-8 rounded-full border-2 transition-all',
                                                newNote.color === colorOption ? 'border-gray-600 ring-2 ring-gray-400' : 'border-gray-300',
                                                getColorPreview(colorOption)
                                            ]"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                                <button 
                                    type="button"
                                    @click="closeModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    Add Sticky Note
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Note Modal -->
        <div v-if="showNoteModal && selectedNote" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeNoteModal">
            <div class="modal-sticky-note relative top-10 mx-auto p-8 w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-2xl rounded-2xl border-2" 
                 :class="getColorClasses(selectedNote.color || selectedNote.Color || selectedNote.noteColor || selectedNote.background || selectedNote.backgroundColor || selectedNote.bg_color || 'yellow')"
                 @click.stop>
                
                <!-- Tape effect at the top -->
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 w-24 h-6 bg-yellow-200 border border-yellow-300 rounded-sm opacity-75 shadow-lg"></div>
                
                <!-- Pin/Pushpin effect -->
                <div class="absolute top-4 left-4">
                    <div class="w-5 h-5 bg-red-500 rounded-full shadow-lg border border-red-600"></div>
                    <div class="absolute top-1 right-1 w-3 h-3 bg-red-400 rounded-full"></div>
                </div>

                <!-- Close button -->
                <button @click="closeNoteModal" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800 bg-white/50 rounded-full p-2 hover:bg-white/70 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Delete button -->
                <button @click="deleteNote(selectedNote)" class="absolute top-4 right-16 text-red-600 hover:text-red-800 bg-white/50 rounded-full p-2 hover:bg-white/70 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>

                <!-- Note Content -->
                <div class="pt-12 pr-12">
                    <h2 class="font-bold text-gray-800 mb-6 text-3xl leading-tight handwriting">
                        {{ selectedNote.title || selectedNote.Title || selectedNote.name || 'Untitled' }}
                    </h2>
                    
                    <div class="mb-6">
                        <p class="text-lg text-gray-700 leading-relaxed handwriting whitespace-pre-wrap">
                            {{ selectedNote.description || selectedNote.Description || selectedNote.content || 'No description' }}
                        </p>
                    </div>
                    
                    <!-- Status, Priority, and Category badges -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">Status</span>
                            <span :class="['inline-flex items-center px-3 py-2 rounded-full text-sm font-bold shadow-sm', getStatusColor(selectedNote.status || selectedNote.Status || 'To-do')]">
                                {{ selectedNote.status || selectedNote.Status || 'To-do' }}
                            </span>
                        </div>
                        
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">Priority</span>
                            <span :class="['inline-flex items-center px-3 py-2 rounded-full text-sm font-bold shadow-sm', getPriorityColor(selectedNote.priority || selectedNote.Priority || 'Medium')]">
                                {{ selectedNote.priority || selectedNote.Priority || 'Medium' }}
                            </span>
                        </div>
                        
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">Category</span>
                            <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-white/70 text-gray-800 border border-gray-300 shadow-sm">
                                {{ selectedNote.category || selectedNote.Category || selectedNote.type || 'Other' }}
                            </span>
                        </div>
                    </div>

                    <!-- Assignee info -->
                    <div class="flex items-center text-base text-gray-700 bg-white/60 rounded-lg px-4 py-3 border border-gray-200 shadow-sm" 
                         v-if="selectedNote.user || selectedNote.User || selectedNote.assignedTo || selectedNote.assignee || selectedNote.Assignee || selectedNote.assigned_to">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-medium">Assigned to:</span>
                        <span class="ml-2 font-semibold">{{ selectedNote.user || selectedNote.User || selectedNote.assignedTo || selectedNote.assignee || selectedNote.Assignee || selectedNote.assigned_to || 'Unknown' }}</span>
                    </div>

                    <!-- Creation/Update date if available -->
                    <div class="mt-6 pt-6 border-t border-gray-300/50" v-if="selectedNote.createdAt || selectedNote.updatedAt">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span v-if="selectedNote.createdAt">
                                Created: {{ new Date(selectedNote.createdAt).toLocaleDateString() }}
                            </span>
                            <span v-if="selectedNote.updatedAt">
                                Updated: {{ new Date(selectedNote.updatedAt).toLocaleDateString() }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Fold corner effect -->
                <div class="absolute bottom-0 right-0 w-8 h-8 bg-black/10 transform rotate-45 translate-x-4 translate-y-4"></div>
            </div>
        </div>

        <!-- Edit Note Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeEditModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" @click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-yellow-300 border border-yellow-400 rounded transform rotate-12"></div>
                            <h3 class="text-lg font-medium text-gray-900">Edit Sticky Note</h3>
                        </div>
                        <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="py-4">
                        <form @submit.prevent="updateNote" class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Title <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    v-model="editNote.title"
                                    type="text" 
                                    required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Enter note title"
                                >
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    v-model="editNote.description"
                                    required
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Enter note description"
                                ></textarea>
                            </div>

                            <!-- Row 1: Status and Priority -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select 
                                        v-model="editNote.status"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="To-do">To-do</option>
                                        <option value="In progress">In progress</option>
                                        <option value="Update">Update</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                    <select 
                                        v-model="editNote.priority"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Row 2: Category and Assignee -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select 
                                        v-model="editNote.category"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="Feature">Feature</option>
                                        <option value="Update">Update</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Assignee <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        v-model="editNote.user"
                                        type="text" 
                                        required
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="Enter assignee name"
                                    >
                                </div>
                            </div>

                            <!-- Color Selection -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Note Color</label>
                                    <div class="flex flex-wrap gap-2">
                                        <label v-for="colorOption in ['yellow', 'green', 'blue', 'purple', 'pink', 'orange', 'red', 'teal']" 
                                               :key="colorOption" 
                                               class="cursor-pointer">
                                            <input 
                                                type="radio" 
                                                :value="colorOption" 
                                                v-model="editNote.color" 
                                                class="sr-only"
                                            >
                                            <div :class="[
                                                'w-8 h-8 rounded-full border-2 transition-all',
                                                editNote.color === colorOption ? 'border-gray-600 ring-2 ring-gray-400' : 'border-gray-300',
                                                getColorPreview(colorOption)
                                            ]"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                                <button 
                                    type="button"
                                    @click="closeEditModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    Update Note
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-4 {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s;
}
.modal-enter, .modal-leave-to /* .modal-leave-active in <2.1.8 */ {
    opacity: 0;
}

/* Sticky note specific styles */
.sticky-note {
    min-height: 200px;
    position: relative;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1), 0 2px 8px rgba(0, 0, 0, 0.06);
    transform: rotate(-1deg);
}

.sticky-note:nth-child(even) {
    transform: rotate(1deg);
}

.sticky-note:nth-child(3n) {
    transform: rotate(-0.5deg);
}

.sticky-note:hover {
    transform: rotate(0deg) scale(1.02);
    z-index: 10;
}

/* Add subtle animation on load */
@keyframes stickyNoteAppear {
    from {
        opacity: 0;
        transform: rotate(-10deg) scale(0.8);
    }
    to {
        opacity: 1;
        transform: rotate(-1deg) scale(1);
    }
}

@keyframes modalAppear {
    from {
        opacity: 0;
        transform: scale(0.8) rotate(-5deg);
    }
    to {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }
}

.sticky-note {
    animation: stickyNoteAppear 0.3s ease-out;
}

/* Modal sticky note animation */
.modal-sticky-note {
    animation: modalAppear 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25), 0 12px 24px rgba(0, 0, 0, 0.15);
    transform: rotate(0deg);
    min-height: 400px;
}

/* Handwriting-like font effect */
.handwriting {
    font-family: 'Comic Sans MS', cursive, sans-serif;
    line-height: 1.4;
}

/* Paper texture effect */
.sticky-note::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(90deg, transparent 79px, rgba(255,255,255,0.1) 79px, rgba(255,255,255,0.1) 81px, transparent 81px),
        linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px);
    background-size: 80px 20px, 100% 20px;
    pointer-events: none;
    border-radius: inherit;
}
</style>