import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export const useTherapistStore = defineStore('therapists', () => {
  const therapists = ref([])
  const currentPage = ref(1)
  const totalPages = ref(1)
  const perPage = ref(10)
  const loading = ref(false)

  // جلب قائمة المعالجين
  const fetchTherapists = async (filters = {}) => {
    try {
      loading.value = true

      const params = {
        page: currentPage.value,
        per_page: perPage.value,
        ...filters
      }

      const response = await api.get('/therapists', { params })

      therapists.value = response.data.data
      currentPage.value = response.data.meta.current_page
      totalPages.value = response.data.meta.total

      return response.data
    } catch (error) {
      console.error('Error fetching therapists:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // جلب معالج محدد
  const fetchTherapist = async (therapistId) => {
    try {
      const response = await api.get(`/therapists/${therapistId}`)
      return response.data.data
    } catch (error) {
      console.error('Error fetching therapist:', error)
      throw error
    }
  }

  // إنشاء معالج جديد
  const createTherapist = async (therapistData) => {
    try {
      const config = therapistData instanceof FormData
        ? { headers: { 'Content-Type': 'multipart/form-data' } }
        : {}

      const response = await api.post('/therapists', therapistData, config)

      // إضافة المعالج الجديد للقائمة
      therapists.value.unshift(response.data.data)

      return response.data
    } catch (error) {
      console.error('Error creating therapist:', error)
      throw error
    }
  }

  // تحديث معالج
  const updateTherapist = async (therapistId, therapistData) => {
    try {
      console.log('Updating therapist:', therapistId, therapistData);
      console.log('Is FormData?', therapistData instanceof FormData);

      if (therapistData instanceof FormData) {
        // عرض محتويات FormData للتحقق
        console.log('FormData contents:');
        for (const [key, value] of therapistData.entries()) {
          if (value instanceof File) {
            console.log(`${key}:`, {
              name: value.name,
              type: value.type,
              size: value.size
            });
          } else {
            console.log(`${key}:`, value);
          }
        }
      }

      // لا نضيف Content-Type header يدوياً للـ FormData - المتصفح يضيفه تلقائياً مع boundary
      const config = therapistData instanceof FormData
        ? {} // لا نضيف headers للـ FormData - المتصفح يضيفها تلقائياً
        : {}

      const response = await api.post(`/therapists/${therapistId}?_method=PUT`, therapistData, config)

      // التحقق من نجاح العملية
      if (response.data?.success !== false) {
        // تحديث البيانات المحلية
        const index = therapists.value.findIndex(t => t.id === therapistId)
        if (index !== -1 && response.data?.data) {
          therapists.value[index] = response.data.data
        }

        return response.data
      } else {
        throw new Error(response.data?.message || 'فشل في تحديث بيانات المعالج')
      }
    } catch (error) {
      console.error('Error updating therapist:', error)

      // تسجيل تفاصيل الخطأ
      if (error.response) {
        console.error('Response error:', error.response.data)
        console.error('Response status:', error.response.status)
      }

      // معالجة خطأ 404 بشكل خاص
      if (error.response?.status === 404) {
        // إذا كان المعالج غير موجود، قم بإزالته من القائمة المحلية
        therapists.value = therapists.value.filter(t => t.id !== therapistId)
        throw new Error('المعالج غير موجود أو تم حذفه مسبقاً')
      }

      throw error
    }
  }

  // حذف معالج
  const deleteTherapist = async (therapistId) => {
    try {
      const response = await api.delete(`/therapists/${therapistId}`)

      // التحقق من نجاح العملية
      if (response.data?.success !== false) {
        // إزالة من القائمة المحلية
        therapists.value = therapists.value.filter(therapist => therapist.id !== therapistId)
        return true
      } else {
        throw new Error(response.data?.message || 'فشل في حذف المعالج')
      }
    } catch (error) {
      console.error('Error deleting therapist:', error)

      // معالجة خطأ 404 بشكل خاص
      if (error.response?.status === 404) {
        // إذا كان المعالج غير موجود، قم بإزالته من القائمة المحلية
        therapists.value = therapists.value.filter(t => t.id !== therapistId)
        throw new Error('المعالج غير موجود أو تم حذفه مسبقاً')
      }

      throw error
    }
  }

  // إدارة الشهادات
  const fetchCertifications = async (therapistId) => {
    try {
      const response = await api.get(`/therapists/${therapistId}/certifications`)
      return response.data.data
    } catch (error) {
      console.error('Error fetching certifications:', error)
      throw error
    }
  }

  const createCertification = async (therapistId, certificationData) => {
    try {
      const response = await api.post(`/therapists/${therapistId}/certifications`, certificationData)
      return response.data
    } catch (error) {
      console.error('Error creating certification:', error)
      throw error
    }
  }

  const updateCertification = async (therapistId, certificationId, certificationData) => {
    try {
      const response = await api.put(`/therapists/${therapistId}/certifications/${certificationId}`, certificationData)
      return response.data
    } catch (error) {
      console.error('Error updating certification:', error)
      throw error
    }
  }

  const deleteCertification = async (therapistId, certificationId) => {
    try {
      await api.delete(`/therapists/${therapistId}/certifications/${certificationId}`)
      return true
    } catch (error) {
      console.error('Error deleting certification:', error)
      throw error
    }
  }

  // إدارة المؤهلات
  const fetchQualifications = async (therapistId) => {
    try {
      const response = await api.get(`/therapists/${therapistId}/qualifications`)
      return response.data.data
    } catch (error) {
      console.error('Error fetching qualifications:', error)
      throw error
    }
  }

  const createQualification = async (therapistId, qualificationData) => {
    try {
      const response = await api.post(`/therapists/${therapistId}/qualifications`, qualificationData)
      return response.data
    } catch (error) {
      console.error('Error creating qualification:', error)
      throw error
    }
  }

  const updateQualification = async (therapistId, qualificationId, qualificationData) => {
    try {
      const response = await api.put(`/therapists/${therapistId}/qualifications/${qualificationId}`, qualificationData)
      return response.data
    } catch (error) {
      console.error('Error updating qualification:', error)
      throw error
    }
  }

  const deleteQualification = async (therapistId, qualificationId) => {
    try {
      await api.delete(`/therapists/${therapistId}/qualifications/${qualificationId}`)
      return true
    } catch (error) {
      // إذا كان المؤهل غير موجود (404)، لا نعتبره خطأ
      if (error.response?.status === 404) {
        console.warn('Qualification not found (may have been deleted already):', qualificationId)
        return true
      }
      console.error('Error deleting qualification:', error)
      throw error
    }
  }

  // إدارة الجداول
  const fetchSchedules = async (therapistId) => {
    try {
      const response = await api.get(`/therapists/${therapistId}/schedules`)
      return response.data.data
    } catch (error) {
      console.error('Error fetching schedules:', error)
      throw error
    }
  }

  const createSchedule = async (therapistId, scheduleData) => {
    try {
      const response = await api.post(`/therapists/${therapistId}/schedules`, scheduleData)
      return response.data
    } catch (error) {
      console.error('Error creating schedule:', error)
      throw error
    }
  }

  const updateSchedule = async (therapistId, scheduleId, scheduleData) => {
    try {
      const response = await api.put(`/therapists/${therapistId}/schedules/${scheduleId}`, scheduleData)
      return response.data
    } catch (error) {
      console.error('Error updating schedule:', error)
      throw error
    }
  }

  const deleteSchedule = async (therapistId, scheduleId) => {
    try {
      await api.delete(`/therapists/${therapistId}/schedules/${scheduleId}`)
      return true
    } catch (error) {
      console.error('Error deleting schedule:', error)
      throw error
    }
  }




  // تغيير الصفحة
  const setPage = (page) => {
    currentPage.value = page
  }

  // تغيير عدد العناصر لكل صفحة
  const setPerPage = (count) => {
    perPage.value = count
    currentPage.value = 1
  }

  return {
    therapists,
    currentPage,
    totalPages,
    perPage,
    loading,
    fetchTherapists,
    fetchTherapist,
    createTherapist,
    updateTherapist,
    deleteTherapist,
    fetchCertifications,
    createCertification,
    updateCertification,
    deleteCertification,
    fetchQualifications,
    createQualification,
    updateQualification,
    deleteQualification,
    fetchSchedules,
    createSchedule,
    updateSchedule,
    deleteSchedule,
    setPage,
    setPerPage
  }
})
