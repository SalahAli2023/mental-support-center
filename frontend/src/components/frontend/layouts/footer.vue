<template>
  <!-- Footer احترافي -->
  <footer class="bg-gradient-to-br from-slate-900 to-gray-900 text-white relative overflow-hidden">
    <!-- أشكال زخرفية -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-brand-500 opacity-10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-accent-500 opacity-10 rounded-full translate-x-1/3 translate-y-1/3"></div>

    <div class="relative z-10">
      <!-- المحتوى الرئيسي -->
      <div class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
          <!-- الشعار والمعلومات -->
          <div class="lg:col-span-2">
            <div class="flex items-center gap-4 mb-6">
              <!-- الشعار في الفوتر -->
              <router-link to="/">
                <img 
                  :src="footerLogo" 
                  :alt="$t('header.logoAlt')"
                  class="h-16 sm:h-[80px] w-auto" 
                />
              </router-link>
            </div>
            <p class="text-gray-300 mb-6 leading-relaxed text-lg">
              {{ footerDescription }}
            </p>
            <div class="flex gap-3">
              <a 
                v-if="socialLinks.facebook" 
                :href="socialLinks.facebook" 
                target="_blank"
                class="group w-12 h-12 bg-white/5 hover:bg-brand-500 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg border border-white/10"
              >
                <i class="fab fa-facebook-f text-gray-400 group-hover:text-white"></i>
              </a>
              <a 
                v-if="socialLinks.twitter" 
                :href="socialLinks.twitter" 
                target="_blank"
                class="group w-12 h-12 bg-white/5 hover:bg-brand-500 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg border border-white/10"
              >
                <i class="fab fa-twitter text-gray-400 group-hover:text-white"></i>
              </a>
              <a 
                v-if="socialLinks.instagram" 
                :href="socialLinks.instagram" 
                target="_blank"
                class="group w-12 h-12 bg-white/5 hover:bg-accent-500 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg border border-white/10"
              >
                <i class="fab fa-instagram text-gray-400 group-hover:text-white"></i>
              </a>
              <a 
                v-if="socialLinks.youtube" 
                :href="socialLinks.youtube" 
                target="_blank"
                class="group w-12 h-12 bg-white/5 hover:bg-accent-500 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg border border-white/10"
              >
                <i class="fab fa-youtube text-gray-400 group-hover:text-white"></i>
              </a>

              <a 
              v-if="socialLinks.linkedin" 
              :href="socialLinks.linkedin" 
              target="_blank"
              class="group w-12 h-12 bg-white/5 hover:bg-blue-600 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg border border-white/10"
              >
                <i class="fab fa-linkedin-in text-gray-400 group-hover:text-white"></i>
              </a>
            </div>
          </div>

          <!-- روابط سريعة -->
          <div>
            <h4 class="text-lg font-bold text-white mb-6 pb-3 border-b-2 border-brand-500 inline-block">
              {{ $t('footer.linksText') }}
            </h4>
            <ul class="space-y-3">
              <li>
                <router-link to="/" class="text-gray-300 hover:text-brand-500 transition-all duration-300 flex items-center gap-3 group">
                  <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"
                    class="text-brand-500 text-xs group-hover:translate-x-1 transition-transform"></i>
                  {{ $t('menuItems.Home') }}
                </router-link>
              </li>
              <li>
                <router-link to="/about" class="text-gray-300 hover:text-brand-500 transition-all duration-300 flex items-center gap-3 group">
                  <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"
                    class="text-brand-500 text-xs group-hover:translate-x-1 transition-transform"></i>
                  {{ $t('menuItems.about') }}
                </router-link>
              </li>
              <li>
                <router-link to="/Specialists" class="text-gray-300 hover:text-brand-500 transition-all duration-300 flex items-center gap-3 group">
                  <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"
                    class="text-brand-500 text-xs group-hover:translate-x-1 transition-transform"></i>
                  {{ $t('menuItems.specialists') }}
                </router-link>
              </li>
              <li>
                <router-link to="/article" class="text-gray-300 hover:text-brand-500 transition-all duration-300 flex items-center gap-3 group">
                  <i :class="currentLanguage === 'ar' ? 'fas fa-chevron-left' : 'fas fa-chevron-right'"
                    class="text-brand-500 text-xs group-hover:translate-x-1 transition-transform"></i>
                  {{ $t('menuItems.articles') }}
                </router-link>
              </li>
            </ul>
          </div>

          <!-- الخدمات -->
          <div>
            <h4 class="text-lg font-bold text-white mb-6 pb-3 border-b-2 border-accent-500 inline-block">
              {{ $t('footer.servicesText') }}
            </h4>
            <ul class="space-y-3">
              <li>
                <router-link to="/session" class="text-gray-300 hover:text-accent-500 transition-all duration-300 flex items-center gap-3 group">
                  <i class="fas fa-comments text-accent-500 text-sm"></i>
                  {{ $t('footer.supportSessions') }}
                </router-link>
              </li>
              <li>
                <router-link to="/Specialists" class="text-gray-300 hover:text-accent-500 transition-all duration-300 flex items-center gap-3 group">
                  <i class="fas fa-user-md text-accent-500 text-sm"></i>
                  {{ $t('footer.consultations') }}
                </router-link>
              </li>
              <li>
                <router-link to="/events" class="text-gray-300 hover:text-accent-500 transition-all duration-300 flex items-center gap-3 group">
                  <i class="fas fa-users text-accent-500 text-sm"></i>
                  {{ $t('footer.trainingWorkshops') }}
                </router-link>
              </li>
              <li>
                <router-link to="/legal" class="text-gray-300 hover:text-accent-500 transition-all duration-300 flex items-center gap-3 group">
                  <i class="fas fa-book text-accent-500 text-sm"></i>
                  {{ $t('footer.resources') }}
                </router-link>
              </li>
            </ul>
          </div>

          <!-- اتصل بنا -->
          <div>
            <h4 class="text-lg font-bold text-white mb-6 pb-3 border-b-2 border-brand-500 inline-block">
              {{ $t('footer.contactUsText') }}
            </h4>
            <div class="space-y-4">
              <a v-if="contactInfo.phone" :href="`tel:${contactInfo.phone}`" class="flex items-center gap-3 text-gray-300 hover:text-brand-500 transition-colors group">
                <div class="w-10 h-10 bg-brand-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                  <i class="fas fa-phone text-white text-sm"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-400  group-hover:text-brand-500 transition-colors">{{ $t('footer.phoneText') }}</p>
                  <p class="text-left font-medium">{{ contactInfo.phone }}</p>
                </div>
              </a>
              <a v-if="contactInfo.email" :href="`mailto:${contactInfo.email}`" class="flex items-center gap-3 text-gray-300 hover:text-accent-500 transition-colors group">
                <div class="w-10 h-10 bg-accent-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                  <i class="fas fa-envelope text-white text-sm"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-400 group-hover:text-accent-500 transition-colors">{{ $t('footer.emailText') }}</p>
                  <p class="font-medium">{{ contactInfo.email }}</p>
                </div>
              </a>
              <a v-if="contactInfo.address" href="https://maps.google.com" target="_blank" class="flex items-center gap-3 text-gray-300 hover:text-brand-500 transition-colors group">
                <div class="w-10 h-10 bg-brand-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                  <i class="fas fa-map-marker-alt text-white text-sm"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-400 group-hover:text-brand-500 transition-colors">{{ $t('footer.addressText') }}</p>
                  <p class="font-medium">{{ contactInfo.address }}</p>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- القسم السفلي -->
      <div class="border-t border-gray-700/50 bg-black/20">
        <div class="max-w-7xl mx-auto px-6 py-8">
          <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-center md:text-right">
              <p class="text-gray-400">
                &copy; 2025 {{ footerCopyright }}
              </p>
            </div>
            <div class="flex flex-wrap justify-center gap-6 text-sm">
              <router-link to="/terms" class="text-gray-400 hover:text-brand-500 transition-colors">{{ $t('footer.termsText') }}</router-link>
              <router-link to="/privacy" class="text-gray-400 hover:text-brand-500 transition-colors">{{ $t('footer.privacyText') }}</router-link>
              <router-link to="/faq" class="text-gray-400 hover:text-accent-500 transition-colors">{{ $t('footer.faqText') }}</router-link>
              <router-link to="/contact" class="text-gray-400 hover:text-brand-500 transition-colors">{{ $t('footer.contactUsText') }}</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { inject, computed } from 'vue'
