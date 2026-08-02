<template>
<div class="space-y-6">
  <!-- Hero Section -->
  <div class="card p-6 space-y-4">
    <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
      <BuildingOffice2Icon class="w-5 h-5 text-brand-500" />
      بانر صفحة عن المركز (Hero Section)
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          العنوان الرئيسي (عربي) *
        </label>
        <input 
          v-model="localSettings.hero_title_ar"
          type="text"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
          placeholder="من"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          Title (English) *
        </label>
        <input 
          v-model="localSettings.hero_title_en"
          type="text"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
          placeholder="About"
        />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          النص المظلل (عربي) *
        </label>
        <input 
          v-model="localSettings.hero_highlight_ar"
          type="text"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
          placeholder="نحن"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          Highlight (English) *
        </label>
        <input 
          v-model="localSettings.hero_highlight_en"
          type="text"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
          placeholder="Us"
        />
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          الوصف الفرعي (عربي) *
        </label>
        <textarea 
          v-model="localSettings.hero_subtitle_ar"
          rows="2"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
          placeholder="نحن نعمل على تمكين المجتمع وتقديم برامج دعم نفسي واجتماعي ذات تأثير حقيقي."
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-primary mb-2">
          Subtitle (English) *
        </label>
        <textarea 
          v-model="localSettings.hero_subtitle_en"
          rows="2"
          class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
          placeholder="We work to empower the community and provide psychological and social support programs with real impact."
        />
      </div>
    </div>
  </div>

  <!-- فقرات النظرة العامة -->
  <div class="card p-6 space-y-4">
    <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
      <DocumentTextIcon class="w-5 h-5 text-brand-500" />
      فقرات النظرة العامة والتعريف بالمركز (Overview)
    </h3>

    <div 
      v-for="(paragraph, index) in 4" 
      :key="index"
      class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0"
    >
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            الفقرة {{ index + 1 }} (عربي) *
          </label>
          <textarea 
            v-model="localSettings[`overview_paragraph_${index + 1}_ar`]"
            rows="4"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
            :placeholder="`الفقرة ${index + 1} بالعربية...`"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            Paragraph {{ index + 1 }} (English) *
          </label>
          <textarea 
            v-model="localSettings[`overview_paragraph_${index + 1}_en`]"
            rows="4"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            :placeholder="`Paragraph ${index + 1} in English...`"
          />
        </div>
      </div>
    </div>
  </div>

  <!-- صورة النظرة العامة -->
<div class="grid grid-cols-1 gap-4">
  <div>
    <label class="block text-sm font-medium text-primary mb-2">
      صورة النظرة العامة (Overview Image)
    </label>
    
    <!-- معاينة الصورة الحالية -->
    <div v-if="localSettings.overview_image" class="mb-3">
      <div class="relative inline-block">
        <img 
          :src="getImageUrl(localSettings.overview_image)" 
          alt="صورة النظرة العامة"
          class="h-32 w-auto object-contain border border-gray-200 dark:border-gray-700 rounded-lg p-2 bg-white dark:bg-gray-800"
        />
        <button 
          @click="removeOverviewImage"
          class="absolute -top-2 -end-2 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition-colors"
        >
          <XMarkIcon class="w-4 h-4" />
        </button>
      </div>
    </div>
    
    <!-- زر رفع الصورة -->
    <div class="flex items-center gap-4">
      <label 
        for="overview-image-upload"
        class="btn btn-outline cursor-pointer"
      >
        <ArrowUpTrayIcon class="w-4 h-4 ml-2" />
        رفع صورة النظرة العامة
      </label>
      <input 
        id="overview-image-upload"
        type="file"
        accept="image/png,image/jpeg,image/webp"
        class="hidden"
        @change="handleOverviewImageUpload"
      />
      <span class="text-xs text-tertiary">PNG, JPG, WEBP</span>
    </div>
    
    <!-- رابط الصورة -->
    <div class="mt-2">
      <label class="block text-xs text-tertiary mb-1">
        أو أدخل رابط الصورة (URL)
      </label>
      <input 
        v-model="localSettings.overview_image"
        type="url"
        class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
        placeholder="https://example.com/image.png"
      />
    </div>
  </div>
