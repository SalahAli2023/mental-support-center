<template>
  <div class="mb-3 flex items-center justify-between">
    <div class="text-base sm:text-lg font-semibold text-primary">{{ title }}</div>
    <button class="inline-grid h-8 w-8 sm:h-9 sm:w-9 place-items-center rounded-lg hover:bg-tertiary text-primary" @click="$emit('close')">
      ✕
    </button>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string;
}>();

defineEmits<{
  close: [];
}>();
</script>