<template>
  <header :class="[
    'fixed top-0 left-0 right-0 z-[100] w-full transition-all duration-500',
    isMobile
      ? (scrolled ? 'bg-white shadow-lg' : 'bg-transparent')  // للجوال
      : (hideHeader ? '-translate-y-full' : 'translate-y-0')  // للابتوب
  ]" :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'">
    <!-- الشعار والأزرار -->
    <div class="flex justify-between items-center px-4 sm:px-8 py-6 relative z-10">
      <div class="flex-shrink-0">
        <router-link to="/">
          <img 
            :src="siteLogo" 
            :alt="t('header.logoAlt')" 
            :class="[
              'h-10 sm:h-[60px] w-auto transition-all duration-300',
              isHomePage && !scrolled && !isMobile ? 'logo-white-filter' : isHomePage && !scrolled && isMobile ? 'logo-white-filter-mobile' : ''
            ]" 
          />
        </router-link>
      </div>

      <div class="flex flex-row items-center gap-3 md:gap-6 sm:gap-4 relative">
        <!-- زر انضم إلينا / تسجيل خروج حسب حالة تسجيل الدخول -->
        <button v-if="!isAuthenticated" @click="goToRegister"
          class="bg-primary-green text-white font-semibold h-12 w-[130px] sm:w-[180px] md:w-[200px] rounded-2xl flex items-center justify-center gap-2 hover:bg-secondary-green transition-all duration-300 shadow-md hover:shadow-lg text-base sm:text-lg">
          <img src="https://injazalarab.org/_nuxt/img/compus-arrow.7f03aae.svg" :alt="t('header.arrowAlt')"
            class="w-5 sm:w-7" />
          <span class="text-white">{{ translate('header.joinUs') }}</span>
        </button>

        <button
          v-else
          @click="handleLogout"
          class="bg-primary-green text-white h-10 w-10 sm:h-11 sm:w-11 rounded-2xl flex items-center justify-center hover:bg-secondary-green transition-all duration-300 shadow-md hover:shadow-lg text-lg"
          :title="t('header.logout')"
        >
          <i class="fas fa-right-from-bracket"></i>
        </button>

        <!-- زر اللغة في الهيدر العلوي - يظهر فقط في اللابتوب -->
        <button @click="handleLanguageToggle"
          :class="[
            'bg-primary-green text-white font-semibold h-12 w-12 rounded-2xl flex items-center justify-center gap-2 hover:bg-secondary-green transition-all duration-300 shadow-md hover:shadow-lg',
            isMobile ? 'hidden' : 'flex'
          ]"
          :title="t('header.languageToggle')">
          {{ currentLanguage === 'ar' ? 'EN' : 'AR' }}
        </button>

        <button @click="toggleMenu"
          class="w-12 h-12 bg-primary-green text-white text-2xl font-bold rounded-2xl flex items-center justify-center shadow-md hover:bg-secondary-green hover:shadow-lg transition-all duration-300"
          :aria-label="t('header.openMenu')">
          &#9776;
        </button>
      </div>
    </div>
  </header>

  <!-- القائمة المنبثقة -->
  <transition name="fade">
    <div v-if="menuVisible"
      class="fixed inset-0 bg-[#000000]/80 backdrop-blur-md z-[999] flex flex-col justify-center items-center text-white text-2xl space-y-6">

      <button @click="toggleMenu"
        class="absolute md:top-8 top-6 md:left-8 left-4 w-12 h-12 border-2 border-primary-green text-primary-green text-2xl font-bold rounded-2xl flex items-center justify-center shadow-md hover:bg-primary-green hover:text-white transition-all duration-300"
        :aria-label="t('header.closeMenu')">
        &times;
      </button>

      <div class="flex flex-col text-center space-y-0 mt-20 text-xl max-w-xs sm:max-w-md">
        <router-link v-for="item in menuItems" :key="item.path" :to="item.path" @click="toggleMenu"
          class="hover:text-primary-green hover:scale-110 transition-all duration-300 cursor-pointer py-2 block">
          {{ item.name[currentLanguage] }}
        </router-link>
        
        <!-- زر اللغة في القائمة المنبثقة - يظهر في جميع الأجهزة -->
        <button 
          @click="handleLanguageToggle"
          class="hover:text-primary-green hover:scale-110 transition-all duration-300 cursor-pointer py-2 block flex items-center justify-center gap-2"
          :title="t('header.languageToggle')">
          <i class="fas fa-globe text-lg"></i>
          <span>{{ currentLanguage === 'ar' ? 'English' : 'العربية' }}</span>
        </button>
      </div>

      <SocialLinks />
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, provide, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { translations, t } from '@/locales'
import SocialLinks from '../layouts/SocialLinks.vue'
import { useTranslations } from '@/composables/useTranslations'
import { useProfile } from '@/composables/useProfile'
import { useSettings } from '@/composables/useSettings'

const { toggleLanguage, translate } = useTranslations()
const router = useRouter()
const route = useRoute()
const { user, logout, isAuthenticated: isAuthFn } = useProfile()
const menuVisible = ref(false)
const currentLanguage = ref('ar')
const hideHeader = ref(false)
const scrolled = ref(false)
const isMobile = ref(window.innerWidth <= 768)

