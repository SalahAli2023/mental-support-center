<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2 sm:p-3">
    <div class="w-full max-w-4xl max-h-[95vh] sm:max-h-[90vh] overflow-y-auto rounded-xl border border-primary bg-primary p-4 sm:p-6 shadow-lg">
      <ModalHeader
        :title="`معاينة المقياس: ${previewData?.name_ar}`"
        @close="$emit('close')"
      />

      <div class="space-y-4 sm:space-y-6">
        <!-- معلومات المقياس -->
        <ScalePreviewInfo :preview-data="previewData" />
        
        <!-- تفسير النتائج -->
        <ScalePreviewInterpretations :preview-data="previewData" />
        
        <!-- الأسئلة -->
        <ScalePreviewQuestions :preview-data="previewData" />
      </div>

      <div class="mt-6 flex justify-end">
        <Button variant="outline" @click="$emit('close')" class="w-full sm:w-auto">
          إغلاق
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import ModalHeader from './ModalHeader.vue';
import ScalePreviewInfo from './ScalePreviewInfo.vue';
import ScalePreviewInterpretations from './ScalePreviewInterpretations.vue';
import ScalePreviewQuestions from './ScalePreviewQuestions.vue';
import Button from '@/components/dashboard/component/ui/Button.vue';

import type { Scale } from './types';

defineProps<{
  show: boolean;
  previewData: Scale | null;
}>();

defineEmits<{
  close: [];
}>();
</script>