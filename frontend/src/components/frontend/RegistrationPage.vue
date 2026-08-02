<template>
  <div class="bg-gray-50">
    <!-- Minimal Header with Back Arrow Only -->
    <header class="container mx-auto px-4 pt-6">
      <button type="button" @click="router.push('/')" class="text-gray-600 hover:text-gray-900 transition-colors"
        :class="isRTL ? 'ml-auto block' : 'mr-auto block'">
        <i :class="isRTL ? 'fas fa-arrow-right-long' : 'fas fa-arrow-left-long'"></i>
      </button>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
      <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        <!-- Left Side - Registration Form -->
        <div class="flex justify-center lg:mt-6">
          <div class="bg-white rounded-2xl p-8 w-full max-w-md">
            <RegistrationForm :is-page="true" :language="currentLanguage"
              @registration-success="handleRegistrationSuccess" />
          </div>
        </div>

        <!-- Right Side - Static Image -->
        <div class="hidden lg:flex justify-center">
          <img
            src="/images/t-removebg-preview.png"
            alt="انضم إلى منصة الدعم النفسي"
            class="max-h-[520px] w-auto object-contain"
          />
        </div>
      </div>
    </main>

    <!-- Footer -->
    <Footer :language="currentLanguage" />
  </div>
</template>
<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { t } from '@/locales'
import Footer from '@/components/frontend/layouts/footer.vue' 
import RegistrationForm from '@/components/frontend/auth/RegistrationForm.vue'

const router = useRouter()

// استخدم ref تفاعلي للغة
const currentLanguage = ref(localStorage.getItem('preferredLanguage') || 'ar')

// راقب تغييرات اللغة من الـ Header
const handleLanguageChange = (event) => {
  currentLanguage.value = event.detail.language
}

onMounted(() => {
  window.addEventListener('languageChanged', handleLanguageChange)
})

onUnmounted(() => {
  window.removeEventListener('languageChanged', handleLanguageChange)
})

// Check if current language is RTL
const isRTL = computed(() => {
  return currentLanguage.value === 'ar'
})

// Translation functions
const translate = (key) => {
  return t(key, currentLanguage.value)
}

const getTranslatedTitle = (key) => {
  const translation = t(key, currentLanguage.value)
  return typeof translation === 'object' ? translation[currentLanguage.value] : translation
}

const getTranslatedDescription = (key) => {
  const translation = t(key, currentLanguage.value)
  return typeof translation === 'object' ? translation[currentLanguage.value] : translation
}

const handleRegistrationSuccess = () => {
  router.push('/')
}
</script>