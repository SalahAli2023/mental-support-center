<script setup lang="ts">
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import axios from 'axios'

// Types
interface Program {
  id: string
  name_ar: string
  name_en: string
  description_ar?: string
  description_en?: string
  target_category_ar?: string
  target_category_en?: string
  duration: number
  max_duration_days?: number
  session_duration_minutes?: number
  session_gap_hours?: number
  activity_gap_hours?: number
  status: 'active' | 'inactive' | 'draft'
  scale_id?: string
  image_url?: string
  created_at: string
  updated_at: string
  sessions_count?: number
  total_duration?: string
  scale?: PsychologicalScale
  sessions?: Session[]
}

interface Session {
  id: string
  program_id: string
  title_ar: string
  title_en: string
  session_order: number
  goal_ar?: string
  goal_en?: string
  duration: number
  created_at: string
  updated_at: string
  activities_count?: number
  mandatory_activities_count?: number
  activities?: Activity[]
}

interface Activity {
  id: string
  session_id: string
  name_ar: string
  name_en: string
  activity_type: 'text' | 'audio' | 'video' | 'file' | 'form' | 'exercise' | 'reflection_questions' | 'quiz'
  instructions_ar?: string
  instructions_en?: string
  content_ar?: string
  content_en?: string
  media_url?: string
  media_type?: string
  duration_minutes?: number
  activity_order?: number
  is_active?: boolean
  scale_id?: string | null
  activity_config?: any
  is_mandatory: boolean
  created_at: string
  updated_at: string
}

interface PsychologicalScale {
  id: string
  name_ar: string
  name_en: string
  description_ar?: string
  description_en?: string
  is_active: boolean
}

// State
const activeTab = ref<'programs' | 'sessions' | 'activities'>('programs')
const searchQuery = ref('')
const statusFilter = ref('')

const selectedProgram = ref<Program | null>(null)
const selectedSession = ref<Session | null>(null)

// Modal States
const showProgramModal = ref(false)
const showSessionModal = ref(false)
const showActivityModal = ref(false)
const showDeleteModal = ref(false)

// Editing
const editingProgram = ref<Program | null>(null)
const editingSession = ref<Session | null>(null)
const editingActivity = ref<Activity | null>(null)

// Data
const programs = ref<Program[]>([])
const sessions = ref<Session[]>([])
const activities = ref<Activity[]>([])
const psychologicalScales = ref<PsychologicalScale[]>([])

// Loading States
const isLoading = ref(false)
const isLoadingPrograms = ref(false)
const isLoadingSessions = ref(false)
const isLoadingActivities = ref(false)

// Image
const programImagePreview = ref<string | null>(null)
const originalImageUrl = ref<string | null>(null)

// Pagination
const programsCurrentPage = ref(1)
const programsTotal = ref(0)
const programsPerPage = ref(10)

const sessionsCurrentPage = ref(1)
const sessionsTotal = ref(0)
const sessionsPerPage = ref(10)

const activitiesCurrentPage = ref(1)
const activitiesTotal = ref(0)
const activitiesPerPage = ref(10)

// API Base URL
const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

// Auth Store
const authStore = useAuthStore()

// Forms
const programForm = ref({
  name_ar: '',
  name_en: '',
  description_ar: '',
  description_en: '',
  target_category_ar: '',
  target_category_en: '',
  duration: 30,
  max_duration_days: 90,
  session_duration_minutes: 60,
  session_gap_hours: 48,
  activity_gap_hours: 24,
  status: 'draft' as 'active' | 'inactive' | 'draft',
  scale_id: '',
  image: null as File | null
})

const sessionForm = ref({
  program_id: '',
  title_ar: '',
  title_en: '',
  session_order: 1,
  goal_ar: '',
  goal_en: '',
  duration: 60
})

const activityForm = ref({
  session_id: '',
  name_ar: '',
  name_en: '',
  activity_type: 'text' as 'text' | 'audio' | 'video' | 'file' | 'form' | 'exercise' | 'reflection_questions' | 'quiz',
  instructions_ar: '',
  instructions_en: '',
  content_ar: '',
  content_en: '',
  media_url: '',
  media_type: 'video',
  duration_minutes: undefined as number | undefined,
  activity_order: 1,
  is_active: true,
  scale_id: null as string | null,
  activity_config: null as any,
  is_mandatory: true
})

// Utility Functions
function formatDate(dateString: string) {
  if (!dateString) return 'غير محدد'
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('ar-SA', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (error) {
    return 'غير محدد'
  }
}

function formatDateShort(dateString: string) {
  if (!dateString) return 'غير محدد'
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('ar-SA')
  } catch (error) {
    return 'غير محدد'
  }
}

function getProgramStatusText(program: Program) {
  const statusLabels = {
    'active': 'نشط',
    'inactive': 'غير نشط',
    'draft': 'مسودة'
  }
  return statusLabels[program.status] || 'غير محدد'
}

function getActivityTypeText(type: string) {
  const typeLabels = {
    'text': 'نص',
    'audio': 'صوتي',
    'video': 'فيديو',
    'file': 'ملف',
    'form': 'نموذج',
    'exercise': 'تمرين',
    'reflection_questions': 'أسئلة انعكاسية',
    'quiz': 'اختبار'
  }
  return typeLabels[type as keyof typeof typeLabels] || 'غير محدد'
}

function getMandatoryText(isMandatory: boolean) {
  return isMandatory ? 'إجباري' : 'اختياري'
}

function getScaleName(program: Program): string {
  if (!program.scale_id) return 'بدون مقياس'
  const scale = psychologicalScales.value.find(s => s.id === program.scale_id)
  return scale ? scale.name_ar : 'مقياس غير معروف'
}

// دالة لبناء روابط الصور - مصممة لـ media/
function getSafeImageUrl(url?: string): string {
  const baseUrl = API_BASE.replace('/api', '');
  
  if (!url) {
    // صورة افتراضية
    return `${baseUrl}/media/defaults/program-default.jpg`;
  }
  
  // ✅ إذا كان الرابط يبدأ بـ media/
  if (url.startsWith('media/')) {
    return `${baseUrl}/${url}`;
  }
  
  // ✅ إذا كان الرابط كاملاً
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url;
  }
  
  // ✅ إذا كان الرابط يبدأ بـ /media/
  if (url.startsWith('/media/')) {
    return `${baseUrl}${url}`;
  }
  
  // ✅ بخلاف ذلك، أضف media/
  if (!url.includes('/')) {
    return `${baseUrl}/media/programs/${url}`;
  }
  
  // ✅ آخر محاولة
  return `${baseUrl}/${url}`;
}

// دالة لتحميل المقاييس النفسية مع محاولات متعددة
async function loadPsychologicalScales() {
  try {
    console.log('📋 Loading psychological scales...')
    
    // المحاولة الأولى: المسار المباشر
    let response
    try {
      response = await api.get('/psychological-scales/active/list')
      console.log('✅ Scales loaded from /psychological-scales/active/list')
    } catch (firstError) {
      console.log('🔄 Trying alternative route...')
      // المحاولة الثانية: المسار مع فلتر
      response = await api.get('/psychological-scales', {
        params: {
          is_active: true,
          all: true
        }
      })
      console.log('✅ Scales loaded from /psychological-scales?is_active=true')
    }
    
    console.log('📊 Scales response:', {
      success: response.data?.success,
      dataLength: response.data?.data?.length,
      hasData: !!response.data?.data
    })
    
    // معالجة البيانات
    let scalesData = []
    
    if (response.data?.success && response.data.data) {
      scalesData = response.data.data
    } else if (response.data?.data) {
      scalesData = response.data.data
    } else if (Array.isArray(response.data)) {
      scalesData = response.data
    }
    
    // فلترة المقاييس النشطة
    psychologicalScales.value = scalesData.filter(scale => 
      scale.is_active === true || scale.is_active === 1 || scale.status === 'active'
    )
    
    console.log(`✅ Loaded ${psychologicalScales.value.length} active scales`)
    
    // إذا لم توجد مقاييس، إنشاء بيانات تجريبية للاختبار
    if (psychologicalScales.value.length === 0) {
      console.warn('⚠️ No scales found, creating demo data')
      psychologicalScales.value = [
        {
          id: 'demo-scale-1',
          name_ar: 'مقياس القلق العام',
          name_en: 'General Anxiety Scale',
          is_active: true
        },
        {
          id: 'demo-scale-2',
          name_ar: 'مقياس الاكتئاب',
          name_en: 'Depression Scale',
          is_active: true
        },
        {
          id: 'demo-scale-3',
          name_ar: 'مقياس تقدير الذات',
          name_en: 'Self Esteem Scale',
          is_active: true
        }
      ]
    }
    
  } catch (error: any) {
    console.error('❌ Error loading psychological scales:', {
      message: error.message,
      response: error.response?.data,
      url: error.config?.url
    })
    
    // بيانات تجريبية في حالة الفشل
    psychologicalScales.value = [
      {
        id: 'test-scale-1',
        name_ar: 'مقياس الاختبار 1',
        name_en: 'Test Scale 1',
        is_active: true
      },
      {
        id: 'test-scale-2',
        name_ar: 'مقياس الاختبار 2',
        name_en: 'Test Scale 2',
        is_active: true
      }
    ]
    
    console.log('ℹ️ Using demo scales due to API error')
  }
}

// API Client
const api = axios.create({
  baseURL: API_BASE,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  timeout: 30000
})

// Add Authorization Interceptor
api.interceptors.request.use(config => {
  const token = authStore.token || localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  
  if (config.method === 'get') {
    config.params = {
      ...config.params,
      _t: Date.now()
    }
  }
  
  return config
}, error => {
  return Promise.reject(error)
})

// ==================== API Functions ====================

