<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3">
    <div class="w-full max-w-md rounded-xl border border-primary bg-white p-4 shadow-lg">
      <h3 class="text-lg font-semibold text-primary mb-2">تأكيد الحذف</h3>
      <p class="text-secondary mb-4">
        {{ message || `هل أنت متأكد من رغبتك في حذف "${itemName}"؟ لا يمكن التراجع عن هذا الإجراء.` }}
      </p>
      <div class="flex justify-end gap-2">
        <button @click="$emit('close')" class="btn btn-ghost">إلغاء</button>
        <button @click="$emit('confirm')" class="btn btn-danger">حذف</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  open: boolean
  itemName?: string
  message?: string
}

defineProps<Props>()
defineEmits<{
  close: []
  confirm: []
}>()
</script>




