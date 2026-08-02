<template>
  <div class="space-y-2">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
      <label class="block text-sm font-medium text-primary">الخيارات</label>
      <button 
        type="button"
        @click="$emit('addOption')"
        class="text-sm text-brand-500 hover:text-brand-500 hover:underline font-medium"
      >
        + إضافة خيار
      </button>
    </div>
    
    <div class="space-y-3">
      <div 
        v-for="(option, optionIndex) in question.options" 
        :key="optionIndex" 
        class="flex card card-secondary p-3 space-y-3"
      >
        <!-- نص الخيار -->
        <div class="grid grid-cols-2 md:grid-cols-2 w-full gap-1">

          <FormField label="نص الخيار (العربية)">
            <input 
              v-model="option.option_text_ar"
              type="text"
              class="input text-sm sm:text-base"
              :placeholder="`الخيار ${optionIndex + 1} بالعربية`"
              required
            />
          </FormField>
          <FormField label="نص الخيار (الإنجليزية)">
            <input 
              v-model="option.option_text_en"
              type="text"
              class="input text-sm sm:text-base"
              :placeholder="`Option ${optionIndex + 1} in English`"
              required
            />
          </FormField>

        </div>
        <FormField class=" mr-1" label="الدرجة">
            <input 
              v-model="option.score_value"
              type="number"
              min="0"
              max="10"
              class="input w-20 text-sm sm:text-base"
              placeholder="0-10"
              required
            />
          </FormField>
          <button 
              type="button"
              @click="$emit('removeOption', { questionIndex, optionIndex })"
              class="text-red-500 hover:text-red-300  w-40 mt-4 rounded transition-colors text-sm"
              :disabled="question.options?.length === 1"
              title="حذف الخيار"
            >
              <i class="fas fa-times"></i>حذف الخيار
            </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import FormField from './FormField.vue';

import type { Question } from './types';

defineProps<{
  question: Question;
  questionIndex: number;
}>();

defineEmits<{
  addOption: [];
  removeOption: [payload: { questionIndex: number; optionIndex: number }];
}>();
</script>