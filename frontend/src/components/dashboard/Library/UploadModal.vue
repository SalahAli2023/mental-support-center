<template>
  <div
    class="fixed inset-0 z-[9999] bg-black/50 backdrop-blur-sm overflow-y-auto"

  >
    <div
      class="w-full max-w-2xl mx-auto my-8 bg-primary rounded-xl border border-primary shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
    >
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-primary">
        <div>
          <h2 class="text-lg sm:text-xl font-semibold text-primary">
            {{ editingItem ? 'تعديل مادة في المكتبة' : 'إضافة مادة جديدة إلى المكتبة' }}
          </h2>
          <p class="text-sm text-secondary mt-1">
            {{ editingItem ? 'قم بتعديل بيانات الكتاب أو المادة ثم احفظ التغييرات.' : 'املأ الحقول التالية ثم قم برفع الملف.' }}
          </p>
        </div>
        <button
          type="button"
          @click="emitClose"
          class="w-9 h-9 rounded-lg flex items-center justify-center bg-tertiary hover:bg-secondary transition-colors"
        >
          <i class="fas fa-times text-primary text-sm"></i>
        </button>
      </div>

      <!-- Body -->
      <form @submit.prevent="handleSubmit" class="flex-1 px-6 py-4 space-y-4 overflow-y-auto">
        <!-- File (حقل إدخال نصي) -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-primary">
            مسار الملف الرئيسي *
          </label>
          <div class="flex gap-2">
            <input
              v-model="form.file_path"
              type="text"
              required
              class="flex-1 px-3 py-2 rounded-lg border border-primary bg-primary text-primary placeholder-secondary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
              placeholder="مثال: uploads/library/file.pdf أو https://example.com/file.pdf"
            />
            <button
              type="button"
              @click="testFilePath"
              class="px-3 py-2 rounded-lg border border-primary text-sm text-primary hover:bg-tertiary transition-colors whitespace-nowrap"
            >
              اختبار المسار
            </button>
          </div>
          <p class="text-xs text-secondary">
            أدخل مسار الملف مباشرة (PDF, DOC, MP4, إلخ)
          </p>
          
          <!-- File Size -->
          <div v-if="fileSizeFromUrl" class="mt-2 bg-tertiary/50 rounded-lg p-3">
            <div class="text-sm text-primary">
              <span class="font-medium">حجم الملف:</span>
              {{ fileSizeFromUrl }}
            </div>
          </div>
        </div>

        <!-- Cover image - حقل رفع ملف -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-primary">
            صورة الغلاف
          </label>
          
          <!-- معاينة الصورة إذا كان هناك ملف أو رابط -->
          <div v-if="coverPreview" class="mb-3">
            <div class="w-32 h-40 rounded-lg overflow-hidden border border-primary bg-tertiary">
              <img :src="coverPreview" alt="معاينة الغلاف" class="w-full h-full object-cover" />
            </div>
          </div>
          
          <!-- حقل رفع ملف للصورة -->
          <input
            ref="coverInput"
            type="file"
            accept="image/*"
            @change="onCoverChange"
            class="w-full text-sm text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-500/80 file:text-white hover:file:bg-brand-600 cursor-pointer"
          />
          <p class="text-xs text-secondary">
            يُفضل استخدام صورة بحجم 300×400 بكسل بصيغة JPG أو PNG
          </p>
          
          <!-- أو إدخال مسار الصورة يدوياً -->
          <div class="mt-2">
            <div class="flex items-center gap-2 mb-1">
              <span class="text-xs text-secondary">أو أدخل رابط الصورة مباشرة:</span>
            </div>
            <input
              v-model="form.cover_image"
              type="text"
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary placeholder-secondary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
              placeholder="مثال: uploads/covers/book-cover.jpg"
              @input="updateCoverPreviewFromUrl"
            />
          </div>
        </div>

        <!-- Titles -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              العنوان (عربي) *
            </label>
            <input
              v-model="form.title_ar"
              type="text"
              required
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary placeholder-secondary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
              placeholder="اكتب عنوان الكتاب أو المادة بالعربية"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              العنوان (إنجليزي) *
            </label>
            <input
              v-model="form.title_en"
              type="text"
              required
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary placeholder-secondary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
              placeholder="Write the title in English"
            />
          </div>
        </div>

        <!-- Type & Category -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              نوع المادة *
            </label>
            <select
              v-model="form.type"
              required
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            >
              <option value="">اختر النوع</option>
              <option value="book">كتاب</option>
              <option value="research">بحث</option>
              <option value="guide">دليل</option>
              <option value="article">مقال</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              التصنيف *
            </label>
            <select
              v-model="form.category_id"
              required
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            >
              <option value="">اختر التصنيف</option>
              <option
                v-for="cat in categories"
                :key="cat.id"
                :value="cat.id"
              >
                {{ cat.name_ar }}
              </option>
            </select>
          </div>
        </div>

        <!-- Optional fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              اسم المؤلف (عربي)
            </label>
            <input
              v-model="form.author_ar"
              type="text"
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary placeholder-secondary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              اسم المؤلف (إنجليزي)
            </label>
            <input
              v-model="form.author_en"
              type="text"
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary placeholder-secondary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              سنة / تاريخ النشر
            </label>
            <input
              v-model="form.publish_date"
              type="date"
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              عدد الصفحات
            </label>
            <input
              v-model.number="form.pages"
              type="number"
              min="1"
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            />
          </div>
        </div>

        <!-- Description -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              وصف مختصر (عربي)
            </label>
            <textarea
              v-model="form.description_ar"
              rows="3"
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary placeholder-secondary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none"
            ></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-primary mb-1">
              وصف مختصر (إنجليزي)
            </label>
            <textarea
              v-model="form.description_en"
              rows="3"
              class="w-full px-3 py-2 rounded-lg border border-primary bg-primary text-primary placeholder-secondary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none"
            ></textarea>
          </div>
        </div>

        <!-- Flags -->
        <div class="flex flex-wrap gap-4 items-center">
          <label class="inline-flex items-center gap-2 text-sm text-secondary">
            <input
              v-model="form.is_new"
              type="checkbox"
              class="rounded border-primary text-brand-500 focus:ring-brand-500"
            />
            <span>جديد</span>
          </label>
          <label class="inline-flex items-center gap-2 text-sm text-secondary">
            <input
              v-model="form.is_published"
              type="checkbox"
              class="rounded border-primary text-brand-500 focus:ring-brand-500"
            />
            <span>منشور في المكتبة</span>
          </label>
        </div>
      </form>

      <!-- Footer -->
      <div class="px-6 py-4 border-t border-primary flex items-center justify-end gap-3">
        <button
          type="button"
          @click="emitClose"
          class="px-4 py-2 rounded-lg border border-primary text-sm font-medium text-secondary hover:bg-tertiary transition-colors"
        >
          إلغاء
        </button>
        <button
          type="button"
          :disabled="submitting"
          @click="handleSubmit"
          class="px-5 py-2 rounded-lg bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 disabled:opacity-60 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
        >
          <span v-if="!submitting">
            {{ editingItem ? 'حفظ التغييرات' : 'رفع المادة' }}
          </span>
          <span v-else class="flex items-center gap-2">
            <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            جاري الحفظ...
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue'
import { useLibraryStore, type LibraryItem, type LibraryCategory } from '@/stores/library'
import { useToast } from '@/composables/useToast'

