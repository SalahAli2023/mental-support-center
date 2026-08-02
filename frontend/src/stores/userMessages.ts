import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export interface UserMessage {
      id: number
      name: string
      email: string
      subject?: string | null
      message_type: 'complaint' | 'inquiry' | 'review'
      message: string
      response?: string | null
      status: 'new' | 'in_progress' | 'resolved'
      is_read: boolean
      read_at?: string | null
      is_public?: boolean
      public_at?: string | null
      responded_at?: string | null
      responder?: {
            id: number
            name: string
            email: string
      } | null
      created_at?: string
}

interface PaginationMeta {
      total: number
      per_page: number
      current_page: number
}

export const useUserMessageStore = defineStore('userMessages', () => {
      const messages = ref<UserMessage[]>([])
      const loading = ref(false)
      const error = ref<string | null>(null)
      const meta = ref<PaginationMeta>({ total: 0, per_page: 10, current_page: 1 })

      const fetchMessages = async (params: Record<string, any> = {}) => {
            loading.value = true
            error.value = null
            try {
                  const response = await api.get('/user-messages', { params })
                  messages.value = response.data.data || []
                  if (response.data.meta) {
                        meta.value = {
                              total: response.data.meta.total,
                              per_page: response.data.meta.per_page,
                              current_page: response.data.meta.current_page
                        }
                  } else {
                        meta.value = {
                              total: messages.value.length,
                              per_page: messages.value.length,
                              current_page: 1
                        }
                  }
            } catch (err: any) {
                  error.value =
                        err.response?.data?.message || 'فشل في تحميل رسائل المستخدمين'
                  throw err
            } finally {
                  loading.value = false
            }
      }

      const updateMessage = async (id: number, payload: Partial<UserMessage>) => {
            const response = await api.put(`/user-messages/${id}`, payload)
            const updated = response.data.data
            const index = messages.value.findIndex((msg) => msg.id === id)
            if (index !== -1) {
                  messages.value[index] = updated
            }
            return updated
      }

      const deleteMessage = async (id: number) => {
            await api.delete(`/user-messages/${id}`)
            messages.value = messages.value.filter((msg) => msg.id !== id)
      }

      return {
            messages,
            loading,
            error,
            meta,
            fetchMessages,
            updateMessage,
            deleteMessage
      }
})

