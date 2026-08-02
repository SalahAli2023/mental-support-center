<template>
  <div
    class="border rounded-lg p-4 cursor-pointer hover:border-primary transition-colors"
    :class="{
      'border-gray-300': !isUnlocked,
      'border-primary': isUnlocked && !isCompleted,
      'border-green-500 bg-green-50': isCompleted,
      'opacity-50': !isUnlocked
    }"
    @click="handleClick"
  >
    <div class="flex items-center justify-between">
      <!-- يسار الكارد -->
      <div class="flex items-center gap-4">
        <!-- رقم المرحلة -->
        <div
          class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg"
          :class="{
            'bg-gray-200 text-gray-500': !isUnlocked,
            'bg-primary/10 text-primary': isUnlocked && !isCompleted,
            'bg-green-500 text-white': isCompleted
          }"
        >
          {{ phase.phase_order }}
        </div>

        <!-- الاسم وعدد الجلسات -->
        <div>
          <h3 class="font-semibold text-primary">
            {{ phaseName }}
          </h3>
          <p class="text-sm text-secondary">
            {{ phase.sessions?.length || 0 }} جلسة
          </p>
        </div>
      </div>

      <!-- يمين الكارد -->
      <div class="flex items-center gap-2">
        <span v-if="!isUnlocked" class="badge badge-secondary">
          مقفل
        </span>

        <span v-else-if="isCompleted" class="badge badge-success">
          مكتمل
        </span>

        <span v-else class="badge badge-primary">
          متاح
        </span>

        <ChevronLeftIcon class="w-5 h-5 text-secondary" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { ChevronLeftIcon } from '@heroicons/vue/24/outline'

/* ===== Types ===== */
interface Phase {
  id: string
  phase_order: number
  name_ar?: string
  name?: string
  sessions?: any[]
}

interface Props {
  phase: Phase
  userProgress?: any
  isEnrolled?: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  click: []
}>()

/* ===== الاسم (حل المشكلة) ===== */
const phaseName = computed(() => {
  return props.phase.name_ar || props.phase.name || 'بدون اسم'
})

/* ===== حالة الفتح ===== */
const isUnlocked = computed(() => {
  // المرحلة الأولى متاحة إذا كان المستخدم مسجل
  if (props.phase.phase_order === 1) {
    return props.isEnrolled || !!props.userProgress
  }

  // المراحل الأخرى
  if (!props.isEnrolled && !props.userProgress) {
    return false
  }

  return true
})

/* ===== حالة الاكتمال ===== */
const isCompleted = computed(() => {
  if (!props.userProgress) return false

  // منطق الاكتمال (عدّليه حسب بياناتك)
  return false
})

/* ===== التعامل مع النقر ===== */
const handleClick = () => {
  if (!props.isEnrolled && !props.userProgress) {
    alert('يجب التسجيل في البرنامج أولاً')
    return
  }

  if (!isUnlocked.value) {
    alert('المرحلة مقفلة. يجب إكمال المراحل السابقة')
    return
  }

  emit('click')
}
</script>
