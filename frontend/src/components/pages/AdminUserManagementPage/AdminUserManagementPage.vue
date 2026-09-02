<template>
  <main class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 md:px-6 lg:px-8 py-8 md:py-12">
      <!-- Page Header -->
      <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900">User Management</h1>
        <p class="text-slate-600 mt-2">Manage all users in the system</p>
      </header>

      <!-- Stats -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <div class="text-2xl font-bold text-indigo-700">{{ totalUsers }}</div>
            <div class="text-sm text-slate-600 font-medium">Total Users</div>
          </div>
          <div>
            <div class="text-2xl font-bold text-slate-900">{{ adminCount }}</div>
            <div class="text-sm text-slate-600 font-medium">Administrators</div>
          </div>
          <div>
            <div class="text-2xl font-bold text-emerald-700">{{ regularUserCount }}</div>
            <div class="text-sm text-slate-600 font-medium">Regular Users</div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center py-24">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-700"></div>
      </div>

      <!-- Error State -->
      <Alert v-else-if="error" type="error" :message="error" class="mb-6" />

      <!-- Users Table -->
      <div v-else-if="users.length > 0" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Name</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Email</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Role</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900">Member Since</th>
                <th class="px-6 py-4 text-right text-sm font-semibold text-slate-900">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center flex-shrink-0">
                      <span class="text-sm font-bold text-white">{{ getInitials(user.name) }}</span>
                    </div>
                    <span class="font-medium text-slate-900">{{ user.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-slate-700">{{ user.email }}</td>
                <td class="px-6 py-4">
                  <Badge :variant="user.role === 'admin' ? 'danger' : 'primary'" size="sm">
                    {{ user.role === 'admin' ? 'Admin' : 'User' }}
                  </Badge>
                </td>
                <td class="px-6 py-4 text-slate-700">{{ formatDate(user.created_at) }}</td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      @click="viewUser(user.id)"
                      class="px-3 py-1.5 rounded-lg text-sm text-indigo-700 hover:bg-indigo-50 font-medium transition-colors"
                      title="View user"
                    >
                      View
                    </button>
                    <button
                      v-if="authStore.user.id !== user.id"
                      @click="deleteUserConfirm(user.id, user.name)"
                      class="px-3 py-1.5 rounded-lg text-sm text-red-700 hover:bg-red-50 font-medium transition-colors"
                      title="Delete user"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
          <p class="text-sm text-slate-600">Showing {{ (currentPage - 1) * limit + 1 }} to {{ Math.min(currentPage * limit, totalUsers) }} of {{ totalUsers }} users</p>
          <div class="flex gap-2">
            <button
              @click="previousPage"
              :disabled="currentPage === 1"
              class="px-3 py-2 rounded-lg border border-slate-300 text-slate-900 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              Previous
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage >= totalPages"
              class="px-3 py-2 rounded-lg border border-slate-300 text-slate-900 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-xl border border-slate-200 shadow-sm p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 8.048M12 4.354L7.773 8.427M12 4.354l4.227 4.073M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
        </svg>
        <h3 class="text-lg font-semibold text-slate-900 mb-2">No users found</h3>
        <p class="text-slate-600">There are no users in the system yet.</p>
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
  name: 'AdminUserManagementPage',
})

const router = useRouter()
const authStore = useAuthStore()

// Data
const users = ref([])
const totalUsers = ref(0)
const isLoading = ref(false)
const error = ref(null)
const isDeleting = ref(false)
const currentPage = ref(1)
const limit = 10

// Computed
const totalPages = computed(() => Math.ceil(totalUsers.value / limit))

const adminCount = computed(() => {
  return users.value.filter(u => u.role === 'admin').length
})

const regularUserCount = computed(() => {
  return users.value.filter(u => u.role !== 'admin').length
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

const getInitials = (name) => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

const fetchUsers = async () => {
  isLoading.value = true
  error.value = null

  try {
    const offset = (currentPage.value - 1) * limit
    const response = await api.get('/api/users', {
      params: {
        limit,
        offset
      }
    })

    users.value = response.data.data || []
    totalUsers.value = response.data.pagination?.total || 0
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load users'
    console.error('Users fetch error:', err)
  } finally {
    isLoading.value = false
  }
}

const viewUser = (userId) => {
  router.push(`/admin/users/${userId}`)
}

const deleteUserConfirm = async (userId, userName) => {
  if (!confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
    return
  }

  isDeleting.value = true
  try {
    await api.delete(`/api/users/${userId}`)
    // Refresh the list
    currentPage.value = 1
    await fetchUsers()
    alert('User deleted successfully')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to delete user'
    console.error('Delete user error:', err)
  } finally {
    isDeleting.value = false
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    fetchUsers()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    fetchUsers()
  }
}

// Lifecycle
onMounted(fetchUsers)
</script>
