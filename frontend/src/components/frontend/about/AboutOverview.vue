<template>
  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        <!-- ✅ الصورة (ديناميكية) -->
        <div class="order-2 lg:order-2">
          <div class="relative rounded-2xl overflow-hidden animate-float">
            <img 
              :src="overviewImageSrc" 
              alt="نظرة عامة عن المركز"
              class="w-full h-auto"
              @error="handleImageError"
            />
            <div class="absolute inset-0 bg-gradient-to-br from-primary-green/10 to-primary-pink/10 transition-opacity duration-500 hover:opacity-0"></div>
          </div>
        </div>

        <!-- المحتوى -->
        <div class="order-1 lg:order-1 text-start" :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'">

          <!-- عنوان -->
          <TitleSection
            :mainText="translate('about.overview.mainTitle')"
            :highlightText="translate('about.overview.highlightTitle')"
            textColor="text-gray-900"
            highlightColor="text-primary-green"
            gradientClass="bg-primary-green"
          />

          <div class="space-y-4 text-gray-600 leading-relaxed">
            <p v-for="(paragraph, index) in overviewParagraphs" :key="index" class="animate-fade-in-up" :style="`animation-delay: ${(index + 1) * 0.1}s`">
              {{ paragraph }}
            </p>
          </div>

          <!-- الشارات -->
          <div class="mt-8 flex flex-wrap gap-4">
            <div 
              v-for="(badge, index) in badges" 
              :key="index"
              class="flex items-center gap-2 bg-gray-50 rounded-lg px-4 py-2 hover:shadow-md transition-all duration-300 animate-bounce-in"
              :style="`animation-delay: ${(index + 3) * 0.1}s`"
            >
              <div class="w-3 h-3 rounded-full" :class="index % 2 === 0 ? 'bg-primary-green' : 'bg-primary-pink'"></div>
              <span class="text-gray-700 font-medium text-sm">{{ badge }}</span>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'
import { useSettings } from '@/composables/useSettings'
import TitleSection from '../layouts/TitleSection.vue'

const { currentLanguage, translate } = useTranslations()
const { aboutSettings, loading, getArray } = useSettings()

// فقرات النظرة العامة
const overviewParagraphs = computed(() => {
  if (loading.value) {
    return [
      translate('about.overview.description1'),
      translate('about.overview.description2'),
      translate('about.overview.description3'),
      translate('about.overview.description4')
    ]
  }
  
  const lang = currentLanguage.value === 'ar' ? '_ar' : '_en'
  const paragraphs = []
  
  for (let i = 1; i <= 4; i++) {
    const key = `overview_paragraph_${i}${lang}`
    const value = aboutSettings.value[key]
    if (value) {
      paragraphs.push(value)
    } else {
      // Fallback للترجمة
      paragraphs.push(translate(`about.overview.description${i}`))
    }
  }
  
  return paragraphs
})

// ✅ الصورة الافتراضية
const defaultImage = new URL('@/assets/images/Statistics/جلسات.png', import.meta.url).href

// ✅ صورة النظرة العامة
const overviewImageSrc = computed(() => {
  if (loading.value) return defaultImage
  
  const image = aboutSettings.value.overview_image
  if (!image) return defaultImage
  
  // إذا كانت رابط كامل
  if (image.startsWith('http')) return image
  if (image.startsWith('data:image')) return image
  
  // مسار تخزين نسبي
  const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  const baseUrl = apiBaseUrl.replace('/api', '')
  const cleanUrl = image.replace(/^\/+/, '')
  return `${baseUrl}/storage/${cleanUrl}`
})

// ✅ معالجة خطأ الصورة
const handleImageError = (event) => {
  event.target.src = defaultImage
}

// الشارات
const badges = computed(() => {
  if (loading.value) {
    return [
      translate('about.overview.badges.independent'),
      translate('about.overview.badges.nonprofit'),
      translate('about.overview.badges.licensed')
    ]
  }
  
  const badgeData = getArray('about', 'badges')
  const lang = currentLanguage.value === 'ar' ? 'label_ar' : 'label_en'
  
  if (badgeData.length > 0) {
    return badgeData.map(b => b[lang] || b.label_ar || b.label_en || '')
  }
  
  // Fallback
  return [
    translate('about.overview.badges.independent'),
    translate('about.overview.badges.nonprofit'),
    translate('about.overview.badges.licensed')
  ]
})
</script>