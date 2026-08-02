<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2 sm:p-3">

    <div class="w-full max-w-6xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto rounded-xl border border-primary bg-primary p-3 sm:p-4 shadow-lg">
      <!-- الهيدر -->
      <ModalHeader
        :title="current ? 'تعديل المقياس' : 'إضافة مقياس جديد'"
        @close="$emit('close')"
      />
      
      <!-- خطوات التقدم -->
      <ProgressSteps :current-step="currentStep" />
      
      <!-- محتوى النموذج -->
      <div class="modal-content">
        <!-- الخطوة 1: معلومات المقياس -->
        <ScaleInfoStep
          v-if="currentStep === 1"
          :form="form"
          :submitted="submitted"
          @image-upload="$emit('imageUpload', $event)"
          @image-remove="$emit('imageRemove')"
        />
        
        <!-- الخطوة 2: الأسئلة -->
        <QuestionsStep
          v-else-if="currentStep === 2"
          :questions="form.questions"
          @add-question="$emit('addQuestion')"
          @remove-question="$emit('removeQuestion', $event)"
          @add-option="$emit('addOption', $event)"
          @remove-option="$emit('removeOption', $event)"
        />
        
        <!-- الخطوة 3: تفسير النتائج -->
        <InterpretationsStep
          v-else-if="currentStep === 3"
          :interpretations="form.interpretations"
          :max-score="form.max_score"
          @add-interpretation="$emit('addInterpretation')"
          @remove-interpretation="$emit('removeInterpretation', $event)"
        />
      </div>

      <!-- أزرار التنقل -->
      <ModalActions
        :current-step="currentStep"
        :total-steps="3"
        @prev-step="$emit('prevStep')"
        @next-step="$emit('nextStep')"
        @close="$emit('close')"
        @save="$emit('save')"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import ModalHeader from './ModalHeader.vue';
import ProgressSteps from './ProgressSteps.vue';
import ScaleInfoStep from './ScaleInfoStep.vue';
import QuestionsStep from './QuestionsStep.vue';
import InterpretationsStep from './InterpretationsStep.vue';
import ModalActions from './ModalActions.vue';

import type { Scale, ScaleForm } from './types';

defineProps<{
  show: boolean;
  current: Scale | null;
  currentStep: number;
  form: ScaleForm;
  submitted: boolean;
}>();

defineEmits<{
  close: [];
  prevStep: [];
  nextStep: [];
  save: [];
  addQuestion: [];
  removeQuestion: [index: number];
  addOption: [questionIndex: number];
  removeOption: [payload: { questionIndex: number; optionIndex: number }];
  addInterpretation: [];
  removeInterpretation: [index: number];
  imageUpload: [event: Event];
  imageRemove: [];
}>();
</script>