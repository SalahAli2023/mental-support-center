<template>
  <div class="space-y-6">
    <!-- الإنجازات والأرقام -->
    <div class="card p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
          <ChartBarIcon class="w-5 h-5 text-brand-500" />
          إنجازات وأرقام المركز
          <span class="text-xs text-tertiary font-normal">(اختياري)</span>
        </h3>
        <button 
          @click="addStat"
          class="btn btn-outline text-sm"
        >
          <PlusIcon class="w-4 h-4 ml-2" />
          إضافة إحصائية
        </button>
      </div>

      <div 
        v-for="(stat, index) in localSettings.stats" 
        :key="index"
        class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg"
      >
        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              التسمية (عربي) *
            </label>
            <input 
              v-model="stat.label_ar"
              type="text"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
              placeholder="جلسة استشارية"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              Label (English) *
            </label>
            <input 
              v-model="stat.label_en"
              type="text"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
              placeholder="Consultation Sessions"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              الرقم / القيمة *
            </label>
            <input 
              v-model="stat.value"
              type="text"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
              placeholder="5000+"
            />
          </div>
        </div>
        <button 
          @click="removeStat(index)"
          class="text-red-500 hover:text-red-600 transition-colors mt-1"
        >
          <TrashIcon class="w-5 h-5" />
        </button>
      </div>

      <!-- معاينة الإنجازات -->
      <div v-if="localSettings.stats.length > 0" class="mt-4 pt-4 border-t border-primary/10">
        <p class="text-xs text-tertiary mb-3">معاينة الإنجازات:</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div 
            v-for="stat in localSettings.stats" 
            :key="stat.id || stat.label_ar"
            class="text-center p-4 bg-brand-50 dark:bg-brand-900/20 rounded-lg"
          >
            <div class="text-2xl font-bold text-brand-600 dark:text-brand-400">
              {{ stat.value || '0' }}
            </div>
            <div class="text-sm text-tertiary mt-1">
              {{ stat.label_ar || stat.label_en || 'إحصائية' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ✅ توصيات العملاء (Testimonials) -->
    <div class="card p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-primary flex items-center gap-2">
          <ChatBubbleLeftRightIcon class="w-5 h-5 text-brand-500" />
          توصيات العملاء (Testimonials)
          <span class="text-xs text-tertiary font-normal">(اختياري)</span>
        </h3>
        <button 
          @click="addTestimonial"
          class="btn btn-outline text-sm"
        >
          <PlusIcon class="w-4 h-4 ml-2" />
          إضافة توصية
        </button>
      </div>

      <div 
        v-for="(testimonial, index) in localSettings.testimonials" 
        :key="index"
        class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700"
      >
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              الاسم (عربي) *
            </label>
            <input 
              v-model="testimonial.name_ar"
              type="text"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
              placeholder="أحمد محمد"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              Name (English) *
            </label>
            <input 
              v-model="testimonial.name_en"
              type="text"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
              placeholder="Ahmed Mohammed"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              النص (عربي) *
            </label>
            <textarea 
              v-model="testimonial.text_ar"
              rows="2"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
              placeholder="تجربة رائعة مع الفريق المتخصص..."
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              Text (English) *
            </label>
            <textarea 
              v-model="testimonial.text_en"
              rows="2"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
              placeholder="Amazing experience with the specialized team..."
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              التقييم (1-5) *
            </label>
            <div class="flex gap-1">
              <button 
                v-for="star in 5" 
                :key="star"
                @click="testimonial.rating = star"
                class="text-xl transition-colors"
                :class="star <= (testimonial.rating || 0) ? 'text-yellow-400' : 'text-gray-300'"
              >
                ★
              </button>
              <span class="text-xs text-tertiary mr-2 self-center">
                ({{ testimonial.rating || 0 }}/5)
              </span>
            </div>
          </div>
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              الدور (عربي)
            </label>
            <input 
              v-model="testimonial.role_ar"
              type="text"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
              placeholder="مستفيد"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-tertiary mb-1">
              Role (English)
            </label>
            <input 
              v-model="testimonial.role_en"
              type="text"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
              placeholder="Beneficiary"
            />
          </div>
          <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm text-primary cursor-pointer">
              <input 
                v-model="testimonial.is_active"
                type="checkbox"
                class="rounded border-gray-300 text-brand-500 focus:ring-brand-500"
              />
              مفعل
            </label>
          </div>
        </div>
        <button 
          @click="removeTestimonial(index)"
          class="text-red-500 hover:text-red-600 transition-colors mt-1"
        >
          <TrashIcon class="w-5 h-5" />
        </button>
      </div>

      <!-- معاينة التوصيات -->
      <div v-if="localSettings.testimonials.length > 0" class="mt-4 pt-4 border-t border-primary/10">
        <p class="text-xs text-tertiary mb-3">معاينة التوصيات:</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="(testimonial, index) in localSettings.testimonials" 
            :key="index"
            class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700"
          >
            <div class="flex gap-1 mb-2">
              <span v-for="star in 5" :key="star" class="text-xs" :class="star <= (testimonial.rating || 0) ? 'text-yellow-400' : 'text-gray-300'">★</span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
              "{{ testimonial.text_ar || testimonial.text_en }}"
            </p>
            <div class="mt-2 flex items-center gap-2">
              <div class="w-6 h-6 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold">
                {{ getInitials(testimonial) }}
              </div>
              <div>
                <p class="text-xs font-medium text-primary">{{ testimonial.name_ar || testimonial.name_en }}</p>
                <p class="text-xs text-tertiary">{{ testimonial.role_ar || testimonial.role_en }}</p>
              </div>
            </div>
          </div>
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
  ChartBarIcon,
  PlusIcon,
  TrashIcon,
  ArrowPathIcon,
  EyeIcon,
  ChatBubbleLeftRightIcon
} from '@heroicons/vue/24/outline'
import type { Stat, Testimonial } from '@/types/settings'

// 🔹 Props & Emits
const props = defineProps<{
  saving?: boolean
}>()

const emit = defineEmits(['save', 'reset', 'update'])

const settingsStore = useSettingsStore()

// 🔹 البيانات المحلية
const localSettings = ref({
  stats: [] as Stat[],
  testimonials: [] as Testimonial[],
})

// 🔹 تحميل البيانات من الـ Store
const loadSettings = () => {
  const achievements = settingsStore.settings.achievements
  
  localSettings.value = {
    stats: achievements.stats ? JSON.parse(JSON.stringify(achievements.stats)) : [],
    testimonials: achievements.testimonials ? JSON.parse(JSON.stringify(achievements.testimonials)) : [], // ✅ من achievements
  }
}

// 🔹 دوال الإحصائيات
const addStat = () => {
  localSettings.value.stats.push({
    label_ar: '',
    label_en: '',
    value: '',
  })
}

const removeStat = (index: number) => {
  localSettings.value.stats.splice(index, 1)
}

// 🔹 دوال التوصيات
const addTestimonial = () => {
  localSettings.value.testimonials.push({
    name_ar: '',
    name_en: '',
    text_ar: '',
    text_en: '',
    rating: 5,
    role_ar: '',
    role_en: '',
    is_active: true,
  })
}

const removeTestimonial = (index: number) => {
  localSettings.value.testimonials.splice(index, 1)
}

// 🔹 الحصول على الحروف الأولى
const getInitials = (testimonial: Testimonial) => {
  const name = testimonial.name_ar || testimonial.name_en || ''
  if (!name) return '?'
  const parts = name.trim().split(' ')
  if (parts.length === 1) return parts[0].charAt(0).toUpperCase()
  return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase()
}

// 🔹 مراقبة التغييرات - تأكد من إرسال المصفوفات
watch(localSettings, (newVal) => {
  const stats = Array.isArray(newVal.stats) ? newVal.stats : []
  const testimonials = Array.isArray(newVal.testimonials) ? newVal.testimonials : []
  
  // ✅ إرسال الكل في مجموعة واحدة (achievements)
  emit('update', {
    group: 'achievements',
    data: {
      stats: stats,
      testimonials: testimonials
    }
  })
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

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>