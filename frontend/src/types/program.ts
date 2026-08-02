export interface Program {
  id: string;
  name_ar: string;
  name_en: string;
  description_ar: string;
  description_en: string;
  start_date: string;
  end_date: string;
  image_url?: string;
  color?: string;
  is_active: boolean;
  sessions_count: number;
  completion_rate?: number;
  created_at?: string;
  updated_at?: string;
}

export interface Session {
  id: string;
  program_id: string;
  session_order: number;
  title_ar: string;
  title_en: string;
  description_ar: string;
  description_en: string;
  scheduled_date: string;
  status: 'pending' | 'completed' | 'in_progress' | 'scheduled' | 'cancelled';
  activities_count: number;
  duration?: number;
  image_url?: string;
  is_active: boolean;
  created_at?: string;
  updated_at?: string;
}

export interface Activity {
  id: string;
  session_id: string;
  activity_order: number;
  title_ar: string;
  title_en: string;
  instructions_ar: string;
  instructions_en: string;
  duration?: number;
  materials?: string;
  activity_type: 'text' | 'audio' | 'video' | 'file' | 'quiz';
  media_url?: string;
  is_active: boolean;
  completion_rate?: number;
  created_at?: string;
  updated_at?: string;
}

export interface ProgramFilters {
  search?: string;
  is_active?: boolean;
  scale_id?: string;
  user_id?: string;
  start_date_from?: string;
  start_date_to?: string;
  sort_field?: string;
  sort_direction?: 'asc' | 'desc';
  page?: number;
  per_page?: number;
}

export interface SessionFilters {
  search?: string;
  is_active?: boolean;
  program_id?: string;
  status?: string;
  date_from?: string;
  date_to?: string;
  page?: number;
  per_page?: number;
}

export interface ActivityFilters {
  search?: string;
  is_active?: boolean;
  session_id?: string;
  activity_type?: string;
  page?: number;
  per_page?: number;
}