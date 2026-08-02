<template>
  <div class="mb-4 sm:mb-6">
    <div class="flex items-center justify-center space-x-4 sm:space-x-8 rtl:space-x-reverse overflow-x-auto pb-2">
      <div v-for="(step, index) in steps" :key="step.id" class="flex items-center flex-shrink-0">
        <div class="flex flex-col items-center">
          <div 
            :class="[
              'w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold border-2 transition-all duration-300',
              currentStep > index ? 'bg-brand-500 border-brand-500 text-white' :
              currentStep === index ? 'border-brand-500 text-brand-500 bg-white' :
              'border-gray-300 text-gray-500 bg-white'
            ]"
          >
            {{ currentStep > index ? '✓' : index + 1 }}
          </div>
          <span 
            :class="[
              'text-xs mt-1 font-medium transition-colors duration-300 text-center',
              currentStep >= index ? 'text-brand-500' : 'text-gray-500'
            ]"
          >
            {{ step.title }}
          </span>
        </div>
        <div 
          v-if="index < steps.length - 1"
          :class="[
            'w-6 sm:w-12 h-1 mx-1 sm:mx-2 transition-colors duration-300',
            currentStep > index ? 'bg-brand-500' : 'bg-gray-300'
          ]"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const steps = [
  { id: 1, title: 'معلومات المقياس' },
  { id: 2, title: 'الأسئلة' },
  { id: 3, title: 'تفسير النتائج' }
];

defineProps<{
  currentStep: number;
}>();
</script>