<template>
  <div>
    <!-- الفلترة للجوال -->
    <button 
      @click="toggleMobileFilter"
      class="md:hidden w-full bg-gradient-to-l from-primary-green to-secondary-green text-white px-6 py-4 rounded-2xl flex items-center justify-between font-bold text-lg mb-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5"
      :class="isRTL ? 'flex-row-reverse' : 'flex-row'"
    >
      <span class="flex items-center gap-3" :class="isRTL ? 'flex-row-reverse' : 'flex-row'">
        <i class="fas fa-filter text-xl"></i>
        {{ translate('filters.mobileTitle') }}
      </span>
      <i :class="showMobileFilter ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-white text-lg transition-transform duration-300"></i>
    </button>

    <!-- الفلتر للجوال -->
    <transition
      enter-active-class="transition-all duration-300 ease-out"
      leave-active-class="transition-all duration-200 ease-in"
      enter-from-class="opacity-0 transform -translate-y-4"
      enter-to-class="opacity-100 transform translate-y-0"
      leave-from-class="opacity-100 transform translate-y-0"
      leave-to-class="opacity-0 transform -translate-y-4"
    >
      <div v-if="showMobileFilter" class="md:hidden bg-white/95 backdrop-blur-lg p-6 rounded-3xl shadow-2xl mb-6 border border-white/30">
        <!-- الهيدر -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200/50" :class="isRTL ? 'flex-row-reverse' : 'flex-row'">
          <div class="flex items-center gap-3" :class="isRTL ? 'flex-row-reverse' : 'flex-row'">
            <div class="w-10 h-10 bg-gradient-to-br from-primary-green to-secondary-green rounded-xl flex items-center justify-center shadow-lg">
              <i class="fas fa-filter text-white text-lg"></i>
            </div>
            <div :class="isRTL ? 'text-right' : 'text-left'">
              <h3 class="font-bold text-xl text-gray-800">{{ translate('filters.title') }}</h3>
              <p class="text-sm text-gray-500">{{ translate('filters.subtitle') }}</p>
            </div>
          </div>
          <button @click="toggleMobileFilter" class="w-10 h-10 bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-500 rounded-xl flex items-center justify-center transition-all duration-300 shadow-sm">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>
        
        <!-- قسم الفلاتر -->
        <div class="max-h-96 overflow-y-auto pr-2">
          <FilterSection 
            :filters="translatedFilters"
            :selectedFilters="selectedFilters"
            :openDropdowns="openDropdowns"
            @toggle-dropdown="toggleDropdown"
            @toggle-filter="toggleFilterItem"
          />
        </div>
        
        <!-- أزرار الإجراءات -->
        <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200/50" :class="isRTL ? 'flex-row-reverse' : 'flex-row'">
          <button
            @click="clearFilters"
            class="flex-1 border-2 border-red-500 text-red-500 px-6 py-4 rounded-xl hover:bg-red-500 hover:text-white transition-all duration-300 font-bold flex items-center justify-center gap-3 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
            :class="isRTL ? 'flex-row-reverse' : 'flex-row'"
          >
            <i class="fas fa-broom text-lg"></i>
            {{ translate('buttons.clearAll') }}
          </button>
          <button
            @click="toggleMobileFilter"
            class="flex-1 bg-gradient-to-l from-primary-green to-secondary-green text-white px-6 py-4 rounded-xl hover:shadow-lg transition-all duration-300 font-bold flex items-center justify-center gap-3 shadow-md transform hover:-translate-y-0.5"
            :class="isRTL ? 'flex-row-reverse' : 'flex-row'"
          >
            <i class="fas fa-check-circle text-lg"></i>
            {{ translate('buttons.applyFilters') }}
          </button>
        </div>
      </div>
    </transition>

    <!-- الفلتر للشاشات الكبيرة -->
    <div class="hidden md:block w-full md:w-80 bg-white/95 backdrop-blur-lg p-8 rounded-3xl shadow-2xl border border-white/30 h-fit sticky top-24">
      <!-- الهيدر -->
      <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-200/50" :class="isRTL ? 'flex-row-reverse' : 'flex-row'">
        <div class="flex items-center gap-4" :class="isRTL ? 'flex-row-reverse' : 'flex-row'">
          <div class="w-12 h-12 bg-gradient-to-br from-primary-green to-secondary-green rounded-2xl flex items-center justify-center shadow-lg">
            <i class="fas fa-filter text-white text-xl"></i>
          </div>
          <div :class="isRTL ? 'text-right' : 'text-left'">
            <h3 class="font-bold text-2xl text-gray-800">{{ translate('filters.title') }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ translate('filters.subtitle') }}</p>
          </div>
        </div>
        <button 
          @click="clearFilters" 
          class="w-10 h-10 bg-gray-100 ml-1 hover:bg-red-50 text-gray-500 hover:text-red-500 rounded-xl flex items-center justify-center transition-all duration-300 shadow-sm hover:shadow-md"
          :title="translate('buttons.resetFilters')"
        >
          <i class="fas fa-redo-alt text-sm"></i>
        </button>
      </div>
      
      <!-- إحصائيات الفلاتر -->
      <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border border-blue-100">
        <div class="flex justify-between items-center text-sm" :class="isRTL ? 'flex-row-reverse' : 'flex-row'">
          <span class="text-gray-600 font-medium">{{ translate('filters.appliedFilters') }}:</span>
          <span class="bg-primary-green text-white px-3 py-1 rounded-full font-bold">
            {{ getAppliedFiltersCount }}
          </span>
        </div>
      </div>
      
      <!-- قسم الفلاتر -->
      <div class="space-y-4 max-h-[500px] overflow-y-auto pr-3">
        <FilterSection 
          :filters="translatedFilters"
          :selectedFilters="selectedFilters"
          :openDropdowns="openDropdowns"
          @toggle-dropdown="toggleDropdown"
          @toggle-filter="toggleFilterItem"
        />
      </div>
      
      <!-- زر تطبيق الفلاتر -->
      <button
        @click="$emit('apply-filters')"
        class="w-full mt-8 bg-gradient-to-l from-primary-green to-secondary-green text-white px-6 py-4 rounded-xl hover:shadow-xl transition-all duration-300 font-bold flex items-center justify-center gap-3 shadow-lg transform hover:-translate-y-0.5"
        :class="isRTL ? 'flex-row-reverse' : 'flex-row'"
      >
        <i class="fas fa-rocket text-lg"></i>
        {{ translate('buttons.applyFilters') }}
        <span v-if="getAppliedFiltersCount > 0" class="bg-white/20 px-2 py-1 rounded-full text-sm">
          {{ getAppliedFiltersCount }}
        </span>
      </button>
    </div>
  </div>
