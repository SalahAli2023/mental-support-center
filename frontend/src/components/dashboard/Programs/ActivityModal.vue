<template>
  <div v-if="open" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-black/50" ></div>

      <div class="relative bg-white rounded-lg shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-xl font-semibold text-primary">
            {{ activity ? 'تعديل النشاط' : 'إضافة نشاط جديد' }}
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
            />
          </div>

          <!-- نوع النشاط -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              نوع النشاط *
            </label>
            <select v-model="form.activity_type" required class="input w-full">
              <option value="text">قراءة محتوى</option>
              <option value="video">فيديو</option>
              <option value="audio">صوت (تمارين استرخاء)</option>
              <option value="form">نموذج إدخال</option>
              <option value="exercise">تمرين بزمن</option>
              <option value="reflection_questions">أسئلة انعكاسية</option>
              <option value="quiz">اختبار</option>
            </select>
          </div>

          <!-- المقياس المرتبط (للاختبارات) -->
          <div v-if="form.activity_type === 'quiz'">
            <label class="block text-sm font-medium text-primary mb-1">
              المقياس المرتبط
            </label>
            <select v-model="form.scale_id" class="input w-full">
              <option :value="null">اختر مقياساً...</option>
              <option v-for="scale in scales" :key="scale.id" :value="scale.id">
                {{ scale.name_ar }}
              </option>
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

          <!-- المحتوى -->
          <div v-if="form.activity_type === 'text'">
            <label class="block text-sm font-medium text-primary mb-1">
              المحتوى بالعربية
            </label>
            <textarea
              v-model="form.content_ar"
              rows="6"
              class="input w-full"
            ></textarea>
          </div>

          <!-- رابط الوسائط -->
          <div v-if="['video', 'audio'].includes(form.activity_type)">
            <label class="block text-sm font-medium text-primary mb-1">
              رابط الفيديو/الصوت
            </label>
            <input
              v-model="form.media_url"
              type="url"
              class="input w-full"
              placeholder="https://..."
            />
            <div class="mt-2">
              <label class="block text-sm font-medium text-primary mb-1">
                نوع الوسائط
              </label>
              <select v-model="form.media_type" class="input w-full">
                <option value="video">فيديو</option>
                <option value="audio">صوت</option>
                <option value="image">صورة</option>
              </select>
            </div>
          </div>

          <!-- مدة التمرين -->
          <div v-if="form.activity_type === 'exercise'">
            <label class="block text-sm font-medium text-primary mb-1">
              المدة (بالدقائق)
            </label>
            <input
              v-model.number="form.duration_minutes"
              type="number"
              min="1"
              class="input w-full"
            />
          </div>

          <!-- ترتيب النشاط -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              ترتيب النشاط
            </label>
            <input
              v-model.number="form.activity_order"
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
import { ref, watch, onMounted } from 'vue'
import { programService } from '@/services/programService'
import { scaleService } from '@/services/scaleService'
import { XMarkIcon } from '@heroicons/vue/24/outline'

interface Activity {
  id?: string
  session_id: string
  name_ar: string
  name_en: string
  activity_type: string
  scale_id?: string | null
  instructions_ar?: string
  instructions_en?: string
  content_ar?: string
  content_en?: string
  media_url?: string
  media_type?: string
  duration_minutes?: number
  activity_order: number
  is_mandatory: boolean
  is_active: boolean
  activity_config?: any
}

interface Props {
  open: boolean
  activity: Activity | null
  sessionId: string
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  save: []
}>()

const saving = ref(false)
const scales = ref<any[]>([])
const form = ref<Activity>({
  session_id: props.sessionId,
  name_ar: '',
  name_en: '',
  activity_type: 'text',
  scale_id: null,
  instructions_ar: '',
  instructions_en: '',
  content_ar: '',
  content_en: '',
  media_url: '',
  media_type: 'video',
  duration_minutes: undefined,
  activity_order: 1,
  is_mandatory: true,
  is_active: true
})

const fetchScales = async () => {
  try {
    scales.value = await scaleService.getAllScales()
  } catch (error) {
    console.error('Error fetching scales:', error)
  }
}

watch(() => props.activity, (newActivity) => {
  if (newActivity) {
    form.value = { ...newActivity }
  } else {
    form.value = {
      session_id: props.sessionId,
      name_ar: '',
      name_en: '',
      activity_type: 'text',
      scale_id: null,
      instructions_ar: '',
      instructions_en: '',
      content_ar: '',
      content_en: '',
      media_url: '',
      media_type: 'video',
      duration_minutes: undefined,
      activity_order: 1,
      is_mandatory: true,
      is_active: true
    }
  }
}, { immediate: true })

onMounted(() => {
  fetchScales()
})

const handleSubmit = async () => {
  saving.value = true
  try {
    // إرسال scale_id كـ null إذا لم يكن محدداً (أو إذا لم يكن نوع النشاط quiz)
    if (form.value.activity_type !== 'quiz') {
      form.value.scale_id = null
    }

    if (props.activity) {
      await programService.updateActivity(props.activity.id!, form.value)
    } else {
      await programService.createActivity(form.value, props.sessionId)
    }
    emit('save')
  } catch (error) {
    console.error('Error saving activity:', error)
  } finally {
    saving.value = false
  }
}
</script>




