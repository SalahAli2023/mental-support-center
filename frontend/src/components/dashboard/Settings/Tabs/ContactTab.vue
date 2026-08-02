<template>
  <div class="space-y-6">
    <!-- معلومات التواصل -->
    <div class="card p-6 space-y-4">
      <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
        <PhoneIcon class="w-5 h-5 text-brand-500" />
        معلومات التواصل
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            رقم الهاتف للتواصل *
          </label>
          <input 
            v-model="localSettings.phone"
            type="text"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="+967 770 000 000"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            البريد الإلكتروني الرسمي *
          </label>
          <input 
            v-model="localSettings.email"
            type="email"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="info@cs-wc.org"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            العنوان / المقر (عربي) *
          </label>
          <input 
            v-model="localSettings.address_ar"
            type="text"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
            placeholder="تعز، اليمن"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            Address (English) *
          </label>
          <input 
            v-model="localSettings.address_en"
            type="text"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="Taiz, Yemen"
          />
        </div>
      </div>
    </div>

    <!-- وسائل التواصل الاجتماعي -->
    <div class="card p-6 space-y-4">
      <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
        <ShareIcon class="w-5 h-5 text-brand-500" />
        وسائل التواصل الاجتماعي
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            فيسبوك (Facebook)
          </label>
          <input 
            v-model="localSettings.facebook"
            type="url"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="https://facebook.com/..."
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            تويتر (Twitter / X)
          </label>
          <input 
            v-model="localSettings.twitter"
            type="url"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="https://twitter.com/..."
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            إنستغرام (Instagram)
          </label>
          <input 
            v-model="localSettings.instagram"
            type="url"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="https://instagram.com/..."
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            يوتيوب (YouTube)
          </label>
          <input 
            v-model="localSettings.youtube"
            type="url"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="https://youtube.com/..."
          />
        </div>

        <!-- لينكد ان -->
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            لينكد ان (LinkedIn)
          </label>
          <input 
            v-model="localSettings.linkedin"
            type="url"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="https://linkedin.com/..."
          />
        </div>
      </div>
    </div>

    <!-- إعدادات الفوتر -->
    <div class="card p-6 space-y-4">
      <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
        <DocumentTextIcon class="w-5 h-5 text-brand-500" />
        إعدادات الفوتر (Footer)
      </h3>

      <!-- شعار الفوتر -->
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          شعار الفوتر (Footer Logo)
        </label>

        <!-- معاينة الشعار الحالي -->
        <div v-if="localSettings.footer_logo" class="mb-3">
          <div class="relative inline-block">
            <img 
              :src="getImageUrl(localSettings.footer_logo)" 
              alt="شعار الفوتر"
              class="h-20 w-auto object-contain border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800"
            />
            <button 
              @click="removeFooterLogo"
              class="absolute -top-2 -end-2 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition-colors"
            >
              <XMarkIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- زر رفع الشعار -->
        <div class="flex items-center gap-4">
          <label 
            for="footer-logo-upload"
            class="btn btn-outline cursor-pointer"
          >
            <ArrowUpTrayIcon class="w-4 h-4 ml-2" />
            رفع شعار الفوتر
          </label>
          <input 
            id="footer-logo-upload"
            type="file"
            accept="image/png,image/svg+xml,image/jpeg,image/webp"
            class="hidden"
            @change="handleFooterLogoUpload"
          />
          <span class="text-xs text-tertiary">PNG, SVG, JPG, WEBP</span>
        </div>

        <!-- رابط الشعار -->
        <div class="mt-2">
          <label class="block text-xs text-tertiary mb-1">
            أو أدخل رابط الشعار (URL)
          </label>
          <input 
            v-model="localSettings.footer_logo"
            type="url"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="https://example.com/footer-logo.png"
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            وصف المنصة أسفل الموقع (عربي) *
          </label>
          <textarea 
            v-model="localSettings.footer_description_ar"
            rows="3"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
            placeholder="منصة رائدة في تقديم خدمات الدعم النفسي والاستشارات المتخصصة. نؤمن بأن الصحة النفسية هي أساس الحياة المتوازنة والمستقرة."
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            Footer Description (English) *
          </label>
          <textarea 
            v-model="localSettings.footer_description_en"
            rows="3"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="A leading platform in providing psychological support services and specialized consultations. We believe that mental health is the foundation of a balanced and stable life."
          />
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            حقوق الملكية والنشر (عربي) *
          </label>
          <input 
            v-model="localSettings.footer_copyright_ar"
            type="text"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
            placeholder="جميع الحقوق محفوظة - مركز الدراسات الاستراتيجية لدعم المرأة والطفل."
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            Copyright Text (English) *
          </label>
          <input 
            v-model="localSettings.footer_copyright_en"
            type="text"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="All rights reserved - Center for Strategic Studies to Support Women and Children."
          />
        </div>
      </div>
    </div>

    <!-- أزرار الإجراءات -->
    <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-primary/10">
      <button 
        @click="$emit('reset')"
        class="btn btn-outline text-sm"
      >
        <ArrowPathIcon class="w-4 h-4 ml-2" />
        إعادة للافتراضي
      </button>
      
      <button 
        @click="$emit('save')"
        :disabled="saving"
        class="btn btn-primary"
      >
        <span v-if="saving" class="flex items-center gap-2">
          <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          جاري الحفظ...
        </span>
        <span v-else>حفظ التغييرات</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { useSettingsStore } from '@/stores/settings'
