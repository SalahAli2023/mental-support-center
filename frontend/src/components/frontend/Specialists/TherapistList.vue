<template>
  <div :dir="isRTL ? 'rtl' : 'ltr'">
    <Header />

    <Hero
      :titleKey="'therapists.hero.title'"
      :highlightKey="'therapists.hero.highlight'"
      :subtitleKey="'therapists.hero.subtitle'"
      :buttons="heroButtons"
    />

    <!-- Loading -->
    <div v-if="loading" class="max-w-7xl mx-auto px-4 py-16 text-center">
      <i class="fas fa-spinner fa-spin text-4xl text-brand-500 mb-4"></i>
      <p class="text-gray-600">
        {{ currentLang === 'ar' ? 'جاري تحميل المعالجين...' : 'Loading therapists...' }}
      </p>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="max-w-7xl mx-auto px-4 py-16 text-center">
      <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
      <p class="text-red-600 mb-4">{{ error }}</p>
      <button
        @click="fetchTherapists"
        class="px-6 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600"
      >
        {{ currentLang === 'ar' ? 'إعادة المحاولة' : 'Retry' }}
      </button>
    </div>

    <!-- Content -->
    <TherapistListContent
      v-else
      :therapists="localizedTherapists"
      :specializations="specializations"
      :isRTL="isRTL"
      :translate="translate"
    />

    <Footer />
  </div>
</template>

<script>
import Header from '@/components/frontend/layouts/header.vue'  
import Footer from '@/components/frontend/layouts/footer.vue' 
import Hero from '@/components/frontend/layouts/hero.vue'
import TherapistListContent from '@/components/frontend/Specialists/TherapistListContent.vue'

import { inject, computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'
import { resolveMediaUrl } from '@/utils/media'

export default {
  name: 'TherapistList',

  components: {
    Header,
    Footer,
    Hero,
    TherapistListContent
  },

  setup() {
    const { translate } = useTranslations()

    // ⬅️ inject المصدر الحقيقي للغة
    const languageState = inject('languageState')

    // ⬅️ تحويل اللغة إلى computed تفاعلي حقيقي
    const currentLang = computed(() => {
      return languageState?.currentLanguage?.value || 'ar'
    })

    const isRTL = computed(() => currentLang.value === 'ar')

    const heroButtons = computed(() => [
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
    ])

    return {
      translate,
      currentLang,
      isRTL,
      heroButtons
    }
  },

  data() {
    return {
      therapists: [],
      loading: false,
      error: null,

      specializations: [
        { key: 'anxiety', ar: 'القلق والتوتر', en: 'Anxiety & Stress' },
        { key: 'depression', ar: 'الاكتئاب', en: 'Depression' },
        { key: 'ocd', ar: 'الوسواس القهري', en: 'OCD' },
        { key: 'addiction', ar: 'الإدمان', en: 'Addiction' },
        { key: 'psychosomatic', ar: 'الأمراض النفسجسمانية', en: 'Psychosomatic Disorders' },
        { key: 'confidence', ar: 'ضعف الثقة بالنفس', en: 'Low Self-Confidence' },
        { key: 'teenagers', ar: 'مشكلات المراهقين', en: 'Teenage Problems' },
        { key: 'specialEducation', ar: 'التربية الخاصة', en: 'Special Education' },
        { key: 'various', ar: 'مشكلات منوعة', en: 'Various Issues' }
      ]
    }
  },

  computed: {
    localizedTherapists() {
      const lang = this.currentLang

      return this.therapists.map(t => ({
        id: t.id,

        name:
          lang === 'ar'
            ? t.name_ar || 'غير محدد'
            : t.name_en || 'Unknown',

        title:
          lang === 'ar'
            ? t.title_ar || 'أخصائي صحة نفسية'
            : t.title_en || 'Mental Health Specialist',

        description:
          lang === 'ar'
            ? t.bio_ar || 'لا يوجد وصف'
            : t.bio_en || 'Description not available',

        avatar: t.avatar,
        rating: t.rating,
        rating_count: t.rating_count,
        gender: t.gender,
        specializations: t.specializations,
        session_duration: t.session_duration
      }))
    }
  },

  async mounted() {
    await this.fetchTherapists()
  },

  methods: {
    async fetchTherapists() {
      this.loading = true
      this.error = null

      try {
        const api = (await import('@/utils/api')).default
        const response = await api.get('/therapists', { params: { per_page: 1000 } })

        const data = response.data?.data || []

        this.therapists = data.map(t => ({
          id: t.id,
          name_ar: t.name_ar || '',
          name_en: t.name_en || '',
          title_ar: t.title_ar || '',
          title_en: t.title_en || '',
          bio_ar: t.bio_ar || '',
          bio_en: t.bio_en || '',
          avatar: resolveMediaUrl(
            t.avatar || t.user?.avatar,
            '/images/default-female-avatar.png'
          ),
          rating: t.rating ? Number(t.rating) : null,
          rating_count: t.rating_count || 0,
          gender: t.gender || 'male',
          session_duration: t.session_duration || 45,
          specializations: this.parseSpecializations(
            t.specialty_ar || t.specialty_en || ''
          )
        }))

        if (!this.therapists.length) {
          this.error =
            this.currentLang === 'ar'
              ? 'لا توجد معالجين متاحين حالياً'
              : 'No therapists available at the moment'
        }
      } catch (e) {
        console.error(e)
        this.error =
          this.currentLang === 'ar'
            ? 'فشل تحميل المعالجين'
            : 'Failed to load therapists'
        this.therapists = []
      } finally {
        this.loading = false
      }
    },

    parseSpecializations(text) {
      if (!text) return ['various']

      const value = text.toLowerCase()
      const list = []

      if (value.includes('قلق') || value.includes('anxiety')) list.push('anxiety')
      if (value.includes('اكتئاب') || value.includes('depression')) list.push('depression')
      if (value.includes('وسواس') || value.includes('ocd')) list.push('ocd')
      if (value.includes('إدمان') || value.includes('addiction')) list.push('addiction')

      return list.length ? list : ['various']
    }
  }
}
</script>