interface Props {
  categories: LibraryCategory[]
  editingItem: LibraryItem | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'close'): void
  (e: 'uploaded'): void
}>()

const libraryStore = useLibraryStore()
const toast = useToast()

const submitting = ref(false)
const coverInput = ref<HTMLInputElement | null>(null)
const coverFile = ref<File | null>(null)
const coverPreview = ref<string | null>(null)

const categories = computed(() => props.categories || [])
const editingItem = computed(() => props.editingItem)

const form = reactive({
  title_ar: '',
  title_en: '',
  description_ar: '',
  description_en: '',
  author_ar: '',
  author_en: '',
  type: '',
  category_id: '' as string | number,
  publish_date: '',
  pages: '' as string | number,
  is_new: true,
  is_published: true,
  file_path: '', // حقل نصي للملف الرئيسي
  cover_image: '' // حقل نصي للصورة (اختياري)
})

// حساب حجم الملف من الامتداد (تقدير)
const fileSizeFromUrl = computed(() => {
  if (!form.file_path) return null
  
  // محاكاة حجم الملف بناءً على الامتداد
  const ext = form.file_path.split('.').pop()?.toLowerCase()
  const sizes: Record<string, string> = {
    pdf: '~2-5 MB',
    doc: '~1-3 MB',
    docx: '~1-3 MB',
    mp4: '~10-50 MB',
    mp3: '~3-10 MB',
    jpg: '~0.5-2 MB',
    png: '~1-3 MB',
    zip: '~5-20 MB'
  }
  
  return sizes[ext || ''] || '~1-5 MB'
})

