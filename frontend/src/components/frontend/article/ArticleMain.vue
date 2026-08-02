
<template>
  <div class="bg-white">
  
    
    <!-- محتوى المقالات -->
    <MentalHealthArticles />
    
    <!-- الفوتر -->
    <Footer />
  </div>
</template>

<script>
import MentalHealthArticles from './MentalHealthArticles.vue'
import Header from '../layouts/header.vue'    
//  import Hero from './ArticleHero.vue'      
import Footer from '../layouts/footer.vue'   

export default {
  name: 'ArticleMain',
  components: {
    MentalHealthArticles,
    Header,
    // Hero,
    Footer
  }
}

</script>