// Load Programs
async function loadPrograms() {
  try {
    isLoadingPrograms.value = true
    console.log('📋 Loading programs...')

    const response = await api.get('/programs', {
      params: {
        page: programsCurrentPage.value,
        per_page: programsPerPage.value,
        search: searchQuery.value,
        status: statusFilter.value || undefined
      }
    })

    if (response.data?.success && response.data.data) {
      programs.value = response.data.data
      programsTotal.value = response.data.meta?.total || programs.value.length
      console.log(`✅ Loaded ${programs.value.length} programs`)
      
      // ✅ سجل بيانات كل برنامج للتصحيح
      programs.value.forEach((program, index) => {
        console.log(`📊 Program ${index + 1}:`, {
          id: program.id,
          name: program.name_ar,
          image_url: program.image_url,
          full_url: getSafeImageUrl(program.image_url)
        })
      })
    } else {
      console.error('❌ Unknown response structure:', response.data)
      programs.value = []
      programsTotal.value = 0
    }
  } catch (error: any) {
    console.error('❌ Error loading programs:', error)
    showError('فشل تحميل البرامج: ' + error.message)
    programs.value = []
    programsTotal.value = 0
  } finally {
    isLoadingPrograms.value = false
  }
}

// Load Program Sessions
async function loadProgramSessions(programId: string) {
  try {
    isLoadingSessions.value = true
    sessions.value = []
    sessionsTotal.value = 0
    
    console.log(`📋 Loading sessions for program: ${programId}`)
    
    // ✅ استخدم المسار الجديد مع معامل program_id
    const response = await api.get('/program-sessions', {
      params: {
        program_id: programId
      }
    })
    
    console.log('📊 Sessions response:', {
      success: response.data?.success,
      dataLength: response.data?.data?.length
    })
    
    if (response.data?.success && response.data.data) {
      sessions.value = response.data.data
      sessionsTotal.value = response.data.data.length
      console.log(`✅ Loaded ${sessions.value.length} sessions`)
    } else {
      console.warn('⚠️ No sessions found or unexpected response structure')
      sessions.value = []
      sessionsTotal.value = 0
    }
  } catch (error: any) {
    console.error('❌ Error loading sessions:', error)
    showError('فشل تحميل الجلسات: ' + error.message)
    sessions.value = []
    sessionsTotal.value = 0
  } finally {
    isLoadingSessions.value = false
  }
}

// Load Session Activities
async function loadSessionActivities(sessionId: string) {
  try {
    isLoadingActivities.value = true
    activities.value = []
    activitiesTotal.value = 0
    
    console.log(`📋 Loading activities for session: ${sessionId}`)
    
    // المحاولة الأولى: استخدام endpoint المباشر للجلسة
    const response = await api.get(`/sessions/${sessionId}/activities`)
    
    console.log('✅ Session activities API response:', {
      status: response.status,
      data: response.data,
      url: response.config.url
    })
    
    if (response.data?.success && response.data.data) {
      activities.value = response.data.data
      activitiesTotal.value = response.data.data.length
      console.log(`✅ Loaded ${activities.value.length} activities from direct endpoint`)
    } else {
      // المحاولة الثانية: استخدام endpoint الأنشطة مع التصفية
      const fallbackResponse = await api.get('/activities', {
        params: {
          session_id: sessionId,
          all: true
        }
      })
      
      console.log('🔄 Fallback response:', fallbackResponse.data)
      
      if (fallbackResponse.data?.success && fallbackResponse.data.data) {
        activities.value = fallbackResponse.data.data
        activitiesTotal.value = fallbackResponse.data.data.length
        console.log(`✅ Loaded ${activities.value.length} activities from filtered endpoint`)
      } else {
        console.warn('⚠️ No activities found for this session')
        activities.value = []
        activitiesTotal.value = 0
      }
    }
    
  } catch (error: any) {
    console.error('❌ Error loading session activities:', {
      message: error.message,
      response: error.response?.data,
      status: error.response?.status,
      url: error.config?.url
    })
    
    // محاولة بديلة أخيرة
    try {
      console.log('🔄 Trying alternative method...')
      const allResponse = await api.get('/activities')
      if (allResponse.data?.success && allResponse.data.data) {
        const allActivities = allResponse.data.data
        activities.value = allActivities.filter((activity: Activity) => 
          activity.session_id === sessionId
        )
        activitiesTotal.value = activities.value.length
        console.log(`✅ Loaded ${activities.value.length} activities using filtering method`)
      } else {
        activities.value = []
        activitiesTotal.value = 0
      }
    } catch (fallbackError) {
      console.error('❌ Fallback also failed:', fallbackError)
      activities.value = []
      activitiesTotal.value = 0
    }
    
    // عرض رسالة خطأ للمستخدم فقط إذا لم يكن هناك أنشطة
    if (activities.value.length === 0) {
      console.log('ℹ️ No activities to display')
    }
  } finally {
    isLoadingActivities.value = false
  }
}

// وظيفة حفظ البرنامج
async function saveProgram() {
  try {
    isLoading.value = true
    
    console.log('🔍 ===== START PROGRAM SAVE =====')
    
    // التحقق من البيانات المطلوبة
    if (!programForm.value.name_ar || !programForm.value.name_en) {
      showError('❌ جميع الحقول المطلوبة (*) يجب تعبئتها')
      isLoading.value = false
      return
    }
    
    // ✅ تحميل المقاييس إذا لم تكن محملة
    if (psychologicalScales.value.length === 0) {
      console.log('📋 Loading scales before saving...')
      await loadPsychologicalScales()
    }
    
    // استخدام FormData
    const formData = new FormData()
    
    // أضف الحقول الأساسية
    formData.append('name_ar', programForm.value.name_ar)
    formData.append('name_en', programForm.value.name_en)
    formData.append('description_ar', programForm.value.description_ar || '')
    formData.append('description_en', programForm.value.description_en || '')
    formData.append('target_category_ar', programForm.value.target_category_ar || '')
    formData.append('target_category_en', programForm.value.target_category_en || '')
    formData.append('duration', programForm.value.duration.toString())
    formData.append('max_duration_days', programForm.value.max_duration_days?.toString() || '')
    formData.append('session_duration_minutes', programForm.value.session_duration_minutes?.toString() || '')
    formData.append('session_gap_hours', programForm.value.session_gap_hours?.toString() || '0')
    formData.append('activity_gap_hours', programForm.value.activity_gap_hours?.toString() || '0')
    formData.append('status', programForm.value.status)
    
    // إضافة scale_id إذا كان موجوداً
    if (programForm.value.scale_id) {
      formData.append('scale_id', programForm.value.scale_id)
      console.log('📝 Adding scale_id:', programForm.value.scale_id)
    }
    
    // معالجة الصورة
    if (programForm.value.image) {
      console.log('🖼️ Adding image file:', {
        name: programForm.value.image.name,
        size: programForm.value.image.size,
        type: programForm.value.image.type
      })
      formData.append('image', programForm.value.image)
    }
    
    let response
    if (editingProgram.value) {
      console.log('✏️ UPDATING program ID:', editingProgram.value.id)
      
      // استخدام POST مع _method=PUT لـ FormData
      formData.append('_method', 'PUT')
      
      response = await api.post(`/programs/${editingProgram.value.id}`, formData, {
        headers: { 
          'Content-Type': 'multipart/form-data'
        }
      })
      
      console.log(`✅ Program update successful`)
      
    } else {
      console.log('➕ CREATING new program')
      
      response = await api.post('/programs', formData, {
        headers: { 
          'Content-Type': 'multipart/form-data'
        }
      })
    }
    
    console.log('✅ Save program response:', response?.data)
    console.log('🔍 ===== END PROGRAM SAVE =====')
    
    if (response?.data?.success) {
      showSuccess(response.data.message || '✅ تم حفظ البرنامج بنجاح')
      closeModals()
      
      // إعادة تحميل البيانات
      await loadPrograms()
      
    } else {
      showError(response?.data?.message || 'فشل حفظ البرنامج')
    }
  } catch (error: any) {
    console.error('❌ Error saving program:', error)
    
    if (error.response?.status === 422) {
      const errors = error.response.data.errors
      if (errors) {
        let errorMessage = '❌ خطأ في التحقق:\n'
        Object.keys(errors).forEach(key => {
          errorMessage += `• ${errors[key].join(', ')}\n`
        })
        showError(errorMessage)
      }
    } else {
      showError(`❌ خطأ: ${error.response?.data?.message || 'حدث خطأ غير معروف'}`)
    }
  } finally {
    isLoading.value = false
  }
}

