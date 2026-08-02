<template>
  <!-- قسم فريق الخبراء -->
  <section 
    ref="sectionRef"
    class="relative py-16 md:py-20 bg-white overflow-hidden transition-all duration-1000"
    :class="sectionClass"
  >
    <!-- أشكال زخرفية في الخلفية -->
    <div 
      class="absolute top-0 left-0 w-72 h-72 bg-primary-green opacity-5 rounded-full blur-3xl transition-all duration-1000 delay-300"
      :class="decorativeClass"
    ></div>
    <div 
      class="absolute bottom-0 right-0 w-80 h-80 bg-primary-pink opacity-5 rounded-full blur-3xl transition-all duration-1000 delay-500"
      :class="decorativeClass"
    ></div>
    
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6">
      <!-- العنوان الرئيسي -->
      <div class="text-center mb-12 md:mb-16">
        <div class="inline-block relative">
          <TitleSection
            :mainText="translate('home.experts.title')"
            :highlightText="translate('home.experts.highlight')"
          />
        </div>
        <p 
          class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto mt-4 transition-all duration-700 delay-300"
          :class="contentItemClass"
        >
          {{ translate('home.experts.subtitle') }}
        </p>
      </div>

      <!-- حالات التحميل -->
      <div v-if="loading" class="flex justify-center py-10">
        <i class="fas fa-spinner fa-spin text-3xl text-primary-green"></i>
      </div>
      <div v-else-if="error" class="text-center text-red-600 py-6">
        {{ error }}
      </div>
      <div v-else-if="!hasExperts" class="text-center text-gray-500 py-6">
        {{ currentLanguage === 'ar' ? 'لم يتم العثور على أخصائيين متاحين حالياً.' : 'No specialists available right now.' }}
      </div>

      <!-- كاروسيل الأخصائيين -->
      <div v-else class="relative">
        <!-- زر السابق -->
        <button 
          @click="prevExpert" 
          class="absolute top-1/2 transform -translate-y-1/2 z-20 w-10 h-10 md:w-12 md:h-12 bg-white rounded-full shadow-lg hover:shadow-xl border border-gray-200 flex items-center justify-center text-primary-green hover:text-primary-pink transition-all duration-300 hover:scale-110"
          :class="navButtonClass"
          :style="{ [currentLanguage === 'ar' ? 'right' : 'left']: '2rem' }"
        >
          <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"></i>
        </button>

        <!-- زر التالي -->
        <button 
          @click="nextExpert" 
          class="absolute top-1/2 transform -translate-y-1/2 z-20 w-10 h-10 md:w-12 md:h-12 bg-white rounded-full shadow-lg hover:shadow-xl border border-gray-200 flex items-center justify-center text-primary-green hover:text-primary-pink transition-all duration-300 hover:scale-110"
          :class="navButtonClass"
          :style="{ [currentLanguage === 'ar' ? 'left' : 'right']: '2rem' }"
        >
          <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
        </button>

        <!-- الحاوية الرئيسية للسلايدر -->
        <div :class="currentLanguage === 'ar' ? 'rtl' : 'ltr'" @mouseenter="stopAutoSlide" @mouseleave="startAutoSlide">
          <div class="overflow-hidden rounded-2xl">
            <div 
              class="flex transition-transform duration-700 ease-out" 
              :style="{ transform: currentLanguage === 'ar' ? `translateX(${currentIndex * 100}%)` : `translateX(-${currentIndex * 100}%)` }"
            >
              <div v-for="(expert, index) in experts" :key="expert.id" class="w-full flex-shrink-0 px-3 md:px-4">
                <div class="max-w-4xl mx-auto">
                  <div class="flex flex-col lg:grid lg:grid-cols-2 gap-8 md:gap-12 items-center">
                    <!-- الصورة -->
                    <div class="relative group transition-all duration-700 delay-200 order-1 lg:order-1"
                         :class="expertImageClass">
                      <div class="relative">
                        <img 
                          :src="expert.image" 
                          :alt="expert.name"
                          class="w-64 h-64 sm:w-72 sm:h-72 md:w-80 md:h-80 rounded-2xl md:rounded-3xl object-cover mx-auto border-4 border-white transform group-hover:scale-105 transition-all duration-500"
                        />
                      </div>
                    </div>

                    <!-- المعلومات -->
                    <div class="transition-all duration-700 delay-400 order-2 lg:order-2"
                         :class="expertInfoClass">
                      <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 mb-3 md:mb-4">{{ expert.name }}</h3>
                      <p class="text-lg sm:text-xl text-primary-pink font-semibold mb-4 md:mb-6">{{ expert.specialty }}</p>
                      
                      <!-- الوصف -->
                      <p class="text-gray-600 leading-relaxed mb-6 md:mb-8 text-sm sm:text-base md:text-lg">
                        {{ expert.description }}
                      </p>

                      <!-- أزرار التواصل -->
                      <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center lg:justify-start transition-all duration-700 delay-600"
                           :class="buttonClass">
                        <router-link
                          :to="getBookingRoute(expert)"
                          class="bg-primary-green text-white font-semibold py-2 md:py-3 px-6 md:px-8 rounded-xl hover:bg-[#8aa835] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 text-sm md:text-base text-center"
                        >
                          <span class="flex items-center gap-2 justify-center">
                            {{ translate('home.experts.book') }}
                            <i class="fas fa-calendar-check text-xs md:text-sm"></i>
                          </span>
                        </router-link>
                        <router-link
                          :to="getProfileRoute(expert)"
                          class="bg-transparent text-primary-green font-semibold py-2 md:py-3 px-6 md:px-8 rounded-xl border-2 border-primary-green hover:bg-primary-green hover:text-white transition-all duration-300 transform hover:scale-105 text-sm md:text-base text-center"
                        >
                          <span class="flex items-center gap-2 justify-center">
                            {{ translate('home.experts.profile') }}
                            <i class="fas fa-user text-xs md:text-sm"></i>
                          </span>
                        </router-link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- مؤشر الصفحات -->
      <div class="flex justify-center mt-8 md:mt-12 gap-2 md:gap-3 transition-all duration-700 delay-800"
           :class="buttonClass">
        <button 
          v-for="index in visibleIndices" 
          :key="index"
          @click="goToExpert(index)"
          class="w-8 h-2 md:w-12 md:h-2 rounded-full transition-all duration-300 hover:scale-110"
          :class="index === currentIndex ? 'bg-primary-green scale-110' : 'bg-gray-300 hover:bg-gray-400'"
        >
        </button>
      </div>
    </div>
  </section>
