<template>
  <div class="text-center py-12 bg-primary rounded-lg border border-primary">
    <div class="text-secondary mb-4">
      <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
      </svg>
    </div>
    <h3 class="text-lg font-medium text-primary mb-2">لا توجد مرضى</h3>
    <p class="text-secondary mb-4">لم يتم إضافة أي مرضى بعد</p>
    <button 
      @click="$emit('create-patient')"
      class="bg-brand-500 hover:bg-[#8FAE2F] text-white px-6 py-2 rounded-lg inline-flex items-center gap-2"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      إضافة أول مريض
    </button>
  </div>
</template>

<script setup>
defineEmits(['create-patient'])
</script>