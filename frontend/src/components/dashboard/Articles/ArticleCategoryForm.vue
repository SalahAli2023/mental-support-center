<template>
  <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
    <Card class="w-full max-w-md max-h-[90vh] overflow-y-auto">
      <template #header>
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-semibold text-primary">
            {{ category ? 'تعديل التصنيف' : 'إضافة تصنيف جديد' }}
          </h2>
          <button 
            @click="$emit('cancel')"
            class="text-gray-400 hover:text-gray-500"
          >
            <XMarkIcon class="h-5 w-5" />
          </button>
        </div>
      </template>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- الاسم العربي -->
        <div>
          <label for="name_ar" class="block text-sm font-medium text-primary mb-1">
            الاسم العربي *
          </label>
          <input
            id="name_ar"
            v-model="formData.name_ar"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            placeholder="أدخل الاسم العربي"
          >
        </div>

        <!-- الاسم الإنجليزي -->
        <div>
          <label for="name_en" class="block text-sm font-medium text-primary mb-1">
            الاسم الإنجليزي *
          </label>
          <input
            id="name_en"
            v-model="formData.name_en"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            placeholder="أدخل الاسم الإنجليزي"
          >
        </div>

        <!-- اللون -->
        <div>
          <label for="color" class="block text-sm font-medium text-primary mb-1">
            اللون
          </label>
          <div class="flex items-center gap-3">
            <input
              id="color"
              v-model="formData.color"
              type="color"
              class="w-12 h-12 border border-gray-300 rounded-lg cursor-pointer"
            >
            <input
              v-model="formData.color"
              type="text"
              class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent"
              placeholder="#3b82f6"
            >
          </div>
        </div>

        <!-- الوصف العربي -->
        <div>
          <label for="description_ar" class="block text-sm font-medium text-primary mb-1">
            الوصف العربي
          </label>
          <textarea
            id="description_ar"
            v-model="formData.description_ar"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            placeholder="أدخل الوصف العربي"
          ></textarea>
        </div>

        <!-- الوصف الإنجليزي -->
        <div>
          <label for="description_en" class="block text-sm font-medium text-primary mb-1">
            الوصف الإنجليزي
          </label>
          <textarea
            id="description_en"
            v-model="formData.description_en"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent"
            placeholder="أدخل الوصف الإنجليزي"
          ></textarea>
        </div>

        <!-- الإجراءات - تم نقلها داخل النموذج -->
        <div class="flex gap-2 justify-end pt-4">
          <Button type="button" @click="$emit('cancel')" variant="outline">
            إلغاء
          </Button>
          <Button type="submit" variant="primary" :loading="loading">
            {{ category ? 'تحديث' : 'إنشاء' }}
          </Button>
        </div>
      </form>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import Button from '@/components/dashboard/component/ui/Button.vue'
import Card from '@/components/dashboard/component/ui/Card.vue'
import { useArticleStore } from '@/stores/articles'
import type { ArticleCategory } from '@/types/article'

interface Props {
  category?: ArticleCategory | null
}

interface Emits {
  (e: 'save'): void
  (e: 'cancel'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const articleStore = useArticleStore()
const loading = ref(false)

const formData = ref({
  name_ar: '',
  name_en: '',
  color: '#3b82f6',
  description_ar: '',
  description_en: ''
})

// تعبئة البيانات عند التعديل
watch(() => props.category, (newCategory) => {
  if (newCategory) {
    formData.value = {
      name_ar: newCategory.name_ar,
      name_en: newCategory.name_en,
      color: newCategory.color || '#3b82f6',
      description_ar: newCategory.description_ar || '',
      description_en: newCategory.description_en || ''
    }
  } else {
    // إعادة التعيين للنموذج الجديد
    formData.value = {
      name_ar: '',
      name_en: '',
      color: '#3b82f6',
      description_ar: '',
      description_en: ''
    }
  }
}, { immediate: true })

const handleSubmit = async () => {
  loading.value = true
  try {
    if (props.category) {
      // تحديث التصنيف
      await articleStore.updateCategory(props.category.id, formData.value)
    } else {
      // إنشاء تصنيف جديد
      await articleStore.createCategory(formData.value)
    }
    emit('save')
  } catch (error) {
    console.error('Failed to save category:', error)
  } finally {
    loading.value = false
  }
}
</script>