// ✅ جلب الإعدادات
const { identitySettings, loading } = useSettings()

// ✅ الشعار الديناميكي
const siteLogo = computed(() => {
  // إذا كان في حالة تحميل، استخدم الصورة الافتراضية
  if (loading.value) {
    return new URL('@/assets/images/logo/7.png', import.meta.url).href
  }
  
  const logo = identitySettings.value.site_logo
  if (!logo) {
    return new URL('@/assets/images/logo/7.png', import.meta.url).href
  }
  
  // إذا كان رابط كامل
  if (logo.startsWith('http')) return logo
  if (logo.startsWith('data:image')) return logo
  
  // مسار تخزين نسبي
  const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  const baseUrl = apiBaseUrl.replace('/api', '')
  const cleanUrl = logo.replace(/^\/+/, '')
  return `${baseUrl}/storage/${cleanUrl}`
})

const toggleMenu = () => (menuVisible.value = !menuVisible.value)

// حساب إذا كانت الصفحة الحالية هي الرئيسية
const isHomePage = computed(() => route.path === '/')

// تحديث menuItems لاستخدام دالة الترجمة
const menuItems = computed(() => [
  { name: { ar: translate('menuItems.Home'), en: translate('menuItems.Home') }, path: '/' },
  { name: { ar: translate('menuItems.about'), en: translate('menuItems.about') }, path: '/about' },
  { name: { ar: translate('menuItems.specialists'), en: translate('menuItems.specialists') }, path: '/Specialists' },
  { name: { ar: translate('menuItems.sessions'), en: translate('menuItems.sessions') }, path: '/session' },
  { name: { ar: translate('menuItems.events'), en: translate('menuItems.events') }, path: '/events' },
  { name: { ar: translate('menuItems.measures'), en: translate('menuItems.measures') }, path: '/measures' },
  { name: { ar: translate('menuItems.articles'), en: translate('menuItems.articles') }, path: '/article' },
  { name: { ar: translate('menuItems.legal'), en: translate('menuItems.legal') }, path: '/legal' },
  { name: { ar: translate('menuItems.library'), en: translate('menuItems.library') }, path: '/library' },
  { name: { ar: translate('menuItems.programs'), en: translate('menuItems.programs') }, path: '/program' },
  { name: { ar: translate('menuItems.contact'), en: translate('menuItems.contact') }, path: '/contact' }
])

// دالة لجلب اللغة من localStorage مع fallback
const getSavedLanguage = () => {
  const savedLanguage = localStorage.getItem('preferredLanguage')
  if (savedLanguage && (savedLanguage === 'ar' || savedLanguage === 'en')) {
    return savedLanguage
  }
  return 'ar'
}

// تحديث currentLanguage عند تغيير اللغة
watch(() => getSavedLanguage(), (newLang) => {
  currentLanguage.value = newLang
}, { immediate: true })

const handleScroll = () => {
  const currentScroll = window.scrollY

  if (isMobile.value) {
    scrolled.value = currentScroll > 50
  } else {
    hideHeader.value = currentScroll > 0
  }
}

const handleResize = () => {
  isMobile.value = window.innerWidth <= 768
}

// دالة خاصة لتبديل اللغة مع إغلاق القائمة في الجوال
const handleLanguageToggle = () => {
  toggleLanguage()
  currentLanguage.value = getSavedLanguage()
  if (isMobile.value) {
    toggleMenu()
  }
}

const isAuthenticated = computed(() => isAuthFn())

const goToRegister = () => {
  router.push('/register')
}

const handleLogout = () => {
  logout()
  router.push('/')
}

onMounted(() => {
  currentLanguage.value = getSavedLanguage()
  
  document.documentElement.dir = currentLanguage.value === 'ar' ? 'rtl' : 'ltr'
  document.documentElement.lang = currentLanguage.value

  window.addEventListener('scroll', handleScroll)
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('resize', handleResize)
})

provide('languageState', {
  currentLanguage,
  toggleLanguage,
  t: translate
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

.fade-enter-to,
.fade-leave-from {
  opacity: 1;
  transform: translateY(0);
}

.router-link-active {
  color: #9EBF3B;
  font-weight: bold;
}

header {
  overflow: visible;
}

/* فلتر أبيض للشعار في الصفحة الرئيسية للابتوب */
.logo-white-filter {
  filter: brightness(0) invert(1) !important;
  -webkit-filter: brightness(0) invert(1) !important;
}

/* فلتر أبيض للشعار في الصفحة الرئيسية للجوال */
.logo-white-filter-mobile {
  filter: brightness(0) invert(1) !important;
  -webkit-filter: brightness(0) invert(1) !important;
}

/* تعديلات للصفحة الرئيسية */
.home-page-header .logo-white-filter {
  filter: brightness(0) invert(1) !important;
}

/* إخفاء زر اللغة في الهيدر للجوال باستخدام CSS فقط كبديل */
@media (max-width: 768px) {
  .mobile-hide-language {
    display: none !important;
  }
}
</style>