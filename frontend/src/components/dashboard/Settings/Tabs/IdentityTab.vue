<template>
  <div class="space-y-6">
    <!-- الهوية البصرية -->
    <div class="card p-6 space-y-6">
      <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
        <BuildingOfficeIcon class="w-5 h-5 text-brand-500" />
        الشعار والهوية البصرية
      </h3>
      
      <!-- اسم المركز (عربي) -->
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          اسم المركز (بالعربية) *
        </label>
        <input 
          v-model="localSettings.site_name_ar"
          type="text"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
          placeholder="مركز الدراسات الاستراتيجية لدعم المرأة والطفل"
        />
      </div>
      
      <!-- اسم المركز (English) -->
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          اسم المركز (English) *
        </label>
        <input 
          v-model="localSettings.site_name_en"
          type="text"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent direction-ltr"
          placeholder="Center for Strategic Studies to Support Women and Children"
        />
      </div>
      
      <!-- الوصف المختصر (عربي) -->
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          الوصف المختصر للمؤسسة (بالعربية)
        </label>
        <input 
          v-model="localSettings.site_tagline_ar"
          type="text"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
          placeholder="منصة متخصصة في تمكين المجتمع وتقديم برامج دعم نفسي واجتماعي ذات تأثير حقيقي"
        />
      </div>
      
      <!-- الوصف المختصر (English) -->
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          الوصف المختصر (English Tagline)
        </label>
        <input 
          v-model="localSettings.site_tagline_en"
          type="text"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent direction-ltr"
          placeholder="Specialized platform for community empowerment and psychosocial support"
        />
      </div>
      
      <!-- الشعار -->
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          الشعار الرئيسي (Logo Image)
        </label>
        
        <!-- معاينة الشعار الحالي -->
        <div v-if="localSettings.site_logo" class="mb-3">
          <div class="relative inline-block">
            <img 
              :src="getImageUrl(localSettings.site_logo)" 
              alt="شعار المركز"
              class="h-20 w-auto object-contain border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800"
            />
            <button 
              @click="removeLogo"
              class="absolute -top-2 -end-2 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition-colors"
            >
              <XMarkIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
        
        <!-- زر رفع الشعار -->
        <div class="flex items-center gap-4">
          <label 
            for="logo-upload"
            class="btn btn-outline cursor-pointer"
          >
            <ArrowUpTrayIcon class="w-4 h-4 ml-2" />
            رفع صورة شعار جديدة
          </label>
          <input 
            id="logo-upload"
            type="file"
            accept="image/png,image/svg+xml,image/jpeg,image/webp"
            class="hidden"
            @change="handleLogoUpload"
          />
          <span class="text-xs text-tertiary">PNG, SVG, JPG</span>
        </div>
        
        <!-- رابط الصورة (URL) -->
        <div class="mt-2">
          <label class="block text-xs text-tertiary mb-1">
            أو أدخل رابط صورة الشعار (URL)
          </label>
          <input 
            v-model="localSettings.site_logo"
            type="url"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent direction-ltr"
            placeholder="https://example.com/logo.png"
          />
        </div>
      </div>

      <!-- حقل الفافيكون -->
    <div>
      <label class="block text-sm font-medium text-primary mb-2">
        أيقونة المتصفح (Favicon)
      </label>

      <!-- معاينة الفافيكون الحالي -->
      <div v-if="localSettings.site_favicon" class="mb-3">
        <div class="relative inline-block">
          <img 
            :src="getImageUrl(localSettings.site_favicon)" 
            alt="أيقونة الموقع"
            class="h-16 w-16 object-contain border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800"
          />
          <button 
            @click="removeFavicon"
            class="absolute -top-2 -end-2 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition-colors"
          >
            <XMarkIcon class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- زر رفع الفافيكون -->
      <div class="flex items-center gap-4">
        <label 
          for="favicon-upload"
          class="btn btn-outline cursor-pointer"
        >
          <ArrowUpTrayIcon class="w-4 h-4 ml-2" />
          رفع أيقونة جديدة
        </label>
        <input 
          id="favicon-upload"
          type="file"
          accept="image/png,image/svg+xml,image/x-icon,image/vnd.microsoft.icon"
          class="hidden"
          @change="handleFaviconUpload"
        />
        <span class="text-xs text-tertiary">PNG, SVG, ICO</span>
      </div>

      <!-- رابط الفافيكون -->
      <div class="mt-2">
        <label class="block text-xs text-tertiary mb-1">
          أو أدخل رابط الأيقونة (URL)
        </label>
        <input 
          v-model="localSettings.site_favicon"
          type="url"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
          placeholder="https://example.com/favicon.ico"
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
  BuildingOfficeIcon,
  XMarkIcon,
  ArrowUpTrayIcon,
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
  site_name_ar: '',
  site_name_en: '',
  site_tagline_ar: '',
  site_tagline_en: '',
  site_logo: null as string | null,
  site_favicon: null as string | null,
})

// 🔹 تحميل البيانات من الـ Store
const loadSettings = () => {
  const identity = settingsStore.settings.identity
  localSettings.value = {
    site_name_ar: identity.site_name_ar || '',
    site_name_en: identity.site_name_en || '',
    site_tagline_ar: identity.site_tagline_ar || '',
    site_tagline_en: identity.site_tagline_en || '',
    site_logo: identity.site_logo || null,
    site_favicon: identity.site_favicon || null,
  }
}

// 🔹 الحصول على رابط الصورة
const getImageUrl = (path: string) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  if (path.startsWith('data:image')) return path
  
  const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
  const baseUrl = apiBaseUrl.replace('/api', '')
  const cleanUrl = path.replace(/^\/+/, '')
  return `${baseUrl}/storage/${cleanUrl}`
}

// 🔹 رفع الشعار
const handleLogoUpload = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files || !input.files[0]) return
  
  const file = input.files[0]
  
  // التحقق من الحجم
  if (file.size > 2 * 1024 * 1024) {
    toast('حجم الصورة كبير جداً. الحد الأقصى 2 ميجابايت')
    input.value = ''
    return
  }
  
  try {
    const url = await settingsStore.uploadImage(file, 'identity', 'site_logo')
    localSettings.value.site_logo = url
    emit('update', { site_logo: url })
  } catch (error) {
    // alert('فشل في رفع الصورة')
    toast('فشل في رفع الصورة')
    console.error(error)
  } finally {
    input.value = ''
  }
}

// 🔹 إزالة الشعار
const removeLogo = () => {
  localSettings.value.site_logo = null
  emit('update', { site_logo: null })
}


// ✅ رفع الفافيكون
const handleFaviconUpload = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files || !input.files[0]) return
  
  const file = input.files[0]
  
  // التحقق من الحجم (1 ميجابايت كحد أقصى للفافيكون)
  if (file.size > 1 * 1024 * 1024) {
    toast.warning('حجم الأيقونة كبير جداً. الحد الأقصى 1 ميجابايت')
    input.value = ''
    return
  }
  
  try {
    const url = await settingsStore.uploadImage(file, 'identity', 'site_favicon')
    localSettings.value.site_favicon = url
    emit('update', { site_favicon: url })
  } catch (error) {
    toast.error('فشل في رفع الأيقونة')
  } finally {
    input.value = ''
  }
}

// ✅ إزالة الفافيكون
const removeFavicon = () => {
  localSettings.value.site_favicon = null
  emit('update', { site_favicon: null })
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