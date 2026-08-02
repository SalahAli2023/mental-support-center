export interface LibraryCategory {
  id: number
  key: string
  name_ar: string
  name_en: string
  color: string
}

export interface LibraryItem {
  id: number
  title_ar: string
  title_en: string
  description_ar?: string
  description_en?: string
  author_ar?: string
  author_en?: string
  type: 'book' | 'research' | 'guide' | 'article'
  category_id: number
  cover_image?: string
  file_path?: string
  file_size?: number
  pages?: number
  publish_date?: string
  downloads: number
  views: number
  rating: number
  rating_count: number
  is_new: boolean
  is_published: boolean
  created_at: string
  updated_at: string
  category?: LibraryCategory
}

export interface LibraryStats {
  total_items: number
  total_downloads: number
  new_this_week: number
  average_rating: number
}