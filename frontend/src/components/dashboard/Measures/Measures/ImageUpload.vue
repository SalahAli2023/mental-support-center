<template>
  <div>
    <label class="block text-sm font-medium text-primary mb-2">صورة المقياس</label>
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
      <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg border-2 border-dashed border-primary bg-secondary flex items-center justify-center overflow-hidden flex-shrink-0">
        <img 
          v-if="image" 
          :src="image" 
          alt="صورة المقياس"
          class="w-full h-full object-cover"
        />
        <div v-else class="text-secondary text-xs sm:text-sm text-center p-2">
          لا توجد صورة
        </div>
      </div>
      <div class="flex-1 w-full">
        <input 
          type="file" 
          accept="image/*"
          @change="$emit('upload', $event)"
          class="hidden"
          ref="fileInput"
        />
        <div class="flex flex-col sm:flex-row gap-2">
          <Button 
            variant="outline" 
            @click="$refs.fileInput.click()" 
            class="text-sm w-full sm:w-auto"
          >
            {{ image ? 'تغيير الصورة' : 'رفع صورة' }}
          </Button>
          <button 
            v-if="image"
            @click="$emit('remove')"
            class="text-sm text-accent-500 hover:text-accent-500 py-2 text-center sm:text-right"
          >
            إزالة الصورة
          </button>
        </div>
        <p class="text-xs text-gray-500 mt-1">
          اختياري - يجب أن يكون رابط URL صالحاً
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Button from '@/components/dashboard/component/ui/Button.vue';

defineProps<{
  image: string;
}>();

defineEmits<{
  upload: [event: Event];
  remove: [];
}>();

const fileInput = ref<HTMLInputElement>();
</script>