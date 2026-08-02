<template>
  <Card>
    <template #header>
      معلومات المقياس
    </template>
    <div class="space-y-3">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
        <PreviewField label="الاسم:" :value="previewData?.name_ar" />
        <PreviewField label="الفئة:" :value="getCategoryName(previewData?.category_id)" />
        <PreviewField label="عدد الأسئلة:" :value="(previewData?.questions_count || previewData?.questions?.length || 0).toString()" />
        <PreviewField label="الدرجة القصوى:" :value="previewData?.max_score?.toString()" />
      </div>
      <div v-if="previewData?.description_ar" class="mt-3">
        <PreviewField label="الوصف:" :value="previewData.description_ar" />
      </div>
    </div>
  </Card>
</template>

<script setup lang="ts">
import Card from '@/components/dashboard/component/ui/Card.vue';
import PreviewField from './PreviewField.vue';
import { useScalesStore } from '@/stores/scales.ts';

import type { Scale } from './types';

const scalesStore = useScalesStore();

defineProps<{
  previewData: Scale | null;
}>();

function getCategoryName(categoryId?: string) {
  if (!categoryId) return 'غير معروف';
  return scalesStore.getCategoryName(categoryId);
}
</script>