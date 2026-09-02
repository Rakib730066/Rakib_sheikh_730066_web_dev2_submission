import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../utils/axiosConfig.js'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const isLoading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  const register = async (name, email, password) => {
    isLoading.value = true
    error.value = null
    try {
      const { data } = await api.post('/api/auth/register', {
        name,
        email,
        password
      })
      token.value = data.data.token
      user.value = data.data.user
      localStorage.setItem('auth_token', token.value)
      return data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
      throw error.value
    } finally {
      isLoading.value = false
    }
  }

  const login = async (email, password) => {
    isLoading.value = true
    error.value = null
    try {
      const { data } = await api.post('/api/auth/login', {
        email,
        password
      })
      token.value = data.data.token
      user.value = data.data.user
      localStorage.setItem('auth_token', token.value)
      return data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
      throw error.value
    } finally {
      isLoading.value = false
    }
  }

  const fetchProfile = async () => {
    if (!token.value) return null
    isLoading.value = true
    try {
      const { data } = await api.get('/api/auth/me')
      user.value = data.data.user
      return user.value
    } catch (err) {
      logout()
      return null
    } finally {
      isLoading.value = false
    }
  }

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
  }

  const initialize = async () => {
    if (token.value) {
      await fetchProfile()
    }
  }

  return {
    // State
    user,
    token,
    isLoading,
    error,
    // Computed
    isAuthenticated,
    isAdmin,
    // Methods
    register,
    login,
    logout,
    fetchProfile,
    initialize
  }
})
