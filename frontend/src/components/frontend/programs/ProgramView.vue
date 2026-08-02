<template>
  <div class="space-y-6">
    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="text-secondary">جاري التحميل...</div>
    </div>

    <!-- Error State -->
    <div v-else-if="!program && !loading" class="text-center py-12">
      <div class="text-red-500 mb-4">
        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
        <h2 class="text-2xl font-bold mb-2">فشل في تحميل البرنامج</h2>
        <p class="text-gray-600 mb-4">حدث خطأ أثناء تحميل البرنامج. يرجى المحاولة مرة أخرى</p>
        <button 
          @click="fetchProgram"
          class="btn btn-primary mr-2"
        >
          إعادة المحاولة
        </button>
        <button 
          @click="$router.push('/program')"
          class="btn btn-secondary"
        >
          العودة إلى قائمة البرامج
        </button>
      </div>
    </div>

    <!-- Program Content -->
    <div v-else-if="program" class="space-y-6">
      <!-- Header -->
      <div class="card p-6">
        <div class="flex flex-col md:flex-row gap-6">
          <img
            v-if="program.image_url"
            :src="program.image_url"
            :alt="program.name_ar"
            class="w-full md:w-64 h-48 md:h-64 rounded-lg object-cover"
          />
          <div class="flex-1">
            <h1 class="text-3xl font-bold text-primary mb-4">{{ program.name_ar }}</h1>
            <p class="text-secondary mb-4">{{ program.description_ar }}</p>
            <div class="flex flex-wrap gap-4 text-sm">
              <span class="text-secondary">المدة: {{ program.duration }} يوم</span>
              <span v-if="program.max_duration_days" class="text-secondary">
                الحد الأقصى: {{ program.max_duration_days }} يوم
              </span>
              <span v-if="program.session_duration_minutes" class="text-secondary">
                مدة الجلسة: {{ program.session_duration_minutes }} دقيقة
              </span>
              <span v-if="program.session_gap_hours !== undefined" class="text-secondary">
                الفاصل بين الجلسات: {{ program.session_gap_hours }} ساعة
              </span>
              <span v-if="program.activity_gap_hours !== undefined" class="text-secondary">
                الفاصل بين الأنشطة: {{ program.activity_gap_hours }} ساعة
              </span>
              <span v-if="program.scale" class="text-secondary">
                المقياس: {{ program.scale.name_ar }}
              </span>
            </div>
            <div class="mt-4">
              <button
                v-if="!isEnrolled && !enrolling"
                @click="handleStartProgram"
                class="btn btn-primary"
              >
                التسجيل في البرنامج
              </button>
              <button
                v-else-if="enrolling"
                disabled
                class="btn btn-primary opacity-50 cursor-not-allowed"
              >
                جاري التسجيل...
              </button>
              <button
                v-else-if="isEnrolled"
                @click="handleContinueProgram"
                class="btn btn-primary"
              >
                متابعة البرنامج
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Progress (if enrolled) -->
      <div v-if="userProgress" class="card p-6">
        <h2 class="text-xl font-semibold text-primary mb-4">تقدمك في البرنامج</h2>
        <div class="space-y-4">
          <div>
            <div class="flex items-center justify-between text-sm mb-2">
              <span class="text-secondary">التقدم الإجمالي</span>
              <span class="font-semibold text-primary">{{ userProgress.progress_percentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div
                class="bg-primary h-3 rounded-full transition-all"
                :style="{ width: `${userProgress.progress_percentage}%` }"
              ></div>
            </div>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-primary">{{ progress.phases.completed }}/{{ progress.phases.total }}</div>
              <div class="text-sm text-secondary">المراحل</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-primary">{{ progress.sessions.completed }}/{{ progress.sessions.total }}</div>
              <div class="text-sm text-secondary">الجلسات</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-primary">{{ progress.activities.completed }}/{{ progress.activities.total }}</div>
              <div class="text-sm text-secondary">الأنشطة</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-primary">{{ progress.homework.completed }}/{{ progress.homework.total }}</div>
              <div class="text-sm text-secondary">المهام</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Phases -->
      <div class="card p-6">
        <h2 class="text-xl font-semibold text-primary mb-4">مراحل البرنامج</h2>
        <div class="space-y-4">
          <PhaseCard
            v-for="phase in program.phases"
            :key="phase.id"
            :phase="phase"
            :user-progress="userProgress"
            :is-enrolled="isEnrolled"
            @click="viewPhase(phase)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { programService } from '@/services/programService'
import PhaseCard from './PhaseCard.vue'
import { useUserProgramStore } from '@/stores/userPrograms'
import { useNotifications } from '@/composables/useNotifications'
import api from '@/utils/api'

const route = useRoute()
const router = useRouter()
const { showError, showSuccess } = useNotifications()
const program = ref<any>(null)
const userProgress = ref<any>(null)
const progress = ref<any>({
  phases: { total: 0, completed: 0 },
  sessions: { total: 0, completed: 0 },
  activities: { total: 0, completed: 0 },
  homework: { total: 0, completed: 0 }
})
const loading = ref(false)
const enrolling = ref(false)

const isEnrolled = computed(() => {
  // التحقق من is_enrolled في بيانات البرنامج أولاً (أسرع)
  if (program.value?.is_enrolled) return true
  // التحقق من userProgress (يعني مسجل)
  if (userProgress.value) return true
  return false
})

const isFrontendAuthenticated = computed(() => {
  return !!localStorage.getItem('frontend_token')
})

const ensureFrontendAuth = () => {
  if (!isFrontendAuthenticated.value) {
    showError('يجب تسجيل الدخول لبدء البرنامج')
    router.push({
      path: '/login',
      query: { redirect: router.currentRoute.value.fullPath }
    })
    return false
  }
  return true
}

const fetchProgram = async () => {
  loading.value = true
  try {
    const programId = route.params.id as string
    console.log('📡 Fetching program:', programId)
    console.log('📡 API URL will be:', `${api.defaults.baseURL}/frontend/programs/${programId}`)
    
    const response = await programService.getPublicProgram(programId)
    console.log('📦 Program API Response:', response)
    console.log('📦 Response status:', response.status)
    console.log('📦 Response data:', response.data)
    
    if (response.data && response.data.success) {
      program.value = response.data.data
      console.log('✅ Program loaded:', program.value)
      
      // إذا كان المستخدم مسجل، جلب التقدم
      if (program.value.is_enrolled) {
        await fetchUserProgress()
      } else {
        // إذا لم يكن مسجل، تأكد من مسح userProgress
        userProgress.value = null
      }
    } else {
      console.error('❌ API response not successful:', response.data)
      program.value = null
      // عرض toast بدلاً من صفحة خطأ
      showError(response.data?.message || 'فشل في تحميل البرنامج')
      // لا نعيد التوجيه تلقائياً - نعرض رسالة خطأ فقط
    }
  } catch (error: any) {
    console.error('❌ Error fetching program:', error)
    console.error('Error details:', {
      message: error.message,
      response: error.response,
      request: error.request,
      config: error.config
    })
    
    if (error.response) {
      console.error('Response status:', error.response.status)
      console.error('Response data:', error.response.data)
      
      if (error.response.status === 404) {
        showError('البرنامج غير موجود')
      } else if (error.response.status === 500) {
        showError('حدث خطأ في الخادم. يرجى المحاولة لاحقاً')
      } else {
        showError(error.response.data?.message || 'حدث خطأ في تحميل البرنامج')
      }
    } else if (error.request) {
      console.error('No response received:', error.request)
      showError('لا يمكن الاتصال بالخادم. يرجى التحقق من الاتصال بالإنترنت')
    } else {
      console.error('Error setting up request:', error.message)
      showError('حدث خطأ في الاتصال بالخادم: ' + error.message)
    }
    
    program.value = null
    // لا نعيد التوجيه تلقائياً - نعرض رسالة خطأ فقط
  } finally {
    loading.value = false
  }
}

const fetchUserProgress = async () => {
  try {
    if (!isFrontendAuthenticated.value) {
      userProgress.value = null
      return
    }
    const programId = route.params.id as string
    const response = await programService.getUserProgress(programId)
    if (response.data.success) {
      userProgress.value = response.data.data.user_program
      progress.value = response.data.data.progress
    }
  } catch (error) {
    // User not enrolled
    userProgress.value = null
  }
}

const enrollInProgram = async () => {
  if (!ensureFrontendAuth()) return
  enrolling.value = true
  try {
    const programId = route.params.id as string
    // التسجيل في البرنامج
    const enrollResponse = await programService.enrollInProgram(programId)
    if (enrollResponse.data.success) {
      // تحديث حالة التسجيل محلياً فوراً (قبل إعادة الجلب)
      if (program.value) {
        program.value.is_enrolled = true
      }
      
      // إعادة جلب بيانات البرنامج والتقدم للحصول على البيانات الكاملة
      await Promise.all([
        fetchProgram(),
        fetchUserProgress()
      ])
      
      // التأكد من تحديث is_enrolled بعد إعادة الجلب
      if (program.value) {
        program.value.is_enrolled = true
      }
      
      console.log('✅ Enrollment successful, isEnrolled:', isEnrolled.value)
    }
  } catch (error: any) {
    console.error('Error enrolling:', error)
    if (error.response?.status === 400) {
      // المستخدم مسجل بالفعل - تحديث البيانات
      if (program.value) {
        program.value.is_enrolled = true
      }
      await Promise.all([
        fetchProgram(),
        fetchUserProgress()
      ])
    }
  } finally {
    enrolling.value = false
  }
}

const continueProgram = () => {
  if (!ensureFrontendAuth()) return
  // التحقق من التسجيل أولاً
  if (!isEnrolled.value) {
    showError('يجب التسجيل في البرنامج أولاً')
    return
  }
  
  // إذا كان هناك تقدم، ابدأ من المرحلة الحالية
  if (userProgress.value?.current_phase_id) {
    const currentPhase = program.value?.phases?.find((p: any) => p.id === userProgress.value.current_phase_id)
    if (currentPhase) {
      viewPhase(currentPhase)
      return
    }
  }
  
  // إذا لم يكن هناك تقدم، ابدأ من المرحلة الأولى
  if (program.value?.phases?.[0]) {
    viewPhase(program.value.phases[0])
  } else {
    showError('لا توجد مراحل متاحة في هذا البرنامج')
  }
}

const viewPhase = async (phase: any) => {
  if (!ensureFrontendAuth()) return
  // التحقق من التسجيل قبل الانتقال
  if (!isEnrolled.value) {
    // محاولة إعادة جلب بيانات البرنامج للتأكد
    await fetchProgram()
    
    if (!program.value?.is_enrolled && !userProgress.value) {
      alert('يجب التسجيل في البرنامج أولاً. يرجى الضغط على زر "التسجيل في البرنامج"')
      return
    }
  }
  
  // التحقق من أن المرحلة غير مقفلة (المرحلة الأولى دائماً متاحة)
  if (phase.phase_order > 1) {
    // يمكن إضافة منطق للتحقق من إكمال المراحل السابقة لاحقاً
    // حالياً نسمح بالوصول إذا كان المستخدم مسجل
    if (!isEnrolled.value) {
      alert('يجب التسجيل في البرنامج أولاً')
      return
    }
  }
  
  // عرض الجلسات الخاصة بالمرحلة
  if (program.value?.phases) {
    const targetPhase = program.value.phases.find((p: any) => p.id === phase.id)
    if (targetPhase && targetPhase.sessions && targetPhase.sessions.length > 0) {
      // عرض الجلسات في modal أو navigation
      // حالياً نعرض رسالة توضيحية
      const sessionsList = targetPhase.sessions.map((s: any) => s.title_ar || s.title_en).join('\n- ')
      alert(`مرحلة: ${phase.name_ar}\n\nالجلسات المتاحة:\n- ${sessionsList}\n\nسيتم إضافة صفحة المرحلة قريباً`)
    } else {
      alert(`مرحلة: ${phase.name_ar}\n\nلا توجد جلسات متاحة في هذه المرحلة حالياً`)
    }
  } else {
    // إذا لم تكن المراحل محملة، أعد تحميل البرنامج
    await fetchProgram()
    viewPhase(phase)
  }
}

const handleStartProgram = () => {
  if (!ensureFrontendAuth()) return
  enrollInProgram()
}

const handleContinueProgram = () => {
  if (!ensureFrontendAuth()) return
  continueProgram()
}

onMounted(async () => {
  await fetchProgram()
  await fetchUserProgress()
})
</script>

