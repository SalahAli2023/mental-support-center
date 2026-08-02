<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-6xl max-h-[90vh] overflow-y-auto bg-primary rounded-2xl shadow-xl border border-primary">
      <!-- رأس نافذة الجلسات -->
      <div class="flex items-center justify-between p-6 border-b border-primary">
        <div class="flex items-center gap-4">
          <img 
            :src="patient?.avatar" 
            :alt="patient?.name"
            class="w-12 h-12 rounded-full border-2 border-brand-500"
          />
          <div>
            <h2 class="text-xl font-bold text-primary">جلسات {{ patient?.name }}</h2>
            <p class="text-secondary">إدارة الجلسات العلاجية للمريض</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <button 
            @click="$emit('add-session')"
            class="bg-brand-500 hover:bg-[#8FAE2F] text-white px-4 py-2 rounded-lg flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            إضافة جلسة
          </button>
          <button @click="$emit('close')" class="p-2 hover:bg-tertiary rounded-lg text-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- محتوى نافذة الجلسات -->
      <div class="p-6">
        <!-- إحصائيات الجلسات -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-secondary rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-brand-500">{{ sessions.length }}</div>
            <div class="text-sm text-secondary">إجمالي الجلسات</div>
          </div>
          <div class="bg-secondary rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-500">
              {{ completedSessions }}
            </div>
            <div class="text-sm text-secondary">مكتملة</div>
          </div>
          <div class="bg-secondary rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-500">
              {{ scheduledSessions }}
            </div>
            <div class="text-sm text-secondary">مجدولة</div>
          </div>
          <div class="bg-secondary rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-orange-500">
              {{ cancelledSessions }}
            </div>
            <div class="text-sm text-secondary">ملغاة</div>
          </div>
        </div>

        <!-- قائمة الجلسات -->
        <div class="space-y-4">
          <div 
            v-for="session in sortedSessions" 
            :key="session.id"
            class="bg-secondary rounded-lg p-4 border border-primary hover:bg-tertiary transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div :class="[
                  'w-3 h-3 rounded-full',
                  session.status === 'completed' ? 'bg-green-500' :
                  session.status === 'scheduled' ? 'bg-blue-500' :
                  'bg-orange-500'
                ]"></div>
                <div>
                  <h4 class="font-semibold text-primary">{{ session.title_ar || session.title }}</h4>
                  <p class="text-sm text-secondary">{{ session.session_date }} - {{ session.session_time }}</p>
                  <p class="text-xs text-secondary">المعالج: {{ session.therapist?.name_ar || session.therapist?.name || 'غير محدد' }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  session.status === 'completed' ? 'bg-green-100 text-green-800' :
                  session.status === 'scheduled' ? 'bg-blue-100 text-blue-800' :
                  'bg-orange-100 text-orange-800'
                ]">
                  {{ sessionStatusText(session.status) }}
                </span>
                <button 
                  @click="$emit('edit-session', session)"
                  class="p-1 hover:bg-primary rounded text-primary"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button 
                  @click="$emit('delete-session', session)"
                  class="p-1 hover:bg-primary rounded text-red-500"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
            <div class="mt-3 text-sm text-primary">
              <p>{{ session.notes_ar || session.notes }}</p>
            </div>
            <div v-if="session.progress" class="mt-2">
              <div class="flex justify-between text-xs text-secondary mb-1">
                <span>التقدم</span>
                <span>{{ session.progress }}%</span>
              </div>
              <div class="w-full bg-primary rounded-full h-2">
                <div 
                  class="bg-brand-500 h-2 rounded-full" 
                  :style="{ width: session.progress + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- لا توجد جلسات -->
        <div v-if="sessions.length === 0" class="text-center py-8">
          <div class="text-secondary mb-4">
            <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-primary mb-2">لا توجد جلسات</h3>
          <p class="text-secondary mb-4">لم يتم إضافة أي جلسات لهذا المريض بعد</p>
          <button 
            @click="$emit('add-session')"
            class="bg-brand-500 hover:bg-[#8FAE2F] text-white px-4 py-2 rounded-lg"
          >
            إضافة أول جلسة
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePatientSessionsStore } from '@/stores/patientSessions'

const sessionsStore = usePatientSessionsStore()

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  patient: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'add-session', 'edit-session', 'delete-session'])

// استخدام الجلسات من الـ store
const sessions = computed(() => sessionsStore.sessions)

// إحصائيات الجلسات من الـ store
const completedSessions = computed(() => 
  sessionsStore.stats?.completed_sessions || 0
)

const scheduledSessions = computed(() => 
  sessionsStore.stats?.scheduled_sessions || 0
)

const cancelledSessions = computed(() => 
  sessionsStore.stats?.cancelled_sessions || 0
)

// ترتيب الجلسات
const sortedSessions = computed(() => {
  return [...sessions.value].sort((a, b) => {
    const statusOrder = { scheduled: 1, completed: 2, cancelled: 3 }
    return statusOrder[a.status] - statusOrder[b.status] || new Date(b.session_date) - new Date(a.session_date)
  })
})

const sessionStatusText = (status) => {
  const statusMap = {
    completed: 'مكتملة',
    scheduled: 'مجدولة',
    cancelled: 'ملغاة'
  }
  return statusMap[status] || status
}
</script>