</div>

  <!-- الشارات البارزة -->
  <div class="card p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
        <BadgeCheckIcon class="w-5 h-5 text-brand-500" />
        الشارات البارزة (Badges)
        <span class="text-xs text-tertiary font-normal">(اختياري - الحد الأقصى 10)</span>
      </h3>
      <button 
        @click="addBadge"
        :disabled="localSettings.badges.length >= 10"
        class="btn btn-outline text-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <PlusIcon class="w-4 h-4 ml-2" />
        إضافة شارة
      </button>
    </div>

    <!-- رسالة عند الوصول للحد الأقصى -->
    <p v-if="localSettings.badges.length >= 10" class="text-xs text-amber-600 dark:text-amber-400">
      ⚠️ لقد وصلت إلى الحد الأقصى للشارات (10)
    </p>

    <div 
      v-for="(badge, index) in localSettings.badges" 
      :key="index"
      class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg"
    >
      <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-tertiary mb-1">
            الشارة (عربي)
            <span class="text-xs text-tertiary">(اختياري)</span>
          </label>
          <input 
            v-model="badge.label_ar"
            type="text"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
            placeholder="مؤسسة مستقلة"
          />
        </div>
        <div>
          <label class="block text-xs font-medium text-tertiary mb-1">
            Badge (English)
            <span class="text-xs text-tertiary">(Optional)</span>
          </label>
          <input 
            v-model="badge.label_en"
            type="text"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="Independent Institution"
          />
        </div>
      </div>
      <button 
        @click="removeBadge(index)"
        class="text-red-500 hover:text-red-600 transition-colors mt-1"
        :disabled="localSettings.badges.length <= 1"
      >
        <TrashIcon class="w-5 h-5" />
      </button>
    </div>

    <!-- عرض الشارات الحالية في الموقع -->
    <div v-if="localSettings.badges.length > 0" class="mt-4 pt-4 border-t border-primary/10">
      <p class="text-xs text-tertiary mb-3">معاينة الشارات:</p>
      <div class="flex flex-wrap gap-3">
        <span 
          v-for="badge in localSettings.badges" 
          :key="badge.id || badge.label_ar"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-brand-100 text-brand-700 dark:bg-brand-900/30 dark:text-brand-300 border border-brand-200 dark:border-brand-800"
        >
          <BadgeCheckIcon class="w-4 h-4" />
          {{ badge.label_ar || badge.label_en || 'شارة' }}
        </span>
      </div>
    </div>

    <!-- رسالة عند عدم وجود شارات -->
    <div v-else class="text-center py-6 text-tertiary">
      <BadgeCheckIcon class="w-10 h-10 mx-auto mb-2 opacity-30" />
      <p class="text-sm">لا توجد شارات مضافة</p>
      <p class="text-xs">يمكنك إضافة شارات جديدة باستخدام الزر أعلاه</p>
    </div>
  </div>

  <!-- الأهداف -->
  <div class="card p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
        <StarIcon class="w-5 h-5 text-brand-500" />
        أهداف وغايات المركز (Objectives)
      </h3>
      <button 
        @click="addObjective"
        class="btn btn-outline text-sm"
      >
        <PlusIcon class="w-4 h-4 ml-2" />
        إضافة هدف جديد
      </button>
    </div>

    <div 
      v-for="(objective, index) in localSettings.objectives" 
      :key="index"
      class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg"
    >
      <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-tertiary mb-1">
            الهدف (بالعربية) *
          </label>
          <textarea 
            v-model="objective.text_ar"
            rows="2"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
            :placeholder="`الهدف ${index + 1} بالعربية...`"
          />
        </div>
        <div>
          <label class="block text-xs font-medium text-tertiary mb-1">
            Objective (English) *
          </label>
          <textarea 
            v-model="objective.text_en"
            rows="2"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            :placeholder="`Objective ${index + 1} in English...`"
          />
        </div>
      </div>
      <button 
        @click="removeObjective(index)"
        class="text-red-500 hover:text-red-600 transition-colors mt-1"
      >
        <TrashIcon class="w-5 h-5" />
      </button>
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
BuildingOffice2Icon,
DocumentTextIcon,
CheckBadgeIcon,
StarIcon,
PlusIcon,
TrashIcon,
ArrowPathIcon
} from '@heroicons/vue/24/outline'
import type { AboutBadge, Objective } from '@/types/settings'
import { useToast } from '@/composables/useToast'