</template>

<script>
import { useScrollAnimation } from '@/assets/js/animations.js'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'
import api from '@/utils/api'
import defaultTherapistImage from '@/assets/images/dashboard/images.jpg'

export default {
  name: "ExpertsSection",
  mixins: [useScrollAnimation],
  components: { TitleSection },
  inject: ['languageState'],
  data() {
    return {
      currentIndex: 0,
      autoSlideInterval: null,
      experts: [],
      rawTherapists: [],
      loading: false,
      error: null
    }
  },
  computed: {
    translate() { return this.languageState?.translate || ((key)=>key) },
    currentLanguage() { return this.languageState?.currentLanguage?.value || 'ar' },
    decorativeClass() { return { 'opacity-5 scale-100': this.isVisible, 'opacity-0 scale-50': !this.isVisible } },
    navButtonClass() { return { 'opacity-100': this.isVisible, 'opacity-0': !this.isVisible } },
    expertImageClass() { 
      const dir = this.currentLanguage === 'ar' ? 'translate-x-8' : '-translate-x-8';
      return { 'opacity-100 translate-x-0': this.isVisible, [`opacity-0 ${dir}`]: !this.isVisible };
    },
    expertInfoClass() { 
      const dir = this.currentLanguage === 'ar' ? '-translate-x-8' : 'translate-x-8';
      return { 'opacity-100 translate-x-0': this.isVisible, [`opacity-0 ${dir}`]: !this.isVisible };
    },
    hasExperts() { return this.experts.length > 0 },
    visibleIndices() {
      const total = this.experts.length;
      if (!total) return [];
      const indices = [this.currentIndex];
      const prevIndex = this.currentIndex === 0 ? total - 1 : this.currentIndex - 1;
      if (total > 1 && !indices.includes(prevIndex)) indices.push(prevIndex);
      const nextIndex = this.currentIndex === total - 1 ? 0 : this.currentIndex + 1;
      if (total > 1 && !indices.includes(nextIndex)) indices.push(nextIndex);
      return indices.sort((a,b)=>a-b);
    }
  },
  watch: {
    'languageState.currentLanguage.value': { handler() { this.updateExpertsFromRaw() } }
  },
  created() { this.fetchExperts() },
  mounted() { this.startAutoSlide() },
  beforeUnmount() { this.stopAutoSlide() },
  methods: {
    async fetchExperts() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/therapists', { params: { per_page: 5, status: 'active', sort_by_rating: 1 }})
        this.rawTherapists = response.data?.data || []
        this.updateExpertsFromRaw()
        this.restartAutoSlide()
      } catch (error) {
        console.error('Failed to load therapists:', error)
        this.error = this.currentLanguage === 'ar' ? 'تعذر تحميل بيانات الأخصائيين حالياً.' : 'Unable to load specialists right now.'
        this.rawTherapists = []
        this.experts = []
      } finally { this.loading = false }
    },
    updateExpertsFromRaw() {
      if (!this.rawTherapists?.length) { this.experts=[]; this.currentIndex=0; return }
      this.experts = this.rawTherapists.map((t,idx)=>this.transformTherapist(t, idx))
      if(this.currentIndex >= this.experts.length) this.currentIndex = 0
    },
    transformTherapist(therapist, fallbackId = 0) {
      const name = this.currentLanguage==='ar'
        ? therapist.name_ar || therapist.name_en || therapist.user?.name || 'معالج متخصص'
        : therapist.name_en || therapist.name_ar || therapist.user?.name || 'Expert Therapist'

      const specialty = this.currentLanguage==='ar'
        ? therapist.specialty_ar || therapist.title_ar || therapist.specialty_en || therapist.title_en || 'أخصائي صحة نفسية'
        : therapist.specialty_en || therapist.title_en || therapist.specialty_ar || therapist.title_ar || 'Mental Health Specialist'

      let bio = this.currentLanguage==='ar'
        ? therapist.bio_ar || therapist.methodologies_ar || therapist.bio_en || therapist.methodologies_en || 'أخصائي معتمد يقدم خدمات دعم نفسي متكاملة.'
        : therapist.bio_en || therapist.methodologies_en || therapist.bio_ar || therapist.methodologies_ar || 'Certified specialist offering comprehensive psychological support.'

      if (Array.isArray(bio)) bio = bio.join('،')

      return {
        id: therapist.id ?? fallbackId,
        name,
        specialty,
        description: bio,
        image: therapist.avatar || defaultTherapistImage
      }
    },
    restartAutoSlide(){ this.stopAutoSlide(); this.startAutoSlide() },
    getProfileRoute(expert){ return expert?.id ? {name:'therapisteDetail', params:{id:expert.id}} : {name:'Specialists'} },
    getBookingRoute(expert){ return expert?.id ? {name:'therapisteDetail', params:{id:expert.id}, query:{book:'1'}} : {name:'Specialists'} },
    prevExpert(){ if(!this.experts.length)return; this.currentIndex = this.currentIndex===0? this.experts.length-1:this.currentIndex-1 },
    nextExpert(){ if(!this.experts.length)return; this.currentIndex = this.currentIndex===this.experts.length-1?0:this.currentIndex+1 },
    goToExpert(index){ if(!this.experts.length)return; this.currentIndex=index },
    startAutoSlide(){ if(this.autoSlideInterval || this.experts.length<=1) return; this.autoSlideInterval=setInterval(()=>{this.nextExpert()},5000) },
    stopAutoSlide(){ if(this.autoSlideInterval){ clearInterval(this.autoSlideInterval); this.autoSlideInterval=null } }
  }
}
</script>

<style scoped>
.rtl { direction: rtl; text-align: right; }
.ltr { direction: ltr; text-align: left; }
.transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4,0,0.2,1); }

@media (max-width:640px){ .text-2xl{font-size:1.5rem} .text-xl{font-size:1.25rem} .text-lg{font-size:1.125rem} .w-64{width:16rem;height:16rem} .gap-8{gap:1.5rem} .absolute.right-2, .absolute.left-2{top:40%} }
@media (max-width:480px){ .text-2xl{font-size:1.375rem} .text-xl{font-size:1.125rem} .w-64{width:14rem;height:14rem} .px-3{padding-left:0.75rem;padding-right:0.75rem} }
@media (max-width:768px){ .py-16{padding-top:3rem;padding-bottom:3rem} .mb-12{margin-bottom:2rem} .mt-8{margin-top:1.5rem} }
@media (max-width:640px){ .text-sm{font-size:0.875rem;line-height:1.5} }
</style>
