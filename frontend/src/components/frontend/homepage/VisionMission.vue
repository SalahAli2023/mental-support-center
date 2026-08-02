<template>
  <section ref="whySection"
    class="relative py-16  transition-all duration-700 bg-gradient-to-br from-white via-gray-50/30 to-gray-100/20"
    :class="sectionClass">
    <!-- خلفية ديكورية خفيفة -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-primary-green/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-0 w-72 h-72 bg-primary-pink/5 rounded-full blur-3xl"></div>

    <div class="max-w-6xl mx-auto px-6 text-gray-700 relative z-10">
      <TitleSection :mainText="translate('home.visionMission.title')"
        :highlightText="translate('home.visionMission.highlight')" />
      <!-- التقسيم إلى قسمين -->
      <div class="flex flex-col md:flex-row items-center md:items-start gap-8 md:gap-12">
        <!-- القسم الأيمن (المحتوى) -->
        <div class="flex-1 order-2 md:order-1 flex flex-col justify-between">
          <div class="flex flex-col gap-8 md:gap-12">
            <!-- كل عنصر محتوى -->
            <div v-for="(item, index) in contentItems" :key="index"
              class="flex flex-row items-center justify-start gap-6 transition-all duration-500"
              :class="contentItemClass">
              <div class="flex items-center justify-center">
                <img :src="item.img" :alt="item.title" class="w-20 h-20 md:w-24 md:h-24 transition-all duration-500" />
              </div>
              <div class="flex-1">
                <h3 class="text-xl font-semibold text-primary-green mb-3">{{ item.title }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ item.desc }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- القسم الأيسر (الصورة) -->
        <div
          class="flex-1 order-1 md:order-2 flex justify-center md:justify-end items-start transition-all duration-500"
          :class="imageClass">
          <div class="relative">
            <img src="@/assets/images/Platform/تنزيل.jpg" :alt="translate('home.visionMission.imageAlt')"
              class="w-60 h-60 md:w-[400px] md:h-[500px] object-contain relative z-10" />
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script>
import { useScrollAnimation } from '@/assets/js/animations.js'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'

export default {
  name: "WhyChooseUs",
  components: { TitleSection },
  mixins: [useScrollAnimation],
  inject: ['languageState'],
  data() {
    return {
      contentItems: [
        {
          title: '',
          img: new URL('@/assets/images/Platform/معالجون.svg', import.meta.url).href,
          desc: ''
        },
        {
          title: '',
          img: new URL('@/assets/images/Platform/سرية تامة.svg', import.meta.url).href,
          desc: ''
        },
        {
          title: '',
          img: new URL('@/assets/images/Platform/سهولة الاستخدام.svg', import.meta.url).href,
          desc: ''
        },
        {
          title: '',
          img: new URL('@/assets/images/Platform/مرونة في المواعيد.svg', import.meta.url).href,
          desc: ''
        },
      ]
    }
  },
  computed: {
    translate() {
      return this.languageState?.translate || ((key) => key)
    }
  },
  watch: {
    'languageState.currentLanguage': {
      immediate: true,
      handler() {
        this.updateContentItems()
      }
    }
  },
  methods: {
    updateContentItems() {
      this.contentItems = [
        {
          title: this.translate('home.visionMission.items.therapists'),
          img: new URL('@/assets/images/Platform/معالجون.svg', import.meta.url).href,
          desc: this.translate('home.visionMission.items.therapistsDesc')
        },
        {
          title: this.translate('home.visionMission.items.confidentiality'),
          img: new URL('@/assets/images/Platform/سرية تامة.svg', import.meta.url).href,
          desc: this.translate('home.visionMission.items.confidentialityDesc')
        },
        {
          title: this.translate('home.visionMission.items.easeOfUse'),
          img: new URL('@/assets/images/Platform/سهولة الاستخدام.svg', import.meta.url).href,
          desc: this.translate('home.visionMission.items.easeOfUseDesc')
        },
        {
          title: this.translate('home.visionMission.items.flexibility'),
          img: new URL('@/assets/images/Platform/مرونة في المواعيد.svg', import.meta.url).href,
          desc: this.translate('home.visionMission.items.flexibilityDesc')
        },
      ]
    }
  },
  mounted() {
    this.updateContentItems()
  }
}
</script>

<style scoped>
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

@media (max-width: 768px) {
  .text-3xl {
    font-size: 1.75rem;
  }

  .text-xl {
    font-size: 1.1rem;
  }

  .gap-8 {
    gap: 1.5rem;
  }

  .w-60 {
    width: 15rem;
  }

  .h-60 {
    height: 15rem;
  }
}

section,
h2,
div {
  will-change: transform, opacity;
}
</style>
