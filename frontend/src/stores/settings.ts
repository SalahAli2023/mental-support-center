// stores/settings.ts
import { defineStore } from 'pinia'
import { ref, reactive } from 'vue'
import api from '../utils/api'
import type { 
  AllSettings, 
  IdentitySettings, 
  AboutSettings, 
  VisionMissionSettings, 
  AchievementSettings, 
  ContactSettings,
  Setting,
  AboutBadge,
  Objective,
  Value,
  Stat,
  SettingsPayload
} from '../types/settings'

export const useSettingsStore = defineStore('settings', () => {
  // 🔹 State
  const loading = ref(false)
  const saving = ref(false)
  const error = ref<string | null>(null)
  
  // 🔹 جميع الإعدادات
  const settings = reactive<AllSettings>({
    identity: {
      site_name_ar: '',
      site_name_en: '',
      site_tagline_ar: '',
      site_tagline_en: '',
      site_logo: null,
      site_favicon: null,
    },
    about: {
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
      badges: [],
      objectives: [],
      overview_image:null,
    },
    vision: {
      vision_ar: '',
      vision_en: '',
      mission_ar: '',
      mission_en: '',
      values: [],
    },
    achievements: {
      stats: [],
      testimonials: [], 
    },
    contact: {
      phone: '',
      email: '',
      address_ar: '',
      address_en: '',
      facebook: '',
      twitter: '',
      instagram: '',
      youtube: '',
      footer_description_ar: '',
      footer_description_en: '',
      footer_copyright_ar: '',
      footer_copyright_en: '',
    }
  })

  // 🔹 جلب جميع الإعدادات
  const fetchSettings = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get('/settings')
      
      if (response.data.success) {
        const data = response.data.data
        
        // تحديث identity
        if (data.identity) {
          Object.assign(settings.identity, data.identity)
        }
        
        // تحديث about
        if (data.about) {
          Object.assign(settings.about, data.about)
        }
        
        // تحديث vision
        if (data.vision) {
          Object.assign(settings.vision, data.vision)
        }
        
        // تحديث achievements
        if (data.achievements) {
          Object.assign(settings.achievements, data.achievements)
        }
        
        // تحديث contact
        if (data.contact) {
          Object.assign(settings.contact, data.contact)
        }
        
        return response.data.data
      } else {
        throw new Error(response.data.message || 'فشل في جلب الإعدادات')
      }
    } catch (err: any) {
      error.value = err.message || 'حدث خطأ أثناء جلب الإعدادات'
      console.error('Error fetching settings:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  // 🔹 حفظ مجموعة إعدادات
  const saveSettings = async (group: string, data: Record<string, any>) => {
    saving.value = true
    error.value = null
    
    try {
      const payload: SettingsPayload = {
        group,
        settings: data
      }
      
      const response = await api.post('/settings', payload)
      
      if (response.data.success) {
        // تحديث المجموعة المحلية
        if (group === 'identity') {
          Object.assign(settings.identity, data)
        } else if (group === 'about') {
          Object.assign(settings.about, data)
        } else if (group === 'vision') {
          Object.assign(settings.vision, data)
        } else if (group === 'achievements') {
          Object.assign(settings.achievements, data)
        } else if (group === 'contact') {
          Object.assign(settings.contact, data)
        }
        
        return response.data.data
      } else {
        throw new Error(response.data.message || 'فشل في حفظ الإعدادات')
      }
    } catch (err: any) {
      error.value = err.message || 'حدث خطأ أثناء حفظ الإعدادات'
      console.error('Error saving settings:', err)
      throw err
    } finally {
      saving.value = false
    }
  }

  // 🔹 إعادة تعيين الإعدادات للافتراضي
  const resetSettings = async (group: string) => {
    try {
      const response = await api.post(`/settings/${group}/reset`)
      
      if (response.data.success) {
        // إعادة تحميل الإعدادات
        await fetchSettings()
        return response.data.data
      } else {
        throw new Error(response.data.message || 'فشل في إعادة تعيين الإعدادات')
      }
    } catch (err: any) {
      error.value = err.message || 'حدث خطأ أثناء إعادة تعيين الإعدادات'
      console.error('Error resetting settings:', err)
      throw err
    }
  }

  // 🔹 رفع صورة
  const uploadImage = async (file: File, group: string, key: string) => {
    try {
      const formData = new FormData()
      formData.append('image', file)
      formData.append('group', group)
      formData.append('key', key)
      
      const response = await api.post('/settings/upload-image', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      if (response.data.success) {
        return response.data.data.url
      } else {
        throw new Error(response.data.message || 'فشل في رفع الصورة')
      }
    } catch (err: any) {
      console.error('Error uploading image:', err)
      throw err
    }
  }

  return {
    // State
    settings,
    loading,
    saving,
    error,
    
    // Actions
    fetchSettings,
    saveSettings,
    resetSettings,
    uploadImage,
  }
})