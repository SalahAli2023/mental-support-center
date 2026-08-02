<template>
  <div v-cloak >
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12  sm:py-20">
      <div class="animate-spin rounded-full h-12 w-12 sm:h-16 sm:w-16 border-b-2 border-[#9EBF3B]"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center px-4 py-12 sm:py-20">
      <i class="fas fa-exclamation-triangle text-3xl sm:text-4xl text-red-500 mb-3 sm:mb-4"></i>
      <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">خطأ في تحميل المقال</h3>
      <p class="text-gray-600 mb-4 text-sm sm:text-base">{{ error }}</p>
      <button @click="fetchArticle" class="bg-[#9EBF3B] text-white px-5 py-2 sm:px-6 sm:py-2 rounded-lg hover:bg-[#8CAF2B] text-sm sm:text-base">
        إعادة المحاولة
      </button>
    </div>

    <!-- Content -->
    <div v-else>
      <!-- Hero Section - Mobile Optimized -->
      <Hero
        :title="article.title"
        :subtitle="article.excerpt"
        :buttons="heroButtons"
        @cta="handleHeroCta"
        class="px-4"
      />
 <div class="sm:px-20 ">
      <!-- Breadcrumb - Mobile Optimized -->
      <div class="py-3 mb-4 sm:mb-2  bg-gray-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-6">
          <div class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm text-gray-500 overflow-x-auto whitespace-nowrap scrollbar-hide py-1">
            <span class="cursor-pointer transition-colors duration-300 hover:text-[#9EBF3B] px-1 flex-shrink-0" @click="goBack">
              {{ translate('breadcrumb.articles') }}
            </span>
            <i class="fas fa-chevron-left text-xs text-gray-400 flex-shrink-0"></i>
            <span class="cursor-pointer transition-colors duration-300 hover:text-[#9EBF3B] px-1 flex-shrink-0">
              {{ article.category?.name }}
            </span>
            <i class="fas fa-chevron-left text-xs text-gray-400 flex-shrink-0"></i>
            <span class="text-[#9EBF3B] font-semibold px-1 truncate flex-shrink-0 max-w-[200px] sm:max-w-none" :id="`article-${article.id}`">
              {{ article.title }}
            </span>
          </div>
        </div>
      </div>

      <!-- Main Content Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-[1fr_350px] gap-6 lg:gap-8 max-w-7xl mx-auto px- 2sm:px-4 lg:px-6">
        <!-- Main Content - Article Details -->
        <main class="rounded-xl sm:rounded-1xl p-3 sm:p-4 md:p-6 border border-gray-100 bg-white lg:order-1 order-2">
          <!-- Article Header with Title Above Image -->
          <div class="mb-6 sm:mb-8">
            <div class="mb-6 sm:mb-8 pb-4 sm:pb-6 border-b border-gray-200">
              <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 leading-tight mb-3 sm:mb-4 scroll-mt-20 sm:scroll-mt-24">
                {{ article.title }}
              </h1>
              <div class="flex flex-wrap gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500">
                <span class="flex items-center gap-1 sm:gap-2">
                  <i class="fas fa-calendar-alt text-[#9EBF3B] text-xs sm:text-sm"></i>
                  {{ formatDate(article.published_at) }}
                </span>
                <span class="flex items-center gap-1 sm:gap-2">
                  <i class="fas fa-clock text-[#9EBF3B] text-xs sm:text-sm"></i>
                  {{ translate('article.readingTime') }}: {{ calculateReadingTime(article.content) }}
                </span>
                <span class="flex items-center gap-1 sm:gap-2">
                  <i class="fas fa-eye text-[#9EBF3B] text-xs sm:text-sm"></i>
                  {{ article.views || 0 }} {{ translate('article.views') }}
                </span>
              </div>
            </div>
          </div>

          <!-- Featured Image - Mobile Optimized -->
          <div class="relative rounded-xl sm:rounded-2xl overflow-hidden mb-6 sm:mb-8" v-if="article.image">
            <img :src="article.image" :alt="article.title" class="w-full h-48 sm:h-64 md:h-80 lg:h-96 object-cover">
            <div class="absolute top-3 right-3 sm:top-4 sm:right-4">
              <div class="bg-[#9EBF3B] text-white px-2 py-1 sm:px-3 sm:py-1 rounded text-xs font-semibold">
                {{ article.category?.name }}
              </div>
            </div>
          </div>

          <!-- Article Content -->
          <div class="mt-6 sm:mt-8">
            <!-- Article Text Content -->
            <div class="mb-6 sm:mb-8">
              <!-- Introduction -->
              <section class="pb-6 sm:pb-8 border-b border-gray-200" v-if="article.introduction">
                <div>
                  <p class="text-sm sm:text-base leading-relaxed text-gray-700 font-medium mb-4 sm:mb-6">
                    {{ article.introduction }}
                  </p>
                </div>
              </section>

              <!-- Main Content -->
              <section class="pb-6 sm:pb-8 border-b border-gray-200" v-if="article.content">
                <div class="text-gray-600 leading-loose text-sm sm:text-base" v-html="formatContent(article.content)"></div>
              </section>

              <!-- Article Attachments -->
              <section class="pb-6 sm:pb-8 border-b border-gray-200" v-if="article.attachments && article.attachments.length > 0">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                  <i class="fas fa-paperclip text-[#9EBF3B]"></i>
                  {{ translate('attachments.title') }}
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                  <div 
                    v-for="attachment in article.attachments" 
                    :key="attachment.id"
                    class="bg-gray-50 rounded-lg sm:rounded-xl p-3 sm:p-4 cursor-pointer transition-all duration-300 border border-gray-200 flex items-center gap-3 sm:gap-4 hover:bg-white hover:border-[#9EBF3B]"
                    @click="openAttachment(attachment)"
                  >
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-[#9EBF3B] text-white flex items-center justify-center text-sm sm:text-base flex-shrink-0">
                      <i :class="getAttachmentIcon(attachment.type)"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="text-xs sm:text-sm font-semibold text-gray-800 mb-1 truncate">{{ attachment.title }}</h4>
                      <span class="text-xs text-gray-500">{{ getAttachmentTypeText(attachment.type) }}</span>
                    </div>
                  </div>
                </div>
              </section>
            </div>

            <!-- Test Button Section - Mobile Optimized -->
            <section class="bg-gradient-to-br from-[#9EBF3B] to-[#7CA52D] rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 mt-6 sm:mt-8 relative overflow-hidden">
              <div class="absolute inset-0 opacity-30"></div>
              <div class="flex flex-col lg:flex-row items-center justify-between gap-4 sm:gap-6 lg:gap-8 relative z-10">
                <!-- Content Part -->
                <div class="flex items-center gap-3 sm:gap-4 lg:gap-6 w-full lg:w-auto mb-4 lg:mb-0">
                  <!-- Icon -->
                  <div class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-lg sm:text-xl lg:text-2xl text-white border-2 border-white/30 flex-shrink-0">
                    <i class="fas fa-brain"></i>
                  </div>
                  
                  <!-- Text -->
                  <div class="flex-1 min-w-0">
                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white mb-1 sm:mb-2">
                      {{ translate('testSection.title') }}
                    </h3>
                    <p class="text-xs sm:text-sm text-white/90 leading-normal m-0 line-clamp-2">
                      {{ translate('testSection.description') }}
                    </p>
                  </div>
                </div>
                
                <!-- Button -->
                <div class="w-full lg:w-auto">
                  <button 
                    @click="startTest"
                    :disabled="!hasLinkedScale"
                    class="bg-white text-[#9EBF3B] border-none px-5 py-3 sm:px-6 sm:py-3 lg:px-8 lg:py-4 rounded-full font-bold text-xs sm:text-sm lg:text-base cursor-pointer transition-all duration-300 flex items-center justify-center gap-2 sm:gap-3 hover:gap-3 sm:hover:gap-4 active:-translate-y-0.5 whitespace-nowrap w-full disabled:opacity-60 disabled:cursor-not-allowed"
                  >
                    <span>{{ translate('testSection.startButton') }}</span>
                    <i class="fas" :class="isRTL ? 'fa-arrow-left' : 'fa-arrow-right'"></i>
                  </button>
                  <p v-if="!hasLinkedScale" class="text-white/80 text-xs text-center mt-2 sm:mt-3 m-0">
                    هذا المقال لا يتضمن اختباراً مرتبطاً حالياً
                  </p>
                </div>
              </div>
            </section>
          </div>
        </main>

        <!-- Right Sidebar - Related Articles - Desktop Only -->
        <aside class="sticky top-4 sm:top-6 lg:top-8 h-fit lg:order-2 lg:block hidden">
          <div class="bg-white rounded-xl sm:rounded-1xl p-4 sm:p-6 border border-gray-100">
            <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2">
              <i class="fas fa-link text-[#9EBF3B]"></i>
              {{ translate('sidebar.relatedArticles') }}
            </h3>
            
            <div class="flex flex-col gap-3 sm:gap-4">
              <div 
                v-for="relatedArticle in relatedArticles" 
                :key="relatedArticle.id" 
                class="flex items-center gap-3 sm:gap-4 p-2 sm:p-3 cursor-pointer transition-all duration-300 rounded-lg sm:rounded-xl hover:bg-gray-50"
                @click="goToArticle(relatedArticle.id)"
              >
                <!-- Circular Image -->
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full overflow-hidden border-2 border-[#9EBF3B]">
                    <img 
                      :src="relatedArticle.image" 
                      :alt="relatedArticle.title" 
                      class="w-full h-full object-cover"
                    >
                  </div>
                </div>
                
                <!-- Text Content -->
                <div class="flex-1 min-w-0">
                  <h4 class="text-xs sm:text-sm font-semibold text-gray-800 mb-1 leading-tight line-clamp-2">
                    {{ relatedArticle.title }}
                  </h4>
                  <div class="flex items-center gap-2 text-xs text-gray-500">
                    <i class="fas fa-calendar-alt text-[#9EBF3B] text-xs"></i>
                    <span class="text-xs">{{ formatDate(relatedArticle.published_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </div>

      <!-- Related Articles - Mobile Only (After Main Content) -->
      <div class="lg:hidden mt-8 max-w-7xl mx-auto px-3 sm:px-4">
        <div class="bg-white rounded-xl sm:rounded-1xl p-4 sm:p-6 border border-gray-100">
          <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2">
            <i class="fas fa-link text-[#9EBF3B]"></i>
            {{ translate('sidebar.relatedArticles') }}
          </h3>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div 
              v-for="relatedArticle in relatedArticles" 
              :key="relatedArticle.id" 
              class="flex items-center gap-3 sm:gap-4 p-3 sm:p-4 cursor-pointer transition-all duration-300 rounded-lg sm:rounded-xl hover:bg-gray-50 border border-gray-100"
              @click="goToArticle(relatedArticle.id)"
            >
              <!-- Circular Image -->
              <div class="flex-shrink-0">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full overflow-hidden border-2 border-[#9EBF3B]">
                  <img 
                    :src="relatedArticle.image" 
                    :alt="relatedArticle.title" 
                    class="w-full h-full object-cover"
                  >
                </div>
              </div>
              
              <!-- Text Content -->
              <div class="flex-1 min-w-0">
                <h4 class="text-sm sm:text-base font-semibold text-gray-800 mb-2 leading-tight line-clamp-2">
                  {{ relatedArticle.title }}
                </h4>
                <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500">
                  <i class="fas fa-calendar-alt text-[#9EBF3B] text-xs sm:text-sm"></i>
                  <span class="text-xs sm:text-sm">{{ formatDate(relatedArticle.published_at) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<script>
import Hero from '@/components/frontend/layouts/hero.vue'
import { useTranslations } from '@/composables/useTranslations'
import { inject, ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/utils/api'
import { resolveMediaUrl } from '@/utils/media'
import defaultArticleImage from '@/assets/images/dashboard/images.jpg'

export default {
  name: 'ArticleDetailContent',
  components: {
    Hero
  },
  props: {
    articleId: {
      type: [String, Number],
      required: true
    }
  },
  setup(props) {
    const { translate } = useTranslations()
    const { currentLanguage } = inject('languageState')
    const router = useRouter()
    
    const isRTL = ref(currentLanguage.value === 'ar')
    const loading = ref(false)
    const error = ref(null)
    const article = ref({})
    const relatedArticles = ref([])
    const isLiked = ref(false)
    const likeCount = ref(0)

    // مراقبة تغيير اللغة
    watch(currentLanguage, (newLang) => {
      isRTL.value = newLang === 'ar'
      fetchArticle()
    })

    watch(() => props.articleId, () => {
      fetchArticle()
    })

    // جلب المقال الرئيسي
    const fetchArticle = async () => {
      loading.value = true
      error.value = null
      
      try {
        const response = await api.get(`/articles/${props.articleId}`, {
          params: {
            locale: currentLanguage.value
          }
        })
        
        const payload = response.data?.data ?? response.data
        article.value = formatArticle(payload)

        await fetchRelatedArticles(article.value.category_id)
      } catch (err) {
        console.error('Failed to fetch article:', err)
        error.value = 'فشل في تحميل المقال. يرجى المحاولة مرة أخرى.'
      } finally {
        loading.value = false
      }
    }

    // جلب المقالات ذات الصلة
    const fetchRelatedArticles = async (categoryId) => {
      if (!categoryId) {
        relatedArticles.value = []
        return
      }

      try {
        const response = await api.get('/articles', {
          params: {
            is_published: true,
            locale: currentLanguage.value,
            per_page: 4,
            exclude: props.articleId,
            category_id: categoryId
          }
        })
        
        relatedArticles.value = (response.data.data || []).map(formatArticle)
      } catch (err) {
        console.error('Failed to fetch related articles:', err)
        relatedArticles.value = []
      }
    }

    // تنسيق المحتوى
    const formatContent = (content) => {
      if (!content) return ''
      
      // تحويل الأسطر الجديدة إلى فقرات
      return content.replace(/\n/g, '</p><p>').replace(/<p><\/p>/g, '')
    }

    // تنسيق التاريخ
    const formatDate = (dateString) => {
      if (!dateString) return ''
      
      try {
        const date = new Date(dateString)
        return date.toLocaleDateString('ar-EG', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        })
      } catch (error) {
        return dateString
      }
    }

    // حساب وقت القراءة
    const calculateReadingTime = (content) => {
      if (!content) return '5 دقائق'
      
      const wordsPerMinute = 200
      const words = content.split(/\s+/).length
      const minutes = Math.ceil(words / wordsPerMinute)
      
      return `${minutes} ${minutes === 1 ? 'دقيقة' : 'دقائق'}`
    }

    // الحصول على الصورة الافتراضية للمؤلف
    const getDefaultAvatar = () => {
      return 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'
    }

    const formatArticle = (rawArticle = {}) => {
      return {
        ...rawArticle,
        image: resolveMediaUrl(rawArticle.image, defaultArticleImage)
      }
    }

    // أيقونة المرفقات
    const getAttachmentIcon = (type) => {
      const icons = {
        image: 'fas fa-image',
        video: 'fas fa-video',
        pdf: 'fas fa-file-pdf',
        document: 'fas fa-file-alt'
      }
      return icons[type] || 'fas fa-paperclip'
    }

    // نص نوع المرفق
    const getAttachmentTypeText = (type) => {
      return translate(`attachments.types.${type}`) || translate('attachments.types.document')
    }

    // فتح المرفق
    const openAttachment = (attachment) => {
      if (attachment.url) {
        window.open(attachment.url, '_blank')
      }
    }

    // بدء الاختبار
    const startTest = () => {
      const scaleId = article.value?.psychological_scale?.id || article.value?.psychological_scale_id

      if (!scaleId) {
        alert('لا يوجد مقياس مرتبط مع هذا المقال حالياً.')
        return
      }

      router.push({
        name: 'Measures',
        query: { measureId: scaleId }
      })
    }

    // أزرار الهيرو
    const hasLinkedScale = computed(() => {
      return Boolean(article.value?.psychological_scale?.id || article.value?.psychological_scale_id)
    })

    const heroButtons = computed(() => {
      return [
        {
          text: translate('buttons.startReading'),
          icon: 'fas fa-book-open',
          primary: true
        },
        {
          text: translate('buttons.relatedArticles'),
          icon: 'fas fa-link'
        }
      ]
    })

    // معالجة زر الهيرو
    const handleHeroCta = (btn) => {
      if (btn.text === translate('buttons.startReading')) {
        const articleElement = document.getElementById(`article-${article.value.id}`)
        if (articleElement) {
          articleElement.scrollIntoView({ behavior: 'smooth' })
        }
      } else if (btn.text === translate('buttons.relatedArticles')) {
        const relatedSection = document.querySelector('.lg\\:hidden')
        if (relatedSection) {
          relatedSection.scrollIntoView({ behavior: 'smooth' })
        }
      }
    }

    // العودة للخلف
    const goBack = () => {
      if (window.history.length > 1) {
        router.back()
      } else {
        router.push({ name: 'ArticleMain' })
      }
    }

    // الانتقال لمقال آخر
    const goToArticle = (id) => {
      router.push({ name: 'ArticleDetail', params: { id } })
    }

    // جلب البيانات عند التحميل
    onMounted(() => {
      fetchArticle()
    })

    return {
      translate,
      isRTL,
      loading,
      error,
      article,
      relatedArticles,
      isLiked,
      likeCount,
      heroButtons,
      hasLinkedScale,
      formatContent,
      formatDate,
      calculateReadingTime,
      getDefaultAvatar,
      getAttachmentIcon,
      getAttachmentTypeText,
      openAttachment,
      startTest,
      handleHeroCta,
      goBack,
      goToArticle,
      fetchArticle
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

[v-cloak] {
  display: none;
}

/* Mobile Optimizations */
@media (max-width: 640px) {
  .scroll-mt-20 {
    scroll-margin-top: 5rem;
  }
  
  /* Improve touch targets */
  button, [role="button"] {
    min-height: 44px;
    min-width: 44px;
  }
  
  /* Better text readability on mobile */
  p, span, div {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }
}

/* Tablet Optimizations */
@media (max-width: 1024px) {
  .lg\:grid-cols-\[1fr_350px\] {
    grid-template-columns: 1fr;
  }
  
  aside {
    display: none;
  }
  
  main {
    width: 100%;
  }
}

/* Prevent horizontal overflow */
* {
  max-width: 100%;
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}
</style>