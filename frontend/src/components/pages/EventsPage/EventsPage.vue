<template>
  <main class="min-h-screen bg-slate-50 pb-16">
    <!-- Page Header with Stats -->
    <section class="bg-white border-b border-slate-200 py-8 md:py-12">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
          <div>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900">Discover Events</h1>
            <p class="text-slate-600 mt-2">Find and register for events that match your interests</p>
          </div>
          
          <!-- Admin Only: Create Event Button -->
          <CreateEventButton
            v-if="authStore.isAdmin"
            variant="admin"
            size="md"
            label="Create Event"
            icon="+"
            :class="'self-start md:self-auto'"
          />
        </div>

        <!-- Stats Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
            <p class="text-sm text-slate-600 font-medium">Total Events</p>
            <p class="text-2xl font-bold text-indigo-700 mt-1">{{ pagination.total }}</p>
          </div>
          <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
            <p class="text-sm text-slate-600 font-medium">Showing</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ events.length }}</p>
          </div>
          <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
            <p class="text-sm text-slate-600 font-medium">Active Category</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ selectedCategory || 'All' }}</p>
          </div>
          <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
            <p class="text-sm text-slate-600 font-medium">Per Page</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ itemsPerPage }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Filters & Controls Section -->
    <section class="bg-white border-b border-slate-200 py-8">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <!-- Quick Sort Buttons -->
        <div class="mb-6">
          <p class="text-sm font-semibold text-slate-700 mb-3">Sort by:</p>
          <div class="flex flex-wrap gap-2">
            <button
              @click="sortBy = 'date'; fetchEvents()"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-all duration-200',
                sortBy === 'date'
                  ? 'bg-indigo-700 text-white'
                  : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
              ]"
            >
              📅 Nearest
            </button>
            <button
              @click="sortBy = 'newest'; fetchEvents()"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-all duration-200',
                sortBy === 'newest'
                  ? 'bg-indigo-700 text-white'
                  : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
              ]"
            >
              ✨ Newest
            </button>
            <button
              @click="sortBy = 'popular'; fetchEvents()"
              :class="[
                'px-4 py-2 rounded-lg font-medium transition-all duration-200',
                sortBy === 'popular'
                  ? 'bg-indigo-700 text-white'
                  : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
              ]"
            >
              🔥 Popular
            </button>
          </div>
        </div>

        <!-- Filters Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <!-- Search Input -->
          <input
            v-model="searchQuery"
            @input="resetPaginationAndSearch"
            type="text"
            placeholder="Search events..."
            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-indigo-200 text-slate-900 placeholder-slate-500"
          />

          <!-- Category Filter -->
          <select
            v-model="selectedCategory"
            @change="resetPaginationAndSearch"
            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-indigo-200 text-slate-900 bg-white"
          >
            <option value="">All Categories</option>
            <option v-for="cat in allCategories" :key="cat" :value="cat">{{ cat }}</option>
          </select>

          <!-- Items Per Page -->
          <select
            v-model.number="itemsPerPage"
            @change="resetPaginationAndSearch"
            class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-indigo-200 text-slate-900 bg-white"
          >
            <option :value="5">5 per page</option>
            <option :value="10">10 per page</option>
            <option :value="15">15 per page</option>
            <option :value="20">20 per page</option>
          </select>

          <!-- Clear Filters Button -->
          <div v-if="searchQuery || selectedCategory" class="flex items-end">
            <button
              @click="clearFilters"
              class="w-full px-4 py-2.5 bg-red-50 text-red-700 border border-red-300 rounded-lg font-medium hover:bg-red-100 transition-colors duration-200"
            >
              ✕ Clear All
            </button>
          </div>
        </div>

        <!-- Active Filters Display -->
        <div v-if="searchQuery || selectedCategory" class="mt-4 flex flex-wrap gap-2 pt-4 border-t border-slate-200">
          <span class="text-xs font-semibold text-slate-600">Active:</span>
          <span v-if="searchQuery" class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
            🔍 {{ searchQuery }}
          </span>
          <span v-if="selectedCategory" class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-medium">
            📌 {{ selectedCategory }}
          </span>
        </div>
      </div>
    </section>

    <!-- Main Content Area -->
    <section class="py-12 md:py-16">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <!-- Loading State -->
        <div v-if="isLoading" class="flex flex-col items-center justify-center py-24">
          <div class="animate-spin rounded-full h-16 w-16 border-4 border-indigo-200 border-t-indigo-700 mb-4"></div>
          <p class="text-slate-600 font-semibold">Loading events...</p>
          <p class="text-slate-500 text-sm mt-1">Just a moment</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 border-2 border-red-300 rounded-xl p-8 text-center">
          <p class="text-red-700 font-semibold text-lg mb-2">⚠️ Something went wrong</p>
          <p class="text-red-600 mb-6">{{ error }}</p>
          <button
            @click="fetchEvents"
            class="px-6 py-2 bg-red-700 text-white rounded-lg font-semibold hover:bg-red-800 transition-colors duration-200"
          >
            Try Again
          </button>
        </div>

        <!-- Events Grid -->
        <div v-else-if="events.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
          <EventCard v-for="event in events" :key="event.id" :event="event" />
        </div>

        <!-- Empty State - No Results from Search/Filter -->
        <div v-else-if="searchQuery || selectedCategory" class="bg-white rounded-xl border-2 border-dashed border-slate-300 p-12 text-center">
          <div class="w-20 h-20 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-2">No events found</h3>
          <p class="text-slate-600 mb-8 max-w-sm mx-auto">
            We couldn't find any events matching your search criteria.
            <br/>Try adjusting your filters or search terms.
          </p>
          <button
            @click="clearFilters"
            class="px-6 py-2.5 bg-indigo-700 text-white rounded-lg font-semibold hover:bg-indigo-800 transition-colors duration-200"
          >
            Clear All Filters
          </button>
        </div>

        <!-- Empty State - No Events at All -->
        <div v-else class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl border-2 border-slate-200 p-16 text-center">
          <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center mb-6 shadow-sm">
            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-slate-900 mb-3">No events available yet</h3>
          <p class="text-slate-600 mb-8 max-w-lg mx-auto text-lg">
            There are currently no events scheduled. Check back soon or create the first event!
          </p>
          
          <!-- Conditional CTA based on role -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button
              v-if="authStore.isAdmin"
              @click="$router.push('/admin/create')"
              class="px-8 py-3 bg-indigo-700 text-white rounded-lg font-semibold hover:bg-indigo-800 transition-colors duration-200"
            >
              ✨ Create First Event
            </button>
            <button
              v-else
              @click="$router.push('/')"
              class="px-8 py-3 bg-slate-700 text-white rounded-lg font-semibold hover:bg-slate-800 transition-colors duration-200"
            >
              ← Back to Home
            </button>
          </div>
        </div>

        <!-- Admin Actions (for admin viewing events) -->
        <div v-if="authStore.isAdmin && events.length" class="mt-12 pt-8 border-t border-slate-200">
          <h3 class="text-lg font-bold text-slate-900 mb-4">Admin Tools</h3>
          <div class="flex flex-wrap gap-3">
            <button
              @click="$router.push('/admin/create')"
              class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg font-medium hover:bg-indigo-200 transition-colors duration-200"
            >
              + Add New Event
            </button>
            <button
              @click="refreshData"
              class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition-colors duration-200"
            >
              🔄 Refresh Data
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Pagination -->
    <section v-if="events.length" class="bg-white border-t border-slate-200 py-8">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-6">
          <!-- Previous Button -->
          <button
            :disabled="pagination.offset === 0"
            @click="handlePageChange(Math.max(0, pagination.offset - itemsPerPage))"
            :class="[
              'px-6 py-2.5 rounded-lg font-semibold transition-all duration-200',
              pagination.offset === 0
                ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
                : 'bg-slate-700 text-white hover:bg-slate-800'
            ]"
          >
            ← Previous
          </button>

          <!-- Page Info -->
          <div class="text-center">
            <p class="text-slate-900 font-semibold">
              Page <span class="text-indigo-700 font-bold">{{ currentPage }}</span> of <span class="text-indigo-700 font-bold">{{ totalPages }}</span>
            </p>
            <p class="text-slate-600 text-sm mt-1">
              {{ pagination.offset + 1 }}–{{ Math.min(pagination.offset + itemsPerPage, pagination.total) }} of {{ pagination.total }} events
            </p>
          </div>

          <!-- Next Button -->
          <button
            :disabled="pagination.offset + itemsPerPage >= pagination.total"
            @click="handlePageChange(pagination.offset + itemsPerPage)"
            :class="[
              'px-6 py-2.5 rounded-lg font-semibold transition-all duration-200',
              pagination.offset + itemsPerPage >= pagination.total
                ? 'bg-slate-100 text-slate-400 cursor-not-allowed'
                : 'bg-slate-700 text-white hover:bg-slate-800'
            ]"
          >
            Next →
          </button>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../../../stores/auth.js'
