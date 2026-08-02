<!-- ArticleCard.vue -->
<template>
  <article class="group bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 flex flex-col h-full relative">
    
    <!-- صورة المقال مع التصنيف في الأعلى -->
    <div class="relative overflow-hidden h-48 image-overlay-container">
      <component
        :is="disableNavigation ? 'button' : RouterLink"
        v-bind="disableNavigation ? { type: 'button' } : { to: articleLink }"
        class="block h-full w-full text-start"
        @click="handleClick"
      >
        <img :src="articleImage" :alt="article.title" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" @error="onImageError" />
      </component>
      <!-- تراكب لوني عند التمرير -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
      <!-- التصنيف في أعلى يمين الصورة -->
      <div class="absolute top-3 right-3 z-10">
        <span class="inline-flex items-center text-xs font-semibold px-3 py-1 rounded-full bg-white text-gray-800 shadow-md border border-gray-200 whitespace-nowrap">
          {{ getTranslatedCategory(article.category) }}
        </span>
      </div>
    </div>

    <!-- محتوى المقال -->
    <div class="p-6 flex flex-col flex-1">
      <!-- العنوان -->
      <component
        :is="disableNavigation ? 'button' : RouterLink"
        v-bind="disableNavigation ? { type: 'button', class: 'block text-start w-full' } : { to: articleLink, class: 'block' }"
        @click="handleClick"
      >
        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary-pink transition-colors duration-300">
          {{ article.title }}
        </h3>
      </component>

      <!-- الوصف -->
      <div class="flex-grow mb-4">
        <p class="text-gray-600 text-sm line-clamp-3">
          {{ article.description }}
        </p>
      </div>

      <!-- قسم المعلومات - مثبت في الأسفل دائماً -->
      <div class="mt-auto pt-4">
        <div class="flex items-center justify-between text-xs text-gray-500" :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'">
          <!-- التاريخ فقط -->
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            <span>{{ article.date }}</span>
          </div>
          
          <!-- زر "اقرأ المزيد" -->
          <component
            :is="disableNavigation ? 'button' : RouterLink"
            v-bind="disableNavigation ? { type: 'button' } : { to: articleLink }"
            class="text-primary-green font-semibold text-xs px-0 py-0 hover:text-secondary-green transition-colors duration-300 whitespace-nowrap flex items-center gap-1"
            @click="handleClick"
          >
            <span>{{ getReadMoreText() }}</span>
            <i class="fas fa-arrow-left text-xs" v-if="currentLanguage === 'ar'"></i>
            <i class="fas fa-arrow-right text-xs" v-else></i>
          </component>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useTranslations } from '@/composables/useTranslations'
import { resolveMediaUrl } from '@/utils/media'
import defaultArticleImage from '@/assets/images/dashboard/images.jpg'

// استخدام composable الترجمة
const { currentLanguage, translate } = useTranslations()

// تعريف الـ props التي يستقبلها المكون
const props = defineProps({
  article: {
    type: Object,
    required: true
  },
  disableNavigation: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['click'])

const fallbackImage = ref(defaultArticleImage)

const articleLink = computed(() => ({
  name: 'ArticleDetail',
  params: { id: props.article?.id }
}))

const articleImage = computed(() => {
  return resolveMediaUrl(props.article?.image, fallbackImage.value)
})

const onImageError = () => {
  fallbackImage.value = defaultArticleImage
}

const handleClick = (event) => {
  if (props.disableNavigation) {
    event?.preventDefault?.()
    emit('click', props.article)
  }
}

// دالة للحصول على نص "اقرأ المزيد" المترجم
const getReadMoreText = () => {
  return translate('buttons.readMore')
}

// دالة لترجمة التصنيف
const getTranslatedCategory = (category) => {
  const categories = {
    'evenings': currentLanguage === 'ar' ? 'أمسيات' : 'Evenings',
    'events': currentLanguage === 'ar' ? 'فعاليات' : 'Events',
    'workshops': currentLanguage === 'ar' ? 'ورش عمل' : 'Workshops',
    'أمسيات': currentLanguage === 'ar' ? 'أمسيات' : 'Evenings',
    'فعاليات': currentLanguage === 'ar' ? 'فعاليات' : 'Events',
    'ورش عمل': currentLanguage === 'ar' ? 'ورش عمل' : 'Workshops'
  }
  return categories[category] || category
}
</script>

<style scoped>
/* لقطع النصوص الطويلة وإضافة "..." في النهاية */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* إضافة الإطار المتدرج مع حواف دائرية في أسفل الكارد */
article {
  position: relative;
}

article::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(to left, #9EBF3B, #D6A29A);
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
}
</style>