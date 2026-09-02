<template>
  <main class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-8 md:py-12">
      <!-- Back Button -->
      <router-link
        to="/events"
        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-indigo-700 hover:bg-indigo-50 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors mb-6 font-medium text-sm"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to Events
      </router-link>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-24">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700 mb-4"></div>
        <p class="text-slate-600 font-medium">Loading event details...</p>
      </div>

      <!-- Error State -->
      <Alert v-else-if="error" type="error" :message="error" class="mb-6" />

      <!-- Event Detail Content -->
      <div v-else-if="event" class="flex flex-col gap-8">
        <!-- Event Header -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8">
          <!-- Category Badge -->
          <div class="flex items-center gap-3 mb-4">
            <Badge :variant="getCategoryVariant(event.category)" size="md">
              {{ event.category }}
            </Badge>
          </div>

          <!-- Title -->
          <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4 leading-tight">
            {{ event.title }}
          </h1>

          <!-- Description -->
          <p class="text-slate-700 text-lg leading-relaxed max-w-3xl">
            {{ event.description }}
          </p>
        </div>

        <!-- Event Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Event Info Cards -->
          <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-indigo-700 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <div>
                <p class="text-xs font-semibold text-slate-600 uppercase mb-1">Date & Time</p>
                <p class="text-base font-semibold text-slate-900">{{ formatDate(event.date) }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-indigo-700 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <div>
                <p class="text-xs font-semibold text-slate-600 uppercase mb-1">Location</p>
                <p class="text-base font-semibold text-slate-900">{{ event.location }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-indigo-700 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <p class="text-xs font-semibold text-slate-600 uppercase mb-1">Organizer</p>
                <p class="text-base font-semibold text-slate-900">{{ event.created_by || 'EventFlow' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Registration Card -->
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl border border-indigo-200 shadow-sm p-6 md:p-8">
          <h2 class="text-2xl font-bold text-slate-900 mb-4">Event Registration</h2>

          <!-- Already Registered State -->
          <div v-if="authStore.isAuthenticated && isRegistered" class="flex flex-col gap-4 py-4">
            <div class="flex items-center gap-3">
              <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-lg font-semibold text-emerald-700">You are registered for this event</span>
            </div>
            <Button variant="danger" size="lg" @click="cancelRegistration" :disabled="isCanceling" class="w-full sm:w-auto">
              {{ isCanceling ? 'Canceling...' : 'Cancel Registration' }}
            </Button>
          </div>

          <!-- Authenticated - Not Registered -->
          <div v-else-if="authStore.isAuthenticated && !isRegistered" class="flex flex-col gap-4">
            <p class="text-slate-700">Ready to attend this event?</p>
            <Button
              variant="primary"
              size="lg"
              @click="registerForEvent"
              :disabled="isRegistering"
              class="w-full sm:w-auto"
            >
              {{ isRegistering ? 'Registering...' : 'Register for Event' }}
            </Button>
          </div>

          <!-- Not Authenticated -->
          <div v-else class="flex flex-col gap-4">
            <p class="text-slate-700 font-medium">Sign in to register for this event</p>
            <router-link
              to="/login"
              class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-indigo-700 text-white hover:bg-indigo-800 focus:ring-4 focus:ring-indigo-200 focus:outline-none font-semibold transition-colors"
            >
              Sign In
            </router-link>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <p class="text-slate-600">Event not found</p>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../../../stores/auth.js'
import api from '../../../utils/axiosConfig.js'
import Badge from '../../atoms/Badge/Badge.vue'
import Button from '../../atoms/Button/Button.vue'
import Alert from '../../atoms/Alert/Alert.vue'

defineOptions({
  name: 'EventDetailPage',
})

const route = useRoute()
const authStore = useAuthStore()

const event = ref(null)
const isLoading = ref(false)
const error = ref(null)
const isRegistered = ref(false)
const isRegistering = ref(false)
const isCanceling = ref(false)

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getCategoryVariant = (category) => {
  const variants = {
    workshop: 'primary',
    conference: 'info',
    meetup: 'success',
    seminar: 'warning',
    masterclass: 'primary',
  }
  return variants[category?.toLowerCase()] || 'neutral'
}

const fetchEvent = async () => {
  isLoading.value = true
  error.value = null

  try {
    const response = await api.get(`/api/events/${route.params.id}`)
    event.value = response.data.data
    
    // Check if user is already registered
    if (authStore.isAuthenticated) {
      await checkRegistration()
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load event'
  } finally {
    isLoading.value = false
  }
}

const checkRegistration = async () => {
  if (!authStore.user) return
  
  try {
    const response = await api.get(`/api/registrations/user/${authStore.user.id}`)
    const userRegistrations = response.data.data || []
    isRegistered.value = userRegistrations.some(reg => reg.event_id === parseInt(route.params.id))
  } catch (err) {
    // Silently fail - just assume not registered
    console.error('Failed to check registration status:', err)
  }
}

const registerForEvent = async () => {
  isRegistering.value = true
  try {
    await api.post('/api/registrations', { event_id: parseInt(route.params.id) })
    isRegistered.value = true
    // Success feedback could be a toast/alert here
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to register for event'
  } finally {
    isRegistering.value = false
  }
}

const cancelRegistration = async () => {
  if (!authStore.user) return
  
  if (!confirm('Are you sure you want to cancel this registration?')) {
    return
  }
  
  isCanceling.value = true
  try {
    // Fetch user's registrations to find the registration ID
    const response = await api.get(`/api/registrations/user/${authStore.user.id}`)
    const userRegistrations = response.data.data || []
    const registration = userRegistrations.find(reg => reg.event_id === parseInt(route.params.id))
    
    if (registration) {
      await api.delete(`/api/registrations/${registration.id}`)
      isRegistered.value = false
      alert('Registration cancelled successfully')
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to cancel registration'
  } finally {
    isCanceling.value = false
  }
}

onMounted(() => {
  fetchEvent()
})
</script>
