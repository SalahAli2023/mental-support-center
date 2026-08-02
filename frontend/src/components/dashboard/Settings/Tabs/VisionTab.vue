<template>
  <div class="space-y-6">
    <!-- الرؤية -->
    <div class="card p-6 space-y-4">
      <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
        <EyeIcon class="w-5 h-5 text-brand-500" />
        الرؤية (Vision)
      </h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            رؤيتنا (عربي) *
          </label>
          <textarea 
            v-model="localSettings.vision_ar"
            rows="4"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
            placeholder="أن نكون في طليعة المجتمع المدني المتخصص في صناعة مستقبل أفضل للنساء والأطفال والفئات الضعيفة في اليمن"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            Vision (English) *
          </label>
          <textarea 
            v-model="localSettings.vision_en"
            rows="4"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="To be at the forefront of civil society specialized in creating a better future for women, children and vulnerable groups in Yemen"
          />
        </div>
      </div>
    </div>

    <!-- الرسالة -->
    <div class="card p-6 space-y-4">
      <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
        <MegaphoneIcon class="w-5 h-5 text-brand-500" />
        الرسالة (Mission)
      </h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            رسالتنا (عربي) *
          </label>
          <textarea 
            v-model="localSettings.mission_ar"
            rows="4"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
            placeholder="نسعى الى دعم ومساندة المرأة والطفل والفئات الضعيفة والمهمشة من خلال تعزيز قيم المشاركة والحماية والأمن والسلم المجتمعي..."
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-primary mb-2">
            Mission (English) *
          </label>
          <textarea 
            v-model="localSettings.mission_en"
            rows="4"
            class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
            placeholder="We seek to support and assist women, children and vulnerable and marginalized groups by promoting the values of participation, protection, security and community peace..."
          />
        </div>
      </div>
    </div>

    <!-- القيم -->
    <div class="card p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
          <HeartIcon class="w-5 h-5 text-brand-500" />
          القيم (Values)
        </h3>
        <button 
          @click="addValue"
          class="btn btn-outline text-sm"
        >
          <PlusIcon class="w-4 h-4 ml-2" />
          إضافة قيمة جديدة
        </button>
      </div>

      <div 
        v-for="(value, index) in localSettings.values" 
        :key="index"
        class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg"
      >
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              اسم القيمة (عربي) *
            </label>
            <input 
              v-model="value.title_ar"
              type="text"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
              placeholder="العدالة والإنصاف"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              Value Title (English) *
            </label>
            <input 
              v-model="value.title_en"
              type="text"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
              placeholder="Justice & Fairness"
            />
          </div>
        </div>
        <button 
          @click="removeValue(index)"
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
  EyeIcon,
  MegaphoneIcon,
  HeartIcon,
  PlusIcon,
  TrashIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'
import type { Value } from '@/types/settings'

// 🔹 Props & Emits
const props = defineProps<{
  saving?: boolean
}>()

const emit = defineEmits(['save', 'reset', 'update'])

const settingsStore = useSettingsStore()

// 🔹 البيانات المحلية
const localSettings = ref({
  vision_ar: '',
  vision_en: '',
  mission_ar: '',
  mission_en: '',
  values: [] as Value[],
})

// 🔹 تحميل البيانات من الـ Store
const loadSettings = () => {
  const vision = settingsStore.settings.vision
  localSettings.value = {
    vision_ar: vision.vision_ar || '',
    vision_en: vision.vision_en || '',
    mission_ar: vision.mission_ar || '',
    mission_en: vision.mission_en || '',
    values: vision.values ? JSON.parse(JSON.stringify(vision.values)) : [],
  }
}

// 🔹 دوال القيم
const addValue = () => {
  localSettings.value.values.push({
    title_ar: '',
    title_en: '',
  })
}

const removeValue = (index: number) => {
  localSettings.value.values.splice(index, 1)
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