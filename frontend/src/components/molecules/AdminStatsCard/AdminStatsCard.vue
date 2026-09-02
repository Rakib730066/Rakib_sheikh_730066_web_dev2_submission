<template>
  <div class="bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg transition-all duration-200 flex items-start gap-4">
    <!-- Icon -->
    <div :class="['w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0', iconBackgroundClass]">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" :viewBox="iconViewBox" aria-hidden="true">
        <path v-for="(d, i) in iconPaths" :key="i" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="d" />
      </svg>
    </div>

    <!-- Content -->
    <div class="flex-grow">
      <p class="text-sm font-medium text-slate-600 mb-1">{{ label }}</p>
      <div class="flex items-baseline gap-2">
        <div class="text-3xl md:text-4xl font-bold" :class="textColorClass">
          {{ value }}
        </div>
        <span v-if="trend" :class="['text-sm font-semibold', trend.isPositive ? 'text-emerald-600' : 'text-red-600']">
          {{ trend.isPositive ? '↑' : '↓' }} {{ trend.percentage }}%
        </span>
      </div>
      <p v-if="subtitle" class="text-xs text-slate-500 mt-2">{{ subtitle }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  name: 'AdminStatsCard',
})

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  icon: {
    type: String,
    default: 'chart',
    validator: (value) => ['chart', 'users', 'events', 'registrations'].includes(value)
  },
  colorScheme: {
    type: String,
    default: 'indigo',
    validator: (value) => ['indigo', 'emerald', 'blue', 'amber', 'red'].includes(value)
  },
  trend: {
    type: Object,
    default: null,
    validator: (value) => {
      if (!value) return true
      return value.percentage !== undefined && value.isPositive !== undefined
    }
  },
  subtitle: {
    type: String,
    default: null
  }
})

// Icon paths based on type
const iconPaths = computed(() => {
  const icons = {
    chart: [
      'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    ],
    users: [
      'M12 4.354a4 4 0 110 8.308 4 4 0 010-8.308M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
      'M15 12a3 3 0 11-6 0 3 3 0 016 0z'
    ],
    events: [
      'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
    ],
    registrations: [
      'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
    ]
  }
  return icons[props.icon] || icons.chart
})

const iconViewBox = '0 0 24 24'

// Background color based on scheme
const iconBackgroundClass = computed(() => {
  const colors = {
    indigo: 'bg-indigo-100',
    emerald: 'bg-emerald-100',
    blue: 'bg-blue-100',
    amber: 'bg-amber-100',
    red: 'bg-red-100'
  }
  return colors[props.colorScheme] || colors.indigo
})

// Text color based on scheme
const textColorClass = computed(() => {
  const colors = {
    indigo: 'text-indigo-700',
    emerald: 'text-emerald-700',
    blue: 'text-blue-700',
    amber: 'text-amber-700',
    red: 'text-red-700'
  }
  return colors[props.colorScheme] || colors.indigo
})
</script>
