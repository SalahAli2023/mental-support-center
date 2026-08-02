<template>
  <div class="space-y-4 p-3 sm:p-4">
    <!-- عنوان + زر -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h1 class="text-lg font-semibold text-primary sm:text-2xl">{{ pageTitle }}</h1>
      <Button
        v-if="isAdmin"
        variant="primary"
        class="w-full sm:w-auto"
        @click="openCreateModal"
      >
        إنشاء جلسة جديدة
      </Button>
    </div>

    <!-- الفلاتر -->
    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
      <select
        v-model="statusFilter"
        class="w-full sm:w-48 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
      >
        <option value="">جميع الحالات</option>
        <option value="scheduled">مجدولة</option>
        <option value="active">نشطة</option>
        <option value="ended">منتهية</option>
        <option value="cancelled">ملغاة</option>
      </select>

      <input
        v-model="searchQuery"
        placeholder="البحث..."
        class="w-full sm:w-48 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
      />
    </div>

    <!-- جدول الجلسات -->
    <Card>
      <template #header>قائمة الجلسات</template>
      
      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center text-secondary">
        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
        <p>جاري التحميل...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredSessions.length === 0" class="p-8 text-center text-secondary">
        <i class="fas fa-video-slash text-4xl mb-2"></i>
        <p>لا توجد جلسات</p>
      </div>

      <!-- Mobile View - Cards -->
      <div v-else class="block sm:hidden space-y-3">
        <div
          v-for="session in filteredSessions"
          :key="session.id"
          class="border border-primary rounded-lg p-4 hover:bg-secondary transition-colors"
        >
          <div class="flex justify-between items-start mb-3">
            <div>
              <h3 class="font-semibold text-base">
                {{ session.appointment?.client?.name || 'غير محدد' }}
              </h3>
              <p class="text-sm text-secondary mt-1">
                {{ formatDate(session.appointment?.starts_at) }}
              </p>
            </div>
            <span
              :class="getStatusClass(session.status)"
              class="px-2 py-1 rounded-full text-xs font-medium"
            >
              {{ getStatusLabel(session.status) }}
            </span>
          </div>

          <div class="space-y-2 text-sm">
            <div class="flex items-center gap-2">
              <i class="fas fa-user-md text-primary w-5"></i>
              <span>{{ session.appointment?.therapist?.name_ar || session.appointment?.therapist?.name_en || 'غير محدد' }}</span>
            </div>
            
            <div class="flex items-center gap-2">
              <i class="fas fa-clock text-primary w-5"></i>
              <span>البدء: {{ session.start_time ? formatDateTime(session.start_time) : '-' }}</span>
            </div>
            
            <div class="flex items-center gap-2">
              <i class="fas fa-flag-checkered text-primary w-5"></i>
              <span>الانتهاء: {{ session.end_time ? formatDateTime(session.end_time) : '-' }}</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-between items-center mt-4 pt-3 border-t border-primary">
            <div class="flex gap-2">
              <button
                v-if="session.status === 'scheduled' && isAdmin"
                @click="startSession(session.id)"
                class="text-green-500 hover:text-green-600 p-2 rounded-lg hover:bg-green-50"
                title="بدء الجلسة"
              >
                <i class="fas fa-play"></i>
              </button>
              <button
                v-if="session.status === 'active'"
                @click="endSession(session.id)"
                class="text-red-500 hover:text-red-600 p-2 rounded-lg hover:bg-red-50"
                title="إنهاء الجلسة"
              >
                <i class="fas fa-stop"></i>
              </button>
            </div>
            <button
              @click="viewSession(session.id)"
              class="text-primary hover:text-secondary flex items-center gap-1"
              title="عرض التفاصيل"
            >
              <span class="text-sm">تفاصيل</span>
              <i class="fas fa-chevron-left text-xs"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Desktop View - Table -->
      <div class="hidden sm:block overflow-x-auto -mx-1 sm:mx-0">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-start text-secondary border-b border-primary">
              <th class="px-3 py-3 text-start">الموعد</th>
              <th class="px-3 py-3 text-start">العميل</th>
              <th class="px-3 py-3 text-start">المعالج</th>
              <th class="px-3 py-3 text-start">الحالة</th>
              <th class="px-3 py-3 text-start">وقت البدء</th>
              <th class="px-3 py-3 text-start">وقت الانتهاء</th>
              <th class="px-3 py-3 text-start">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="session in filteredSessions"
              :key="session.id"
              class="border-b border-primary hover:bg-secondary transition-colors"
            >
              <td class="px-3 py-3">
                {{ formatDate(session.appointment?.starts_at) }}
              </td>
              <td class="px-3 py-3">
                {{ session.appointment?.client?.name || 'غير محدد' }}
              </td>
              <td class="px-3 py-3">
                {{ session.appointment?.therapist?.name_ar || session.appointment?.therapist?.name_en || 'غير محدد' }}
              </td>
              <td class="px-3 py-3">
                <span
                  :class="getStatusClass(session.status)"
                  class="px-2 py-1 rounded-full text-xs font-medium"
                >
                  {{ getStatusLabel(session.status) }}
                </span>
              </td>
              <td class="px-3 py-3">
                {{ session.start_time ? formatDateTime(session.start_time) : '-' }}
              </td>
              <td class="px-3 py-3">
                {{ session.end_time ? formatDateTime(session.end_time) : '-' }}
              </td>
              <td class="px-3 py-3">
                <div class="flex items-center gap-2">
                  <button
                    v-if="session.status === 'scheduled' && isAdmin"
                    @click="startSession(session.id)"
                    class="text-green-500 hover:text-green-600"
                    title="بدء الجلسة"
                  >
                    <i class="fas fa-play"></i>
                  </button>
                  <button
                    v-if="session.status === 'active'"
                    @click="endSession(session.id)"
                    class="text-red-500 hover:text-red-600"
                    title="إنهاء الجلسة"
                  >
                    <i class="fas fa-stop"></i>
                  </button>
                  <button
                    @click="viewSession(session.id)"
                    class="text-primary hover:text-secondary"
                    title="عرض التفاصيل"
                  >
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="mt-4 flex justify-center">
        <div class="flex gap-2">
          <button
            @click="currentPage > 1 && fetchSessions(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 rounded border border-primary disabled:opacity-50"
          >
            السابق
          </button>
          <span class="px-3 py-1">{{ currentPage }} / {{ totalPages }}</span>
          <button
            @click="currentPage < totalPages && fetchSessions(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 rounded border border-primary disabled:opacity-50"
          >
            التالي
          </button>
        </div>
      </div>
    </Card>

    <!-- Create Session Modal -->
    <div
      v-if="showCreateModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="showCreateModal = false"
    >
      <div class="bg-primary rounded-lg p-6 w-full max-w-md mx-4">
        <h2 class="text-xl font-bold mb-4">إنشاء جلسة جديدة</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">الموعد</label>
            <div v-if="!availableAppointments.length" class="text-sm text-secondary bg-secondary/40 p-3 rounded-lg">
              لا توجد مواعيد جاهزة لإنشاء جلسة حالياً. تأكد من وجود حجوزات (بانتظار التأكيد أو مؤكدة).
            </div>
            <select
              v-else
              v-model="selectedAppointmentId"
              class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
            >
              <option value="">اختر موعد</option>
              <option
                v-for="appointment in availableAppointments"
                :key="appointment.id"
                :value="appointment.id"
              >
                {{ getAppointmentDisplay(appointment) }}
              </option>
            </select>
            <div v-if="selectedAppointment" class="mt-3 p-3 rounded-lg bg-secondary/40 text-sm space-y-1">
              <div>
                <span class="font-semibold">العميل:</span>
                {{ selectedAppointment.appointment?.client?.name || selectedAppointment.client?.name || `#${selectedAppointment.client_id}` }}
              </div>
              <div>
                <span class="font-semibold">المعالج:</span>
                {{ getTherapistName(selectedAppointment.therapist || selectedAppointment.appointment?.therapist) }}
              </div>
              <div>
                <span class="font-semibold">التاريخ:</span>
                {{ formatDate(selectedAppointment.starts_at) }}
              </div>
              <div>
                <span class="font-semibold">الحالة:</span>
                {{ getAppointmentStatusLabel(selectedAppointment.status) }}
              </div>
            </div>
          </div>
          <div class="flex gap-2 justify-end">
            <button
              @click="showCreateModal = false"
              class="px-4 py-2 rounded border border-primary hover:bg-secondary"
            >
              إلغاء
            </button>
            <button
              @click="createSession"
              :disabled="!selectedAppointmentId || !availableAppointments.length"
              class="px-4 py-2 rounded bg-brand-500 text-white hover:bg-brand-600 disabled:opacity-50"
            >
              إنشاء
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import api from '@/utils/api'
import Card from '@/components/dashboard/component/ui/Card.vue'
import Button from '@/components/dashboard/component/ui/Button.vue'

