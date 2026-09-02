<template>
  <div class="flex flex-col gap-2">
    <label v-if="label" :for="id" class="block text-sm font-semibold text-slate-900">
      {{ label }}
      <span v-if="required" class="text-red-600">*</span>
    </label>
    <input
      :id="id"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="required"
      :aria-label="ariaLabel || label"
      :aria-describedby="error ? `${id}-error` : undefined"
      :class="[
        'w-full px-4 py-2.5 rounded-lg',
        'border bg-white',
        'text-slate-900 placeholder-slate-500',
        'font-normal text-base',
        'focus:outline-none focus:ring-2 focus:border-indigo-500',
        'disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed',
        'transition-colors duration-200',
        error ? 'border-red-600 focus:ring-red-500' : 'border-slate-300 focus:ring-indigo-500',
      ]"
      @input="$emit('update:modelValue', $event.target.value)"
      v-bind="$attrs"
    />
    <p v-if="error" :id="`${id}-error`" class="text-red-600 text-sm font-medium">
      {{ error }}
    </p>
    <p v-if="helperText && !error" class="text-xs text-slate-600">
      {{ helperText }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  name: 'Input',
  inheritAttrs: false,
})

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  id: {
    type: String,
    required: true,
  },
  label: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: '',
  },
  helperText: {
    type: String,
    default: '',
  },
  ariaLabel: {
    type: String,
    default: '',
  },
})

defineEmits(['update:modelValue'])
</script>
