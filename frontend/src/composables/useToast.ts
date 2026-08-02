import { ref } from 'vue'

export interface Toast {
      id: string
      message: string
      type: 'success' | 'error' | 'warning' | 'info'
      duration?: number
}

const toasts = ref<Toast[]>([])

export const useToast = () => {
      const addToast = (message: string, type: Toast['type'] = 'info', duration: number = 5000) => {
            const id = Date.now().toString() + Math.random().toString(36).substr(2, 9)
            const toast: Toast = {
                  id,
                  message,
                  type,
                  duration
            }

            toasts.value.push(toast)

            // إزالة تلقائية بعد المدة المحددة
            setTimeout(() => {
                  removeToast(id)
            }, duration)

            return id
      }

      const removeToast = (id: string) => {
            const index = toasts.value.findIndex(t => t.id === id)
            if (index > -1) {
                  toasts.value.splice(index, 1)
            }
      }

      const success = (message: string, duration?: number) => {
            return addToast(message, 'success', duration || 4000)
      }

      const error = (message: string, duration?: number) => {
            // رسائل الأخطاء تبقى لفترة أطول
            return addToast(message, 'error', duration || 6000)
      }

      const warning = (message: string, duration?: number) => {
            return addToast(message, 'warning', duration || 5000)
      }

      const info = (message: string, duration?: number) => {
            return addToast(message, 'info', duration || 4000)
      }

      // دالة لمعالجة أخطاء API وتحويلها إلى رسائل واضحة
      const handleApiError = (err: any, defaultMessage: string = 'حدث خطأ غير متوقع') => {
            let errorMessage = defaultMessage

            if (err.response?.data) {
                  const data = err.response.data

                  // أخطاء التحقق من Laravel (validation errors)
                  if (data.errors && typeof data.errors === 'object') {
                        const errors = Object.entries(data.errors)
                              .map(([field, messages]: [string, any]) => {
                                    // تحويل أسماء الحقول إلى أسماء واضحة بالعربية
                                    const fieldNames: Record<string, string> = {
                                          'category_id': 'التصنيف',
                                          'name_ar': 'الاسم بالعربية',
                                          'name_en': 'الاسم بالإنجليزية',
                                          'description_ar': 'الوصف بالعربية',
                                          'description_en': 'الوصف بالإنجليزية',
                                          'image_url': 'رابط الصورة',
                                          'max_score': 'الدرجة القصوى',
                                          'is_active': 'حالة التفعيل',
                                          'email': 'البريد الإلكتروني',
                                          'password': 'كلمة المرور',
                                          'name': 'الاسم',
                                          'phone': 'رقم الهاتف',
                                          'role': 'الدور',
                                          'title_ar': 'العنوان بالعربية',
                                          'title_en': 'العنوان بالإنجليزية',
                                          'content_ar': 'المحتوى بالعربية',
                                          'content_en': 'المحتوى بالإنجليزية',
                                    }

                                    const fieldName = fieldNames[field] || field
                                    const messagesArray = Array.isArray(messages) ? messages : [messages]

                                    return messagesArray.map((msg: string) => {
                                          // تحسين الرسائل لتكون أكثر وضوحاً
                                          let improvedMsg = msg

                                          // تحسين رسائل URL
                                          if (msg.includes('url') && msg.includes('valid')) {
                                                improvedMsg = 'رابط الصورة غير صالح. يرجى التأكد من صحة الرابط أو استخدام صورة بصيغة base64.'
                                          }

                                          // تحسين رسائل الطول
                                          if (msg.includes('greater than') || msg.includes('أكبر من')) {
                                                improvedMsg = msg.replace(/must not be greater than (\d+) characters?/i, 'يجب ألا يتجاوز $1 حرف')
                                          }

                                          return `${fieldName}: ${improvedMsg}`
                                    }).join('، ')
                              })
                              .join(' | ')

                        errorMessage = errors || 'يرجى التحقق من البيانات المدخلة'
                  }
                  // رسالة خطأ مباشرة من الخادم
                  else if (data.message) {
                        errorMessage = data.message
                  }
                  // رسالة خطأ من Laravel
                  else if (typeof data === 'string') {
                        errorMessage = data
                  }
            }
            // خطأ في الاتصال بالخادم
            else if (err.request) {
                  errorMessage = 'تعذر الاتصال بالخادم. يرجى التحقق من اتصال الإنترنت والمحاولة مرة أخرى.'
            }
            // خطأ عام
            else if (err.message) {
                  errorMessage = err.message
            }

            return error(errorMessage)
      }

      return {
            toasts,
            success,
            error,
            warning,
            info,
            removeToast,
            handleApiError
      }
}





