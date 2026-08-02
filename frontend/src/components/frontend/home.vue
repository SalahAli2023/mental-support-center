<template>
  <div>
    <Header />
    <Hero />
    <Feilds />
    <VisionMission />
    <HeadingComponent />
    <SuccessNumbers />
    <articles />
    <Doctors />
    <FAQSection />
    <Footer />
  </div>
</template>

<script setup>
import { provide } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

// توفير حالة اللغة للمكونات الفرعية
const { currentLanguage, toggleLanguage, translate } = useTranslations()

provide('languageState', {
  currentLanguage,
  toggleLanguage,
  translate
})

import articles from '@/components/frontend/homepage/articles.vue'
import Header from '@/components/frontend/layouts/header.vue'
import Hero from '@/components/frontend/homepage/hero.vue'
import HeadingComponent from '@/components/frontend/homepage/HeadingComponent.vue'
import Feilds from '@/components/frontend/homepage/Feilds.vue'
import Doctors from '@/components/frontend/homepage/doctors.vue'
import SuccessNumbers from '@/components/frontend/homepage/SuccessNumbers.vue'
import VisionMission from '@/components/frontend/homepage/VisionMission.vue'
import FAQSection from '@/components/frontend/homepage/FAQSection.vue'
import Footer from '@/components/frontend/layouts/footer.vue'
</script>