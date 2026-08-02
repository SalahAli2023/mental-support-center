<template>
  <div class="bg-primary border border-primary rounded-lg overflow-hidden">
    <!-- جدول للشاشات الكبيرة -->
    <div class="hidden lg:block overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="border-b border-primary bg-secondary">
            <th class="px-4 py-3 text-start font-medium text-primary">المريض</th>
            <th class="px-4 py-3 text-start font-medium text-primary">معلومات الاتصال</th>
            <th class="px-4 py-3 text-start font-medium text-primary">الحالة الصحية</th>
            <th class="px-4 py-3 text-start font-medium text-primary">الجلسات</th>
            <th class="px-4 py-3 text-start font-medium text-primary">البرامج والتقدم</th>
            <th class="px-4 py-3 text-start font-medium text-primary">الحالة</th>
            <th class="px-4 py-3 text-start font-medium text-primary">الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          <tr 
            v-for="patient in patients" 
            :key="patient.id"
            class="border-b border-primary hover:bg-tertiary transition-colors"
          >
            <!-- Patient Info -->
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <img 
                  :src="getPatientAvatar(patient)" 
                  :alt="patient.name"
                  class="w-10 h-10 rounded-full object-cover border border-primary"
                />
                <div>
                  <div class="text-primary font-medium">{{ patient.name }}</div>
                  <div class="text-xs text-secondary">{{ calculateAge(patient.dateOfBirth) }} سنة • {{ patient.gender === 'male' ? 'ذكر' : 'أنثى' }}</div>
                </div>
              </div>
            </td>

            <!-- Contact Info -->
            <td class="px-4 py-3">
              <div class="text-primary">{{ patient.email }}</div>
              <div class="text-xs text-secondary">{{ patient.phone }}</div>
            </td>

            <!-- Health Conditions -->
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-1">
                <span 
                  v-for="condition in (patient.conditions || []).slice(0, 2)" 
                  :key="condition"
                  class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full"
                >
                  {{ condition }}
                </span>
                <span 
                  v-if="patient.conditions && patient.conditions.length > 2"
                  class="px-2 py-1 bg-tertiary text-secondary text-xs rounded-full"
                >
                  +{{ patient.conditions.length - 2 }}
                </span>
                <span 
                  v-if="!patient.conditions || patient.conditions.length === 0"
                  class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded-full"
                >
                  لا توجد
                </span>
              </div>
            </td>

            <!-- Sessions - بيانات حقيقية -->
            <td class="px-4 py-3">
              <div class="space-y-1">
                <div class="text-primary font-medium">{{ getPatientSessionsCount(patient.id) }} جلسة</div>
                <div class="text-xs text-secondary">
                  آخر جلسة: {{ getLastSessionDate(patient.id) }}
                </div>
                <div class="text-xs">
                  <span :class="[
                    'px-2 py-1 rounded-full',
                    getNextSession(patient.id) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
                  ]">
                    {{ getNextSession(patient.id) ? `القادمة: ${getNextSession(patient.id)}` : 'لا توجد جلسة قادمة' }}
                  </span>
                </div>
              </div>
            </td>

            <!-- Programs -->
            <td class="px-4 py-3">
              <div class="space-y-2">
                <div v-if="patient.programs && patient.programs.length" class="space-y-1">
                  <div
                    v-for="program in patient.programs.slice(0, 2)"
                    :key="program.id"
                    class="flex items-center justify-between gap-2"
                  >
                    <span class="text-primary text-xs truncate">
                      {{ program.program_name || 'برنامج' }}
                    </span>
                    <span class="text-xs text-secondary whitespace-nowrap">
                      {{ program.progress ?? 0 }}%
                    </span>
                  </div>
                  <div v-if="patient.programs.length > 2" class="text-xs text-secondary">
                    +{{ patient.programs.length - 2 }} برامج أخرى
                  </div>
                </div>
                <div v-else class="text-xs text-secondary">لا توجد برامج</div>
              </div>
            </td>

            <!-- Status -->
            <td class="px-4 py-3">
              <span :class="[
                'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                patient.status === 'active' ? 'bg-green-100 text-green-800' :
                'bg-red-100 text-red-800'
              ]">
                {{ patient.status === 'active' ? 'نشط' : 'غير نشط' }}
              </span>
            </td>

            <!-- Actions -->
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <button 
                  @click="$emit('view-profile', patient)"
                  class="bg-brand-500 hover:bg-[#8FAE2F] text-white p-2 rounded-lg flex items-center gap-1 text-xs"
                  title="الملف الشخصي"
                >
                  <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                <button 
                  @click="openSessionsAndLoad(patient)"
                  class="bg-gray-500 hover:bg-gray-600 text-white p-2 rounded-lg flex items-center gap-1 text-xs"
                  title="الجلسات"
                >
                  <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </button>
                <button 
                  @click="$emit('edit-patient', patient)"
                  class="bg-tertiary hover:bg-primary text-primary p-2 rounded"
                  title="تعديل"
                >
                  <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button 
                  @click="$emit('delete-patient', patient)"
                  class="bg-tertiary hover:bg-primary text-red-500 p-2 rounded"
                  title="حذف"
                >
                  <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- بطاقات للشاشات الصغيرة -->
    <div class="lg:hidden space-y-3 p-3">
      <div 
        v-for="patient in patients" 
        :key="patient.id"
        class="bg-secondary rounded-lg p-4 border border-primary"
      >
        <!-- رأس البطاقة -->
        <div class="flex items-start justify-between mb-3">
          <div class="flex items-center gap-3">
            <img 
              :src="getPatientAvatar(patient)" 
              :alt="patient.name"
              class="w-12 h-12 rounded-full object-cover border border-primary"
            />
            <div>
              <div class="text-primary font-medium">{{ patient.name }}</div>
              <div class="text-xs text-secondary">{{ calculateAge(patient.dateOfBirth) }} سنة • {{ patient.gender === 'male' ? 'ذكر' : 'أنثى' }}</div>
            </div>
          </div>
          <span :class="[
            'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
            patient.status === 'active' ? 'bg-green-100 text-green-800' :
            'bg-red-100 text-red-800'
          ]">
            {{ patient.status === 'active' ? 'نشط' : 'غير نشط' }}
          </span>
        </div>

        <!-- معلومات الاتصال -->
        <div class="space-y-2 mb-3">
          <div class="flex items-center gap-2 text-sm">
            <span class="text-secondary">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
              </svg>
            </span>
            <span class="text-primary">{{ patient.email }}</span>
          </div>
          <div class="flex items-center gap-2 text-sm">
            <span class="text-secondary">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
              </svg>
            </span>
            <span class="text-primary">{{ patient.phone }}</span>
          </div>
        </div>

        <!-- الحالات الصحية -->
        <div class="mb-3">
          <div class="flex flex-wrap gap-1">
            <span 
              v-for="condition in (patient.conditions || []).slice(0, 3)" 
              :key="condition"
              class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full"
            >
              {{ condition }}
            </span>
            <span 
              v-if="patient.conditions && patient.conditions.length > 3"
              class="px-2 py-1 bg-tertiary text-secondary text-xs rounded-full"
            >
              +{{ patient.conditions.length - 3 }}
            </span>
            <span 
              v-if="!patient.conditions || patient.conditions.length === 0"
              class="px-2 py-1 bg-gray-100 text-gray-500 text-xs rounded-full"
            >
              لا توجد حالات
            </span>
          </div>
        </div>

        <!-- الجلسات - بيانات حقيقية -->
        <div class="space-y-2 mb-4">
          <div class="text-sm text-primary">
            <span class="font-medium">{{ getPatientSessionsCount(patient.id) }} جلسة</span>
            <span class="text-secondary text-xs mr-2"> • آخر جلسة: {{ getLastSessionDate(patient.id) }}</span>
          </div>
          <div class="text-xs">
            <span :class="[
              'px-2 py-1 rounded-full',
              getNextSession(patient.id) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'
            ]">
              {{ getNextSession(patient.id) ? `القادمة: ${getNextSession(patient.id)}` : 'لا توجد جلسة قادمة' }}
            </span>
          </div>
        </div>

        <!-- البرامج -->
        <div class="space-y-2 mb-4">
          <div class="text-sm text-primary font-medium">البرامج والتقدم</div>
          <div v-if="patient.programs && patient.programs.length" class="space-y-1">
            <div
              v-for="program in patient.programs.slice(0, 2)"
              :key="program.id"
              class="flex items-center justify-between gap-2 text-xs"
            >
              <span class="text-primary truncate">
                {{ program.program_name || 'برنامج' }}
              </span>
              <span class="text-secondary whitespace-nowrap">
                {{ program.progress ?? 0 }}%
              </span>
            </div>
            <div v-if="patient.programs.length > 2" class="text-xs text-secondary">
              +{{ patient.programs.length - 2 }} برامج أخرى
            </div>
          </div>
          <div v-else class="text-xs text-secondary">لا توجد برامج</div>
        </div>

        <!-- الإجراءات -->
        <div class="flex items-center justify-between pt-3 border-t border-primary">
          <div class="flex items-center gap-1">
            <button 
              @click="$emit('view-profile', patient)"
              class="bg-brand-500 hover:bg-[#8FAE2F] text-white p-2 rounded-lg flex items-center gap-1 text-xs"
              title="الملف الشخصي"
            >
              <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
            </button>
            <button 
              @click="openSessionsAndLoad(patient)"
              class="bg-gray-500 hover:bg-gray-600 text-white p-2 rounded-lg flex items-center gap-1 text-xs"
              title="الجلسات"
            >
              <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </button>
          </div>
          <div class="flex items-center gap-1">
            <button 
              @click="$emit('edit-patient', patient)"
              class="bg-tertiary hover:bg-primary text-primary p-2 rounded text-sm"
              title="تعديل"
            >
              <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
            <button 
              @click="$emit('delete-patient', patient)"
              class="bg-tertiary hover:bg-primary text-red-500 p-2 rounded text-sm"
              title="حذف"
            >
              <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePatientsStore } from '@/stores/patients'
