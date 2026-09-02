<template>
  <article
    class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col gap-3 h-full"
  >
    <!-- Category Badge -->
    <div class="flex gap-2 items-start">
      <Badge :variant="getCategoryVariant(event.category)" size="sm">
        {{ event.category }}
      </Badge>
    </div>

    <!-- Event Title -->
    <h3 class="text-lg font-semibold text-slate-900 line-clamp-2 leading-tight">
      {{ event.title }}
    </h3>

    <!-- Date & Location -->
    <div class="flex flex-col gap-1.5 text-sm text-slate-700">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-slate-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <time :datetime="event.date" class="text-slate-700">
          {{ formatDate(event.date) }}
        </time>
      </div>
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-slate-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <span class="text-slate-700">{{ event.location }}</span>
      </div>
    </div>

    <!-- Description -->
    <p class="text-sm text-slate-600 line-clamp-3 leading-normal flex-grow">
      {{ event.description }}
    </p>

    <!-- Capacity Info (if available) -->
    <div v-if="event.capacity" class="flex items-center gap-2 pt-1 border-t border-slate-200">
      <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 006-6V4a6 6 0 00-6-6H6a6 6 0 00-6 6v10a6 6 0 006 6z" />
      </svg>
      <span class="text-xs font-medium text-slate-600">
        {{ spotsAvailable }} of {{ event.capacity }} spots available
      </span>
    </div>

    <!-- CTA Button -->
    <router-link
      :to="`/events/${event.id}`"
      class="mt-2 px-4 py-2.5 bg-indigo-700 text-white rounded-lg font-semibold text-sm hover:bg-indigo-800 focus:outline-none focus:ring-4 focus:ring-indigo-200 transition-colors duration-200 text-center"
    >
      View Details
    </router-link>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import Badge from '../../atoms/Badge/Badge.vue'

defineOptions({
  name: 'EventCard',
})

const props = defineProps({
  event: {
    type: Object,
    required: true,
    validator: (value) => {
      return value && typeof value === 'object' && 'id' in value && 'title' in value
    },
  },
})

// Format date for display
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date)
}

// Get badge variant based on category
const getCategoryVariant = (category) => {
  const variants = {
    workshop: 'primary',
    conference: 'info',
    meetup: 'success',
    seminar: 'warning',
    masterclass: 'primary',
  }
  return variants[category?.toLowerCase()] || 'neutral'
}

// Calculate available spots
const spotsAvailable = computed(() => {
  if (!props.event.capacity) return 0
  const registered = props.event.registration_count || props.event.registered_count || 0
  return Math.max(0, props.event.capacity - registered)
})
</script>