const resetForm = () => {
  form.title_ar = ''
  form.title_en = ''
  form.description_ar = ''
  form.description_en = ''
  form.author_ar = ''
  form.author_en = ''
  form.type = ''
  form.category_id = ''
  form.publish_date = ''
  form.pages = ''
  form.is_new = true
  form.is_published = true
  form.file_path = ''
  form.cover_image = ''
  coverFile.value = null
  coverPreview.value = null
  if (coverInput.value) coverInput.value.value = ''
}

watch(
  () => editingItem.value,
  (item) => {
    if (item) {
      form.title_ar = item.title_ar || ''
      form.title_en = item.title_en || ''
      form.description_ar = item.description_ar || ''
      form.description_en = item.description_en || ''
      form.author_ar = item.author_ar || ''
      form.author_en = item.author_en || ''
      form.type = item.type || ''
      form.category_id = item.category_id || ''
      form.publish_date = item.publish_date || ''
      form.pages = item.pages || ''
      form.is_new = item.is_new ?? false
      form.is_published = item.is_published ?? true
      form.file_path = item.file_path || ''
      form.cover_image = item.cover_image || ''
      
      // تحديث معاينة الصورة
      if (item.cover_image) {
        coverPreview.value = item.cover_image
      }
    } else {
      resetForm()
    }
  },
  { immediate: true }
)

const onCoverChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  const file = target.files && target.files[0] ? target.files[0] : null
  coverFile.value = file
  
  if (file) {
    // عرض معاينة للصورة المرفوعة
    coverPreview.value = URL.createObjectURL(file)
    
    // مسح حقل الإدخال النصي للصورة
    form.cover_image = ''
  } else {
    coverPreview.value = null
  }
}

const updateCoverPreviewFromUrl = () => {
  if (form.cover_image) {
    coverPreview.value = form.cover_image
    
    // مسح ملف الصورة المرفوع
    coverFile.value = null
    if (coverInput.value) coverInput.value.value = ''
  } else {
    coverPreview.value = null
  }
}

const testFilePath = () => {
  if (!form.file_path) {
    toast.error('أدخل مسار الملف أولاً')
    return
  }
  
  window.open(form.file_path, '_blank')
  toast.success('تم فتح المسار في نافذة جديدة')
}

const emitClose = () => {
  emit('close')
}

const handleSubmit = async () => {
  if (!form.title_ar || !form.title_en || !form.type || !form.category_id || !form.file_path) {
    toast.error('الرجاء ملء الحقول المطلوبة')
    return
  }

  submitting.value = true

  try {
    const fd = new FormData()
    fd.append('title_ar', form.title_ar)
    fd.append('title_en', form.title_en)
    fd.append('description_ar', form.description_ar || '')
    fd.append('description_en', form.description_en || '')
    fd.append('author_ar', form.author_ar || '')
    fd.append('author_en', form.author_en || '')
    fd.append('type', form.type)
    fd.append('category_id', String(form.category_id))
    fd.append('publish_date', form.publish_date || '')
    fd.append('pages', String(form.pages || ''))
    fd.append('is_new', form.is_new ? '1' : '0')
    fd.append('is_published', form.is_published ? '1' : '0')
    
    // إضافة file_path (نصي)
    fd.append('file_path', form.file_path)

    // إضافة cover_image (ملف أو نص)
    if (coverFile.value) {
      fd.append('cover_image', coverFile.value)
    } else if (form.cover_image) {
      fd.append('cover_image', form.cover_image)
    }

    if (editingItem.value) {
      // update existing item
      fd.append('_method', 'PUT')
      await libraryStore.updateItem(editingItem.value.id, fd)
      toast.success('تم تحديث الكتاب بنجاح')
    } else {
      // create new
      await libraryStore.createItem(fd)
      toast.success('تم إضافة الكتاب بنجاح')
    }

    emit('uploaded')
  } catch (err: any) {
    console.error('Failed to save library item:', err)
    toast.handleApiError(err, 'حدث خطأ أثناء حفظ الكتاب. يرجى المحاولة مرة أخرى.')
  } finally {
    submitting.value = false
  }
}
</script>