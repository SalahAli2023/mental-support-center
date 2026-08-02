<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3">
    <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-4 shadow-lg mx-3">
      <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ title }}</h3>
      <p class="text-gray-600 mb-4 text-sm sm:text-base">
        {{ message }}
      </p>
      <div class="flex flex-col sm:flex-row justify-end gap-2">
        <Button variant="outline" @click="$emit('cancel')" class="w-full sm:w-auto">
          إلغاء
        </Button>
        <Button 
          variant="primary" 
          class="bg-red-500 hover:bg-red-600 text-white w-full sm:w-auto" 
          @click="$emit('confirm')"
        >
          حذف
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from '@/components/dashboard/component/ui/Button.vue';

defineProps({
  open: {
    type: Boolean,
    required: true
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  }
})

defineEmits(['confirm', 'cancel'])
</script>