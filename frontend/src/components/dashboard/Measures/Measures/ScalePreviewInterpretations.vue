<template>
  <Card>
    <template #header>
      تفسير النتائج
    </template>
    <div class="space-y-4">
      <!-- شريط التفسيرات -->
      <div class="p-3 sm:p-4 border border-gray-200 rounded-lg">
        <div class="flex items-center h-8 sm:h-10 bg-gray-100 rounded-lg overflow-hidden">
          <div 
            v-for="(interpretation, index) in previewData?.interpretations"
            :key="index"
            :class="[
              'h-full flex items-center justify-center text-xs sm:text-sm font-medium text-white',
              getColorClass(interpretation.color)
            ]"
            :style="{ width: getInterpretationWidth(interpretation) + '%' }"
          >
            <span class="hidden xs:inline">{{ interpretation.interpretation_label_ar }}</span>
            <span class="xs:hidden">•</span>
          </div>
        </div>
        <div class="flex justify-between text-xs sm:text-sm text-gray-500 mt-2">
          <span>0</span>
          <span>{{ previewData?.max_score }}</span>
        </div>
      </div>

      <!-- جدول التفسيرات -->
      <div class="overflow-x-auto -mx-2 sm:mx-0">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="bg-secondary">
              <th class="px-2 sm:px-4 py-2 text-start font-medium text-primary text-xs sm:text-sm">المدى</th>
              <th class="px-2 sm:px-4 py-2 text-start font-medium text-primary text-xs sm:text-sm">التصنيف</th>
              <th class="px-2 sm:px-4 py-2 text-start font-medium text-primary text-xs sm:text-sm">التفسير</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(interpretation, index) in previewData?.interpretations" :key="index" class="border-t border-primary">
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm">
                {{ interpretation.min_score }} - {{ interpretation.max_score }}
              </td>
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm">
                <span :class="['badge text-xs', getColorClass(interpretation.color)]">
                  {{ interpretation.interpretation_label_ar }}
                </span>
              </td>
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm">
                {{ interpretation.description_ar }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </Card>
</template>

<script setup lang="ts">
import Card from '@/components/dashboard/component/ui/Card.vue';

import type { Scale, Interpretation } from './types';

defineProps<{
  previewData: Scale | null;
}>();

function getInterpretationWidth(interpretation: Interpretation) {
  if (!interpretation) return 0;
  const range = interpretation.max_score - interpretation.min_score;
  return (range / (interpretation.max_score || 1)) * 100;
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