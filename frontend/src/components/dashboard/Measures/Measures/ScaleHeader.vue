<template>
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
    <h1 class="text-xl sm:text-2xl font-semibold text-primary">المقاييس النفسية</h1>
    <Button variant="primary" @click="$emit('create')" class="w-full sm:w-auto">
      إضافة مقياس جديد
    </Button>
  </div>
</template>

<script setup lang="ts">
import Button from '@/components/dashboard/component/ui/Button.vue';

defineEmits<{
  create: [];
}>();
</script>