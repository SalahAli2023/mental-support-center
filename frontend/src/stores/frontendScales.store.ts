// stores/frontendScales.store.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export const useFrontendScalesStore = defineStore('frontendScales', () => {
  // الحالة - خاصة بالفرونت فقط
  const scales = ref([])
  const categories = ref([])
  const popularMeasures = ref([])
  const currentScale = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const dataLoaded = ref(false)

  // ==================== دوال جلب البيانات للفرونت ====================

  const fetchFrontendScales = async (params = {}) => {
    // منع إعادة التحميل إذا كانت البيانات محملة
    if (dataLoaded.value && !params.force) {
      console.log('✅ بيانات المقاييس محملة مسبقاً')
      return
    }

    console.log('🔄 جلب المقاييس للصفحة الرئيسية...')
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get('/frontend/scales', { params })
      
      // معالجة الاستجابة
      if (response.data && Array.isArray(response.data)) {
        scales.value = response.data
      } else if (response.data && response.data.data) {
        scales.value = response.data.data
      } else {
        scales.value = []
      }
      
      dataLoaded.value = true
      console.log(`📊 تم تحميل ${scales.value.length} مقياس للصفحة الرئيسية`)
      
    } catch (err) {
      console.error('❌ خطأ في جلب المقاييس:', err)
      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchFrontendCategories = async () => {
    try {
      console.log('🔄 جلب تصنيفات المقاييس للصفحة الرئيسية...')
      const response = await api.get('/frontend/scales/categories')
      
      if (response.data && response.data.data) {
        categories.value = response.data.data
      } else {
        categories.value = response.data
      }
      
      console.log(`📂 تم تحميل ${categories.value.length} تصنيف للصفحة الرئيسية`)
    } catch (err) {
      console.error('❌ خطأ في جلب التصنيفات:', err)
      handleError(err)
      throw err
    }
  }

  const fetchFrontendScaleById = async (id) => {
    loading.value = true
    error.value = null
    try {
      console.log(`🔄 جلب المقياس ${id} للصفحة الرئيسية...`)
      
      const response = await api.get(`/frontend/scales/${id}`)
      
      let scaleData
      if (response.data && response.data.data) {
        scaleData = response.data.data
      } else {
        scaleData = response.data
      }
      
      console.log('✅ تم جلب بيانات المقياس للصفحة الرئيسية:', scaleData)
      currentScale.value = scaleData
      return scaleData
    } catch (err) {
      console.error(`❌ خطأ في جلب المقياس ${id}:`, err)
      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchFrontendFullScale = async (id) => {
    loading.value = true
    error.value = null
    try {
      console.log(`🔄 جلب المقياس الكامل ${id} للصفحة الرئيسية...`)
      
      const response = await api.get(`/frontend/scales/${id}/full`)
      
      let scaleData
      if (response.data && response.data.data) {
        scaleData = response.data.data
      } else {
        scaleData = response.data
      }
      
      console.log('✅ المقياس الكامل للصفحة الرئيسية:', scaleData)
      currentScale.value = scaleData
      return scaleData
    } catch (err) {
      console.error(`❌ خطأ في جلب المقياس الكامل ${id}:`, err)
      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const submitFrontendTest = async (scaleId, answers) => {
    loading.value = true
    error.value = null
    try {
      console.log(`🔄 إرسال إجابات الاختبار للمقياس ${scaleId}...`)
      
      const response = await api.post(`/frontend/scales/${scaleId}/submit`, {
        answers: answers
      })
      
      console.log('✅ تم حساب النتيجة بنجاح:', response.data)
      return response.data
    } catch (err) {
      console.error('❌ خطأ في إرسال الإجابات:', err)
      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchPopularScales = async () => {
    try {
      console.log('🔄 جلب المقاييس الشعبية...')
      const response = await api.get('/frontend/scales/popular')
      
      let popularData
      if (response.data && response.data.data) {
        popularData = response.data.data
      } else {
        popularData = response.data
      }
      
      popularMeasures.value = popularData
      console.log('⭐ تم تحميل المقاييس الشعبية:', popularData.length)
      return popularData
    } catch (err) {
      console.error('❌ خطأ في جلب المقاييس الشعبية:', err)
      handleError(err)
      throw err
    }
  }

  const fetchScalesByCategory = async (categoryId) => {
    try {
      console.log(`🔄 جلب المقاييس للفئة ${categoryId}...`)
      const response = await api.get(`/frontend/scales/category/${categoryId}`)
      
      let categoryData
      if (response.data && response.data.data) {
        categoryData = response.data.data
      } else {
        categoryData = response.data
      }
      
      return categoryData
    } catch (err) {
      console.error('❌ خطأ في جلب مقاييس الفئة:', err)
      handleError(err)
      throw err
    }
  }

  // ==================== دوال البحث والفلترة ====================

  const searchScales = async (searchQuery) => {
    try {
      console.log(`🔍 البحث عن: "${searchQuery}"`)
      return await fetchFrontendScales({ search: searchQuery, force: true })
    } catch (err) {
      console.error('❌ خطأ في البحث:', err)
      throw err
    }
  }

  const filterByCategory = async (categoryId) => {
    try {
      console.log(`🎯 تصفية حسب الفئة: ${categoryId}`)
      if (categoryId === 'all') {
        return await fetchFrontendScales({ force: true })
      } else {
        return await fetchScalesByCategory(categoryId)
      }
    } catch (err) {
      console.error('❌ خطأ في التصفية:', err)
      throw err
    }
  }

  // ==================== دوال مساعدة ====================

  const handleError = (err) => {
    if (err.response) {
      let message = `خطأ ${err.response.status}: `
      
      if (err.response.data?.errors) {
        const errors = Object.values(err.response.data.errors).flat()
        message += errors.join(', ')
      } else if (err.response.data?.message) {
        message += err.response.data.message
      } else {
        message += 'فشل في العملية'
      }
      
      error.value = message
      
      console.error('تفاصيل الخطأ:', {
        status: err.response.status,
        data: err.response.data,
        message: message
      })
    } else if (err.request) {
      error.value = 'تعذر الاتصال بالخادم. يرجى التحقق من اتصال الإنترنت.'
    } else {
      error.value = err.message || 'حدث خطأ غير متوقع'
    }
  }

  const resetError = () => {
    error.value = null
  }

  const resetCurrentScale = () => {
    currentScale.value = null
  }

  const resetAllData = () => {
    scales.value = []
    categories.value = []
    popularMeasures.value = []
    currentScale.value = null
    dataLoaded.value = false
    error.value = null
    console.log('🧹 تم إعادة تعيين جميع بيانات الفرونت')
  }

  const getCategoryName = (categoryId) => {
    const category = categories.value.find(cat => cat.id === categoryId)
    return category ? category.name_ar : 'غير معروف'
  }

  const getScaleById = (id) => {
    return scales.value.find(scale => scale.id === id)
  }

  // ==================== الحسابات المحسوبة ====================

  const activeScales = () => {
    return scales.value.filter(scale => scale.is_active)
  }

  const scalesCount = () => {
    return scales.value.length
  }

  const categoriesCount = () => {
    return categories.value.length
  }

  return {
    // الحالة
    scales,
    categories,
    popularMeasures,
    currentScale,
    loading,
    error,
    dataLoaded,

    // دوال جلب البيانات
    fetchFrontendScales,
    fetchFrontendCategories,
    fetchFrontendScaleById,
    fetchFrontendFullScale,
    submitFrontendTest,
    fetchPopularScales,
    fetchScalesByCategory,

    // دوال البحث والفلترة
    searchScales,
    filterByCategory,

    // دوال مساعدة
    resetError,
    resetCurrentScale,
    resetAllData,
    getCategoryName,
    getScaleById,

    // الحسابات المحسوبة
    activeScales,
    scalesCount,
    categoriesCount
  }
})