<template>
  <div
    class="bg-white rounded-lg shadow hover:shadow-xl transition-all duration-300 overflow-hidden group cursor-pointer transform hover:-translate-y-1"
    @click="$emit('open-modal', book)"
  >
    <!-- صورة الكتاب -->
    <div class="relative h-48 overflow-hidden">
      <img 
        :src="book.cover || '/images/default-book-cover.jpg'" 
        :alt="book.title" 
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
      />
      
      <!-- زر المفضلة -->
      <button
        @click.stop="$emit('toggle-favorite', book.id)"
        class="absolute top-2 left-2 w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-all duration-300 shadow-md"
        :class="isRTL ? 'right-2 left-auto' : 'left-2 right-auto'"
      >
        <i :class="book.isFavorite ? 'fas text-red-500' : 'far text-gray-600'" class="fa-heart"></i>
      </button>

      <!-- شارة النوع -->
      <div class="absolute top-2 right-2 bg-primary-green text-white px-2 py-1 rounded-full text-xs font-medium" 
           :class="isRTL ? 'left-2 right-auto' : 'right-2 left-auto'">
        {{ getTypeLabel(book.type) }}
      </div>
    </div>
    
    <!-- معلومات الكتاب -->
    <div class="p-3">
      <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2 text-center">{{ book.title }}</h4>
      <p class="text-xs text-gray-600 text-center">{{ book.author }}</p>

      <!-- إحصائيات إضافية -->
      <div class="flex justify-between items-center mt-2 text-xs text-gray-500">
        <span class="flex items-center gap-1">
          <i class="fas fa-eye"></i>
          {{ book.views }}
        </span>
        <span class="flex items-center gap-1">
          <i class="fas fa-download"></i>
          {{ book.downloads }}
        </span>
        <span v-if="book.is_new" class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
          جديد
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import { inject } from 'vue'

export default {
  name: 'BookCard',
  props: {
    book: {
      type: Object,
      required: true
    }
  },
  setup() {
    // استدعاء حالة اللغة من inject
    const { currentLanguage } = inject('languageState')
    const isRTL = currentLanguage.value === 'ar'

    const getTypeLabel = (type) => {
      const types = {
        book: { ar: 'كتاب', en: 'Book' },
        research: { ar: 'أبحاث', en: 'Research' },
        guide: { ar: 'أدلة', en: 'Guide' },
        article: { ar: 'مقالات', en: 'Article' }
      }
      if(types[type]){
        return isRTL ? types[type].ar : types[type].en
      }
      return type
    }

    return {
      isRTL,
      getTypeLabel
    }
  },
  emits: ['toggle-favorite', 'open-modal']
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
