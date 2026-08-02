import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../utils/api'

export interface Category {
  id: string
  name_ar: string
  name_en: string
  description_ar?: string
  description_en?: string
  color: string
  is_active: boolean
  scales_count?: number
  created_at?: string
  updated_at?: string
}

export const useCategoriesStore = defineStore('categories', () => {
  const categories = ref<Category[]>([])
  const currentCategory = ref<Category | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchCategories = async (publicAccess: boolean = false) => {
    loading.value = true
    error.value = null
    try {
      const endpoint = publicAccess ? 'categories' : 'categories'
      const response = await api.get(`/${endpoint}`)
      
      categories.value = response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'فشل في تحميل التصنيفات'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchCategory = async (id: string) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.get(`/categories/${id}`)
      currentCategory.value = response.data.data
      
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'فشل في تحميل التصنيف'
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchCategoryScales = async (id: string) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.get(`/categories/${id}/scales`)
      return response.data.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'فشل في تحميل مقاييس التصنيف'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    categories,
    currentCategory,
    loading,
    error,
    fetchCategories,
    fetchCategory,
    fetchCategoryScales
  }
})