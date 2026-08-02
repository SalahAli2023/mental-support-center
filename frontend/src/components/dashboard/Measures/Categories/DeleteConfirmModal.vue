<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3">
    <div class="w-full max-w-md rounded-xl border border-primary bg-primary p-4 shadow-lg mx-3">
      <h3 class="text-lg font-semibold text-primary mb-2">تأكيد الحذف</h3>
      <p class="text-secondary mb-4 text-sm sm:text-base">
        هل أنت متأكد من رغبتك في حذف هذا التصنيف؟ لا يمكن التراجع عن هذا الإجراء.
      </p>
      <div class="flex flex-col sm:flex-row justify-end gap-2">
        <Button variant="outline" @click="$emit('cancel')" class="w-full sm:w-auto">
          إلغاء
        </Button>
        <Button 
          variant="primary" 
          class="bg-accent-500 hover:bg-accent-500 text-white w-full sm:w-auto" 
          @click="$emit('confirm')"
        >
          حذف
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from '@/components/dashboard/component/ui/Button.vue';

defineProps<{
  show: boolean;
}>();

defineEmits<{
  confirm: [];
  cancel: [];
}>();
</script>