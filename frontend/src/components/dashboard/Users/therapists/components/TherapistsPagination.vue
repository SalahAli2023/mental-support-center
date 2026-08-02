<template>
  <div v-if="filteredTherapists.length > 0" class="flex flex-col sm:flex-row items-center justify-between gap-3 py-3">
    <!-- Page Info -->
    <div class="text-xs sm:text-sm text-secondary">
      عرض {{ startItem }}-{{ endItem }} من {{ filteredTherapists.length }} معالج
    </div>

    <!-- Pagination Controls -->
    <div class="flex items-center gap-2">
      <!-- Items Per Page -->
      <select 
        :value="pagination.itemsPerPage"
        @change="updateItemsPerPage($event.target.value)"
        class="rounded-lg border border-primary bg-primary px-2 py-1 text-xs sm:text-sm text-primary focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors"
      >
        <option value="5">5 لكل صفحة</option>
        <option value="10">10 لكل صفحة</option>
        <option value="20">20 لكل صفحة</option>
      </select>

      <!-- Pagination Buttons -->
      <div class="flex items-center gap-1">
        <button 
          @click="prevPage"
          :disabled="pagination.currentPage === 1"
          :class="[
            'p-1 sm:p-2 rounded-lg border border-primary transition-colors text-xs',
            pagination.currentPage === 1 
              ? 'bg-tertiary text-secondary cursor-not-allowed' 
              : 'bg-primary text-primary hover:bg-secondary'
          ]"
        >
          <ChevronRightIcon class="h-3 w-3 sm:h-4 sm:w-4" />
        </button>

        <!-- Page Numbers -->
        <div class="flex items-center gap-1">
          <button 
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'w-6 h-6 sm:w-8 sm:h-8 rounded-lg text-xs sm:text-sm border transition-colors',
              page === pagination.currentPage
                ? 'bg-brand-500 text-white border-brand-500'
                : 'bg-primary border-primary text-primary hover:bg-secondary'
            ]"
          >
            {{ page }}
          </button>
        </div>

        <button 
          @click="nextPage"
          :disabled="pagination.currentPage === totalPages"
          :class="[
            'p-1 sm:p-2 rounded-lg border border-primary transition-colors text-xs',
            pagination.currentPage === totalPages 
              ? 'bg-tertiary text-secondary cursor-not-allowed' 
              : 'bg-primary text-primary hover:bg-secondary'
          ]"
        >
          <ChevronLeftIcon class="h-3 w-3 sm:h-4 sm:w-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'
import { computed } from 'vue'

const props = defineProps({
  pagination: {
    type: Object,
    required: true
  },
  filteredTherapists: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['update:pagination'])

const totalPages = computed(() => Math.ceil(props.filteredTherapists.length / props.pagination.itemsPerPage))
const startItem = computed(() => (props.pagination.currentPage - 1) * props.pagination.itemsPerPage + 1)
const endItem = computed(() => Math.min(props.pagination.currentPage * props.pagination.itemsPerPage, props.filteredTherapists.length))

const visiblePages = computed(() => {
  const pages = []
  const current = props.pagination.currentPage
  const total = totalPages.value
  
  if (total <= 5) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    if (current <= 3) {
      for (let i = 1; i <= 4; i++) pages.push(i)
      if (total > 4) pages.push('...')
    } else if (current >= total - 2) {
      pages.push(1, '...')
      for (let i = total - 3; i <= total; i++) pages.push(i)
    } else {
      pages.push(1, '...')
      for (let i = current - 1; i <= current + 1; i++) pages.push(i)
      pages.push('...', total)
    }
  }
  
  return pages
})

const updateItemsPerPage = (value) => {
  emit('update:pagination', {
    ...props.pagination,
    itemsPerPage: parseInt(value),
    currentPage: 1
  })
}

const prevPage = () => {
  if (props.pagination.currentPage > 1) {
    emit('update:pagination', {
      ...props.pagination,
      currentPage: props.pagination.currentPage - 1
    })
  }
}

const nextPage = () => {
  if (props.pagination.currentPage < totalPages.value) {
    emit('update:pagination', {
      ...props.pagination,
      currentPage: props.pagination.currentPage + 1
    })
  }
}

const goToPage = (page) => {
  if (page !== '...') {
    emit('update:pagination', {
      ...props.pagination,
      currentPage: page
    })
  }
}
</script>