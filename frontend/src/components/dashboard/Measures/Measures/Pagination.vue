<template>
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-primary">
    <div class="text-sm text-secondary order-2 sm:order-1">
      عرض {{ startIndex + 1 }} - {{ endIndex }} من {{ totalItems }} مقياس
    </div>
    
    <div class="flex items-center gap-2 order-1 sm:order-2">
      <button 
        @click="$emit('prevPage')"
        :disabled="currentPage === 1"
        :class="['p-2 rounded-lg border border-primary text-primary hover:bg-secondary transition-colors text-sm', currentPage === 1 ? 'opacity-50 cursor-not-allowed' : '']"
      >
        السابق
      </button>
      
      <div class="flex gap-1">
        <button 
          v-for="page in visiblePages" 
          :key="page"
          @click="$emit('pageChange', page)"
          :class="['w-7 h-7 sm:w-8 sm:h-8 rounded-lg text-xs sm:text-sm font-medium transition-colors', page === currentPage ? 'bg-brand-500 text-white' : 'border border-primary text-primary hover:bg-secondary']"
        >
          {{ page }}
        </button>
      </div>

      <button 
        @click="$emit('nextPage')"
        :disabled="currentPage === totalPages"
        :class="['p-2 rounded-lg border border-primary text-primary hover:bg-secondary transition-colors text-sm', currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : '']"
      >
        التالي
      </button>
    </div>
    
    <div class="flex items-center gap-2 text-sm text-secondary order-3">
      <label class="whitespace-nowrap">عدد العناصر:</label>
      <select 
        :value="itemsPerPage"
        @change="$emit('update:itemsPerPage', parseInt(($event.target as HTMLSelectElement).value))"
        class="input text-sm py-1 px-2 w-16 sm:w-20"
      >
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
      </select>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  currentPage: number;
  totalPages: number;
  startIndex: number;
  endIndex: number;
  itemsPerPage: number;
  visiblePages: (number | string)[];
  totalItems: number;
}>();

defineEmits<{
  pageChange: [page: number | string];
  prevPage: [];
  nextPage: [];
  'update:itemsPerPage': [value: number];
}>();
</script>