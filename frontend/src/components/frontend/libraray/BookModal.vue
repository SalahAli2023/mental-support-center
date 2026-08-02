<template>
  <transition name="modal">
    <div v-if="selectedBook" class="fixed inset-0 mt-18 z-100 overflow-y-auto">
      <!-- الخلفية -->
      <div class="fixed inset-0 bg-white/50" @click="closeModal"></div>

      <!-- المودال -->
      <div class="flex min-h-full items-center justify-center p-2 sm:p-4">
        <div
          class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden border"
          :dir="isRTL ? 'rtl' : 'ltr'"
        >
          <!-- إغلاق -->
          <button
            @click="closeModal"
            class="absolute top-4 left-4 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow"
          >
            <i class="fas fa-times"></i>
          </button>

          <!-- مفضلة -->
          <button
            @click="toggleFavoriteModal"
            class="absolute top-4 right-4 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow"
          >
            <i
              class="fa-heart"
              :class="selectedBook.isFavorite ? 'fas text-red-500' : 'far text-gray-600'"
            ></i>
          </button>

          <div class="flex flex-col md:flex-row h-full">
            <!-- صورة -->
            <div class="md:w-2/5 bg-primary-green flex items-center justify-center p-6">
              <img
                :src="selectedBook.cover || '/images/default-book-cover.jpg'"
                class="w-40 h-60 object-cover rounded-xl shadow-lg"
              />
            </div>

            <!-- المحتوى -->
            <div class="md:w-3/5 p-6 overflow-y-auto">
              <!-- العنوان -->
              <h2 class="text-2xl font-bold mb-2">
                {{ localizedBook.title }}
              </h2>

              <!-- المؤلف -->
              <p class="text-gray-600 mb-3">
                {{ localizedBook.author }}
              </p>

              <!-- بيانات -->
              <div class="flex flex-wrap gap-3 text-sm text-gray-500 mb-4">
                <span>
                  {{ localizedBook.category }}
                </span>
                <span>• {{ selectedBook.year }}</span>
                <span>• {{ getTypeLabel(selectedBook.type) }}</span>
              </div>

              <!-- الوصف -->
              <h3 class="font-semibold mb-1">
                {{ translate('modal.bookSummary') }}
              </h3>
              <p class="text-gray-600 leading-relaxed mb-6">
                {{ localizedBook.description || translate('library.noDescription') }}
              </p>

              <!-- إحصائيات -->
              <div class="grid grid-cols-3 gap-4 text-center mb-6">
                <div>
                  <div class="font-bold text-primary-green">
                    {{ selectedBook.pages || '—' }}
                  </div>
                  <div class="text-xs">{{ translate('modal.pages') }}</div>
                </div>
                <div>
                  <div class="font-bold text-primary-green">
                    {{ selectedBook.views }}
                  </div>
                  <div class="text-xs">{{ translate('modal.views') }}</div>
                </div>
                <div>
                  <div class="font-bold text-primary-green">
                    {{ selectedBook.downloads }}
                  </div>
                  <div class="text-xs">{{ translate('modal.downloads') }}</div>
                </div>
              </div>

              <!-- زر التحميل -->
              <button
                v-if="selectedBook.file_path"
                @click="downloadBook"
                class="w-full bg-primary-green text-white py-3 rounded-xl flex items-center justify-center gap-2"
              >
                <i class="fas fa-download"></i>
                {{ translate('buttons.download') }}
              </button>

              <!-- أسفل -->
              <div class="mt-4 text-xs text-gray-500 flex justify-between">
                <span>
                  {{ translate('modal.language') }}:
                  {{ getLanguage(selectedBook) }}
                </span>
                <span>
                  {{ getFileSize(selectedBook.file_size) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

export default {
  name: 'BookModal',
  props: {
    selectedBook: {
      type: Object,
      default: null
    }
  },
  setup(props) {
    const { translate, currentLanguage } = useTranslations()

    const isRTL = computed(() => currentLanguage.value === 'ar')

    const localizedBook = computed(() => {
      if (!props.selectedBook) return {}

      return {
        title: isRTL.value
          ? props.selectedBook.title_ar
          : props.selectedBook.title_en,
        author: isRTL.value
          ? props.selectedBook.author_ar
          : props.selectedBook.author_en,
        description: isRTL.value
          ? props.selectedBook.description_ar
          : props.selectedBook.description_en,
        category: isRTL.value
          ? props.selectedBook.category?.name_ar
          : props.selectedBook.category?.name_en
      }
    })

    const getTypeLabel = (type) => {
      const map = {
        book: { ar: 'كتاب', en: 'Book' },
        research: { ar: 'بحث', en: 'Research' },
        guide: { ar: 'دليل', en: 'Guide' },
        article: { ar: 'مقالة', en: 'Article' }
      }
      return map[type]
        ? map[type][isRTL.value ? 'ar' : 'en']
        : type
    }

    const getLanguage = (book) => {
      if (book.title_ar && !book.title_en) return isRTL.value ? 'عربي' : 'Arabic'
      if (book.title_en && !book.title_ar) return 'English'
      return isRTL.value ? 'ثنائي اللغة' : 'Bilingual'
    }

    const getFileSize = (size) => {
      if (!size) return 'N/A'
      return `PDF • ${(size / 1024 / 1024).toFixed(1)} MB`
    }

    return {
      translate,
      isRTL,
      localizedBook,
      getTypeLabel,
      getLanguage,
      getFileSize
    }
  },
  emits: ['close', 'toggle-favorite', 'download'],
  methods: {
    closeModal() {
      this.$emit('close')
    },
    toggleFavoriteModal() {
      this.$emit('toggle-favorite', this.selectedBook.id)
    },
    downloadBook() {
      this.$emit('download', this.selectedBook.id)
    }
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