import { usePatientSessionsStore } from '@/stores/patientSessions'
import api from '@/utils/api'

const patientsStore = usePatientsStore()
const sessionsStore = usePatientSessionsStore()

const patientSessionStats = ref({})
const statsLoading = ref(false)

defineProps({
  patients: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['view-profile', 'edit-patient', 'delete-patient', 'open-sessions'])

const defaultStats = () => ({
  total_sessions: 0,
  completed_sessions: 0,
  scheduled_sessions: 0,
  cancelled_sessions: 0,
  average_progress: 0,
  last_session_date: null,
  next_session_date: null,
  video_sessions: null
})

const loadAllPatientsStats = async () => {
  if (!patientsStore.patients || patientsStore.patients.length === 0) {
    patientSessionStats.value = {}
    return
  }

  statsLoading.value = true
  try {
    await Promise.all(
      patientsStore.patients.map((patient) => loadPatientStats(patient.id))
    )
  } finally {
    statsLoading.value = false
  }
}

const loadPatientStats = async (patientId) => {
  try {
    const response = await api.get(`/patients/${patientId}/sessions/stats`)
    const stats = response.data?.stats || {}
    const mergedStats = {
      ...defaultStats(),
      ...{
        total_sessions: stats.total_sessions ?? 0,
        completed_sessions: stats.completed_sessions ?? 0,
        scheduled_sessions: stats.scheduled_sessions ?? 0,
        cancelled_sessions: stats.cancelled_sessions ?? 0,
        average_progress: stats.average_progress ?? 0,
        last_session_date: extractSessionDate(stats.last_session) ?? extractSessionDate(stats.video_sessions?.last_session),
        next_session_date: extractSessionDate(stats.upcoming_session) ?? extractSessionDate(stats.video_sessions?.next_session),
        video_sessions: stats.video_sessions || null
      }
    }
    patientSessionStats.value[patientId] = mergedStats
  } catch (error) {
    console.error('Error loading patient stats:', error)
    patientSessionStats.value[patientId] = defaultStats()
  }
}

watch(
  () => patientsStore.patients,
  async (newPatients) => {
    if (newPatients && newPatients.length > 0) {
      await loadAllPatientsStats()
    } else {
      patientSessionStats.value = {}
    }
  },
  { immediate: true }
)

// عدد جلسات المريض
const getPatientSessionsCount = (patientId) => {
  return patientSessionStats.value[patientId]?.total_sessions ?? 0
}

// تاريخ آخر جلسة
const getLastSessionDate = (patientId) => {
  const date = patientSessionStats.value[patientId]?.last_session_date
  return date ? formatSessionDate(date) : 'لا توجد'
}

// الجلسة القادمة
const getNextSession = (patientId) => {
  const date = patientSessionStats.value[patientId]?.next_session_date
  return date ? formatSessionDate(date) : null
}

// تنسيق تاريخ الجلسة
const formatSessionDate = (dateString) => {
  if (!dateString) return 'غير محدد'
  
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('ar-SA', {
      month: 'long',
      day: 'numeric'
    })
  } catch (error) {
    return dateString
  }
}

// فتح نافذة الجلسات مع تحميل البيانات
const openSessionsAndLoad = async (patient) => {
  try {
    await sessionsStore.fetchSessions(patient.id)
    await sessionsStore.fetchStats(patient.id)
  } catch (error) {
    console.error('Error loading patient sessions:', error)
  }
  emit('open-sessions', patient)
}

const extractSessionDate = (session) => {
  if (!session) return null
  if (session.date) {
    return session.date
  }
  if (session.session_date) {
    const time = session.session_time ? `${session.session_time}` : null
    return time ? `${session.session_date}T${time}` : session.session_date
  }
  return null
}

// دالة حساب العمر من تاريخ الميلاد
const calculateAge = (dateOfBirth) => {
  if (!dateOfBirth) return 'غير محدد'
  
  try {
    const birthDate = new Date(dateOfBirth)
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
}

// دالة للحصول على الصورة الافتراضية
const getPatientAvatar = (patient) => {
  if (patient.avatar) {
    return patient.avatar
  }
  
  if (patient.gender === 'female') {
    return '/images/default-female-avatar.png'
  } else {
    return '/images/default-female-avatar.png'
  }
}
</script>