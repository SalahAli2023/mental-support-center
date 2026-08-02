<template>
  <div class="flex justify-center mb-8 w-full">
    <!-- شريط البحث والتصفية -->
    <div class="flex gap-4 items-center w-full max-w-6xl justify-center">
      <!-- محرك البحث -->
      <div class="relative flex-1 min-w-0" :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'">
        <i class="fas fa-search absolute top-1/2 transform -translate-y-1/2 text-gray-400 z-10 text-sm" 
          :class="currentLanguage === 'ar' ? 'right-4' : 'left-4'"></i>
        <input
          type="text"
          v-model="searchQuery"
          @input="handleSearch"
          :placeholder="searchPlaceholderArticle"
          class="w-full border-2 border-gray-200 rounded-xl py-3.5 px-12 transition-all duration-300 ease-in-out text-sm bg-white shadow-sm focus:outline-none focus:border-[#9EBF3B] focus:ring-4 focus:ring-[#9EBF3B]/15 focus:shadow-lg"
        />
      </div>

      <!-- باقي الكود كما هو -->
      <div class="relative min-w-56 flex-shrink-0">
        <div 
          class="flex items-center justify-between px-5 py-3 rounded-xl font-semibold transition-all duration-300 ease-in-out border-2 border-gray-200 bg-white cursor-pointer shadow-sm hover:border-[#9EBF3B] hover:shadow-md gap-2 w-full min-w-52"
          @click="toggleDropdown"
        >
          <span class="flex items-center gap-2">
            <i :class="['fas', activeCategoryIcon]"></i>
            {{ activeCategoryName }}
          </span>
          <i class="fas fa-chevron-down text-gray-500 text-sm transition-transform duration-300 ease-in-out" :class="{ 'rotate-180': isDropdownOpen }"></i>
        </div>
        
        <div 
          class="absolute top-full left-0 right-0 bg-white rounded-xl shadow-xl z-100 opacity-0 invisible -translate-y-2.5 transition-all duration-300 ease-in-out mt-2 overflow-hidden border border-gray-100"
          :class="{ 'opacity-100 visible translate-y-0': isDropdownOpen }"
        >
          <div
            v-for="category in categories"
            :key="category.id"
            @click="handleCategoryChange(category.id)"
            class="flex items-center gap-3 px-5 py-3 cursor-pointer transition-all duration-200 ease-in-out border-b border-gray-50 last:border-b-0 hover:bg-gray-50"
            :class="{ 'bg-gradient-to-r from-[#9EBF3B] to-[#D6A29A] text-white': activeCategory === category.id }"
          >
           <i 
            :class="[
              'fas',
              category.icon,
              'text-sm w-4 text-center',
              activeCategory === category.id ? 'text-white' : 'text-[#9EBF3B]'
            ]"></i>
            {{ getTranslatedCategoryName(category) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useTranslations } from '@/composables/useTranslations'
import { computed, watch } from 'vue'

export default {
  name: 'SearchAndFilter',
  props: {
    categories: {
      type: Array,
      required: true
    },
    activeCategory: {
      type: String,
      required: true
    }
  },
  emits: ['category-change', 'search-change'],
  setup(props, { emit }) {
    const { translate, currentLanguage } = useTranslations()
    
    const searchPlaceholderArticle = computed(() => {
      return translate('filters.searchPlaceholderArticle')
    })

    const getTranslatedCategoryName = (category) => {
      if (category.id === 'all') {
        return translate('filters.allCategories')
      }
      return category.name
    }

    const activeCategoryName = computed(() => {
      if (props.activeCategory === 'all') {
        return translate('filters.allCategories')
      }
      const category = props.categories.find(cat => cat.id === props.activeCategory)
      return category ? category.name : translate('filters.allCategories')
    })

    const activeCategoryIcon = computed(() => {
      const category = props.categories.find(cat => cat.id === props.activeCategory)
      return category ? category.icon : 'fa-list'
    })

    // مراقبة تغيير اللغة وإعادة تعيين البحث
    watch(currentLanguage, (newLang) => {
      // يمكنك إضافة أي منطق إضافي تحتاجه عند تغيير اللغة
      console.log('Language changed to:', newLang)
    })

    return {
      searchPlaceholderArticle,
      getTranslatedCategoryName,
      activeCategoryName,
      activeCategoryIcon,
      currentLanguage
    }
  },
  data() {
    return {
      searchQuery: '',
      isDropdownOpen: false
    }
  },
  methods: {
    handleCategoryChange(categoryId) {
      this.$emit('category-change', categoryId);
      this.isDropdownOpen = false;
    },
    handleSearch() {
      this.$emit('search-change', this.searchQuery);
    },
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen;
    },
    closeDropdown() {
      this.isDropdownOpen = false;
    }
  },
  mounted() {
    document.addEventListener('click', (e) => {
      if (!this.$el.contains(e.target)) {
        this.closeDropdown();
      }
    });
  }
}
</script>

<style scoped>
.z-100 {
  z-index: 100;
}

/* تأكد من أن العناصر تستجيب لتغيير الاتجاه */
[dir="rtl"] .search-input {
  text-align: right;
}

[dir="ltr"] .search-input {
  text-align: left;
}
</style>