<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <!-- Navigation Bar -->
    <Navbar />

    <!-- Page Content -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import Navbar from './components/organisms/Navbar/Navbar.vue'
import Footer from './components/organisms/Footer/Footer.vue'

defineOptions({
  name: 'App',
})
</script>

<style scoped>
/* Smooth transitions */
:deep(.router-view-transition-enter-active),
:deep(.router-view-transition-leave-active) {
  transition: opacity 0.3s ease;
}

:deep(.router-view-transition-enter-from),
:deep(.router-view-transition-leave-to) {
  opacity: 0;
}
</style>
