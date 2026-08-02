import { ref, reactive, computed, onMounted } from 'vue'
import { useSettingsStore } from '../stores/settings'

export function useSettings() {
  const settingsStore = useSettingsStore()
  const loading = ref(false)
  const error = ref<string | null>(null)

  // جلب الإعدادات
  const fetchSettings = async () => {
    loading.value = true
    error.value = null
    try {
      await settingsStore.fetchSettings()
    } catch (err: any) {
      error.value = err.message || 'فشل في جلب الإعدادات'
      console.error('Error fetching settings:', err)
    } finally {
      loading.value = false
    }
  }

  // الإعدادات الخاصة بـ About
  const aboutSettings = computed(() => settingsStore.settings.about)
  const visionSettings = computed(() => settingsStore.settings.vision)
  const achievementsSettings = computed(() => settingsStore.settings.achievements)
  const identitySettings = computed(() => settingsStore.settings.identity)
  const contactSettings = computed(() => settingsStore.settings.contact)

  // دوال مساعدة للوصول السريع
  const getValue = (group: string, key: string, locale: 'ar' | 'en' = 'ar') => {
    const settings = settingsStore.settings as any
    const groupData = settings[group]
    if (!groupData) return ''
    
    // إذا كان هناك حقل مخصص للغة
    const keyWithLocale = `${key}_${locale}`
    if (groupData[keyWithLocale] !== undefined) {
      return groupData[keyWithLocale]
    }
    
    // وإلا استخدم القيمة العادية
    return groupData[key] || ''
  }

  // الحصول على المصفوفات (badges, objectives, values, stats)
  const getArray = (group: string, key: string) => {
    const settings = settingsStore.settings as any
    const groupData = settings[group]
    if (!groupData) return []
    
    const data = groupData[key]
    if (Array.isArray(data)) {
      return data
    }
    
    // إذا كانت JSON string
    if (typeof data === 'string') {
      try {
        const parsed = JSON.parse(data)
        return Array.isArray(parsed) ? parsed : []
      } catch {
        return []
      }
    }
    
    return []
  }

  // جلب البيانات عند التحميل
  onMounted(() => {
    if (!settingsStore.settings.about.hero_title_ar) {
      fetchSettings()
    }
  })

  return {
    loading,
    error,
    fetchSettings,
    aboutSettings,
    visionSettings,
    achievementsSettings,
    identitySettings,
    contactSettings,
    getValue,
    getArray,
  }
}