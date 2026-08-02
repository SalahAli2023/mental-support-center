<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3" @click.self="$emit('cancel')">
    <div class="w-full max-w-4xl rounded-xl border border-primary bg-primary p-4 shadow-lg flex flex-col max-h-[90vh]">
      <div class="mb-3 flex items-center justify-between shrink-0">
        <div class="text-lg font-semibold text-primary">{{ article ? 'تعديل المقال' : 'إضافة مقال' }}</div>
        <button class="inline-grid h-9 w-9 place-items-center rounded-lg hover:bg-tertiary text-primary" @click="$emit('cancel')">✕</button>
      </div>
      
      <div class="overflow-y-auto flex-1 custom-scrollbar">
        <form @submit.prevent="handleSave" class="grid gap-3 pr-2">
          <!-- العنوان -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <input
                v-model="formData.title_ar"
                type="text"
                required
                placeholder="العنوان بالعربية"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
            </div>
            <div>
              <input
                v-model="formData.title_en"
                type="text"
                required
                placeholder="العنوان بالإنجليزية"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
            </div>
          </div>

          <!-- المقدمة -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <textarea
                v-model="formData.introduction_ar"
                rows="3"
                placeholder="المقدمة بالعربية"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
            </div>
            <div>
              <textarea
                v-model="formData.introduction_en"
                rows="3"
                placeholder="المقدمة بالإنجليزية"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
            </div>
          </div>

          <!-- الملخص -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <textarea
                v-model="formData.excerpt_ar"
                rows="3"
                required
                placeholder="الملخص بالعربية"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
            </div>
            <div>
              <textarea
                v-model="formData.excerpt_en"
                rows="3"
                required
                placeholder="الملخص بالإنجليزية"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
            </div>
          </div>

          <!-- التصنيف وتاريخ النشر -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <select
                v-model="formData.category_id"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              >
                <option value="">اختر التصنيف</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name_ar }}
                </option>
              </select>
            </div>
            <div>
              <input
                v-model="formData.published_at"
                type="datetime-local"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
            </div>
          </div>

          <!-- ربط المقياس -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">المقياس المرتبط (اختياري)</label>
            <select
              v-model="formData.psychological_scale_id"
              class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
            >
              <option value="">بدون مقياس</option>
              <option
                v-for="scale in scales"
                :key="scale.id"
                :value="scale.id"
              >
                {{ scale.name_ar || scale.name_en }}
              </option>
            </select>
            <p class="text-xs text-secondary mt-1">سيتم توجيه زر "ابدأ الاختبار" لهذا المقياس</p>
          </div>

          <!-- المحتوى بالعربية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">المحتوى بالعربية</label>
            <div class="rounded-lg border border-primary overflow-hidden">
              <QuillEditor 
                theme="snow" 
                v-model:content="formData.content_ar" 
                contentType="html" 
                class="h-64 bg-primary text-primary" 
              />
            </div>
          </div>

          <!-- المحتوى بالإنجليزية -->
          <div>
            <label class="block text-sm font-medium text-primary mb-1">المحتوى بالإنجليزية</label>
            <div class="rounded-lg border border-primary overflow-hidden">
              <QuillEditor 
                theme="snow" 
                v-model:content="formData.content_en" 
                contentType="html" 
                class="h-64 bg-primary text-primary" 
              />
            </div>
          </div>

          <!-- الصورة -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-primary mb-1">صورة المقال</label>
              <input
                type="file"
                @change="handleImageUpload"
                accept="image/*"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
              />
              <p class="text-xs text-secondary mt-1">الحجم الأقصى: 2MB</p>
            </div>
            <div v-if="imagePreview" class="mt-6">
              <img :src="imagePreview" alt="Preview" class="w-20 h-20 rounded-lg object-cover" />
            </div>
          </div>

          <!-- حالة النشر -->
          <div class="flex items-center gap-2">
            <input
              v-model="formData.is_published"
              type="checkbox"
              id="is_published"
              class="rounded border-primary text-brand-500"
            />
            <label for="is_published" class="text-sm text-primary">
              نشر مباشرة
            </label>
          </div>

          <!-- الأزرار -->
          <div class="mt-4 flex justify-end gap-2 pt-4 border-t border-primary shrink-0">
            <Button variant="outline" @click="$emit('cancel')" type="button">إلغاء</Button>
            <Button variant="primary" type="submit" :disabled="loading">
              <span v-if="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
              {{ article ? 'تحديث' : 'حفظ' }}
            </Button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import Button from '@/components/dashboard/component/ui/Button.vue'
import { useArticleStore } from '@/stores/articles'
import { useScalesStore } from '@/stores/scales'
import type { Article } from '@/types/article'
import { useToast } from '@/composables/useToast'

interface Props {
  article?: Article | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  save: []
  cancel: []
}>()

const articleStore = useArticleStore()
const toast = useToast()
const loading = ref(false)
const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)

const categories = computed(() => articleStore.categories)
const scalesStore = useScalesStore()
const scales = computed(() => scalesStore.scales)