import { 
  PhoneIcon,
  ShareIcon,
  DocumentTextIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'

import { useToast } from '@/composables/useToast' // ✅ استيراد الـ Toast

const toast = useToast()

// 🔹 Props & Emits
const props = defineProps<{
  saving?: boolean
}>()

const emit = defineEmits(['save', 'reset', 'update'])

const settingsStore = useSettingsStore()

// 🔹 البيانات المحلية
const localSettings = ref({
  phone: '',
  email: '',
  address_ar: '',
  address_en: '',
  facebook: '',
  twitter: '',
  instagram: '',
  youtube: '',
  linkedin: '',  // ✅ إضافة
  footer_logo: null,  // ✅ إضافة
  footer_description_ar: '',
  footer_description_en: '',
  footer_copyright_ar: '',
  footer_copyright_en: '',
})

// 🔹 تحميل البيانات من الـ Store
const loadSettings = () => {
  const contact = settingsStore.settings.contact
  localSettings.value = {
    phone: contact.phone || '',
    email: contact.email || '',
    address_ar: contact.address_ar || '',
    address_en: contact.address_en || '',
    facebook: contact.facebook || '',
    twitter: contact.twitter || '',
    instagram: contact.instagram || '',
    youtube: contact.youtube || '',
    linkedin: contact.linkedin || '',  // ✅ إضافة
    footer_logo: contact.footer_logo || null,  // ✅ إضافة
    footer_description_ar: contact.footer_description_ar || '',
    footer_description_en: contact.footer_description_en || '',
    footer_copyright_ar: contact.footer_copyright_ar || '',
    footer_copyright_en: contact.footer_copyright_en || '',
  }
}

// ✅ رفع شعار الفوتر
const handleFooterLogoUpload = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files || !input.files[0]) return
  
  const file = input.files[0]
  
  if (file.size > 2 * 1024 * 1024) {
    toast.warning('حجم الصورة كبير جداً. الحد الأقصى 2 ميجابايت')
    input.value = ''
    return
  }
  
  try {
    const url = await settingsStore.uploadImage(file, 'contact', 'footer_logo')
    localSettings.value.footer_logo = url
    emit('update', { footer_logo: url })
  } catch (error) {
    toast.error('فشل في رفع الشعار')
  } finally {
    input.value = ''
  }
}

// ✅ إزالة شعار الفوتر
const removeFooterLogo = () => {
  localSettings.value.footer_logo = null
  emit('update', { footer_logo: null })
}

// ✅ الحصول على رابط الصورة
const getImageUrl = (path: string) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  if (path.startsWith('data:image')) return path
  
  const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  const baseUrl = apiBaseUrl.replace('/api', '')
  const cleanUrl = path.replace(/^\/+/, '')
  return `${baseUrl}/storage/${cleanUrl}`
}

// 🔹 مراقبة التغييرات
watch(localSettings, (newVal) => {
  emit('update', newVal)
}, { deep: true })

// 🔹 تحميل البيانات عند التركيب
onMounted(() => {
  loadSettings()
})

// 🔹 إعادة تحميل البيانات عند التحديث
defineExpose({ loadSettings })
</script>

<style scoped>
.direction-ltr {
  direction: ltr;
  text-align: left;
}
</style>