<template>
  <router-link
    to="/admin/create"
    :class="[
      'inline-flex items-center justify-center px-4 py-3 font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-4',
      variantClasses,
      sizeClasses,
      className
    ]"
    :aria-label="label"
  >
    {{ icon }} {{ label }}
  </router-link>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  name: 'CreateEventButton',
})

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'outline', 'admin'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  label: {
    type: String,
    default: 'Create Event'
  },
  icon: {
    type: String,
    default: '+'
  },
  className: {
    type: String,
    default: ''
  }
})

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-white text-indigo-700 hover:bg-slate-100 focus:ring-indigo-200',
    secondary: 'bg-transparent text-white border-2 border-white hover:bg-white hover:text-indigo-700 focus:ring-indigo-200',
    outline: 'bg-slate-50 text-indigo-700 border-2 border-indigo-200 hover:bg-indigo-50 focus:ring-indigo-200',
    admin: 'bg-indigo-700 text-white hover:bg-indigo-800 focus:ring-indigo-200'
  }
  return variants[props.variant] || variants.primary
})

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-3 text-base',
    lg: 'px-6 py-3 text-base'
  }
  return sizes[props.size] || sizes.md
})
</script>
