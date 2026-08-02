import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/utils/api'

export interface ScaleCategory {
  id: string
  name_ar: string
  name_en: string
  description_ar?: string
  description_en?: string
  color?: string
  is_active: boolean
  scales_count?: number
  created_at?: string
  updated_at?: string
}

export interface Scale {
  id: string
  name_ar?: string
  name_en?: string
  description_ar?: string
  description_en?: string
  category_id?: string
  image_url?: string
  max_score?: number
  is_active?: boolean
  questions_count?: number
  interpretations_count?: number
  created_at?: string
  updated_at?: string
  category?: ScaleCategory
  questions?: Question[]
  interpretations?: Interpretation[]
}

export interface Question {
  id?: string
  scale_id?: string
  question_text_ar?: string
  question_text_en?: string
  question_order?: number
  options?: Option[]
  created_at?: string
  updated_at?: string
}

export interface Option {
  id?: string
  question_id?: string
  option_text_ar?: string
  option_text_en?: string
  score_value?: number
  option_order?: number
  created_at?: string
  updated_at?: string
}

export interface Interpretation {
  id?: string
  scale_id?: string
  min_score?: number
  max_score?: number
  interpretation_label_ar?: string
  interpretation_label_en?: string
  description_ar?: string
  description_en?: string
  color?: string
  created_at?: string
  updated_at?: string
}

export interface CreateScaleData {
  category_id: string
  name_ar: string
  name_en: string
  description_ar?: string
  description_en?: string
  image_url?: string
  max_score?: number
  is_active?: boolean
  questions?: Question[]
  interpretations?: Interpretation[]
}

