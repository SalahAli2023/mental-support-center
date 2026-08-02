<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-primary mb-2">
      {{ label }}
    </label>
    <slot />
  </div>
</template>

<script setup lang="ts">
defineProps<{
  label?: string;
}>();
</script>