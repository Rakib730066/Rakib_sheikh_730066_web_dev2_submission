<template>
  <span :class="[classes, variantClasses]">
    <slot></slot>
  </span>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  name: 'Badge',
})

const props = defineProps({
  variant: {
    type: String,
    default: 'neutral',
    validator: (value) => ['neutral', 'primary', 'success', 'warning', 'error', 'info'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
})

const classes = computed(() => {
  const base = 'inline-flex items-center font-semibold rounded-full'
  
  const sizes = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-3 py-1 text-xs',
    lg: 'px-4 py-1.5 text-sm',
  }
  
  return `${base} ${sizes[props.size]}`
})

const variantClasses = computed(() => {
  const variants = {
    neutral: 'bg-slate-100 text-slate-800',
    primary: 'bg-indigo-100 text-indigo-800',
    success: 'bg-emerald-100 text-emerald-800',
    warning: 'bg-amber-100 text-amber-800',
    error: 'bg-red-100 text-red-800',
    info: 'bg-sky-100 text-sky-800',
  }
  
  return variants[props.variant] || variants['neutral']
})
</script>
