<template>
  <footer class="bg-slate-900 text-slate-200 mt-12">
    <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-8 md:py-12">
      <!-- Footer Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 mb-8">
        <!-- Brand Section -->
        <div>
          <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
              </svg>
            </div>
            <span class="text-lg font-bold text-white">EventFlow</span>
          </div>
          <p class="text-slate-400 text-sm leading-relaxed">
            Smart event management made simple. Create, manage, and discover events with ease.
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="font-semibold text-white text-sm mb-4">Navigation</h3>
          <ul class="space-y-2">
            <li>
              <router-link to="/" class="text-slate-300 hover:text-white transition-colors text-sm">
                Home
              </router-link>
            </li>
            <li>
              <router-link to="/events" class="text-slate-300 hover:text-white transition-colors text-sm">
                Browse Events
              </router-link>
            </li>
            <li v-if="isAuthenticated">
              <router-link to="/dashboard" class="text-slate-300 hover:text-white transition-colors text-sm">
                My Registrations
              </router-link>
            </li>
            <li v-if="isAuthenticated">
              <router-link to="/profile" class="text-slate-300 hover:text-white transition-colors text-sm">
                My Profile
              </router-link>
            </li>
            <li v-if="isAdmin">
              <router-link to="/admin" class="text-slate-300 hover:text-white transition-colors text-sm">
                Admin Dashboard
              </router-link>
            </li>
            <li v-if="isAdmin">
              <router-link to="/admin/users" class="text-slate-300 hover:text-white transition-colors text-sm">
                Manage Users
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Features -->
        <div>
          <h3 class="font-semibold text-white text-sm mb-4">Features</h3>
          <ul class="space-y-2">
            <li>
              <a href="#" class="text-slate-300 hover:text-white transition-colors text-sm">
                Event Discovery
              </a>
            </li>
            <li>
              <a href="#" class="text-slate-300 hover:text-white transition-colors text-sm">
                Easy Registration
              </a>
            </li>
            <li>
              <a href="#" class="text-slate-300 hover:text-white transition-colors text-sm">
                Admin Tools
              </a>
            </li>
            <li>
              <a href="#" class="text-slate-300 hover:text-white transition-colors text-sm">
                Event Management
              </a>
            </li>
          </ul>
        </div>

        <!-- Legal -->
        <div>
          <h3 class="font-semibold text-white text-sm mb-4">Legal</h3>
          <ul class="space-y-2">
            <li>
              <a href="#" class="text-slate-300 hover:text-white transition-colors text-sm">
                Privacy Policy
              </a>
            </li>
            <li>
              <a href="#" class="text-slate-300 hover:text-white transition-colors text-sm">
                Terms of Service
              </a>
            </li>
            <li>
              <a href="#" class="text-slate-300 hover:text-white transition-colors text-sm">
                Cookie Policy
              </a>
            </li>
            <li>
              <a href="#" class="text-slate-300 hover:text-white transition-colors text-sm">
                Contact Us
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Footer Bottom -->
      <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-slate-400 text-xs">
          &copy; {{ currentYear }} Smart Event Management System. All rights reserved.
        </p>
        <div class="flex gap-4 text-slate-400 text-xs">
          <a href="#" class="hover:text-white transition-colors">
            Privacy
          </a>
          <a href="#" class="hover:text-white transition-colors">
            Terms
          </a>
          <a href="#" class="hover:text-white transition-colors">
            Accessibility
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../../../stores/auth'

defineOptions({
  name: 'Footer',
})

const authStore = useAuthStore()

const isAuthenticated = computed(() => authStore.isAuthenticated)
const isAdmin = computed(() => authStore.isAdmin)

const currentYear = computed(() => new Date().getFullYear())
</script>

