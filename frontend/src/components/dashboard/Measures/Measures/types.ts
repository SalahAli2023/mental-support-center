// types.ts
export interface ScaleCategory {
  id: string
  name_ar: string
  name_en: string
  description_ar?: string
  description_en?: string
  is_active: boolean
  scales_count?: number
  created_at?: string
  updated_at?: string
}

export interface Scale {
  id: string
  name_ar?: string
  name_en?: string
  description_ar?: string
  description_en?: string
  category_id?: string
  image_url?: string
  max_score?: number
  is_active?: boolean
  questions_count?: number
  interpretations_count?: number
  created_at?: string
  updated_at?: string
  category?: ScaleCategory
  questions?: Question[]
  interpretations?: Interpretation[]
}

export interface Question {
  id?: string
  scale_id?: string
  question_text_ar?: string
  question_text_en?: string
  question_order?: number
  options?: Option[]
  created_at?: string
  updated_at?: string
}

export interface Option {
  id?: string
  question_id?: string
  option_text_ar?: string
  option_text_en?: string
  score_value?: number
  option_order?: number
  created_at?: string
  updated_at?: string
}

export interface Interpretation {
  id?: string
  scale_id?: string
  min_score?: number
  max_score?: number
  interpretation_label_ar?: string
  interpretation_label_en?: string
  description_ar?: string
  description_en?: string
  color?: string
  created_at?: string
  updated_at?: string
}

export interface CreateScaleData {
  category_id: string
  name_ar: string
  name_en: string
  description_ar?: string
  description_en?: string
  image_url?: string
  max_score?: number
  is_active?: boolean
  questions?: Question[]
  interpretations?: Interpretation[]
}

export interface ScaleForm {
  name_ar: string
  name_en: string
  description_ar: string
  description_en: string
  category_id: string
  image_url: string
  max_score: number
  is_active: boolean
  questions: Question[]
  interpretations: Interpretation[]
}