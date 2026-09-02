import api from './axiosConfig.js'

export async function registerForEvent(payload) {
  const { data } = await api.post('/api/registrations', payload)
  return data
}

export async function getRegistrationsByEvent(eventId) {
  const { data } = await api.get(`/api/events/${eventId}/registrations`)
  return data
}
