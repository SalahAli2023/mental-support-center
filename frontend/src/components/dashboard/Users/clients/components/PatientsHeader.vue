<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="text-center sm:text-right">
      <h1 class="text-xl sm:text-2xl font-semibold text-primary">إدارة المرضى</h1>
      <p class="text-xs sm:text-sm text-secondary mt-1">إدارة وحفظ سجلات المرضى ومعلوماتهم الصحية</p>
    </div>
    
    <div class="flex gap-3">
      <button 
        @click="$emit('create-patient')"
        class="bg-brand-500 hover:bg-[#8FAE2F] text-white px-3 py-2 rounded-lg flex items-center gap-2 w-full sm:w-auto justify-center transition-colors text-sm sm:text-base"
      >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        إضافة مريض جديد
      </button>
      
      <button class="bg-tertiary hover:bg-primary text-primary px-3 py-2 rounded-lg flex items-center gap-2 w-full sm:w-auto justify-center border border-primary transition-colors text-sm sm:text-base">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        تصدير البيانات
      </button>
    </div>
  </div>
</template>

<script setup>
defineEmits(['create-patient'])
</script>