<template>
  <div class="space-y-6">
    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="text-secondary">جاري التحميل...</div>
    </div>

    <!-- Activity Content -->
    <div v-else-if="activity" class="space-y-6">
      <!-- Header -->
      <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-2xl font-bold text-primary">{{ activity.name_ar }}</h1>
          <span
            class="badge"
            :class="activityStatus.isUnlocked ? 'badge-success' : 'badge-secondary'"
          >
            {{ activityStatus.isUnlocked ? 'متاح' : 'مقفل' }}
          </span>
        </div>
        <p v-if="!activityStatus.isUnlocked && activityStatus.unlockAt" class="text-xs text-secondary mb-2">
          يفتح في: {{ formatUnlockTime(activityStatus.unlockAt) }}
        </p>
        <p v-if="activity.instructions_ar" class="text-secondary mb-4">
          {{ activity.instructions_ar }}
        </p>
      </div>

      <!-- Content based on type -->
      <div class="card p-6">
        <!-- Text Content -->
        <div v-if="activity.activity_type === 'text' && activity.content_ar" class="prose">
          <div v-html="activity.content_ar"></div>
        </div>

        <!-- Video -->
        <!-- <div v-else-if="activity.activity_type === 'video' && activity.media_url" class="space-y-4">
          <video
            :src="activity.media_url"
            controls
            class="w-full rounded-lg"
          ></video>
        </div> -->

        <!-- Video -->
<div v-else-if="activity.activity_type === 'video' && activity.media_url" class="space-y-4">
  <!-- معلومات الرابط للتصحيح -->
  <div class="bg-gray-50 p-3 rounded-lg text-xs text-gray-600 break-all border border-gray-200">
    <div><span class="font-medium">الرابط الأصلي:</span> {{ activity.media_url }}</div>
    <div><span class="font-medium">الرابط النهائي:</span> {{ getVideoUrl(activity.media_url) }}</div>
  </div>
  
  <!-- مشغل الفيديو -->
  <video
    ref="videoPlayer"
    controls
    preload="metadata"
    playsinline
    class="w-full rounded-lg shadow-lg"
    style="max-height: 500px; background: #000;"
    @error="handleVideoError"
  >
    <source :src="getVideoUrl(activity.media_url)" type="video/mp4">
    <p class="text-secondary p-4 text-center">
      متصفحك لا يدعم تشغيل الفيديو.
    </p>
  </video>
  
  <!-- أزرار التحكم -->
  <div class="flex flex-wrap gap-3">
    <button 
      @click="openVideoInNewTab"
      class="btn btn-primary text-sm"
    >
      <i class="fas fa-external-link-alt ml-2"></i>
      فتح الفيديو في تبويب جديد
    </button>
    
    <button 
      @click="copyVideoLink"
      class="btn btn-outline text-sm"
    >
      <i class="fas fa-copy ml-2"></i>
      نسخ الرابط
    </button>
    
    <button 
      @click="downloadVideo"
      class="btn btn-outline text-sm"
    >
      <i class="fas fa-download ml-2"></i>
      تحميل الفيديو
    </button>
    
    <button 
      @click="validateVideoUrl"
      class="btn btn-outline text-sm"
    >
      <i class="fas fa-check-circle ml-2"></i>
      التحقق من الرابط
    </button>
    
    <button 
      @click="toggleFullscreen"
      class="btn btn-outline text-sm"
      v-if="videoPlayer"
    >
      <i class="fas fa-expand ml-2"></i>
      عرض كامل
    </button>
  </div>
