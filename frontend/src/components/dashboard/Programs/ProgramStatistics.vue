<template>
  <div class="space-y-6">
    <div v-if="loading" class="text-center py-8">
      <div class="text-secondary">جاري التحميل...</div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Total Enrollments -->
      <div class="card p-4">
        <div class="text-sm text-secondary mb-1">إجمالي المشتركين</div>
        <div class="text-2xl font-bold text-primary">{{ statistics.total_enrollments || 0 }}</div>
      </div>

      <!-- Active Users -->
      <div class="card p-4">
        <div class="text-sm text-secondary mb-1">المستخدمون النشطون</div>
        <div class="text-2xl font-bold text-primary">{{ statistics.active_users || 0 }}</div>
      </div>

      <!-- Completion Rate -->
      <div class="card p-4">
        <div class="text-sm text-secondary mb-1">نسبة الإكمال</div>
        <div class="text-2xl font-bold text-primary">{{ statistics.completion_rate || 0 }}%</div>
      </div>

      <!-- Average Progress -->
      <div class="card p-4">
        <div class="text-sm text-secondary mb-1">متوسط التقدم</div>
        <div class="text-2xl font-bold text-primary">{{ statistics.average_progress || 0 }}%</div>
      </div>
    </div>

    <!-- Progress Chart -->
    <div class="card p-6">
      <h3 class="text-lg font-semibold text-primary mb-4">التقدم حسب المراحل</h3>
      <div class="space-y-4">
        <div
          v-for="phase in phaseProgress"
          :key="phase.id"
          class="space-y-2"
        >
          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-primary">{{ phase.name }}</span>
            <span class="text-sm text-secondary">{{ phase.completion_rate }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-primary h-2 rounded-full transition-all"
              :style="{ width: `${phase.completion_rate}%` }"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { programService } from '@/services/programService'

interface Props {
  programId: string
}

const props = defineProps<Props>()

const loading = ref(false)
const statistics = ref<any>({})
const phaseProgress = ref<any[]>([])

const fetchStatistics = async () => {
  loading.value = true
  try {
    // يمكن إضافة endpoint للإحصائيات لاحقاً
    // const response = await programService.getProgramStatistics(props.programId)
    // statistics.value = response.data
    
    // بيانات تجريبية
    statistics.value = {
      total_enrollments: 0,
      active_users: 0,
      completion_rate: 0,
      average_progress: 0
    }
    phaseProgress.value = []
  } catch (error) {
    console.error('Error fetching statistics:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchStatistics()
})
</script>