import api from '../../../utils/axiosConfig.js'
import EventCard from '../../organisms/EventCard/EventCard.vue'
import CreateEventButton from '../../molecules/CreateEventButton/CreateEventButton.vue'

defineOptions({
  name: 'EventsPage',
})

const authStore = useAuthStore()

// Data
const events = ref([])
const searchQuery = ref('')
const selectedCategory = ref('')
const itemsPerPage = ref(10)
const sortBy = ref('date')
const isLoading = ref(false)
const error = ref(null)
const pagination = ref({
  total: 0,
  limit: 10,
  offset: 0
})

// All available categories
const allCategories = [
  'Workshop',
  'Conference',
  'Meetup',
  'Seminar',
  'Masterclass',
  'Webinar',
  'Training'
]

// Computed properties
const currentPage = computed(() => Math.floor(pagination.value.offset / itemsPerPage.value) + 1)
const totalPages = computed(() => Math.ceil(pagination.value.total / itemsPerPage.value))

// Methods
const fetchEvents = async () => {
  isLoading.value = true
  error.value = null

  try {
    const response = await api.get('/api/events', {
      params: {
        search: searchQuery.value || undefined,
        category: selectedCategory.value || undefined,
        limit: itemsPerPage.value,
        offset: pagination.value.offset,
        sort: sortBy.value
      }
    })

    events.value = response.data.data || []
    pagination.value = {
      total: response.data.pagination?.total || 0,
      limit: itemsPerPage.value,
      offset: pagination.value.offset
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load events. Please try again.'
    console.error('Events fetch error:', err)
  } finally {
    isLoading.value = false
  }
}

const handlePageChange = (newOffset) => {
  pagination.value.offset = newOffset
  fetchEvents()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const resetPaginationAndSearch = () => {
  pagination.value.offset = 0
  fetchEvents()
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  sortBy.value = 'date'
  pagination.value.offset = 0
  fetchEvents()
}

const refreshData = () => {
  fetchEvents()
}

// Lifecycle
onMounted(fetchEvents)
</script>