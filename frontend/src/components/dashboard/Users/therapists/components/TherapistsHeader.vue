<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="text-center sm:text-right">
      <h1 class="text-xl sm:text-2xl font-semibold text-primary">إدارة المعالجين</h1>
      <p class="text-xs sm:text-sm text-secondary mt-1">إدارة المعالجين وجداولهم اليومية</p>
    </div>
    <button 
      @click="$emit('create-therapist')"
      class="bg-brand-500 hover:bg-brand-600 text-white px-3 py-2 rounded-lg flex items-center gap-2 w-full sm:w-auto justify-center transition-colors text-sm sm:text-base"
    >
      <PlusIcon class="h-4 w-4" />
      إضافة معالج جديد
    </button>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline'

defineEmits(['create-therapist'])
</script>