</div>
        <!-- Audio -->
        <div v-else-if="activity.activity_type === 'audio' && activity.media_url" class="space-y-4">
          <audio
            :src="activity.media_url"
            controls
            class="w-full"
          ></audio>
        </div>

        <!-- Form -->
        <div v-else-if="activity.activity_type === 'form'" class="space-y-4">
          <form @submit.prevent="submitForm" class="space-y-4">
            <!-- يمكن إضافة حقول النموذج بناءً على activity_config -->
            <textarea
              v-model="formData.notes"
              rows="6"
              class="input w-full"
              placeholder="أدخل ملاحظاتك..."
            ></textarea>
            <button
              type="submit"
              :disabled="submitting"
              class="btn btn-primary"
            >
              {{ submitting ? 'جاري الحفظ...' : 'حفظ' }}
            </button>
          </form>
        </div>

        <!-- Exercise -->
        <div v-else-if="activity.activity_type === 'exercise'" class="space-y-4">
          <div class="text-center">
            <div class="text-4xl font-bold text-primary mb-2">{{ timer }}</div>
            <p class="text-secondary">دقيقة</p>
          </div>
          <div class="flex justify-center gap-4">
            <button
              v-if="!isTimerRunning"
              @click="startTimer"
              class="btn btn-primary"
            >
              بدء التمرين
            </button>
            <button
              v-else
              @click="stopTimer"
              class="btn btn-danger"
            >
              إيقاف
            </button>
          </div>
        </div>

        <!-- Reflection Questions -->
        <div v-else-if="activity.activity_type === 'reflection_questions'" class="space-y-4">
          <div v-if="activity.content_ar" class="prose">
            <div v-html="activity.content_ar"></div>
          </div>
          <div v-if="activity.activity_config?.questions?.length" class="space-y-3">
            <div
              v-for="(question, index) in activity.activity_config.questions"
              :key="index"
              class="rounded-lg border border-primary p-3 text-sm text-primary"
            >
              {{ question }}
            </div>
          </div>
        </div>

        <!-- Quiz / Assessment -->
        <div v-else-if="activity.activity_type === 'quiz' && activity.scale_id" class="text-center space-y-6 py-8">
          <div class="bg-primary/5 rounded-lg p-6 max-w-lg mx-auto">
            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-clipboard-check text-2xl text-primary"></i>
            </div>
            <h3 class="text-lg font-semibold text-primary mb-2">مقياس نفسي مطلوب</h3>
            <p class="text-secondary mb-6">
              يتطلب هذا النشاط إكمال مقياس نفسي لقياس مدى تقدمك.
            </p>
            <button
              @click="navigateToScale(activity.scale_id)"
              class="btn btn-primary w-full"
            >
              بدء المقياس الآن
            </button>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-between">
        <button
          v-if="activityStatus.isUnlocked && !activityStatus.isCompleted"
          @click="startActivity"
          :disabled="starting"
          class="btn btn-primary"
        >
          {{ starting ? 'جاري البدء...' : 'بدء النشاط' }}
        </button>
        <button
          v-if="activityStatus.isStarted"
          @click="completeActivity"
          :disabled="completing"
          class="btn btn-success"
        >
          {{ completing ? 'جاري الإكمال...' : 'إكمال النشاط' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { programService } from '../../../services/programService'

const route = useRoute()
const router = useRouter()

// 🔹 Refs
const activity = ref<any>(null)
const videoPlayer = ref<HTMLVideoElement | null>(null)

const activityStatus = ref({
  isUnlocked: false,
  isStarted: false,
  isCompleted: false,
  unlockAt: null as string | null
})

const loading = ref(false)
const starting = ref(false)
const completing = ref(false)
const submitting = ref(false)
const formData = ref({ notes: '' })
const timer = ref(0)
const isTimerRunning = ref(false)
let timerInterval: ReturnType<typeof setInterval> | null = null

// 🔹 دوال الفيديو
const getVideoUrl = (url: string) => {
  if (!url) return ''
  
  // 1. بيانات Base64
  if (url.startsWith('data:video') || url.startsWith('data:image')) {
    return url
  }
  
  // 2. رابط كامل
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url
  }
  
  // 3. Blob URL
  if (url.startsWith('blob:')) {
    return url
  }
  
  // 4. مسار تخزين نسبي
  const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  const baseUrl = apiBaseUrl.replace(/\/api$/, '').replace(/\/$/, '')
  let cleanUrl = url.replace(/^\/+/, '')
  
  if (!cleanUrl.startsWith('storage/')) {
    cleanUrl = `storage/${cleanUrl}`
  }
  
  return `${baseUrl}/${cleanUrl}`
}

// 🔹 دالة التحقق من توفر الفيديو
const isVideoAvailable = computed(() => {
  if (!activity.value?.media_url) return false
  const url = getVideoUrl(activity.value.media_url)
  return url && url.trim() !== ''
})

// 🔹 دالة فتح الفيديو في تبويب جديد
const openVideoInNewTab = () => {
  if (!activity.value?.media_url) {
    console.warn('⚠️ No media URL found')
    return
  }
  
  const url = getVideoUrl(activity.value.media_url)
  console.log('🔗 Opening video in new tab:', url)
  
  if (!url || url === '') {
    console.error('❌ Invalid URL')
    return
  }
  
  window.open(url, '_blank')
}

// 🔹 دالة نسخ الرابط
const copyVideoLink = async () => {
  if (!activity.value?.media_url) {
    console.warn('⚠️ No media URL found')
    return
  }
  
  const url = getVideoUrl(activity.value.media_url)
  console.log('📋 Copying URL:', url)
  
  try {
    await navigator.clipboard.writeText(url)
    console.log('✅ URL copied to clipboard')
  } catch (err) {
    console.error('❌ Failed to copy URL:', err)
    const textArea = document.createElement('textarea')
    textArea.value = url
    document.body.appendChild(textArea)
    textArea.select()
    document.execCommand('copy')
    document.body.removeChild(textArea)
    console.log('✅ URL copied using fallback method')
  }
}

// 🔹 دالة تحميل الفيديو
const downloadVideo = () => {
  if (!activity.value?.media_url) {
    console.warn('⚠️ No media URL found')
    return
  }
  
  const url = getVideoUrl(activity.value.media_url)
  console.log('⬇️ Downloading video:', url)
  
  const link = document.createElement('a')
  link.href = url
  link.download = `video_${activity.value.id || 'download'}.mp4`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// 🔹 دالة التحقق من صحة الرابط
const validateVideoUrl = async () => {
  if (!activity.value?.media_url) {
    console.warn('⚠️ No media URL found')
    return
  }
  
  const url = getVideoUrl(activity.value.media_url)
  console.log('🔍 Validating URL:', url)
  
  try {
    const response = await fetch(url, { method: 'HEAD' })
    console.log(`✅ URL valid: ${response.ok} (${response.status})`)
    return response.ok
  } catch (error) {
    console.error('❌ URL validation failed:', error)
    return false
  }
}

// 🔹 معالجة خطأ الفيديو
const handleVideoError = (event: Event) => {
  const video = event.target as HTMLVideoElement
  console.error('🎬 Video load error:', video.error)
  console.log('🔗 URL attempted:', getVideoUrl(activity.value?.media_url || ''))
}

// 🔹 تفعيل عرض كامل الشاشة
const toggleFullscreen = () => {
  if (!videoPlayer.value) return
  
  if (document.fullscreenElement) {
    document.exitFullscreen()
  } else {
    videoPlayer.value.requestFullscreen().catch((err) => {
      console.error('❌ Fullscreen error:', err)
    })
  }
}

// 🔹 باقي الدوال...
const ensureFrontendAuth = () => {
  const token = localStorage.getItem('frontend_token')
  if (!token) {
    router.push({
      path: '/login',
      query: { redirect: router.currentRoute.value.fullPath }
    })
    return false
  }
  return true
}

const navigateToScale = (scaleId: string) => {
  router.push({
    name: 'Measures', 
    query: { measureId: scaleId }
  })
}

const fetchActivity = async () => {
  loading.value = true
  try {
    if (!ensureFrontendAuth()) return
    
    const programId = route.params.programId as string
    const activityId = route.params.activityId as string
    
    const activityResponse = await programService.getActivity(activityId)
    if (activityResponse.data.success) {
      activity.value = activityResponse.data.data
      console.log('📹 Activity data:', activity.value)
      console.log('📹 Media URL:', activity.value?.media_url)
    }

    const statusResponse = await programService.checkActivityStatus(programId, activityId)
    if (statusResponse.data.success) {
      activityStatus.value = {
        isUnlocked: statusResponse.data.data.is_unlocked,
        isStarted: statusResponse.data.data.status === 'in_progress',
        isCompleted: statusResponse.data.data.status === 'completed',
        unlockAt: statusResponse.data.data.unlock_at || null
      }
    }
  } catch (error) {
    console.error('Error fetching activity:', error)
  } finally {
    loading.value = false
  }
}

const startActivity = async () => {
  starting.value = true
  try {
    if (!ensureFrontendAuth()) return
    const programId = route.params.programId as string
    const activityId = route.params.activityId as string
    await programService.startActivity(programId, activityId)
    activityStatus.value.isStarted = true
  } catch (error) {
    console.error('Error starting activity:', error)
  } finally {
    starting.value = false
  }
}

const completeActivity = async () => {
  completing.value = true
  try {
    if (!ensureFrontendAuth()) return
    const programId = route.params.programId as string
    const activityId = route.params.activityId as string
    await programService.completeActivity(programId, activityId, {
      progress_data: formData.value
    })
    activityStatus.value.isCompleted = true
  } catch (error) {
    console.error('Error completing activity:', error)
  } finally {
    completing.value = false
  }
}

const submitForm = async () => {
  submitting.value = true
  try {
    await completeActivity()
  } catch (error) {
    console.error('Error submitting form:', error)
  } finally {
    submitting.value = false
  }
}

const startTimer = () => {
  if (activity.value?.duration_minutes) {
    timer.value = activity.value.duration_minutes
    isTimerRunning.value = true
    timerInterval = setInterval(() => {
      if (timer.value > 0) {
        timer.value--
      } else {
        stopTimer()
      }
    }, 60000) // كل دقيقة
  }
}

const stopTimer = () => {
  isTimerRunning.value = false
  if (timerInterval) {
    clearInterval(timerInterval)
    timerInterval = null
  }
}

const formatUnlockTime = (value: string) => {
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleString('ar-SA', { dateStyle: 'medium', timeStyle: 'short' })
}

onMounted(() => {
  fetchActivity()
})

onUnmounted(() => {
  stopTimer()
})
</script>