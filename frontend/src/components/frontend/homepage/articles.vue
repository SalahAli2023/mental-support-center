<template>
  <!-- قسم المقالات التوعوية -->
  <section ref="sectionRef" class="relative py-20 bg-white  overflow-hidden">
    <!-- أشكال زخرفية في الخلفية -->
    <div
      class="absolute top-10 left-10 w-32 h-32 bg-primary-pink opacity-5 rounded-full blur-2xl transition-all duration-1000"
      :class="decorativeClass"></div>
    <div
      class="absolute bottom-10 right-10 w-40 h-40 bg-primary-green opacity-5 rounded-full blur-2xl transition-all duration-1000"
      :class="decorativeClass"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6">
      <!-- العنوان الرئيسي -->
      <div class="text-center mb-16">
        <TitleSection :mainText="translate('home.articles.title')"
          :highlightText="translate('home.articles.highlight')" />

        <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed mt-4 transition-all duration-700"
          :class="contentItemClass">
          {{ translate('home.articles.subtitle') }}
        </p>
      </div>

      <!-- حالات التحميل والخطأ -->
      <div v-if="loading" class="flex justify-center py-16">
        <div class="h-10 w-10 border-4 border-primary-green border-t-transparent rounded-full animate-spin"></div>
      </div>
      <p v-else-if="error" class="text-center text-red-600 mb-6">
        {{ error }}
      </p>
      <p v-else-if="!articles.length" class="text-center text-gray-500 mb-6">
        {{ currentLanguage === 'ar' ? 'لا توجد مقالات متاحة حالياً.' : 'No articles available right now.' }}
      </p>

      <!-- شبكة المقالات -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <ArticleCard v-for="(article, index) in articles" :key="article.id" :article="article" :class="{
          'opacity-0 translate-y-8': !isVisible,
          'opacity-100 translate-y-0': isVisible
        }" :style="{
            transitionDelay: `${index * 150 + 300}ms`
          }" />
      </div>

      <!-- دعوة للعمل (عرض جميع المقالات) -->
      <div class="text-center mt-12">
        <router-link to="/article"
          class="curtain-hover-btn relative bg-transparent text-primary-green font-bold text-base px-8 py-4 rounded-xl border-2 border-primary-green transition-all duration-500 transform overflow-hidden group inline-block text-center"
          :class="{
            'opacity-0 translate-y-4': !isVisible,
            'opacity-100 translate-y-0': isVisible
          }" :style="{ transitionDelay: '800ms' }">
          <span class="flex items-center gap-2 relative z-10 transition-colors duration-500 group-hover:text-white">
            {{ translate('home.articles.viewAll') }}
            <i class="fas fa-arrow-left transition-colors duration-500 group-hover:text-white"></i>
          </span>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script>
import { useScrollAnimation } from '@/assets/js/animations.js'
import ArticleCard from '../layouts/ArticleCard.vue'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'
import api from '@/utils/api'
import { resolveMediaUrl } from '@/utils/media'
import defaultArticleImage from '@/assets/images/dashboard/images.jpg'

export default {
  name: "ArticlesSection",
  components: {
    ArticleCard,
    TitleSection
  },
  mixins: [useScrollAnimation],
  inject: {
    languageState: {
      from: 'languageState',
      default: () => ({
        currentLanguage: { value: 'ar' },
        translate: (key) => key
      })
    }
  },
  data() {
    return {
      articles: [],
      loading: false,
      error: null
    }
  },
  computed: {
    translate() {
      return this.languageState?.translate || ((key) => key)
    },
    decorativeClass() {
      return {
        'opacity-5': this.isVisible,
        'opacity-0': !this.isVisible
      }
    },
    currentLanguage() {
      return this.languageState?.currentLanguage?.value || 'ar'
    }
  },
  watch: {
    'languageState.currentLanguage.value': {
      handler() {
        this.fetchArticles()
      }
    }
  },
  created() {
    this.fetchArticles()
  },
  methods: {
    async fetchArticles() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/articles', {
          params: {
            is_published: 1,
            per_page: 3,
            locale: this.currentLanguage
          }
        })

        const rawArticles = response.data?.data || []
        this.articles = rawArticles.map(this.transformArticle)
      } catch (error) {
        console.error('Failed to load homepage articles:', error)
        this.error = this.currentLanguage === 'ar'
          ? 'تعذر تحميل المقالات حالياً.'
          : 'Unable to load articles right now.'
        this.articles = []
      } finally {
        this.loading = false
      }
    },
    transformArticle(article) {
      return {
        id: article.id,
        // نمرر قيمة الصورة كما تأتي من الـ API (وهي URL جاهز من ArticleResource/MediaHelper)
        // وسيقوم مكون ArticleCard باستخدام resolveMediaUrl ومعالجة أي مسار نسبي أو قيمة فارغة
        image: article.image || null,
        category: this.currentLanguage === 'ar'
          ? (article.category?.name_ar || article.category?.name_en || '')
          : (article.category?.name_en || article.category?.name_ar || ''),
        title: this.currentLanguage === 'ar'
          ? (article.title_ar || article.title)
          : (article.title_en || article.title),
        description: this.currentLanguage === 'ar'
          ? (article.excerpt_ar || article.introduction_ar || article.excerpt || '')
          : (article.excerpt_en || article.introduction_en || article.excerpt || ''),
        // لا نعرض الكاتب في واجهة المستخدم
        author: null,
        date: this.formatArticleDate(article.published_at)
      }
    },
    resolveArticleImage(path) {
      return resolveMediaUrl(path, defaultArticleImage)
    },
    formatArticleDate(dateString) {
      if (!dateString) return ''
      try {
        return new Date(dateString).toLocaleDateString(
          this.currentLanguage === 'ar' ? 'ar-EG' : 'en-US',
          { day: 'numeric', month: 'long', year: 'numeric' }
        )
      } catch {
        return dateString
      }
    },
    getFallbackAuthor() {
      return this.currentLanguage === 'ar' ? 'منصة الدعم النفسي' : 'Psychological Support'
    }
  }
}
</script>

<style scoped>
/* تأثير الستار على زر عرض جميع المقالات */
.curtain-hover-btn {
  position: relative;
  overflow: hidden;
}

.curtain-hover-btn::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 0;
  height: 100%;
  background-color: #9EBF3B;
  transition: width 0.5s ease-in-out;
  z-index: 1;
}

.curtain-hover-btn:hover::before {
  width: 100%;
}

/* تحسين الانتقالات */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* تحسين التجاوب */
@media (max-width: 768px) {
  .text-3xl {
    font-size: 2rem;
  }

  .gap-8 {
    gap: 1.5rem;
  }
}
</style>