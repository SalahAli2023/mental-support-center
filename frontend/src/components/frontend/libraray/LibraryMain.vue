<template>
  <div class="font-almarai" :dir="isRTL ? 'rtl' : 'ltr'">
    <!-- Header -->
    <Header />

    <!-- Hero Section -->
    <Hero 
      :titleKey="'libraryHero.title'"
      :highlightKey="'libraryHero.highlight'"
      :subtitleKey="'libraryHero.subtitle'"
      :buttons="heroButtons"
    />

    <!-- محتوى المكتبة -->
    <section class="max-w-7xl mx-auto px-6 py-10">
      <div class="space-y-6">
        <div class="flex-1">
          <!-- شريط البحث -->
          <div class="hidden md:flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4" 
               :class="isRTL ? 'md:flex-row-reverse' : 'md:flex-row'">
            <div class="flex gap-2 w-full" :class="isRTL ? 'flex-row-reverse' : 'flex-row'">
              <!-- زر البحث وحقل الإدخال -->
              <button
                v-if="isRTL"
                @click="searchBooks"
                class="bg-primary-green text-white px-6 py-3 rounded-lg hover:bg-secondary-green transition duration-300 flex items-center gap-2 shadow-md hover:shadow-lg min-w-[120px] justify-center"
                :class="isRTL ? 'flex-row-reverse' : 'flex-row'"
              >
                <i class="fas fa-search" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
                <span>{{ translate('buttons.search') }}</span>
              </button>
              
              <input
                v-model="searchQuery"
                type="text"
                :placeholder="searchPlaceholder"
                class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-green focus:border-transparent text-gray-700"
                :class="isRTL ? 'text-right placeholder:text-right' : 'text-left placeholder:text-left'"
                @keyup.enter="searchBooks"
              />
              
              <button
                v-if="!isRTL"
                @click="searchBooks"
                class="bg-primary-green text-white px-6 py-3 rounded-lg hover:bg-secondary-green transition duration-300 flex items-center gap-2 shadow-md hover:shadow-lg min-w-[120px] justify-center"
                :class="isRTL ? 'flex-row-reverse' : 'flex-row'"
              >
                <span>{{ translate('buttons.search') }}</span>
                <i class="fas fa-search" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
              </button>
            </div>
          </div>

          <!-- فلتر نوع الكتاب -->
          <div class="mb-6">
            <!-- للجوال: قائمة منسدلة -->
            <div class="md:hidden">
              <label :for="'typeFilter'" class="block text-sm font-medium text-gray-700 mb-1">
                {{ isRTL ? 'تصفية حسب النوع:' : 'Filter by type:' }}
              </label>
              <select
                id="typeFilter"
                v-model="selectedType"
                class="block w-full rounded-lg border border-gray-300 text-gray-700 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-green focus:border-transparent"
              >
                <option v-for="option in typeOptions" :key="option.value" :value="option.value">
                  {{ isRTL ? option.labelAr : option.labelEn }}
                </option>
              </select>
            </div>

            <!-- لأجهزة أكبر من md: أزرار -->
            <div class="hidden md:flex flex-wrap gap-3 items-center" 
                 :class="isRTL ? 'flex-row-reverse justify-start md:justify-end' : 'flex-row justify-start'">
              <span class="text-sm font-medium text-gray-700" 
                    :class="[isRTL ? 'ml-2 order-last' : 'mr-2 order-first']">
                {{ isRTL ? 'تصفية حسب النوع:' : 'Filter by type:' }}
              </span>

              <button
                v-for="option in typeOptions"
                :key="option.value"
                @click="selectedType = option.value"
                :class="[ 
                  'px-4 py-2 rounded-full text-sm font-medium border transition-all duration-200 flex items-center gap-2',
                  selectedType === option.value
                    ? 'bg-primary-green text-white border-primary-green shadow-sm'
                    : 'bg-white text-gray-700 border-gray-300 hover:border-primary-green hover:text-primary-green'
                ]"
              >
                <i v-if="option.icon" :class="[option.icon, 'text-xs']"></i>
                <span>{{ isRTL ? option.labelAr : option.labelEn }}</span>
              </button>
            </div>
          </div>

          <!-- شبكة الكتب -->
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <BookCard
              v-for="book in paginatedBooks"
              :key="book.id"
              :book="localizedBook(book)"
              @toggle-favorite="toggleFavorite"
              @open-modal="openBookModal"
            />
          </div>
          
          <!-- لا توجد نتائج -->
          <div v-if="filteredBooks.length === 0" class="text-center py-12">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">{{ translate('library.noResults') }}</h3>
            <p class="text-gray-500">{{ translate('library.tryDifferentSearch') }}</p>
          </div>

          <!-- الترقيم -->
          <div v-if="totalPages > 1" class="flex justify-center mt-8">
            <div class="flex flex-col items-center space-y-6" :dir="isRTL ? 'rtl' : 'ltr'">
              <div class="flex items-center" :class="isRTL ? 'space-x-2 space-x-reverse' : 'space-x-2'">
                <button
                  @click="previousPage"
                  :disabled="currentPage === 1"
                  :class="[ 'pagination-btn', 'prev-next-btn', currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary hover:text-white']"
                >
                  <i :class="isRTL ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
                </button>
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="[ 'pagination-btn', 'page-number', page === currentPage ? 'active-page' : 'inactive-page hover:bg-gray-100']"
                >
                  {{ page }}
                </button>
                <button
                  @click="nextPage"
                  :disabled="currentPage === totalPages"
                  :class="[ 'pagination-btn', 'prev-next-btn', currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary hover:text-white']"
                >
                  <i :class="isRTL ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"></i>
                </button>
              </div>

              <div class="flex" :class="isRTL ? 'space-x-1 space-x-reverse' : 'space-x-1'">
                <div
                  v-for="page in totalPages"
                  :key="page"
                  :class="[ 'h-1 rounded-full transition-all duration-300 cursor-pointer', page === currentPage ? 'bg-primary w-6' : 'bg-gray-300 w-2 hover:bg-gray-400']"
                  @click="goToPage(page)"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- المودال -->
    <BookModal
      :selectedBook="selectedBook ? localizedBook(selectedBook) : null"
      @close="closeModal"
      @toggle-favorite="toggleFavoriteModal"
      @download="downloadBook"
      @preview="previewBook"
      @rate="rateBook"
    />

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script>
import Header from '@/components/frontend/layouts/header.vue'
import Footer from '@/components/frontend/layouts/footer.vue'
import Hero from '@/components/frontend/layouts/hero.vue'
import BookModal from '@/components/frontend/libraray/BookModal.vue'
import BookCard from '@/components/frontend/libraray/LibraryCard.vue'
import { useTranslations } from '@/composables/useTranslations'
import { useLibraryStore } from '@/stores/library'
import { inject } from 'vue'

