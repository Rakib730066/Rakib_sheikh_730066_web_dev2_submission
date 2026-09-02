<template>
  <div class="flex flex-wrap gap-3">
    <button
      v-for="category in categories"
      :key="category"
      @click="selectCategory(category)"
      :class="[
        'px-4 py-2.5 rounded-full font-semibold text-sm transition-all duration-200 whitespace-nowrap',
        selectedCategory === category
          ? 'bg-indigo-700 text-white shadow-md'
          : 'bg-white text-slate-700 border border-slate-300 hover:border-indigo-400 hover:bg-indigo-50'
      ]"
    >
      {{ category }}
    </button>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue'

defineOptions({
  name: 'CategoryChips',
})

defineProps({
  categories: {
    type: Array,
    required: true,
    validator: (value) => Array.isArray(value) && value.every(cat => typeof cat === 'string')
  },
  selectedCategory: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['category-select'])

const selectCategory = (category) => {
  emit('category-select', category)
}
</script>
