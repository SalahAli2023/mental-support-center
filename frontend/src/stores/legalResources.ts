import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export const useLegalResourceStore = defineStore('legalResources', () => {
  const resources = ref([])
  const categories = ref([])
  const currentPage = ref(1)
  const totalPages = ref(1)
  const perPage = ref(15)

  // جلب الموارد من API
  const fetchResources = async (filters = {}) => {
    try {
      const params = {
        page: currentPage.value,
        per_page: perPage.value,
        ...filters
      }

      const response = await api.get('/legal-resources', { params })
      
      resources.value = response.data.data
      currentPage.value = response.data.meta?.current_page || 1
      totalPages.value = response.data.meta?.last_page || 1
      
      return response.data
    } catch (error) {
      console.error('Error fetching legal resources:', error)
      throw error
    }
  }

  // جلب التصنيفات
  const fetchCategories = async () => {
    try {
      const response = await api.get('/legal-resource-categories')
      categories.value = response.data
      return response.data
    } catch (error) {
      console.error('Error fetching categories:', error)
      throw error
    }
  }

  // إنشاء مورد جديد
  const createResource = async (resourceData) => {
    try {
      const response = await api.post('/legal-resources', resourceData)
      
      // إضافة المورد الجديد إلى القائمة
      resources.value.unshift(response.data)
      
      return response.data
    } catch (error) {
      console.error('Error creating legal resource:', error)
      throw error
    }
  }

  // تحديث مورد
  const updateResource = async (resourceId, resourceData) => {
    try {
      const response = await api.put(`/legal-resources/${resourceId}`, resourceData)
      
      // تحديث المورد في القائمة
      const index = resources.value.findIndex(resource => resource.id === resourceId)
      if (index !== -1) {
        resources.value[index] = { ...resources.value[index], ...response.data }
      }
      
      return response.data
    } catch (error) {
      console.error('Error updating legal resource:', error)
      throw error
    }
  }

  // حذف مورد
  const deleteResource = async (resourceId) => {
    try {
      await api.delete(`/legal-resources/${resourceId}`)
      
      // إزالة المورد من القائمة
      resources.value = resources.value.filter(resource => resource.id !== resourceId)
      
      return true
    } catch (error) {
      console.error('Error deleting legal resource:', error)
      throw error
    }
  }

  // البحث في الموارد
  const searchResources = async (searchTerm) => {
    try {
      const params = {
        search: searchTerm,
        page: 1,
        per_page: perPage.value
      }

      const response = await api.get('/legal-resources', { params })
      
      resources.value = response.data.data
      currentPage.value = response.data.meta?.current_page || 1
      totalPages.value = response.data.meta?.last_page || 1
      
      return response.data
    } catch (error) {
      console.error('Error searching legal resources:', error)
      throw error
    }
  }

  // التصفية حسب النوع والتصنيف
  const filterResources = async (filters = {}) => {
    try {
      const params = {
        page: 1,
        per_page: perPage.value,
        ...filters
      }

      const response = await api.get('/legal-resources', { params })
      
      resources.value = response.data.data
      currentPage.value = response.data.meta?.current_page || 1
      totalPages.value = response.data.meta?.last_page || 1
      
      return response.data
    } catch (error) {
      console.error('Error filtering legal resources:', error)
      throw error
    }
  }

  // تغيير الصفحة
  const setPage = (page) => {
    currentPage.value = page
  }

  // تغيير عدد العناصر في الصفحة
  const setPerPage = (count) => {
    perPage.value = count
  }

  // الحصول على مورد بواسطة ID
  const getResourceById = (id) => {
    return resources.value.find(resource => resource.id === id)
  }

  // إحصائيات سريعة
  const getStats = () => {
    const totalResources = resources.value.length
    const lawTypes = [...new Set(resources.value.map(resource => resource.law_type))]
    const totalCategories = categories.value.length
    
    return {
      total_resources: totalResources,
      law_types_count: lawTypes.length,
      categories_count: totalCategories,
      updated_today: resources.value.filter(resource => {
        const updatedDate = new Date(resource.updated_at).toDateString()
        return updatedDate === new Date().toDateString()
      }).length
    }
  }

  return {
    // البيانات
    resources,
    categories,
    currentPage,
    totalPages,
    perPage,
    
    // الدوال
    fetchResources,
    fetchCategories,
    createResource,
    updateResource,
    deleteResource,
    searchResources,
    filterResources,
    setPage,
    setPerPage,
    getResourceById,
    getStats
  }
})