// وظيفة حفظ الجلسة
async function saveSession() {
  try {
    isLoading.value = true
    
    console.log('🔍 ===== START SESSION SAVE =====')
    console.log('📤 Session form data:', sessionForm.value)
    
    // التحقق من البيانات المطلوبة
    if (!sessionForm.value.title_ar || !sessionForm.value.title_en) {
      showError('❌ جميع الحقول المطلوبة (*) يجب تعبئتها')
      isLoading.value = false
      return
    }
    
    // تأكد من وجود program_id
    if (!sessionForm.value.program_id && selectedProgram.value) {
      sessionForm.value.program_id = selectedProgram.value.id
    }
    
    if (!sessionForm.value.program_id) {
      showError('❌ يجب اختيار برنامج')
      isLoading.value = false
      return
    }
    
    // البيانات المرسلة
    const data = {
      program_id: sessionForm.value.program_id,
      session_order: parseInt(sessionForm.value.session_order) || 1,
      title_ar: sessionForm.value.title_ar.trim(),
      title_en: sessionForm.value.title_en.trim(),
      goal_ar: sessionForm.value.goal_ar?.trim() || '',
      goal_en: sessionForm.value.goal_en?.trim() || '',
      duration: parseInt(sessionForm.value.duration) || 60
    }
    
    console.log('📤 Sending data to program-sessions:', data)
    
    let response
    if (editingSession.value) {
      console.log(`✏️ Updating session ID: ${editingSession.value.id}`)
      // ✅ استخدم المسار الجديد
      response = await api.put(`/program-sessions/${editingSession.value.id}`, data)
    } else {
      console.log('➕ Creating new session')
      // ✅ استخدم المسار الجديد
      response = await api.post('/program-sessions', data)
    }
    
    console.log('✅ Response:', response?.data)
    console.log('🔍 ===== END SESSION SAVE =====')
    
    if (response?.data?.success) {
      showSuccess(response.data.message || '✅ تم حفظ الجلسة بنجاح')
      closeModals()
      
      // إعادة تحميل الجلسات
      if (selectedProgram.value) {
        await loadProgramSessions(selectedProgram.value.id)
      }
    } else {
      showError(response?.data?.message || '❌ فشل حفظ الجلسة')
    }
    
  } catch (error: any) {
    console.error('❌ Error saving session:', error)
    console.error('❌ Response data:', error.response?.data)
    
    if (error.response?.status === 422) {
      const errors = error.response.data.errors
      if (errors) {
        let errorMessage = '❌ خطأ في التحقق:\n'
        Object.keys(errors).forEach(key => {
          errorMessage += `• ${errors[key].join(', ')}\n`
        })
        showError(errorMessage)
      }
    } else {
      showError(`❌ خطأ: ${error.response?.data?.message || 'حدث خطأ غير معروف'}`)
    }
  } finally {
    isLoading.value = false
  }
}

// وظيفة حفظ النشاط
async function saveActivity() {
  try {
    isLoading.value = true
    
    console.log('🔍 ===== START ACTIVITY SAVE =====')
    
    // التحقق من البيانات المطلوبة
    if (!activityForm.value.name_ar || !activityForm.value.name_en) {
      showError('❌ جميع الحقول المطلوبة (*) يجب تعبئتها')
      isLoading.value = false
      return
    }
    
    // التأكد من وجود session_id
    const sessionId = activityForm.value.session_id || selectedSession.value?.id
    if (!sessionId) {
      showError('❌ يجب اختيار جلسة للنشاط')
      isLoading.value = false
      return
    }
    
    const data = {
      session_id: sessionId,
      name_ar: activityForm.value.name_ar,
      name_en: activityForm.value.name_en,
      activity_type: activityForm.value.activity_type,
      instructions_ar: activityForm.value.instructions_ar || '',
      instructions_en: activityForm.value.instructions_en || '',
      content_ar: activityForm.value.content_ar || '',
      content_en: activityForm.value.content_en || '',
      media_url: activityForm.value.media_url || '',
      media_type: activityForm.value.media_type || '',
      duration_minutes: activityForm.value.duration_minutes,
      activity_order: activityForm.value.activity_order,
      is_active: activityForm.value.is_active,
      scale_id: activityForm.value.activity_type === 'quiz' ? activityForm.value.scale_id : null,
      activity_config: activityForm.value.activity_config,
      is_mandatory: activityForm.value.is_mandatory
    }
    
    let response
    if (editingActivity.value) {
      response = await api.put(`/activities/${editingActivity.value.id}`, data)
    } else {
      response = await api.post('/activities', data)
    }
    
    console.log('✅ Save activity response:', response?.data)
    console.log('🔍 ===== END ACTIVITY SAVE =====')
    
    if (response?.data?.success) {
      showSuccess(response.data.message || '✅ تم حفظ النشاط بنجاح')
      closeModals()
      
      // إعادة تحميل الأنشطة
      if (selectedSession.value) {
        await loadSessionActivities(selectedSession.value.id)
      }
    } else {
      showError(response?.data?.message || 'فشل حفظ النشاط')
    }
  } catch (error: any) {
    console.error('Error saving activity:', error)
    
    if (error.response?.status === 422) {
      const errors = error.response.data.errors
      if (errors) {
        let errorMessage = '❌ خطأ في التحقق:\n'
        Object.keys(errors).forEach(key => {
          errorMessage += `• ${errors[key].join(', ')}\n`
        })
        showError(errorMessage)
      }
    } else {
      showError(`❌ خطأ: ${error.response?.data?.message || 'حدث خطأ غير معروف'}`)
    }
  } finally {
    isLoading.value = false
  }
}

// وظيفة حذف العنصر
async function deleteItem() {
  try {
    isLoading.value = true
    
    let response
    if (editingProgram.value) {
      response = await api.delete(`/programs/${editingProgram.value.id}`)
    } else if (editingSession.value) {
      // ✅ استخدم المسار الجديد
      response = await api.delete(`/program-sessions/${editingSession.value.id}`)
    } else if (editingActivity.value) {
      response = await api.delete(`/activities/${editingActivity.value.id}`)
    }
    
    if (response?.data?.success) {
      showSuccess(response.data.message || '✅ تم الحذف بنجاح')
      showDeleteModal.value = false
      
      // إعادة تحميل البيانات
      switch (activeTab.value) {
        case 'programs':
          await loadPrograms()
          break
        case 'sessions':
          if (selectedProgram.value) {
            await loadProgramSessions(selectedProgram.value.id)
          }
          break
        case 'activities':
          if (selectedSession.value) {
            await loadSessionActivities(selectedSession.value.id)
          }
          break
      }
    } else {
      showError(response?.data?.message || '❌ فشل الحذف')
    }
  } catch (error: any) {
    console.error('Error deleting:', error)
    showError('❌ فشل الحذف: ' + error.message)
  } finally {
    isLoading.value = false
  }
}

// وظيفة تغيير حالة البرنامج
async function toggleProgramStatus(program: Program) {
  try {
    const newStatus = program.status === 'active' ? 'inactive' : 'active'
    const response = await api.patch(`/programs/${program.id}/status`, { status: newStatus })
    if (response.data.success) {
      showSuccess(response.data.message)
      await loadPrograms()
    }
  } catch (error: any) {
    showError('❌ فشل تغيير الحالة: ' + error.message)
  }
}

// UI Helpers
function confirmDelete(type: 'program' | 'session' | 'activity', item: any) {
  if (type === 'program') {
    editingProgram.value = item
  } else if (type === 'session') {
    editingSession.value = item
  } else if (type === 'activity') {
    editingActivity.value = item
  }
  showDeleteModal.value = true
}

function openProgramModal(program?: Program) {
  editingProgram.value = program || null
  
  // ✅ تحميل المقاييس إذا لم تكن محملة
  if (psychologicalScales.value.length === 0) {
    console.log('📋 Loading scales before opening modal...')
    loadPsychologicalScales()
  }
  
  if (program) {
    console.log('📂 Opening program modal for edit:', program.id)
    console.log('📊 Program data:', {
      scale_id: program.scale_id,
      scales_available: psychologicalScales.value.length
    })
    
    programForm.value = {
      name_ar: program.name_ar || '',
      name_en: program.name_en || '',
      description_ar: program.description_ar || '',
      description_en: program.description_en || '',
      target_category_ar: program.target_category_ar || '',
      target_category_en: program.target_category_en || '',
      duration: program.duration || 30,
      max_duration_days: program.max_duration_days ?? 90,
      session_duration_minutes: program.session_duration_minutes ?? 60,
      session_gap_hours: program.session_gap_hours ?? 48,
      activity_gap_hours: program.activity_gap_hours ?? 24,
      status: program.status,
      scale_id: program.scale_id || '',
      image: null
    }
    
    originalImageUrl.value = program.image_url || null
    
    if (program.image_url) {
      programImagePreview.value = getSafeImageUrl(program.image_url)
    } else {
      programImagePreview.value = null
    }
  } else {
    programForm.value = {
      name_ar: '',
      name_en: '',
      description_ar: '',
      description_en: '',
      target_category_ar: '',
      target_category_en: '',
      duration: 30,
      max_duration_days: 90,
      session_duration_minutes: 60,
      session_gap_hours: 48,
      activity_gap_hours: 24,
      status: 'draft',
      scale_id: '',
      image: null
    }
    programImagePreview.value = null
    originalImageUrl.value = null
    console.log('📂 Opening program modal for creation')
  }
  showProgramModal.value = true
}

function openSessionModal(session?: Session) {
  editingSession.value = session || null
  if (session) {
    console.log('📂 Opening session modal for edit:', session.id)
    
    sessionForm.value = {
      program_id: session.program_id,
      session_order: session.session_order,
      title_ar: session.title_ar || '',
      title_en: session.title_en || '',
      goal_ar: session.goal_ar || '',
      goal_en: session.goal_en || '',
      duration: session.duration || 60
    }
  } else {
    const programId = selectedProgram.value?.id || ''
    
    let nextOrder = 1
    if (selectedProgram.value && sessions.value.length > 0) {
      const maxOrder = Math.max(...sessions.value.map(s => s.session_order || 0))
      nextOrder = maxOrder + 1
    }
    
    sessionForm.value = {
      program_id: programId,
      session_order: nextOrder,
      title_ar: '',
      title_en: '',
      goal_ar: '',
      goal_en: '',
      duration: 60
    }
  }
  showSessionModal.value = true
}

