<template>
  <div class="grid gap-4 md:grid-cols-2">
    <EventCard
      v-for="event in events"
      :key="event.id"
      :event="event"
      @view="emit('view', $event)"
    />
  </div>
</template>

<script setup>
import EventCard from '../EventCard/EventCard.vue'

defineProps({
  events: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['view'])
</script>
