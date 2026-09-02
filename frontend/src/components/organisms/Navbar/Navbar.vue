<template>
  <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
    <nav class="max-w-6xl mx-auto px-4 md:px-6 flex items-center justify-between h-16 md:h-20" aria-label="Main navigation">
      <!-- Brand / Logo -->
      <router-link to="/" class="flex items-center gap-2 group">
        <div class="w-8 h-8 md:w-10 md:h-10 bg-indigo-700 rounded-lg flex items-center justify-center">
          <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
          </svg>
        </div>
        <span class="text-lg md:text-xl font-bold text-indigo-700 group-hover:text-indigo-800 transition-colors">
          EventFlow
        </span>
      </router-link>

      <!-- Desktop Navigation -->
      <div class="hidden md:flex gap-8 items-center flex-1 justify-center">
        <router-link
          to="/"
          class="text-slate-900 font-medium text-sm hover:text-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none rounded px-2 py-1 transition-colors relative group"
          :class="{ 'text-indigo-700 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-indigo-700 after:rounded-full': route.name === 'Home' }"
        >
          Home
        </router-link>
        <router-link
          to="/events"
          class="text-slate-900 font-medium text-sm hover:text-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none rounded px-2 py-1 transition-colors relative group"
          :class="{ 'text-indigo-700 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-indigo-700 after:rounded-full': route.name === 'Events' }"
        >
          Events
        </router-link>
        <router-link
          v-if="isAuthenticated"
          to="/dashboard"
          class="text-slate-900 font-medium text-sm hover:text-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none rounded px-2 py-1 transition-colors relative group"
          :class="{ 'text-indigo-700 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-indigo-700 after:rounded-full': route.name === 'UserDashboard' }"
        >
          My Registrations
        </router-link>
      </div>

      <!-- Desktop Auth & Actions -->
      <div class="hidden md:flex gap-3 items-center">
        <!-- Admin Dashboard Link -->
        <router-link
          v-if="isAdmin"
          to="/admin"
          class="text-slate-900 font-medium text-sm hover:text-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none rounded px-3 py-1.5 transition-colors"
        >
          Admin
        </router-link>

        <!-- User Menu or Auth Buttons -->
        <div v-if="isAuthenticated" class="flex items-center gap-3 border-l border-slate-200 pl-3">
          <div class="text-right hidden sm:block">
            <p class="text-sm font-medium text-slate-900">{{ userDisplayName }}</p>
            <p v-if="isAdmin" class="text-xs text-indigo-700 font-semibold">Admin</p>
          </div>
          
          <!-- User Dropdown Menu -->
          <div class="relative group">
            <button
              class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-semibold hover:ring-2 hover:ring-indigo-300 transition-all"
            >
              {{ getInitials(userDisplayName) }}
            </button>
            
            <!-- Dropdown Panel -->
            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg border border-slate-200 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
              <router-link
                to="/profile"
                class="block px-4 py-3 text-sm text-slate-900 hover:bg-slate-50 border-b border-slate-200 font-medium"
              >
                My Profile
              </router-link>
              <router-link
                v-if="isAdmin"
                to="/admin/users"
                class="block px-4 py-3 text-sm text-slate-900 hover:bg-slate-50 border-b border-slate-200 font-medium"
              >
                Manage Users
              </router-link>
              <button
                @click="handleLogout"
                class="w-full text-left px-4 py-3 text-sm text-red-700 hover:bg-red-50 font-medium"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
        <div v-else class="flex gap-2">
          <router-link
            to="/login"
            class="px-4 py-2 rounded-lg text-slate-900 border border-slate-300 bg-white hover:bg-slate-50 font-semibold text-sm transition-colors focus:ring-2 focus:ring-indigo-500 focus:outline-none"
          >
            Login
          </router-link>
          <router-link
            to="/register"
            class="px-4 py-2.5 rounded-lg bg-indigo-700 text-white hover:bg-indigo-800 font-semibold text-sm transition-colors focus:ring-2 focus:ring-indigo-200 focus:outline-none"
          >
            Register
          </router-link>
        </div>
      </div>

      <!-- Mobile Menu Button -->
      <button
        @click="mobileMenuOpen = !mobileMenuOpen"
        :aria-label="mobileMenuOpen ? 'Close menu' : 'Open menu'"
        :aria-expanded="mobileMenuOpen"
        class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg hover:bg-slate-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors"
      >
        <svg class="w-6 h-6" :class="{ 'hidden': mobileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg class="w-6 h-6" :class="{ 'hidden': !mobileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </nav>

    <!-- Mobile Menu -->
    <transition enter-active-class="transition-all duration-200" leave-active-class="transition-all duration-200" enter-from-class="opacity-0 -translate-y-2" leave-to-class="opacity-0 -translate-y-2">
      <div v-show="mobileMenuOpen" class="md:hidden absolute top-full left-0 right-0 bg-white border-b border-slate-200 shadow-lg">
        <nav class="max-w-6xl mx-auto px-4 py-4 flex flex-col gap-2" aria-label="Mobile navigation">
          <router-link
            to="/"
            @click="mobileMenuOpen = false"
            class="px-4 py-2 rounded-lg text-slate-900 hover:bg-slate-100 font-medium transition-colors"
          >
            Home
          </router-link>
          <router-link
            to="/events"
            @click="mobileMenuOpen = false"
            class="px-4 py-2 rounded-lg text-slate-900 hover:bg-slate-100 font-medium transition-colors"
          >
            Events
          </router-link>
          <router-link
            v-if="isAuthenticated"
            to="/dashboard"
            @click="mobileMenuOpen = false"
            class="px-4 py-2 rounded-lg text-slate-900 hover:bg-slate-100 font-medium transition-colors"
          >
            My Registrations
          </router-link>
          <router-link
            v-if="isAdmin"
            to="/admin"
            @click="mobileMenuOpen = false"
            class="px-4 py-2 rounded-lg text-indigo-700 hover:bg-indigo-50 font-medium transition-colors"
          >
            Admin Dashboard
          </router-link>

          <div class="border-t border-slate-200 pt-3 mt-2 flex flex-col gap-2">
            <div v-if="isAuthenticated">
              <p class="px-4 py-2 text-sm font-semibold text-slate-900">{{ userDisplayName }}</p>
              <p v-if="isAdmin" class="px-4 text-xs text-indigo-700 font-semibold -mt-1 mb-2">Admin</p>
              <router-link
                to="/profile"
                @click="mobileMenuOpen = false"
                class="px-4 py-2 rounded-lg text-slate-900 hover:bg-slate-100 font-medium"
              >
                My Profile
              </router-link>
              <router-link
                v-if="isAdmin"
                to="/admin/users"
                @click="mobileMenuOpen = false"
                class="px-4 py-2 rounded-lg text-slate-900 hover:bg-slate-100 font-medium"
              >
                Manage Users
              </router-link>
              <button
                @click="handleLogout"
                class="w-full px-4 py-2 rounded-lg text-slate-900 border border-slate-300 bg-white hover:bg-slate-50 font-semibold text-sm transition-colors mt-2"
              >
                Logout
              </button>
            </div>
            <div v-else class="flex flex-col gap-2">
              <router-link
                to="/login"
                @click="mobileMenuOpen = false"
                class="px-4 py-2 rounded-lg text-slate-900 border border-slate-300 bg-white hover:bg-slate-50 font-semibold text-sm text-center transition-colors"
              >
                Login
              </router-link>
              <router-link
                to="/register"
                @click="mobileMenuOpen = false"
                class="px-4 py-2.5 rounded-lg bg-indigo-700 text-white hover:bg-indigo-800 font-semibold text-sm text-center transition-colors"
              >
                Register
              </router-link>
            </div>
          </div>
        </nav>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../../stores/auth'

defineOptions({
  name: 'Navbar',
})

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const mobileMenuOpen = ref(false)

const isAuthenticated = computed(() => authStore.isAuthenticated)
const isAdmin = computed(() => authStore.isAdmin)
const userDisplayName = computed(() => {
  const user = authStore.user
  if (!user) return ''
  return user.name || user.full_name || user.email || 'User'
})

const handleLogout = async () => {
  try {
    await authStore.logout()
    mobileMenuOpen.value = false
    router.push('/login')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

const getInitials = (name) => {
  if (!name) return '?'
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}
</script>
