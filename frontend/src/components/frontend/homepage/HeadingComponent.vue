<template>
  <section
    ref="sectionRef"
    class="relative py-12 md:py-20 overflow-hidden bg-gradient-to-br from-gray-50 to-white  transition-all duration-1000"
    :class="sectionClass"
  >
    <!-- العنوان -->
    <div class="text-center mb-10 md:mb-16 px-4">
      <div class="inline-block relative">
        <TitleSection
          :mainText="translate('home.howItWorks.title')"
          :highlightText="translate('home.howItWorks.highlight')"
        />
      </div>
      <p 
        class="text-base md:text-lg text-gray-600 max-w-2xl mx-auto mt-4 transition-all duration-700 delay-400"
        :class="contentItemClass"
      >
        {{ translate('home.howItWorks.subtitle') }}
      </p>
    </div>

    <!-- Desktop Layout -->
    <div v-if="!isMobile" class="relative flex justify-between items-start max-w-7xl mx-auto px-8">
      <!-- SVG الزجزاج مع الأسهم من 1 → 2 → 3 → 4 -->
      <svg class="absolute top-0 left-0 w-full h-full pointer-events-none hidden md:block" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <marker id="arrowhead-green" markerWidth="10" markerHeight="7" refX="10" refY="3.5" orient="auto">
            <polygon points="0 0, 10 3.5, 0 7" fill="#9EBF3B" />
          </marker>
          <marker id="arrowhead-pink" markerWidth="10" markerHeight="7" refX="10" refY="3.5" orient="auto">
            <polygon points="0 0, 10 3.5, 0 7" fill="#D6A29A" />
          </marker>
        </defs>

        <!-- 1 → 2 -->
        <polyline 
          points="160,170 250,120 430,50" 
          stroke="#9EBF3B" 
          stroke-width="1" 
          stroke-opacity="0.8" 
          fill="transparent" 
          class="path-animate transition-all duration-1000 delay-200"
          :class="svgPathClass"
          marker-end="url(#arrowhead-green)" 
        />

        <!-- 2 → 3 -->
        <polyline 
          points="430,10 600,100 760,180" 
          stroke="#D6A29A" 
          stroke-width="1" 
          stroke-opacity="0.8" 
          fill="transparent" 
          class="path-animate delay transition-all duration-1000 delay-400"
          :class="svgPathClass"
          marker-end="url(#arrowhead-pink)" 
        />

        <!-- 3 → 4 -->
        <polyline 
          points="760,230 880,150 1100,50" 
          stroke="#9EBF3B" 
          stroke-width="1" 
          stroke-opacity="0.8" 
          fill="transparent" 
          class="path-animate delay2 transition-all duration-1000 delay-600"
          :class="svgPathClass"
          marker-end="url(#arrowhead-green)" 
        />
      </svg>

      <!-- Steps Desktop -->
      <div 
        class="flex flex-col items-center text-center relative z-10 transition-all duration-700 delay-200"
        :class="contentItemClass"
      >
        <div class="bg-gradient-to-br from-primary-green to-secondary-green text-white text-4xl font-semibold w-24 h-24 flex items-center justify-center rounded-2xl shadow-md transition-transform duration-500 transform hover:scale-110 hover:shadow-lg border-2 border-white">
          01
        </div>
        <div class="mt-4">
          <h3 class="text-lg font-bold mb-2 text-primary-green">{{ translate('home.howItWorks.steps.step1') }}</h3>
          <p class="text-gray-600 w-56">{{ translate('home.howItWorks.steps.step1Desc') }}</p>
        </div>
      </div>

      <div 
        class="flex flex-col items-center text-center relative z-10 transition-all duration-700 delay-300"
        :class="contentItemClass"
      >
        <div class="mb-4">
          <h3 class="text-lg font-bold mb-2 text-primary-pink">{{ translate('home.howItWorks.steps.step2') }}</h3>
          <p class="text-gray-600 w-56">{{ translate('home.howItWorks.steps.step2Desc') }}</p>
        </div>
        <div class="bg-gradient-to-br from-primary-pink to-secondary-pink text-white text-4xl font-semibold w-24 h-24 flex items-center justify-center rounded-2xl shadow-md transition-transform duration-500 transform hover:scale-110 hover:shadow-lg border-2 border-white">
          02
        </div>
      </div>

      <div 
        class="flex flex-col items-center text-center relative z-10 transition-all duration-700 delay-400"
        :class="contentItemClass"
      >
        <div class="bg-gradient-to-br from-primary-green to-secondary-green text-white text-4xl font-semibold w-24 h-24 flex items-center justify-center rounded-2xl shadow-md transition-transform duration-500 transform hover:scale-110 hover:shadow-lg border-2 border-white">
          03
        </div>
        <div class="mt-4">
          <h3 class="text-lg font-bold mb-2 text-primary-green">{{ translate('home.howItWorks.steps.step3') }}</h3>
          <p class="text-gray-600 w-56">{{ translate('home.howItWorks.steps.step3Desc') }}</p>
        </div>
      </div>

      <div 
        class="flex flex-col items-center text-center relative z-10 transition-all duration-700 delay-500"
        :class="contentItemClass"
      >
        <div class="mb-4">
          <h3 class="text-lg font-bold mb-2 text-primary-pink">{{ translate('home.howItWorks.steps.step4') }}</h3>
          <p class="text-gray-600 w-56">{{ translate('home.howItWorks.steps.step4Desc') }}</p>
        </div>
        <div class="bg-gradient-to-br from-primary-pink to-secondary-pink text-white text-4xl font-semibold w-24 h-24 flex items-center justify-center rounded-2xl shadow-md transition-transform duration-500 transform hover:scale-110 hover:shadow-lg border-2 border-white">
          04
        </div>
      </div>
    </div>

    <!-- Mobile Layout -->
    <div v-else class="max-w-md mx-auto px-4">
      <div
        v-for="(step, index) in steps"
        :key="index"
        class="flex items-center justify-between mb-6 transition-all duration-700"
        :class="mobileStepClass"
        :style="{ transitionDelay: `${index * 100 + 200}ms` }"
      >
        <div
          :class="[
            'text-white text-2xl font-semibold w-16 h-16 flex items-center justify-center rounded-xl shadow-md transition-transform duration-500 transform hover:scale-110 border-2 border-white',
            index % 2 === 0
              ? 'bg-gradient-to-br from-primary-green to-secondary-green'
              : 'bg-gradient-to-br from-primary-pink to-secondary-pink',
          ]"
        >
          {{ step.number }}
        </div>
        <div class="pr-4 pl-4 text-start flex-1">
          <h3 
            class="text-lg font-bold mb-2"
            :class="index % 2 === 0 ? 'text-primary-green' : 'text-primary-pink'"
          >
            {{ step.title }}
          </h3>
          <p class="text-gray-600 text-sm">{{ step.text }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { useScrollAnimation } from '@/assets/js/animations.js'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'

export default {
  name: "HowItWorks",
  mixins: [useScrollAnimation],
  components: { TitleSection },
  inject: ['languageState'],
  data() {
    return {
      isMobile: false,
      steps: [
        { number: "01", title: "", text: "" },
        { number: "02", title: "", text: "" },
        { number: "03", title: "", text: "" },
        { number: "04", title: "", text: "" },
      ]
    }
  },
  computed: {
    translate() {
      return this.languageState?.translate || ((key) => key)
    },
    svgPathClass() {
      return {
        'opacity-0': !this.isVisible,
        'opacity-100': this.isVisible
      }
    },
    mobileStepClass() {
      return {
        'opacity-0 translate-x-8': !this.isVisible,
        'opacity-100 translate-x-0': this.isVisible
      }
    }
  },
  watch: {
    'languageState.currentLanguage': {
      immediate: true,
      handler() {
        this.updateSteps()
      }
    }
  },
  methods: {
    updateSteps() {
      this.steps = [
        { 
          number: "01", 
          title: this.translate('home.howItWorks.steps.step1'), 
          text: this.translate('home.howItWorks.steps.step1Desc') 
        },
        { 
          number: "02", 
          title: this.translate('home.howItWorks.steps.step2'), 
          text: this.translate('home.howItWorks.steps.step2Desc') 
        },
        { 
          number: "03", 
          title: this.translate('home.howItWorks.steps.step3'), 
          text: this.translate('home.howItWorks.steps.step3Desc') 
        },
        { 
          number: "04", 
          title: this.translate('home.howItWorks.steps.step4'), 
          text: this.translate('home.howItWorks.steps.step4Desc') 
        },
      ]
    },
    checkMobile() {
      this.isMobile = window.innerWidth < 768;
    }
  },
  mounted() {
    this.updateSteps()
    this.checkMobile();
    window.addEventListener("resize", this.checkMobile);
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkMobile);
  }
}
</script>

<style scoped>
.path-animate { 
  stroke-dasharray: 8 8; 
  stroke-dashoffset: 0; 
  animation: draw 3s linear forwards; 
}
.path-animate.delay { animation-delay: 0.6s; }
.path-animate.delay2 { animation-delay: 1.2s; }

@keyframes draw { 
  from { 
    stroke-dashoffset: 16; 
  } 
  to { 
    stroke-dashoffset: 0; 
  } 
}

div.bg-gradient-to-br { 
  box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
}

/* تحسين الانتقالات */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* تحسين التجاوب */
@media (max-width: 768px) {
  .text-2xl {
    font-size: 1.75rem;
  }
  
  .text-lg {
    font-size: 1.1rem;
  }
}

/* تأثيرات hover محسنة */
.bg-gradient-to-br {
  transition: all 0.3s ease;
}
</style>