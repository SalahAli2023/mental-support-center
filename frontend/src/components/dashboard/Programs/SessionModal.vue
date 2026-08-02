<template>
  <div v-if="open" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-black/50"></div>

      <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-primary">
            {{ session ? 'تعديل الجلسة' : 'إضافة جلسة جديدة' }}
          </h3>
          <button @click="$emit('close')" class="text-secondary hover:text-primary">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- المرحلة -->
          <div v-if="phases && phases.length > 0">
            <label class="block text-sm font-medium text-primary mb-1">
              المرحلة
            </label>
            <select v-model="form.phase_id" class="input w-full">
              <option value="">بدون مرحلة</option>
              <option v-for="phase in phases" :key="phase.id" :value="phase.id">
                {{ phase.name_ar }}
              </option>
            </select>
          </div>

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

          <!-- الهدف -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              الهدف
            </label>
            <textarea
              v-model="form.goal_ar"
              rows="3"
              class="input w-full"
              placeholder="هدف الجلسة بالعربية"
            ></textarea>
          </div>

          <!-- ترتيب الجلسة -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              ترتيب الجلسة
            </label>
            <input
              v-model.number="form.session_order"
              type="number"
              min="1"
              class="input w-full"
            />
          </div>

          <!-- المدة -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              المدة (بالدقائق)
            </label>
            <input
              v-model.number="form.duration"
              type="number"
              min="1"
              class="input w-full"
            />
          </div>

          <!-- الحالة -->
          <div>
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

interface Session {
  id?: string
  program_id: string
  phase_id?: string
  title_ar: string
  title_en: string
  goal_ar?: string
  goal_en?: string
  session_order: number
  duration?: number
  is_active?: boolean
}

interface Props {
  open: boolean
  session: Session | null
  programId: string
  phases?: any[]
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  save: []
}>()

const saving = ref(false)
const form = ref<Session>({
  program_id: props.programId,
  title_ar: '',
  title_en: '',
  goal_ar: '',
  goal_en: '',
  session_order: 1,
  duration: 30,
  is_active: true
})

watch(() => props.session, (newSession) => {
  if (newSession) {
    form.value = { ...newSession }
  } else {
    form.value = {
      program_id: props.programId,
      title_ar: '',
      title_en: '',
      goal_ar: '',
      goal_en: '',
      session_order: 1,
      duration: 30,
      is_active: true
    }
  }
}, { immediate: true })

const handleSubmit = async () => {
  saving.value = true
  try {
    if (props.session) {
      await programService.updateSession(props.session.id!, form.value)
    } else {
      await programService.createSession(form.value, props.programId)
    }
    emit('save')
  } catch (error) {
    console.error('Error saving session:', error)
  } finally {
    saving.value = false
  }
}
</script>




