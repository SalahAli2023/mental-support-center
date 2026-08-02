// services/scaleService.ts
import type { 
  Scale, 
  Category, 
  ScaleForm, 
  Question, 
  Interpretation,
  ScaleResponse,
  ScaleListResponse,
  CategoryListResponse,
  ScaleStatistics
} from '../types';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

class ScaleService {
  private async request(endpoint: string, options: RequestInit = {}) {
    const token = localStorage.getItem('auth_token');
    
    const config: RequestInit = {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(token && { 'Authorization': `Bearer ${token}` }),
        ...options.headers,
      },
      ...options,
    };

    try {
      const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
      
      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
      }
      
      return response.json();
    } catch (error) {
      if (error instanceof Error) {
        throw error;
      }
      throw new Error('Network error occurred');
    }
  }

  // ==================== Scale Operations ====================

  /**
   * الحصول على جميع المقاييس
   */
  async getAllScales(): Promise<Scale[]> {
    const data = await this.request('/scales');
    return this.transformScales(data.data || data);
  }

  /**
   * الحصول على مقياس محدد
   */
  async getScale(id: string): Promise<Scale> {
    const data = await this.request(`/scales/${id}`);
    return this.transformScale(data.data || data);
  }

  /**
   * إنشاء مقياس جديد
   */
  async createScale(scaleData: ScaleForm): Promise<Scale> {
    const data = await this.request('/scales', {
      method: 'POST',
      body: JSON.stringify(this.prepareScaleData(scaleData)),
    });
    return this.transformScale(data.data || data);
  }

  /**
   * تحديث مقياس موجود
   */
  async updateScale(id: string, scaleData: Partial<ScaleForm>): Promise<Scale> {
    const data = await this.request(`/scales/${id}`, {
      method: 'PUT',
      body: JSON.stringify(this.prepareScaleData(scaleData)),
    });
    return this.transformScale(data.data || data);
  }

  /**
   * حذف مقياس
   */
  async deleteScale(id: string): Promise<void> {
    await this.request(`/scales/${id}`, {
      method: 'DELETE',
    });
  }

  /**
   * تبديل حالة المقياس (نشط/غير نشط)
   */
  async toggleScaleStatus(id: string): Promise<Scale> {
    const data = await this.request(`/scales/${id}/toggle-status`, {
      method: 'PATCH',
    });
    return this.transformScale(data.data || data);
  }

  // ==================== Category Operations ====================

  /**
   * الحصول على جميع التصنيفات
   */
  async getAllCategories(): Promise<Category[]> {
    const data = await this.request('/categories');
    return data.data || data;
  }

  /**
   * الحصول على تصنيف محدد
   */
  async getCategory(id: string): Promise<Category> {
    const data = await this.request(`/categories/${id}`);
    return data.data || data;
  }

  /**
   * الحصول على مقاييس تصنيف معين
   */
  async getScalesByCategory(categoryId: string): Promise<Scale[]> {
    const data = await this.request(`/categories/${categoryId}/scales`);
    return this.transformScales(data.data || data);
  }

  // ==================== Question Operations ====================

  /**
   * الحصول على أسئلة مقياس معين
   */
  async getScaleQuestions(scaleId: string): Promise<Question[]> {
    const data = await this.request(`/scales/${scaleId}/questions`);
    return this.transformQuestions(data.data || data);
  }

  /**
   * إضافة سؤال لمقياس
   */
  async addQuestion(scaleId: string, questionData: any): Promise<Question> {
    const data = await this.request(`/scales/${scaleId}/questions`, {
      method: 'POST',
      body: JSON.stringify(questionData),
    });
    return data.data || data;
  }

  // ==================== Search & Filter Operations ====================

  /**
   * بحث في المقاييس
   */
  async searchScales(query: string, categoryId?: string): Promise<Scale[]> {
    const params = new URLSearchParams();
    params.append('search', query);
    if (categoryId) {
      params.append('category_id', categoryId);
    }

    const data = await this.request(`/scales/search?${params.toString()}`);
    return this.transformScales(data.data || data);
  }

  /**
   * الحصول على المقاييس النشطة فقط
   */
  async getActiveScales(): Promise<Scale[]> {
    const data = await this.request('/scales?is_active=true');
    return this.transformScales(data.data || data);
  }

  /**
   * الحصول على المقاييس مع التصفية
   */
  async getScalesWithFilters(filters: {
    category_id?: string;
    is_active?: boolean;
    search?: string;
  }): Promise<Scale[]> {
    const params = new URLSearchParams();
    
    if (filters.category_id) params.append('category_id', filters.category_id);
    if (filters.is_active !== undefined) params.append('is_active', filters.is_active.toString());
    if (filters.search) params.append('search', filters.search);

    const queryString = params.toString();
    const endpoint = queryString ? `/scales?${queryString}` : '/scales';
    
    const data = await this.request(endpoint);
    return this.transformScales(data.data || data);
  }

  // ==================== Statistics & Analytics ====================

  /**
   * الحصول على إحصائيات المقياس
   */
  async getScaleStatistics(scaleId: string): Promise<ScaleStatistics> {
    const data = await this.request(`/scales/${scaleId}/statistics`);
    return data.data || data;
  }

  /**
   * الحصول على إحصائيات عامة
   */
  async getGeneralStatistics(): Promise<{
    total_scales: number;
    active_scales: number;
    total_categories: number;
    total_assessments: number;
  }> {
    const data = await this.request('/statistics/scales');
    return data.data || data;
  }

  // ==================== Bulk Operations ====================

  /**
   * تحديث مجموعة من المقاييس
   */
  async bulkUpdateScales(scaleIds: string[], updates: Partial<ScaleForm>): Promise<Scale[]> {
    const data = await this.request('/scales/bulk-update', {
      method: 'PUT',
      body: JSON.stringify({
        scale_ids: scaleIds,
        updates: this.prepareScaleData(updates)
      }),
    });
    return this.transformScales(data.data || data);
  }

  /**
   * حذف مجموعة من المقاييس
   */
  async bulkDeleteScales(scaleIds: string[]): Promise<void> {
    await this.request('/scales/bulk-delete', {
      method: 'POST',
      body: JSON.stringify({ scale_ids: scaleIds }),
    });
  }

  // ==================== Import/Export Operations ====================

  /**
   * تصدير بيانات المقياس
   */
  async exportScale(scaleId: string): Promise<Blob> {
    const token = localStorage.getItem('auth_token');
    const response = await fetch(`${API_BASE_URL}/scales/${scaleId}/export`, {
      headers: {
        ...(token && { 'Authorization': `Bearer ${token}` }),
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.blob();
  }

  /**
   * استيراد مقياس
   */
  async importScale(scaleData: any): Promise<Scale> {
    const data = await this.request('/scales/import', {
      method: 'POST',
      body: JSON.stringify(scaleData),
    });
    return this.transformScale(data.data || data);
  }

  // ==================== Image Upload ====================

  /**
   * رفع صورة
   */
  async uploadImage(file: File): Promise<{ url: string }> {
    const formData = new FormData();
    formData.append('image', file);

    const token = localStorage.getItem('auth_token');
    const response = await fetch(`${API_BASE_URL}/upload`, {
      method: 'POST',
      headers: {
        ...(token && { 'Authorization': `Bearer ${token}` }),
      },
      body: formData,
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
  }

  // ==================== Data Transformation Methods ====================

  /**
   * تحويل بيانات المقاييس
   */
  private transformScales(scales: any[]): Scale[] {
    return scales.map(scale => this.transformScale(scale));
  }

  /**
   * تحويل بيانات المقياس
   */
  private transformScale(scale: any): Scale {
    return {
      id: scale.id,
      name_ar: scale.name_ar,
      name_en: scale.name_en,
      description_ar: scale.description_ar,
      description_en: scale.description_en,
      category_id: scale.category_id,
      category: scale.category,
      image_url: scale.image_url,
      max_score: scale.max_score,
      is_active: scale.is_active,
      created_at: scale.created_at,
      updated_at: scale.updated_at,
      questions: scale.questions ? this.transformQuestions(scale.questions) : [],
      interpretations: scale.interpretations ? this.transformInterpretations(scale.interpretations) : [],
      questions_count: scale.questions_count,
      assessments_count: scale.assessments_count
    };
  }

  /**
   * تحويل بيانات الأسئلة
   */
  private transformQuestions(questions: any[]): Question[] {
    return questions.map(q => ({
      id: q.id,
      scale_id: q.scale_id,
      question_text_ar: q.question_text_ar,
      question_text_en: q.question_text_en,
      question_order: q.question_order,
      options: q.options ? this.transformOptions(q.options) : [],
      created_at: q.created_at,
      updated_at: q.updated_at
    }));
  }

  /**
   * تحويل بيانات الخيارات
   */
  private transformOptions(options: any[]): any[] {
    return options.map(opt => ({
      id: opt.id,
      question_id: opt.question_id,
      option_text_ar: opt.option_text_ar,
      option_text_en: opt.option_text_en,
      score_value: opt.score_value,
      option_order: opt.option_order,
      created_at: opt.created_at,
      updated_at: opt.updated_at
    }));
  }

  /**
   * تحويل بيانات التفسيرات
   */
  private transformInterpretations(interpretations: any[]): Interpretation[] {
    return interpretations.map(int => ({
      id: int.id,
      scale_id: int.scale_id,
      min_score: int.min_score,
      max_score: int.max_score,
      interpretation_label_ar: int.interpretation_label_ar,
      interpretation_label_en: int.interpretation_label_en,
      description_ar: int.description_ar,
      description_en: int.description_en,
      color: int.color,
      created_at: int.created_at,
      updated_at: int.updated_at
    }));
  }

  /**
   * تحضير بيانات المقياس للإرسال
   */
  private prepareScaleData(formData: any): any {
    const scaleData: any = {
      name_ar: formData.name_ar,
      name_en: formData.name_en,
      description_ar: formData.description_ar,
      description_en: formData.description_en,
      category_id: formData.category_id,
      max_score: formData.max_score,
      is_active: formData.is_active
    };

    // إضافة image_url إذا موجود
    if (formData.image_url) {
      scaleData.image_url = formData.image_url;
    }

    // إضافة الأسئلة إذا موجودة
    if (formData.questions && formData.questions.length > 0) {
      scaleData.questions = formData.questions.map((q: any) => ({
        question_text_ar: q.question_text_ar,
        question_text_en: q.question_text_en,
        question_order: q.question_order,
        options: q.options.map((opt: any) => ({
          option_text_ar: opt.option_text_ar,
          option_text_en: opt.option_text_en,
          score_value: opt.score_value,
          option_order: opt.option_order
        }))
      }));
    }

    // إضافة التفسيرات إذا موجودة
    if (formData.interpretations && formData.interpretations.length > 0) {
      scaleData.interpretations = formData.interpretations.map((int: any) => ({
        min_score: int.min_score,
        max_score: int.max_score,
        interpretation_label_ar: int.interpretation_label_ar,
        interpretation_label_en: int.interpretation_label_en,
        description_ar: int.description_ar,
        description_en: int.description_en,
        color: int.color
      }));
    }

    return scaleData;
  }

  /**
   * إنشاء مقياس مكرر
   */
  async duplicateScale(scaleId: string): Promise<Scale> {
    const data = await this.request(`/scales/${scaleId}/duplicate`, {
      method: 'POST',
    });
    return this.transformScale(data.data || data);
  }

  /**
   * التحقق من صحة بيانات المقياس
   */
  validateScaleData(scaleData: ScaleForm): { isValid: boolean; errors: string[] } {
    const errors: string[] = [];

    if (!scaleData.name_ar.trim()) {
      errors.push('اسم المقياس بالعربية مطلوب');
    }

    if (!scaleData.name_en.trim()) {
      errors.push('اسم المقياس بالإنجليزية مطلوب');
    }

    if (!scaleData.category_id) {
      errors.push('التصنيف مطلوب');
    }

    if (scaleData.max_score <= 0) {
      errors.push('أقصى درجة يجب أن تكون أكبر من الصفر');
    }

    if (scaleData.questions.length === 0) {
      errors.push('يجب إضافة سؤال واحد على الأقل');
    }

    // التحقق من صحة الأسئلة
    scaleData.questions.forEach((question, index) => {
      if (!question.question_text_ar.trim() || !question.question_text_en.trim()) {
        errors.push(`السؤال ${index + 1} يجب أن يحتوي على نص بالعربية والإنجليزية`);
      }

      if (question.options.length < 2) {
        errors.push(`السؤال ${index + 1} يجب أن يحتوي على خيارين على الأقل`);
      }

      question.options.forEach((option, optIndex) => {
        if (!option.option_text_ar.trim() || !option.option_text_en.trim()) {
          errors.push(`الخيار ${optIndex + 1} في السؤال ${index + 1} يجب أن يحتوي على نص بالعربية والإنجليزية`);
        }
      });
    });

    // التحقق من صحة التفسيرات
    scaleData.interpretations.forEach((interpretation, index) => {
      if (interpretation.min_score > interpretation.max_score) {
        errors.push(`التفسير ${index + 1}:
 الدرجة الدنيا يجب أن تكون أقل من أو تساوي الدرجة القصوى`);
      }
    });

    return {
      isValid: errors.length === 0,
      errors
    };
  }
}

// إنشاء نسخة واحدة من الخدمة وتصديرها
export const scaleService = new ScaleService();