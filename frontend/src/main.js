import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router.js'
import { useAuthStore } from './stores/auth.js'

// Import axios config to register interceptors
import './utils/axiosConfig.js'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Initialize auth on app startup, then mount.
// (Avoids top-level await, which breaks the production build target.)
const authStore = useAuthStore()
authStore.initialize().finally(() => {
  app.mount('#app')
})