<template>
  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">

      <TitleSection
        :mainText="translate('about.objectives.mainTitle')"
        :highlightText="translate('about.objectives.highlightTitle')"
        textColor="text-gray-900"
        highlightColor="text-primary-green"
        subtitleText="translate('about.objectives.subtitle')"
        gradientClass="bg-primary-green"
      />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
        <div 
          v-for="(objective, index) in objectivesList" 
          :key="index"
          class="flex items-start gap-4 bg-white rounded-2xl p-6 border border-gray-100 transition-all duration-300 hover:-translate-y-1 animate-slide-up"
          :style="`animation-delay: ${index * 0.1}s`"
        >
          <div class="w-12 h-12 bg-primary-pink rounded-xl flex items-center justify-center flex-shrink-0 mt-1 transition-all duration-300 group-hover:scale-110">
            <span class="text-white font-bold text-base">{{ index + 1 }}</span>
          </div>
          <p class="text-gray-700 text-base leading-relaxed flex-1">
            {{ objective }}
          </p>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'
import { useSettings } from '@/composables/useSettings'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'

const { currentLanguage, translate } = useTranslations()
const { aboutSettings, loading, getArray } = useSettings()

// الأهداف
const objectivesList = computed(() => {
  if (loading.value) {
    return [
      translate('about.objectives.item1'),
      translate('about.objectives.item2'),
      translate('about.objectives.item3'),
      translate('about.objectives.item4'),
      translate('about.objectives.item5'),
      translate('about.objectives.item6'),
      translate('about.objectives.item7'),
      translate('about.objectives.item8')
    ]
  }
  
  const objectivesData = getArray('about', 'objectives')
  const lang = currentLanguage.value === 'ar' ? 'text_ar' : 'text_en'
  
  if (objectivesData.length > 0) {
    return objectivesData.map(obj => obj[lang] || obj.text_ar || obj.text_en || '')
  }
  
  // Fallback
  return [
    translate('about.objectives.item1'),
    translate('about.objectives.item2'),
    translate('about.objectives.item3'),
    translate('about.objectives.item4'),
    translate('about.objectives.item5'),
    translate('about.objectives.item6'),
    translate('about.objectives.item7'),
    translate('about.objectives.item8')
  ]
})
</script>

<style scoped>
@keyframes slideUp { from{opacity:0;transform:translateY(30px);} to{opacity:1;transform:translateY(0);} }
.animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity:0; }
</style>