function openActivityModal(activity?: Activity) {
  editingActivity.value = activity || null
  if (activity) {
    activityForm.value = {
      session_id: activity.session_id,
      name_ar: activity.name_ar || '',
      name_en: activity.name_en || '',
      activity_type: activity.activity_type,
      instructions_ar: activity.instructions_ar || '',
      instructions_en: activity.instructions_en || '',
      content_ar: activity.content_ar || '',
      content_en: activity.content_en || '',
      media_url: activity.media_url || '',
      media_type: activity.media_type || 'video',
      duration_minutes: activity.duration_minutes,
      activity_order: activity.activity_order || 1,
      is_active: activity.is_active ?? true,
      scale_id: activity.scale_id ?? null,
      activity_config: activity.activity_config ?? null,
      is_mandatory: activity.is_mandatory
    }
  } else {
    const sessionId = selectedSession.value?.id || ''
    
    activityForm.value = {
      session_id: sessionId,
      name_ar: '',
      name_en: '',
      activity_type: 'text',
      instructions_ar: '',
      instructions_en: '',
      content_ar: '',
      content_en: '',
      media_url: '',
      media_type: 'video',
      duration_minutes: undefined,
      activity_order: 1,
      is_active: true,
      scale_id: null,
      activity_config: null,
      is_mandatory: true
    }
  }
  showActivityModal.value = true
}

async function viewProgramSessions(program: Program) {
  try {
    selectedProgram.value = program
    selectedSession.value = null
    activeTab.value = 'sessions'
    
    await nextTick()
    
    console.log(`🔍 Loading sessions for program: ${program.id}`)
    await loadProgramSessions(program.id)
    
  } catch (error) {
    console.error('Error in viewProgramSessions:', error)
    showError('❌ فشل تحميل جلسات البرنامج')
  }
}

async function viewSessionActivities(session: Session) {
  try {
    selectedSession.value = session
    activeTab.value = 'activities'
    
    await nextTick()
    
    await loadSessionActivities(session.id)
    
  } catch (error) {
    console.error('Error in viewSessionActivities:', error)
    showError('❌ فشل تحميل أنشطة الجلسة')
  }
}

function closeModals() {
  showProgramModal.value = false
  showSessionModal.value = false
  showActivityModal.value = false
  showDeleteModal.value = false
  
  editingProgram.value = null
  editingSession.value = null
  editingActivity.value = null
  
  programImagePreview.value = null
  originalImageUrl.value = null
  
  // إعادة تعيين النماذج
  programForm.value = {
    name_ar: '',
    name_en: '',
    description_ar: '',
    description_en: '',
    target_category_ar: '',
    target_category_en: '',
    duration: 30,
    status: 'draft',
    scale_id: '',
    image: null
  }
  
  sessionForm.value = {
    program_id: '',
    session_order: 1,
    title_ar: '',
    title_en: '',
    goal_ar: '',
    goal_en: '',
    duration: 60
  }
  
  activityForm.value = {
    session_id: '',
    name_ar: '',
    name_en: '',
    activity_type: 'text',
    instructions_ar: '',
    instructions_en: '',
    is_mandatory: true
  }
}

// Image Handling
function selectImage(event: Event) {
  const input = event.target as HTMLInputElement
  if (input.files && input.files[0]) {
    const file = input.files[0]
    
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
    if (!validTypes.includes(file.type)) {
      showError('❌ نوع الملف غير مدعوم. الرجاء استخدام صورة (JPEG, PNG, JPG, GIF, WebP)')
      input.value = ''
      return
    }
    
    if (file.size > 5 * 1024 * 1024) {
      showError('❌ حجم الصورة يجب أن يكون أقل من 5MB')
      input.value = ''
      return
    }
    
    const reader = new FileReader()
    reader.onload = (e) => {
      const base64String = e.target?.result as string
      
      programForm.value.image = file
      programImagePreview.value = base64String
      
      console.log('🖼️ Program image loaded:', {
        name: file.name,
        size: file.size,
        type: file.type
      })
    }
    
    reader.readAsDataURL(file)
  }
}

function removeImage() {
  programForm.value.image = null
  programImagePreview.value = null
}

// Toast Notifications
function showSuccess(message: string) {
  const toast = document.createElement('div')
  toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2'
  toast.innerHTML = `
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span>${message}</span>
  `
  document.body.appendChild(toast)
  
  setTimeout(() => {
    toast.remove()
  }, 3000)
  
  console.log('✅ Success:', message)
}

function showError(message: string) {
  const toast = document.createElement('div')
  toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2'
  toast.innerHTML = `
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
    <span>${message}</span>
  `
  document.body.appendChild(toast)
  
  setTimeout(() => {
    toast.remove()
  }, 5000)
  
  console.error('❌ Error:', message)
}

// دالة لتحديث المقاييس يدوياً
async function refreshScales() {
  console.log('🔄 Manually refreshing scales...')
  await loadPsychologicalScales()
}

// Computed Properties
const filteredPrograms = computed(() => {
  return programs.value.filter(program => {
    let matches = true
    
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      matches = matches && (
        program.name_ar.toLowerCase().includes(query) ||
        program.name_en.toLowerCase().includes(query) ||
        (program.description_ar && program.description_ar.toLowerCase().includes(query)) ||
        (program.description_en && program.description_en.toLowerCase().includes(query))
      )
    }
    
    if (statusFilter.value) {
      matches = matches && (program.status === statusFilter.value)
    }
    
    return matches
  })
})

const filteredSessions = computed(() => {
  return sessions.value.filter(session => {
    let matches = true
    
    if (selectedProgram.value) {
      matches = matches && (session.program_id === selectedProgram.value.id)
    }
    
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      matches = matches && (
        session.title_ar.toLowerCase().includes(query) ||
        session.title_en.toLowerCase().includes(query)
      )
    }
    
    return matches
  })
})

const filteredActivities = computed(() => {
  return activities.value.filter(activity => {
    let matches = true
    
    if (selectedSession.value) {
      matches = matches && (activity.session_id === selectedSession.value.id)
    }
    
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      matches = matches && (
        activity.name_ar.toLowerCase().includes(query) ||
        activity.name_en.toLowerCase().includes(query)
      )
    }
    
    return matches
  })
})

// Lifecycle
onMounted(async () => {
  console.log('🚀 Component mounted, loading data...')
  console.log('🌐 API Base URL:', API_BASE)
  
  // تحميل البرامج والمقاييس في نفس الوقت
  await Promise.all([
    loadPrograms(),
    loadPsychologicalScales()
  ])
})

// Watchers
watch([searchQuery, statusFilter], () => {
  switch (activeTab.value) {
    case 'programs':
      loadPrograms()
      break
    case 'sessions':
      if (selectedProgram.value) {
        loadProgramSessions(selectedProgram.value.id)
      }
      break
    case 'activities':
      if (selectedSession.value) {
        loadSessionActivities(selectedSession.value.id)
      }
      break
  }
})

watch(activeTab, (newTab) => {
  if (newTab === 'activities' && selectedSession.value) {
    loadSessionActivities(selectedSession.value.id)
  }
  else if (newTab === 'sessions' && selectedProgram.value) {
    loadProgramSessions(selectedProgram.value.id)
  }
})

watch(programsCurrentPage, () => {
  loadPrograms()
})

watch(sessionsCurrentPage, () => {
  if (selectedProgram.value) {
    loadProgramSessions(selectedProgram.value.id)
  }
})

watch(activitiesCurrentPage, () => {
  if (selectedSession.value) {
    loadSessionActivities(selectedSession.value.id)
  }
})

// Export for template
defineExpose({
  activeTab,
  searchQuery,
  statusFilter,
  selectedProgram,
  selectedSession,
  programs,
  sessions,
  activities,
  filteredPrograms,
  filteredSessions,
  filteredActivities,
  openProgramModal,
  openSessionModal,
  openActivityModal,
  viewProgramSessions,
  viewSessionActivities,
  saveProgram,
  saveSession,
  saveActivity,
  deleteItem,
  formatDate,
  formatDateShort,
  getProgramStatusText,
  getActivityTypeText,
  getMandatoryText,
  getScaleName,
  selectImage,
  removeImage,
  getSafeImageUrl,
  toggleProgramStatus,
  refreshScales,
  loadPsychologicalScales
})
</script>

