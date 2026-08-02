<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-primary mb-2">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <slot />
  </div>
</template>

<script setup lang="ts">
defineProps<{
  label?: string;
  required?: boolean;
}>();
</script>