<template>
  <div class="space-y-6">
    <!-- Program Header -->
    <div class="card p-6">
      <div class="flex items-start gap-4">
        <img
          v-if="program.image_url"
          :src="program.image_url"
          :alt="program.name_ar"
          class="w-24 h-24 rounded-lg object-cover"
        />
        <div class="flex-1">
          <h2 class="text-2xl font-bold text-primary mb-2">{{ program.name_ar }}</h2>
          <p class="text-secondary mb-4">{{ program.description_ar }}</p>
          <div class="flex items-center gap-4 text-sm">
            <span class="badge" :class="statusBadgeClass">
              {{ statusLabel }}
            </span>
            <span class="text-secondary">المدة: {{ program.duration }} ساعة</span>
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
        </div>
        <button @click="$emit('edit')" class="btn btn-primary">تعديل البرنامج</button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
      <nav class="flex gap-4">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-4 py-2 font-medium border-b-2 transition-colors',
            activeTab === tab.id
              ? 'border-primary text-primary'
              : 'border-transparent text-secondary hover:text-primary'
          ]"
        >
          {{ tab.label }}
        </button>
      </nav>
    </div>

    <!-- Tab Content -->
    <div class="mt-6">
      <!-- Phases Tab -->
      <PhasesManagement
        v-if="activeTab === 'phases'"
        :program-id="program.id"
        ref="phasesRef"
      />

      <!-- Sessions Tab -->
      <SessionsManagement
        v-if="activeTab === 'sessions'"
        :program-id="program.id"
        :phases="phases"
        ref="sessionsRef"
      />

      <!-- Assessments Tab -->
      <AssessmentsManagement
        v-if="activeTab === 'assessments'"
        :program-id="program.id"
        ref="assessmentsRef"
      />

      <!-- Users Tab -->
      <ProgramUsers
        v-if="activeTab === 'users'"
        :program-id="program.id"
      />

      <!-- Statistics Tab -->
      <ProgramStatistics
        v-if="activeTab === 'statistics'"
        :program-id="program.id"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import PhasesManagement from './PhasesManagement.vue'
import SessionsManagement from './SessionsManagement.vue'
import AssessmentsManagement from './AssessmentsManagement.vue'
import ProgramStatistics from './ProgramStatistics.vue'
import ProgramUsers from './ProgramUsers.vue'

interface Program {
  id: string
  name_ar: string
  description_ar?: string
  duration: number
  max_duration_days?: number
  session_duration_minutes?: number
  session_gap_hours?: number
  activity_gap_hours?: number
  status: 'active' | 'inactive' | 'draft'
  image_url?: string
  scale?: {
    name_ar: string
  }
}

interface Props {
  program: Program
  phases?: any[]
}

const props = defineProps<Props>()
defineEmits<{
  edit: []
}>()

const activeTab = ref('phases')
const phasesRef = ref()
const sessionsRef = ref()
const assessmentsRef = ref()

const tabs = [
  { id: 'phases', label: 'المراحل' },
  { id: 'sessions', label: 'الجلسات' },
  { id: 'assessments', label: 'التقييمات' },
  { id: 'users', label: 'المشتركين' },
  { id: 'statistics', label: 'الإحصائيات' }
]

const statusLabel = computed(() => {
  const labels = {
    active: 'نشط',
    inactive: 'غير نشط',
    draft: 'مسودة'
  }
  return labels[props.program.status] || props.program.status
})

const statusBadgeClass = computed(() => {
  const classes = {
    active: 'badge-success',
    inactive: 'badge-secondary',
    draft: 'badge-warning'
  }
  return classes[props.program.status] || 'badge-secondary'
})
</script>