<template>
  <div class="space-y-4 p-3 md:p-6 bg-primary">
    <!-- Loading State -->
    <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="rounded-xl bg-primary p-6 flex flex-col items-center">
        <div class="h-12 w-12 animate-spin rounded-full border-b-2 border-brand-500"></div>
        <p class="mt-4 text-primary">جاري التحميل...</p>
      </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-xl font-bold text-primary md:text-2xl">
          إدارة البرامج العلاجية
        </h1>
        <p class="mt-1 text-xs text-tertiary md:text-sm">
          أضف وأدر البرامج والجلسات والأنشطة
        </p>
      </div>
      
      <div class="flex flex-wrap justify-end gap-2 md:justify-start">
        <button
          @click="openProgramModal()"
          class="btn btn-primary flex items-center gap-2 rounded-lg px-4 py-2 text-sm shadow-sm transition-all duration-200 hover:opacity-90 md:text-base"
        >
          <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          إضافة برنامج جديد
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="rounded-xl border border-primary bg-secondary p-3 md:p-4">
      <div class="flex flex-col gap-3 md:flex-row md:items-center">
        <!-- Search Input -->
        <div class="flex-1">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="ابحث عن برنامج، جلسة، أو نشاط..."
              class="input w-full py-2 pl-10 pr-4 text-primary placeholder-tertiary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
            />
            <svg class="absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 transform text-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap gap-2">
          <select v-model="statusFilter" class="input px-3 py-2 text-sm text-primary focus:border-brand-500 focus:outline-none">
            <option value="">جميع الحالات</option>
            <option value="active">نشط</option>
            <option value="inactive">غير نشط</option>
            <option value="draft">مسودة</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="overflow-x-auto rounded-lg border-b border-primary bg-secondary">
      <nav class="flex min-w-max md:min-w-0">
        <button
          @click="activeTab = 'programs'; selectedProgram = null; selectedSession = null"
          :class="[
            'relative whitespace-nowrap border-b-2 px-2 py-3 text-xs font-medium transition-all duration-300 md:px-4 md:text-sm',
            activeTab === 'programs'
              ? 'border-brand-500 bg-gradient-to-t from-brand-500/5 to-transparent text-brand-500'
              : 'border-transparent text-tertiary hover:bg-secondary/50 hover:text-primary'
          ]"
        >
          البرامج
          <span v-if="filteredPrograms.length" :class="[
            'absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full text-xs',
            activeTab === 'programs' ? 'bg-brand-500 text-white' : 'bg-tertiary text-secondary'
          ]">
            {{ filteredPrograms.length }}
          </span>
        </button>
        
        <button
          @click="activeTab = 'sessions'"
          :disabled="!selectedProgram"
          :class="[
            'relative whitespace-nowrap border-b-2 px-2 py-3 text-xs font-medium transition-all duration-300 md:px-4 md:text-sm',
            activeTab === 'sessions' && selectedProgram
              ? 'border-accent-500 bg-gradient-to-t from-accent-500/5 to-transparent text-accent-500'
              : 'border-transparent text-tertiary hover:bg-secondary/50 hover:text-primary',
            !selectedProgram ? 'cursor-not-allowed opacity-50' : ''
          ]"
        >
          الجلسات
          <span v-if="filteredSessions.length" :class="[
            'absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full text-xs',
            activeTab === 'sessions' ? 'bg-accent-500 text-white' : 'bg-tertiary text-secondary'
          ]">
            {{ filteredSessions.length }}
          </span>
        </button>
        
        <button
          @click="activeTab = 'activities'"
          :disabled="!selectedSession"
          :class="[
            'relative whitespace-nowrap border-b-2 px-2 py-3 text-xs font-medium transition-all duration-300 md:px-4 md:text-sm',
            activeTab === 'activities' && selectedSession
              ? 'border-brand-500 bg-gradient-to-r from-primary-green to-primary-pink bg-clip-text text-transparent'
              : 'border-transparent text-tertiary hover:bg-secondary/50 hover:text-primary',
            !selectedSession ? 'cursor-not-allowed opacity-50' : ''
          ]"
        >
          الأنشطة
          <span v-if="filteredActivities.length" :class="[
            'absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full text-xs',
            activeTab === 'activities' ? 'bg-gradient-to-r from-primary-green to-primary-pink text-white' : 'bg-tertiary text-secondary'
          ]">
            {{ filteredActivities.length }}
          </span>
        </button>
      </nav>
    </div>

    <!-- Programs Tab -->
    <div v-if="activeTab === 'programs'" class="animate-fade-in">
      <!-- حالة التحميل -->
      <div v-if="isLoadingPrograms" class="py-12 text-center">
        <div class="mx-auto h-12 w-12 animate-spin rounded-full border-b-2 border-brand-500"></div>
        <p class="mt-4 text-tertiary">جاري تحميل البرامج...</p>
      </div>
      
      <div v-else>
        <!-- Desktop View - Table -->
        <!-- <div class="hidden md:block"> -->
        <div class="md:block">
          <div class="overflow-x-auto rounded-xl border border-primary shadow-sm">
            <table class="min-w-full divide-y divide-primary">
              <thead class="bg-secondary">
                <tr>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">البرنامج</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الفئة المستهدفة</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">المدة</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">المقياس النفسي</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الحالة</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الإجراءات</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-primary bg-primary">
                <tr 
                  v-for="program in filteredPrograms" 
                  :key="program.id"
                  class="transition-colors duration-200 hover:bg-secondary/50"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="flex-shrink-0">
                        <div class="h-10 w-10 overflow-hidden rounded-lg">
                          <img 
                            :src="getSafeImageUrl(program.image_url)" 
                            :alt="program.name_ar"
                            class="h-full w-full object-cover"
                            @error="(e) => { const target = e.target as HTMLImageElement | null; if (target) target.src = getSafeImageUrl() }"
                          />
                        </div>
                      </div>
                      <div class="text-right">
                        <div class="text-sm font-semibold text-primary">
                          {{ program.name_ar }}
                        </div>
                        <div class="text-xs text-tertiary">
                          {{ program.name_en }}
                        </div>
                        <div class="mt-1 text-xs text-tertiary line-clamp-2">
                          {{ program.description_ar }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-primary">
                      {{ program.target_category_ar || 'غير محدد' }}
                    </div>
                    <div class="text-xs text-tertiary">
                      {{ program.target_category_en }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-primary">
                      {{ program.duration }} يوم
                    </div>
                    <div class="text-xs text-tertiary">
                      {{ program.sessions_count || 0 }} جلسة
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div v-if="program.scale_id" class="flex flex-col items-end">
                      <span class="inline-flex items-center gap-1 text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ getScaleName(program) }}
                      </span>
                    </div>
                    <div v-else class="text-xs text-tertiary">
                      بدون مقياس
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span :class="[
                      'badge rounded-full px-3 py-1 text-xs',
                      program.status === 'active' 
                        ? 'badge-brand' 
                        : program.status === 'inactive'
                        ? 'badge-neutral'
                        : 'badge-neutral'
                    ]">
                      {{ getProgramStatusText(program) }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <button
                        @click="viewProgramSessions(program)"
                        class="btn-ghost rounded-lg p-2 text-sm text-brand-500 transition-colors hover:bg-brand-500/10"
                        title="عرض الجلسات"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                      </button>
                      <button
                        @click="openProgramModal(program)"
                        class="btn-ghost rounded-lg p-2 text-sm text-brand-500 transition-colors hover:bg-brand-500/10"
                        title="تعديل"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button
                        @click="toggleProgramStatus(program)"
                        class="btn-ghost rounded-lg p-2 text-sm text-amber-500 transition-colors hover:bg-amber-500/10"
                        :title="program.status === 'active' ? 'تعطيل' : 'تفعيل'"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                      </button>
                      <button
                        @click="confirmDelete('program', program)"
                        class="btn-ghost rounded-lg p-2 text-sm text-red-500 transition-colors hover:bg-red-500/10"
                        title="حذف"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
                
                <!-- Empty State -->
                <tr v-if="filteredPrograms.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center gap-3">
                      <svg class="h-12 w-12 text-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <p class="text-tertiary">لا توجد برامج مضافة</p>
                      <button
                        @click="openProgramModal()"
                        class="btn btn-primary mt-2 rounded-lg px-4 py-2 text-sm transition-all duration-200 hover:opacity-90"
                      >
                        إضافة أول برنامج
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="filteredPrograms.length > 0" class="mt-6 flex flex-col justify-between gap-4 md:flex-row md:items-center">
          <div class="text-sm text-tertiary">
            عرض {{ (programsCurrentPage - 1) * programsPerPage + 1 }}-{{ Math.min(programsCurrentPage * programsPerPage, programsTotal) }} من {{ programsTotal }}
          </div>
          <div class="flex items-center gap-2">
            <button
              @click="programsCurrentPage--"
              :disabled="programsCurrentPage === 1"
              :class="[
                'rounded-lg px-3 py-1 text-sm transition-all duration-200',
                programsCurrentPage === 1 
                  ? 'cursor-not-allowed border border-primary bg-secondary text-tertiary' 
                  : 'border border-primary bg-secondary text-primary hover:border-brand-500 hover:bg-tertiary'
              ]"
            >
              ← التالي
            </button>
            
            <button
              @click="programsCurrentPage++"
              :disabled="programsCurrentPage * programsPerPage >= programsTotal"
              :class="[
                'rounded-lg px-3 py-1 text-sm transition-all duration-200',
                programsCurrentPage * programsPerPage >= programsTotal
                  ? 'cursor-not-allowed border border-primary bg-secondary text-tertiary' 
                  : 'border border-primary bg-secondary text-primary hover:border-brand-500 hover:bg-tertiary'
              ]"
            >
              السابق →
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Sessions Tab -->
    <div v-if="activeTab === 'sessions'" class="animate-fade-in">
      <!-- حالة التحميل -->
      <div v-if="isLoadingSessions" class="py-12 text-center">
        <div class="mx-auto h-12 w-12 animate-spin rounded-full border-b-2 border-accent-500"></div>
        <p class="mt-4 text-tertiary">جاري تحميل الجلسات...</p>
      </div>
      
      <div v-else>
        <!-- Selected Program Info -->
        <div v-if="selectedProgram" class="mb-4 rounded-xl border border-brand-500/20 bg-gradient-to-r from-brand-500/10 to-accent-500/10 p-3 md:mb-6 md:p-4">
          <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
              <div class="mb-2 flex items-center gap-2">
                <button
                  @click="selectedProgram = null; selectedSession = null"
                  class="p-1 text-tertiary transition-colors hover:text-primary"
                >
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                  </svg>
                </button>
                <h3 class="text-base font-semibold text-primary md:text-lg">
                  جلسات برنامج: {{ selectedProgram.name_ar }}
                </h3>
              </div>
              <p class="text-xs text-tertiary md:text-sm">
                {{ selectedProgram.description_ar }}
              </p>
              <div class="mt-2 flex items-center gap-2 text-xs text-tertiary">
                <span>المدة: {{ selectedProgram.duration }} يوم</span>
                <span>•</span>
                <span v-if="selectedProgram.scale_id" class="text-blue-600">
                  {{ getScaleName(selectedProgram) }}
                </span>
              </div>
            </div>
            <div class="mt-2 flex gap-2 md:mt-0">
              <button
                @click="openSessionModal()"
                class="btn btn-accent flex items-center gap-2 rounded-lg px-4 py-2 text-xs shadow-sm transition-all duration-200 hover:opacity-90 md:text-sm"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                إضافة جلسة
              </button>
            </div>
          </div>
        </div>

        <!-- Desktop View - Table -->
        <!-- <div class="hidden md:block"> -->
        <div class="md:block">
          <div class="overflow-x-auto rounded-xl border border-primary shadow-sm">
            <table class="min-w-full divide-y divide-primary">
              <thead class="bg-secondary">
                <tr>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الجلسة</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الترتيب</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الهدف</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الأنشطة</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">المدة</th>
                  <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الإجراءات</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-primary bg-primary">
                <tr 
                  v-for="session in filteredSessions" 
                  :key="session.id"
                  class="transition-colors duration-200 hover:bg-secondary/50"
                >
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="flex-shrink-0">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent-500/10 shadow-inner">
                          <span class="text-sm font-bold text-accent-500">
                            {{ session.session_order }}
                          </span>
                        </div>
                      </div>
                      <div class="text-right">
                        <div class="text-sm font-semibold text-primary">
                          {{ session.title_ar }}
                        </div>
                        <div class="text-xs text-tertiary">
                          {{ session.title_en }}
                        </div>
                        <div class="text-xs text-tertiary">
                          {{ formatDateShort(session.created_at) }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <span class="badge badge-neutral rounded-full px-3 py-1 text-xs">
                      الجلسة {{ session.session_order }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="max-w-xs line-clamp-2 text-sm text-primary">
                      {{ session.goal_ar || 'لا يوجد هدف' }}
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex flex-col items-end">
                      <span class="badge badge-neutral mb-1 rounded-full px-3 py-1 text-xs">
                        {{ session.activities_count || 0 }} نشاط
                      </span>
                      <button 
                        v-if="(session.activities_count || 0) > 0"
                        @click="viewSessionActivities(session)"
                        class="text-xs text-accent-500 transition-colors hover:text-accent-700"
                      >
                        عرض الأنشطة
                      </button>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-primary">
                      {{ session.duration || '60' }} دقيقة
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                      <button
                        @click="viewSessionActivities(session)"
                        class="btn-ghost rounded-lg p-2 text-sm text-accent-500 transition-colors hover:bg-accent-500/10"
                        title="عرض الأنشطة"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button
                        @click="openSessionModal(session)"
                        class="btn-ghost rounded-lg p-2 text-sm text-brand-500 transition-colors hover:bg-brand-500/10"
                        title="تعديل"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </button>
                      <button
                        @click="confirmDelete('session', session)"
                        class="btn-ghost rounded-lg p-2 text-sm text-red-500 transition-colors hover:bg-red-500/10"
                        title="حذف"
                      >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
                
                <!-- Empty State -->
                <tr v-if="filteredSessions.length === 0">
                  <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center gap-3">
                      <svg class="h-12 w-12 text-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <p class="text-tertiary">لا توجد جلسات مضافة</p>
                      <button
                        @click="openSessionModal()"
                        class="btn btn-accent mt-2 rounded-lg px-4 py-2 text-sm transition-all duration-200 hover:opacity-90"
                      >
                        إضافة أول جلسة
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Activities Tab -->
    <div v-if="activeTab === 'activities'" class="animate-fade-in">
      <!-- حالة التحميل -->
      <div v-if="isLoadingActivities" class="py-12 text-center">
        <div class="mx-auto h-12 w-12 animate-spin rounded-full border-b-2 border-brand-500"></div>
        <p class="mt-4 text-tertiary">جاري تحميل الأنشطة...</p>
      </div>
      
      <div v-else>
        <!-- Selected Session Info -->
        <div v-if="selectedSession" class="mb-4 rounded-xl border border-accent-500/20 bg-gradient-to-r from-brand-500/10 to-accent-500/10 p-3 md:mb-6 md:p-4">
          <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
              <div class="mb-2 flex items-center gap-2">
                <button
                  @click="selectedSession = null"
                  class="p-1 text-tertiary transition-colors hover:text-primary"
                >
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                  </svg>
                </button>
                <h3 class="text-base font-semibold text-primary md:text-lg">
                  أنشطة الجلسة: {{ selectedSession.title_ar }}
                </h3>
              </div>
              <p class="mb-2 text-xs text-tertiary md:text-sm">
                {{ selectedSession.goal_ar }}
              </p>
              <div class="flex items-center gap-3 text-xs text-tertiary">
                <span>الجلسة {{ selectedSession.session_order }}</span>
                <span>•</span>
                <span>{{ selectedSession.duration || '60' }} دقيقة</span>
              </div>
            </div>
            <div class="mt-2 flex gap-2 md:mt-0">
              <button
                @click="openActivityModal()"
                class="btn flex items-center gap-2 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 px-4 py-2 text-xs text-white shadow-sm transition-all duration-200 hover:opacity-90 md:text-sm"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                إضافة نشاط
              </button>
            </div>
          </div>
        </div>

        <!-- عرض الأنشطة -->
        <div v-if="filteredActivities.length > 0">
          <!-- Desktop View - Table -->
          <div class="md:block">
            <div class="overflow-x-auto rounded-xl border border-primary shadow-sm">
              <table class="min-w-full divide-y divide-primary">
                <thead class="bg-secondary">
                  <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">النشاط</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">التعليمات</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">النوع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الإلزامية</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">تاريخ الإنشاء</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-tertiary">الإجراءات</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-primary bg-primary">
                  <tr 
                    v-for="activity in filteredActivities" 
                    :key="activity.id"
                    class="transition-colors duration-200 hover:bg-secondary/50"
                  >
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-brand-500/20 to-accent-500/20 shadow-inner">
                            <span class="text-sm font-bold text-brand-500">
                              {{ getActivityTypeText(activity.activity_type).charAt(0) }}
                            </span>
                          </div>
                        </div>
                        <div class="text-right">
                          <div class="text-sm font-semibold text-primary">
                            {{ activity.name_ar }}
                          </div>
                          <div class="text-xs text-tertiary">
                            {{ activity.name_en }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div v-if="activity.instructions_ar" class="max-w-md line-clamp-2 text-sm text-primary">
                        {{ activity.instructions_ar }}
                      </div>
                      <div v-else class="max-w-md line-clamp-2 text-sm text-tertiary">
                        لا توجد تعليمات
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="badge rounded-full px-3 py-1 text-xs" :class="{
                        'bg-blue-100 text-blue-800': activity.activity_type === 'text',
                        'bg-purple-100 text-purple-800': activity.activity_type === 'audio',
                        'bg-pink-100 text-pink-800': activity.activity_type === 'video',
                        'bg-gray-100 text-gray-800': activity.activity_type === 'file',
                        'bg-indigo-100 text-indigo-800': activity.activity_type === 'quiz'
                      }">
                        {{ getActivityTypeText(activity.activity_type) }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <span :class="[
                        'badge rounded-full px-3 py-1 text-xs',
                        activity.is_mandatory ? 'badge-success' : 'badge-neutral'
                      ]">
                        {{ getMandatoryText(activity.is_mandatory) }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-primary">
                        {{ formatDateShort(activity.created_at) }}
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-2">
                        <button
                          @click="openActivityModal(activity)"
                          class="btn-ghost rounded-lg p-2 text-sm text-accent-500 transition-colors hover:bg-accent-500/10"
                          title="تعديل"
                        >
                          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                          </svg>
                        </button>
                        <button
                          @click="confirmDelete('activity', activity)"
                          class="btn-ghost rounded-lg p-2 text-sm text-red-500 transition-colors hover:bg-red-500/10"
                          title="حذف"
                        >
                          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredActivities.length === 0" class="card rounded-xl border border-primary bg-secondary py-12 text-center">
          <svg class="mx-auto h-16 w-16 text-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          <p class="mt-3 text-tertiary">
            {{ selectedSession ? 'لا توجد أنشطة في هذه الجلسة' : 'لا توجد أنشطة مضافة' }}
          </p>
          <button
            v-if="selectedSession"
            @click="openActivityModal()"
            class="btn mt-4 rounded-lg bg-gradient-to-r from-primary-green to-primary-pink px-4 py-2 text-sm text-white transition-all duration-200 hover:opacity-90"
          >
            إضافة أول نشاط
          </button>
        </div>
      </div>
    </div>

    <!-- Program Modal -->
    <div v-if="showProgramModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="fixed inset-0 bg-black/50 transition-opacity duration-300" @click="closeModals"></div>
        
        <div class="relative w-full max-w-2xl scale-100 transform rounded-2xl bg-primary shadow-2xl transition-all duration-300">
          <div class="border-b border-primary bg-gradient-to-r from-brand-500/5 to-accent-500/5 px-6 py-4">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-primary">
                {{ editingProgram ? 'تعديل البرنامج' : 'إضافة برنامج جديد' }}
              </h3>
              <button @click="closeModals" class="text-tertiary transition-colors hover:text-primary">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
          
          <div class="max-h-[60vh] overflow-y-auto px-6 py-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <!-- صورة البرنامج -->
              <div class="col-span-full">
                <label class="block text-sm font-medium text-primary mb-2">
                  صورة البرنامج (اختياري)
                </label>
                
                <div class="space-y-4">
                  <!-- Image Preview -->
                  <div v-if="programImagePreview" class="flex flex-col items-center gap-3">
                    <div class="relative h-40 w-40 overflow-hidden rounded-xl border border-primary shadow-md">
                      <img 
                        :src="programImagePreview" 
                        alt="معاينة صورة البرنامج"
                        class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
                      />
                    </div>
                    <div class="flex gap-2">
                      <label class="cursor-pointer rounded-lg border border-brand-500 bg-brand-500/10 px-3 py-1 text-xs text-brand-500 transition-colors hover:bg-brand-500/20">
                        تغيير الصورة
                        <input
                          type="file"
                          accept="image/*"
                          @change="selectImage"
                          class="hidden"
                        />
                      </label>
                      <button
                        @click="removeImage"
                        type="button"
                        class="rounded-lg border border-red-500 bg-red-500/10 px-3 py-1 text-xs text-red-500 transition-colors hover:bg-red-500/20"
                      >
                        حذف الصورة
                      </button>
                    </div>
                  </div>
                  
                  <!-- Image Upload Button (when no image) -->
                  <div v-else class="flex flex-col items-center gap-3">
                    <div class="flex h-40 w-40 items-center justify-center rounded-xl border-2 border-dashed border-tertiary bg-tertiary/10">
                      <div class="text-center">
                        <svg class="mx-auto h-10 w-10 text-tertiary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-xs text-tertiary">اضغط لاختيار صورة</p>
                      </div>
                    </div>
                    <label class="cursor-pointer rounded-lg border border-brand-500 bg-brand-500/10 px-3 py-1 text-xs text-brand-500 transition-colors hover:bg-brand-500/20">
                      اختر صورة
                      <input
                        type="file"
                        accept="image/*"
                        @change="selectImage"
                        class="hidden"
                      />
                    </label>
                    <p class="text-[11px] text-tertiary">
                      الصور المدعومة: JPG, PNG, GIF, WebP (الحد الأقصى: 5MB)
                    </p>
                  </div>
                </div>
              </div>

              <!-- معلومات البرنامج -->
              <div class="col-span-full">
                <label class="block text-sm font-medium text-primary mb-2">
                  معلومات البرنامج الأساسية
                </label>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- اسم البرنامج بالعربية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      اسم البرنامج (عربي) *
                    </label>
                    <input
                      v-model="programForm.name_ar"
                      type="text"
                      required
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل اسم البرنامج بالعربية"
                    />
                  </div>
                  
                  <!-- اسم البرنامج بالإنجليزية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      اسم البرنامج (إنجليزي) *
                    </label>
                    <input
                      v-model="programForm.name_en"
                      type="text"
                      required
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل اسم البرنامج بالإنجليزية"
                    />
                  </div>
                </div>
              </div>

              <!-- الوصف -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- الوصف بالعربية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      الوصف (عربي)
                    </label>
                    <textarea
                      v-model="programForm.description_ar"
                      rows="3"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل وصف البرنامج بالعربية"
                    ></textarea>
                  </div>
                  
                  <!-- الوصف بالإنجليزية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      الوصف (إنجليزي)
                    </label>
                    <textarea
                      v-model="programForm.description_en"
                      rows="3"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل وصف البرنامج بالإنجليزية"
                    ></textarea>
                  </div>
                </div>
              </div>

              <!-- الفئة المستهدفة -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- الفئة المستهدفة بالعربية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      الفئة المستهدفة (عربي)
                    </label>
                    <input
                      v-model="programForm.target_category_ar"
                      type="text"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل الفئة المستهدفة بالعربية"
                    />
                  </div>
                  
                  <!-- الفئة المستهدفة بالإنجليزية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      الفئة المستهدفة (إنجليزي)
                    </label>
                    <input
                      v-model="programForm.target_category_en"
                      type="text"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل الفئة المستهدفة بالإنجليزية"
                    />
                  </div>
                </div>
              </div>

              <!-- المقياس النفسي والمدة -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- المقياس النفسي -->
                  <div>
                    <div class="flex items-center justify-between mb-1">
                      <label class="text-xs font-medium text-primary">
                        المقياس النفسي (اختياري)
                      </label>
                      <span class="text-xs text-tertiary">
                        {{ psychologicalScales.length }} مقياس
                      </span>
                    </div>
                    
                    <!-- حالة التحميل -->
                    <div v-if="psychologicalScales.length === 0" class="mb-2">
                      <div class="flex items-center gap-2 text-xs text-amber-600 bg-amber-50 px-3 py-2 rounded-lg">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>جاري تحميل المقاييس...</span>
                        <button 
                          @click="refreshScales"
                          class="text-xs text-brand-500 hover:underline"
                        >
                          إعادة المحاولة
                        </button>
                      </div>
                    </div>
                    
                    <select
                      v-model="programForm.scale_id"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      :disabled="psychologicalScales.length === 0"
                    >
                      <option value="">اختر مقياس نفسي</option>
                      <option 
                        v-for="scale in psychologicalScales" 
                        :key="scale.id" 
                        :value="scale.id"
                      >
                        {{ scale.name_ar }} ({{ scale.name_en }})
                      </option>
                    </select>
                    
                    <!-- اختبار: عرض المقاييس المحملة -->
                    <div v-if="false" class="mt-2 p-2 bg-gray-100 rounded text-xs">
                      <div>المقاييس المحملة: {{ psychologicalScales.length }}</div>
                      <div v-for="scale in psychologicalScales" :key="scale.id" class="text-xs">
                        {{ scale.id }} - {{ scale.name_ar }}
                      </div>
                    </div>
                  </div>
                  
                  <!-- المدة -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      مدة البرنامج (بالأيام) *
                    </label>
                    <input
                      v-model.number="programForm.duration"
                      type="number"
                      min="1"
                      max="365"
                      required
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل المدة بالأيام"
                    />
                  </div>
                </div>
              </div>

              <!-- إعدادات التوقيت -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      مدة البرنامج القصوى (بالأيام)
                    </label>
                    <input
                      v-model.number="programForm.max_duration_days"
                      type="number"
                      min="1"
                      max="365"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    />
                  </div>
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      مدة الجلسة الافتراضية (بالدقائق)
                    </label>
                    <input
                      v-model.number="programForm.session_duration_minutes"
                      type="number"
                      min="1"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    />
                  </div>
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      الفاصل بين الجلسات (بالساعات)
                    </label>
                    <input
                      v-model.number="programForm.session_gap_hours"
                      type="number"
                      min="0"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    />
                  </div>
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      الفاصل بين الأنشطة (بالساعات)
                    </label>
                    <input
                      v-model.number="programForm.activity_gap_hours"
                      type="number"
                      min="0"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    />
                  </div>
                </div>
              </div>

              <!-- حالة البرنامج -->
              <div class="col-span-full">
                <label class="mb-1 block text-xs font-medium text-primary">
                  حالة البرنامج
                </label>
                <div class="flex gap-3">
                  <label class="flex items-center gap-2">
                    <input
                      v-model="programForm.status"
                      type="radio"
                      value="draft"
                      class="h-4 w-4 border-primary text-brand-500 focus:ring-brand-500"
                    />
                    <span class="text-sm text-primary">مسودة</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      v-model="programForm.status"
                      type="radio"
                      value="active"
                      class="h-4 w-4 border-primary text-brand-500 focus:ring-brand-500"
                    />
                    <span class="text-sm text-primary">نشط</span>
                  </label>
                  <label class="flex items-center gap-2">
                    <input
                      v-model="programForm.status"
                      type="radio"
                      value="inactive"
                      class="h-4 w-4 border-primary text-brand-500 focus:ring-brand-500"
                    />
                    <span class="text-sm text-primary">غير نشط</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          
          <div class="border-t border-primary bg-secondary px-6 py-4">
            <div class="flex justify-between items-center">
              <button
                @click="refreshScales"
                type="button"
                class="text-xs text-tertiary hover:text-primary flex items-center gap-1"
                title="تحديث قائمة المقاييس"
              >
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                تحديث المقاييس
              </button>
              
              <div class="flex gap-3">
                <button
                  @click="closeModals"
                  type="button"
                  class="rounded-lg border border-primary bg-primary px-4 py-2 text-sm font-medium text-primary transition-colors hover:bg-tertiary"
                >
                  إلغاء
                </button>
                <button
                  @click="saveProgram"
                  :disabled="isLoading"
                  type="button"
                  class="btn btn-primary flex items-center gap-2 rounded-lg px-4 py-2 text-sm transition-all duration-200 hover:opacity-90"
                >
                  <svg v-if="isLoading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{{ isLoading ? 'جاري الحفظ...' : (editingProgram ? 'تحديث البرنامج' : 'إضافة البرنامج') }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Session Modal -->
    <div v-if="showSessionModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="fixed inset-0 bg-black/50 transition-opacity duration-300" @click="closeModals"></div>
        
        <div class="relative w-full max-w-2xl scale-100 transform rounded-2xl bg-primary shadow-2xl transition-all duration-300">
          <div class="border-b border-primary bg-gradient-to-r from-accent-500/5 to-brand-500/5 px-6 py-4">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-primary">
                {{ editingSession ? 'تعديل الجلسة' : 'إضافة جلسة جديدة' }}
              </h3>
              <button @click="closeModals" class="text-tertiary transition-colors hover:text-primary">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
          
          <div class="max-h-[60vh] overflow-y-auto px-6 py-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <!-- عنوان الجلسة -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- عنوان الجلسة بالعربية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      عنوان الجلسة (عربي) *
                    </label>
                    <input
                      v-model="sessionForm.title_ar"
                      type="text"
                      required
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل عنوان الجلسة بالعربية"
                    />
                  </div>
                  
                  <!-- عنوان الجلسة بالإنجليزية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      عنوان الجلسة (إنجليزي) *
                    </label>
                    <input
                      v-model="sessionForm.title_en"
                      type="text"
                      required
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل عنوان الجلسة بالإنجليزية"
                    />
                  </div>
                </div>
              </div>
              
              <!-- ترتيب الجلسة والمدة -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- ترتيب الجلسة -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      ترتيب الجلسة
                    </label>
                    <input
                      v-model.number="sessionForm.session_order"
                      type="number"
                      min="1"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    />
                  </div>
                  
                  <!-- مدة الجلسة -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      مدة الجلسة (بالدقائق) *
                    </label>
                    <input
                      v-model.number="sessionForm.duration"
                      type="number"
                      min="1"
                      max="480"
                      required
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل المدة بالدقائق"
                    />
                  </div>
                </div>
              </div>
              
              <!-- هدف الجلسة -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- هدف الجلسة بالعربية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      هدف الجلسة (عربي)
                    </label>
                    <textarea
                      v-model="sessionForm.goal_ar"
                      rows="3"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل هدف الجلسة بالعربية"
                    ></textarea>
                  </div>
                  
                  <!-- هدف الجلسة بالإنجليزية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      هدف الجلسة (إنجليزي)
                    </label>
                    <textarea
                      v-model="sessionForm.goal_en"
                      rows="3"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل هدف الجلسة بالإنجليزية"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="border-t border-primary bg-secondary px-6 py-4">
            <div class="flex justify-end gap-3">
              <button
                @click="closeModals"
                type="button"
                class="rounded-lg border border-primary bg-primary px-4 py-2 text-sm font-medium text-primary transition-colors hover:bg-tertiary"
              >
                إلغاء
              </button>
              <button
                @click="saveSession"
                :disabled="isLoading"
                type="button"
                class="btn btn-accent flex items-center gap-2 rounded-lg px-4 py-2 text-sm transition-all duration-200 hover:opacity-90"
              >
                <svg v-if="isLoading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isLoading ? 'جاري الحفظ...' : (editingSession ? 'تحديث الجلسة' : 'إضافة الجلسة') }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity Modal -->
    <div v-if="showActivityModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="fixed inset-0 bg-black/50 transition-opacity duration-300" @click="closeModals"></div>
        
        <div class="relative w-full max-w-2xl scale-100 transform rounded-2xl bg-primary shadow-2xl transition-all duration-300">
          <div class="border-b border-primary bg-gradient-to-r from-brand-500/5 to-accent-500/5 px-6 py-4">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-primary">
                {{ editingActivity ? 'تعديل النشاط' : 'إضافة نشاط جديد' }}
              </h3>
              <button @click="closeModals" class="text-tertiary transition-colors hover:text-primary">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
          
          <div class="max-h-[60vh] overflow-y-auto px-6 py-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <!-- اسم النشاط -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- اسم النشاط بالعربية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      اسم النشاط (عربي) *
                    </label>
                    <input
                      v-model="activityForm.name_ar"
                      type="text"
                      required
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل اسم النشاط بالعربية"
                    />
                  </div>
                  
                  <!-- اسم النشاط بالإنجليزية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      اسم النشاط (إنجليزي) *
                    </label>
                    <input
                      v-model="activityForm.name_en"
                      type="text"
                      required
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل اسم النشاط بالإنجليزية"
                    />
                  </div>
                </div>
              </div>
              
              <!-- نوع النشاط والإلزامية -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- نوع النشاط -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      نوع النشاط
                    </label>
                    <select
                      v-model="activityForm.activity_type"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    >
                      <option value="text">نص</option>
                      <option value="audio">صوتي</option>
                      <option value="video">فيديو</option>
                      <option value="file">ملف</option>
                      <option value="form">نموذج</option>
                      <option value="exercise">تمرين</option>
                      <option value="reflection_questions">أسئلة انعكاسية</option>
                      <option value="quiz">اختبار</option>
                    </select>
                  </div>
                  
                  <!-- إلزامية النشاط -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      إلزامية النشاط
                    </label>
                    <div class="mt-2">
                      <label class="flex items-center gap-2">
                        <input
                          v-model="activityForm.is_mandatory"
                          :value="true"
                          type="radio"
                          class="h-4 w-4 border-primary text-brand-500 focus:ring-brand-500"
                        />
                        <span class="text-sm text-primary">إجباري</span>
                      </label>
                      <label class="flex items-center gap-2">
                        <input
                          v-model="activityForm.is_mandatory"
                          :value="false"
                          type="radio"
                          class="h-4 w-4 border-primary text-brand-500 focus:ring-brand-500"
                        />
                        <span class="text-sm text-primary">اختياري</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- تعليمات النشاط -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <!-- تعليمات النشاط بالعربية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      التعليمات (عربي)
                    </label>
                    <textarea
                      v-model="activityForm.instructions_ar"
                      rows="3"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل التعليمات بالعربية"
                    ></textarea>
                  </div>
                  
                  <!-- تعليمات النشاط بالإنجليزية -->
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      التعليمات (إنجليزي)
                    </label>
                    <textarea
                      v-model="activityForm.instructions_en"
                      rows="3"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                      placeholder="أدخل التعليمات بالإنجليزية"
                    ></textarea>
                  </div>
                </div>
              </div>

              <!-- المقياس المرتبط (للاختبارات) -->
              <div v-if="activityForm.activity_type === 'quiz'" class="col-span-full">
                <label class="mb-1 block text-xs font-medium text-primary">
                  المقياس المرتبط
                </label>
                <select
                  v-model="activityForm.scale_id"
                  class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                >
                  <option :value="null">اختر مقياساً...</option>
                  <option v-for="scale in psychologicalScales" :key="scale.id" :value="scale.id">
                    {{ scale.name_ar }}
                  </option>
                </select>
              </div>

              <!-- محتوى نصي -->
              <div v-if="['text', 'reflection_questions'].includes(activityForm.activity_type)" class="col-span-full">
                <label class="mb-1 block text-xs font-medium text-primary">
                  المحتوى بالعربية
                </label>
                <textarea
                  v-model="activityForm.content_ar"
                  rows="4"
                  class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                  placeholder="أدخل محتوى النشاط"
                ></textarea>
              </div>

              <!-- رابط الوسائط -->
              <div v-if="['video', 'audio'].includes(activityForm.activity_type)" class="col-span-full">
                <label class="mb-1 block text-xs font-medium text-primary">
                  رابط الوسائط
                </label>
                <input
                  v-model="activityForm.media_url"
                  type="url"
                  class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                  placeholder="https://..."
                />
              </div>

              <!-- مدة التمرين -->
              <div v-if="activityForm.activity_type === 'exercise'" class="col-span-full">
                <label class="mb-1 block text-xs font-medium text-primary">
                  مدة التمرين (بالدقائق)
                </label>
                <input
                  v-model.number="activityForm.duration_minutes"
                  type="number"
                  min="1"
                  class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                />
              </div>

              <!-- ترتيب النشاط والحالة -->
              <div class="col-span-full">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <div>
                    <label class="mb-1 block text-xs font-medium text-primary">
                      ترتيب النشاط
                    </label>
                    <input
                      v-model.number="activityForm.activity_order"
                      type="number"
                      min="1"
                      class="input w-full border-primary bg-secondary py-2 text-sm text-primary transition-all focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                    />
                  </div>
                  <div class="flex items-center gap-2">
                    <input
                      v-model="activityForm.is_active"
                      type="checkbox"
                      class="checkbox"
                    />
                    <span class="text-sm text-primary">نشط</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="border-t border-primary bg-secondary px-6 py-4">
            <div class="flex justify-end gap-3">
              <button
                @click="closeModals"
                type="button"
                class="rounded-lg border border-primary bg-primary px-4 py-2 text-sm font-medium text-primary transition-colors hover:bg-tertiary"
              >
                إلغاء
              </button>
              <button
                @click="saveActivity"
                :disabled="isLoading"
                type="button"
                class="btn btn-brand flex items-center gap-2 rounded-lg bg-gradient-to-r from-brand-500 to-accent-500 px-4 py-2 text-sm text-white transition-all duration-200 hover:opacity-90"
              >
                <svg v-if="isLoading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isLoading ? 'جاري الحفظ...' : (editingActivity ? 'تحديث النشاط' : 'إضافة النشاط') }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center p-4 text-center">
        <div class="fixed inset-0 bg-black/50 transition-opacity duration-300" @click="showDeleteModal = false"></div>
        
        <div class="relative w-full max-w-md scale-100 transform overflow-hidden rounded-2xl bg-primary shadow-2xl transition-all duration-300">
          <div class="border-b border-primary bg-gradient-to-r from-red-500/5 to-red-500/10 px-6 py-4">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-primary">
                تأكيد الحذف
              </h3>
              <button @click="showDeleteModal = false" class="text-tertiary transition-colors hover:text-primary">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
          
          <div class="px-6 py-8">
            <div class="flex flex-col items-center gap-4">
              <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-500/10">
                <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </div>
              
              <div class="text-center">
                <h4 class="mb-2 text-lg font-semibold text-primary">
                  هل أنت متأكد من الحذف؟
                </h4>
                <p class="text-tertiary">
                  {{ 
                    editingProgram ? `سيتم حذف البرنامج "${editingProgram.name_ar}" وجميع الجلسات والأنشطة المرتبطة به.` :
                    editingSession ? `سيتم حذف الجلسة "${editingSession.title_ar}" وجميع الأنشطة المرتبطة بها.` :
                    `سيتم حذف النشاط "${editingActivity?.name_ar}" ولا يمكن التراجع عن هذا الإجراء.`
                  }}
                </p>
              </div>
            </div>
          </div>
          
          <div class="border-t border-primary bg-secondary px-6 py-4">
            <div class="flex justify-end gap-3">
              <button
                @click="showDeleteModal = false"
                type="button"
                class="rounded-lg border border-primary bg-primary px-4 py-2 text-sm font-medium text-primary transition-colors hover:bg-tertiary"
              >
                إلغاء
              </button>
              <button
                @click="deleteItem"
                :disabled="isLoading"
                type="button"
                class="btn flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2 text-sm text-white transition-all duration-200 hover:opacity-90"
              >
                <svg v-if="isLoading" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isLoading ? 'جاري الحذف...' : 'نعم، احذف' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}
</style>