import { useSettings } from '@/composables/useSettings'

// الحصول على حالة اللغة من provide
const languageState = inject('languageState')

// استخدام دالة الترجمة واللغة الحالية
const $t = languageState?.translate || ((key) => key)
const currentLanguage = languageState?.currentLanguage || 'ar'

// ✅ جلب الإعدادات
const { identitySettings, contactSettings, loading } = useSettings()

// ✅ الفافيكون (أيقونة صغيرة)
// const faviconUrl = computed(() => {
//   if (loading.value) {
//     // return '/favicon.ico'
//     return new URL('@/assets/images/logo/4.png', import.meta.url).href

//   }
  
//   const favicon = identitySettings.value.site_favicon
//   if (!favicon) return '/favicon.ico'
  
//   if (favicon.startsWith('http')) return favicon
//   if (favicon.startsWith('data:image')) return favicon
  
//   const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
//   const baseUrl = apiBaseUrl.replace('/api', '')
//   const cleanUrl = favicon.replace(/^\/+/, '')
//   return `${baseUrl}/storage/${cleanUrl}`
// })

// // ✅ الشعار
// const siteLogo = computed(() => {
//   if (loading.value) {
//     return new URL('@/assets/images/logo/4.png', import.meta.url).href
//   }
  
//   const logo = identitySettings.value.site_logo
//   if (!logo) {
//     return new URL('@/assets/images/logo/4.png', import.meta.url).href
//   }
  