</template>

<script>
import FilterSection from './FilterSection.vue'
import { useTranslations } from '@/composables/useTranslations'

export default {
  name: 'BookFilters',
  components: {
    FilterSection
  },
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
  setup() {
    const { translate, currentLanguage } = useTranslations()
    
    const isRTL = currentLanguage.value === 'ar'
    
    return {
      translate,
      isRTL
    }
  },
  data() {
    return {
      showMobileFilter: false
    }
  },
  computed: {
    getAppliedFiltersCount() {
      let count = 0;
      Object.values(this.selectedFilters).forEach(filterArray => {
        count += filterArray.length;
      });
      return count;
    },
    translatedFilters() {
      // تحويل مفاتيح الفلاتر بناءً على اللغة
      const translated = {}
      Object.keys(this.filters).forEach(key => {
        const translatedKey = this.getTranslatedFilterKey(key)
        translated[translatedKey] = this.filters[key]
      })
      return translated
    }
  },
  emits: ['toggle-dropdown', 'toggle-filter', 'clear-filters', 'apply-filters'],
  methods: {
    getTranslatedFilterKey(oldKey) {
      const keyMap = {
        'التصنيفات': this.translate('filters.categories'),
        'اسم المؤلف': this.translate('filters.authors'),
        'اللغة': this.translate('filters.languages'),
        'سنة النشر': this.translate('filters.years'),
        'التقييم': this.translate('filters.ratings')
      }
      return keyMap[oldKey] || oldKey
    },
    toggleMobileFilter() {
      this.showMobileFilter = !this.showMobileFilter;
    },
    toggleDropdown(title) {
      this.$emit('toggle-dropdown', title);
    },
    clearFilters() {
      this.$emit('clear-filters');
      if (this.showMobileFilter) {
        this.showMobileFilter = false;
      }
    },
    toggleFilterItem(category, item) {
      this.$emit('toggle-filter', category, item);
    }
  }
};
</script>

<style scoped>
/* تخصيص شريط التمرير */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #9EBF3B, #8cad35);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #8cad35, #7a9a2e);
}

/* تأثيرات الظل */
.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

/* تأثيرات الخلفية الشفافة */
.backdrop-blur-lg {
  backdrop-filter: blur(16px);
}

/* تحسينات للاستجابة على الجوال */
@media (max-width: 768px) {
  .sticky {
    position: static;
  }
}
</style>