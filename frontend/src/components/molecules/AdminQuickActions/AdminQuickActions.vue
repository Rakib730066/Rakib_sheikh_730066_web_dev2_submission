<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <button
      v-for="action in actions"
      :key="action.id"
      @click="handleAction(action)"
      :class="[
        'p-4 rounded-lg border-2 transition-all duration-200 flex items-center gap-3',
        'hover:shadow-md hover:-translate-y-0.5 focus:outline-none focus:ring-4',
        action.variant === 'primary'
          ? 'bg-indigo-50 border-indigo-300 hover:border-indigo-400 focus:ring-indigo-200'
          : 'bg-white border-slate-200 hover:border-slate-300 focus:ring-slate-200'
      ]"
    >
      <div :class="['w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0', action.iconClass]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" :viewBox="action.iconViewBox" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="action.iconPath" />
        </svg>
      </div>
      <div class="text-left">
        <div class="font-semibold text-sm text-slate-900">{{ action.label }}</div>
        <div class="text-xs text-slate-600">{{ action.description }}</div>
      </div>
    </button>
  </div>
</template>

<script setup>
defineOptions({
  name: 'AdminQuickActions',
})

defineProps({
  actions: {
    type: Array,
    required: true,
    validator: (value) => Array.isArray(value) && value.every(action => 
      action.id && action.label && action.description && action.iconPath
    )
  }
})

const emit = defineEmits(['action-click'])

const handleAction = (action) => {
  emit('action-click', action)
}
</script>
