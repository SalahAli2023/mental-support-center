<template>
  <Hero 
    :title="heroTitle"
    :highlight="heroHighlight"
    :subtitle="heroSubtitle"
    :buttons="[
      { text: translate('buttons.startJourney'), icon: 'fas fa-play-circle', primary: true },
      { text: translate('buttons.learnMore'), icon: 'fas fa-info-circle', primary: false }
    ]"
  />
</template>

<script setup>
import { computed } from 'vue'
import Hero from '@/components/frontend/layouts/hero.vue'
import { useTranslations } from '@/composables/useTranslations'
import { useSettings } from '@/composables/useSettings'

const { translate, currentLanguage } = useTranslations()
const { aboutSettings, loading } = useSettings()

// جلب البيانات من الإعدادات
const heroTitle = computed(() => {
  if (loading.value) return translate('about.hero.title')
  const lang = currentLanguage.value === 'ar' ? '_ar' : '_en'
  return aboutSettings.value[`hero_title${lang} `] || translate('about.hero.title')
})

const heroHighlight = computed(() => {
  if (loading.value) return translate('about.hero.highlight')
  const lang = currentLanguage.value === 'ar' ? '_ar' : '_en'
  return aboutSettings.value[` hero_highlight${lang}`] || translate('about.hero.highlight')
})

const heroSubtitle = computed(() => {
  if (loading.value) return translate('about.hero.subtitle')
  const lang = currentLanguage.value === 'ar' ? '_ar' : '_en'
  return aboutSettings.value[`hero_subtitle${lang}`] || translate('about.hero.subtitle')
})
</script>