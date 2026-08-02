<template>
  <div class="space-y-6 p-2 sm:p-4">
    <!-- الهيدر المحسّن -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-secondary p-6 rounded-2xl border border-primary shadow-sm">
      <div>
        <h1 class="text-2xl font-bold text-primary flex items-center gap-2">
          <Cog6ToothIcon class="h-7 w-7 text-brand-500" />
          <span>إعدادات النظام والموقع</span>
        </h1>
        <p class="text-sm text-tertiary mt-1">
          إدارة إعدادات الهوية وساعات العمل وعن المركز باللغتين العربية والإلكترونية مباشرة في قاعدة البيانات.
        </p>
      </div>
      
      <div class="flex items-center gap-3">
        <!-- زر إعادة للافتراضي -->
        <button 
          @click="handleResetAll"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-primary text-sm font-medium text-primary hover:bg-tertiary transition duration-200"
        >
          <ArrowPathIcon class="h-4 w-4 text-tertiary" />
          <span>إعادة للافتراضي</span>
        </button>
        
        <!-- زر حفظ التغييرات -->
        <button 
          @click="handleSaveAll"
          :disabled="saving"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-medium text-sm shadow-md hover:shadow-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <CheckIcon class="h-4 w-4" />
          <span>{{ saving ? 'جاري الحفظ...' : 'حفظ التغييرات' }}</span>
        </button>
      </div>
    </div>

    <!-- حالة التحميل -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-500"></div>
    </div>

    <!-- رسالة الخطأ -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex items-center gap-2 text-red-700">
        <ExclamationCircleIcon class="w-5 h-5" />
        <span>{{ error }}</span>
      </div>
      <button 
        @click="fetchSettings"
        class="mt-2 text-sm text-red-600 hover:text-red-800 underline"
      >
        إعادة المحاولة
      </button>
    </div>

    <!-- محتوى الإعدادات -->
    <div v-else>
      <!-- Tabs المحسّنة -->
      <div class="flex border-b border-primary overflow-x-auto gap-2 scrollbar-none">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          @click="activeTab = tab.key"
          class="flex items-center gap-2.5 px-5 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition-all duration-200 rounded-t-xl"
          :class="activeTab === tab.key 
            ? 'border-brand-500 text-brand-500 bg-brand-500/5' 
            : 'border-transparent text-tertiary hover:text-primary hover:bg-tertiary/40'"
        >
          <component :is="tab.icon" class="h-5 w-5" />
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- محتوى كل Tab -->
      <div class="mt-6">
        <!-- Tab: الهوية -->
        <IdentityTab
          v-if="activeTab === 'identity'"
          ref="identityTabRef"
          :saving="saving"
          @update="handleUpdateLegacy('identity', $event)"
          @save="handleSave('identity')"
          @reset="handleReset('identity')"
        />

        <!-- Tab: عن المركز -->
        <AboutTab
          v-else-if="activeTab === 'about'"
          ref="aboutTabRef"
          :saving="saving"
          @update="handleUpdateLegacy('about', $event)"
          @save="handleSave('about')"
          @reset="handleReset('about')"
        />

        <!-- Tab: الرؤية والرسالة -->
        <VisionTab
          v-else-if="activeTab === 'vision'"
          ref="visionTabRef"
          :saving="saving"
          @update="handleUpdateLegacy('vision', $event)"
          @save="handleSave('vision')"
          @reset="handleReset('vision')"
        />

        <!-- Tab: الإنجازات -->
        <AchievementsTab
          v-else-if="activeTab === 'achievements'"
          ref="achievementsTabRef"
          :saving="saving"
          @update="handleUpdate"
          @save="handleSave('achievements')"
          @reset="handleReset('achievements')"
        />

        <!-- Tab: التواصل -->
        <ContactTab
          v-else-if="activeTab === 'contact'"
          ref="contactTabRef"
          :saving="saving"
          @update="handleUpdateLegacy('contact', $event)"
          @save="handleSave('contact')"
          @reset="handleReset('contact')"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive, nextTick } from 'vue'
import { useSettingsStore } from '@/stores/settings'
import IdentityTab from './Tabs/IdentityTab.vue'
import AboutTab from './Tabs/AboutTab.vue'
import VisionTab from './Tabs/VisionTab.vue'
import AchievementsTab from './Tabs/AchievementsTab.vue'
import ContactTab from './Tabs/ContactTab.vue'
import { 
  Cog6ToothIcon,
  ExclamationCircleIcon,
  ArrowPathIcon,
  CheckIcon,
  IdentificationIcon,
  UserGroupIcon,
  EyeIcon,
  ChartBarIcon,
  PhoneIcon,
  UserIcon,
  BuildingOffice2Icon
} from '@heroicons/vue/24/outline'
import { useToast } from '@/composables/useToast' // ✅ استيراد الـ Toast

const toast = useToast()

// 🔹 Store
const settingsStore = useSettingsStore()
const loading = ref(false)
const saving = ref(false)
const error = ref<string | null>(null)
const activeTab = ref('identity')

// 🔹 Refs for Tabs
const identityTabRef = ref<InstanceType<typeof IdentityTab> | null>(null)
const aboutTabRef = ref<InstanceType<typeof AboutTab> | null>(null)
const visionTabRef = ref<InstanceType<typeof VisionTab> | null>(null)
const achievementsTabRef = ref<InstanceType<typeof AchievementsTab> | null>(null)
const contactTabRef = ref<InstanceType<typeof ContactTab> | null>(null)

