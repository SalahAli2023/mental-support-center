<template>
  <Card>
    <template #header>
      الأسئلة ({{ previewData?.questions?.length || 0 }})
    </template>
    <div class="space-y-3 sm:space-y-4">
      <div 
        v-for="(question, index) in previewData?.questions" 
        :key="index" 
        class="border border-primary rounded-lg p-3 sm:p-4"
      >
        <h4 class="font-medium text-primary mb-2 sm:mb-3 text-sm sm:text-base">
          سؤال {{ index + 1 }}: {{ question.question_text_ar }}
        </h4>
        <div class="space-y-2">
          <div 
            v-for="(option, optIndex) in question.options" 
            :key="optIndex" 
            class="flex items-center gap-2 sm:gap-3 p-2 bg-secondary rounded text-xs sm:text-sm"
          >
            <span class="text-primary">{{ option.option_text_ar }}</span>
            <span class="text-gray-500 whitespace-nowrap">({{ option.score_value }} نقطة)</span>
          </div>
        </div>
      </div>
    </div>
  </Card>
</template>

<script setup lang="ts">
import Card from '@/components/dashboard/component/ui/Card.vue';

import type { Scale } from './types';

defineProps<{
  previewData: Scale | null;
}>();
</script>