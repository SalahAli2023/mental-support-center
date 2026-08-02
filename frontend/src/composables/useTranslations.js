// src/composables/useTranslations.js
import { ref, provide } from 'vue'
import { translations, t } from '@/locales'

const currentLanguage = ref('ar')

// تحميل اللغة المفضلة مباشرة عند تحميل الملف
const initLanguage = () => {
  const savedLanguage = localStorage.getItem('preferredLanguage')
  if (savedLanguage && (savedLanguage === 'ar' || savedLanguage === 'en')) {
    currentLanguage.value = savedLanguage
  }
  document.documentElement.dir = currentLanguage.value === 'ar' ? 'rtl' : 'ltr'
  document.documentElement.lang = currentLanguage.value
}
initLanguage() // استدعاء مباشر

// دالة التبديل بين اللغات
const toggleLanguage = () => {
  currentLanguage.value = currentLanguage.value === 'ar' ? 'en' : 'ar'
  localStorage.setItem('preferredLanguage', currentLanguage.value)
  document.documentElement.dir = currentLanguage.value === 'ar' ? 'rtl' : 'ltr'
  document.documentElement.lang = currentLanguage.value
}

const translate = (key) => t(key, currentLanguage.value)

export function useTranslations() {
  provide('languageState', {
    currentLanguage,
    toggleLanguage,
    translate
  })

  return {
    currentLanguage,
    toggleLanguage,
    translate
  }
}
