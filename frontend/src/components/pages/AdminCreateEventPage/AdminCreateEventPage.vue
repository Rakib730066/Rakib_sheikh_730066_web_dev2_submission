<template>
  <EventForm :is-edit="false" />
</template>

<script setup>
import EventForm from '../../organisms/EventForm/EventForm.vue'

defineOptions({
  name: 'AdminCreateEventPage',
})
</script>
