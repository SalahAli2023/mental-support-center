// types/settings.ts

// 🔹 هيكل البيانات الأساسي
export interface Setting {
  id: number
  group: string
  key: string
  value: any
  value_ar: string | null
  value_en: string | null
  type: string
  is_active: boolean
  created_at?: string
  updated_at?: string
}

// 🔹 مجموعة الهوية (Identity)
export interface IdentitySettings {
  site_name_ar: string
  site_name_en: string
  site_tagline_ar: string
  site_tagline_en: string
  site_logo: string | null
  site_favicon: string | null
}

// 🔹 مجموعة عن المركز (About)
export interface AboutSettings {
  // Hero Section
  hero_title_ar: string
  hero_title_en: string
  hero_highlight_ar: string
  hero_highlight_en: string
  hero_subtitle_ar: string
  hero_subtitle_en: string
  
  // Overview Paragraphs
  overview_paragraph_1_ar: string
  overview_paragraph_1_en: string
  overview_paragraph_2_ar: string
  overview_paragraph_2_en: string
  overview_paragraph_3_ar: string
  overview_paragraph_3_en: string
  overview_paragraph_4_ar: string
  overview_paragraph_4_en: string
  
  // Badges
  badges: AboutBadge[]
  
  // Objectives
  objectives: Objective[]

  overview_image: string | null
}

export interface AboutBadge {
  id?: string
  label_ar: string
  label_en: string
}

export interface Objective {
  id?: string
  text_ar: string
  text_en: string
}

// 🔹 مجموعة الرؤية والرسالة
export interface VisionMissionSettings {
  vision_ar: string
  vision_en: string
  mission_ar: string
  mission_en: string
  values: Value[]
}

export interface Value {
  id?: string
  title_ar: string
  title_en: string
}

export interface Stat {
  id?: string
  label_ar: string
  label_en: string
  value: string
  icon?: string
}

export interface Testimonial {
  id?: string
  name_ar: string
  name_en: string
  text_ar: string
  text_en: string
  rating:  number // 1-5
  role_ar: string
  role_en: string
  avatar?: string
  is_active?: boolean
}

// 🔹 مجموعة الإنجازات
export interface AchievementSettings {
  stats: Stat[]
  testimonials: Testimonial[]
}
// 🔹 مجموعة التواصل والفوتر
export interface ContactSettings {
  phone: string
  email: string
  address_ar: string
  address_en: string
  facebook: string
  twitter: string
  instagram: string
  youtube: string
  linkedin: string  // ✅ إضافة لينكد ان
  footer_logo: string | null
  footer_description_ar: string
  footer_description_en: string
  footer_copyright_ar: string
  footer_copyright_en: string
}

// 🔹 جميع الإعدادات
export interface AllSettings {
  identity: IdentitySettings
  about: AboutSettings
  vision: VisionMissionSettings
  achievements: AchievementSettings
  contact: ContactSettings
}

// 🔹 Payload للحفظ
export interface SettingsPayload {
  group: string
  settings: Record<string, any>
}

