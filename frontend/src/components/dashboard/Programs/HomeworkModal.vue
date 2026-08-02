<template>
  <div v-if="open" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-black/50"></div>

      <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-primary">
            {{ homework ? 'تعديل المهمة' : 'إضافة مهمة منزلية جديدة' }}
          </h3>
          <button @click="$emit('close')" class="text-secondary hover:text-primary">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- العنوان بالعربية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              العنوان بالعربية *
            </label>
            <input
              v-model="form.title_ar"
              type="text"
              required
              class="input w-full"
            />
          </div>

          <!-- العنوان بالإنجليزية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              العنوان بالإنجليزية *
            </label>
            <input
              v-model="form.title_en"
              type="text"
              required
              class="input w-full"
            />
          </div>

          <!-- الوصف -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              الوصف بالعربية
            </label>
            <textarea
              v-model="form.description_ar"
              rows="3"
              class="input w-full"
            ></textarea>
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

          <!-- نوع الإكمال -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              نوع الإكمال *
            </label>
            <select v-model="form.completion_type" required class="input w-full">
              <option value="confirmation">تأكيد فقط</option>
              <option value="text_input">إدخال نص</option>
              <option value="file_upload">رفع ملف</option>
              <option value="form">نموذج</option>
            </select>
          </div>

          <!-- ترتيب المهمة -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              ترتيب المهمة
            </label>
            <input
              v-model.number="form.homework_order"
              type="number"
              min="1"
              class="input w-full"
            />
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
import { ref, watch } from 'vue'
import { programService } from '@/services/programService'
import { XMarkIcon } from '@heroicons/vue/24/outline'

interface Homework {
  id?: string
  session_id: string
  title_ar: string
  title_en: string
  description_ar?: string
  description_en?: string
  instructions_ar?: string
  instructions_en?: string
  completion_type: string
  is_mandatory: boolean
  is_active: boolean
  homework_order: number
  completion_config?: any
}

interface Props {
  open: boolean
  homework: Homework | null
  sessionId: string
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  save: []
}>()

const saving = ref(false)
const form = ref<Homework>({
  session_id: props.sessionId,
  title_ar: '',
  title_en: '',
  description_ar: '',
  description_en: '',
  instructions_ar: '',
  instructions_en: '',
  completion_type: 'confirmation',
  is_mandatory: true,
  is_active: true,
  homework_order: 1
})

watch(() => props.homework, (newHomework) => {
  if (newHomework) {
    form.value = { ...newHomework }
  } else {
    form.value = {
      session_id: props.sessionId,
      title_ar: '',
      title_en: '',
      description_ar: '',
      description_en: '',
      instructions_ar: '',
      instructions_en: '',
      completion_type: 'confirmation',
      is_mandatory: true,
      is_active: true,
      homework_order: 1
    }
  }
}, { immediate: true })

const handleSubmit = async () => {
  saving.value = true
  try {
    if (props.homework) {
      await programService.updateHomework(props.sessionId, props.homework.id!, form.value)
    } else {
      await programService.createHomework(props.sessionId, form.value)
    }
    emit('save')
  } catch (error) {
    console.error('Error saving homework:', error)
  } finally {
    saving.value = false
  }
}
</script>