// 🔹 Tabs
const tabs = [
  { key: 'identity', label: 'الشعار والهوية', icon: IdentificationIcon },
  { key: 'about', label: 'عن المركز', icon: UserGroupIcon },
  { key: 'vision', label: 'الرؤية والرسالة والقيم', icon: EyeIcon },
  { key: 'achievements', label: 'الإنجازات والأرقام', icon: ChartBarIcon },
  { key: 'contact', label: 'التواصل والفوتر', icon: PhoneIcon },
]

// 🔹 بيانات التحديث المؤقتة لكل مجموعة
const pendingUpdates = reactive<Record<string, Record<string, any>>>({})

// 🔹 جلب الإعدادات
const fetchSettings = async () => {
  loading.value = true
  error.value = null
  
  try {
    await settingsStore.fetchSettings()
    reloadAllTabs()
  } catch (err: any) {
    error.value = err.message || 'فشل في تحميل الإعدادات'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

// 🔹 إعادة تحميل جميع التابات
const reloadAllTabs = () => {
  if (identityTabRef.value) identityTabRef.value.loadSettings()
  if (aboutTabRef.value) aboutTabRef.value.loadSettings()
  if (visionTabRef.value) visionTabRef.value.loadSettings()
  if (achievementsTabRef.value) achievementsTabRef.value.loadSettings()
  if (contactTabRef.value) contactTabRef.value.loadSettings()
}

// 🔹 معالجة التحديث من جميع التابات
const handleUpdate = (payload: any) => {
  // إذا كان payload يحتوي على group و data (من AchievementsTab)
  if (payload && payload.group && payload.data) {
    pendingUpdates[payload.group] = { 
      ...pendingUpdates[payload.group], 
      ...payload.data 
    }
  } 
  // إذا كان payload هو group (من التابات الأخرى)
  else if (typeof payload === 'string') {
    // هذه حالة خاصة، لكننا نستخدم handleUpdateLegacy لها
    // لا تفعل شيء هنا
  }
}

// 🔹 معالجة التحديث للتابات القديمة (ترسل group, data)
const handleUpdateLegacy = (group: string, data: Record<string, any>) => {
  pendingUpdates[group] = { ...pendingUpdates[group], ...data }
}


// 🔹 حفظ مجموعة محددة
const handleSave = async (group: string) => {
  const data = pendingUpdates[group]
  if (!data || Object.keys(data).length === 0) {
    toast.warning('لا توجد تغييرات لحفظها')
    return
  }
  
  saving.value = true
  error.value = null
  
  try {
    await settingsStore.saveSettings(group, data)
    pendingUpdates[group] = {}
    await fetchSettings()
    toast.success('تم حفظ الإعدادات بنجاح')
  } catch (err: any) {
    error.value = err.message || 'فشل في حفظ الإعدادات'
    toast.error(error.value)
  } finally {
    saving.value = false
  }
}

// 🔹 حفظ جميع التغييرات
const handleSaveAll = async () => {
  const allUpdates: Record<string, Record<string, any>> = {}
  let hasChanges = false
  
  for (const [group, data] of Object.entries(pendingUpdates)) {
    if (data && Object.keys(data).length > 0) {
      allUpdates[group] = data
      hasChanges = true
    }
  }
  
  if (!hasChanges) {
    toast.warning('لا توجد تغييرات لحفظها')
    return
  }
  
  saving.value = true
  error.value = null
  
  try {
    for (const [group, data] of Object.entries(allUpdates)) {
      await settingsStore.saveSettings(group, data)
      pendingUpdates[group] = {}
    }
    await fetchSettings()
    toast.success('تم حفظ جميع التغييرات بنجاح')
  } catch (err: any) {
    error.value = err.message || 'فشل في حفظ بعض الإعدادات'
    toast.error(error.value)
  } finally {
    saving.value = false
  }
}

// 🔹 إعادة تعيين مجموعة محددة
const handleReset = async (group: string) => {
    if (!confirm('هل أنت متأكد من إعادة تعيين الإعدادات إلى القيم الافتراضية؟')) {
        return
    }
    
    try {
        await settingsStore.resetSettings(group)
        reloadAllTabs()
        pendingUpdates[group] = {}
        toast.success('تم إعادة تعيين جميع الإعدادات بنجاح')
        // alert('تم إعادة تعيين الإعدادات بنجاح')
    } catch (err: any) {
        toast.error(err.message || 'فشل في إعادة تعيين الإعدادات')
        // alert(err.message || 'فشل في إعادة تعيين الإعدادات')
    }
}

// 🔹 إعادة تعيين جميع الإعدادات
const handleResetAll = async () => {
  if (!confirm('هل أنت متأكد من إعادة تعيين جميع الإعدادات إلى القيم الافتراضية؟')) {
    return
  }
  
  const groups = ['identity', 'about', 'vision', 'achievements', 'contact']
  
  try {
    for (const group of groups) {
      await settingsStore.resetSettings(group)
      pendingUpdates[group] = {}
    }
    await fetchSettings()
    toast.success('تم إعادة تعيين جميع الإعدادات بنجاح')
  } catch (err: any) {
    toast.error(err.message || 'فشل في إعادة تعيين الإعدادات')
  }
}

// 🔹 تحميل البيانات عند التركيب
onMounted(() => {
    fetchSettings()
})
</script>