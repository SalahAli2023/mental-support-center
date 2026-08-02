<template>
  <div class="min-h-screen bg-gray-50 font-almarai transition-colors duration-300">
    <Header />

    <!-- قسم الهيرو  -->
    <Hero :title="translate('measuresHero.title')" :highlight="translate('measuresHero.titleKey')"
      :subtitle="translate('measuresHero.description')" :subtitleKey="translate('measuresHero.subtitle')" :buttons="[
        { text: translate('measureModal.startTest'), icon: 'fas fa-play-circle', primary: true },
        { text: translate('buttons.learnMore'), icon: 'fas fa-info-circle', primary: false }
      ]" />

    <main class="max-w-7xl mx-auto px-6">
      <!-- حالة التحميل -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-green"></div>
        <span class="mr-3 text-gray-600">{{ translate('loading') }}</span>
      </div>

      <!-- حالة الخطأ -->
      <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center my-8">
        <div class="flex items-center justify-center gap-2 text-red-700 mb-2">
          <i class="fas fa-exclamation-triangle"></i>
          <span class="font-medium">{{ error }}</span>
        </div>
        <button @click="fetchMeasuresData"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
          {{ translate('retry') }}
        </button>
      </div>

      <!-- المحتوى الرئيسي -->
      <div v-else class="max-w-7xl mx-auto px-4 py-8">
        <!-- المقاييس الأكثر استخداماً -->
        <!-- <PopularMeasures :measures="popularMeasures" :language="currentLanguage" @measure-click="handleMeasureClick" /> -->
      
        <!-- شريط البحث والتصفية الجديد -->
        <CategoryFilter 
          :categories="categories"
          :activeCategory="activeFilter"
          :measures="scales"
          @category-change="handleFilterChange"
          @search-change="handleSearchChange"
        />

        <!-- عرض جميع المقاييس -->
        <AllMeasuresContainer 
          :measures="filteredMeasures"
          :activeFilter="activeFilter"
          :language="currentLanguage"
          @measure-click="handleMeasureClick" />
        <!-- الإرشادات -->
        <GuidelinesSection :language="currentLanguage" />
      </div>
    </main>

    <Footer />

    <!-- مودال التسجيل -->
    <!-- مودال الاختبار (يظهر مباشرة إذا كان المستخدم مسجلاً الدخول) -->
    <MeasureModal v-if="showMeasureModal" :measure="currentMeasure" :testStep="testStep"
      :currentQuestionIndex="currentQuestionIndex" :answers="answers" :testResult="testResult"
      :language="currentLanguage" @close="closeMeasureModal" @start-test="startTest" @next-question="nextQuestion"
      @previous-question="previousQuestion" @submit-test="submitTest" @retake-test="retakeTest"
      @show-other-measures="showOtherMeasures" />
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import Header from '@/components/frontend/layouts/Header.vue'  
import Hero from '@/components/frontend/layouts/hero.vue'
import PopularMeasures from '@/components/frontend/measures/PopularMeasures.vue'
import CategoryFilter from '@/components/frontend/measures/CategoryFilter.vue'
import AllMeasuresContainer from '@/components/frontend/measures/AllMeasuresContainer.vue'
import GuidelinesSection from '@/components/frontend/measures/GuidelinesSection.vue'
import MeasureModal from '@/components/frontend/measures/MeasureModal.vue'
import Footer from '@/components/frontend/layouts/Footer.vue' 
import { useScalesStore } from '@/stores/scales'
import { useCategoriesStore } from '@/stores/categories' // استيراد store التصنيفات
import { useRoute, useRouter } from 'vue-router'
import { resourcesData } from '@/data/measures'
import api from '@/utils/api'
import { useTranslations } from '@/composables/useTranslations'
import { useProfile } from '@/composables/useProfile'
import { useNotifications } from '@/composables/useNotifications'

