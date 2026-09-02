<template>
  <main class="min-h-screen bg-slate-50">
    <div class="max-w-2xl mx-auto px-4 md:px-6 lg:px-8 py-8 md:py-12">
      <!-- Page Header -->
      <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900">
          {{ isViewingOtherUser ? 'User Profile' : 'My Profile' }}
        </h1>
        <p class="text-slate-600 mt-2">
          {{ isViewingOtherUser ? 'View account details for this user' : 'View and manage your account details' }}
        </p>
      </header>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center py-24">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700 mb-4"></div>
      </div>

      <!-- Error State -->
      <Alert v-else-if="error" type="error" :message="error" class="mb-6" />

      <!-- Profile Content -->
      <div v-else class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8">
        <!-- Avatar & Basic Info -->
        <div class="flex items-center gap-6 pb-8 border-b border-slate-200">
          <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">
            <span class="text-2xl font-bold text-white">{{ getInitials(profile.name) }}</span>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-slate-900">{{ profile.name }}</h2>
            <p class="text-slate-600">{{ profile.email }}</p>
            <Badge :variant="profile.role === 'admin' ? 'danger' : 'primary'" size="sm" class="mt-2">
              {{ profile.role === 'admin' ? 'Administrator' : 'User' }}
            </Badge>
          </div>
        </div>

        <!-- Edit Form -->
        <div v-if="isEditing" class="pt-8">
          <h3 class="text-lg font-semibold text-slate-900 mb-6">Edit Profile</h3>
          
          <form @submit.prevent="saveProfile" class="space-y-6">
            <!-- Name Field -->
            <div>
              <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
              <input
                v-model="formData.name"
                id="name"
                type="text"
                class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors"
                placeholder="Enter your full name"
              />
              <p v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name }}</p>
            </div>

            <!-- Email Field -->
            <div>
              <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
              <input
                v-model="formData.email"
                id="email"
                type="email"
                class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors"
                placeholder="Enter your email address"
              />
              <p v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
              <Button
                type="submit"
                variant="primary"
                size="lg"
                :disabled="isSaving"
                class="flex-1"
              >
                {{ isSaving ? 'Saving...' : 'Save Changes' }}
              </Button>
              <Button
                type="button"
                variant="outline"
                size="lg"
                @click="cancelEdit"
                class="flex-1"
              >
                Cancel
              </Button>
            </div>
          </form>
        </div>

        <!-- View Mode -->
        <div v-else class="pt-8">
          <div class="space-y-6">
            <!-- Name -->
            <div>
              <p class="text-sm font-semibold text-slate-600 uppercase mb-1">Full Name</p>
              <p class="text-lg text-slate-900">{{ profile.name }}</p>
            </div>

            <!-- Email -->
            <div>
              <p class="text-sm font-semibold text-slate-600 uppercase mb-1">Email Address</p>
              <p class="text-lg text-slate-900">{{ profile.email }}</p>
            </div>

            <!-- Role -->
            <div>
              <p class="text-sm font-semibold text-slate-600 uppercase mb-1">Account Type</p>
              <p class="text-lg text-slate-900">{{ profile.role === 'admin' ? 'Administrator' : 'User' }}</p>
            </div>

            <!-- Member Since -->
            <div>
              <p class="text-sm font-semibold text-slate-600 uppercase mb-1">Member Since</p>
              <p class="text-lg text-slate-900">{{ formatDate(profile.created_at) }}</p>
            </div>

            <!-- Edit Button -->
            <div v-if="!isViewingOtherUser" class="pt-4">
              <Button
                variant="primary"
                size="lg"
                @click="startEdit"
                class="w-full"
              >
                Edit Profile
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../../../stores/auth.js'
import api from '../../../utils/axiosConfig.js'
import Button from '../../atoms/Button/Button.vue'
import Badge from '../../atoms/Badge/Badge.vue'
import Alert from '../../atoms/Alert/Alert.vue'

defineOptions({
  name: 'UserProfilePage',
})

const route = useRoute()
const authStore = useAuthStore()
const viewedUserId = computed(() => route.params.userId ? Number(route.params.userId) : null)
const isViewingOtherUser = computed(() => {
  return Boolean(viewedUserId.value && viewedUserId.value !== Number(authStore.user?.id))
})

// Data
const profile = ref({
  name: '',
  email: '',
  role: 'user',
  created_at: null
})
const isLoading = ref(false)
const isEditing = ref(false)
const isSaving = ref(false)
const error = ref(null)
const formData = ref({
  name: '',
  email: ''
})
const errors = ref({})

// Methods
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  })
}

const getInitials = (name) => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const fetchProfile = async () => {
  isLoading.value = true
  error.value = null

  try {
    const endpoint = viewedUserId.value ? `/api/users/${viewedUserId.value}` : '/api/users/profile'
    const response = await api.get(endpoint)
    profile.value = response.data.data
    formData.value = {
      name: profile.value.name,
      email: profile.value.email
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load profile'
    console.error('Profile fetch error:', err)
  } finally {
    isLoading.value = false
  }
}

const startEdit = () => {
  isEditing.value = true
  errors.value = {}
}

const cancelEdit = () => {
  isEditing.value = false
  formData.value = {
    name: profile.value.name,
    email: profile.value.email
  }
  errors.value = {}
}

const validateForm = () => {
  errors.value = {}

  if (!formData.value.name || formData.value.name.trim() === '') {
    errors.value.name = 'Name is required'
  }

  if (!formData.value.email || formData.value.email.trim() === '') {
    errors.value.email = 'Email is required'
  } else if (!isValidEmail(formData.value.email)) {
    errors.value.email = 'Invalid email address'
  }

  return Object.keys(errors.value).length === 0
}

const isValidEmail = (email) => {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

const saveProfile = async () => {
  if (isViewingOtherUser.value) {
    return
  }

  if (!validateForm()) {
    return
  }

  isSaving.value = true
  error.value = null

  try {
    const response = await api.put('/api/users/profile', {
      name: formData.value.name,
      email: formData.value.email
    })

    profile.value = response.data.data
    isEditing.value = false
    alert('Profile updated successfully!')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update profile'
    console.error('Profile update error:', err)
  } finally {
    isSaving.value = false
  }
}

watch(() => route.params.userId, fetchProfile, { immediate: true })
</script>
