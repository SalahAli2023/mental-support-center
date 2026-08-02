<template>
  <div class="font-almarai bg-white">
    <!-- Header -->
    <Header />

    <!-- Hero Section -->
    <Hero 
      :titleKey="'articlesHero.title'"
      :highlightKey="'articlesHero.highlight'"
      :subtitleKey="'articlesHero.subtitle'"
      :buttons="heroButtons"
    />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Category Filter -->
      <CategoryFilter 
        :categories="translatedCategories"
        :activeCategory="activeCategory"
        @category-change="handleCategoryChange"
        @search-change="handleSearchChange"
      />

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#9EBF3B]"></div>
      </div>

      <!-- Articles Grid -->
      <div v-else class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
          <ArticleCard
            v-for="article in paginatedArticles"
            :key="article.id"
            :article="formatArticleForCard(article)"
            @read-more="handleReadMore"
          />
        </div>
      </div>

      <!-- No Results -->
      <div v-if="!loading && filteredArticles.length === 0" class="text-center py-12 bg-white rounded-xl shadow-md max-w-2xl mx-auto">
        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-bold text-emerald-800 mb-2">{{ noResultsText.title }}</h3>
        <p class="text-gray-600">{{ noResultsText.message }}</p>
      </div>

      <!-- Pagination -->
      <div v-if="!loading && totalPages > 1" class="flex justify-center mt-8">
        <Pagination 
          :current-page="currentPage"
          :total-pages="totalPages"
          :total-items="filteredArticles.length"
          :items-per-page="itemsPerPage"
          @page-change="handlePageChange"
        />
      </div>
    </div>

 
  </div>
</template>

<script>
import Header from '@/components/frontend/layouts/header.vue'
import Footer from '@/components/frontend/layouts/footer.vue'
import Hero from '@/components/frontend/layouts/hero.vue'
import ArticleCard from './ArticleCard.vue'
import CategoryFilter from './CategoryFilter.vue'
import Pagination from './Pagination.vue'
import { useTranslations } from '@/composables/useTranslations'
import api from '@/utils/api'

export default {
  name: 'MentalHealthArticles',
  components: {
    Header,
    Footer,
    Hero,
    ArticleCard,
    CategoryFilter,
    Pagination
  },
  setup() {
    const { translate, currentLanguage } = useTranslations()
    
    const heroButtons = [
      { 
        text: translate('buttons.startJourney'), 
        icon: 'fas fa-play-circle', 
        primary: true 
      },
      { 
        text: translate('buttons.learnMore'), 
        icon: 'fas fa-info-circle', 
        primary: false 
      }
    ]
    const noResultsText = {
      title: translate('noResults.title'),
      message: translate('noResults.message')
    }

    return {
      heroButtons,
      noResultsText,
      currentLanguage
    }
  },
  data() {
    return {
      activeCategory: 'all',
      searchQuery: '',
      currentPage: 1,
      itemsPerPage: 6,
      loading: false,
      error: null,
      articles: [],
      categories: [],
      apiCategories: [
        { id: 'all', name: 'جميع المقالات', icon: 'fa-list' }
      ]
    };
  },
  computed: {
    // تصفية المقالات المنشورة فقط
    publishedArticles() {
      return this.articles.filter(article => article.is_published);
    },
    
    filteredArticles() {
      return this.publishedArticles.filter((article) => {
        const matchesCategory = this.activeCategory === 'all' || 
          article.category_id?.toString() === this.activeCategory;
        
        const matchesSearch = !this.searchQuery || 
          article.title?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          article.excerpt?.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          article.content?.toLowerCase().includes(this.searchQuery.toLowerCase());
        
        return matchesCategory && matchesSearch;
      });
    },
    
    totalPages() {
      return Math.ceil(this.filteredArticles.length / this.itemsPerPage);
    },
    
    paginatedArticles() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredArticles.slice(start, end);
    },
    
    translatedCategories() {
      const { translate } = useTranslations();
      
      // دمج التصنيفات الأساسية مع التصنيفات من API
      const allCategories = [
        ...this.apiCategories,
        ...this.categories.map(cat => ({
          id: cat.id.toString(),
          name: this.currentLanguage === 'ar' ? cat.name_ar : cat.name_en,
          icon: this.getCategoryIcon(cat.name_en || cat.name_ar)
        }))
      ];
      
      return allCategories.map(category => {
        if (category.id === 'all') {
          return {
            ...category,
            name: translate('filters.allCategories')
          };
        }
        return category;
      });
    }
  },
  methods: {
    // جلب المقالات من API
    async fetchArticles() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await api.get('/articles', {
          params: {
            is_published: true, // فقط المقالات المنشورة
            locale: this.currentLanguage
          }
        });
        
        this.articles = response.data.data || [];
      } catch (err) {
        console.error('Failed to fetch articles:', err);
        this.error = 'فشل في تحميل المقالات';
      } finally {
        this.loading = false;
      }
    },
    
    // جلب التصنيفات من API
    async fetchCategories() {
      try {
        const response = await api.get('/articles/categories/list', {
          params: {
            locale: this.currentLanguage
          }
        });
        
        this.categories = response.data.data || response.data || [];
      } catch (err) {
        console.error('Failed to fetch categories:', err);
        // يمكنك إضافة تصنيفات افتراضية هنا إذا فشل الاتصال
      }
    },
    
    // تنسيق المقال لعرضه في البطاقة
    formatArticleForCard(article) {
      return {
        id: article.id,
        title: article.title,
        excerpt: article.excerpt,
        image: article.image,
        badge: article.category?.name || 'مقال',
        // إخفاء اسم الكاتب في واجهة المقالات
        author: null,
        date: this.formatDate(article.published_at),
        category: article.category?.name
      };
    },
    
    // تنسيق التاريخ
    formatDate(dateString) {
      if (!dateString) return '';
      
      try {
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-EG', {
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        });
      } catch (error) {
        return dateString;
      }
    },
    
    // الحصول على أيقونة التصنيف بناءً على الاسم
    getCategoryIcon(categoryName) {
      const iconMap = {
        'anxiety': 'fa-heart-pulse',
        'depression': 'fa-cloud-rain',
        'basics': 'fa-graduation-cap',
        'self-development': 'fa-rocket',
        'relationships': 'fa-handshake',
        'القلق': 'fa-heart-pulse',
        'الاكتئاب': 'fa-cloud-rain',
        'أساسيات': 'fa-graduation-cap',
        'تنمية الذات': 'fa-rocket',
        'العلاقات': 'fa-handshake'
      };
      
      for (const [key, icon] of Object.entries(iconMap)) {
        if (categoryName.toLowerCase().includes(key.toLowerCase())) {
          return icon;
        }
      }
      
      return 'fa-file-alt'; // أيقونة افتراضية
    },
    
    handleCategoryChange(categoryId) {
      this.activeCategory = categoryId;
      this.currentPage = 1;
    },
    
    handleSearchChange(query) {
      this.searchQuery = query;
      this.currentPage = 1;
    },
    
    handleReadMore(articleId) {
      this.$router.push(`/article/${articleId}`);
    },
    
    handlePageChange(page) {
      this.currentPage = page;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  },
  async mounted() {
    await Promise.all([
      this.fetchArticles(),
      this.fetchCategories()
    ]);
  },
  
  watch: {
    // إعادة جلب البيانات عند تغيير اللغة
    currentLanguage() {
      this.fetchArticles();
      this.fetchCategories();
    }
  }
}
</script>

<style scoped>
.font-almarai {
  font-family: 'Almarai', sans-serif;
}
</style>