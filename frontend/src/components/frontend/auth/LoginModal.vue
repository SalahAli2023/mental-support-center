<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 modal-overlay" @click="closeModal"></div>

    <div class="relative bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-xl animate-slide-up"
      @click.stop>
      <!-- Header -->
      <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-start z-10">
        <div class="flex-1" :class="isRTL ? 'text-right' : 'text-left'">
          <h2 class="text-2xl font-bold text-gray-800">
            {{ isRTL ? 'تسجيل الدخول' : 'Login' }}
          </h2>
        </div>
        <button @click="closeModal"
          class="flex-shrink-0 text-gray-500 hover:text-gray-700 transition-colors p-2 rounded-lg hover:bg-gray-100">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <div class="p-6">
        <form @submit.prevent="handleLogin" class="space-y-4">
          <!-- البريد الإلكتروني -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ isRTL ? 'البريد الإلكتروني' : 'Email' }}
            </label>
            <input v-model="form.email" type="email" :placeholder="translate('loginModal.emailPlaceholder')"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent"
              :class="errors.email ? 'border-red-500' : ''" required>
            <p v-if="errors.email" class="text-red-500 text-xs mt-1 text-right">{{ errors.email }}</p>
          </div>

          <!-- كلمة المرور -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2" :class="isRTL ? 'text-right' : 'text-left'">
              {{ isRTL ? 'كلمة المرور' : 'Password' }}
            </label>
            <input v-model="form.password" type="password"
              :placeholder="isRTL ? 'أدخل كلمة المرور' : 'Enter your password'"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent"
              :class="errors.password ? 'border-red-500' : ''" required>
            <p v-if="errors.password" class="text-red-500 text-xs mt-1 text-right">{{ errors.password }}</p>
          </div>

          <!-- تذكرني -->
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

          <!-- زر تسجيل الدخول -->
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

          <!-- رابط إنشاء حساب -->
          <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-gray-600 text-sm">
              {{ isRTL ? 'ليس لديك حساب؟' : "Don't have an account?" }}
              <button type="button" @click="switchToRegister"
                class="text-primary-green hover:text-opacity-80 font-medium transition-colors">
                {{ isRTL ? 'إنشاء حساب جديد' : 'Create an account' }}
              </button>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useNotifications } from '@/composables/useNotifications'
import { useProfile } from '@/composables/useProfile'
import api from '@/utils/api'
import { t } from '@/locales'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  language: {
    type: String,
    default: 'ar'
  }
})

const emit = defineEmits(['close', 'switch-to-register', 'login-success'])

const { showSuccess, showError } = useNotifications()
const { setUserFromApi } = useProfile()

const isSubmitting = ref(false)
const form = reactive({
  email: '',
  password: '',
  remember: false
})

const errors = reactive({})

// تحديد اتجاه اللغة
const isRTL = computed(() => props.language === 'ar')

// دالة الترجمة (تُستخدم للأزرار والرسائل فقط)
const translate = (key) => {
  return t(key, props.language)
}

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

    // إظهار رسالة نجاح مترجمة حسب لغة المودال
    const successMsg =
      props.language === 'ar'
        ? 'تم تسجيل الدخول بنجاح'
        : 'Logged in successfully'
    showSuccess(successMsg)
    emit('login-success')
    closeModal()

  } catch (error) {
    console.error('❌ خطأ في تسجيل الدخول:', error)
    const message = error.response?.data?.message || error.message || translate('loginModal.errorMessage')

    if (
      message.includes('بيانات الدخول غير صحيحة') ||
      message.toLowerCase().includes('invalid') ||
      message === 'loginModal.invalidCredentials'
    ) {
      const invalidMsg =
        props.language === 'ar'
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

const switchToRegister = () => {
  closeModal()
  emit('switch-to-register')
}

const closeModal = () => {
  emit('close')
  // إعادة تعيين النموذج
  Object.assign(form, {
    email: '',
    password: '',
    remember: false
  })
  Object.keys(errors).forEach(key => delete errors[key])
}
</script>

<style scoped>
.modal-overlay {
  background: rgba(0, 0, 0, 0.5);
}

.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>