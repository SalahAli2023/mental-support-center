// library.ts - تحديث الـ store
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export interface LibraryCategory {
  id: number
  key: string
  name_ar: string
  name_en: string
  color: string
}

export interface LibraryItem {
  id: number
  title_ar: string
  title_en: string
  description_ar?: string
  description_en?: string
  author_ar?: string
  author_en?: string
  type: 'book' | 'research' | 'guide' | 'article'
  category_id: number
  cover_image?: string
  file_path?: string
  file_size?: number
  pages?: number
  publish_date?: string
  downloads: number
  views: number
  rating: number
  rating_count: number
  is_new: boolean
  is_published: boolean
  created_at: string
  updated_at: string
  category?: LibraryCategory
  // حقول إضافية للواجهة
  isFavorite?: boolean
  cover?: string
  author?: string
  title?: string
  description?: string
  year?: string
}

export const useLibraryStore = defineStore('library', () => {
  const items = ref<LibraryItem[]>([])
  const categories = ref<LibraryCategory[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const favorites = ref<number[]>([])

  // تحويل البيانات من API إلى تنسيق الواجهة
  const transformItemForUI = (item: LibraryItem): any => {
    return {
      ...item,
      cover: item.cover_image,
      author: item.author_ar || item.author_en || '',
      title: item.title_ar || item.title_en,
      description: item.description_ar || item.description_en,
      year: item.publish_date ? new Date(item.publish_date).getFullYear().toString() : '',
      isFavorite: favorites.value.includes(item.id),
      rating: parseFloat(item.rating.toString()) || 0
    }
  }

  // جلب جميع العناصر
  const fetchItems = async (params?: {
    category_id?: string
    type?: string
    search?: string
    per_page?: number
  }) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.get('/library', { params })
      items.value = response.data.data.map(transformItemForUI)
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch library items'
      throw err
    } finally {
      loading.value = false
    }
  }

  // جلب التصنيفات
  const fetchCategories = async () => {
    try {
      const response = await api.get('/library/categories/list')
      categories.value = response.data.data
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch categories'
      throw err
    }
  }

  // جلب عنصر واحد
  const fetchItem = async (id: number) => {
    try {
      const response = await api.get(`/library/${id}`)
      return transformItemForUI(response.data.data)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch item'
      throw err
    }
  }

  // إنشاء عنصر جديد
  const createItem = async (formData: FormData) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/library', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        },
        timeout: 60000,
      })
      const newItem = transformItemForUI(response.data.data)
      items.value.unshift(newItem)
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create item'
      throw err
    } finally {
      loading.value = false
    }
  }

  // تحديث عنصر
  const updateItem = async (id: number, formData: FormData) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post(`/library/${id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      const updatedItem = transformItemForUI(response.data.data)
      const index = items.value.findIndex(item => item.id === id)
      if (index !== -1) {
        items.value[index] = updatedItem
      }
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to update item'
      throw err
    } finally {
      loading.value = false
    }
  }

  // حذف عنصر
  const deleteItem = async (id: number) => {
    loading.value = true
    error.value = null
    try {
      await api.delete(`/library/${id}`)
      items.value = items.value.filter(item => item.id !== id)
      // إزالة من المفضلة إذا كان موجوداً
      favorites.value = favorites.value.filter(favId => favId !== id)
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete item'
      throw err
    } finally {
      loading.value = false
    }
  }

  // زيادة المشاهدات
  const incrementViews = async (id: number) => {
    try {
      await api.get(`/library/${id}`)
      // تحديث العدد محلياً
      const item = items.value.find(item => item.id === id)
      if (item) {
        item.views++
      }
    } catch (err) {
      console.error('Failed to increment views:', err)
    }
  }

  // إدارة المفضلة
  const toggleFavorite = (id: number) => {
    const index = favorites.value.indexOf(id)
    if (index > -1) {
      favorites.value.splice(index, 1)
    } else {
      favorites.value.push(id)
    }

    // تحديث العنصر في القائمة
    const item = items.value.find(item => item.id === id)
    if (item) {
      item.isFavorite = !item.isFavorite
    }

    // حفظ في localStorage (اختياري)
    localStorage.setItem('library_favorites', JSON.stringify(favorites.value))
  }

  // التحميل
  const downloadItem = async (id: number) => {
    try {
      const item = items.value.find(item => item.id === id)
      if (item && item.file_path) {
        // فتح رابط التحميل
        window.open(item.file_path, '_blank')

        // استدعاء الـ API لتسجيل التحميل وجلب العدد المحدث
        const response = await api.get(`/library/${id}/download`)
        const updated = transformItemForUI(
          response.data?.data || response.data
        )

        const index = items.value.findIndex(b => b.id === id)
        if (index !== -1) {
          items.value[index] = updated
        }
      }
    } catch (err) {
      console.error('Download failed:', err)
    }
  }

  // التقييم
  const rateItem = async (id: number, rating: number) => {
    try {
      // يمكنك إضافة API call للتقييم إذا كان موجوداً
      // await api.post(`/library/${id}/rate`, { rating })

      // تحديث محلي
      const item = items.value.find(item => item.id === id)
      if (item) {
        const newRatingCount = item.rating_count + 1
        const newRating = ((item.rating * item.rating_count) + rating) / newRatingCount
        item.rating = parseFloat(newRating.toFixed(1))
        item.rating_count = newRatingCount
      }
    } catch (err) {
      console.error('Rating failed:', err)
    }
  }

  // تحميل المفضلة من localStorage
  const loadFavorites = () => {
    const saved = localStorage.getItem('library_favorites')
    if (saved) {
      favorites.value = JSON.parse(saved)
    }
  }

  // التهيئة
  loadFavorites()

  return {
    items,
    categories,
    loading,
    error,
    favorites,
    fetchItems,
    fetchCategories,
    fetchItem,
    createItem,
    updateItem,
    deleteItem,
    incrementViews,
    toggleFavorite,
    downloadItem,
    rateItem,
    loadFavorites
  }
})