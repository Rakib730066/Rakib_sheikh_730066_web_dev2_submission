<template>
  <main class="min-h-screen bg-slate-50">
    <div class="max-w-4xl mx-auto px-4 md:px-6 lg:px-8 py-8 md:py-12">
      <!-- Page Header -->
      <header class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-slate-900">
          {{ isEdit ? 'Edit Event' : 'Create New Event' }}
        </h1>
        <p class="text-slate-600 mt-2">
          {{ isEdit ? 'Update event details and manage your event' : 'Share your event with the community' }}
        </p>
      </header>

      <!-- Form Card -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8">
        <!-- Error Alert -->
        <Alert v-if="errorMessage" type="error" :message="errorMessage" class="mb-6" />

        <!-- Success Alert -->
        <Alert v-if="successMessage" type="success" :message="successMessage" class="mb-6" />

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Title -->
          <Input
            id="title"
            v-model="form.title"
            label="Event Title"
            type="text"
            placeholder="e.g., Web Development Workshop"
            :error="validationErrors.title"
            helperText="Choose a clear, descriptive title for your event"
            required
          />

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-semibold text-slate-900 mb-2">
              Description <span class="text-red-500">*</span>
            </label>
            <textarea
              id="description"
              v-model="form.description"
              placeholder="Describe your event, what attendees will learn, and any important details..."
              rows="6"
              required
              class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-4 focus:ring-indigo-500 focus:border-transparent outline-none font-family-sans resize-none"
              :aria-invalid="!!validationErrors.description"
              :aria-describedby="validationErrors.description ? 'description-error' : undefined"
            ></textarea>
            <p v-if="validationErrors.description" id="description-error" class="mt-1 text-sm font-medium text-red-600">
              {{ validationErrors.description }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Provide helpful details about your event</p>
          </div>

          <!-- Date and Time -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Input
              id="date"
              v-model="form.date"
              label="Event Date"
              type="date"
              :error="validationErrors.date"
              required
            />

            <Input
              id="time"
              v-model="form.time"
              label="Event Time"
              type="time"
              :error="validationErrors.time"
              helperText="Start time for the event"
            />
          </div>

          <!-- Location -->
          <Input
            id="location"
            v-model="form.location"
            label="Location"
            type="text"
            placeholder="e.g., San Francisco, CA or Online"
            :error="validationErrors.location"
            helperText="Where will the event take place?"
            required
          />

          <!-- Category -->
          <Select
            id="category"
            v-model="form.category"
            label="Category"
            :options="categoryOptions"
            :error="validationErrors.category"
            placeholder="Select a category"
            helperText="Choose the category that best describes your event"
            required
          />

          <!-- Capacity (Optional) -->
          <Input
            id="capacity"
            v-model.number="form.capacity"
            label="Event Capacity (Optional)"
            type="number"
            placeholder="Maximum number of attendees"
            min="1"
            :error="validationErrors.capacity"
            helperText="Leave blank for unlimited capacity"
          />

          <!-- Form Actions -->
          <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-slate-200">
            <Button
              type="submit"
              variant="primary"
              size="lg"
              :disabled="isSubmitting"
              class="flex-1"
            >
              {{ isSubmitting ? 'Saving...' : isEdit ? 'Update Event' : 'Create Event' }}
            </Button>

            <Button
              type="button"
              variant="secondary"
              size="lg"
              @click="handleCancel"
              class="flex-1"
            >
              Cancel
            </Button>
          </div>
        </form>

        <!-- Form Info Box -->
        <div class="mt-8 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
          <p class="text-sm text-indigo-900">
            <span class="font-semibold">Tips for creating great events:</span> Use a clear title, write a detailed description with key information, set an accurate date and time, and choose the most relevant category. This helps attendees find and understand your event better.
          </p>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '../../../utils/axiosConfig.js'
import Input from '../../atoms/Input/Input.vue'
import Select from '../../atoms/Select/Select.vue'
import Button from '../../atoms/Button/Button.vue'
import Alert from '../../atoms/Alert/Alert.vue'

defineOptions({
  name: 'EventForm',
})

const router = useRouter()
const route = useRoute()

// Props
const props = defineProps({
  isEdit: {
    type: Boolean,
    default: false
  },
  initialData: {
    type: Object,
    default: () => ({
      id: null,
      title: '',
      description: '',
      date: '',
      time: '',
      location: '',
      category: 'workshop',
      capacity: null
    })
  }
})

// Data
const form = ref({ ...props.initialData })
const isSubmitting = ref(false)
const errorMessage = ref(null)
const successMessage = ref(null)
const validationErrors = ref({})

// Category options
const categoryOptions = [
  { value: 'workshop', label: 'Workshop' },
  { value: 'conference', label: 'Conference' },
  { value: 'meetup', label: 'Meetup' },
  { value: 'seminar', label: 'Seminar' },
  { value: 'masterclass', label: 'Masterclass' },
  { value: 'networking', label: 'Networking' },
  { value: 'other', label: 'Other' }
]

// Methods
const validateForm = () => {
  validationErrors.value = {}

  if (!form.value.title?.trim()) {
    validationErrors.value.title = 'Event title is required'
  }

  if (!form.value.description?.trim()) {
    validationErrors.value.description = 'Event description is required'
  }

  if (!form.value.date) {
    validationErrors.value.date = 'Event date is required'
  }

  if (!form.value.location?.trim()) {
    validationErrors.value.location = 'Event location is required'
  }

  if (!form.value.category) {
    validationErrors.value.category = 'Event category is required'
  }

  if (form.value.capacity !== null && form.value.capacity < 1) {
    validationErrors.value.capacity = 'Capacity must be at least 1'
  }

  return Object.keys(validationErrors.value).length === 0
}

const submitForm = async () => {
  errorMessage.value = null
  successMessage.value = null

  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    // Combine date and time into YYYY-MM-DD HH:mm:ss format for backend
    const dateTime = form.value.date && form.value.time
      ? `${form.value.date} ${form.value.time}:00`
      : form.value.date
    
    const eventData = {
      title: form.value.title,
      description: form.value.description,
      date: dateTime,
      location: form.value.location,
      category: form.value.category,
      capacity: form.value.capacity || null
    }

    if (props.isEdit && form.value.id) {
      // Update event
      await api.put(`/api/events/${form.value.id}`, eventData)
      successMessage.value = 'Event updated successfully!'
      setTimeout(() => {
        router.push(`/events/${form.value.id}`)
      }, 1500)
    } else {
      const response = await api.post('/api/events', eventData)
      successMessage.value = 'Event created successfully!'
      setTimeout(() => {
        router.push(`/events/${response.data.data.id}`)
      }, 1500)
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to save event. Please try again.'
    console.error('Event save error:', err)
  } finally {
    isSubmitting.value = false
  }
}

const handleCancel = () => {
  if (props.isEdit && form.value.id) {
    router.push(`/events/${form.value.id}`)
  } else {
    router.push('/events')
  }
}

watch(() => props.initialData, (initialData) => {
  form.value = { ...initialData }
}, {
  immediate: true,
  deep: true
})
</script>
