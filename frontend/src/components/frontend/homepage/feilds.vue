<template>
  <section ref="sectionRef" class="relative py-16 bg-white  overflow-hidden">
    <!-- أشكال زخرفية -->
    <div
      class="absolute top-10 left-10 w-32 h-32 bg-primary-pink opacity-10 rounded-full blur-2xl transition-all duration-1000"
      :class="{ 'opacity-10 scale-100': isVisible, 'opacity-0 scale-50': !isVisible }"></div>
    <div
      class="absolute bottom-10 right-10 w-40 h-40 bg-primary-green opacity-10 rounded-full blur-2xl transition-all duration-1000"
      :class="{ 'opacity-10 scale-100': isVisible, 'opacity-0 scale-50': !isVisible }"></div>

    <div class="relative z-10 max-w-6xl mx-auto px-6">
      <div class="text-center mb-12">
        <TitleSection :mainText="translate('home.fields.title')" :highlightText="translate('home.fields.highlight')" />
        <p class="text-gray-600 max-w-xl mx-auto transition-all duration-700"
          :class="{ 'opacity-100 translate-y-0': isVisible, 'opacity-0 translate-y-6': !isVisible }">
          {{ translate('home.fields.subtitle') }}
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="(specialty, index) in specialties" :key="index"
          class="group relative bg-white rounded-xl p-6 shadow-lg border border-gray-100 overflow-hidden transition-all duration-500"
          :class="{
            'opacity-0 translate-y-8': !isVisible,
            'opacity-100 translate-y-0': isVisible
          }" :style="{ transitionDelay: `${index * 150 + 300}ms` }">
          <div
            class="absolute inset-0 bg-gradient-to-l from-primary-green to-primary-pink opacity-0 group-hover:opacity-5 transform translate-x-full group-hover:translate-x-0 transition-all duration-700 ease-out">
          </div>
          <div
            class="relative z-10 w-12 h-12 bg-primary-green rounded-lg flex items-center justify-center mb-4 group-hover:bg-primary-pink transition-all duration-500">
            <i :class="specialty.icon" class="text-white group-hover:scale-110 transition-transform duration-300"></i>
          </div>
          <div class="relative z-10">
            <h3
              class="text-lg font-bold text-gray-900 mb-3 group-hover:text-primary-green transition-colors duration-500">
              {{ specialty.title }}
            </h3>
            <p class="text-gray-600 text-sm leading-relaxed group-hover:text-gray-700 transition-colors duration-500">
              {{ specialty.description }}
            </p>
          </div>
          <div
            class="absolute bottom-0 right-0 w-0 h-1 bg-gradient-to-l from-primary-green to-primary-pink group-hover:w-full transition-all duration-700 ease-out">
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, inject, watch, computed } from 'vue'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'

const { translate, currentLanguage } = inject('languageState')
const sectionRef = ref(null)
const isVisible = ref(false)

onMounted(() => {
  const observer = new IntersectionObserver(([entry]) => {
    if (entry.isIntersecting) {
      isVisible.value = true
      observer.unobserve(entry.target)
    }
  }, { threshold: 0.3 })

  if (sectionRef.value) observer.observe(sectionRef.value)
})

// البيانات الأساسية للمجالات
const specialtiesData = {
  anxiety: {
    icon: 'fas fa-sad-tear',
    ar: { title: 'القلق والاكتئاب', description: 'علاج اضطرابات القلق والاكتئاب بمختلف درجاتها باستخدام أحدث الأساليب العلاجية.' },
    en: { title: 'Anxiety & Depression', description: 'Treatment of anxiety and depression disorders at various levels using the latest therapeutic methods.' }
  },
  family: {
    // استخدام أيقونة مناسبة للعائلة (fa-users متوفرة في Font Awesome)
    icon: 'fas fa-users',
    ar: { title: 'الاستشارات الأسرية', description: 'تحسين العلاقات الأسرية وحل النزاعات وتعزيز التواصل الفعال بين أفراد الأسرة.' },
    en: { title: 'Family Counseling', description: 'Improving family relationships, resolving conflicts, and enhancing effective communication among family members.' }
  },
  children: {
    icon: 'fas fa-user-graduate',
    ar: { title: 'صحة الأطفال والمراهقين', description: 'دعم الصحة النفسية للأطفال والمراهقين وعلاج المشكلات السلوكية والنفسية.' },
    en: { title: 'Children & Adolescents Health', description: 'Supporting the mental health of children and adolescents and treating behavioral and psychological problems.' }
  },
  career: {
    icon: 'fas fa-briefcase',
    ar: { title: 'الإرشاد المهني', description: 'مساعدة الأفراد في التغلب على ضغوط العمل وتحقيق التوازن بين الحياة الشخصية والمهنية.' },
    en: { title: 'Career Guidance', description: 'Helping individuals overcome work pressures and achieve balance between personal and professional life.' }
  },
  trauma: {
    icon: 'fas fa-heartbeat',
    ar: { title: 'الصدمات النفسية', description: 'علاج آثار الصدمات النفسية واضطراب ما بعد الصدمة باستخدام أساليب علاجية متخصصة.' },
    en: { title: 'Psychological Trauma', description: 'Treating the effects of psychological trauma and post-traumatic stress disorder using specialized therapeutic methods.' }
  },
  wellness: {
    icon: 'fas fa-brain',
    ar: { title: 'تعزيز الصحة النفسية', description: 'برامج وقائية وتنموية لتعزيز الصحة النفسية والمرونة النفسية لدى الأفراد.' },
    en: { title: 'Mental Health Enhancement', description: 'Preventive and developmental programs to enhance mental health and psychological resilience in individuals.' }
  }
}

// computed property لتحديث specialties بناءً على اللغة الحالية
const specialties = computed(() => {
  return Object.values(specialtiesData).map(item => ({
    icon: item.icon,
    title: item[currentLanguage.value]?.title || item.ar.title,
    description: item[currentLanguage.value]?.description || item.ar.description
  }))
})
</script>