<template>
  <div v-if="totalItems > 0" class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-primary rounded-lg border border-primary">
    <!-- معلومات الصفحة -->
    <div class="text-sm text-secondary">
      عرض {{ startItem }} - {{ endItem }} من إجمالي {{ totalItems }} مريض
    </div>

    <!-- أزرار التنقل - في المنتصف -->
    <div class="flex items-center gap-1 order-first sm:order-none">
      <!-- زر الصفحة السابقة -->
      <button 
        @click="$emit('go-to-page', currentPage - 1)"
        :disabled="currentPage === 1"
        :class="[
          'p-2 rounded-lg border transition-colors',
          currentPage === 1 
            ? 'bg-tertiary text-secondary cursor-not-allowed border-primary' 
            : 'bg-primary text-primary hover:bg-tertiary border-primary'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </button>

      <!-- أرقام الصفحات -->
      <div class="flex gap-1">
        <button 
          v-for="page in visiblePages" 
          :key="page"
          @click="page !== '...' && $emit('go-to-page', page)"
          :class="[
            'w-8 h-8 rounded-lg text-sm font-medium transition-colors border',
            page === currentPage
              ? 'bg-brand-500 text-white border-brand-500'
              : 'bg-primary text-primary hover:bg-tertiary border-primary',
            page === '...' ? 'cursor-default border-transparent' : 'cursor-pointer'
          ]"
        >
          {{ page }}
        </button>
      </div>

      <!-- زر الصفحة التالية -->
      <button 
        @click="$emit('go-to-page', currentPage + 1)"
        :disabled="currentPage === totalPages"
        :class="[
          'p-2 rounded-lg border transition-colors',
          currentPage === totalPages
            ? 'bg-tertiary text-secondary cursor-not-allowed border-primary' 
            : 'bg-primary text-primary hover:bg-tertiary border-primary'
        ]"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </button>
    </div>

    <!-- اختيار عدد العناصر -->
    <div class="flex items-center gap-2">
      <span class="text-sm text-secondary">عرض</span>
      <select 
        :value="itemsPerPage"
        @change="$emit('update-items-per-page', $event.target.value)"
        class="px-3 py-1 rounded-lg border border-primary bg-primary text-primary text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
      >
        <option value="6">6</option>
        <option value="9">9</option>
        <option value="12">12</option>
        <option value="15">15</option>
      </select>
      <span class="text-sm text-secondary">لكل صفحة</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  currentPage: {
    type: Number,
    required: true
  },
  totalPages: {
    type: Number,
    required: true
  },
  startItem: {
    type: Number,
    required: true
  },
  endItem: {
    type: Number,
    required: true
  },
  totalItems: {
    type: Number,
    required: true
  },
  itemsPerPage: {
    type: Number,
    required: true
  },
  visiblePages: {
    type: Array,
    required: true
  }
})

defineEmits(['go-to-page', 'update-items-per-page'])
</script>