const toast = useToast()

// 🔹 Props & Emits
const props = defineProps<{
saving?: boolean
}>()

const emit = defineEmits(['save', 'reset', 'update'])

const settingsStore = useSettingsStore()

// 🔹 البيانات المحلية
const localSettings = ref({
hero_title_ar: '',
hero_title_en: '',
hero_highlight_ar: '',
hero_highlight_en: '',
hero_subtitle_ar: '',
hero_subtitle_en: '',
overview_paragraph_1_ar: '',
overview_paragraph_1_en: '',
overview_paragraph_2_ar: '',
overview_paragraph_2_en: '',
overview_paragraph_3_ar: '',
overview_paragraph_3_en: '',
overview_paragraph_4_ar: '',
overview_paragraph_4_en: '',
badges: [] as AboutBadge[],
objectives: [] as Objective[],
overview_image: null,
})

// 🔹 تحميل البيانات من الـ Store
const loadSettings = () => {
const about = settingsStore.settings.about
  localSettings.value = {
    hero_title_ar: about.hero_title_ar || '',
    hero_title_en: about.hero_title_en || '',
    hero_highlight_ar: about.hero_highlight_ar || '',
    hero_highlight_en: about.hero_highlight_en || '',
    hero_subtitle_ar: about.hero_subtitle_ar || '',
    hero_subtitle_en: about.hero_subtitle_en || '',
    overview_paragraph_1_ar: about.overview_paragraph_1_ar || '',
    overview_paragraph_1_en: about.overview_paragraph_1_en || '',
    overview_paragraph_2_ar: about.overview_paragraph_2_ar || '',
    overview_paragraph_2_en: about.overview_paragraph_2_en || '',
    overview_paragraph_3_ar: about.overview_paragraph_3_ar || '',
    overview_paragraph_3_en: about.overview_paragraph_3_en || '',
    overview_paragraph_4_ar: about.overview_paragraph_4_ar || '',
    overview_paragraph_4_en: about.overview_paragraph_4_en || '',
    badges: about.badges ? JSON.parse(JSON.stringify(about.badges)) : [],
    objectives: about.objectives ? JSON.parse(JSON.stringify(about.objectives)) : [],
    overview_image: about.overview_image || null,

  }
}


// ✅ رفع صورة النظرة العامة
const handleOverviewImageUpload = async (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files || !input.files[0]) return
  
  const file = input.files[0]
  
  if (file.size > 2 * 1024 * 1024) {
    toast.warning('حجم الصورة كبير جداً. الحد الأقصى 2 ميجابايت')
    input.value = ''
    return
  }
  
  try {
    const url = await settingsStore.uploadImage(file, 'about', 'overview_image')
    localSettings.value.overview_image = url
    emit('update', { overview_image: url })
  } catch (error) {
    toast.error('فشل في رفع الصورة')
  } finally {
    input.value = ''
  }
}

// ✅ إزالة الصورة
const removeOverviewImage = () => {
  localSettings.value.overview_image = null
  emit('update', { overview_image: null })
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

// 🔹 دوال الشارات
const addBadge = () => {
  // ✅ منع إضافة أكثر من 10 شارات
  if (localSettings.value.badges.length >= 10) {
    // alert('لا يمكن إضافة أكثر من 10 شارات')
    toast.warning('لا يمكن إضافة أكثر من 10 شارات')
    return
  }
  
  localSettings.value.badges.push({
    label_ar: '',
    label_en: '',
  })
}

const removeBadge = (index: number) => {
  // ✅ منع حذف آخر شارة (اختياري)
  if (localSettings.value.badges.length <= 1) {
    toast.warning('يجب أن يبقى على الأقل شارة واحدة')
    // alert('يجب أن يبقى على الأقل شارة واحدة')
    return
  }
  localSettings.value.badges.splice(index, 1)
}

// 🔹 دوال الأهداف
const addObjective = () => {
localSettings.value.objectives.push({
  text_ar: '',
  text_en: '',
})
}

const removeObjective = (index: number) => {
  if (localSettings.value.objectives.length <= 1) {
    toast.warning('يجب أن يبقى على الأقل هدف واحد')
    return
  }
  localSettings.value.objectives.splice(index, 1)
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