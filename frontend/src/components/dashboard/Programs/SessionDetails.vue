<template>
  <div class="space-y-6">
    <!-- Session Header -->
    <div class="card p-6">
      <div class="flex items-start justify-between">
        <div>
          <h2 class="text-2xl font-bold text-primary mb-2">{{ session.title_ar }}</h2>
          <p class="text-secondary mb-4">{{ session.goal_ar || 'لا يوجد هدف محدد' }}</p>
          <div class="flex items-center gap-4 text-sm">
            <span class="text-secondary">الترتيب: {{ session.session_order }}</span>
            <span v-if="session.duration" class="text-secondary">المدة: {{ session.duration }} دقيقة</span>
            <span v-if="session.phase" class="text-secondary">المرحلة: {{ session.phase.name_ar }}</span>
          </div>
        </div>
        <button @click="$emit('edit')" class="btn btn-primary">تعديل الجلسة</button>
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
      <!-- Activities Tab -->
      <ActivitiesManagement
        v-if="activeTab === 'activities'"
        :session-id="session.id"
        ref="activitiesRef"
      />

      <!-- Homework Tab -->
      <HomeworkManagement
        v-if="activeTab === 'homework'"
        :session-id="session.id"
        ref="homeworkRef"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ActivitiesManagement from './ActivitiesManagement.vue'
import HomeworkManagement from './HomeworkManagement.vue'

interface Session {
  id: string
  title_ar: string
  goal_ar?: string
  session_order: number
  duration?: number
  phase?: {
    name_ar: string
  }
}

interface Props {
  session: Session
}

defineProps<Props>()
defineEmits<{
  edit: []
}>()

const activeTab = ref('activities')
const activitiesRef = ref()
const homeworkRef = ref()

const tabs = [
  { id: 'activities', label: 'الأنشطة' },
  { id: 'homework', label: 'المهام المنزلية' }
]
</script>