const router = useRouter()
const { t } = useI18n()
const authStore = useAuthStore()

const sessions = ref([])
const loading = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const statusFilter = ref('')
const searchQuery = ref('')
const showCreateModal = ref(false)
const selectedAppointmentId = ref('')
const confirmedAppointments = ref([])
const initialized = ref(false)

const isAdmin = computed(() => authStore.user?.role === 'Admin')
const props = defineProps({
  filter: {
    type: String,
    default: ''
  }
})

const pageTitle = computed(() => {
  if (props.filter === 'active') return t('nav.activeSessions')
  if (props.filter === 'history') return t('nav.sessionHistory')
  return t('nav.sessions')
})

const applyRouteFilter = () => {
  if (props.filter === 'active') {
    statusFilter.value = 'active'
  } else if (props.filter === 'history') {
    statusFilter.value = ''
  } else {
    statusFilter.value = ''
  }
}

const filteredSessions = computed(() => {
  let filtered = sessions.value

  if (props.filter === 'active') {
    filtered = filtered.filter(s => s.status === 'active')
  } else if (props.filter === 'history') {
    filtered = filtered.filter(s => ['ended', 'cancelled'].includes(s.status))
  }

  if (statusFilter.value) {
    filtered = filtered.filter(s => s.status === statusFilter.value)
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(s => {
      const clientName = s.appointment?.client?.name?.toLowerCase() || ''
      const therapistName = (s.appointment?.therapist?.name_ar || s.appointment?.therapist?.name_en || '').toLowerCase()
      return clientName.includes(query) || therapistName.includes(query)
    })
  }

  return filtered
})

