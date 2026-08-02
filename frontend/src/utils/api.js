import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: 60000,
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()

    // Token لوحة التحكم (المدير)
    const adminToken = authStore.token || localStorage.getItem('admin_token')
    // Token المستخدم العادي (العميل من الواجهة الأمامية)
    const frontendToken = localStorage.getItem('frontend_token')

    // أولوية حسب السياق الحالي في الواجهة:
    // إذا كان المسار يبدأ بـ /dashboard أو /admin نستخدم توكن الأدمن
    // في غير ذلك نستخدم توكن الفرونتند (عميل) إن وجد، وإلا نرجع لتوكن الأدمن إن وُجد
    const path = window.location?.pathname || ''
    const inDashboard = path.startsWith('/dashboard') || path.startsWith('/admin')

    // في لوحة التحكم نستخدم فقط توكن الأدمن
    // في الموقع العام نستخدم فقط توكن العميل (ولا نستخدم توكن الأدمن حتى لو كان موجوداً)
    const token = inDashboard ? adminToken : frontendToken

    console.log('API Request:', config.url)
    console.log('Using admin token:', !!adminToken, 'frontend token:', !!frontendToken)

    // إضافة Authorization header فقط إذا كان هناك token صالح
    // هذا يمنع إرسال "Bearer undefined" أو "Bearer null" للـ routes العامة
    if (token && token.trim() !== '' && token !== 'null' && token !== 'undefined') {
      config.headers.Authorization = `Bearer ${token}`
    } else {
      // إزالة Authorization header إذا لم يكن هناك token صالح
      delete config.headers.Authorization
    }

    // إذا كان FormData، لا نضيف Content-Type - المتصفح يضيفه تلقائياً مع boundary
    if (config.data instanceof FormData) {
      delete config.headers['Content-Type']
    }

    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    console.log('API Response Success:', response.status, response.config.url)
    return response
  },
  (error) => {
    console.log('API Response Error:', error.response?.status, error.config?.url)
    console.log('Error details:', error.response?.data)

    if (error.response?.status === 401) {
      // Unauthorized - مسح البيانات وإعادة التوجيه فقط للواجهة الإدارية
      const path = window.location?.pathname || ''
      const inDashboard = path.startsWith('/dashboard') || path.startsWith('/admin')

      if (inDashboard) {
        // فقط في لوحة التحكم: إعادة التوجيه إلى صفحة تسجيل الدخول الإدارية
        console.log('Unauthorized access in dashboard, redirecting to admin login...')
        const authStore = useAuthStore()
        authStore.logout()
        window.location.href = '/admin/login'
      } else {
        // في الواجهة العامة: فقط حذف token (لا إعادة توجيه)
        console.log('Unauthorized access in frontend, clearing tokens...')
        localStorage.removeItem('frontend_token')
        // لا نمسح admin_token لأنه ليس ذا صلة بالواجهة العامة
      }
    }

    if (error.response?.status === 419) {
      window.location.reload()
    }

    return Promise.reject(error)
  }
)

export default api
