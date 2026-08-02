<template>
  <div class="mt-6 p-3 sm:p-4 border border-gray-200 rounded-lg">
    <h4 class="text-sm font-medium text-primary mb-3">معاينة شريط التفسيرات</h4>
    <div class="flex items-center h-8 bg-gray-100 rounded-lg overflow-hidden">
      <div 
        v-for="(interpretation, index) in sortedInterpretations"
        :key="index"
        :class="[
          'h-full flex items-center justify-center text-xs font-medium text-white transition-all',
          getColorClass(interpretation.color)
        ]"
        :style="{ width: getInterpretationWidth(interpretation) + '%' }"
        :title="`${interpretation.interpretation_label_ar}: ${interpretation.min_score}-${interpretation.max_score}`"
      >
        <span class="hidden sm:inline">{{ interpretation.interpretation_label_ar }}</span>
        <span class="sm:hidden">•</span>
      </div>
    </div>
    <div class="flex justify-between text-xs text-gray-500 mt-1">
      <span>0</span>
      <span>{{ maxScore }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

import type { Interpretation } from './types';

const props = defineProps<{
  interpretations: Interpretation[];
  maxScore: number;
}>();

const sortedInterpretations = computed(() => {
  return [...props.interpretations].sort((a, b) => a.min_score - b.min_score);
});

function getInterpretationWidth(interpretation: Interpretation) {
  const range = interpretation.max_score - interpretation.min_score;
  return (range / props.maxScore) * 100;
}

function getColorClass(color: string) {
  const colorClasses = {
    green: 'bg-green-500 text-white',
    blue: 'bg-blue-500 text-white',
    yellow: 'bg-yellow-500 text-white',
    orange: 'bg-orange-500 text-white',
    red: 'bg-red-500 text-white'
  };
  return colorClasses[color as keyof typeof colorClasses] || 'bg-gray-500 text-white';
}
</script>