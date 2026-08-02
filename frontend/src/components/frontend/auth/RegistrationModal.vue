<template>
  <div v-if="showRegistration" class="fixed inset-0 z-900 flex items-center justify-center p-4">
    <div class="fixed inset-0 modal-overlay" @click="closeRegistration"></div>
    
    <div class="relative bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto shadow-xl animate-slide-up" @click.stop>
      <!-- Header -->
      <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-start z-10">
        <div class="flex-1">
          <h2 class="text-2xl font-bold text-gray-800">{{ translate('registrationModal.title') }}</h2>
          <p class="text-gray-600 mt-2 text-sm leading-relaxed">
            {{ translate('registrationModal.description') }}
          </p>
        </div>
        <button @click="closeRegistration" class="flex-shrink-0 text-gray-500 hover:text-gray-700 transition-colors p-2 rounded-lg hover:bg-gray-100">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <div class="p-6">
        <!-- Registration Steps -->
        <div class="flex justify-between items-center mb-8">
          <div 
            v-for="step in steps" 
            :key="step.number"
            class="flex flex-col items-center relative flex-1"
            :class="{ 'active': currentStep === step.number, 'completed': currentStep > step.number }"
          >
            <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center mb-2 transition-colors z-10"
                  :class="currentStep >= step.number ? 'bg-primary-green border-primary-green' : 'bg-white border-gray-300'">
              <svg width="16" height="16" viewBox="0 0 25 25" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M24.0847 3.9275C25.3051 5.16416 25.3051 7.16918 24.0847 8.40584L11.5847 21.0725C10.3643 22.3092 8.38568 22.3092 7.16529 21.0725L0.915291 14.7392C-0.305097 13.5025 -0.305097 11.4975 0.915291 10.2608C2.13568 9.02417 4.11432 9.02417 5.33471 10.2608L9.375 14.355L19.6653 3.9275C20.8857 2.69083 22.8643 2.69083 24.0847 3.9275Z" 
                      :fill="currentStep >= step.number ? 'white' : '#A1B0D5'"/>
              </svg>
            </div>
            <span class="text-xs text-center" 
                  :class="currentStep >= step.number ? 'text-primary-green font-medium' : 'text-gray-500'">
              {{ step.title }}
            </span>
            <div v-if="step.number < 3" class="absolute top-4 right-1/2 w-full h-0.5 bg-gray-300 -z-10"></div>
          </div>
        </div>

        <!-- Steps Content -->
        <div class="min-h-64">
          <!-- Step 1: Phone Number -->
          <div v-if="currentStep === 1" class="space-y-6">
            <h3 class="text-lg font-semibold text-center text-gray-800">{{ translate('registrationModal.phoneStep.enterPhone') }}</h3>
            
            <form @submit.prevent="handlePhoneSubmit" class="space-y-4">
              <!-- Country -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">{{ translate('registrationModal.phoneStep.countryLabel') }}</label>
                <div class="relative">
                  <select 
                    v-model="form.country"
                    @change="updateDialCode"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent appearance-none bg-white pr-3"
                  >
                    <option value="">{{ translate('registrationModal.phoneStep.selectCountry') }}</option>
                    <option 
                      v-for="country in countries" 
                      :key="country.code"
                      :value="country.code"
                    >
                      {{ country.name }} {{ country.flag }}
                    </option>
                  </select>
                  <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 pointer-events-none">
                    <i class="fas fa-chevron-down"></i>
                  </div>
                </div>
              </div>

              <!-- Phone Number -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">{{ translate('registrationModal.phoneStep.phoneLabel') }}</label>
                <div class="flex gap-2">
                  <div class="flex-shrink-0 w-24 px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-center text-gray-700">
                    {{ form.dialCode }}
                  </div>
                  <input 
                    v-model="form.phone"
                    type="tel"
                    :placeholder="translate('registrationModal.phoneStep.phonePlaceholder')"
                    @input="validatePhone"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent"
                    :class="errors.phone ? 'border-red-500' : ''"
                  >
                </div>
                <p v-if="errors.phone" class="text-red-500 text-xs mt-1 text-right">{{ translate('registrationModal.phoneStep.phoneError') }}</p>
              </div>
              
              <button 
                @click="handlePhoneSubmit"
                :disabled="!isPhoneValid || isSubmitting"
                class="w-full py-3 bg-primary-green text-white rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-medium"
              >
                <span v-if="!isSubmitting">{{ translate('registrationModal.phoneStep.continue') }}</span>
                <span v-else class="flex items-center justify-center gap-2">
                  <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  {{ translate('registrationModal.phoneStep.sending') }}
                </span>
              </button>
            </form>
            <!-- Verification Method Modal -->
            <VerificationMethodModal
              :show="showMethodModal"
              :initial-method="selectedMethod"
              :language="currentLanguage"

              @close="showMethodModal = false"
              @confirm="handleMethodConfirm"
            />
          </div>


          <!-- Step 2: Verification Code -->
          <div v-if="currentStep === 2" class="space-y-6">
            <h3 class="text-lg font-semibold text-center text-gray-800">{{ translate('registrationModal.otpStep.title') }}</h3>
            
            <form @submit.prevent="handleOtpSubmit" class="space-y-4">
              <div class="text-center space-y-2">
                <p class="text-gray-600">{{ translate('registrationModal.otpStep.sentTo') }}</p>
                <p class="font-semibold text-primary-green">{{ form.dialCode }} {{ form.phone }}</p>
              </div>

              <!-- OTP Inputs -->
              <div class="flex justify-center gap-3">
                <input 
                  v-for="n in 4"
                  :key="n"
                  v-model="otp[n-1]"
                  type="number"
                  maxlength="1"
                  @input="handleOtpInput(n-1, $event)"
                  @keydown="handleOtpKeydown(n-1, $event)"
                  class="w-16 h-16 text-center text-2xl font-bold border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent"
                >
              </div>

              <!-- OTP Actions -->
              <div class="text-center space-y-3">
                <div v-if="resendCounter > 0" class="text-gray-600">
                  <span>{{ translate('registrationModal.otpStep.resendIn') }}</span>
                  <span class="font-semibold mx-1">{{ formatTime(resendCounter) }}</span>
                </div>
                <button 
                  v-else
                  type="button"
                  @click="resendOtp"
                  class="text-primary-green hover:text-opacity-80 font-medium transition-colors"
                >
                  {{ translate('registrationModal.otpStep.resend') }}
                </button>
                
                <button 
                  type="button"
                  @click="currentStep = 1"
                  class="text-gray-600 hover:text-gray-800 block mx-auto transition-colors"
                >
                  {{ translate('registrationModal.otpStep.editNumber') }}
                </button>
              </div>

              <button 
                type="submit"
                :disabled="!isOtpComplete || isSubmitting"
                class="w-full py-3 bg-primary-green text-white rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-medium"
              >
                <span v-if="!isSubmitting">{{ translate('registrationModal.otpStep.confirm') }}</span>
                <span v-else class="flex items-center justify-center gap-2">
                  <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  {{ translate('registrationModal.otpStep.verifying') }}
                </span>
              </button>
            </form>
          </div>

          <!-- Step 3: Personal Information -->
          <div v-if="currentStep === 3" class="space-y-6">
            <h3 class="text-lg font-semibold text-center text-gray-800">{{ translate('registrationModal.infoStep.title') }}</h3>
            
            <form @submit.prevent="handleInfoSubmit" class="space-y-4">
              <!-- Name -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">{{ translate('registrationModal.infoStep.nameLabel') }}</label>
                <input 
                  v-model="form.name"
                  type="text"
                  :placeholder="translate('registrationModal.infoStep.namePlaceholder')"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent"
                  :class="errors.name ? 'border-red-500' : ''"
                >
                <p v-if="errors.name" class="text-red-500 text-xs mt-1 text-right">{{ translate('registrationModal.infoStep.nameRequired') }}</p>
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">{{ translate('registrationModal.infoStep.emailLabel') }}</label>
                <input 
                  v-model="form.email"
                  type="email"
                  :placeholder="translate('registrationModal.infoStep.emailPlaceholder')"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-green focus:border-transparent"
                  :class="errors.email ? 'border-red-500' : ''"
                >
                <p v-if="errors.email" class="text-red-500 text-xs mt-1 text-right">{{ errors.email }}</p>
              </div>

              <button 
                type="submit"
                :disabled="!form.name || isSubmitting"
                class="w-full py-3 bg-primary-green text-white rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-medium"
              >
                <span v-if="!isSubmitting">{{ translate('registrationModal.infoStep.confirm') }}</span>
                <span v-else class="flex items-center justify-center gap-2">
                  <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                  {{ translate('registrationModal.infoStep.creating') }}
                </span>
              </button>
            </form>
          </div>

          <!-- Step 4: Success -->
          <div v-if="currentStep === 4" class="text-center space-y-6">
            <h3 class="text-lg font-semibold text-gray-800">{{ translate('registrationModal.successStep.title') }}</h3>
            <p class="text-gray-600 leading-relaxed">
              {{ translate('registrationModal.successStep.message') }}
            </p>

            <!-- Download Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
              <a href="https://apps.apple.com/app/id1244654624?mt=8" target="_blank" 
                  class="flex items-center gap-3 px-4 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors">
                <i class="fab fa-apple text-xl"></i>
                <div class="text-right">
                  <div class="text-xs opacity-80">{{ translate('registrationModal.successStep.availableOn') }}</div>
                  <div class="font-semibold">{{ translate('registrationModal.successStep.appStore') }}</div>
                </div>
              </a>
              
              <a href="https://play.google.com/store/apps/details?id=com.labayh" target="_blank" 
                  class="flex items-center gap-3 px-4 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors">
                <i class="fab fa-google-play text-xl"></i>
                <div class="text-right">
                  <div class="text-xs opacity-80">{{ translate('registrationModal.successStep.availableOn') }}</div>
                  <div class="font-semibold">{{ translate('registrationModal.successStep.googlePlay') }}</div>
                </div>
              </a>
            </div>

            <button 
              @click="handleRegistrationSuccess"
              class="w-full py-3 bg-primary-green text-white rounded-lg hover:bg-opacity-90 transition-all font-medium"
            >
              {{ translate('registrationModal.successStep.start') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useNotifications } from '@/composables/useNotifications'
import { useProfile } from '@/composables/useProfile'
import { countries } from '@/data/countries'
import { t } from '@/locales'
import VerificationMethodModal from '@/components/frontend/auth/VerificationMethodModal.vue'

const props = defineProps({
  showRegistration: {
    type: Boolean,
    default: false
  },
  language: {
    type: String,
    default: 'ar'
  }
})

const { showSuccess, showError } = useNotifications()
const { login } = useProfile()

const emit = defineEmits(['close', 'switch-to-login', 'registration-success'])
const currentLanguage = ref(props.language)
const currentStep = ref(1)
const isSubmitting = ref(false)
const resendCounter = ref(0)
const otp = ref(['', '', '', ''])

const form = reactive({
  country: '+967',
  dialCode: '+967',
  phone: '',
  name: '',
  email: ''
})

const errors = reactive({})
const showMethodModal = ref(false)
const selectedMethod = ref('sms')

// دالة الترجمة
const translate = (key) => {
  return t(key, props.language)
}

const steps = computed(() => [
  { number: 1, title: translate('registrationModal.steps.phone') },
  { number: 2, title: translate('registrationModal.steps.otp') },
  { number: 3, title: translate('registrationModal.steps.info') }
])

const isPhoneValid = computed(() => {
  return form.phone.length >= 9 && /^7\d{8}$/.test(form.phone)
})

const isOtpComplete = computed(() => {
  return otp.value.every(digit => digit !== '')
})

const validatePhone = () => {
  if (form.phone && !/^7\d{8}$/.test(form.phone)) {
    errors.phone = translate('registrationModal.phoneStep.phoneError')
  } else {
    delete errors.phone
  }
}

const updateDialCode = () => {
  const country = countries.find(c => c.code === form.country)
  if (country) {
    form.dialCode = country.code
  }
}

const handlePhoneSubmit = async () => {
  if (!isPhoneValid.value) return
  
  isSubmitting.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 1500))
    showMethodModal.value = true
  } catch (error) {
    showError(translate('registrationModal.errors.sendCode'))
  } finally {
    isSubmitting.value = false
  }
}