export const useScalesStore = defineStore('scales', () => {
  // الحالة
  const scales = ref<Scale[]>([])
  const categories = ref<ScaleCategory[]>([])
  const currentScale = ref<Scale | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  // الإجراءات الأساسية للمقاييس
  const fetchScales = async (params?: any) => {
    console.log('🔄 جلب المقاييس...')
    loading.value = true
    error.value = null

    try {
      const response = await api.get('/psychological-scales', { params })
      console.log('✅ استجابة المقاييس:', response.data)

      // التعامل مع استجابة Laravel المختلفة
      if (response.data && Array.isArray(response.data)) {
        scales.value = response.data
      } else if (response.data && response.data.data) {
        scales.value = response.data.data
      } else {
        scales.value = []
      }

      console.log(`📊 تم تحميل ${scales.value.length} مقياس`)

    } catch (err: any) {
      console.error('❌ خطأ في جلب المقاييس:', err)
      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchCategories = async () => {
    try {
      console.log('🔄 جلب تصنيفات المقاييس...')
      const response = await api.get('/categories')
      console.log('✅ استجابة التصنيفات:', response.data)

      if (response.data && response.data.data) {
        categories.value = response.data.data
      } else {
        categories.value = response.data
      }

      console.log(`📂 تم تحميل ${categories.value.length} تصنيف`)
    } catch (err: any) {
      console.error('❌ خطأ في جلب التصنيفات:', err)
      handleError(err)
      throw err
    }
  }



  const fetchScaleById = async (id: string) => {
  loading.value = true
  error.value = null
  try {
    console.log(`🔄 جلب المقياس ${id} مع جميع العلاقات...`)

    // الحل 1: استخدام endpoint مختلف
    const response = await api.get(`/psychological-scales/${id}/full`)

    // أو الحل 2: جلب البيانات على حدة
    // const [scaleRes, questionsRes, interpretationsRes] = await Promise.all([
    //   api.get(`/psychological-scales/${id}`),
    //   api.get(`/psychological-scales/${id}/questions`),
    //   api.get(`/psychological-scales/${id}/interpretations`)
    // ])

    let scaleData
    if (response.data && response.data.data) {
      scaleData = response.data.data
    } else {
      scaleData = response.data
    }

    // إذا كان الـ API لا يدعم include، نجلب البيانات بشكل منفصل
    if (!scaleData.questions) {
      console.log('⚠️ الـ API لم يرجع الأسئلة، نجلبها بشكل منفصل...')
      try {
        const questionsResponse = await api.get(`/scales/${id}/questions`)
        scaleData.questions = questionsResponse.data.data || questionsResponse.data
      } catch (err) {
        console.warn('❌ لا يمكن جلب الأسئلة:', err)
        scaleData.questions = []
      }
    }

    if (!scaleData.interpretations) {
      console.log('⚠️ الـ API لم يرجع التفسيرات، نجلبها بشكل منفصل...')
      try {
        const interpretationsResponse = await api.get(`/scales/${id}/interpretations`)
        scaleData.interpretations = interpretationsResponse.data.data || interpretationsResponse.data
      } catch (err) {
        console.warn('❌ لا يمكن جبل التفسيرات:', err)
        scaleData.interpretations = []
      }
    }

    console.log('✅ البيانات النهائية:', {
      id: scaleData.id,
      name: scaleData.name_ar,
      questions: scaleData.questions ? `${scaleData.questions.length} سؤال` : 'لا توجد',
      interpretations: scaleData.interpretations ? `${scaleData.interpretations.length} تفسير` : 'لا توجد'
    })

    currentScale.value = scaleData
    return scaleData
  } catch (err: any) {
    console.error(`❌ خطأ في جلب المقياس ${id}:`, err)
    handleError(err)
    throw err
  } finally {
    loading.value = false
  }
}

  const fetchFullScale = async (id: string) => {
    loading.value = true
    error.value = null
    try {
      console.log(`🔄 جلب المقياس الكامل ${id}...`)

      // محاولة استخدام الـ endpoint الصحيح
      let response
      try {
        response = await api.get(`/psychological-scales/${id}/full`)
      } catch (fullError) {
        console.log('⚠️ فشل endpoint full، جلب البيانات الأساسية...')
        response = await api.get(`/psychological-scales/${id}`, {
          params: {
            include: 'category,questions.options,interpretations'
          }
        })
      }

      let scaleData
      if (response.data && response.data.data) {
        scaleData = response.data.data
      } else {
        scaleData = response.data
      }

      console.log('✅ المقياس الكامل:', scaleData)
      currentScale.value = scaleData
      return scaleData
    } catch (err: any) {
      console.error(`❌ خطأ في جلب المقياس الكامل ${id}:`, err)
      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const createScale = async (scaleData: CreateScaleData) => {
    loading.value = true
    error.value = null
    try {
      console.log('🔄 إنشاء مقياس جديد...', scaleData)

      // تأكد من إرسال البيانات بشكل صحيح
      const response = await api.post('/psychological-scales', {
        category_id: scaleData.category_id,
        name_ar: scaleData.name_ar,
        name_en: scaleData.name_en,
        description_ar: scaleData.description_ar || null,
        description_en: scaleData.description_en || null,
        // إرسال null بدلاً من سلسلة فارغة، وقبول base64 data URLs
        image_url: scaleData.image_url && scaleData.image_url.trim() !== '' ? scaleData.image_url.trim() : null,
        max_score: scaleData.max_score || 100,
        is_active: scaleData.is_active !== undefined ? scaleData.is_active : true
      })

      let newScale
      if (response.data && response.data.data) {
        newScale = response.data.data
      } else {
        newScale = response.data
      }

      scales.value.unshift(newScale)
      console.log('✅ تم إنشاء المقياس بنجاح:', newScale)
      return newScale
    } catch (err: any) {
      console.error('❌ خطأ في إنشاء المقياس:', err)
      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateScale = async (id: string, scaleData: Partial<Scale>) => {
    loading.value = true
    error.value = null
    try {
      console.log(`🔄 تحديث المقياس ${id}...`, scaleData)
      const response = await api.put(`/psychological-scales/${id}`, scaleData)

      let updatedScale
      if (response.data && response.data.data) {
        updatedScale = response.data.data
      } else {
        updatedScale = response.data
      }

      const index = scales.value.findIndex(scale => scale.id === id)
      if (index !== -1) {
        scales.value[index] = { ...scales.value[index], ...updatedScale }
      }
      if (currentScale.value && currentScale.value.id === id) {
        currentScale.value = updatedScale
      }

      console.log('✅ تم تحديث المقياس بنجاح:', updatedScale)
      return updatedScale
    } catch (err: any) {
      console.error(`❌ خطأ في تحديث المقياس ${id}:`, err)
      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteScale = async (id: string) => {
    loading.value = true
    error.value = null
    try {
      console.log(`🔄 حذف المقياس ${id}...`)

      const response = await api.delete(`/psychological-scales/${id}`)

      // التحقق من نجاح العملية
      if (response.data?.success !== false) {
        scales.value = scales.value.filter(scale => scale.id !== id)
        if (currentScale.value && currentScale.value.id === id) {
          currentScale.value = null
        }
        console.log('✅ تم حذف المقياس بنجاح')
      } else {
        throw new Error(response.data?.message || 'فشل في حذف المقياس')
      }
    } catch (err: any) {
      console.error(`❌ خطأ في حذف المقياس ${id}:`, err)

      // معالجة خطأ 404 بشكل خاص
      if (err.response?.status === 404) {
        // إذا كان المقياس غير موجود، قم بإزالته من القائمة المحلية
        scales.value = scales.value.filter(scale => scale.id !== id)
        if (currentScale.value && currentScale.value.id === id) {
          currentScale.value = null
        }
        throw new Error('المقياس غير موجود أو تم حذفه مسبقاً')
      }

      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const toggleScaleStatus = async (id: string) => {
    loading.value = true
    error.value = null
    try {
      console.log(`🔄 تبديل حالة المقياس ${id}...`)
      const response = await api.patch(`/psychological-scales/${id}/toggle-status`)

      let updatedScale
      if (response.data && response.data.data) {
        updatedScale = response.data.data
      } else {
        updatedScale = response.data
      }

      const index = scales.value.findIndex(scale => scale.id === id)
      if (index !== -1) {
        scales.value[index].is_active = updatedScale.is_active
      }

      console.log('✅ تم تبديل الحالة بنجاح:', updatedScale)
      return updatedScale
    } catch (err: any) {
      console.error(`❌ خطأ في تبديل حالة المقياس ${id}:`, err)
      handleError(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // 🔥 دوال إدارة التصنيفات - المطلوبة لحل المشكلة
  const toggleCategoryStatus = async (id: string): Promise<void> => {
    try {
      console.log(`🔄 تبديل حالة التصنيف ${id}...`)

      const category = categories.value.find(cat => cat.id === id);
      if (!category) {
        throw new Error('التصنيف غير موجود');
      }

      const newStatus = !category.is_active;

      // تحديث محلي أولاً
      category.is_active = newStatus;

      // تحديث في الخادم
      const response = await api.patch(`/categories/${id}/toggle-status`);

      // تحديث البيانات من الاستجابة
      if (response.data?.data) {
        const index = categories.value.findIndex(cat => cat.id === id);
        if (index !== -1) {
          categories.value[index] = response.data.data;
        }
      }

      console.log('✅ تم تبديل حالة التصنيف بنجاح');
      return response.data;
    } catch (err: any) {
      console.error('❌ خطأ في تبديل حالة التصنيف:', err);

      // التراجع عن التحديث المحلي في حالة الخطأ
      const category = categories.value.find(cat => cat.id === id);
      if (category) {
        category.is_active = !category.is_active;
      }

      handleError(err);
      throw err;
    }
  }

  const deleteCategory = async (id: string): Promise<void> => {
    loading.value = true;
    try {
      console.log(`🔄 حذف التصنيف ${id}...`);

      const response = await api.delete(`/categories/${id}`);

      // التحقق من نجاح العملية
      if (response.data?.success !== false) {
        // حذف محلي
        const index = categories.value.findIndex(cat => cat.id === id);
        if (index !== -1) {
          categories.value.splice(index, 1);
        }
        console.log('✅ تم حذف التصنيف بنجاح');
      } else {
        throw new Error(response.data?.message || 'فشل في حذف التصنيف');
      }
    } catch (err: any) {
      console.error('❌ خطأ في حذف التصنيف:', err);

      // معالجة خطأ 404 بشكل خاص
      if (err.response?.status === 404) {
        // إذا كان التصنيف غير موجود، قم بإزالته من القائمة المحلية
        const index = categories.value.findIndex(cat => cat.id === id);
        if (index !== -1) {
          categories.value.splice(index, 1);
        }
        throw new Error('التصنيف غير موجود أو تم حذفه مسبقاً');
      }

      handleError(err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  const createCategory = async (categoryData: any): Promise<ScaleCategory> => {
    loading.value = true;
    error.value = null;
    try {
      console.log('🔄 إنشاء تصنيف جديد...', categoryData);

      const response = await api.post('/categories', categoryData);

      let newCategory;
      if (response.data && response.data.data) {
        newCategory = response.data.data;
      } else {
        newCategory = response.data;
      }

      categories.value.unshift(newCategory);
      console.log('✅ تم إنشاء التصنيف بنجاح:', newCategory);
      return newCategory;
    } catch (err: any) {
      console.error('❌ خطأ في إنشاء التصنيف:', err);
      handleError(err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  const updateCategory = async (id: string, categoryData: any): Promise<ScaleCategory> => {
    loading.value = true;
    error.value = null;
    try {
      console.log(`🔄 تحديث التصنيف ${id}...`, categoryData);

      const response = await api.put(`/categories/${id}`, categoryData);

      let updatedCategory;
      if (response.data && response.data.data) {
        updatedCategory = response.data.data;
      } else {
        updatedCategory = response.data;
      }

      // تحديث محلي
      const index = categories.value.findIndex(cat => cat.id === id);
      if (index !== -1) {
        categories.value[index] = updatedCategory;
      }

      console.log('✅ تم تحديث التصنيف بنجاح:', updatedCategory);
      return updatedCategory;
    } catch (err: any) {
      console.error(`❌ خطأ في تحديث التصنيف ${id}:`, err);
      handleError(err);
      throw err;
    } finally {
      loading.value = false;
    }
  }

  // دالة لرفع الصور (اختياري)
  const uploadImage = async (file: File) => {
    try {
      console.log('🔼 رفع الصورة...')
      const formData = new FormData()
      formData.append('image', file)

      const response = await api.post('/upload-image', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      console.log('✅ تم رفع الصورة:', response.data)
      return response.data.url
    } catch (err: any) {
      console.error('❌ خطأ في رفع الصورة:', err)
      throw err
    }
  }

  // دوال مساعدة
  const handleError = (err: any) => {
    if (err.response) {
      let message = `خطأ ${err.response.status}: `

      if (err.response.data?.errors) {
        // أخطاء التحقق من Laravel
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

  const getCategoryName = (categoryId: string) => {
    const category = categories.value.find(cat => cat.id === categoryId)
    return category ? category.name_ar : 'غير معروف'
  }

  return {
    // الحالة
    scales,
    categories,
    currentScale,
    loading,
    error,

    // الإجراءات الأساسية للمقاييس
    fetchScales,
    fetchCategories,
    fetchScaleById,
    fetchFullScale,
    createScale,
    updateScale,
    deleteScale,
    toggleScaleStatus,
    uploadImage,

    // 🔥 دوال إدارة التصنيفات - المضافة
    toggleCategoryStatus,
    deleteCategory,
    createCategory,
    updateCategory,

    // دوال مساعدة
    resetError,
    resetCurrentScale,
    getCategoryName
  }
})