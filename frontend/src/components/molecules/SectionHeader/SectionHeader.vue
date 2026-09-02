<template>
  <div class="mb-8 md:mb-12">
    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-3">{{ title }}</h2>
    <p v-if="subtitle" class="text-slate-600 max-w-2xl leading-relaxed">{{ subtitle }}</p>
  </div>
</template>

<script setup>
defineOptions({
  name: 'SectionHeader',
})

defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: null
  }
})
</script>
