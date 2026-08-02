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
          :placeholder="translate('filters.searchPlaceholderScale')"
          class="w-full border-2 border-gray-200 rounded-xl py-3.5 px-12 transition-all duration-300 ease-in-out text-sm bg-white shadow-sm focus:outline-none focus:border-[#9EBF3B] focus:ring-4 focus:ring-[#9EBF3B]/15 focus:shadow-lg"
        />
      </div>

      <!-- قائمة التصنيفات المنسدلة -->
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
        
        <!-- القائمة المنسدلة -->
        <div 
          class="absolute top-full left-0 right-0 bg-white rounded-xl shadow-xl z-100 opacity-0 invisible -translate-y-2.5 transition-all duration-300 ease-in-out mt-2 overflow-hidden border border-gray-100 max-h-96 overflow-y-auto"
          :class="{ 'opacity-100 visible translate-y-0': isDropdownOpen }"
        >
          <!-- عنصر "الكل" -->
          <div
            @click="handleCategoryChange('all')"
            class="flex items-center gap-3 px-5 py-3 cursor-pointer transition-all duration-200 ease-in-out border-b border-gray-50 last:border-b-0 hover:bg-gray-50"
            :class="{ 'bg-gradient-to-r from-[#9EBF3B] to-[#D6A29A] text-white': activeCategory === 'all' }"
          >
            <i class="fas fa-list text-sm w-4 text-center" :class="activeCategory === 'all' ? 'text-white' : 'text-[#9EBF3B]'"></i>
            {{ translate('filters.allCategories') }}
            <span class="mr-auto text-xs bg-gray-100 px-2 py-1 rounded-full" :class="activeCategory === 'all' ? 'bg-white/20 text-white' : 'text-gray-600'">
              {{ totalMeasuresCount }}
            </span>
          </div>

          <!-- التصنيفات الفعلية -->
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
                getCategoryIcon(category),
                'text-sm w-4 text-center',
                activeCategory === category.id ? 'text-white' : 'text-[#9EBF3B]'
              ]"></i>
            <span>{{ getCategoryName(category) }}</span>
            <span class="mr-auto text-xs bg-gray-100 px-2 py-1 rounded-full" :class="activeCategory === category.id ? 'bg-white/20 text-white' : 'text-gray-600'">
              {{ getCategoryMeasuresCount(category.id) }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref, watch,onUnmounted } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

export default {
  name: 'CategoryFilter',
  props: {
    categories: {
      type: Array,
      required: true
    },
    activeCategory: {
      type: String,
      required: true
    },
    measures: {
      type: Array,
      default: () => []
    }
  },
  emits: ['category-change', 'search-change'],
  setup(props, { emit }) {
    const { translate, currentLanguage } = useTranslations()
    const searchQuery = ref('')
    const isDropdownOpen = ref(false)

    // إجمالي عدد المقاييس
    const totalMeasuresCount = computed(() => props.measures.length)

    // اسم التصنيف النشط
    const activeCategoryName = computed(() => {
      if (props.activeCategory === 'all') {
        return translate('filters.allCategories')
      }
      const category = props.categories.find(cat => cat.id === props.activeCategory)
      return category ? getCategoryName(category) : translate('filters.allCategories')
    })

    // أيقونة التصنيف النشط
    const activeCategoryIcon = computed(() => {
      if (props.activeCategory === 'all') {
        return 'fa-list'
      }
      const category = props.categories.find(cat => cat.id === props.activeCategory)
      return category ? getCategoryIcon(category) : 'fa-list'
    })

    // الحصول على اسم التصنيف حسب اللغة
    const getCategoryName = (category) => {
      if (!category) return ''
      return currentLanguage.value === 'ar' 
        ? category.name_ar || category.name_en 
        : category.name_en || category.name_ar
    }

    // الحصول على أيقونة التصنيف
    const getCategoryIcon = (category) => {
      // أيقونات افتراضية حسب اسم التصنيف
      const iconMap = {
        'women': 'fa-female',
        'نساء': 'fa-female',
        'children': 'fa-child',
        'أطفال': 'fa-child',
        'specialists': 'fa-user-md',
        'متخصصين': 'fa-user-md',
        'anxiety': 'fa-heart-pulse',
        'قلق': 'fa-heart-pulse',
        'depression': 'fa-cloud-rain',
        'اكتئاب': 'fa-cloud-rain',
        'self-development': 'fa-rocket',
        'تطوير': 'fa-rocket',
        'relationships': 'fa-handshake',
        'علاقات': 'fa-handshake'
      }

      const categoryName = (category.name_ar + ' ' + category.name_en).toLowerCase()
      
      for (const [key, icon] of Object.entries(iconMap)) {
        if (categoryName.includes(key.toLowerCase())) {
          return icon
        }
      }
      
      return 'fa-chart-bar' // أيقونة افتراضية
    }

    // حساب عدد المقاييس في تصنيف معين
    const getCategoryMeasuresCount = (categoryId) => {
      return props.measures.filter(measure => 
        measure.category_id === categoryId || 
        measure.category?.id === categoryId
      ).length
    }

    // دوال التفاعل
    const handleCategoryChange = (categoryId) => {
      emit('category-change', categoryId)
      isDropdownOpen.value = false
    }

    const handleSearch = () => {
      emit('search-change', searchQuery.value)
    }

    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value
    }

    const closeDropdown = () => {
      isDropdownOpen.value = false
    }

    // إغلاق القائمة عند النقر خارجها
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        closeDropdown()
      }
    }

    // مراقبة تغيير البحث
    watch(searchQuery, () => {
      // يمكن إضافة تأخير (debounce) إذا أردت
    })

    // إضافة مستمع الحدث
    if (typeof window !== 'undefined') {
      document.addEventListener('click', handleClickOutside)
    }

    // تنظيف المستمع
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })

    return {
      searchQuery,
      isDropdownOpen,
      totalMeasuresCount,
      activeCategoryName,
      activeCategoryIcon,
      currentLanguage,
      translate,
      getCategoryName,
      getCategoryIcon,
      getCategoryMeasuresCount,
      handleCategoryChange,
      handleSearch,
      toggleDropdown,
      closeDropdown
    }
  }
}
</script>

<style scoped>
.z-100 {
  z-index: 100;
}

/* أنيميشن للقائمة المنسدلة */
.rotate-180 {
  transform: rotate(180deg);
}

/* تحسين ظهور الأرقام */
.bg-white\/20 {
  background-color: rgba(255, 255, 255, 0.2);
}
</style>