import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

interface LoginData {
  email: string
  password: string
  remember: boolean
}

interface User {
  id: number
  name: string
  email: string
  role: string
  phone?: string
  joined_at?: string
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  // لا نحمل admin_token تلقائياً - يتم تحميله فقط عند الوصول للواجهة الإدارية
  // هذا يمنع تحميل admin_token عند زيارة الواجهة العامة
  const token = ref<string | null>(null)
  const loading = ref(false)
  const initializing = ref(false)
  // isAuthenticated يعتمد على token فقط (user يمكن جلبها لاحقاً)
  const isAuthenticated = computed(() => !!token.value)

  const login = async (loginData: LoginData): Promise<boolean> => {
    try {
      loading.value = true
      const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

      console.log('جاري الاتصال بالخادم:', `${API_URL}/login`)

      const response = await fetch(`${API_URL}/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          email: loginData.email,
          password: loginData.password
        })
      })

      if (response.ok) {
        const responseData = await response.json()

        // استخراج البيانات من response (البنية: { success, message, data: { user, token } })
        const data = responseData.data || responseData
        const userData = data.user
        const authToken = data.token

        // تحقق من أن المستخدم موجود وأنه مدير
        if (!userData || !userData.role || userData.role !== 'Admin') {
          throw new Error('غير مصرح بالدخول إلى لوحة التحكم')
        }

        // حفظ Token في localStorage دائماً (للبقاء مسجلاً بعد تحديث الصفحة)
        token.value = authToken
        localStorage.setItem('admin_token', authToken)

        // جلب بيانات المستخدم من API للتأكد من أحدث البيانات
        await fetchUser()

        return true
      } else {
        const errorData = await response.json()
        throw new Error(errorData.message || 'فشل تسجيل الدخول')
      }

    } catch (error: any) {
      console.error('API Login failed:', error)
      throw new Error(error.message || 'حدث خطأ في الاتصال بالخادم')
    } finally {
      loading.value = false
    }
  }

  // دالة جلب بيانات المستخدم من API
  const fetchUser = async () => {
    if (!token.value) {
      user.value = null
      return
    }

    try {
      loading.value = true
      const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

      const response = await fetch(`${API_URL}/user`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Accept': 'application/json'
        }
      })

      if (response.ok) {
        const responseData = await response.json()
        const userData = responseData.data?.user || responseData.user

        if (userData && userData.role) {
          user.value = userData
        } else {
          throw new Error('بيانات المستخدم غير صحيحة')
        }
      } else if (response.status === 401) {
        // Token غير صالح - حذف Token فقط في حالة 401
        token.value = null
        user.value = null
        localStorage.removeItem('admin_token')
        throw new Error('انتهت صلاحية الجلسة')
      } else {
        // خطأ آخر - لا تحذف Token (قد يكون مشكلة مؤقتة في الاتصال)
        throw new Error('فشل في جلب بيانات المستخدم')
      }
    } catch (error: any) {
      console.error('Error fetching user:', error)

      // حذف Token فقط إذا كان الخطأ 401 (غير مصرح)
      if (error.message && error.message.includes('انتهت صلاحية')) {
        token.value = null
        user.value = null
        localStorage.removeItem('admin_token')
      }
      // في حالة أخطاء أخرى (مثل مشاكل الاتصال)، لا تحذف Token

      throw error
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    try {
      if (token.value) {
        const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

        await fetch(`${API_URL}/logout`, {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token.value}`,
            'Accept': 'application/json'
          }
        })
      }
    } catch (error) {
      console.error('Logout API error:', error)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('admin_token')
    }
  }

  // تهيئة المصادقة - جلب بيانات المستخدم من API
  const initializeAuth = async () => {
    // إذا كان token موجود بالفعل في memory، لا حاجة لإعادة التحميل
    if (token.value && user.value) {
      return
    }

    const savedToken = localStorage.getItem('admin_token')

    if (savedToken) {
      token.value = savedToken
      initializing.value = true
      try {
        await fetchUser()
      } catch (error) {
        // إذا فشل جلب البيانات، لا تحذف Token فوراً (قد يكون هناك مشكلة مؤقتة في الاتصال)
        console.error('Failed to initialize auth:', error)
        // فقط إذا كان الخطأ 401 (غير مصرح)، احذف Token
        if (error instanceof Error && error.message.includes('انتهت صلاحية')) {
          token.value = null
          localStorage.removeItem('admin_token')
        }
      } finally {
        initializing.value = false
      }
    }
  }

  return {
    user,
    token,
    loading,
    initializing,
    isAuthenticated,
    login,
    logout,
    fetchUser,
    initializeAuth
  }
})