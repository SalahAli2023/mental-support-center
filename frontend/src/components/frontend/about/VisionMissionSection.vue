<template>
  <section class="py-20 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-6">
      
      <div class="text-center mb-16">
        <TitleSection
          :mainText="translate('visionMission.title')"
          :highlightText="translate('visionMission.highlightTitle')"
          :subtitle="translate('visionMission.subtitle')"
          textColor="text-gray-900"
          highlightColor="text-primary-green"
          gradientClass="bg-primary-green"
        />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- الرؤية -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:shadow-xl transition-all duration-300 animate-card-slide" style="animation-delay: 0.1s">
          <div class="w-16 h-16 bg-primary-green rounded-2xl flex items-center justify-center mb-6 mx-auto hover:scale-110 transition-transform duration-300">
            <i class="fas fa-eye text-white text-2xl"></i>
          </div>

          <h3 class="text-xl font-bold text-gray-900 mb-4 text-center animate-fade-in">
            {{ translate('visionMission.vision.title') }}
          </h3>
          <p class="text-gray-600 leading-relaxed text-center animate-fade-in-delay">
            {{ visionText }}
          </p>
        </div>
        
        <!-- الرسالة -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:shadow-xl transition-all duration-300 animate-card-slide" style="animation-delay: 0.2s">
          <div class="w-16 h-16 bg-primary-pink rounded-2xl flex items-center justify-center mb-6 mx-auto hover:scale-110 transition-transform duration-300">
            <i class="fas fa-bullseye text-white text-2xl"></i>
          </div>

          <h3 class="text-xl font-bold text-gray-900 mb-4 text-center animate-fade-in">
            {{ translate('visionMission.mission.title') }}
          </h3>
          <p class="text-gray-600 leading-relaxed text-center animate-fade-in-delay">
            {{ missionText }}
          </p>
        </div>
        
        <!-- القيم -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 hover:shadow-xl transition-all duration-300 animate-card-slide" style="animation-delay: 0.3s">
          <div class="w-16 h-16 bg-primary-green rounded-2xl flex items-center justify-center mb-6 mx-auto hover:scale-110 transition-transform duration-300">
            <i class="fas fa-heart text-white text-2xl"></i>
          </div>

          <h3 class="text-xl font-bold text-gray-900 mb-4 text-center animate-fade-in">
            {{ translate('visionMission.values.title') }}
          </h3>

          <div class="space-y-3 text-right" :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'">
            <div v-for="value in valuesList" :key="value" class="flex items-center gap-3 text-gray-600 animate-fade-in-list">
              <i class="fas fa-check text-primary-green"></i>
              <span>{{ value }}</span>
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
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'

const { currentLanguage, translate } = useTranslations()
const { visionSettings, loading, getArray } = useSettings()

// الرؤية
const visionText = computed(() => {
  if (loading.value) return translate('visionMission.vision.description')
  const lang = currentLanguage.value === 'ar' ? '_ar' : '_en'
  return visionSettings.value[`vision${lang}`] || translate('visionMission.vision.description')
})

// الرسالة
const missionText = computed(() => {
  if (loading.value) return translate('visionMission.mission.description')
  const lang = currentLanguage.value === 'ar' ? '_ar' : '_en'
  return visionSettings.value[`mission${lang}`] || translate('visionMission.mission.description')
})

// القيم
const valuesList = computed(() => {
  if (loading.value) {
    return [
      translate('visionMission.values.items.justice'),
      translate('visionMission.values.items.protection'),
      translate('visionMission.values.items.participation'),
      translate('visionMission.values.items.transparency')
    ]
  }
  
  const valuesData = getArray('vision', 'values')
  const lang = currentLanguage.value === 'ar' ? 'title_ar' : 'title_en'
  
  if (valuesData.length > 0) {
    return valuesData.map(v => v[lang] || v.title_ar || v.title_en || '')
  }
  
  // Fallback
  return [
    translate('visionMission.values.items.justice'),
    translate('visionMission.values.items.protection'),
    translate('visionMission.values.items.participation'),
    translate('visionMission.values.items.transparency')
  ]
})
</script>

<style scoped>
.animate-card-slide { animation: cardSlide 0.6s ease-out forwards; opacity: 0; }
.animate-fade-in { animation: fadeInUp 0.6s ease-out 0.4s forwards; opacity: 0; }
.animate-fade-in-delay { animation: fadeInUp 0.6s ease-out 0.6s forwards; opacity: 0; }
.animate-fade-in-list { animation: fadeInList 0.5s ease-out forwards; opacity: 0; }

@keyframes cardSlide { from { opacity: 0; transform: translateY(40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fadeInList { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
</style>