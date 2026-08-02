<template>
  <div class="flex justify-center mt-8">
    <div class="flex flex-col items-center space-y-6" :dir="isRTL ? 'rtl' : 'ltr'">
      <!-- Page Buttons -->
      <div class="flex items-center" :class="isRTL ? 'space-x-2 space-x-reverse' : 'space-x-2'">
        <!-- Previous Page Button -->
        <button
          @click="$emit('previousPage')"
          :disabled="currentPage === 1"
          :class="[
            'pagination-btn',
            'prev-next-btn',
            currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary hover:text-white'
          ]"
        >
          <i :class="isRTL ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
        </button>

        <!-- Page Numbers -->
        <button
          v-for="page in visiblePages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'pagination-btn',
            'page-number',
            page === currentPage 
              ? 'active-page' 
              : 'inactive-page hover:bg-gray-100'
          ]"
        >
          {{ page }}
        </button>

        <!-- Next Page Button -->
        <button
          @click="$emit('nextPage')"
          :disabled="currentPage === totalPages"
          :class="[
            'pagination-btn',
            'prev-next-btn',
            currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary hover:text-white'
          ]"
        >
          <i :class="isRTL ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"></i>
        </button>
      </div>

      <!-- Progress Dots -->
      <div class="flex" :class="isRTL ? 'space-x-1 space-x-reverse' : 'space-x-1'">
        <div
          v-for="page in totalPages"
          :key="page"
          :class="[
            'h-1 rounded-full transition-all duration-300 cursor-pointer',
            page === currentPage 
              ? 'bg-primary w-6' 
              : 'bg-gray-300 w-2 hover:bg-gray-400'
          ]"
          @click="goToPage(page)"
        ></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TherapistPagination',
  props: {
    currentPage: Number,
    totalPages: Number,
    visiblePages: Array,
    isRTL: Boolean
  },
  emits: ['previousPage', 'nextPage', 'goToPage'],
  methods: {
    goToPage(page) {
      if (page !== '...' && page >= 1 && page <= this.totalPages) {
        this.$emit('goToPage', page)
      }
    }
  }
}
</script>

<style scoped>

</style>