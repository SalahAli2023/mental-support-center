<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3"
    @click.self="$emit('cancel')">
    <div class="w-full max-w-md rounded-xl border border-primary bg-primary p-4 shadow-lg mx-3">

      <!-- العنوان والنص -->
      <h3 class="text-lg font-semibold text-primary mb-2">
        تأكيد الحذف
      </h3>

      <p class="text-secondary mb-4 text-sm sm:text-base">
        {{ message || 'هل أنت متأكد من رغبتك في حذف هذا العنصر؟ لا يمكن التراجع عن هذا الإجراء.' }}
      </p>

      <!-- الأزرار -->
      <div class="flex flex-col sm:flex-row justify-end gap-2">
        <Button variant="outline" @click="$emit('cancel')" type="button" class="w-full sm:w-auto">
          إلغاء
        </Button>

        <Button variant="primary" @click="$emit('confirm')" type="button"
          class="bg-accent-500 hover:bg-accent-500 text-white w-full sm:w-auto">
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
  message?: string;
}>();

defineEmits<{
  confirm: [];
  cancel: [];
}>();
</script>