const fetchSessions = async (page = 1) => {
  try {
    loading.value = true
    const params = {
      page,
      per_page: 15
    }
    if (statusFilter.value) {
      params.status = statusFilter.value
    } else if (props.filter === 'active') {
      params.status = 'active'
    }

    const response = await api.get('/sessions', { params })
    
    if (response.data?.data) {
      sessions.value = response.data.data
      currentPage.value = response.data.meta?.current_page || 1
      totalPages.value = response.data.meta?.last_page || 1
    }
  } catch (error) {
    console.error('Error fetching sessions:', error)
  } finally {
    loading.value = false
  }
}

const availableAppointments = computed(() => {
  return confirmedAppointments.value
    .filter(app => ['pending', 'confirmed'].includes(app.status))
    .sort((a, b) => new Date(a.starts_at) - new Date(b.starts_at))
})

const selectedAppointment = computed(() =>
  availableAppointments.value.find(app => String(app.id) === String(selectedAppointmentId.value))
)

const fetchConfirmedAppointments = async () => {
  try {
    const response = await api.get('/appointments', {
      params: { per_page: 200 }
    })
    if (response.data?.data) {
      confirmedAppointments.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching appointments:', error)
  }
}

const openCreateModal = async () => {
  selectedAppointmentId.value = ''
  await fetchConfirmedAppointments()
  showCreateModal.value = true
}

const createSession = async () => {
  try {
    const response = await api.post('/sessions', {
      appointment_id: selectedAppointmentId.value
    })
    
    if (response.data) {
      showCreateModal.value = false
      selectedAppointmentId.value = ''
      await fetchSessions()
    }
  } catch (error) {
    console.error('Error creating session:', error)
    alert('فشل إنشاء الجلسة: ' + (error.response?.data?.message || error.message))
  }
}

const startSession = async (sessionId) => {
  try {
    const response = await api.post(`/sessions/${sessionId}/start`)
    if (response.data) {
      alert('تم بدء الجلسة بنجاح')
      await fetchSessions()
    }
  } catch (error) {
    console.error('Error starting session:', error)
    const errorMessage = error.response?.data?.message || error.message || 'فشل بدء الجلسة'
    alert(`فشل بدء الجلسة: ${errorMessage}`)
  }
}

const endSession = async (sessionId) => {
  if (!confirm('هل أنت متأكد من إنهاء الجلسة؟')) return
  
  try {
    const response = await api.post(`/sessions/${sessionId}/end`)
    if (response.data) {
      alert('تم إنهاء الجلسة بنجاح')
      await fetchSessions()
    }
  } catch (error) {
    console.error('Error ending session:', error)
    const errorMessage = error.response?.data?.message || error.message || 'فشل إنهاء الجلسة'
    alert(`فشل إنهاء الجلسة: ${errorMessage}`)
  }
}

const viewSession = (sessionId) => {
  // يمكن إضافة modal لعرض التفاصيل
  console.log('View session:', sessionId)
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('ar-SA', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatDateTime = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleString('ar-SA', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusLabel = (status) => {
  const labels = {
    scheduled: 'مجدولة',
    active: 'نشطة',
    ended: 'منتهية',
    cancelled: 'ملغاة'
  }
  return labels[status] || status
}

const getStatusClass = (status) => {
  const classes = {
    scheduled: 'bg-yellow-100 text-yellow-800',
    active: 'bg-green-100 text-green-800',
    ended: 'bg-gray-100 text-gray-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getAppointmentStatusLabel = (status) => {
  const map = {
    pending: t('appointments.pending'),
    confirmed: t('appointments.confirmed'),
    completed: t('appointments.completed'),
    cancelled: t('appointments.cancelled')
  }
  return map[status] || status
}

const getAppointmentDisplay = (appointment) => {
  if (!appointment) return ''
  const clientName = appointment.client?.name || `#${appointment.client_id}`
  const therapistName = getTherapistName(appointment.therapist || appointment.appointment?.therapist)
  return `${clientName} | ${formatDate(appointment.starts_at)} | ${therapistName}`
}

const getTherapistName = (therapist) => {
  if (!therapist) return 'غير محدد'
  const locale = localStorage.getItem('locale') || 'ar'
  const nameAr = therapist.name_ar || therapist?.user?.name
  const nameEn = therapist.name_en || therapist?.user?.name
  return locale === 'ar'
    ? (nameAr || nameEn || 'غير محدد')
    : (nameEn || nameAr || 'غير محدد')
}

onMounted(async () => {
  applyRouteFilter()
  await fetchSessions()
  initialized.value = true
})

watch(() => props.filter, () => {
  applyRouteFilter()
  fetchSessions()
})

watch(statusFilter, () => {
  if (!initialized.value) return
  fetchSessions()
})
</script>