export default {
  name: 'BooksPage',
  components: { Header, Footer, Hero, BookModal, BookCard },
  setup() {
    const { translate } = useTranslations()
    const { currentLanguage } = inject('languageState')
    const libraryStore = useLibraryStore()
    const isRTL = currentLanguage.value === 'ar'
    
    const heroButtons = [
      { text: translate('buttons.startJourney'), icon: 'fas fa-play-circle', primary: true },
      { text: translate('buttons.learnMore'), icon: 'fas fa-info-circle', primary: false }
    ]
    const searchPlaceholder = isRTL ? 
      'البحث عن كتاب، مؤلف، أو كلمة مفتاحية...' : 
      'Search for a book, author, or keyword...'
    return { translate, isRTL, heroButtons, searchPlaceholder, libraryStore }
  },
  data() {
    return {
      searchQuery: "",
      selectedBook: null,
      currentPage: 1,
      booksPerPage: 12,
      categories: [],
      loading: false,
      selectedType: 'all',
      typeOptions: [
        { value: 'all', labelAr: 'كل الأنواع', labelEn: 'All types', icon: 'fas fa-layer-group' },
        { value: 'book', labelAr: 'كتب', labelEn: 'Books', icon: 'fas fa-book' },
        { value: 'research', labelAr: 'أبحاث', labelEn: 'Research', icon: 'fas fa-flask' },
        { value: 'guide', labelAr: 'أدلة', labelEn: 'Guides', icon: 'fas fa-compass' },
        { value: 'article', labelAr: 'مقالات', labelEn: 'Articles', icon: 'fas fa-newspaper' }
      ]
    };
  },
  async mounted() {
    await this.loadBooks()
    await this.loadCategories()
  },
  computed: {
    filteredBooks() {
      let result = this.libraryStore.items
      const query = this.searchQuery.trim().toLowerCase()
      if(query){
        result = result.filter(book => {
          const title = this.isRTL ? book.title_ar : book.title_en
          const author = this.isRTL ? book.author_ar : book.author_en
          const desc = this.isRTL ? book.description_ar : book.description_en
          const cat = this.isRTL ? book.category?.name_ar || '' : book.category?.name_en || ''
          return [title, author, desc, cat, book.year, book.type].join(' ').toLowerCase().includes(query)
        })
      }
      if(this.selectedType!=='all') result=result.filter(book=>book.type===this.selectedType)
      return result
    },
    totalPages(){ return Math.ceil(this.filteredBooks.length/this.booksPerPage) },
    paginatedBooks(){
      const start=(this.currentPage-1)*this.booksPerPage
      return this.filteredBooks.slice(start,start+this.booksPerPage)
    },
    visiblePages(){
      const pages=[]
      let start=Math.max(1,this.currentPage-2)
      let end=Math.min(this.totalPages,start+4)
      if(end-start<4) start=Math.max(1,end-4)
      for(let i=start;i<=end;i++) pages.push(i)
      return pages
    }
  },
  methods:{
    localizedBook(book){
      const categoryName = this.isRTL 
        ? book.category?.name_ar 
        : this.categoryInEnglish(book.category?.name_ar)
      return {
        ...book,
        title: this.isRTL ? book.title_ar : book.title_en,
        author: this.isRTL ? book.author_ar : book.author_en,
        description: this.isRTL ? book.description_ar : book.description_en,
        category: {
          ...book.category,
          name: categoryName
        }
      }
    },
    categoryInEnglish(arName) {
      const map = {
        'كتاب': 'Book',
        'أبحاث': 'Research',
        'أدلة': 'Guide',
        'مقالات': 'Article'
      };
      return map[arName] || arName
    },
    async loadBooks(){ this.loading=true; try{ await this.libraryStore.fetchItems() }catch(e){console.error(e)}finally{this.loading=false} },
    async loadCategories(){ try{ await this.libraryStore.fetchCategories(); this.categories=this.libraryStore.categories }catch(e){console.error(e)} },
    searchBooks(){ this.currentPage=1 },
    toggleFavorite(bookId){ this.libraryStore.toggleFavorite(bookId) },
    toggleFavoriteModal(bookId){ this.toggleFavorite(bookId); if(this.selectedBook && this.selectedBook.id===bookId) this.selectedBook.isFavorite=!this.selectedBook.isFavorite },
    openBookModal(book){ this.selectedBook={...book}; this.libraryStore.incrementViews(book.id) },
    closeModal(){ this.selectedBook=null },
    downloadBook(bookId){ this.libraryStore.downloadItem(bookId); this.$toast.success(this.translate('messages.downloading'),{position:this.isRTL?'top-right':'top-left',duration:3000}) },
    previewBook(bookId){ this.$toast.info(this.translate('messages.previewing'),{position:this.isRTL?'top-right':'top-left',duration:2000}) },
    rateBook(bookId){ this.$toast.warning(this.translate('messages.openingRating'),{position:this.isRTL?'top-right':'top-left',duration:2000}) },
    goToPage(page){ this.currentPage=page; window.scrollTo({top:0,behavior:'smooth'}) },
    previousPage(){ if(this.currentPage>1){this.currentPage--; window.scrollTo({top:0,behavior:'smooth'}) } },
    nextPage(){ if(this.currentPage<this.totalPages){this.currentPage++; window.scrollTo({top:0,behavior:'smooth'}) } }
  }
}
</script>
