<template>
  <div class="space-y-4">
    <div v-for="(items, title) in filters" :key="title" class="filter-dropdown">
      <button 
        @click="$emit('toggle-dropdown', title)"
        class="w-full flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition border border-gray-200"
      >
        <span class="font-medium text-gray-700">{{ title }}</span>
        <i :class="openDropdowns[title] ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-primary-green transition-transform duration-300"></i>
      </button>
      
      <transition
        enter-active-class="transition-all duration-300 ease-out"
        leave-active-class="transition-all duration-200 ease-in"
        enter-from-class="opacity-0 max-h-0"
        enter-to-class="opacity-100 max-h-96"
        leave-from-class="opacity-100 max-h-96"
        leave-to-class="opacity-0 max-h-0"
      >
        <div v-if="openDropdowns[title]" class="mt-2 space-y-2 overflow-hidden">
          <div 
            v-for="item in items" 
            :key="item"
            class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer transition-colors duration-200"
            @click="$emit('toggle-filter', title, item)"
          >
            <div class="flex items-center justify-center w-5 h-5 border border-gray-300 rounded mr-2 transition-all duration-200"
                 :class="isFilterSelected(title, item) ? 'bg-primary-green border-primary-green' : 'bg-white'">
              <i v-if="isFilterSelected(title, item)" class="fas fa-check text-white text-xs"></i>
            </div>
            <span :class="isFilterSelected(title, item) ? 'text-primary-green font-medium' : 'text-gray-700'">
              {{ item }}
            </span>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FilterSection',
  props: {
    filters: {
      type: Object,
      required: true
    },
    selectedFilters: {
      type: Object,
      required: true
    },
    openDropdowns: {
      type: Object,
      required: true
    }
  },
  emits: ['toggle-dropdown', 'toggle-filter'],
  methods: {
    isFilterSelected(category, item) {
      return this.selectedFilters[category].includes(item);
    }
  }
};
</script>

<style scoped>
.filter-dropdown {
  border-bottom: 1px solid #f3f4f6;
  padding-bottom: 1rem;
}

.filter-dropdown:last-child {
  border-bottom: none;
  padding-bottom: 0;
}
</style>