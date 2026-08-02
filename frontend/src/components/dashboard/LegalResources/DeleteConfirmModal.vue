<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3" @click.self="$emit('cancel')">
    <div class="w-full max-w-md rounded-xl border border-primary bg-primary p-4 shadow-lg">
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-primary">تأكيد الحذف</h3>
        <p class="text-sm text-secondary mt-1">هل أنت متأكد من أنك تريد حذف هذا المورد؟ لا يمكن التراجع عن هذا الإجراء.</p>
      </div>
      
      <div class="flex justify-end gap-2">
        <Button variant="outline" @click="$emit('cancel')" type="button">إلغاء</Button>
        <Button variant="primary" @click="$emit('confirm')" type="button" class="bg-red-600 hover:bg-red-700">
          حذف
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from '@/components/dashboard/component/ui/Button.vue'

defineProps<{
  show: boolean
}>()

defineEmits<{
  confirm: []
  cancel: []
}>()
</script>