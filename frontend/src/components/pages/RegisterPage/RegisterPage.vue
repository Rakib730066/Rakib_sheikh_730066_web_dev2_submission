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
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900">Create Account</h1>
        <p class="text-slate-600 mt-2">Join EventFlow and discover events</p>
      </div>

      <!-- Alert Messages -->
      <Alert v-if="authStore.error" type="error" :message="authStore.error" closable @close="authStore.error = ''" class="mb-6" />

      <!-- Register Form -->
      <form @submit.prevent="handleRegister" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8 flex flex-col gap-6">
        <!-- Full Name Input -->
        <Input
          id="name"
          label="Full Name"
          type="text"
          v-model="form.name"
          placeholder="John Doe"
          required
          :error="formErrors.name"
          helper-text="Your full name as you want it to appear"
        />

        <!-- Email Input -->
        <Input
          id="email"
          label="Email Address"
          type="email"
          v-model="form.email"
          placeholder="you@example.com"
          required
          :error="formErrors.email"
          helper-text="We'll use this to send event updates"
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
          helper-text="At least 8 characters recommended"
        />

        <!-- Confirm Password Input -->
        <div>
          <Input
            id="confirmPassword"
            label="Confirm Password"
            type="password"
            v-model="form.confirmPassword"
            placeholder="Confirm password"
            required
            :error="passwordMismatchError || formErrors.confirmPassword"
            helper-text="Must match your password above"
          />
        </div>

        <!-- Submit Button -->
        <Button
          variant="primary"
          size="lg"
          type="submit"
          :disabled="authStore.isLoading || form.password !== form.confirmPassword || !form.name || !form.email"
          class="w-full mt-2"
        >
          {{ authStore.isLoading ? 'Creating Account...' : 'Create Account' }}
        </Button>

        <!-- Divider -->
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-slate-600">Already registered?</span>
          </div>
        </div>

        <!-- Login Link -->
        <router-link
          to="/login"
          class="text-center px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 hover:bg-slate-50 font-semibold text-sm transition-colors focus:ring-4 focus:ring-indigo-200 focus:outline-none"
        >
          Sign In Instead
        </router-link>
      </form>
    </div>
  </main>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useAuthStore } from '../../../stores/auth.js'
import { useRouter } from 'vue-router'
import Input from '../../atoms/Input/Input.vue'
import Button from '../../atoms/Button/Button.vue'
import Alert from '../../atoms/Alert/Alert.vue'

defineOptions({
  name: 'RegisterPage',
})

const authStore = useAuthStore()
const router = useRouter()
const formErrors = ref({})

const form = reactive({
  name: '',
  email: '',
  password: '',
  confirmPassword: ''
})

const passwordMismatchError = computed(() => {
  if (form.password && form.confirmPassword && form.password !== form.confirmPassword) {
    return 'Passwords do not match'
  }
  return ''
})

const handleRegister = async () => {
  try {
    formErrors.value = {}
    await authStore.register(form.name, form.email, form.password)
    router.push('/')
  } catch (err) {
    console.error('Registration failed:', err)
    // Error is already set in authStore.error
  }
}
</script>
