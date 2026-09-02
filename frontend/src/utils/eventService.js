import api from './axiosConfig.js'

export async function getEvents() {
  const { data } = await api.get('/api/events')
  return data
}

export async function getEventById(id) {
  const { data } = await api.get(`/api/events/${id}`)
  return data
}

export async function createEvent(payload) {
  const { data } = await api.post('/api/events', payload)
  return data
}
