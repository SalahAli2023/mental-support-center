<template>
  <div class="flex flex-col items-center space-y-6" :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'">
    <!-- معلومات الصفحة -->
    <div class="text-gray-600 text-sm mb-4">
      {{ showingText }}
    </div>

    <!-- أزرار الصفحات -->
    <div class="flex items-center gap-2" :class="currentLanguage === 'ar' ? 'flex-row-reverse' : ''">
      <!-- زر الصفحة السابقة -->
      <button
        @click="goToPage(currentPage - 1)"
        :disabled="currentPage === 1"
        class="flex items-center justify-center w-10 h-10 rounded-xl font-semibold text-sm transition-all duration-300 ease-in-out border-2 border-transparent disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[#9EBF3B] hover:text-white"
        :class="currentPage === 1 ? 'bg-gray-100 text-gray-400' : 'bg-white text-gray-700 border-gray-200 hover:border-[#9EBF3B]'"
      >
        <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
      </button>

      <!-- أرقام الصفحات -->
      <button
        v-for="page in visiblePages"
        :key="page"
        @click="goToPage(page)"
        class="flex items-center justify-center w-10 h-10 rounded-xl font-semibold text-sm transition-all duration-300 ease-in-out border-2"
        :class="page === currentPage 
          ? 'bg-gradient-to-r from-[#9EBF3B] to-[#8aaa2f] text-white transform scale-110 shadow-lg shadow-[#9EBF3B]/30' 
          : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50 hover:border-[#9EBF3B] hover:-translate-y-0.5 hover:shadow-md'"
      >
        {{ page }}
      </button>

      <!-- زر الصفحة التالية -->
      <button
        @click="goToPage(currentPage + 1)"
        :disabled="currentPage === totalPages"
        class="flex items-center justify-center w-10 h-10 rounded-xl font-semibold text-sm transition-all duration-300 ease-in-out border-2 border-transparent disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[#9EBF3B] hover:text-white"
        :class="currentPage === totalPages ? 'bg-gray-100 text-gray-400' : 'bg-white text-gray-700 border-gray-200 hover:border-[#9EBF3B]'"
      >
        <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"></i>
      </button>
    </div>

    <!-- نقاط التقدم -->
    <div class="flex gap-1" :class="currentLanguage === 'ar' ? 'flex-row-reverse' : ''">
      <div
        v-for="page in totalPages"
        :key="page"
        class="h-1 rounded-full transition-all duration-300 cursor-pointer"
        :class="page === currentPage 
          ? 'bg-[#9EBF3B] w-6' 
          : 'bg-gray-300 w-2 hover:bg-gray-400'"
        @click="goToPage(page)"
      ></div>
    </div>
  </div>
</template>

<script>
import { useTranslations } from '@/composables/useTranslations'
import { computed } from 'vue'

export default {
  name: 'Pagination',
  props: {
    currentPage: {
      type: Number,
      required: true
    },
    totalPages: {
      type: Number,
      required: true
    },
    totalItems: {
      type: Number,
      required: true
    },
    itemsPerPage: {
      type: Number,
      default: 6
    }
  },
  setup(props) {
    const { translate, currentLanguage } = useTranslations()
    
    const showingText = computed(() => {
      const start = (props.currentPage - 1) * props.itemsPerPage + 1
      const end = Math.min(props.currentPage * props.itemsPerPage, props.totalItems)
      
      if (currentLanguage.value === 'ar') {
        return `عرض ${start}-${end} من أصل ${props.totalItems} مقالة`
      } else {
        return `Showing ${start}-${end} of ${props.totalItems} articles`
      }
    })

    return {
      showingText,
      currentLanguage
    }
  },
  computed: {
    startItem() {
      return (this.currentPage - 1) * this.itemsPerPage + 1;
    },
    endItem() {
      const end = this.currentPage * this.itemsPerPage;
      return end > this.totalItems ? this.totalItems : end;
    },
    visiblePages() {
      const pages = [];
      const maxVisible = 5;
      
      let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
      let end = Math.min(this.totalPages, start + maxVisible - 1);
      
      if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
      }
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    }
  },
  methods: {
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.$emit('page-change', page);
      }
    }
  },
  emits: ['page-change']
}
</script>