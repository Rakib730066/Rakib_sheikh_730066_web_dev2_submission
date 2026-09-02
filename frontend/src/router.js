import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from './stores/auth.js'

// Lazy-load page components
const HomePage = () => import('./components/pages/HomePage/HomePage.vue')
const EventsPage = () => import('./components/pages/EventsPage/EventsPage.vue')
const EventDetailPage = () => import('./components/pages/EventDetailPage/EventDetailPage.vue')
const LoginPage = () => import('./components/pages/LoginPage/LoginPage.vue')
const RegisterPage = () => import('./components/pages/RegisterPage/RegisterPage.vue')
const AdminCreateEventPage = () => import('./components/pages/AdminCreateEventPage/AdminCreateEventPage.vue')
const AdminEditEventPage = () => import('./components/pages/AdminEditEventPage/AdminEditEventPage.vue')
const AdminDashboardPage = () => import('./components/pages/AdminDashboardPage/AdminDashboardPage.vue')
const UserDashboardPage = () => import('./components/pages/UserDashboardPage/UserDashboardPage.vue')
const UserProfilePage = () => import('./components/pages/UserProfilePage/UserProfilePage.vue')
const AdminUserManagementPage = () => import('./components/pages/AdminUserManagementPage/AdminUserManagementPage.vue')

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage,
    meta: { requiresAuth: false }
  },
  {
    path: '/events',
    name: 'Events',
    component: EventsPage,
    meta: { requiresAuth: false }
  },
  {
    path: '/events/:id',
    name: 'EventDetail',
    component: EventDetailPage,
    meta: { requiresAuth: false },
    props: true
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginPage,
    meta: { requiresAuth: false, guestOnly: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterPage,
    meta: { requiresAuth: false, guestOnly: true }
  },
  {
    path: '/dashboard',
    name: 'UserDashboard',
    component: UserDashboardPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'UserProfile',
    component: UserProfilePage,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: AdminDashboardPage,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/users',
    name: 'AdminUserManagement',
    component: AdminUserManagementPage,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/users/:userId',
    name: 'AdminUserDetail',
    component: UserProfilePage,
    meta: { requiresAuth: true, requiresAdmin: true },
    props: true
  },
  {
    path: '/admin/create',
    name: 'AdminCreateEvent',
    component: AdminCreateEventPage,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/edit/:id',
    name: 'AdminEditEvent',
    component: AdminEditEventPage,
    meta: { requiresAuth: true, requiresAdmin: true },
    props: true
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

/**
 * Route guards for authentication and authorization
 */
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()

  // Initialize auth from stored token on first load
  if (!authStore.user && authStore.token) {
    await authStore.fetchProfile()
  }

  const requiresAuth = to.meta.requiresAuth
  const requiresAdmin = to.meta.requiresAdmin
  const guestOnly = to.meta.guestOnly

  // Redirect guests from guest-only pages
  if (guestOnly && authStore.isAuthenticated) {
    return next({ name: 'Events' })
  }

  // Redirect unauthenticated users
  if (requiresAuth && !authStore.isAuthenticated) {
    return next({ name: 'Login', query: { redirect: to.fullPath } })
  }

  // Redirect non-admins from admin pages
  if (requiresAdmin && !authStore.isAdmin) {
    return next({ name: 'Events' })
  }

  next()
})

export default router