const handleMethodConfirm = async (method) => {
  selectedMethod.value = method
  isSubmitting.value = true
  
  try {
    await new Promise(resolve => setTimeout(resolve, 1000))
    currentStep.value = 2
    startResendCounter()
    showSuccess(translate('registrationModal.success.codeSent'))
  } catch (error) {
    showError(translate('registrationModal.errors.sendCode'))
  } finally {
    isSubmitting.value = false
  }
}

const handleOtpInput = (index, event) => {
  const value = event.target.value
  if (value.length > 1) {
    otp.value[index] = value.slice(-1)
  }
  
  if (value && index < 3) {
    const nextInput = event.target.nextElementSibling
    if (nextInput) nextInput.focus()
  }
}

const handleOtpKeydown = (index, event) => {
  if (event.key === 'Backspace' && !otp.value[index] && index > 0) {
    const prevInput = event.target.previousElementSibling
    if (prevInput) prevInput.focus()
  }
}

const handleOtpSubmit = async () => {
  if (!isOtpComplete.value) return
  
  isSubmitting.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 1500))
    currentStep.value = 3
    showSuccess(translate('registrationModal.success.verified'))
  } catch (error) {
    showError(translate('registrationModal.errors.verifyCode'))
  } finally {
    isSubmitting.value = false
  }
}

