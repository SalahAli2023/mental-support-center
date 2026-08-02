// types.ts
export interface ScaleCategory {
  id: string;
  name_ar: string;
  name_en: string;
  description_ar?: string;
  description_en?: string;
  color?: string;
  is_active: boolean;
  scales_count?: number;
  created_at?: string;
  updated_at?: string;
}

export interface CategoryForm {
  name_ar: string;
  name_en: string;
  description_ar: string;
  description_en: string;
  color: string;
  is_active: boolean;
}