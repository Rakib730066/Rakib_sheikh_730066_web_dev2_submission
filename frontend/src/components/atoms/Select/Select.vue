<template>
  <div class="flex flex-col gap-2">
    <label v-if="label" :for="id" class="block text-sm font-semibold text-slate-900">
      {{ label }}
      <span v-if="required" class="text-red-600">*</span>
    </label>
    <select
      :id="id"
      :value="modelValue"
      :disabled="disabled"
      :required="required"
      :aria-label="ariaLabel || label"
      :aria-describedby="error ? `${id}-error` : undefined"
      :class="[
        'w-full px-4 py-2.5 rounded-lg',
        'border bg-white',
        'text-slate-900',
        'font-normal text-base',
        'focus:outline-none focus:ring-2 focus:border-indigo-500',
        'disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed',
        'appearance-none cursor-pointer',
        'transition-colors duration-200',
        'pr-10',
        error ? 'border-red-600 focus:ring-red-500' : 'border-slate-300 focus:ring-indigo-500',
      ]"
      :style="{
        backgroundImage: `url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22currentColor%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><polyline points=%2218 15 12 9 6 15%22></polyline></svg>')`,
        backgroundRepeat: 'no-repeat',
        backgroundPosition: 'right 8px center',
        backgroundSize: '1.25rem 1.25rem',
      }"
      @change="$emit('update:modelValue', $event.target.value)"
      v-bind="$attrs"
    >
      <option v-if="placeholder" disabled value="">
        {{ placeholder }}
      </option>
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
    <p v-if="error" :id="`${id}-error`" class="text-red-600 text-sm font-medium">
      {{ error }}
    </p>
    <p v-if="helperText && !error" class="text-xs text-slate-600">
      {{ helperText }}
    </p>
  </div>
</template>

<script setup>
defineOptions({
  name: 'Select',
  inheritAttrs: false,
})

defineProps({
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
  options: {
    type: Array,
    required: true,
    // Format: [{ value: 'val', label: 'Label' }, ...]
  },
  placeholder: {
    type: String,
    default: 'Select an option',
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
