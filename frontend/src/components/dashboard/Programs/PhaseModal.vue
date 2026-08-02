<template>
  <div v-if="open" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-black/50"></div>

      <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-primary">
            {{ phase ? 'تعديل المرحلة' : 'إضافة مرحلة جديدة' }}
          </h3>
          <button @click="$emit('close')" class="text-secondary hover:text-primary">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- الاسم بالعربية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              الاسم بالعربية *
            </label>
            <input
              v-model="form.name_ar"
              type="text"
              required
              class="input w-full"
              placeholder="اسم المرحلة بالعربية"
            />
          </div>

          <!-- الاسم بالإنجليزية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              الاسم بالإنجليزية *
            </label>
            <input
              v-model="form.name_en"
              type="text"
              required
              class="input w-full"
              placeholder="Phase name in English"
            />
          </div>

          <!-- الوصف بالعربية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              الوصف بالعربية
            </label>
            <textarea
              v-model="form.description_ar"
              rows="3"
              class="input w-full"
              placeholder="وصف المرحلة بالعربية"
            ></textarea>
          </div>

          <!-- الوصف بالإنجليزية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              الوصف بالإنجليزية
            </label>
            <textarea
              v-model="form.description_en"
              rows="3"
              class="input w-full"
              placeholder="Phase description in English"
            ></textarea>
          </div>

          <!-- ترتيب المرحلة -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              ترتيب المرحلة
            </label>
            <input
              v-model.number="form.phase_order"
              type="number"
              min="1"
              class="input w-full"
            />
          </div>

          <!-- الحالة -->
          <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="form.is_active"
                type="checkbox"
                class="checkbox"
              />
              <span class="text-sm text-primary">نشط</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="form.is_hidden"
                type="checkbox"
                class="checkbox"
              />
              <span class="text-sm text-primary">مخفي</span>
            </label>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t">
            <button
              type="button"
              @click="$emit('close')"
              class="btn btn-ghost"
            >
              إلغاء
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="btn btn-primary"
            >
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

interface Phase {
  id?: string
  name_ar: string
  name_en: string
  description_ar?: string
  description_en?: string
  phase_order: number
  is_active: boolean
  is_hidden: boolean
}

interface Props {
  open: boolean
  phase: Phase | null
  programId: string
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  save: []
}>()

const saving = ref(false)
const form = ref<Phase>({
  name_ar: '',
  name_en: '',
  description_ar: '',
  description_en: '',
  phase_order: 1,
  is_active: true,
  is_hidden: false
})

watch(() => props.phase, (newPhase) => {
  if (newPhase) {
    form.value = { ...newPhase }
  } else {
    form.value = {
      name_ar: '',
      name_en: '',
      description_ar: '',
      description_en: '',
      phase_order: 1,
      is_active: true,
      is_hidden: false
    }
  }
}, { immediate: true })

const handleSubmit = async () => {
  saving.value = true
  try {
    if (props.phase) {
      await programService.updatePhase(props.programId, props.phase.id!, form.value)
    } else {
      await programService.createPhase(props.programId, form.value)
    }
    emit('save')
  } catch (error) {
    console.error('Error saving phase:', error)
  } finally {
    saving.value = false
  }
}
</script>




