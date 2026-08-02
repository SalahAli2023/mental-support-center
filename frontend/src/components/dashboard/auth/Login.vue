<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-secondary-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- الشعار -->
      <div class="text-center">
        <div class="mx-auto h-20 w-20 bg-primary rounded-full flex items-center justify-center mb-4">
          <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-primary">
          لوحة التحكم
        </h2>
        <p class="mt-2 text-sm text-secondary">
          سجل الدخول لحسابك الإداري
        </p>
      </div>

      <!-- النموذج -->
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <!-- رسائل الخطأ -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
          <div class="flex items-center">
            <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ error }}
          </div>
        </div>

        <div class="space-y-4">
          <!-- البريد الإلكتروني -->
          <div>
            <label for="email" class="block text-sm font-medium text-primary mb-2">البريد الإلكتروني</label>
            <div class="relative">
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                :class="[
                  'w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 transition-all duration-200 bg-white pr-10',
                  errors.email ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary'
                ]"
                placeholder="admin@example.com"
                :disabled="loading"
              />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                </svg>
              </div>
            </div>
            <p v-if="errors.email" class="mt-1 text-sm text-red-600 flex items-center">
              <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              {{ errors.email }}
            </p>
          </div>

          <!-- كلمة المرور -->
          <div>
            <label for="password" class="block text-sm font-medium text-primary mb-2">كلمة المرور</label>
            <div class="relative">
              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                :class="[
                  'w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 transition-all duration-200 bg-white pr-10',
                  errors.password ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary focus:border-primary'
                ]"
                placeholder="كلمة المرور"
                :disabled="loading"
              />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
            </div>
            <p v-if="errors.password" class="mt-1 text-sm text-red-600 flex items-center">
              <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
              {{ errors.password }}
            </p>
          </div>
        </div>

        <!-- تذكرني -->
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember"
              v-model="form.remember"
              type="checkbox"
              class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
            />
            <label for="remember" class="mr-2 block text-sm text-primary">
              تذكرني
            </label>
          </div>
        </div>

        <!-- زر الدخول -->
        <div>
          <button
            type="submit"
            :disabled="loading"
            :class="[
              'w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-sm font-medium text-green focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 shadow-lg',
              loading 
                ? 'bg-gray-400 cursor-not-allowed' 
                : 'bg-primary hover:bg-primary-600 focus:ring-primary shadow-primary/25 hover:shadow-lg transform hover:-translate-y-0.5'
            ]"
          >
            <span v-if="loading" class="flex items-center">
              <div class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full mr-2"></div>
              جاري التسجيل...
            </span>
            <span v-else class="flex items-center">
              <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              تسجيل الدخول
            </span>
          </button>
        </div>

        <!-- رابط العودة -->
        <div class="text-center pt-4 border-t border-gray-200">
          <router-link 
            to="/" 
            class="text-sm text-primary hover:text-primary-600 font-medium transition-colors duration-200 flex items-center justify-center"
          >
            <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            العودة للموقع الرئيسي
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// البيانات التفاعلية
const loading = ref(false)
const error = ref('')

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const errors = reactive({
  email: '',
  password: ''
})

// دوال التحقق
const validateForm = () => {
  let isValid = true
  
  errors.email = ''
  errors.password = ''

  if (!form.email) {
    errors.email = 'البريد الإلكتروني مطلوب'
    isValid = false
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.email = 'صيغة البريد الإلكتروني غير صحيحة'
    isValid = false
  }

  if (!form.password) {
    errors.password = 'كلمة المرور مطلوبة'
    isValid = false
  }

  return isValid
}

// دالة تسجيل الدخول
const handleLogin = async () => {
  if (!validateForm()) return

  loading.value = true
  error.value = ''

  try {
    const success = await authStore.login({
      email: form.email,
      password: form.password,
      remember: form.remember
    })

    if (success) {
      router.push('/admin/dashboard')
    }
  } catch (err: any) {
    error.value = err.message || 'حدث خطأ أثناء تسجيل الدخول'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* تخصيص مظهر شريط التمرير */
input:focus {
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* تحسين مظهر زر التحميل */
button:disabled {
  transform: none !important;
  box-shadow: none !important;
}
</style>