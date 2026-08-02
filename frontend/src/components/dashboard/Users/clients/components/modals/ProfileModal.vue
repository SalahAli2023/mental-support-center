<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-primary rounded-2xl shadow-xl border border-primary">
      <!-- الرأس -->
      <div class="flex items-center justify-between p-6 border-b border-primary">
        <div class="flex items-center gap-4">
          <img 
            :src="patientAvatar" 
            :alt="patient.name"
            class="w-16 h-16 rounded-full border-4 border-brand-500"
          />
          <div>
            <h2 class="text-xl font-bold text-primary">{{ patient.name }}</h2>
            <p class="text-secondary">{{ patient.email }} • {{ patient.phone }}</p>
          </div>
        </div>
        <button @click="$emit('close')" class="p-2 hover:bg-tertiary rounded-lg text-primary">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- المحتوى -->
      <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- العمود الأيسر - المعلومات الشخصية -->
          <div class="lg:col-span-1 space-y-6">
            <!-- المعلومات الأساسية -->
            <div class="bg-secondary rounded-xl p-4">
              <h3 class="font-semibold text-primary mb-4">المعلومات الشخصية</h3>
              <div class="space-y-3">
                <div>
                  <p class="text-xs text-secondary">العمر</p>
                  <p class="text-sm font-medium text-primary">{{ calculatedAge }} سنة</p>
                </div>
                <div>
                  <p class="text-xs text-secondary">الجنس</p>
                  <p class="text-sm font-medium text-primary">{{ patient.gender === 'male' ? 'ذكر' : 'أنثى' }}</p>
                </div>
                <div>
                  <p class="text-xs text-secondary">تاريخ الميلاد</p>
                  <p class="text-sm font-medium text-primary">{{ formattedDateOfBirth }}</p>
                </div>
                <div>
                  <p class="text-xs text-secondary">تاريخ التسجيل</p>
                  <p class="text-sm font-medium text-primary">{{ patient.createdAt }}</p>
                </div>
              </div>
            </div>

            <!-- معلومات الاتصال -->
            <div class="bg-secondary rounded-xl p-4">
              <h3 class="font-semibold text-primary mb-4">معلومات الاتصال</h3>
              <div class="space-y-3">
                <div class="flex items-center gap-2">
                  <span class="text-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                  </span>
                  <span class="text-sm text-primary">{{ patient.email }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                  </span>
                  <span class="text-sm text-primary">{{ patient.phone }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <span class="text-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </span>
                  <span class="text-sm text-primary">{{ patient.address }}</span>
                </div>
              </div>
            </div>

            <!-- إحصائيات -->
            <div class="bg-secondary rounded-xl p-4">
              <h3 class="font-semibold text-primary mb-4">الإحصائيات</h3>
              <div class="space-y-3">
                <div>
                  <p class="text-xs text-secondary">إجمالي الجلسات</p>
                  <p class="text-sm font-medium text-primary">{{ sessionsStats.total_sessions || 0 }} جلسة</p>
                </div>
                <div>
                  <p class="text-xs text-secondary">آخر جلسة</p>
                  <p class="text-sm font-medium text-primary">{{ lastSessionDate || 'لا توجد' }}</p>
                </div>
                <div>
                  <p class="text-xs text-secondary">حالة المريض</p>
                  <span :class="[
                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1',
                    patient.status === 'active' ? 'bg-green-100 text-green-800' :
                    'bg-red-100 text-red-800'
                  ]">
                    {{ patient.status === 'active' ? 'نشط' : 'غير نشط' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- العمود الأيمن - المعلومات الطبية -->
          <div class="lg:col-span-2 space-y-6">
            <!-- الحالة الصحية -->
            <div class="bg-primary border border-primary rounded-xl p-4">
              <h3 class="font-semibold text-primary mb-4">الحالة الصحية</h3>
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="condition in patient.conditions || []" 
                  :key="condition"
                  class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full"
                >
                  {{ condition }}
                </span>
                <div v-if="!patient.conditions || patient.conditions.length === 0" class="text-center w-full py-2">
                  <p class="text-secondary">لا توجد حالات صحية مسجلة</p>
                </div>
              </div>
            </div>

            <!-- أهداف العلاج -->
            <div class="bg-primary border border-primary rounded-xl p-4">
              <h3 class="font-semibold text-primary mb-4">أهداف العلاج</h3>
              <p class="text-primary leading-relaxed">{{ patient.therapyGoals || 'لا توجد أهداف علاجية محددة' }}</p>
            </div>

            <!-- التقدم العلاجي -->
              <div class="bg-primary border border-primary rounded-xl p-4">
              <h3 class="font-semibold text-primary mb-4">التقدم العلاجي</h3>
              <div class="space-y-4">
                <div>
                  <div class="flex justify-between text-sm text-primary mb-1">
                    <span>التقدم العام</span>
                    <span>{{ averageSessionProgress }}%</span>
                  </div>
                  <div class="w-full bg-secondary rounded-full h-2">
                    <div class="bg-brand-500 h-2 rounded-full" :style="{ width: averageSessionProgress + '%' }"></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between text-sm text-primary mb-1">
                    <span>الالتزام بالجلسات</span>
                    <span>{{ attendanceRate }}%</span>
                  </div>
                  <div class="w-full bg-secondary rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" :style="{ width: attendanceRate + '%' }"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- الجلسات القادمة -->
            <div class="bg-primary border border-primary rounded-xl p-4">
              <h3 class="font-semibold text-primary mb-4">الجلسات القادمة</h3>
              <div class="space-y-3">
                <div v-if="nextSession" class="flex items-center justify-between p-3 bg-secondary rounded-lg">
                  <div>
                    <p class="font-medium text-primary">{{ nextSession.title_ar || nextSession.title }}</p>
                    <p class="text-sm text-secondary">{{ formatSessionDate(nextSession.session_date) }} - {{ nextSession.session_time }}</p>
                    <p class="text-xs text-secondary">المعالج: {{ getTherapistName(nextSession.therapist_id) }}</p>
                  </div>
                  <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                    مؤكدة
                  </span>
                </div>
                <div v-else class="text-center py-4 text-secondary">
                  <p>لا توجد جلسات قادمة</p>
                </div>
              </div>
            </div>

            <!-- الملاحظات الأخيرة -->
            <div class="bg-primary border border-primary rounded-xl p-4">
              <h3 class="font-semibold text-primary mb-4">آخر الملاحظات</h3>
              <div class="space-y-3">
                <div v-if="latestSessionNote" class="p-3 bg-secondary rounded-lg">
                  <p class="text-sm text-primary">{{ latestSessionNote.notes_ar || latestSessionNote.notes }}</p>
                  <p class="text-xs text-secondary mt-2">{{ getTherapistName(latestSessionNote.therapist_id) }} - {{ formatSessionDate(latestSessionNote.session_date) }}</p>
                </div>
                <div v-else class="text-center py-4 text-secondary">
                  <p>لا توجد ملاحظات مسجلة</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'
import { usePatientSessionsStore } from '@/stores/patientSessions'

const sessionsStore = usePatientSessionsStore()

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  patient: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['close'])

// جلب بيانات الجلسات عند فتح النافذة
onMounted(() => {
  if (props.open && props.patient?.id) {
    loadSessionsData()
  }
})

// مراقبة فتح النافذة لتحميل البيانات
watch(() => props.open, (isOpen) => {
  if (isOpen && props.patient?.id) {
    loadSessionsData()
  }
})

// تحميل بيانات الجلسات
const loadSessionsData = async () => {
  if (!props.patient?.id) return
  
  try {
    await sessionsStore.fetchSessions(props.patient.id)
    await sessionsStore.fetchStats(props.patient.id)
    if (sessionsStore.therapists.length === 0) {
      await sessionsStore.fetchTherapists()
    }
  } catch (error) {
    console.error('Error loading sessions data:', error)
  }
}

const averageSessionProgress = computed(() => {
  if (!sessionsStore.sessions || sessionsStore.sessions.length === 0) return 0
  
  const sessionsWithProgress = sessionsStore.sessions.filter(session => 
    session.progress && session.progress > 0
  )
  
  if (sessionsWithProgress.length === 0) return 0
  
  const totalProgress = sessionsWithProgress.reduce((sum, session) => sum + session.progress, 0)
  return Math.round(totalProgress / sessionsWithProgress.length)
})

// معدل الالتزام (الجلسات المكتملة من المجدولة)
const attendanceRate = computed(() => {
  if (!sessionsStore.sessions || sessionsStore.sessions.length === 0) return 0
  
  const completedSessions = sessionsStore.sessions.filter(session => session.status === 'completed').length
  const scheduledSessions = sessionsStore.sessions.filter(session => 
    session.status === 'scheduled' || session.status === 'completed'
  ).length
  
  if (scheduledSessions === 0) return 0
  
  return Math.round((completedSessions / scheduledSessions) * 100)
})


// الصورة الافتراضية بناءً على الجنس
const patientAvatar = computed(() => {
  if (props.patient.avatar) {
    return props.patient.avatar
  }
  
  if (props.patient.gender === 'female') {
    return '/images/default-female-avatar.png'
  } else {
    return '/images/default-female-avatar.png'
  }
})

// حساب العمر من تاريخ الميلاد
const calculatedAge = computed(() => {
  if (!props.patient.dateOfBirth) return 'غير محدد'
  
  try {
    const birthDate = new Date(props.patient.dateOfBirth)
    const today = new Date()
    let age = today.getFullYear() - birthDate.getFullYear()
    const monthDiff = today.getMonth() - birthDate.getMonth()
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      age--
    }
    
    return age
  } catch (error) {
    console.error('Error calculating age:', error)
    return 'غير محدد'
  }
})

// تنسيق تاريخ الميلاد
const formattedDateOfBirth = computed(() => {
  if (!props.patient.dateOfBirth) return 'غير محدد'
  
  try {
    const date = new Date(props.patient.dateOfBirth)
    return date.toLocaleDateString('ar-SA', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (error) {
    return props.patient.dateOfBirth
  }
})

// بيانات افتراضية للتقدم العلاجي (يمكن استبدالها ببيانات حقيقية من الـ API)
const treatmentProgress = computed(() => {
  return props.patient.treatmentProgress || 65
})

const sessionCompliance = computed(() => {
  return props.patient.sessionCompliance || 80
})

// === البيانات الحقيقية للجلسات ===

// إحصائيات الجلسات من الـ store
const sessionsStats = computed(() => {
  return sessionsStore.stats || {}
})

// الجلسة القادمة
const nextSession = computed(() => {
  if (!sessionsStore.sessions || sessionsStore.sessions.length === 0) return null
  
  const today = new Date()
  const upcomingSessions = sessionsStore.sessions
    .filter(session => {
      if (session.status !== 'scheduled') return false
      const sessionDate = new Date(session.session_date)
      return sessionDate >= today
    })
    .sort((a, b) => new Date(a.session_date) - new Date(b.session_date))
  
  return upcomingSessions[0] || null
})

// تاريخ آخر جلسة
const lastSessionDate = computed(() => {
  if (!sessionsStore.sessions || sessionsStore.sessions.length === 0) return 'لا توجد'
  
  const completedSessions = sessionsStore.sessions
    .filter(session => session.status === 'completed')
    .sort((a, b) => new Date(b.session_date) - new Date(a.session_date))
  
  if (completedSessions.length === 0) return 'لا توجد'
  
  return formatSessionDate(completedSessions[0].session_date)
})

// أحدث ملاحظة من الجلسات
const latestSessionNote = computed(() => {
  if (!sessionsStore.sessions || sessionsStore.sessions.length === 0) return null
  
  const sessionsWithNotes = sessionsStore.sessions
    .filter(session => session.notes_ar || session.notes)
    .sort((a, b) => new Date(b.session_date) - new Date(a.session_date))
  
  return sessionsWithNotes[0] || null
})

// الحصول على اسم المعالج
const getTherapistName = (therapistId) => {
  if (!therapistId) return 'غير محدد'
  
  const therapist = sessionsStore.therapists.find(t => t.id === therapistId)
  return therapist ? (therapist.name_ar || therapist.name_en || therapist.name || 'غير محدد') : 'غير محدد'
}

// تنسيق تاريخ الجلسة
const formatSessionDate = (dateString) => {
  if (!dateString) return 'غير محدد'
  
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('ar-SA', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (error) {
    return dateString
  }
}
</script>