export default {
  name: 'MeasuresPage',
  components: {
    Header,
    Footer,
    Hero,
    PopularMeasures,
    CategoryFilter,
    AllMeasuresContainer,
    GuidelinesSection,
    MeasureModal,
    // تم حذف مودال التسجيل من صفحة المقاييس
  },
  setup() {
    // استخدام الـ store
    const scalesStore = useScalesStore()
    const categoriesStore = useCategoriesStore()
    const route = useRoute()
    const router = useRouter()

    // الحالة العامة
    const searchQuery = ref('')
    const activeFilter = ref('all')
    const showMeasureModal = ref(false)
    const currentMeasure = ref(null)
    const testStep = ref('info')
    const currentQuestionIndex = ref(0)
    const answers = ref([])
    const testResult = ref(null)
    const currentLanguage = ref(localStorage.getItem('preferredLanguage') || 'ar')
    const pendingMeasureId = ref(route.query.measureId || null)

    // تحديث اللغة تلقائيًا عند تغييرها من الهيدر
    const handleLanguageChange = (event) => {
      currentLanguage.value = event.detail.language
    }

    onMounted(() => {
      window.addEventListener('languageChanged', handleLanguageChange)
      fetchMeasuresData()
    })

    onUnmounted(() => {
      window.removeEventListener('languageChanged', handleLanguageChange)
    })

    // البيانات
    const resources = ref(resourcesData)

    // الحسابات
    // البيانات من الـ stores
    const scales = computed(() => scalesStore.scales)
    const categories = computed(() => categoriesStore.categories) // التصنيفات من الـ store
    const loading = computed(() => scalesStore.loading)
    const error = computed(() => scalesStore.error)

    // تصفية المقاييس حسب البحث والتصنيف
    const filteredMeasures = computed(() => {
      let filtered = scales.value.filter(scale => scale.is_active) // فقط النشطة
      // تصفية حسب التصنيف
      if (activeFilter.value !== 'all') {
        filtered = filtered.filter(measure => 
          measure.category_id === activeFilter.value || 
          measure.category?.id === activeFilter.value
        )
      }

      // تصفية حسب البحث
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(measure => {
          const titleAr = measure.name_ar?.toLowerCase() || ''
          const titleEn = measure.name_en?.toLowerCase() || ''
          const descAr = measure.description_ar?.toLowerCase() || ''
          const descEn = measure.description_en?.toLowerCase() || ''

          return titleAr.includes(query) ||
            titleEn.includes(query) ||
            descAr.includes(query) ||
            descEn.includes(query)
        })
      }

      return filtered
    })

    const popularMeasures = computed(() => {
      // المقاييس النشطة فقط - يمكن لاحقاً الاعتماد على حقل popularity من الباك إند
      return scales.value
        .filter(scale => scale.is_active)
        .slice(0, 4)
    })

    // دوال جلب البيانات
    const fetchMeasuresData = async () => {
      try {
        // جلب التصنيفات أولاً
        await categoriesStore.fetchCategories(true) // public access
        
        // ثم جلب المقاييس
        await scalesStore.fetchScales({ 
          is_active: true,
          locale: currentLanguage.value 
        })
        
        openMeasureFromQuery()
      } catch (err) {
        console.error('❌ فشل في تحميل البيانات:', err)
      }
    }

    // تحويل بيانات الـ API لتتوافق مع المكونات
    const transformScaleForFrontend = (scale) => {
      // تحويل مباشر قدر الإمكان من بيانات الباك إند إلى الشكل الذي تحتاجه الواجهة
      return {
        id: scale.id,
        name_ar: scale.name_ar,
        name_en: scale.name_en,
        description_ar: scale.description_ar,
        description_en: scale.description_en,
        image_url: scale.image_url || null,
        category: scale.category || null,
        category_id: scale.category_id,
        time: scale.estimated_time || '5-10',
        questions: scale.questions || [],
        interpretations: scale.interpretations || [],
        questions_count: scale.questions_count || (scale.questions ? scale.questions.length : 0),
        is_active: scale.is_active,
        // قيم تجميلية يمكن لاحقاً أخذها من الباك إند إذا أضيفت
        rating: scale.rating || 0,
        reviews: scale.reviews || 0,
      }
    }

    const getCategoryIcon = (category) => {
      const icons = {
        'نساء': 'fas fa-female',
        'أطفال': 'fas fa-child',
        'متخصصين': 'fas fa-user-md'
      }
      return icons[category] || 'fas fa-chart-bar'
    }

    // الدوال
    const { translate } = useTranslations()
    const { isAuthenticated } = useProfile()
    const { showError } = useNotifications()
    const handleFilterChange = (categoryId) => {
      activeFilter.value = categoryId
    }

    const handleSearchChange = (query) => {
      searchQuery.value = query
    }

    const handleMeasureClick = (measure) => {
      // إذا لم يكن مسجلاً → توست ثم تحويله لصفحة تسجيل الدخول
      if (!isAuthenticated()) {
        const message =
          currentLanguage.value === 'ar'
            ? 'يجب إنشاء حساب وتسجيل الدخول لإجراء الاختبارات النفسية.'
            : 'You need to create an account and log in to take psychological assessments.'

        showError(message)
        router.push('/login')
        return
      }

      // 1) افتح المودال فوراً ببيانات المقياس الأساسية (بدون انتظار تحميل الأسئلة الكاملة)
      currentMeasure.value = transformScaleForFrontend(measure)
      openMeasureModal()

      // 2) قم بتحميل المقياس الكامل (الأسئلة + التفسيرات) في الخلفية وتحديث المودال عندما تجهز
      loadFullScale(measure.id)
    }

    const loadFullScale = async (id) => {
      try {
        const fullScale = await scalesStore.fetchFullScale(id)
        currentMeasure.value = transformScaleForFrontend(fullScale)

        // إعادة تهيئة الإجابات إذا كنا لسنا في شاشة النتائج
        if (testStep.value !== 'results') {
          answers.value = new Array(currentMeasure.value.questions.length).fill(undefined)
        }
      } catch (err) {
        console.error('فشل في جلب بيانات المقياس الكامل:', err)
        const msg =
          currentLanguage.value === 'ar'
            ? 'حدث خطأ أثناء تحميل أسئلة المقياس. يمكنك المحاولة مرة أخرى لاحقاً.'
            : 'An error occurred while loading the assessment questions. You can try again later.'
        showError(msg)
      }
    }

    const openMeasureFromQuery = async () => {
      if (!pendingMeasureId.value) return
      const target = scales.value.find(scale => String(scale.id) === String(pendingMeasureId.value))

      if (target) {
        // نفس منطق الضغط على المقياس
        await handleMeasureClick(target)
        pendingMeasureId.value = null
        removeMeasureQuery()
      }
    }

    const removeMeasureQuery = () => {
      if (route.query.measureId) {
        const newQuery = { ...route.query }
        delete newQuery.measureId
        router.replace({ query: newQuery })
      }
    }

    const openMeasureModal = () => {
      showMeasureModal.value = true
      testStep.value = 'info'
      currentQuestionIndex.value = 0
      answers.value = []
   // answers.value = new Array(currentMeasure.value.questions.length).fill(undefined)

      testResult.value = null
    }

    const closeMeasureModal = () => {
      showMeasureModal.value = false
      currentMeasure.value = null
    }

    const startTest = () => {
      // التحقق من تسجيل الدخول قبل بدء الاختبار
      if (!isAuthenticated()) {
        const message =
          currentLanguage.value === 'ar'
            ? 'يجب إنشاء حساب وتسجيل الدخول لإجراء الاختبارات النفسية.'
            : 'You need to create an account and log in to take psychological assessments.'
        showError(message)
        router.push('/login')
        return
      }

      // إذا كان مسجلاً، ابدأ الاختبار
      testStep.value = 'questions'
    }

    const nextQuestion = () => {
      if (currentQuestionIndex.value < currentMeasure.value.questions.length - 1) {
        currentQuestionIndex.value++
      }
    }

    const previousQuestion = () => {
      if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--
      }
    }

    const submitTest = async () => {
      if (!currentMeasure.value || !currentMeasure.value.questions || currentMeasure.value.questions.length === 0) {
        return
      }

      testStep.value = 'loading'

      try {
        // تجهيز بيانات الإجابات بالشكل الذي يتوقعه الـ API
        const answersPayload = currentMeasure.value.questions
          .map((question, index) => {
            const userAnswer = answers.value[index]

            if (userAnswer === undefined || userAnswer === null || userAnswer === '') {
              return null
            }

            // للأسئلة ذات الخيارات (multiple_choice و غيرها)
            if (Array.isArray(question.options) && question.options.length > 0) {
              // في multiple_choice نخزن رقم الخيار (index)
              // نختار الـ option حسب الـ index أو القيمة المطابقة
              let chosenOption = null

              if (typeof userAnswer === 'number') {
                chosenOption = question.options[userAnswer] || null
              } else {
                // في حال تم تخزين القيمة نفسها (مثلاً 1-5)، نحاول مطابقتها مع score_value
                chosenOption =
                  question.options.find(
                    (opt) => opt.score_value === userAnswer || opt.option_order === userAnswer
                  ) || null
              }

              if (!chosenOption || !chosenOption.id) {
                return null
              }

              return {
                question_id: question.id,
                option_id: chosenOption.id,
              }
            }

            // إذا لم تكن هناك خيارات واضحة، نتجاهل السؤال في الحفظ
            return null
          })
          .filter((item) => item !== null)

        // استدعاء الـ API لحساب النتيجة وحفظها في قاعدة البيانات للمستخدم الحالي
        const response = await api.post(`/frontend/scales/${currentMeasure.value.id}/submit`, {
          answers: answersPayload,
        })

        const data = response.data?.data || {}

        // استخدام النتيجة والتفسير القادمين من الباك إند
        testResult.value = {
          score: Math.round(data.score ?? 0),
          maxScore: Math.round(data.max_score ?? 0),
          interpretation: data.interpretation
            ? {
              level:
                currentLanguage.value === 'ar'
                  ? data.interpretation.interpretation_label_ar || data.interpretation.interpretation_label_en
                  : data.interpretation.interpretation_label_en || data.interpretation.interpretation_label_ar,
              desc:
                currentLanguage.value === 'ar'
                  ? data.interpretation.description_ar || data.interpretation.description_en
                  : data.interpretation.description_en || data.interpretation.description_ar,
            }
            : getInterpretation(0, 0, currentLanguage.value),
        }

        testStep.value = 'results'
      } catch (error) {
        console.error('❌ فشل في حفظ نتيجة المقياس:', error)

        // في حال فشل الاتصال، نرجع للحساب المحلي كخطة بديلة (بدون حفظ في القاعدة)
        calculateResults()
      }
    }

    const calculateResults = () => {
      let score = 0
      const measure = currentMeasure.value

      // حساب النتيجة بناء على الإجابات
      answers.value.forEach((answer, index) => {
        if (answer !== undefined && measure.questions[index]?.options) {
          const option = measure.questions[index].options[answer]
          score += option?.score_value || 0
        }
      })

      // حساب أقصى درجة ممكنة
      const maxScore = measure.questions?.reduce((total, question) => {
        const maxOptionScore = Math.max(...question.options.map(opt => opt.score_value || 0))
        return total + maxOptionScore
      }, 0) || 0

      // محاولة استخدام تفسيرات المقياس القادمة من القاعدة أولاً
      const interpretationFromDb = getInterpretationFromScale(score, measure, currentLanguage.value)

      // إذا لم تتوفر تفسيرات من القاعدة نستخدم التفسير الافتراضي
      const interpretation = interpretationFromDb || getInterpretation(score, maxScore, currentLanguage.value)

      testResult.value = {
        score: Math.round(score),
        maxScore: Math.round(maxScore),
        interpretation
      }

      testStep.value = 'results'
    }

    const getInterpretation = (score, maxScore, language) => {
      const percentage = maxScore > 0 ? (score / maxScore) * 100 : 0

      if (percentage >= 80) {
        return {
          level: language === 'ar' ? 'مرتفع' : 'High',
          desc: language === 'ar'
            ? 'نتيجتك تشير إلى مستوى مرتفع. ننصح بمراجعة مختص للدعم المناسب.'
            : 'Your results indicate a high level. We recommend consulting a specialist for appropriate support.'
        }
      } else if (percentage >= 50) {
        return {
          level: language === 'ar' ? 'متوسط' : 'Medium',
          desc: language === 'ar'
            ? 'نتيجتك تشير إلى مستوى متوسط. ننصح بممارسة تقنيات الاسترخاء.'
            : 'Your results indicate a medium level. We recommend practicing relaxation techniques.'
        }
      } else {
        return {
          level: language === 'ar' ? 'منخفض' : 'Low',
          desc: language === 'ar'
            ? 'نتيجتك تشير إلى مستوى منخفض. حافظ على ممارسة العادات الصحية.'
            : 'Your results indicate a low level. Maintain healthy habits.'
        }
      }
    }

    const getInterpretationFromScale = (score, measure, language) => {
      if (!measure || !Array.isArray(measure.interpretations) || measure.interpretations.length === 0) {
        return null
      }

      const matched = measure.interpretations.find(inter => {
        const min = typeof inter.min_score === 'number' ? inter.min_score : parseFloat(inter.min_score)
        const max = typeof inter.max_score === 'number' ? inter.max_score : parseFloat(inter.max_score)
        if (isNaN(min) || isNaN(max)) return false
        return score >= min && score <= max
      })

      if (!matched) return null

      const level =
        language === 'ar'
          ? matched.interpretation_label_ar || matched.interpretation_label_en || ''
          : matched.interpretation_label_en || matched.interpretation_label_ar || ''

      const desc =
        language === 'ar'
          ? matched.description_ar || matched.description_en || ''
          : matched.description_en || matched.description_ar || ''

      return { level, desc }
    }

    const retakeTest = () => {
      testStep.value = 'info'
      currentQuestionIndex.value = 0
      answers.value = new Array(currentMeasure.value.questions.length).fill(undefined)
      testResult.value = null
    }

    const showOtherMeasures = () => {
      closeMeasureModal()
    }

    watch(() => route.query.measureId, (newValue) => {
      if (newValue) {
        pendingMeasureId.value = newValue
        openMeasureFromQuery()
      }
    })

    watch(scales, () => {
      openMeasureFromQuery()
    })

    const switchToLogin = () => {
      console.log('Switch to login')
    }

    return {
      searchQuery,
      activeFilter,
      showMeasureModal,
      currentMeasure,
      testStep,
      currentQuestionIndex,
      answers,
      testResult,
      scales,
      categories,
      resources,
      filteredMeasures,
      popularMeasures,
      currentLanguage,
      loading,
      error,
      translate,
      handleFilterChange,
      handleSearchChange,
      handleMeasureClick,
      openMeasureModal,
      closeMeasureModal,
      startTest,
      nextQuestion,
      previousQuestion,
      submitTest,
      retakeTest,
      showOtherMeasures,
      switchToLogin,
      fetchMeasuresData,
      openMeasureFromQuery
    }
  }
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, rgba(158, 191, 59, 0.05) 0%, rgba(214, 162, 154, 0.05) 100%);
}
</style>