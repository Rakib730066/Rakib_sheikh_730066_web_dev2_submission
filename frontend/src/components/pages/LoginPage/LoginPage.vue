<template>
  <main class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <!-- Logo & Brand -->
      <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
          <div class="w-12 h-12 bg-indigo-700 rounded-lg flex items-center justify-center">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
              <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
            </svg>
          </div>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900">Sign In</h1>
        <p class="text-slate-600 mt-2">Welcome to EventFlow</p>
      </div>

      <!-- Alert Messages -->
      <Alert v-if="authStore.error" type="error" :message="authStore.error" closable @close="authStore.error = ''" class="mb-6" />

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8 flex flex-col gap-6">
        <!-- Email Input -->
        <Input
          id="email"
          label="Email Address"
          type="email"
          v-model="form.email"
          placeholder="you@example.com"
          required
          :error="formErrors.email"
          helper-text="Enter your registered email address"
        />

        <!-- Password Input -->
        <Input
          id="password"
          label="Password"
          type="password"
          v-model="form.password"
          placeholder="Password"
          required
          :error="formErrors.password"
          helper-text="Your secure password"
        />

        <!-- Submit Button -->
        <Button
          variant="primary"
          size="lg"
          type="submit"
          :disabled="authStore.isLoading"
          class="w-full mt-2"
        >
          {{ authStore.isLoading ? 'Signing in...' : 'Sign In' }}
        </Button>

        <!-- Divider -->
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-slate-600">Don't have an account?</span>
          </div>
        </div>

        <!-- Register Link -->
        <router-link
          to="/register"
          class="text-center px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 hover:bg-slate-50 font-semibold text-sm transition-colors focus:ring-4 focus:ring-indigo-200 focus:outline-none"
        >
          Create an Account
        </router-link>
      </form>

      <!-- Demo Credentials Info -->
      <div class="mt-6 bg-indigo-50 border border-indigo-200 rounded-xl p-4">
        <p class="font-semibold text-indigo-900 text-sm mb-2">Demo Account:</p>
        <p class="text-indigo-800 text-xs mono">Email: john@example.com</p>
        <p class="text-indigo-800 text-xs mono">Password: password123</p>
      </div>
    </div>
  </main>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useAuthStore } from '../../../stores/auth.js'
import { useRouter } from 'vue-router'
import Input from '../../atoms/Input/Input.vue'
import Button from '../../atoms/Button/Button.vue'
import Alert from '../../atoms/Alert/Alert.vue'

defineOptions({
  name: 'LoginPage',
})

const authStore = useAuthStore()
const router = useRouter()
const formErrors = ref({})

const form = reactive({
  email: '',
  password: ''
})

const handleLogin = async () => {
  try {
    // Clear previous errors
    formErrors.value = {}
    await authStore.login(form.email, form.password)
    router.push('/')
  } catch (err) {
    console.error('Login failed:', err)
    // Error is handled in authStore and shown in Alert component
  }
}
</script>