//   if (logo.startsWith('http')) return logo
//   if (logo.startsWith('data:image')) return logo
  
//   const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
//   const baseUrl = apiBaseUrl.replace('/api', '')
//   const cleanUrl = logo.replace(/^\/+/, '')
//   return `${baseUrl}/storage/${cleanUrl}`
// })

// ✅ وصف الفوتر
const footerDescription = computed(() => {
  if (loading.value) return $t('footer.companyDescription')
  const key = currentLanguage.value === 'ar' ? 'footer_description_ar' : 'footer_description_en'
  return contactSettings.value[key] || $t('footer.companyDescription')
})

// ✅ حقوق النشر
const footerCopyright = computed(() => {
  if (loading.value) return $t('footer.allRightsText')
  const key = currentLanguage.value === 'ar' ? 'footer_copyright_ar' : 'footer_copyright_en'
  return contactSettings.value[key] || $t('footer.allRightsText')
})

// ✅ معلومات التواصل
const contactInfo = computed(() => ({
  phone: contactSettings.value.phone || '+967 737 950 689',
  email: contactSettings.value.email || 'info@nafsy.com',
  address: currentLanguage.value === 'ar' 
    ? contactSettings.value.address_ar || 'تعز، اليمن '
    : contactSettings.value.address_en || 'Taizz, Yemen '
}))

// ✅ شعار الفوتر (منفصل عن الشعار الرئيسي)
const footerLogo = computed(() => {
  if (loading.value) {
    return new URL('@/assets/images/logo/4.png', import.meta.url).href
  }
  
  const logo = contactSettings.value.footer_logo
  if (!logo) {
    return new URL('@/assets/images/logo/4.png', import.meta.url).href
  }
  
  if (logo.startsWith('http')) return logo
  if (logo.startsWith('data:image')) return logo
  
  const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  const baseUrl = apiBaseUrl.replace('/api', '')
  const cleanUrl = logo.replace(/^\/+/, '')
  return `${baseUrl}/storage/${cleanUrl}`
})

// ✅ روابط التواصل الاجتماعي
const socialLinks = computed(() => ({
  facebook: contactSettings.value.facebook || null,
  twitter: contactSettings.value.twitter || null,
  instagram: contactSettings.value.instagram || null,
  youtube: contactSettings.value.youtube || null,
  linkedin: contactSettings.value.linkedin || null,  // ✅ إضافة

}))
</script>