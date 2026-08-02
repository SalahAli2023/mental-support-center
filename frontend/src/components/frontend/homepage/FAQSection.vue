<template>
  <!-- قسم الأسئلة الشائعة -->
  <section 
    ref="sectionRef"
    class="relative py-20 bg-white  overflow-hidden transition-all duration-1000"
    :class="sectionClass"
  >
    <!-- أشكال زخرفية في الخلفية -->
    <div 
      class="absolute top-10 left-10 w-32 h-32 bg-primary-pink opacity-5 rounded-full blur-2xl transition-all duration-1000 delay-300"
      :class="decorativeClass"
    ></div>
    <div 
      class="absolute bottom-10 right-10 w-40 h-40 bg-primary-green opacity-5 rounded-full blur-2xl transition-all duration-1000 delay-500"
      :class="decorativeClass"
    ></div>
    <!-- شكل زخرفي إضافي -->
    <div 
      class="absolute top-1/2 left-1/4 w-24 h-24 bg-primary-pink opacity-3 rounded-full blur-xl transition-all duration-1000 delay-700"
      :class="decorativeClass"
    ></div>

    <div class="relative z-10 max-w-6xl mx-auto px-6">
      <!-- العنوان الرئيسي -->
      <header class="text-center mb-16">
         <TitleSection
          :mainText="translate('home.faq.title')"
          :highlightText="translate('home.faq.highlight')"
        />
        <p 
          class="text-lg text-gray-600 max-w-2xl mx-auto mt-4 transition-all duration-700"
          :class="contentItemClass"
        >
          {{ translate('home.faq.subtitle') }}
        </p>
      </header>

      <!-- شبكة الأسئلة - 4 يمين و 4 يسار -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
        <!-- العمود الأيمن - 4 أسئلة -->
        <div class="space-y-4">
          <article 
            v-for="(faq, index) in leftFaqs" 
            :key="faq.id"
            class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-500 overflow-hidden border border-gray-100"
            :class="{ 
              'ring-2 ring-primary-pink ring-opacity-50 shadow-lg': openQuestionId === faq.id,
              'opacity-0 translate-y-8': !isVisible,
              'opacity-100 translate-y-0': isVisible
            }"
            :style="{ 
              transitionDelay: `${index * 100 + 300}ms`
            }"
          >
            <!-- زر السؤال -->
            <button 
              @click="toggleQuestion(faq.id)"
              class="w-full px-6 py-5 text-start flex items-center justify-between text-gray-800 font-semibold hover:text-primary-pink transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-pink focus:ring-opacity-50"
              :class="{ 'text-primary-pink': openQuestionId === faq.id }"
            >
              <span class="flex items-center gap-3 text-start">
                <!-- رقم السؤال -->
                <span 
                  class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-pink text-white text-sm flex items-center justify-center font-bold transition-all duration-300 transform hover:scale-110"
                  :class="{ 'scale-0': !isVisible, 'scale-100': isVisible }"
                  :style="{ transitionDelay: `${index * 100 + 500}ms` }"
                >
                  {{ faq.id }}
                </span>
                <span class="text-base font-semibold">{{ faq.question }}</span>
              </span>
              
              <!-- أيقونة السهم للتوسيع/الطي -->
              <div class="flex items-center gap-2">
                <svg 
                  class="w-5 h-5 transition-transform duration-300" 
                  :class="{ 'rotate-180 text-primary-pink': openQuestionId === faq.id, 'text-gray-400': openQuestionId !== faq.id }"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            
            <!-- الإجابة (تظهر فقط عند النقر) -->
            <div 
              v-show="openQuestionId === faq.id"
              class="overflow-hidden transition-all duration-500 ease-in-out"
            >
              <div class="px-6 pb-5 text-gray-600 leading-relaxed border-t border-gray-100">
                <p class="text-base">{{ faq.answer }}</p>
              </div>
            </div>
          </article>
        </div>

        <!-- العمود الأيسر - 4 أسئلة -->
        <div class="space-y-4">
          <article 
            v-for="(faq, index) in rightFaqs" 
            :key="faq.id"
            class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-500 overflow-hidden border border-gray-100"
            :class="{ 
              'ring-2 ring-primary-pink ring-opacity-50 shadow-lg': openQuestionId === faq.id,
              'opacity-0 translate-y-8': !isVisible,
              'opacity-100 translate-y-0': isVisible
            }"
            :style="{ 
              transitionDelay: `${(index + 4) * 100 + 300}ms`
            }"
          >
            <!-- زر السؤال -->
            <button 
              @click="toggleQuestion(faq.id)"
              class="w-full px-6 py-5 text-start flex items-center justify-between text-gray-800 font-semibold hover:text-primary-pink transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-pink focus:ring-opacity-50"
              :class="{ 'text-primary-pink': openQuestionId === faq.id }"
            >
              <span class="flex items-center gap-3 text-right">
                <!-- رقم السؤال -->
                <span 
                  class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-pink text-white text-sm flex items-center justify-center font-bold transition-all duration-300 transform hover:scale-110"
                  :class="{ 'scale-0': !isVisible, 'scale-100': isVisible }"
                  :style="{ transitionDelay: `${(index + 4) * 100 + 500}ms` }"
                >
                  {{ faq.id }}
                </span>
                <span class="text-base font-semibold">{{ faq.question }}</span>
              </span>
              
              <!-- أيقونة السهم للتوسيع/الطي -->
              <div class="flex items-center gap-2">
                <svg 
                  class="w-5 h-5 transition-transform duration-300" 
                  :class="{ 'rotate-180 text-primary-pink': openQuestionId === faq.id, 'text-gray-400': openQuestionId !== faq.id }"
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </button>
            
            <!-- الإجابة (تظهر فقط عند النقر) -->
            <div 
              v-show="openQuestionId === faq.id"
              class="overflow-hidden transition-all duration-500 ease-in-out"
            >
              <div class="px-6 pb-5 text-gray-600 leading-relaxed border-t border-gray-100">
                <p class="text-base">{{ faq.answer }}</p>
              </div>
            </div>
          </article>
        </div>
      </div>

      <!-- قسم الاتصال الإضافي مع خلفية -->
      <div 
        class="relative rounded-3xl overflow-hidden"
        :class="buttonClass"
      >
        <!-- خلفية متصلة -->
        <div class="absolute inset-0 bg-primary-green"></div>
        
        <!-- أشكال زخرفية في الخلفية -->
        <div class="absolute top-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 right-0 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
        
        <!-- المحتوى -->
        <div class="relative z-10 text-center text-white p-12">
          <h3 class="text-2xl md:text-3xl font-bold mb-4">{{ translate('home.faq.contactTitle') }}</h3>
          <p class="text-lg opacity-90 mb-8 max-w-2xl mx-auto">
            {{ translate('home.faq.contactSubtitle') }}
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <RouterLink 
              :to="{ name: 'contact' }"
              class="bg-white text-primary-green font-bold py-4 px-8 rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform text-center"
              :class="{ 'opacity-0 translate-y-4': !isVisible, 'opacity-100 translate-y-0 hover:scale-105': isVisible }"
              style="transition-delay: 1100ms"
            >
              <span class="flex items-center justify-center gap-2">
                {{ translate('home.faq.contactUs') }}
                <i class="fas fa-phone"></i>
              </span>
            </RouterLink>
            <a 
              href="https://wa.me/966500000000"
              target="_blank"
              rel="noopener"
              class="bg-transparent text-white font-bold py-4 px-8 rounded-xl border-2 border-white hover:bg-white hover:text-primary-green transition-all duration-300 shadow-lg hover:shadow-xl transform text-center"
              :class="{ 'opacity-0 translate-y-4': !isVisible, 'opacity-100 translate-y-0 hover:scale-105': isVisible }"
              style="transition-delay: 1200ms"
            >
              <span class="flex items-center justify-center gap-2">
                {{ translate('home.faq.whatsapp') }}
                <i class="fab fa-whatsapp"></i>
              </span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { useScrollAnimation } from '@/assets/js/animations.js'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'
