<template>
  <div class="card card-secondary p-3 sm:p-4">
    <div class="flex space-y-3 mb-3">
      <!-- نص السؤال -->
      <div class="grid grid-cols-2 md:grid-cols-2 gap-2 w-full">

        <FormField label="نص السؤال (العربية)">
          <input 
            v-model="question.question_text_ar"
            type="text"
            class="input text-sm sm:text-base"
            :placeholder="`السؤال ${index + 1} بالعربية`"
            required
          />
        </FormField>
        <FormField label="نص السؤال (الإنجليزية)">
          <input 
            v-model="question.question_text_en"
            type="text"
            class="input text-sm sm:text-base"
            :placeholder="`Question ${index + 1} in English`"
            required
          />
        </FormField>
      </div>
      <button 
        type="button"
        @click="$emit('remove')"
        class="text-red-500 hover:text-red-300 w-36 mt-10 rounded transition-colors self-start text-sm"
        :disabled="!canRemove"
        title="حذف السؤال"
      >
        <i class="fas fa-times"></i>حذف السؤال
      </button>
    </div>

    <!-- الخيارات -->
    <QuestionOptions
      :question="question"
      :question-index="index"
      @add-option="$emit('addOption')"
      @remove-option="$emit('removeOption', $event)"
    />
  </div>
</template>

<script setup lang="ts">
import FormField from './FormField.vue';
import QuestionOptions from './QuestionOptions.vue';

import type { Question } from './types';

defineProps<{
  question: Question;
  index: number;
  canRemove: boolean;
}>();

defineEmits<{
  remove: [];
  addOption: [];
  removeOption: [payload: { questionIndex: number; optionIndex: number }];
}>();
</script>