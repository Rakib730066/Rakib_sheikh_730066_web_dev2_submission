<template>
  <main class="min-h-screen bg-slate-50 pb-16">
    <section class="bg-gradient-to-br from-indigo-700 via-indigo-600 to-blue-600 text-white">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-20 md:py-32">
        <div class="max-w-3xl">
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
            {{ heroTitle }}
          </h1>
          <p class="text-lg md:text-xl text-indigo-100 mb-8 leading-relaxed">
            {{ heroSubtitle }}
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <!-- Primary CTA - Changes based on auth state -->
            <button
              @click="handlePrimaryCta"
              class="px-6 py-3 text-base font-semibold rounded-xl bg-white text-indigo-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-indigo-200 transition-colors duration-200"
            >
              {{ primaryCtaLabel }}
            </button>

            <!-- Secondary CTA - Changes based on user role -->
            <button
              @click="handleSecondaryCta"
              class="px-6 py-3 text-base font-semibold rounded-xl bg-transparent text-white border-2 border-white hover:bg-white hover:text-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 transition-colors duration-200"
            >
              {{ secondaryCtaLabel }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <section v-if="authStore.isAdmin" class="bg-white border-b border-slate-200 py-12 md:py-16">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <SectionHeader
          title="Admin Dashboard"
          subtitle="Quick overview and management tools for your events"
        />

        <!-- Admin Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <AdminStatsCard
            label="Total Events"
            :value="adminStats.totalEvents"
            icon="events"
            color-scheme="indigo"
            :subtitle="`${adminStats.upcomingEvents} upcoming`"
          />
          <AdminStatsCard
            label="Total Registrations"
            :value="adminStats.totalRegistrations"
            icon="registrations"
            color-scheme="emerald"
            :trend="{ percentage: 12, isPositive: true }"
            subtitle="Last 30 days"
          />
          <AdminStatsCard
            label="Attendees This Month"
            :value="adminStats.attendeesThisMonth"
            icon="users"
            color-scheme="blue"
            :subtitle="`From ${adminStats.eventsThisMonth} events`"
          />
        </div>

        <!-- Admin Quick Actions -->
        <AdminQuickActions
          :actions="quickActions"
          @action-click="handleAdminAction"
        />
      </div>
    </section>

    <section class="py-16 md:py-24">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <SectionHeader
            title="Upcoming Events"
            subtitle="Discover new opportunities to learn, network, and grow"
          />
          <router-link
            to="/events"
            class="text-indigo-700 font-semibold hover:text-indigo-800 transition-colors"
          >
            View all →
          </router-link>
        </div>

        <!-- Loading State -->
        <div v-if="eventsLoading" class="flex items-center justify-center py-24">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700"></div>
        </div>

        <!-- Error State -->
        <Alert v-else-if="eventsError" type="error" :message="eventsError" />

        <!-- Events Grid -->
        <div v-else-if="upcomingEvents.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <EventCard
            v-for="event in upcomingEvents.slice(0, 6)"
            :key="event.id"
            :event="event"
          />
        </div>

        <!-- No Events Empty State -->
        <EmptyStateCard
          v-else
          icon="calendar"
          title="No events yet"
          description="There are no upcoming events at the moment. Check back soon or create one if you're an admin!"
          :cta-label="authStore.isAdmin ? 'Create First Event' : null"
          @cta-click="handleCreateFirstEvent"
        />

        <!-- View All Button -->
        <div v-if="upcomingEvents.length" class="text-center pt-8">
          <Button 
            variant="secondary" 
            size="lg" 
            @click="$router.push('/events')"
          >
            Explore All Events →
          </Button>
        </div>
      </div>
    </section>

    <section v-if="categories.length" class="bg-white py-12 md:py-16 border-b border-slate-200">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <SectionHeader
          title="Browse by Category"
          subtitle="Find events that match your interests"
        />

        <CategoryChips
          :categories="categories"
          :selected-category="selectedCategory"
          @category-select="handleCategorySelect"
        />
      </div>
    </section>

    <section v-if="authStore.isAuthenticated && !authStore.isAdmin" class="py-16 md:py-24">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <SectionHeader
          title="Your Registrations"
          subtitle="Events you're registered for"
        />

        <!-- Loading State -->
        <div v-if="registrationsLoading" class="flex items-center justify-center py-16">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700"></div>
        </div>

        <!-- Registrations Grid -->
        <div v-else-if="userRegistrations.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <EventCard
            v-for="event in userRegistrations.slice(0, 3)"
            :key="event.id"
            :event="event"
          />
        </div>

        <!-- No Registrations Empty State -->
        <EmptyStateCard
          v-else
          icon="check"
          title="No registrations yet"
          description="You haven't registered for any events yet. Browse upcoming events and register to get started!"
          cta-label="Browse Events"
          @cta-click="$router.push('/events')"
        />

        <!-- View All Registrations Button -->
        <div v-if="userRegistrations.length" class="text-center pt-8">
          <Button 
            variant="secondary" 
            size="lg" 
            @click="$router.push('/dashboard')"
          >
            View All My Registrations →
          </Button>
        </div>
      </div>
    </section>

    <section class="bg-white py-16 md:py-24 border-b border-slate-200">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Why EventFlow?</h2>
          <p class="text-slate-600 max-w-2xl mx-auto">
            EventFlow simplifies event discovery, registration, and management all in one place.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="bg-slate-50 rounded-xl p-8 border border-slate-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
              <svg
                class="w-6 h-6 text-indigo-700"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Easy Discovery</h3>
            <p class="text-slate-600">
              Search by category, location, date, or keyword. Filter events that match your interests perfectly.
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="bg-slate-50 rounded-xl p-8 border border-slate-200 hover:border-emerald-300 hover:shadow-md transition-all duration-200">
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center mb-4">
              <svg
                class="w-6 h-6 text-emerald-700"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">One-Click Registration</h3>
            <p class="text-slate-600">
              Register for events instantly. Manage all your registrations in your personal dashboard.
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="bg-slate-50 rounded-xl p-8 border border-slate-200 hover:border-blue-300 hover:shadow-md transition-all duration-200">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
              <svg
                class="w-6 h-6 text-blue-700"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Network & Grow</h3>
            <p class="text-slate-600">
              Connect with professionals in your field. Build your network at meaningful events.
            </p>
          </div>
        </div>
      </div>
    </section>


    <section class="bg-gradient-to-r from-indigo-700 to-blue-600 text-white py-16 md:py-24">
      <div class="max-w-4xl mx-auto text-center px-4 md:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
          {{ ctaSectionTitle }}
        </h2>
        <p class="text-lg text-indigo-100 mb-8 max-w-2xl mx-auto">
          {{ ctaSectionSubtitle }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button
            @click="handleCtaSectionPrimary"
            class="px-6 py-3 text-base font-semibold rounded-xl bg-white text-indigo-700 hover:bg-slate-100 focus:outline-none focus:ring-4 focus:ring-indigo-200 transition-colors duration-200"
          >
            {{ ctaSectionPrimaryLabel }}
          </button>
          <button
            @click="$router.push('/events')"
            class="px-6 py-3 text-base font-semibold rounded-xl bg-transparent text-white border-2 border-white hover:bg-white hover:text-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 transition-colors duration-200"
          >
            Browse Events
          </button>
        </div>
      </div>
    </section>

    <section class="bg-slate-50 py-16 md:py-24">
      <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Growing Community</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="text-4xl md:text-5xl font-bold text-indigo-700 mb-2">{{ platformStats.totalEvents }}</div>
            <p class="text-slate-600 font-medium">Events Available</p>
            <p class="text-sm text-slate-500 mt-1">Across all categories</p>
          </div>
          <div class="text-center">
            <div class="text-4xl md:text-5xl font-bold text-emerald-700 mb-2">{{ platformStats.totalRegistrations }}</div>
            <p class="text-slate-600 font-medium">Active Registrations</p>
            <p class="text-sm text-slate-500 mt-1">Growing daily</p>
          </div>
          <div class="text-center">
            <div class="text-4xl md:text-5xl font-bold text-blue-700 mb-2">{{ platformStats.totalUsers }}</div>
            <p class="text-slate-600 font-medium">Community Members</p>
            <p class="text-sm text-slate-500 mt-1">From all industries</p>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../../../stores/auth.js'
import { useRouter } from 'vue-router'
import api from '../../../utils/axiosConfig.js'
import Button from '../../atoms/Button/Button.vue'
import Alert from '../../atoms/Alert/Alert.vue'

// Components
import EventCard from '../../organisms/EventCard/EventCard.vue'
import SectionHeader from '../../molecules/SectionHeader/SectionHeader.vue'
import CategoryChips from '../../molecules/CategoryChips/CategoryChips.vue'
import AdminStatsCard from '../../molecules/AdminStatsCard/AdminStatsCard.vue'
import AdminQuickActions from '../../molecules/AdminQuickActions/AdminQuickActions.vue'
import EmptyStateCard from '../../molecules/EmptyStateCard/EmptyStateCard.vue'
import CreateEventButton from '../../molecules/CreateEventButton/CreateEventButton.vue'

defineOptions({
  name: 'HomePage',
})

const authStore = useAuthStore()
const router = useRouter()

// State
const upcomingEvents = ref([])
const userRegistrations = ref([])
const eventsLoading = ref(false)
const registrationsLoading = ref(false)
const eventsError = ref(null)
const selectedCategory = ref(null)
const categories = ref([
  'Workshop',
  'Conference',
  'Meetup',
  'Seminar',
  'Masterclass'
])

// Admin stats
const adminStats = ref({
  totalEvents: 0,
  upcomingEvents: 0,
  totalRegistrations: 0,
  eventsThisMonth: 0,
  attendeesThisMonth: 0
})

// Platform stats
const platformStats = ref({
  totalEvents: 0,
  totalRegistrations: 0,
  totalUsers: 0
})

// Quick actions for admin
const quickActions = ref([
  {
    id: 'create-event',
    label: 'Create Event',
    description: 'Add a new event to your calendar',
    variant: 'primary',
    iconClass: 'bg-indigo-100',
    iconViewBox: '0 0 24 24',
    iconPath: 'M12 4v16m8-8H4'
  },
  {
    id: 'manage-events',
    label: 'Manage Events',
    description: 'Edit or delete your events',
    variant: 'default',
    iconClass: 'bg-slate-100',
    iconViewBox: '0 0 24 24',
    iconPath: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
  },
  {
    id: 'view-registrations',
    label: 'View Registrations',
    description: 'See who registered for your events',
    variant: 'default',
    iconClass: 'bg-slate-100',
    iconViewBox: '0 0 24 24',
    iconPath: 'M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 006-6V4a6 6 0 00-6-6H6a6 6 0 00-6 6v10a6 6 0 006 6z'
  }
])

// ========================================
// COMPUTED PROPERTIES - Dynamic labels
// ========================================

const heroTitle = computed(() => {
  if (!authStore.isAuthenticated) {
    return 'Discover Events That Matter'
  }
  if (authStore.isAdmin) {
    return 'Welcome Back, Event Manager'
  }
  return 'Welcome Back'
})

const heroSubtitle = computed(() => {
  if (!authStore.isAuthenticated) {
    return 'Join thousands of professionals connecting at amazing events. Learn, network, and grow your career.'
  }
  if (authStore.isAdmin) {
    return 'Create engaging events and connect with your community. Start building your next event now.'
  }
  return 'Discover and register for events that match your interests.'
})

const primaryCtaLabel = computed(() => {
  if (!authStore.isAuthenticated) return 'Explore Events'
  if (authStore.isAdmin) return 'Go to Dashboard'
  return 'Browse Events'
})

const secondaryCtaLabel = computed(() => {
  if (!authStore.isAuthenticated) return 'Sign Up'
  if (authStore.isAdmin) return 'Create Event'
  return 'My Registrations'
})

const ctaSectionTitle = computed(() => {
  if (!authStore.isAuthenticated) {
    return 'Join EventFlow Today'
  }
  if (authStore.isAdmin) {
    return 'Ready to create more events?'
  }
  return 'Never Miss an Event'
})

const ctaSectionSubtitle = computed(() => {
  if (!authStore.isAuthenticated) {
    return 'Sign up now to discover and register for amazing events in your area.'
  }
  if (authStore.isAdmin) {
    return 'Create your next event and start building your community.'
  }
  return 'Register for upcoming events and grow your network.'
})

const ctaSectionPrimaryLabel = computed(() => {
  if (!authStore.isAuthenticated) return 'Sign Up Free'
  if (authStore.isAdmin) return 'Create Event'
  return 'View Registrations'
})

// ========================================
// METHODS
// ========================================

const handlePrimaryCta = () => {
  if (!authStore.isAuthenticated) {
    router.push('/events')
  } else if (authStore.isAdmin) {
    router.push('/admin')
  } else {
    router.push('/events')
  }
}

const handleSecondaryCta = () => {
  if (!authStore.isAuthenticated) {
    router.push('/register')
  } else if (authStore.isAdmin) {
    router.push('/admin/create')
  } else {
    router.push('/dashboard')
  }
}

const handleCreateEventClick = () => {
  if (authStore.isAdmin) {
    router.push('/admin/create')
  }
}

const handleCtaSectionPrimary = () => {
  if (!authStore.isAuthenticated) {
    router.push('/register')
  } else if (authStore.isAdmin) {
    router.push('/admin/create')
  } else {
    router.push('/dashboard')
  }
}

const handleAdminAction = (action) => {
  switch (action.id) {
    case 'create-event':
      router.push('/admin/create')
      break
    case 'manage-events':
      router.push('/admin')
      break
    case 'view-registrations':
      router.push('/admin')
      break
  }
}

const handleCategorySelect = (category) => {
  selectedCategory.value = selectedCategory.value === category ? null : category
  // Navigate to events page with category filter
  router.push(`/events?category=${category}`)
}

const handleCreateFirstEvent = () => {
  router.push('/admin/create')
}

const fetchUpcomingEvents = async () => {
  eventsLoading.value = true
  eventsError.value = null

  try {
    const response = await api.get('/api/events', {
      params: {
        limit: 6,
        offset: 0,
        sort: 'date_asc'
      }
    })

    upcomingEvents.value = response.data.data || []
    platformStats.value.totalEvents = response.data.pagination?.total || 0
  } catch (err) {
    eventsError.value = 'Failed to load events. Please try again later.'
    console.error('Events fetch error:', err)
  } finally {
    eventsLoading.value = false
  }
}

const fetchUserRegistrations = async () => {
  if (!authStore.isAuthenticated || authStore.isAdmin) return

  registrationsLoading.value = true

  try {
    const userId = authStore.user.id
    const response = await api.get(`/api/registrations/user/${userId}`, {
      params: {
        limit: 3,
        offset: 0
      }
    })

    // Map registrations to extract event data
    userRegistrations.value = (response.data.data || []).map(reg => ({
      id: reg.event?.id || reg.event_id,
      title: reg.event?.title,
      description: reg.event?.description,
      date: reg.event?.date,
      location: reg.event?.location,
      category: reg.event?.category,
      created_by: reg.event?.created_by
    }))
  } catch (err) {
    console.error('Registrations fetch error:', err)
  } finally {
    registrationsLoading.value = false
  }
}

const fetchAdminStats = async () => {
  if (!authStore.isAdmin) return

  try {
    const eventsRes = await api.get('/api/events?limit=100')
    const usersRes = await api.get('/api/users?limit=1')
    const statsRes = await api.get('/api/stats')
    const events = eventsRes.data.data || []
    const now = new Date()

    adminStats.value.totalEvents = eventsRes.data.pagination?.total || 0
    adminStats.value.upcomingEvents = statsRes.data.data?.upcoming_events || events.filter(event => new Date(event.date) > now).length
    adminStats.value.totalRegistrations = statsRes.data.data?.total_registrations || events.reduce((sum, event) => sum + Number(event.registration_count || 0), 0)
    adminStats.value.eventsThisMonth = events.filter((event) => {
      const eventDate = new Date(event.date)
      return eventDate.getFullYear() === now.getFullYear() && eventDate.getMonth() === now.getMonth()
    }).length
    adminStats.value.attendeesThisMonth = adminStats.value.totalRegistrations
    platformStats.value.totalUsers = usersRes.data.pagination?.total || 0
  } catch (err) {
    console.error('Failed to fetch admin stats:', err)
  }
}

const fetchPlatformStats = async () => {
  try {
    const statsRes = await api.get('/api/stats')
    platformStats.value.totalEvents = statsRes.data.data?.total_events || 0
    platformStats.value.totalRegistrations = statsRes.data.data?.total_registrations || 0
    platformStats.value.totalUsers = statsRes.data.data?.total_users || 0
  } catch (err) {
    console.error('Failed to fetch platform stats:', err)
  }
}

// ========================================
// LIFECYCLE
// ========================================

onMounted(async () => {
  await fetchUpcomingEvents()
  await fetchPlatformStats()

  if (authStore.isAuthenticated) {
    await fetchUserRegistrations()
  }

  if (authStore.isAdmin) {
    await fetchAdminStats()
  }
})
</script>
