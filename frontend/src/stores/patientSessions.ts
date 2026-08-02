import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export const usePatientSessionsStore = defineStore('patientSessions', () => {
  const sessions = ref([])
  const currentSession = ref(null)
  const currentPage = ref(1)
  const totalPages = ref(1)
  const totalItems = ref(0)
  const perPage = ref(10)
  const loading = ref(false)
  const error = ref(null)
  const stats = ref(null)
  const availableSlots = ref([])
  const therapists = ref([])
  
  // الفلاتر
  const filters = ref({
    status: '',
    type: '',
    location: '',
    date_from: '',
    date_to: '',
    therapist_id: '',
    sort_by: 'session_date',
    sort_order: 'desc'
  })

  // جلب جلسات المريض
  const fetchSessions = async (patientId, customFilters = {}) => {
    try {
      loading.value = true
      error.value = null
      
      const params = {
        page: currentPage.value,
        per_page: perPage.value,
        ...filters.value,
        ...customFilters
      }

      // تنظيف المعايير الفارغة
      Object.keys(params).forEach(key => {
        if (params[key] === '' || params[key] === null || params[key] === undefined) {
          delete params[key]
        }
      })

      console.log('📋 جلب الجلسات للمريض:', patientId, 'بالمعايير:', params)
      
      const response = await api.get(`/patients/${patientId}/sessions`, { params })
      
      console.log('✅ استجابة الجلسات:', response.data)
      
      sessions.value = response.data.sessions || []
      currentPage.value = response.data.pagination?.current_page || 1
      totalPages.value = response.data.pagination?.total_pages || 1
      totalItems.value = response.data.pagination?.total_items || 0
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في جلب الجلسات:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  // جلب جلسة محددة
  const fetchSession = async (patientId, sessionId) => {
    try {
      loading.value = true
      error.value = null
      
      console.log('🔍 جلب جلسة محددة:', sessionId, 'للمريض:', patientId)
      
      const response = await api.get(`/patients/${patientId}/sessions/${sessionId}`)
      currentSession.value = response.data.session
      
      console.log('✅ جلسة محددة:', response.data.session)
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في جلب الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  // إنشاء جلسة جديدة
  const createSession = async (patientId, sessionData) => {
    try {
      loading.value = true
      error.value = null
      
      console.log('📝 بيانات الجلسة المرسلة:', sessionData)
      
      // تحويل البيانات للنموذج المطلوب في الـ API
      const formattedData = {
        title_ar: sessionData.title_ar,
        title_en: sessionData.title_en,
        session_date: sessionData.session_date,
        session_time: sessionData.session_time,
        therapist_id: sessionData.therapist_id,
        status: sessionData.status,
        progress: sessionData.progress || 0,
        type: sessionData.type,
        location: sessionData.location,
        notes_ar: sessionData.notes_ar || '',
        notes_en: sessionData.notes_en || '',
        report_ar: sessionData.report_ar || '',
        report_en: sessionData.report_en || '',
        duration: sessionData.duration || 60 // افتراضي 60 دقيقة
      }

      console.log('🚀 إنشاء جلسة جديدة:', formattedData)
      
      const response = await api.post(`/patients/${patientId}/sessions`, formattedData)
      
      console.log('✅ تم إنشاء الجلسة:', response.data)
      
      // إضافة الجلسة الجديدة للقائمة
      if (response.data.session) {
        sessions.value.unshift(response.data.session)
      }
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في إنشاء الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  // تحديث جلسة
  const updateSession = async (patientId, sessionId, sessionData) => {
    try {
      loading.value = true
      error.value = null
      
      console.log('📝 بيانات تحديث الجلسة:', sessionData)
      
      const formattedData = {
        title_ar: sessionData.title_ar,
        title_en: sessionData.title_en,
        session_date: sessionData.session_date,
        session_time: sessionData.session_time,
        therapist_id: sessionData.therapist_id,
        status: sessionData.status,
        progress: sessionData.progress || 0,
        type: sessionData.type,
        location: sessionData.location,
        notes_ar: sessionData.notes_ar || '',
        notes_en: sessionData.notes_en || '',
        report_ar: sessionData.report_ar || '',
        report_en: sessionData.report_en || '',
        duration: sessionData.duration || 60
      }

      console.log('🔄 تحديث الجلسة:', sessionId, formattedData)
      
      const response = await api.put(`/patients/${patientId}/sessions/${sessionId}`, formattedData)
      
      console.log('✅ تم تحديث الجلسة:', response.data)
      
      // تحديث الجلسة في القائمة
      const index = sessions.value.findIndex(s => s.id === sessionId)
      if (index !== -1) {
        sessions.value[index] = response.data.session
      }
      
      // تحديث الجلسة الحالية إذا كانت هي نفسها
      if (currentSession.value && currentSession.value.id === sessionId) {
        currentSession.value = response.data.session
      }
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في تحديث الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  // حذف جلسة
  const deleteSession = async (patientId, sessionId) => {
    try {
      loading.value = true
      error.value = null
      
      console.log('🗑️ حذف الجلسة:', sessionId, 'للمريض:', patientId)
      
      await api.delete(`/patients/${patientId}/sessions/${sessionId}`)
      
      // إزالة الجلسة من القائمة
      sessions.value = sessions.value.filter(session => session.id !== sessionId)
      
      console.log('✅ تم حذف الجلسة بنجاح')
      
      return true
    } catch (err) {
      console.error('❌ خطأ في حذف الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  // جلب إحصائيات الجلسات
  const fetchStats = async (patientId) => {
    try {
      loading.value = true
      error.value = null
      
      console.log('📊 جلب إحصائيات الجلسات للمريض:', patientId)
      
      const response = await api.get(`/patients/${patientId}/sessions/stats`)
      stats.value = response.data.stats
      
      console.log('✅ إحصائيات الجلسات:', stats.value)
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في جلب إحصائيات الجلسات:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  // جلب المواعيد المتاحة
  const fetchAvailableSlots = async (patientId, therapistId, sessionDate, duration = 60) => {
    try {
      loading.value = true
      error.value = null
      
      const params = {
        therapist_id: therapistId,
        session_date: sessionDate,
        duration: duration
      }
      
      console.log('📅 جلب المواعيد المتاحة:', params)
      
      const response = await api.get(`/patients/${patientId}/sessions/available-slots`, { params })
      availableSlots.value = response.data.available_slots || []
      
      console.log('✅ المواعيد المتاحة:', availableSlots.value)
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في جلب المواعيد المتاحة:', err)
      error.value = err.response?.data?.message || err.message
      availableSlots.value = []
      throw err
    } finally {
      loading.value = false
    }
  }

  // جلب قائمة المعالجين
  const fetchTherapists = async () => {
    try {
      loading.value = true
      error.value = null
      
      console.log('👨‍⚕️ جلب قائمة المعالجين...')
      
      const response = await api.get('/therapists?per_page=100') // جلب جميع المعالجين
      
      // استخراج البيانات من الاستجابة بناءً على هيكل الـ API
      therapists.value = response.data.data || response.data.therapists || []
      
      console.log('✅ المعالجين المحملين:', therapists.value)
      
      if (therapists.value.length === 0) {
        console.warn('⚠️ لا توجد معالجين متاحين')
      }
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في جلب المعالجين:', err)
      error.value = err.response?.data?.message || err.message
      therapists.value = []
      throw err
    } finally {
      loading.value = false
    }
  }

  // تحديث حالة الجلسة
  const updateSessionStatus = async (patientId, sessionId, status) => {
    try {
      error.value = null
      
      console.log('🔄 تحديث حالة الجلسة:', sessionId, 'إلى:', status)
      
      const response = await api.patch(`/patients/${patientId}/sessions/${sessionId}/status`, { status })
      
      // تحديث الحالة في القائمة
      const index = sessions.value.findIndex(s => s.id === sessionId)
      if (index !== -1) {
        sessions.value[index].status = status
      }
      
      console.log('✅ تم تحديث حالة الجلسة')
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في تحديث حالة الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    }
  }

  // تحديث تقدم الجلسة
  const updateSessionProgress = async (patientId, sessionId, progress) => {
    try {
      error.value = null
      
      console.log('📈 تحديث تقدم الجلسة:', sessionId, 'إلى:', progress)
      
      const response = await api.patch(`/patients/${patientId}/sessions/${sessionId}/progress`, { progress })
      
      // تحديث التقدم في القائمة
      const index = sessions.value.findIndex(s => s.id === sessionId)
      if (index !== -1) {
        sessions.value[index].progress = progress
      }
      
      console.log('✅ تم تحديث تقدم الجلسة')
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في تحديث تقدم الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    }
  }

  // إضافة ملاحظات للجلسة
  const addSessionNotes = async (patientId, sessionId, notes) => {
    try {
      error.value = null
      
      console.log('📝 إضافة ملاحظات للجلسة:', sessionId)
      
      const response = await api.post(`/patients/${patientId}/sessions/${sessionId}/notes`, notes)
      
      // تحديث الملاحظات في القائمة
      const index = sessions.value.findIndex(s => s.id === sessionId)
      if (index !== -1) {
        sessions.value[index].notes_ar = notes.notes_ar
        sessions.value[index].notes_en = notes.notes_en
      }
      
      console.log('✅ تم إضافة الملاحظات')
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في إضافة ملاحظات الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    }
  }

  // إضافة تقرير للجلسة
  const addSessionReport = async (patientId, sessionId, report) => {
    try {
      error.value = null
      
      console.log('📄 إضافة تقرير للجلسة:', sessionId)
      
      const response = await api.post(`/patients/${patientId}/sessions/${sessionId}/report`, report)
      
      // تحديث التقرير في القائمة
      const index = sessions.value.findIndex(s => s.id === sessionId)
      if (index !== -1) {
        sessions.value[index].report_ar = report.report_ar
        sessions.value[index].report_en = report.report_en
      }
      
      console.log('✅ تم إضافة التقرير')
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في إضافة تقرير الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    }
  }

  // رفع مرفقات للجلسة
  const uploadSessionAttachments = async (patientId, sessionId, attachments) => {
    try {
      loading.value = true
      error.value = null
      
      console.log('📎 رفع مرفقات للجلسة:', sessionId)
      
      const formData = new FormData()
      attachments.forEach(file => {
        formData.append('attachments[]', file)
      })
      
      const response = await api.post(`/patients/${patientId}/sessions/${sessionId}/attachments`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      console.log('✅ تم رفع المرفقات')
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في رفع مرفقات الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  // حذف مرفق من الجلسة
  const deleteSessionAttachment = async (patientId, sessionId, attachmentIndex) => {
    try {
      error.value = null
      
      console.log('🗑️ حذف المرفق:', attachmentIndex, 'من الجلسة:', sessionId)
      
      const response = await api.delete(`/patients/${patientId}/sessions/${sessionId}/attachments/${attachmentIndex}`)
      
      console.log('✅ تم حذف المرفق')
      
      return response.data
    } catch (err) {
      console.error('❌ خطأ في حذف مرفق الجلسة:', err)
      error.value = err.response?.data?.message || err.message
      throw err
    }
  }

  // تنسيق بيانات الجلسة للنموذج
  const formatSessionForForm = (session) => {
    if (!session) return null
    
    console.log('🔄 تنسيق الجلسة للنموذج:', session)
    
    const formattedSession = {
      title_ar: session.title_ar || session.title || '',
      title_en: session.title_en || session.title || '',
      session_date: session.session_date || '',
      session_time: session.session_time || '',
      therapist_id: session.therapist_id || session.therapist?.id || '',
      status: session.status || 'scheduled',
      progress: session.progress || 0,
      type: session.type || 'individual',
      location: session.location || 'clinic',
      notes_ar: session.notes_ar || session.notes || '',
      notes_en: session.notes_en || session.notes || '',
      report_ar: session.report_ar || session.report || '',
      report_en: session.report_en || session.report || '',
      duration: session.duration || 60,
      attachments: session.attachments || []
    }
    
    console.log('✅ الجلسة المنسقة:', formattedSession)
    
    return formattedSession
  }

  // إدارة الفلاتر
  const setFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters }
  }

  const resetFilters = () => {
    filters.value = {
      status: '',
      type: '',
      location: '',
      date_from: '',
      date_to: '',
      therapist_id: '',
      sort_by: 'session_date',
      sort_order: 'desc'
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

  // مسح البيانات
  const clearSessions = () => {
    sessions.value = []
    currentSession.value = null
    stats.value = null
    availableSlots.value = []
  }

  const clearError = () => {
    error.value = null
  }

  // إعادة تعيين الـ store
  const resetStore = () => {
    sessions.value = []
    currentSession.value = null
    currentPage.value = 1
    totalPages.value = 1
    totalItems.value = 0
    perPage.value = 10
    loading.value = false
    error.value = null
    stats.value = null
    availableSlots.value = []
    therapists.value = []
    filters.value = {
      status: '',
      type: '',
      location: '',
      date_from: '',
      date_to: '',
      therapist_id: '',
      sort_by: 'session_date',
      sort_order: 'desc'
    }
  }

  return {
    // البيانات
    sessions,
    currentSession,
    currentPage,
    totalPages,
    totalItems,
    perPage,
    loading,
    error,
    stats,
    availableSlots,
    therapists,
    filters,
    
    // الإجراءات
    fetchSessions,
    fetchSession,
    createSession,
    updateSession,
    deleteSession,
    fetchStats,
    fetchAvailableSlots,
    fetchTherapists,
    updateSessionStatus,
    updateSessionProgress,
    addSessionNotes,
    addSessionReport,
    uploadSessionAttachments,
    deleteSessionAttachment,
    formatSessionForForm,
    setFilters,
    resetFilters,
    setPage,
    setPerPage,
    clearSessions,
    clearError,
    resetStore
  }
})