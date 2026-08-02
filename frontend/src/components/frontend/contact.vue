<template>
  <div>
    <!-- Header -->
    <Header />

    <!-- Hero -->
    <Hero
      :title="translate('contact.hero.title')"
      :highlight="translate('contact.hero.highlight')"
      :subtitle="translate('contact.hero.subtitle')"
      :buttons="[
        { text: translate('buttons.startJourney'), icon: 'fas fa-play-circle', primary: true },
        { text: translate('buttons.learnMore'), icon: 'fas fa-info-circle', primary: false }
      ]"
    />

    <!-- Contact Section -->
    <section class="py-16 bg-primary">
      <div class="max-w-6xl mx-auto px-6">
        <!-- Contact Information -->
        <div class="grid md:grid-cols-3 gap-6 mb-12">
          <!-- Phone Card -->
          <div
            class="flex items-center gap-4 p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all"
          >
            <div
              class="w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-tr from-primary-green to-secondary-green text-white text-2xl"
            >
              <i class="fa-solid fa-phone"></i>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">{{ translate('contact.section.phone') }}</h4>
              <p class="text-gray-500 text-sm">+967 737950689</p>
            </div>
          </div>

          <!-- Email Card -->
          <div
            class="flex items-center gap-4 p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all"
          >
            <div
              class="w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-tr from-primary-green to-secondary-green text-white text-2xl"
            >
              <i class="fa-solid fa-envelope"></i>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">{{ translate('contact.section.email') }}</h4>
              <p class="text-gray-500 text-sm">farhm@example.com</p>
            </div>
          </div>

          <!-- Location Card -->
          <div
            class="flex items-center gap-4 p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all"
          >
            <div
              class="w-14 h-14 flex items-center justify-center rounded-xl bg-gradient-to-tr from-primary-green to-secondary-green text-white text-2xl"
            >
              <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
              <h4 class="font-semibold text-gray-800">{{ translate('contact.section.location') }}</h4>
              <p class="text-gray-500 text-sm">{{ currentLanguage === 'ar' ? 'اليمن - تعز' : 'Yemen - Taiz' }}</p>
            </div>
          </div>
        </div>

        <!-- Contact Form -->
        <div>
          <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ translate('contact.section.title') }}</h2>
          <div class="w-10 h-[2px] bg-primary-green mb-6 rounded-full"></div>

          <form class="space-y-6" @submit.prevent="handleSubmit">
            <div class="grid md:grid-cols-2 gap-4">
              <input
                type="text"
                v-model="form.name"
                :placeholder="translate('contact.form.fullName')"
                class="w-full border border-gray-200 rounded-full px-5 py-3 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-green focus:border-transparent transition-all"
                :dir="isRTL ? 'rtl' : 'ltr'"
                required
              />
              <input
                type="email"
                v-model="form.email"
                :placeholder="translate('contact.form.email')"
                class="w-full border border-gray-200 rounded-full px-5 py-3 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-green focus:border-transparent transition-all"
                :dir="isRTL ? 'rtl' : 'ltr'"
                required
              />
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <input
                type="text"
                v-model="form.subject"
                :placeholder="translate('contact.form.subject')"
                class="w-full border border-gray-200 rounded-full px-5 py-3 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-green focus:border-transparent transition-all"
                :dir="isRTL ? 'rtl' : 'ltr'"
              />
              <select
                v-model="form.message_type"
                class="w-full border border-gray-200 rounded-full px-5 py-3 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-green focus:border-transparent transition-all bg-white"
                :dir="isRTL ? 'rtl' : 'ltr'"
                required
              >
                <option value="" disabled>{{ translate('contact.form.messageTypePlaceholder') }}</option>
                <option
                  v-for="option in messageTypeOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <textarea
              rows="5"
              v-model="form.message"
              :placeholder="translate('contact.form.message')"
              class="w-full border border-gray-200 rounded-xl px-5 py-3 text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-green focus:border-transparent transition-all"
              :dir="isRTL ? 'rtl' : 'ltr'"
              required
            ></textarea>

            <div v-if="successMessage" class="text-primary-green text-sm font-medium">
              {{ successMessage }}
            </div>
            <div v-if="errorMessage" class="text-red-500 text-sm font-medium">
              {{ errorMessage }}
            </div>

            <div class="flex justify-start">
              <button
                type="submit"
                class="px-8 py-3 rounded-2xl bg-gradient-to-tr from-primary-green to-secondary-green text-white font-semibold hover:opacity-90 transition-all shadow-md disabled:opacity-60 disabled:cursor-not-allowed"
                :disabled="loading"
              >
                <span v-if="loading" class="inline-flex items-center gap-2">
                  <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z"></path>
                  </svg>
                  {{ translate('contact.form.sending') }}
                </span>
                <span v-else>
                  {{ translate('contact.form.send') }}
                </span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import Header from '@/components/frontend/layouts/Header.vue'  
import Footer from '@/components/frontend/layouts/Footer.vue' 
import Hero from '@/components/frontend/layouts/hero.vue'
import { useTranslations } from '@/composables/useTranslations'
import api from '@/utils/api'

const { translate, currentLanguage } = useTranslations()

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message_type: '',
  message: ''
})

const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const isRTL = computed(() => currentLanguage.value === 'ar')

const messageTypeOptions = computed(() => [
  { value: 'complaint', label: translate('contact.form.types.complaint') },
  { value: 'inquiry', label: translate('contact.form.types.inquiry') },
  { value: 'review', label: translate('contact.form.types.review') }
])

const resetForm = () => {
  form.name = ''
  form.email = ''
  form.subject = ''
  form.message_type = ''
  form.message = ''
}

const handleSubmit = async () => {
  successMessage.value = ''
  errorMessage.value = ''

  if (!form.message_type) {
    errorMessage.value = translate('contact.form.validation.messageType')
    return
  }

  loading.value = true
  try {
    await api.post('/contact/messages', { ...form })
    successMessage.value = translate('contact.form.success')
    resetForm()
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || translate('contact.form.error')
  } finally {
    loading.value = false
  }
}
</script>

<!-- No scoped style -->
<style>
/* يمكن إضافة أي تخصيص عام هنا */
</style>