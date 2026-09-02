<template>
  <button
    :class="[
      'inline-flex items-center justify-center font-semibold rounded-xl transition-colors duration-200',
      'focus:outline-none focus:ring-4',
      'disabled:cursor-not-allowed',
      sizeClasses,
      variantClasses,
      focusClasses,
      disabledClasses,
    ]"
    :disabled="disabled"
    v-bind="$attrs"
  >
    <slot>{{ label }}</slot>
  </button>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  name: 'Button',
})

const props = defineProps({
  label: {
    type: String,
    default: '',
  },
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'danger', 'outline'].includes(value),
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  // Support for old props for backwards compatibility
  primary: {
    type: Boolean,
    default: false,
  },
  backgroundColor: {
    type: String,
    default: null,
  },
})

// Size classes
const sizeClasses = computed(() => {
  const sizes = {
    sm: 'px-3 py-1.5 text-sm min-h-8',
    md: 'px-4 py-2.5 text-base min-h-10',
    lg: 'px-6 py-3 text-base min-h-12',
    // Old sizes mapping
    small: 'px-3 py-1.5 text-sm min-h-8',
    medium: 'px-4 py-2.5 text-base min-h-10',
    large: 'px-6 py-3 text-base min-h-12',
  }
  return sizes[props.size] || sizes['md']
})

// Variant classes
const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-indigo-700 text-white hover:bg-indigo-800',
    secondary: 'bg-white text-slate-900 border border-slate-300 hover:bg-slate-50',
    danger: 'bg-red-600 text-white hover:bg-red-700',
    outline: 'bg-transparent text-indigo-700 border border-indigo-700 hover:bg-indigo-50',
  }
  return variants[props.variant] || variants['primary']
})

// Focus classes
const focusClasses = computed(() => {
  const focuses = {
    primary: 'focus:ring-indigo-200',
    secondary: 'focus:ring-indigo-200',
    danger: 'focus:ring-red-200',
    outline: 'focus:ring-indigo-200',
  }
  return focuses[props.variant] || focuses['primary']
})

// Disabled classes
const disabledClasses = computed(() => {
  if (props.disabled) {
    return props.variant === 'secondary'
      ? 'opacity-50'
      : 'bg-slate-400'
  }
  return ''
})
</script>

