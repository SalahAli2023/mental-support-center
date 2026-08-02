<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3" @click.self="$emit('cancel')">
    <div class="w-full max-w-4xl rounded-xl border border-primary bg-primary p-4 shadow-lg flex flex-col max-h-[90vh]">
      <div class="mb-3 flex items-center justify-between shrink-0">
        <div class="text-lg font-semibold text-primary">{{ resource ? 'تعديل المورد القانوني' : 'إضافة مورد قانوني' }}</div>
        <button class="inline-grid h-9 w-9 place-items-center rounded-lg hover:bg-tertiary text-primary" @click="$emit('cancel')">✕</button>
      </div>
      
      <div class="overflow-y-auto flex-1 custom-scrollbar">
        <form @submit.prevent="handleSave" class="grid gap-3 pr-2">
          <!-- نوع القانون -->
          <div>
            <label class="block text-sm font-medium text-primary mb-2">نوع القانون *</label>
            <input
              v-model="formData.law_type"
              type="text"
              required
              placeholder="مثال: قانون الأحوال الشخصية"
              class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
            />
          </div>

          <!-- التصنيف -->
          <div>
            <label class="block text-sm font-medium text-primary mb-2">التصنيف</label>
            <select
              v-model="formData.category_id"
              class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
            >
              <option value="">اختر التصنيف</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>

          <!-- رقم المادة -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">رقم المادة (عربي) *</label>
              <input
                v-model="formData.article_number_ar"
                type="text"
                required
                placeholder="المادة ١"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-primary mb-2">رقم المادة (إنجليزي) *</label>
              <input
                v-model="formData.article_number_en"
                type="text"
                required
                placeholder="Article 1"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
            </div>
          </div>

          <!-- النص بالعربية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-2">النص (عربي) *</label>
            <div class="rounded-lg border border-primary overflow-hidden">
              <QuillEditor 
                theme="snow" 
                v-model:content="formData.text_ar" 
                contentType="html" 
                class="h-40 bg-primary text-primary" 
              />
            </div>
          </div>

          <!-- النص بالإنجليزية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-2">النص (إنجليزي) *</label>
            <div class="rounded-lg border border-primary overflow-hidden">
              <QuillEditor 
                theme="snow" 
                v-model:content="formData.text_en" 
                contentType="html" 
                class="h-40 bg-primary text-primary" 
              />
            </div>
          </div>

          <!-- الأزرار -->
          <div class="mt-4 flex justify-end gap-2 pt-4 border-t border-primary shrink-0">
            <Button variant="outline" @click="$emit('cancel')" type="button">إلغاء</Button>
            <Button variant="primary" type="submit" :disabled="loading">
              <span v-if="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
              {{ resource ? 'تحديث' : 'حفظ' }}
            </Button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import Button from '@/components/dashboard/component/ui/Button.vue'
import { useLegalResourceStore } from '@/stores/legalResources' 
import type { LegalResource, LegalResourceCategory } from '@/types/legal-resource'

interface Props {
  resource?: LegalResource | null
  categories: LegalResourceCategory[]
}

const props = defineProps<Props>()
const emit = defineEmits<{
  save: []
  cancel: []
}>()

// استخدام المتجر
const legalResourceStore = useLegalResourceStore()

const loading = ref(false)

const formData = ref({
  law_type: '',
  article_number_ar: '',
  article_number_en: '',
  text_ar: '',
  text_en: '',
  category_id: ''
})

// تعبئة البيانات إذا كان تعديل
watch(() => props.resource, (resource) => {
  if (resource) {
    formData.value = {
      law_type: resource.law_type || '',
      article_number_ar: resource.article_number_ar || '',
      article_number_en: resource.article_number_en || '',
      text_ar: resource.text_ar || '',
      text_en: resource.text_en || '',
      category_id: resource.category_id || ''
    }
  } else {
    // إعادة تعيين النموذج
    formData.value = {
      law_type: '',
      article_number_ar: '',
      article_number_en: '',
      text_ar: '',
      text_en: '',
      category_id: ''
    }
  }
}, { immediate: true })

const handleSave = async () => {
  // التحقق من الحقول المطلوبة
  if (!formData.value.law_type || !formData.value.article_number_ar || !formData.value.article_number_en || 
      !formData.value.text_ar || !formData.value.text_en) {
    alert('يرجى ملء جميع الحقول المطلوبة')
    return
  }

  loading.value = true
  
  try {
    if (props.resource) {
      // تحديث مورد موجود
      await legalResourceStore.updateResource(props.resource.id, formData.value)
    } else {
      // إنشاء مورد جديد
      await legalResourceStore.createResource(formData.value)
    }

    emit('save')
    
  } catch (error) {
    console.error('❌ فشل في حفظ المورد:', error)
    alert('حدث خطأ أثناء حفظ المورد. يرجى المحاولة مرة أخرى.')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* تخصيص مظهر محرر النصوص */
:deep(.ql-toolbar) {
  border-top: none !important;
  border-left: none !important;
  border-right: none !important;
  border-bottom: 1px solid #e5e7eb !important;
}

:deep(.ql-container) {
  border: none !important;
  font-size: 14px;
}

:deep(.ql-editor) {
  min-height: 100px;
  padding: 12px;
}

:deep(.ql-editor.ql-blank::before) {
  font-style: normal;
  color: #9ca3af;
}

/* تخصيص شريط التمرير */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #c1c1c1 #f1f1f1;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* التمرير السلس */
.custom-scrollbar {
  scroll-behavior: smooth;
}

/* التأكد من أن المحتوى لا يتجاوز الارتفاع */
:deep(.ql-editor) {
  max-height: 120px;
  overflow-y: auto;
}

:deep(.ql-editor)::-webkit-scrollbar {
  width: 4px;
}

:deep(.ql-editor)::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 2px;
}
</style>