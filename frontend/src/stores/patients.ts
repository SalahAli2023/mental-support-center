import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export const usePatientsStore = defineStore('patients', () => {
  const patients = ref([])
  const currentPatient = ref(null)
  const currentPage = ref(1)
  const totalPages = ref(1)
  const totalItems = ref(0)
  const perPage = ref(9)
  const loading = ref(false)
  const error = ref(null)
  const filters = ref({
    search: '',
    status: '',
    condition: '',
    sort: 'date-desc'
  })

  // جلب المرضى من API
  const fetchPatients = async (customFilters = {}) => {
    try {
      loading.value = true
      error.value = null
      
      const params = {
        page: currentPage.value,
        per_page: perPage.value,
        ...filters.value,
        ...customFilters
      }

      console.log('Fetching patients with params:', params)
      
      const response = await api.get('/patients', { params })
      
      console.log('Patients response:', response.data)
      
      patients.value = response.data.patients || []
      currentPage.value = response.data.pagination?.current_page || 1
      totalPages.value = response.data.pagination?.total_pages || 1
      totalItems.value = response.data.pagination?.total_items || 0
      
      return response.data
    } catch (error) {
      console.error('Error fetching patients:', error)
      error.value = error.response?.data?.message || error.message
      
      // إذا كان الخطأ 401، لا نرمي الخطأ لأن الـ interceptor سيتعامل معه
      if (error.response?.status !== 401) {
        throw error
      }
    } finally {
      loading.value = false
    }
  }

  // جلب مريض محدد
  const fetchPatientById = async (id) => {
    try {
      loading.value = true
      error.value = null
      const response = await api.get(`/patients/${id}`)
      currentPatient.value = response.data.patient
      return response.data
    } catch (error) {
      console.error('Error fetching patient:', error)
      error.value = error.response?.data?.message || error.message
      if (error.response?.status !== 401) {
        throw error
      }
    } finally {
      loading.value = false
    }
  }

  // إنشاء مريض جديد
  const createPatient = async (patientData) => {
    try {
      loading.value = true
      error.value = null
      
      const response = await api.post('/patients', patientData)
      
      // إضافة المريض الجديد للقائمة
      patients.value.unshift(response.data.patient)
      return response.data
    } catch (error) {
      console.error('Error creating patient:', error)
      error.value = error.response?.data?.message || error.message
      throw error
    } finally {
      loading.value = false
    }
  }

  // تحديث مريض
  const updatePatient = async (patientId, patientData) => {
    try {
      loading.value = true
      error.value = null
      
      const response = await api.put(`/patients/${patientId}`, patientData)
      
      // تحديث المريض في القائمة
      const index = patients.value.findIndex(p => p.id === patientId)
      if (index !== -1) {
        patients.value[index] = response.data.patient
      }
      
      // تحديث المريض الحالي إذا كان هو نفسه
      if (currentPatient.value && currentPatient.value.id === patientId) {
        currentPatient.value = response.data.patient
      }
      
      return response.data
    } catch (error) {
      console.error('Error updating patient:', error)
      error.value = error.response?.data?.message || error.message
      throw error
    } finally {
      loading.value = false
    }
  }

  // حذف مريض
  const deletePatient = async (patientId) => {
    try {
      loading.value = true
      error.value = null
      
      await api.delete(`/patients/${patientId}`)
      
      // إزالة المريض من القائمة
      patients.value = patients.value.filter(patient => patient.id !== patientId)
      
      return true
    } catch (error) {
      console.error('Error deleting patient:', error)
      error.value = error.response?.data?.message || error.message
      throw error
    } finally {
      loading.value = false
    }
  }

  // جلب إحصائيات المرضى
  const fetchStats = async () => {
    try {
      loading.value = true
      error.value = null
      
      const response = await api.get('/patients/stats')
      return response.data
    } catch (error) {
      console.error('Error fetching stats:', error)
      error.value = error.response?.data?.message || error.message
      throw error
    } finally {
      loading.value = false
    }
  }

  // تصدير بيانات المرضى
  const exportPatients = async () => {
    try {
      loading.value = true
      error.value = null
      
      const response = await api.get('/patients/export')
      return response.data
    } catch (error) {
      console.error('Error exporting patients:', error)
      error.value = error.response?.data?.message || error.message
      throw error
    } finally {
      loading.value = false
    }
  }

  // تحديث حالة المريض
  const updatePatientStatus = async (patientId, status) => {
    try {
      error.value = null
      
      const response = await api.patch(`/patients/${patientId}/status`, { status })
      
      // تحديث الحالة في القائمة
      const index = patients.value.findIndex(p => p.id === patientId)
      if (index !== -1) {
        patients.value[index].status = status
      }
      
      return response.data
    } catch (error) {
      console.error('Error updating patient status:', error)
      error.value = error.response?.data?.message || error.message
      throw error
    }
  }

  // إدارة الفلاتر
  const setFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters }
  }

  const resetFilters = () => {
    filters.value = {
      search: '',
      status: '',
      condition: '',
      sort: 'date-desc'
    }
  }

  // إدارة التقسيم الصفحي
  const setPage = (page) => {
    currentPage.value = page
  }

  const setPerPage = (newPerPage) => {
    perPage.value = newPerPage
    currentPage.value = 1
  }

  // مسح الخطأ
  const clearError = () => {
    error.value = null
  }

  // إعادة تعيين الـ store
  const resetStore = () => {
    patients.value = []
    currentPatient.value = null
    currentPage.value = 1
    totalPages.value = 1
    totalItems.value = 0
    perPage.value = 9
    loading.value = false
    error.value = null
    filters.value = {
      search: '',
      status: '',
      condition: '',
      sort: 'date-desc'
    }
  }

  return {
    // البيانات
    patients,
    currentPatient,
    currentPage,
    totalPages,
    totalItems,
    perPage,
    loading,
    error,
    filters,
    
    // الإجراءات
    fetchPatients,
    fetchPatientById,
    createPatient,
    updatePatient,
    deletePatient,
    fetchStats,
    exportPatients,
    updatePatientStatus,
    setFilters,
    resetFilters,
    setPage,
    setPerPage,
    clearError,
    resetStore
  }
})