const formData = ref({
  title_ar: '',
  title_en: '',
  introduction_ar: '',
  introduction_en: '',
  excerpt_ar: '',
  excerpt_en: '',
  content_ar: '',
  content_en: '',
  category_id: '',
  psychological_scale_id: '',
  published_at: '',
  is_published: false
})

// ✅ نقل الدوال إلى الأعلى قبل الـ watch
const formatDateTimeLocal = (dateString: string) => {
  const date = new Date(dateString)
  return date.toISOString().slice(0, 16)
}

const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    
    // التحقق من حجم الملف
    if (file.size > 2 * 1024 * 1024) {
      toast.warning('حجم الصورة يجب أن يكون أقل من 2 ميجابايت')
      return
    }
    
    imageFile.value = file
    
    // إنشاء معاينة للصورة
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

// ✅ الآن الـ watch يمكنه استخدام الدوال المعرفة مسبقاً
// تعبئة البيانات إذا كان تعديل
watch(() => props.article, (article) => {
  if (article) {
    formData.value = {
      title_ar: article.title_ar || '',
      title_en: article.title_en || '',
      introduction_ar: article.introduction_ar || '',
      introduction_en: article.introduction_en || '',
      excerpt_ar: article.excerpt_ar || '',
      excerpt_en: article.excerpt_en || '',
      content_ar: article.content_ar || '',
      content_en: article.content_en || '',
      category_id: article.category_id || '',
      psychological_scale_id: article.psychological_scale_id || '',
      published_at: article.published_at ? formatDateTimeLocal(article.published_at) : '',
      is_published: article.is_published || false
    }
    
    if (article.image) {
      imagePreview.value = article.image
    }
  } else {
    // إعادة تعيين النموذج
    formData.value = {
      title_ar: '',
      title_en: '',
      introduction_ar: '',
      introduction_en: '',
      excerpt_ar: '',
      excerpt_en: '',
      content_ar: '',
      content_en: '',
      category_id: '',
      psychological_scale_id: '',
      published_at: formatDateTimeLocal(new Date().toISOString()),
      is_published: false
    }
    imageFile.value = null
    imagePreview.value = null
  }
}, { immediate: true })

onMounted(async () => {
  try {
    if (!scales.value.length) {
      await scalesStore.fetchScales({ per_page: 100, is_active: true })
    }
  } catch (error) {
    console.error('❌ فشل في جلب المقاييس:', error)
    toast.warning('تعذر تحميل قائمة المقاييس، حاول مرة أخرى لاحقاً')
  }
})

const handleSave = async () => {
  // التحقق من الحقول المطلوبة
  if (!formData.value.title_ar || !formData.value.title_en || 
      !formData.value.excerpt_ar || !formData.value.excerpt_en || 
      !formData.value.content_ar || !formData.value.content_en || 
      !formData.value.category_id || !formData.value.published_at) {
    alert('يرجى ملء جميع الحقول المطلوبة')
    return
  }

  loading.value = true
  
  try {
    console.log('🔄 بدء حفظ المقال...')

    // إعداد البيانات للإرسال
    const submitData = new FormData()
    
    // إضافة الحقول النصية
    submitData.append('title_ar', formData.value.title_ar)
    submitData.append('title_en', formData.value.title_en)
    submitData.append('introduction_ar', formData.value.introduction_ar)
    submitData.append('introduction_en', formData.value.introduction_en)
    submitData.append('excerpt_ar', formData.value.excerpt_ar)
    submitData.append('excerpt_en', formData.value.excerpt_en)
    submitData.append('content_ar', formData.value.content_ar)
    submitData.append('content_en', formData.value.content_en)
    submitData.append('category_id', formData.value.category_id)
    submitData.append('psychological_scale_id', formData.value.psychological_scale_id || '')
    submitData.append('published_at', formData.value.published_at)
    submitData.append('is_published', formData.value.is_published ? '1' : '0')

    // إضافة الصورة إذا كانت موجودة
    if (imageFile.value) {
      submitData.append('image', imageFile.value)
    }

    // إذا كان تعديلاً، أضف طريقة PUT
    if (props.article) {
      submitData.append('_method', 'PUT')
    }

    console.log('📤 إرسال البيانات إلى API...')

    let result
    if (props.article) {
      console.log('✏️ تحديث المقال:', props.article.id)
      result = await articleStore.updateArticle(props.article.id, submitData)
    } else {
      console.log('🆕 إنشاء مقال جديد')
      result = await articleStore.createArticle(submitData)
    }

    console.log('✅ تم الحفظ بنجاح:', result)
    
    toast.success(props.article ? 'تم تحديث المقال بنجاح' : 'تم إنشاء المقال بنجاح')

    emit('save')
    
  } catch (error: any) {
    console.error('❌ فشل في حفظ المقال:', error)
    
    toast.handleApiError(error, 'حدث خطأ أثناء حفظ المقال. يرجى المحاولة مرة أخرى.')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* نفس الأنماط المستخدمة في EventForm */
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
  min-height: 200px;
  padding: 12px;
}

:deep(.ql-editor.ql-blank::before) {
  font-style: normal;
  color: #9ca3af;
}

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

:deep(.ql-editor) {
  max-height: 200px;
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