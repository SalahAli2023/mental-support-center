<template>
  <div
    class="card p-6 cursor-pointer hover:shadow-lg transition-shadow"
    @click="$emit('click')"
  >
    <div class="space-y-4">
      <!-- Image -->
      <div v-if="program.image_url" class="aspect-video rounded-lg overflow-hidden">
        <img
          :src="program.image_url"
          :alt="program.name_ar"
          class="w-full h-full object-cover"
        />
      </div>

      <!-- Content -->
      <div>
        <h3 class="text-xl font-semibold text-primary mb-2">{{ program.name_ar }}</h3>
        <p class="text-secondary text-sm line-clamp-2 mb-4">
          {{ program.description_ar || 'لا يوجد وصف' }}
        </p>

        <!-- Meta -->
        <div class="flex items-center justify-between text-sm">
          <span class="text-secondary">
            {{ program.duration }} ساعة
          </span>
          <span class="badge badge-success">نشط</span>
        </div>

        <!-- Progress (if enrolled) -->
        <div v-if="userProgress" class="mt-4">
          <div class="flex items-center justify-between text-sm mb-2">
            <span class="text-secondary">التقدم</span>
            <span class="font-semibold text-primary">{{ userProgress.progress_percentage }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-primary h-2 rounded-full transition-all"
              :style="{ width: `${userProgress.progress_percentage}%` }"
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

interface Program {
  id: string
  name_ar: string
  description_ar?: string
  duration: number
  image_url?: string
  status: string
}

interface Props {
  program: Program
}

const props = defineProps<Props>()
defineEmits<{
  click: []
}>()

const userProgress = ref<any>(null)

const fetchUserProgress = async () => {
  try {
    const response = await programService.getUserProgress(props.program.id)
    if (response.data.success) {
      userProgress.value = response.data.data.user_program
    }
  } catch (error) {
    // User not enrolled
    userProgress.value = null
  }
}

onMounted(() => {
  fetchUserProgress()
})
</script>




