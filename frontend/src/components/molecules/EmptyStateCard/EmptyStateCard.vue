<template>
  <div class="bg-white rounded-xl border-2 border-dashed border-slate-300 p-12 text-center">
    <!-- Icon -->
    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
      <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath" />
      </svg>
    </div>

    <!-- Title -->
    <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ title }}</h3>

    <!-- Description -->
    <p class="text-slate-600 mb-6 max-w-sm mx-auto">{{ description }}</p>

    <!-- CTA Button (optional) -->
    <button
      v-if="ctaLabel"
      @click="handleCta"
      class="px-6 py-2.5 bg-indigo-700 text-white rounded-lg font-semibold text-sm hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-200 transition-colors duration-200"
    >
      {{ ctaLabel }}
    </button>
  </div>
</template>

<script setup>
defineOptions({
  name: 'EmptyStateCard',
})

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    default: 'inbox',
    validator: (value) => ['inbox', 'calendar', 'users', 'check'].includes(value)
  },
  ctaLabel: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['cta-click'])

// Icon paths
const iconPath = (() => {
  const icons = {
    inbox: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
    calendar: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    users: 'M12 4.354a4 4 0 110 8.308 4 4 0 010-8.308M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    check: 'M5 13l4 4L19 7'
  }
  return icons[props.icon] || icons.inbox
})()

const handleCta = () => {
  emit('cta-click')
}
</script>
