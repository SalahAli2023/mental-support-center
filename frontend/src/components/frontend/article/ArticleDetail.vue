<template>
  <div class="bg-gray-50">
    <!-- الهيدر -->
    <Header />
    
    <!-- محتوى تفاصيل المقالة -->
    <ArticleDetailContent :article-id="articleId" :key="articleId" />
    
    <!-- الفوتر -->
    <Footer />
  </div>
</template>

<script>
import ArticleDetailContent from './ArticleDetailContent.vue'
import Header from '@/components/frontend/layouts/header.vue'    
import Footer from '@/components/frontend/layouts/footer.vue'   

export default {
  name: 'ArticleDetail',
  components: {
    ArticleDetailContent,
    Header,
    Footer
  },
  data() {
    return {
      articleId: this.$route.params.id
    }
  },
  watch: {
    '$route.params.id': {
      handler(newId) {
        this.articleId = newId
      },
      immediate: true
    }
  }
}
</script>