<template>
  <section class="py-20 bg-gradient-to-br from-primary-green/5 to-primary-pink/5">
    <div class="max-w-7xl mx-auto px-6">

      <TitleSection
        :mainText="translate('about.statistics.title')"
        :highlightText="translate('about.statistics.highlight')"
        textColor="text-gray-900"
        highlightColor="text-primary-green"
        subtitleText="translate('about.statistics.subtitle')"
        gradientClass="bg-primary-green"
      />

      <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 mt-12">
        <div 
          v-for="stat in statsList" 
          :key="stat.id"
          class="text-center bg-white rounded-2xl p-6 md:p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 animate-count-up"
          :style="`animation-delay: ${stat.id * 0.2}s`"
          @mouseenter="startCounter(stat)"
        >
          <div class="text-3xl md:text-4xl font-bold text-primary-green mb-2" :id="`counter-${stat.id}`">
            {{ stat.displayValue }}
          </div>
          <div class="text-gray-600 text-base">{{ stat.label }}</div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useTranslations } from '@/composables/useTranslations'
import { useSettings } from '@/composables/useSettings'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'

const { currentLanguage, translate } = useTranslations()
const { achievementsSettings, loading, getArray } = useSettings()

// قائمة الإحصائيات
const statsList = computed(() => {
  if (loading.value) {
    return [
      { id: 1, label: translate('about.statistics.counseling'), value: '5000+', displayValue: '0+' },
      { id: 2, label: translate('about.statistics.workshops'), value: '200+', displayValue: '0+' },
      { id: 3, label: translate('about.statistics.satisfaction'), value: '98%', displayValue: '0%' },
      { id: 4, label: translate('about.statistics.specialists'), value: '50+', displayValue: '0+' }
    ]
  }
  
  const statsData = getArray('achievements', 'stats')
  const lang = currentLanguage.value === 'ar' ? 'label_ar' : 'label_en'
  
  if (statsData.length > 0) {
    return statsData.map((stat, index) => ({
      id: index + 1,
      label: stat[lang] || stat.label_ar || stat.label_en || '',
      value: stat.value || '0',
      displayValue: stat.value || '0'
    }))
  }
  
  // Fallback
  return [
    { id: 1, label: translate('about.statistics.counseling'), value: '5000+', displayValue: '0+' },
    { id: 2, label: translate('about.statistics.workshops'), value: '200+', displayValue: '0+' },
    { id: 3, label: translate('about.statistics.satisfaction'), value: '98%', displayValue: '0%' },
    { id: 4, label: translate('about.statistics.specialists'), value: '50+', displayValue: '0+' }
  ]
})

// دوال العداد
const startCounter = (stat) => {
  const element = document.getElementById(`counter-${stat.id}`)
  if (!element) return
  
  // استخراج الرقم من القيمة
  const valueStr = stat.value.toString()
  const isPercentage = valueStr.includes('%')
  const isPlus = valueStr.includes('+')
  const target = parseInt(valueStr.replace(/[^0-9]/g, '')) || 0
  
  if (target === 0) {
    element.textContent = stat.value
    return
  }
  
  let current = 0
  const increment = target / 50
  const timer = setInterval(() => {
    current += increment
    if (current >= target) {
      element.textContent = stat.value
      clearInterval(timer)
    } else {
      let display = Math.floor(current).toString()
      if (isPercentage) display += '%'
      else if (isPlus) display += '+'
      element.textContent = display
    }
  }, 30)
}

onMounted(() => {
  setTimeout(() => {
    statsList.value.forEach(stat => {
      startCounter(stat)
    })
  }, 1000)
})
</script>

<style scoped>
.animate-count-up { animation: countUp 0.6s ease-out forwards; opacity:0; }
@keyframes countUp { from{opacity:0;transform:scale(0.8);} to{opacity:1;transform:scale(1);} }
</style>