import { useTranslations } from '@/composables/useTranslations.js'
import api from '@/utils/api'

export default {
  name: "FAQSection",
  mixins: [useScrollAnimation],
  components: {
    TitleSection
  },
  setup() {
    const { currentLanguage, translate } = useTranslations()
    return {
      currentLanguage,
      translate
    }
  },
  data() {
    return {
      // بيانات الأسئلة الشائعة - سيتم تعبئتها ديناميكياً
      faqs: [],
      remoteFaqs: [],
      // لتتبع السؤال المفتوح حاليًا - null يعني لا يوجد سؤال مفتوح
      openQuestionId: null
    }
  },
  computed: {
    decorativeClass() {
      return {
        'opacity-5 scale-100': this.isVisible,
        'opacity-0 scale-50': !this.isVisible
      }
    },
    // تقسيم الأسئلة إلى عمودين
    leftFaqs() {
      return this.faqs.slice(0, 4)
    },
    rightFaqs() {
      return this.faqs.slice(4, 8)
    }
  },
  watch: {
    // مراقبة تغيير اللغة وتحديث الأسئلة
    currentLanguage: {
      immediate: true,
      handler() {
        this.updateFaqsData()
      }
    }
  },
  mounted() {
    // تحديث البيانات أول مرة
    this.updateFaqsData()
    this.fetchPublicFaqs()
    // تم إزالة السطر الذي كان يفتح أول سؤال تلقائياً
  },
  methods: {
    async fetchPublicFaqs() {
      try {
        const response = await api.get('/contact/messages/public')
        const data = response.data?.data || []
        this.remoteFaqs = data.map((item, index) => ({
          id: index + 1,
          question: item.question,
          answer: item.answer
        }))
        this.updateFaqsData()
      } catch (error) {
        console.error('Failed to fetch public FAQs', error)
      }
    },
    // دالة لفتح أو إغلاق الإجابة
    toggleQuestion(id) {
      this.openQuestionId = this.openQuestionId === id ? null : id
    },
    // تحديث بيانات الأسئلة بناءً على اللغة الحالية
    updateFaqsData() {
      const defaultFaqs = [
        {
          id: 1,
          question: this.translate('home.faq.questions.sessionDuration.question'),
          answer: this.translate('home.faq.questions.sessionDuration.answer')
        },
        {
          id: 2,
          question: this.translate('home.faq.questions.confidentiality.question'),
          answer: this.translate('home.faq.questions.confidentiality.answer')
        },
        {
          id: 3,
          question: this.translate('home.faq.questions.booking.question'),
          answer: this.translate('home.faq.questions.booking.answer')
        },
        {
          id: 4,
          question: this.translate('home.faq.questions.difference.question'),
          answer: this.translate('home.faq.questions.difference.answer')
        },
        {
          id: 5,
          question: this.translate('home.faq.questions.online.question'),
          answer: this.translate('home.faq.questions.online.answer')
        },
        {
          id: 6,
          question: this.translate('home.faq.questions.cost.question'),
          answer: this.translate('home.faq.questions.cost.answer')
        },
        {
          id: 7,
          question: this.translate('home.faq.questions.changeSpecialist.question'),
          answer: this.translate('home.faq.questions.changeSpecialist.answer')
        },
        {
          id: 8,
          question: this.translate('home.faq.questions.paymentMethods.question'),
          answer: this.translate('home.faq.questions.paymentMethods.answer')
        }
      ]

      if (this.remoteFaqs.length) {
        this.faqs = [
          ...this.remoteFaqs.map((faq, idx) => ({
            id: idx + 1,
            question: faq.question,
            answer: faq.answer
          })),
          ...defaultFaqs
        ]
      } else {
        this.faqs = defaultFaqs
      }
    }
  }
}
</script>

<style scoped>
/* تحسين الانتقالات */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* تأثير انتقالي سلس للإجابات */
.overflow-hidden {
  transition: max-height 0.5s ease-in-out;
}

/* تحسين التجاوب */
@media (max-width: 768px) {
  .text-3xl {
    font-size: 2rem;
  }
  
  .text-lg {
    font-size: 1rem;
  }
}

/* تأثيرات إضافية للأسئلة */
article {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

article:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* تأثير على الأرقام الدائرية */
.w-8.h-8 {
  transition: all 0.3s ease;
}

.w-8.h-8:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(214, 162, 154, 0.3);
}
</style>