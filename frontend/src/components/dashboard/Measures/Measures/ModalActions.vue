<template>
  <div class="mt-6 flex flex-col sm:flex-row justify-between gap-3 pt-4 border-t border-primary">
    <Button 
      v-if="currentStep > 1" 
      variant="outline" 
      @click="$emit('prevStep')" 
      class="order-2 sm:order-1 w-full sm:w-auto"
    >
      ← السابق
    </Button>
    <div v-else class="order-2 sm:order-1"></div>

    <div class="flex flex-col sm:flex-row gap-2 order-1 sm:order-2 w-full sm:w-auto">
      <Button variant="outline" @click="$emit('close')" class="w-full sm:w-auto">
        إلغاء
      </Button>
      <Button 
        v-if="currentStep < totalSteps" 
        variant="primary" 
        @click="$emit('nextStep')" 
        class="w-full sm:w-auto"
      >
        التالي →
      </Button>
      <Button 
        v-else 
        variant="primary" 
        @click="$emit('save')" 
        class="w-full sm:w-auto"
      >
        حفظ المقياس
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from '@/components/dashboard/component/ui/Button.vue';

defineProps<{
  currentStep: number;
  totalSteps: number;
}>();

defineEmits<{
  prevStep: [];
  nextStep: [];
  close: [];
  save: [];
}>();
</script>