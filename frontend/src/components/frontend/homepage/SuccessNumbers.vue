<template>
  <section 
    ref="sectionRef"
    class="relative py-16 bg-gradient-to-br from-primary-green/10 via-primary-pink/10 to-white text-gray-800 overflow-hidden transition-all duration-1000"
    :class="sectionClass"
  >
    <!-- أشكال زخرفية في الخلفية -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-primary-green opacity-10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-80 h-80 bg-primary-pink opacity-10 rounded-full blur-3xl"></div>

    <div class="max-w-6xl mx-auto px-4 text-center relative z-10">
      <!-- العنوان -->
      <div class="inline-block relative text-center w-full mb-16">
        <TitleSection
          :mainText="translate('home.stats.title')"
          :highlightText="translate('home.stats.highlight')"
        />
      </div>

      <!-- الأرقام -->
      <div v-if="loading" class="py-10 text-center text-gray-500">
        <i class="fas fa-spinner fa-spin text-3xl mb-4 text-primary-green"></i>
        <p>{{ currentLanguage === 'ar' ? 'جاري تحميل الإحصائيات...' : 'Loading statistics...' }}</p>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-10">
        <div
          v-for="(stat, index) in stats"
          :key="stat.id"
          class="flex flex-col items-center justify-end transition-all duration-700"
          :class="contentItemClass"
        >
          <div class="w-24 h-24 mb-4 flex items-center justify-center">
            <img
              :src="getStatIcon(stat, index)"
              :alt="getStatLabel(stat)"
              :class="['w-full h-full object-contain transition-all duration-700', getIconClass(stat)]"
            >
          </div>
          <span class="text-3xl md:text-4xl font-bold text-primary-green">
            {{ animatedCounters[index] ?? 0 }}
          </span>
          <p class="mt-2 text-base md:text-lg text-gray-700">
            {{ getStatLabel(stat) }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { useScrollAnimation } from '@/assets/js/animations.js'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'
import api from '@/utils/api'
import countriesIcon from '@/assets/images/Statistics/دولة.png'
import questionsIcon from '@/assets/images/Statistics/سؤال.png'
import sessionsIcon from '@/assets/images/Statistics/جلسات.png'
import usersIcon from '@/assets/images/Statistics/مستخدمون.png'

const ICON_FALLBACKS = [
  countriesIcon,
  questionsIcon,
  sessionsIcon,
  usersIcon
]

const ICON_MAP = {
  countries: countriesIcon,
  questions: questionsIcon,
  sessions: sessionsIcon,
  users: usersIcon
}

const DEFAULT_STATS = [
  { id: 1, key: 'countries', label_ar: 'دول', label_en: 'Countries', value: 12, icon: countriesIcon },
  { id: 2, key: 'questions', label_ar: 'أسئلة', label_en: 'Questions', value: 4500, icon: questionsIcon },
  { id: 3, key: 'sessions', label_ar: 'جلسات', label_en: 'Sessions', value: 2105, icon: sessionsIcon },
  { id: 4, key: 'users', label_ar: 'مستخدمون', label_en: 'Users', value: 3320, icon: usersIcon }
]

export default {
  name: "StatsSection",
  mixins: [useScrollAnimation],
  components: { TitleSection },
  inject: ['languageState'],
  data() {
    return {
      stats: [],
      animatedCounters: [],
      targetValues: [],
      animationStarted: false,
      loading: false,
      error: null
    }
  },
  computed: {
    translate() { return this.languageState?.translate || ((key) => key) },
    currentLanguage() { return this.languageState?.currentLanguage?.value || 'ar' }
  },
  watch: {
    isVisible(newVal) {
      if (newVal) {
        this.startAnimationIfNeeded()
      }
    },
    stats: {
      handler() {
        this.animationStarted = false
        this.resetCounters()
        this.startAnimationIfNeeded()
      },
      deep: true
    }
  },
  created() {
    this.fetchStats()
  },
  methods: {
    async fetchStats() {
      this.loading = true
      this.error = null
      try {
        const response = await api.get('/site-stats')
        this.stats = response.data?.data || DEFAULT_STATS
        this.targetValues = this.stats.map(stat => Number(stat.value) || 0)
        this.animatedCounters = new Array(this.stats.length).fill(0)
        this.startAnimationIfNeeded()
      } catch (error) {
        console.error('Failed to load statistics:', error)
        this.stats = DEFAULT_STATS
        this.targetValues = DEFAULT_STATS.map(stat => Number(stat.value) || 0)
        this.animatedCounters = new Array(this.stats.length).fill(0)
        this.startAnimationIfNeeded()
      } finally {
        this.loading = false
      }
    },
    startAnimationIfNeeded() {
      if (this.animationStarted || !this.stats.length) return
      this.animationStarted = true
      this.startCountAnimation()
    },
    getStatLabel(stat) {
      if (!stat) return ''
      return this.currentLanguage === 'ar' ? stat.label_ar : stat.label_en
    },
    getStatIcon(stat, index) {
      if (stat?.key && ICON_MAP[stat.key]) return ICON_MAP[stat.key]
      return ICON_FALLBACKS[index] || ICON_FALLBACKS[0]
    },
    getIconClass(stat) {
      const key = stat?.key
      if (key === 'countries' || key === 'questions') return 'light-green-filter'
      if (key === 'sessions' || key === 'users') return 'light-pink-filter'
      return ''
    },
    startCountAnimation() {
      this.targetValues.forEach((target, index) => {
        this.animateCounter(index, target, 0) // كل الأرقام تبدأ فوراً
      })
    },
    animateCounter(index, target, delay) {
      setTimeout(() => {
        let current = 0
        const duration = 800 // سرعة الحركة (800ms)
        const increment = target / (duration / 16 || 1) // تحديث كل إطار

        const updateCounter = () => {
          current += increment
          if (current < target) {
            this.animatedCounters.splice(index, 1, Math.floor(current))
            requestAnimationFrame(updateCounter)
          } else {
            this.animatedCounters.splice(index, 1, target)
          }
        }

        updateCounter()
      }, delay)
    },
    resetCounters() {
      this.animatedCounters = new Array(this.stats.length || 4).fill(0)
    }
  }
};
</script>

<style scoped>
.light-green-filter {
  filter: invert(56%) sepia(67%) saturate(385%) hue-rotate(35deg) brightness(95%) contrast(85%) opacity(0.7);
}

.light-pink-filter {
  filter: invert(76%) sepia(10%) saturate(600%) hue-rotate(320deg) brightness(90%) contrast(85%) opacity(0.7);
}

/* تحسين التجاوب */
@media (max-width: 768px) {
  .text-3xl { font-size: 2rem; }
  .text-base { font-size: 0.9rem; }
  .w-24 { width: 4rem; height: 4rem; }
}

/* تحسين الانتقالات */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
