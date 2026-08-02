<template>
  <!-- شريط البحث والتصفية -->
  <div class="flex flex-col md:flex-row gap-4 mb-12">
    <!-- حقل البحث -->
    <div class="relative flex-1">
      <input 
        v-model="searchQuery"
        type="text" 
        :placeholder="translate('events.filter.searchPlaceholder')"
        class="w-full px-12 py-4 bg-white border border-gray-300 rounded-2xl text-gray-800 placeholder-gray-500 focus:outline-none focus:border-primary-green focus:ring-4 focus:ring-primary-green/20 transition-all duration-300 text-base shadow-sm hover:shadow-md"
        :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'"
      />
      <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
    </div>

    <!-- قائمة التصفية -->
    <div class="relative">
      <select 
        v-model="selectedCategory"
        class="w-full md:w-48 px-4 py-4 bg-white border border-gray-300 rounded-2xl text-gray-800 focus:outline-none focus:border-primary-green focus:ring-4 focus:ring-primary-green/20 transition-all duration-300 appearance-none shadow-sm hover:shadow-md text-base"
        :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'"
      >
        <option value="all">{{ translate('events.filter.allEvents') }}</option>
        <option value="evenings">{{ translate('events.filter.evenings') }}</option>
        <option value="events">{{ translate('events.filter.events') }}</option>
        <option value="workshops">{{ translate('events.filter.workshops') }}</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { ref, defineEmits, watch } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

// استخدام composable الترجمة
const { currentLanguage, translate } = useTranslations()

const searchQuery = ref('')
const selectedCategory = ref('all')

const emit = defineEmits(['filter-change'])

// مراقبة التغييرات وإرسالها للوالد
watch([searchQuery, selectedCategory], () => {
  emit('filter-change', {
    search: searchQuery.value,
    category: selectedCategory.value
  })
})
</script>