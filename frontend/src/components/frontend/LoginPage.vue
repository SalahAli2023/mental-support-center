<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Minimal Header with Back Arrow Only -->
    <header class="container mx-auto px-4 pt-6">
      <button type="button" @click="router.push('/')" class="text-gray-600 hover:text-gray-900 transition-colors"
        :class="isRTL ? 'ml-auto block' : 'mr-auto block'">
        <i :class="isRTL ? 'fas fa-arrow-right-long' : 'fas fa-arrow-left-long'"></i>
      </button>
    </header>

    <!-- Main Content -->
    <main class="flex-1 container mx-auto px-4 py-16">
      <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
        <!-- Left: Login Form -->
        <div class="flex justify-center">
          <div class="bg-white rounded-2xl p-8 w-full max-w-md">
            <div :class="isRTL ? 'text-right' : 'text-left'">
              <h1 class="text-2xl font-bold text-gray-800 mb-2">
                {{ isRTL ? 'تسجيل الدخول' : 'Login' }}
              </h1>
            </div>

            <form @submit.prevent="handleLogin" class="space-y-4">
              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" :class="isRTL ? 'text-right' : 'text-left'">
                  {{ isRTL ? 'البريد الإلكتروني' : 'Email' }}
                </label>
                <input v-model="form.email" type="email" :placeholder="''"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent"
                  :class="errors.email ? 'border-red-500' : ''" required>
                <p v-if="errors.email" class="text-red-500 text-xs mt-1" :class="isRTL ? 'text-right' : 'text-left'">
                  {{ errors.email }}
                </p>
              </div>

              <!-- Password -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2" :class="isRTL ? 'text-right' : 'text-left'">
                  {{ isRTL ? 'كلمة المرور' : 'Password' }}
                </label>
                <input v-model="form.password" type="password"
                  :placeholder="isRTL ? 'أدخل كلمة المرور' : 'Enter your password'"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent"
                  :class="errors.password ? 'border-red-500' : ''" required>
                <p v-if="errors.password" class="text-red-500 text-xs mt-1" :class="isRTL ? 'text-right' : 'text-left'">
                  {{ errors.password }}
                </p>
              </div>

              <!-- Remember me / Forgot -->
              <div class="flex items-center justify-between">
                <label class="flex items-center">
                  <input v-model="form.remember" type="checkbox"
                    class="w-4 h-4 text-primary-green border-gray-300 rounded focus:ring-primary-green">
                  <span class="text-sm text-gray-700 mr-2">
                    {{ isRTL ? 'تذكرني' : 'Remember me' }}
                  </span>
                </label>

                <button type="button" class="text-sm text-primary-green hover:text-opacity-80 transition-colors">
                  {{ isRTL ? 'نسيت كلمة المرور؟' : 'Forgot password?' }}
                </button>
              </div>

              <!-- Submit -->
              <button type="submit" :disabled="isSubmitting || !form.email || !form.password"
                class="w-full py-3 bg-primary-green text-white rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                <span v-if="!isSubmitting">
                  {{ isRTL ? 'تسجيل الدخول' : 'Login' }}
                </span>
                <span v-else class="flex items-center justify-center gap-2">
                  <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  {{ isRTL ? 'جاري تسجيل الدخول...' : 'Logging in...' }}
                </span>
              </button>

              <!-- Go to register -->
              <div class="pt-4 border-t border-gray-200 flex justify-center">
                <p class="text-gray-600 text-sm text-center">
                  {{ isRTL ? 'ليس لديك حساب؟' : "Don't have an account?" }}
                  <RouterLink to="/register"
                    class="text-primary-green hover:text-opacity-80 font-medium transition-colors">
                    {{ isRTL ? 'إنشاء حساب جديد' : 'Create an account' }}
                  </RouterLink>
                </p>
              </div>
            </form>
          </div>
        </div>

        <!-- Right: Static Image -->
        <div class="hidden lg:flex justify-center">
          <img
            src="/images/t-removebg-preview.png"
            alt="تسجيل الدخول إلى منصة الدعم النفسي"
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
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { useNotifications } from '@/composables/useNotifications'
import { useProfile } from '@/composables/useProfile'
import api from '@/utils/api'
import { t } from '@/locales'
import Footer from '@/components/frontend/layouts/Footer.vue'

const router = useRouter()
const route = useRoute()
const { showSuccess, showError } = useNotifications()
const { setUserFromApi } = useProfile()

const currentLanguage = ref(localStorage.getItem('preferredLanguage') || 'ar')

const handleLanguageChange = (event) => {
  currentLanguage.value = event.detail.language
}

onMounted(() => {
  window.addEventListener('languageChanged', handleLanguageChange)
})

onUnmounted(() => {
  window.removeEventListener('languageChanged', handleLanguageChange)
})

const isRTL = computed(() => currentLanguage.value === 'ar')

const translate = (key) => {
  return t(key, currentLanguage.value)
}

const isSubmitting = ref(false)

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const errors = reactive({
  email: '',
  password: ''
})

const handleLogin = async () => {
  isSubmitting.value = true
  errors.email = ''
  errors.password = ''

  try {
    // تسجيل دخول العميل من خلال مسار الفرونتند
    const response = await api.post('/frontend/login', {
      email: form.email,
      password: form.password,
    })

    const payload = response.data

    if (!payload.success) {
      throw new Error(payload.message || translate('loginModal.errorMessage'))
    }

    const user = payload.data?.user
    const token = payload.data?.token

    if (user) {
      setUserFromApi(user)
    }

    if (token) {
      localStorage.setItem('frontend_token', token)
    }

    // إظهار رسالة نجاح مترجمة حسب لغة الواجهة
    const successMsg =
      currentLanguage.value === 'ar'
        ? 'تم تسجيل الدخول بنجاح'
        : 'Logged in successfully'
    showSuccess(successMsg)
    const redirectTarget = Array.isArray(route.query.redirect)
      ? route.query.redirect[0]
      : route.query.redirect
    router.push(redirectTarget || '/')
  } catch (error) {
    console.error('❌ خطأ في تسجيل الدخول:', error)
    const message = error.response?.data?.message || error.message || translate('loginModal.errorMessage')

    if (
      message.includes('بيانات الدخول غير صحيحة') ||
      message.includes('غير صحيحة') ||
      message.toLowerCase().includes('invalid') ||
      message === 'loginModal.invalidCredentials'
    ) {
      const invalidMsg =
        currentLanguage.value === 'ar'
          ? 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
          : 'Incorrect email or password'

      errors.email = invalidMsg
      errors.password = invalidMsg
    } else {
      showError(message)
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
