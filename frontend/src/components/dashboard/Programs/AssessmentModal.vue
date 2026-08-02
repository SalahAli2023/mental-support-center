<template>
  <div v-if="open" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-black/50" "></div>

      <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-primary">
            {{ assessment ? 'تعديل التقييم' : 'إضافة تقييم جديد' }}
          </h3>
          <button @click="$emit('close')" class="text-secondary hover:text-primary">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- المقياس -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              المقياس النفسي *
            </label>
            <select v-model="form.scale_id" required class="input w-full">
              <option value="">اختر المقياس</option>
              <option v-for="scale in scales" :key="scale.id" :value="scale.id">
                {{ scale.name_ar }}
              </option>
            </select>
          </div>

          <!-- نوع التقييم -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              نوع التقييم *
            </label>
            <select v-model="form.assessment_type" required class="input w-full">
              <option value="pre">تقييم قبلي</option>
              <option value="post">تقييم بعدي</option>
            </select>
          </div>

          <!-- التعليمات -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              التعليمات بالعربية
            </label>
            <textarea
              v-model="form.instructions_ar"
              rows="3"
              class="input w-full"
            ></textarea>
          </div>

          <!-- الحالة -->
          <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="form.is_mandatory"
                type="checkbox"
                class="checkbox"
              />
              <span class="text-sm text-primary">إلزامي</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="checkbox"
              />
              <span class="text-sm text-primary">نشط</span>
            </label>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t">
            <button type="button" @click="$emit('close')" class="btn btn-ghost">
              إلغاء
            </button>
            <button type="submit" :disabled="saving" class="btn btn-primary">
              {{ saving ? 'جاري الحفظ...' : 'حفظ' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { programService } from '@/services/programService'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import api from '@/services/api'

interface Assessment {
  id?: string
  program_id: string
  scale_id: string
  assessment_type: 'pre' | 'post'
  is_mandatory: boolean
  is_active: boolean
  instructions_ar?: string
  instructions_en?: string
}

interface Props {
  open: boolean
  assessment: Assessment | null
  programId: string
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  save: []
}>()

const saving = ref(false)
const scales = ref<any[]>([])
const form = ref<Assessment>({
  program_id: props.programId,
  scale_id: '',
  assessment_type: 'pre',
  is_mandatory: true,
  is_active: true,
  instructions_ar: '',
  instructions_en: ''
})

const fetchScales = async () => {
  try {
    const response = await api.get('/api/psychological-scales')
    if (response.data.success) {
      scales.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching scales:', error)
  }
}

watch(() => props.assessment, (newAssessment) => {
  if (newAssessment) {
    form.value = { ...newAssessment }
  } else {
    form.value = {
      program_id: props.programId,
      scale_id: '',
      assessment_type: 'pre',
      is_mandatory: true,
      is_active: true,
      instructions_ar: '',
      instructions_en: ''
    }
  }
}, { immediate: true })

const handleSubmit = async () => {
  saving.value = true
  try {
    if (props.assessment) {
      await programService.updateAssessment(props.programId, props.assessment.id!, form.value)
    } else {
      await programService.createAssessment(props.programId, form.value)
    }
    emit('save')
  } catch (error) {
    console.error('Error saving assessment:', error)
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchScales()
})
</script>