const handleInfoSubmit = async () => {
  if (!form.name.trim()) {
    errors.name = translate('registrationModal.infoStep.nameRequired')
    return
  }
  
  isSubmitting.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    const userData = {
      id: Date.now(),
      name: form.name,
      email: form.email,
      phone: form.dialCode + form.phone,
      createdAt: new Date().toISOString()
    }
    
    login(userData)
    currentStep.value = 4
    showSuccess(translate('registrationModal.success.accountCreated'))
    
  } catch (error) {
    showError(translate('registrationModal.errors.createAccount'))
  } finally {
    isSubmitting.value = false
  }
}

const handleRegistrationSuccess = () => {
  closeRegistration()
  emit('registration-success')
}

const startResendCounter = () => {
  resendCounter.value = 120
  const timer = setInterval(() => {
    resendCounter.value--
    if (resendCounter.value <= 0) {
      clearInterval(timer)
    }
  }, 1000)
}

const resendOtp = async () => {
  isSubmitting.value = true
  try {
    await new Promise(resolve => setTimeout(resolve, 1000))
    startResendCounter()
    showSuccess(translate('registrationModal.success.codeResent'))
  } catch (error) {
    showError(translate('registrationModal.errors.resendCode'))
  } finally {
    isSubmitting.value = false
  }
}

const formatTime = (seconds) => {
  const minutes = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${minutes}:${secs.toString().padStart(2, '0')}`
}

const closeRegistration = () => {
  emit('close')
  currentStep.value = 1
  isSubmitting.value = false
  resendCounter.value = 0
  otp.value = ['', '', '', '']
  Object.assign(form, {
    country: '+967',
    dialCode: '+967',
    phone: '',
    name: '',
    email: ''
  })
  Object.keys(errors).forEach(key => delete errors[key])
}

onMounted(() => {
  updateDialCode()
})
</script>

<style scoped>
/* Only necessary CSS for animations */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}
</style>