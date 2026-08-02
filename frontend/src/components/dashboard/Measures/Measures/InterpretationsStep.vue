<template>
  <div class="space-y-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-3">
      <label class="block text-sm font-medium text-primary">تفسير النتائج</label>
      <Button size="sm" variant="outline" @click="$emit('addInterpretation')" class="w-full sm:w-auto">
        + إضافة مستوى تفسير
      </Button>
    </div>

    <InfoBox>
      <strong>ملاحظة:</strong> سيتم استخدام هذه التفسيرات لعرض نتائج المستخدمين بناءً على درجاتهم الإجمالية في المقياس.
    </InfoBox>

    <div class="space-y-4">
      <InterpretationItem
        v-for="(interpretation, index) in interpretations"
        :key="index"
        :interpretation="interpretation"
        :index="index"
        :max-score="maxScore"
        :can-remove="interpretations.length > 1"
        @remove="$emit('removeInterpretation', index)"
      />
    </div>

    <!-- معاينة جميع التفسيرات -->
    <InterpretationPreview
      :interpretations="interpretations"
      :max-score="maxScore"
    />
  </div>
</template>

<script setup lang="ts">
import Button from '@/components/dashboard/component/ui/Button.vue';
import InfoBox from './InfoBox.vue';
import InterpretationItem from './InterpretationItem.vue';
import InterpretationPreview from './InterpretationPreview.vue';

import type { Interpretation } from './types';

defineProps<{
  interpretations: Interpretation[];
  maxScore: number;
}>();

defineEmits<{
  addInterpretation: [];
  removeInterpretation: [index: number];
}>();
</script>