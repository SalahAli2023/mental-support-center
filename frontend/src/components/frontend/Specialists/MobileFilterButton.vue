<template>
  <div class="lg:hidden mb-4">
    <button 
      @click="$emit('toggle')"
      class="w-full flex items-center justify-between p-3 bg-primary-pink text-white rounded-xl shadow hover:shadow-md transition-all"
    >
      <span class="flex items-center gap-2 font-bold text-base sm:text-lg">
        <i class="fas fa-sliders-h"></i>
        {{ translate('therapists.filters.title') }}
        <span v-if="isAnyFilterActive" class="bg-white text-primary-pink text-xs px-2 py-1 rounded-full">
          {{ activeFiltersCount }}
        </span>
      </span>
      <i class="fas fa-chevron-down transition-transform" :class="{ 'rotate-180': showMobileFilter }"></i>
    </button>
  </div>
</template>

<script>
export default {
  name: 'MobileFilterButton',
  props: {
    showMobileFilter: Boolean,
    isAnyFilterActive: Boolean,
    activeFiltersCount: Number,
    translate: Function
  },
  emits: ['toggle']
}
</script>