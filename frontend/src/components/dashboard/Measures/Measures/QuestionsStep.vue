<template>
  <div class="space-y-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-3">
      <label class="block text-sm font-medium text-primary">الأسئلة</label>
      <Button size="sm" variant="outline" @click="$emit('addQuestion')" class="w-full sm:w-auto">
        + إضافة سؤال
      </Button>
    </div>
    
    <div class="space-y-4">
      <QuestionItem
        v-for="(question, questionIndex) in questions"
        :key="questionIndex"
        :question="question"
        :index="questionIndex"
        :can-remove="questions.length > 1"
        :show-errors="showErrors"
        @remove="$emit('removeQuestion', questionIndex)"
        @add-option="$emit('addOption', questionIndex)"
        @remove-option="$emit('removeOption', $event)"
      />
    </div>

    <!-- 🔥 عرض الأخطاء العامة -->
    <div v-if="showErrors && hasGeneralErrors" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex items-center gap-2 text-red-700 mb-2">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        <span class="font-medium">يوجد أخطاء في البيانات</span>
      </div>
      <ul class="text-red-600 text-sm list-disc list-inside space-y-1">
        <li v-if="!hasQuestionsWithData">يجب إدخال بيانات لسؤال واحد على الأقل</li>
        <li v-if="hasQuestionsWithData && hasQuestionsWithoutOptions">يجب إدخال بيانات لخيار واحد على الأقل في كل سؤال</li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Button from '@/components/dashboard/component/ui/Button.vue';
import QuestionItem from './QuestionItem.vue';

import type { Question } from './types';

const props = defineProps<{
  questions: Question[];
  submitted: boolean;
  currentStep: number;
}>();

// 🔥 حساب متى تظهر الأخطاء
const showErrors = computed(() => {
  return props.submitted && props.currentStep === 2;
});

// 🔥 حساب إذا كان هناك أسئلة تحتوي على بيانات
const hasQuestionsWithData = computed(() => {
  return props.questions.some(q => q.question_text_ar?.trim() || q.question_text_en?.trim());
});

const hasQuestionsWithoutOptions = computed(() => {
  return props.questions.some(q => {
    const hasQuestionData = q.question_text_ar?.trim() || q.question_text_en?.trim();
    const hasOptionsWithData = q.options?.some(opt => opt.option_text_ar?.trim());
    return hasQuestionData && !hasOptionsWithData;
  });
});

const hasGeneralErrors = computed(() => {
  return !hasQuestionsWithData.value || hasQuestionsWithoutOptions.value;
});

defineEmits<{
  addQuestion: [];
  removeQuestion: [index: number];
  addOption: [questionIndex: number];
  removeOption: [payload: { questionIndex: number; optionIndex: number }];
}>();
</script>