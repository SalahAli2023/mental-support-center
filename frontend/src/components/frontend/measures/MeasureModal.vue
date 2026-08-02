<template>
  <div class="fixed inset-0 z-900 flex items-center justify-center p-4" :dir="language === 'ar' ? 'rtl' : 'ltr'">
    <!-- خلفية داكنة ناعمة -->
    <div class="modal-overlay fixed inset-0 bg-black/40"></div>

    <!-- بطاقة المودال الرئيسية -->
    <div
      class="relative w-full max-w-4xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl animate-slide-up border border-gray-100">
      <!-- الهيدر -->
      <div
        class="sticky top-0 z-10 border-b border-gray-200 bg-white/95 backdrop-blur-sm px-6 py-4 flex items-start justify-between">
        <div class="flex-1 min-w-0">
          <h1 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-1 break-words">
            {{ getTranslatedTitle(measure) }}
          </h1>
          <p class="text-sm text-gray-600">
            {{ getTranslatedDescription(measure) }}
          </p>
        </div>
        <button @click="$emit('close')"
          class="flex-shrink-0 text-gray-400 hover:text-gray-700 transition-colors p-2 rounded-full hover:bg-gray-100 ml-4"
          aria-label="Close">
          <i class="fas fa-times text-lg"></i>
        </button>
      </div>

      <div class="px-6 py-5">
        <!-- معلومات الاختبار (شاشة تمهيدية قبل البدء) -->
        <div v-if="testStep === 'info'" class="flex flex-col items-center justify-center min-h-[260px]">
          <div class="w-full max-w-xl text-center space-y-5">
            <!-- أيقونة وتعريف عام -->
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary-green/10 mb-1">
              <i class="fas fa-info-circle text-primary-green text-2xl"></i>
            </div>

            <h3 class="text-xl font-semibold text-gray-900">
              {{ translate('measureModal.importantInfo') }}
            </h3>

            <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
              {{ translate('measureModal.aboutTest') }}
            </p>

            <!-- معلومات مختصرة عن هذا الاختبار -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm mt-2">
              <div class="flex items-center justify-center gap-2 text-gray-700">
                <i class="fas fa-question-circle text-primary-green"></i>
                <span>{{ measure.questions.length }} {{ translate('measureModal.questionsCount') }}</span>
              </div>
              <div class="flex items-center justify-center gap-2 text-gray-700">
                <i class="fas fa-clock text-primary-green"></i>
                <span>{{ getEstimatedTime(measure) }} {{ translate('measureModal.time') }}</span>
              </div>
            </div>

            <!-- نقاط إرشادية -->
            <ul class="text-xs sm:text-sm text-gray-600 space-y-1 mt-4 list-disc list-inside text-right"
              :class="language === 'ar' ? 'mr-4' : 'ml-4'">
              <li>{{ translate('measureModal.infoList.awareness') }}</li>
              <li>{{ translate('measureModal.infoList.confidentiality') }}</li>
              <li>{{ translate('measureModal.infoList.stopAnytime') }}</li>
            </ul>

            <!-- زر ابدأ الاختبار في المنتصف -->
            <div class="pt-4 flex justify-center">
              <button @click="$emit('start-test')"
                class="px-8 py-3 bg-primary-green text-white rounded-xl hover:bg-secondary-green transition-all font-semibold text-sm sm:text-base shadow-md hover:shadow-lg">
                {{ translate('measureModal.startTest') }}
              </button>
            </div>
          </div>
        </div>

        <!-- أسئلة الاختبار -->
        <div v-else-if="testStep === 'questions'" class="space-y-8">
          <!-- الأسئلة في الصفحة الحالية -->
          <div v-for="(question, questionIndex) in currentPageQuestions" :key="question.id || questionIndex"
            class="border border-gray-300 rounded-lg p-6 space-y-4">
            <!-- عنوان السؤال -->
            <div class="space-y-2">
              <div class="flex items-center text-xs text-gray-500">
                <span class="bg-primary-green/10 text-primary-green text-xs font-semibold px-2.5 py-0.5 rounded-full">
                  {{ getGlobalQuestionIndex(questionIndex) + 1 }}
                </span>
                <span class="mx-2">•</span>
                <span>{{ getQuestionTypeText(question.type) }}</span>
              </div>

              <h3 class="text-base sm:text-lg font-medium text-gray-900">
                {{ getTranslatedQuestion(question) }}
              </h3>

              <p v-if="getTranslatedDescription(question)" class="text-sm text-gray-600">
                {{ getTranslatedDescription(question) }}
              </p>
            </div>

            <!-- الخيارات حسب نوع السؤال -->
            <div class="space-y-3">
              <!-- Multiple Choice -->
              <div v-if="question.type === 'multiple_choice' || !question.type" class="space-y-2">
                <label v-for="(option, optionIndex) in question.options" :key="option.id || optionIndex"
                  class="flex items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer transition-all hover:border-primary-green hover:bg-primary-green/5"
                  :class="{
                    'border-primary-green bg-primary-green/5': getAnswer(getGlobalQuestionIndex(questionIndex)) === optionIndex,
                  }">
                  <input type="radio" :name="`question-${getGlobalQuestionIndex(questionIndex)}`" :value="optionIndex"
                    v-model="answers[getGlobalQuestionIndex(questionIndex)]"
                    class="w-4 h-4 text-primary-green border-gray-300 focus:ring-primary-green" />
                  <span class="text-gray-700 flex-1 text-sm"
                    :class="language === 'ar' ? 'mr-3 text-right' : 'ml-3 text-left'">
                    {{ getTranslatedOption(option) }}
                  </span>
                </label>
              </div>

              <!-- Linear Scale -->
              <div v-if="question.type === 'linear_scale'" class="space-y-4">
                <div class="flex justify-between text-sm text-gray-600">
                  <span>{{ question.scaleLabels?.low[language] || 'منخفض' }}</span>
                  <span>{{ question.scaleLabels?.high[language] || 'مرتفع' }}</span>
                </div>
                <div class="flex space-x-2 justify-center" :class="language === 'ar' ? 'space-x-reverse' : ''">
                  <label v-for="n in (question.scaleTo - question.scaleFrom + 1)" :key="n"
                    class="flex flex-col items-center p-3 bg-white rounded-lg border border-gray-200 cursor-pointer transition-all hover:border-primary-green hover:bg-primary-green/5"
                    :class="{
                      'border-primary-green bg-primary-green/5': getAnswer(getGlobalQuestionIndex(questionIndex)) === (n + question.scaleFrom - 1),
                    }">
                    <input type="radio" :name="`question-${getGlobalQuestionIndex(questionIndex)}`"
                      :value="n + question.scaleFrom - 1" v-model="answers[getGlobalQuestionIndex(questionIndex)]"
                      class="w-4 h-4 text-primary-green border-gray-300 focus:ring-primary-green" />
                    <span class="text-gray-700 text-sm mt-1">
                      {{ n + question.scaleFrom - 1 }}
                    </span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- أزرار التنقل بين الصفحات -->
          <div class="flex justify-between gap-3 pt-4 border-t border-gray-200">
            <button @click="previousPage" :disabled="currentPage === 0"
              class="px-6 py-2 bg-white text-gray-700 rounded-xl border border-gray-300 hover:bg-gray-50 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm flex items-center">
              <template v-if="language === 'ar'">
                <i class="fas fa-arrow-right ml-2"></i>
                {{ translate('measureModal.previous') }}
              </template>
              <template v-else>
                <i class="fas fa-arrow-left mr-2"></i>
                {{ translate('measureModal.previous') }}
              </template>
            </button>

            <!-- زر التالي -->
            <button v-if="currentPage < totalPages - 1" @click="nextPage" :disabled="!isCurrentPageValid"
              class="px-6 py-2 bg-primary-green text-white rounded-xl hover:bg-secondary-green transition-all disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm flex items-center">
              <template v-if="language === 'ar'">
                {{ translate('measureModal.next') }}
                <i class="fas fa-arrow-left mr-2"></i>
              </template>
              <template v-else>
                {{ translate('measureModal.next') }}
                <i class="fas fa-arrow-right ml-2"></i>
              </template>
            </button>

            <!-- زر التقديم -->
            <button v-else @click="$emit('submit-test')" :disabled="!isFormComplete"
              class="px-6 py-2 bg-primary-green text-white rounded-xl hover:bg-secondary-green transition-all disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm flex items-center gap-2 shadow-sm">
              <span>{{ translate('measureModal.submit') }}</span>
              <i class="fas fa-paper-plane text-xs"></i>
            </button>
          </div>
        </div>

        <!-- شاشة التحميل -->
        <div v-else-if="testStep === 'loading'" class="flex flex-col items-center justify-center py-16 space-y-4">
          <div class="w-10 h-10 border-4 border-primary-green border-t-transparent rounded-full animate-spin"></div>
          <p class="text-gray-600 text-sm">{{ translate('measureModal.calculating') }}</p>
        </div>

        <!-- نتائج الاختبار -->
        <div v-else-if="testStep === 'results'" class="space-y-6">
          <div class="text-center space-y-4">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-green/10 rounded-full">
              <i class="fas fa-chart-bar text-primary-green text-xl"></i>
            </div>

            <h3 class="text-xl font-semibold text-gray-900">
              {{ translate('measureModal.resultTitle') }}
            </h3>

            <div class="space-y-2">
              <div class="text-4xl font-semibold text-primary-green">{{ testResult.score }}</div>
              <p class="text-gray-500 text-sm">
                {{ translate('measureModal.yourScore') }}
                {{ testResult.maxScore }}
                {{ translate('measureModal.points') }}
              </p>
            </div>

            <div class="border border-gray-200 rounded-xl p-6 mt-4 text-left bg-gray-50/60">
              <h4 class="text-base font-semibold text-gray-900 mb-2">
                {{ getTranslatedInterpretation(testResult.interpretation, 'level') }}
              </h4>
              <p class="text-gray-600 text-sm leading-relaxed">
                {{ getTranslatedInterpretation(testResult.interpretation, 'desc') }}
              </p>
            </div>
          </div>

          <!-- التوصيات -->
          <div class="space-y-6">
            <!-- توصية الجلسة -->
            <div
              class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col md:flex-row items-center gap-6">
              <div
                class="flex-shrink-0 inline-flex items-center justify-center w-16 h-16 bg-primary-green/10 rounded-full">
                <i class="fas fa-comments text-primary-green text-xl"></i>
              </div>
              <div class="flex-1 text-center md:text-right">
                <h4 class="text-lg font-semibold text-gray-900 mb-2">
                  {{ translate('measureModal.consult.title') }}
                </h4>
                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                  {{ translate('measureModal.consult.desc') }}
                </p>
                <router-link to="/Specialists"
                  class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white bg-primary-green hover:bg-secondary-green transition-all font-semibold text-sm shadow-md">
                  <i class="fas fa-calendar-alt"></i>
                  {{ translate('measureModal.consult.button') }}
                </router-link>
              </div>
            </div>
          </div>

          <!-- أزرار الإجراءات -->
          <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <button @click="$emit('retake-test')"
              class="flex-1 px-4 py-2 bg-white text-gray-700 rounded-md border border-gray-300 hover:bg-gray-50 transition-all font-medium text-sm flex items-center justify-center">
              <i class="fas fa-redo" :class="language === 'ar' ? 'ml-2' : 'mr-2'"></i>
              {{ translate('measureModal.retake') }}
            </button>
            <button @click="$emit('show-other-measures')"
              class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all font-medium text-sm flex items-center justify-center">
              <i class="fas fa-list" :class="language === 'ar' ? 'ml-2' : 'mr-2'"></i>
              {{ translate('measureModal.otherMeasures') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { t } from '@/locales'

export default {
  name: 'MeasureModal',
  props: {
    measure: { type: Object, required: true },
    testStep: { type: String, default: 'info' },
    currentQuestionIndex: { type: Number, default: 0 },
    answers: { type: Array, default: () => [] },
    testResult: { type: Object, default: null },
    language: { type: String, default: 'ar' }
  },
  emits: [
    'close',
    'start-test',
    'next-question',
    'previous-question',
    'submit-test',
    'retake-test',
    'show-other-measures'
  ],
  data() {
    return {
      questionsPerPage: 3,
      currentPage: 0,
    }
  },
  computed: {
    totalPages() {
      return Math.ceil(this.measure.questions.length / this.questionsPerPage)
    },

    currentPageQuestions() {
      const startIndex = this.currentPage * this.questionsPerPage
      const endIndex = startIndex + this.questionsPerPage
      return this.measure.questions.slice(startIndex, endIndex)
    },

    isCurrentPageValid() {
      return this.currentPageQuestions.every((question, index) => {
        const globalIndex = this.getGlobalQuestionIndex(index)
        return this.isAnswerValid(globalIndex)
      })
    },

    isFormComplete() {
      return this.measure.questions.every((question, index) => {
        return this.isAnswerValid(index)
      })
    }
  },
  methods: {
    translate(key) {
      return t(key, this.language)
    },

    getTranslatedTitle(measure) {
      if (this.language === 'ar') {
        return measure.name_ar || measure.name_en || 'بدون عنوان';
      }
      return measure.name_en || measure.name_ar || 'No Title';
    },

    getTranslatedDescription(measure) {
      if (this.language === 'ar') {
        return measure.description_ar || measure.description_en || 'لا يوجد وصف';
      }
      return measure.description_en || measure.description_ar || 'No description';
    },

    getTranslatedQuestion(question) {
      if (this.language === 'ar') {
        return question.question_text_ar || question.question_text_en || question.text || 'بدون نص';
      }
      return question.question_text_en || question.question_text_ar || question.text || 'No text';
    },

    getTranslatedOption(option) {
      if (this.language === 'ar') {
        return option.option_text_ar || option.option_text_en || option.text || option;
      }
      return option.option_text_en || option.option_text_ar || option.text || option;
    },

    getTranslatedInterpretation(interpretation, key) {
      if (!interpretation) return '';
      const value = interpretation[key];
      return typeof value === 'object' ? value[this.language] : value;
    },

    getQuestionTypeText(type) {
      const types = {
        multiple_choice: { ar: 'اختيار من متعدد', en: 'Multiple Choice' },
        linear_scale: { ar: 'مقياس خطي', en: 'Linear Scale' },
      }
      return types[type]?.[this.language] || type || { ar: 'اختيار من متعدد', en: 'Multiple Choice' }[this.language];
    },

    getGlobalQuestionIndex(pageIndex) {
      return this.currentPage * this.questionsPerPage + pageIndex
    },

    getAnswer(questionIndex) {
      return this.answers[questionIndex]
    },

    isAnswerValid(questionIndex) {
      const answer = this.answers[questionIndex];
      return answer !== undefined && answer !== null && answer !== '';
    },

    getEstimatedTime(measure) {
      const questionsCount = measure.questions_count || measure.questions?.length || 0;
      return Math.max(5, Math.min(20, Math.ceil(questionsCount * 0.8)));
    },

    nextPage() {
      if (this.currentPage < this.totalPages - 1) {
        this.currentPage++
      }
    },

    previousPage() {
      if (this.currentPage > 0) {
        this.currentPage--
      }
    }
  }
}
</script>

<style scoped>
.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>