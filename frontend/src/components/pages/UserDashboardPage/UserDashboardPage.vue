<template>
  <main class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-8 md:py-12">
      <!-- Page Header -->
      <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900">My Registrations</h1>
        <p class="text-slate-600 mt-2">View and manage your event registrations</p>
      </header>

      <!-- Stats Card -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <div class="text-2xl font-bold text-indigo-700">{{ registrations.length }}</div>
            <div class="text-sm text-slate-600 font-medium">Events Registered</div>
          </div>
          <div v-if="upcomingRegistrations > 0">
            <div class="text-2xl font-bold text-emerald-700">{{ upcomingRegistrations }}</div>
            <div class="text-sm text-slate-600 font-medium">Upcoming Events</div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center py-24">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700 mb-4"></div>
      </div>

      <!-- Error State -->
      <Alert v-else-if="error" type="error" :message="error" />

      <!-- Registrations Grid -->
      <div v-else-if="registrations.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div v-for="registration in registrations" :key="registration.id" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition">
          <!-- Status Badge -->
          <div class="flex items-start justify-between mb-4">
            <Badge :variant="isEventUpcoming(registration.event.date) ? 'primary' : 'secondary'" size="sm">
              {{ isEventUpcoming(registration.event.date) ? 'Upcoming' : 'Past' }}
            </Badge>
            <button
              @click="cancelRegistration(registration.id)"
              class="text-slate-400 hover:text-red-600 transition"
              title="Cancel registration"
              :disabled="isCanceling"
            >
              ✕
            </button>
          </div>

          <!-- Event Info -->
          <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-2">{{ registration.event.title }}</h3>
          <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ registration.event.description }}</p>

          <!-- Event Details -->
          <div class="space-y-3 mb-6 text-sm">
            <div class="flex items-center gap-3">
              <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span class="text-slate-700 font-medium">{{ formatDate(registration.event.date) }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="text-slate-700 font-medium line-clamp-1">{{ registration.event.location }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-slate-700 text-xs">Registered on {{ formatDate(registration.registered_at) }}</span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="space-y-2">
            <Button
              variant="outline"
              size="sm"
              @click="viewEvent(registration.id)"
              class="w-full"
            >
              View Event
            </Button>
            <Button
              variant="danger"
              size="sm"
              @click="cancelRegistration(registration.id)"
              :disabled="isCanceling"
              class="w-full"
            >
              {{ isCanceling ? 'Canceling...' : 'Cancel Registration' }}
            </Button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-xl border border-slate-200 shadow-sm p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="text-lg font-semibold text-slate-900 mb-2">No registrations yet</h3>
        <p class="text-slate-600 mb-6">You haven't registered for any events. Explore and find events that interest you!</p>
        <Button variant="primary" @click="$router.push('/events')">
          Browse Events
        </Button>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../../stores/auth.js'
import api from '../../../utils/axiosConfig.js'
import Button from '../../atoms/Button/Button.vue'
import Badge from '../../atoms/Badge/Badge.vue'
import Alert from '../../atoms/Alert/Alert.vue'

defineOptions({
  name: 'UserDashboardPage',
})

const router = useRouter()
const authStore = useAuthStore()

// Data
const registrations = ref([])
const isLoading = ref(false)
const error = ref(null)
const isCanceling = ref(false)

// Computed properties
const upcomingRegistrations = computed(() => {
  return registrations.value.filter(reg => isEventUpcoming(reg.event.date)).length
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

const isEventUpcoming = (date) => {
  return new Date(date) > new Date()
}

const fetchRegistrations = async () => {
  isLoading.value = true
  error.value = null

  try {
    const response = await api.get(`/api/registrations/user/${authStore.user.id}`, {
      params: {
        limit: 100,
        offset: 0
      }
    })

    registrations.value = response.data.data || []
    registrations.value.sort((a, b) => new Date(b.date) - new Date(a.date))
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load registrations'
    console.error('Registrations fetch error:', err)
  } finally {
    isLoading.value = false
  }
}

const viewEvent = (registrationId) => {
  const registration = registrations.value.find(r => r.id === registrationId)
  if (registration) {
    router.push(`/events/${registration.event.id}`)
  }
}

const cancelRegistration = async (registrationId) => {
  if (!confirm('Are you sure you want to cancel this registration?')) {
    return
  }

  isCanceling.value = true
  try {
    await api.delete(`/api/registrations/${registrationId}`)
    registrations.value = registrations.value.filter(r => r.id !== registrationId)
    alert('Registration canceled successfully')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to cancel registration'
    console.error('Cancel registration error:', err)
  } finally {
    isCanceling.value = false
  }
}

// Lifecycle
onMounted(fetchRegistrations)
</script>

