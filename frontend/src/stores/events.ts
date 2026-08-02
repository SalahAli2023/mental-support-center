import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export const useEventStore = defineStore('events', () => {
  const events = ref([])
  const currentPage = ref(1)
  const totalPages = ref(1)
  const perPage = ref(15)
  const loading = ref(false)

  // جلب الفعاليات من API
  const fetchEvents = async (filters = {}) => {
    try {
      loading.value = true
      const params = {
        page: currentPage.value,
        per_page: perPage.value,
        ...filters
      }

      const response = await api.get('/events', { params })
      const payload = response.data || {}
      const meta = payload.meta || {}

      events.value = payload.data || []
      currentPage.value = meta.current_page ?? 1
      totalPages.value = meta.last_page ?? 1
      perPage.value = meta.per_page ?? params.per_page ?? events.value.length ?? 0

      return response.data
    } catch (error) {
      console.error('Error fetching events:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // جلب فعالية محددة
  const fetchEventById = async (id) => {
    try {
      loading.value = true
      const response = await api.get(`/events/${id}`)
      return response.data
    } catch (error) {
      console.error('Error fetching event:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // إنشاء فعالية جديدة
  const createEvent = async (eventData) => {
    try {
      const formData = new FormData()

      Object.keys(eventData).forEach(key => {
        const value = eventData[key]

        if (key === 'media_file' && value) {
          formData.append('media', value)
        } else if (key === 'speakers' && value) {
          formData.append(key, JSON.stringify(value))
        } else if (value !== null && value !== undefined && value !== '') {
          formData.append(key, value)
        }
      })

      const response = await api.post('/events', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      events.value.unshift(response.data.data)
      return response.data
    } catch (error) {
      throw error
    }
  }

  // تحديث فعالية
  const updateEvent = async (eventId, eventData) => {
    try {
      const formData = new FormData()

      Object.keys(eventData).forEach(key => {
        const value = eventData[key]

        if (key === 'media_file' && value) {
          formData.append('media', value)
        } else if (key === 'speakers' && value) {
          formData.append(key, JSON.stringify(value))
        } else if (value !== null && value !== undefined && value !== '') {
          formData.append(key, value)
        }
      })

      formData.append('_method', 'PUT')

      const response = await api.post(`/events/${eventId}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      return response.data
    } catch (error) {
      throw error
    }
  }

  // تحديث حالة النشر فقط
  const updateEventPublishStatus = async (eventId, isPublished) => {
    try {
      const formData = new FormData()
      formData.append('is_published', isPublished ? '1' : '0')
      formData.append('_method', 'PUT')

      const response = await api.post(`/events/${eventId}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      return response.data
    } catch (error) {
      throw error
    }
  }

  // حذف فعالية
  const deleteEvent = async (eventId) => {
    try {
      await api.delete(`/events/${eventId}`)

      events.value = events.value.filter(event => event.id !== eventId)

      return true
    } catch (error) {
      throw error
    }
  }

  // تغيير الصفحة
  const setPage = (page) => {
    currentPage.value = page
  }

  return {
    events,
    currentPage,
    totalPages,
    perPage,
    loading,
    fetchEvents,
    fetchEventById,
    createEvent,
    updateEvent,
    updateEventPublishStatus,
    deleteEvent,
    setPage
  }
})