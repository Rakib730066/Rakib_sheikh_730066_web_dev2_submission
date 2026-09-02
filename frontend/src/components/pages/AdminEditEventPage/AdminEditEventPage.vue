<template>
  <EventForm :is-edit="true" :initial-data="eventData" />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../../utils/axiosConfig.js'
import EventForm from '../../organisms/EventForm/EventForm.vue'

defineOptions({
  name: 'AdminEditEventPage',
})

const route = useRoute()
const eventData = ref({
  id: null,
  title: '',
  description: '',
  date: '',
  time: '',
  location: '',
  category: 'workshop',
  capacity: null
})

onMounted(async () => {
  try {
    const id = route.params.id
    const response = await api.get(`/api/events/${id}`)
    const event = response.data.data

    eventData.value = {
      id: event.id,
      title: event.title,
      description: event.description,
      date: event.date,
      time: event.time || '09:00',
      location: event.location,
      category: event.category,
      capacity: event.capacity || null
    }
  } catch (err) {
    console.error('Failed to load event:', err)
  }
})
</script>
