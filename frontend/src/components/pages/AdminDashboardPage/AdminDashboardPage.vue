<template>
  <main class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-8 md:py-12">
      <!-- Page Header -->
      <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900">Admin Dashboard</h1>
        <p class="text-slate-600 mt-2">Manage your events and view registrations</p>
      </header>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
          <div class="text-sm font-semibold text-slate-600 uppercase mb-2">Your Events</div>
          <div class="text-3xl font-bold text-indigo-700">{{ events.length }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
          <div class="text-sm font-semibold text-slate-600 uppercase mb-2">Total Registrations</div>
          <div class="text-3xl font-bold text-emerald-700">{{ totalRegistrations }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
          <div class="text-sm font-semibold text-slate-600 uppercase mb-2">Upcoming Events</div>
          <div class="text-3xl font-bold text-blue-700">{{ upcomingEvents }}</div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="flex flex-col sm:flex-row gap-4 mb-8">
        <Button variant="primary" size="lg" @click="$router.push('/admin/create')" class="flex-1">
          Create New Event
        </Button>
        <Button variant="secondary" size="lg" @click="fetchEvents" class="flex-1">
          Refresh
        </Button>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center py-24">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700"></div>
      </div>

      <!-- Error State -->
      <Alert v-else-if="error" type="error" :message="error" />

      <!-- Events Table -->
      <div v-else-if="events.length > 0" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-100 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Event Title</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Category</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Date</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Location</th>
                <th class="px-6 py-4 text-center text-sm font-semibold text-slate-900">Registered</th>
                <th class="px-6 py-4 text-center text-sm font-semibold text-slate-900">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="event in events" :key="event.id" class="hover:bg-slate-50 transition">
                <td class="px-6 py-4">
                  <div class="font-semibold text-slate-900">{{ event.title }}</div>
                  <div class="text-xs text-slate-600 mt-1 line-clamp-1">{{ event.description }}</div>
                </td>
                <td class="px-6 py-4">
                  <Badge :variant="getCategoryVariant(event.category)" size="sm">
                    {{ event.category }}
                  </Badge>
                </td>
                <td class="px-6 py-4 text-sm text-slate-700">{{ formatDate(event.date) }}</td>
                <td class="px-6 py-4 text-sm text-slate-700">{{ event.location }}</td>
                <td class="px-6 py-4 text-center">
                  <div class="text-sm font-semibold text-slate-900">
                    <span class="text-indigo-700">{{ getEventRegistrations(event.id) }}</span>
                    <span class="text-slate-400"> / </span>
                    <span class="text-slate-600">{{ event.capacity || '∞' }}</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex justify-center gap-2 flex-wrap">
                    <Button
                      variant="secondary"
                      size="sm"
                      @click="viewRegistrations(event.id)"
                      title="View registrations"
                    >
                      View Registrations
                    </Button>
                    <Button
                      variant="secondary"
                      size="sm"
                      @click="$router.push(`/admin/edit/${event.id}`)"
                      title="Edit event"
                    >
                      Edit
                    </Button>
                    <Button
                      variant="danger"
                      size="sm"
                      @click="deleteEvent(event.id)"
                      title="Delete event"
                    >
                      Delete
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-xl border border-slate-200 shadow-sm p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        <h3 class="text-lg font-semibold text-slate-900 mb-2">No events created yet</h3>
        <p class="text-slate-600 mb-6">Start by creating your first event to share with the community.</p>
        <Button variant="primary" @click="$router.push('/admin/create')">
          Create Your First Event
        </Button>
      </div>

      <!-- Registrations Modal -->
      <div
        v-if="showRegistrationsModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50"
        @click.self="showRegistrationsModal = false"
      >
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[70vh] overflow-hidden flex flex-col">
          <!-- Modal Header -->
          <div class="bg-gradient-to-r from-indigo-700 to-blue-600 text-white px-6 py-4 flex justify-between items-center flex-shrink-0">
            <div>
              <h3 class="text-lg font-bold">Event Registrations</h3>
              <p class="text-indigo-100 text-sm">{{ selectedEventTitle }}</p>
            </div>
            <button
              @click="showRegistrationsModal = false"
              class="text-white hover:bg-indigo-600 rounded-lg p-2 transition"
              aria-label="Close modal"
            >
              ✕
            </button>
          </div>

          <!-- Modal Content -->
          <div class="overflow-y-auto flex-1">
            <div v-if="registrations.length > 0" class="divide-y divide-slate-200">
              <div v-for="reg in registrations" :key="reg.id" class="p-6 hover:bg-slate-50 transition">
                <div class="flex items-start justify-between">
                  <div>
                    <div class="font-semibold text-slate-900">{{ reg.user_name }}</div>
                    <div class="text-sm text-slate-600 mt-1">{{ reg.email }}</div>
                  </div>
                  <div class="text-right">
                    <div class="text-xs text-slate-500 font-medium">Registered</div>
                    <div class="text-sm text-slate-700">{{ formatDate(reg.registered_at) }}</div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="flex items-center justify-center py-12 text-slate-500">
              <p>No registrations yet</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../../utils/axiosConfig.js'
import Button from '../../atoms/Button/Button.vue'
import Badge from '../../atoms/Badge/Badge.vue'
import Alert from '../../atoms/Alert/Alert.vue'

defineOptions({
  name: 'AdminDashboardPage',
})

const router = useRouter()

// Data
const events = ref([])
const registrations = ref([])
const isLoading = ref(false)
const error = ref(null)
const showRegistrationsModal = ref(false)
const selectedEventTitle = ref('')

// Computed properties
const totalRegistrations = computed(() => {
  return events.value.reduce((sum, event) => sum + getEventRegistrations(event.id), 0)
})

const upcomingEvents = computed(() => {
  return events.value.filter(event => new Date(event.date) > new Date()).length
})

// Methods
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const getCategoryVariant = (category) => {
  const variants = {
    workshop: 'primary',
    conference: 'secondary',
    meetup: 'primary',
    seminar: 'secondary',
    masterclass: 'primary',
    networking: 'secondary',
    other: 'secondary'
  }
  return variants[category] || 'secondary'
}

const getEventRegistrations = (eventId) => {
  const event = events.value.find(e => e.id === eventId)
  return Number(event?.registration_count || 0)
}

const fetchEvents = async () => {
  isLoading.value = true
  error.value = null

  try {
    const response = await api.get('/api/events', {
      params: {
        limit: 100,
        offset: 0
      }
    })

    events.value = response.data.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load events'
    console.error('Events fetch error:', err)
  } finally {
    isLoading.value = false
  }
}

const viewRegistrations = async (eventId) => {
  const event = events.value.find(e => e.id === eventId)
  selectedEventTitle.value = event?.title || 'Event'

  try {
    const response = await api.get(`/api/events/${eventId}/registrations`, {
      params: {
        limit: 100,
        offset: 0
      }
    })

    registrations.value = response.data.data || []
    showRegistrationsModal.value = true
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load registrations'
    console.error('Registrations fetch error:', err)
  }
}

const deleteEvent = async (eventId) => {
  if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
    return
  }

  try {
    await api.delete(`/api/events/${eventId}`)
    events.value = events.value.filter(e => e.id !== eventId)
    alert('Event deleted successfully')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to delete event'
    console.error('Event delete error:', err)
  }
}

// Lifecycle
onMounted(fetchEvents)
</script>
