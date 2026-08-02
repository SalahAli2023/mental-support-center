<template>
  <div class="card card-secondary p-3 sm:p-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 items-start">
      <!-- المدى -->
      <div class="md:col-span-3">
        <FormField label="المدى *">
          <div class="flex gap-2 items-center">
            <input 
              v-model="interpretation.min_score"
              type="number"
              class="input text-center text-sm sm:text-base"
              :class="{ 'border-red-500': showErrors && (interpretation.min_score === undefined || interpretation.min_score === null) }"
              placeholder="من"
              min="0"
              :max="maxScore"
              required
            />
            <span class="text-gray-500">-</span>
            <input 
              v-model="interpretation.max_score"
              type="number"
              class="input text-center text-sm sm:text-base"
              :class="{ 'border-red-500': showErrors && (interpretation.max_score === undefined || interpretation.max_score === null) }"
              placeholder="إلى"
              :min="interpretation.min_score"
              :max="maxScore"
              required
            />
          </div>
          <p v-if="showErrors && (interpretation.min_score === undefined || interpretation.min_score === null)" class="text-red-500 text-xs mt-1">الحد الأدنى مطلوب</p>
          <p v-if="showErrors && (interpretation.max_score === undefined || interpretation.max_score === null)" class="text-red-500 text-xs mt-1">الحد الأقصى مطلوب</p>
          <p v-if="showErrors && interpretation.min_score !== undefined && interpretation.max_score !== undefined && interpretation.max_score <= interpretation.min_score" class="text-red-500 text-xs mt-1">الحد الأقصى يجب أن يكون أكبر من الحد الأدنى</p>
        </FormField>
      </div>

      <!-- التصنيف -->
      <div class="md:col-span-2">
        <FormField label="التصنيف *">
          <input 
            v-model="interpretation.interpretation_label_ar"
            type="text"
            class="input text-sm sm:text-base"
            :class="{ 'border-red-500': showErrors && !interpretation.interpretation_label_ar && !interpretation.interpretation_label_en }"
            placeholder="مثل: منخفض"
          />
          <p v-if="showErrors && !interpretation.interpretation_label_ar && !interpretation.interpretation_label_en" class="text-red-500 text-xs mt-1">التصنيف (العربية أو الإنجليزية) مطلوب</p>
        </FormField>
      </div>

      <!-- التصنيف الإنجليزي -->
      <div class="md:col-span-2">
        <FormField label="التصنيف (الإنجليزية)">
          <input 
            v-model="interpretation.interpretation_label_en"
            type="text"
            class="input text-sm sm:text-base"
            placeholder="e.g., Low"
          />
        </FormField>
      </div>

      <!-- اللون -->
      <div class="md:col-span-2">
        <FormField label="لون المؤشر">
          <select v-model="interpretation.color" class="input text-sm sm:text-base">
            <option value="green">أخضر (منخفض)</option>
            <option value="blue">أزرق (طبيعي)</option>
            <option value="yellow">أصفر (متوسط)</option>
            <option value="orange">برتقالي (مرتفع)</option>
            <option value="red">أحمر (مرتفع جداً)</option>
          </select>
        </FormField>
      </div>

      <!-- التفسير -->
      <div class="md:col-span-2">
        <FormField label="التفسير">
          <textarea 
            v-model="interpretation.description_ar"
            rows="2"
            class="input text-sm sm:text-base"
            :placeholder="`تفسير النتيجة للمدى ${interpretation.min_score}-${interpretation.max_score}`"
          ></textarea>
        </FormField>
      </div>

      <!-- زر الحذف -->
      <div class="md:col-span-1 flex justify-center pt-6">
        <button 
          @click="$emit('remove')"
          class="text-accent-500 hover:text-accent-500 p-2 rounded transition-colors"
          :disabled="!canRemove"
        >
          ×
        </button>
      </div>
    </div>

    <!-- معاينة المؤشر -->
    <div class="mt-3 p-3 bg-gray-50 rounded-lg">
      <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
        <span class="text-sm text-gray-600 whitespace-nowrap">معاينة:</span>
        <span 
          :class="[
            'badge text-sm font-medium',
            getColorClass(interpretation.color)
          ]"
        >
          {{ interpretation.interpretation_label_ar || interpretation.interpretation_label_en || 'تصنيف' }}: {{ interpretation.min_score }}-{{ interpretation.max_score }}
        </span>
        <span class="text-sm text-gray-600">{{ interpretation.description_ar }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import FormField from './FormField.vue';

import type { Interpretation } from './types';

defineProps<{
  interpretation: Interpretation;
  index: number;
  maxScore: number;
  canRemove: boolean;
  showErrors: boolean;
}>();

defineEmits<{
  remove: [];
}>();

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