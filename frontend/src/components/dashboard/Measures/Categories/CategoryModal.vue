<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2 sm:p-3">

    <div class="w-full max-w-2xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto rounded-xl border border-primary bg-primary p-3 sm:p-4 shadow-lg">
      <!-- الهيدر -->
      <div class="mb-3 flex items-center justify-between">
        <div class="text-base sm:text-lg font-semibold text-primary">
          {{ current ? 'تعديل التصنيف' : 'إضافة تصنيف جديد' }}
        </div>
        <button class="inline-grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-lg hover:bg-tertiary text-primary" @click="$emit('close')">
          ✕
        </button>
      </div>
      
      <!-- محتوى النموذج -->
      <div class="space-y-4">
        <!-- اسم التصنيف -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
          <FormField label="اسم التصنيف (العربية) *">
            <input 
              v-model="form.name_ar" 
              placeholder="أدخل اسم التصنيف بالعربية..." 
              class="input text-sm sm:text-base" 
              required
            />
          </FormField>
          <FormField label="اسم التصنيف (الإنجليزية) *">
            <input 
              v-model="form.name_en" 
              placeholder="Enter category name in English..." 
              class="input text-sm sm:text-base" 
              required
            />
          </FormField>
        </div>

        <!-- وصف التصنيف -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
          <FormField label="وصف التصنيف (العربية)">
            <textarea 
              v-model="form.description_ar" 
              placeholder="أدخل وصف التصنيف بالعربية..." 
              rows="3"
              class="input text-sm sm:text-base"
            ></textarea>
          </FormField>
          <FormField label="وصف التصنيف (الإنجليزية)">
            <textarea 
              v-model="form.description_en" 
              placeholder="Enter category description in English..." 
              rows="3"
              class="input text-sm sm:text-base"
            ></textarea>
          </FormField>
        </div>

        <!-- اللون والحالة -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
          <FormField label="لون التصنيف">
            <div class="flex items-center gap-3">
              <input 
                v-model="form.color"
                type="color"
                class="w-12 h-12 rounded-lg border border-gray-300 cursor-pointer"
              />
              <input 
                v-model="form.color"
                type="text"
                class="input text-sm sm:text-base flex-1"
                placeholder="#3B82F6"
                maxlength="7"
              />
            </div>
            <p class="text-xs text-gray-500 mt-1">اختر لوناً يميز هذا التصنيف</p>
          </FormField>
          
          <div class="flex items-center gap-2 pt-6">
            <input 
              v-model="form.is_active"
              type="checkbox"
              id="active"
              class="rounded border-primary text-brand-500 focus:ring-brand-500"
            />
            <label for="active" class="text-sm text-primary">نشط</label>
          </div>
        </div>
      </div>

      <!-- أزرار التنقل -->
      <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary">
        <Button variant="outline" @click="$emit('close')" class="w-full sm:w-auto">
          إلغاء
        </Button>
        <Button 
          variant="primary" 
          @click="$emit('save')" 
          class="w-full sm:w-auto"
          :loading="saving"
        >
          {{ current ? 'تحديث' : 'حفظ' }}
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from '@/components/dashboard/component/ui/Button.vue';
import FormField from './FormField.vue';

import type { ScaleCategory, CategoryForm } from './types';

defineProps<{
  show: boolean;
  current: ScaleCategory | null;
  form: CategoryForm;
  saving: boolean;
}>();

defineEmits<{
  close: [];
  save: [];
}>();
</script>