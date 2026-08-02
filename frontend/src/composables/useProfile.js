import { ref } from 'vue'
import { useNotifications } from './useNotifications'

// مفتاح ثابت لتخزين المستخدم الحالي في localStorage
const CURRENT_USER_KEY = 'currentUser'

export const useProfile = () => {
  const { showSuccess, showError } = useNotifications()

  // قراءة المستخدم الحالي من localStorage عند البداية
  const initialUser = (() => {
    try {
      const stored = localStorage.getItem(CURRENT_USER_KEY)
      return stored ? JSON.parse(stored) : null
    } catch {
      return null
    }
  })()

  const user = ref(initialUser)

  const login = async (credentials) => {
    try {
      // محاكاة عملية تسجيل الدخول
      await new Promise(resolve => setTimeout(resolve, 1000))

      const storedCredentials = JSON.parse(localStorage.getItem('userCredentials') || '[]')
      const userCredential = storedCredentials.find(
        cred => (cred.email === credentials.email || cred.username === credentials.email) &&
          cred.password === credentials.password
      )

      if (userCredential) {
        const users = JSON.parse(localStorage.getItem('registeredUsers') || '[]')
        user.value = users.find(u => u.email === userCredential.email || u.username === userCredential.username)

        localStorage.setItem(CURRENT_USER_KEY, JSON.stringify(user.value))
        showSuccess('تم تسجيل الدخول بنجاح!')
        return true
      } else {
        showError('بيانات الدخول غير صحيحة')
        return false
      }
    } catch (error) {
      showError('حدث خطأ أثناء تسجيل الدخول')
      return false
    }
  }

  const setUserFromApi = (userData) => {
    user.value = userData
    try {
      localStorage.setItem(CURRENT_USER_KEY, JSON.stringify(userData))
    } catch {
      // تجاهل أخطاء التخزين
    }
  }

  const logout = () => {
    const oldToken = localStorage.getItem('frontend_token')
    user.value = null
    localStorage.removeItem(CURRENT_USER_KEY)
    localStorage.removeItem('frontend_token')
    // إرسال event لتحديث المكونات الأخرى
    window.dispatchEvent(new StorageEvent('storage', {
      key: 'frontend_token',
      oldValue: oldToken,
      newValue: null,
      storageArea: localStorage
    }))
    showSuccess('تم تسجيل الخروج بنجاح')
  }

  const isAuthenticated = () => !!user.value

  return {
    user,
    login,
    setUserFromApi,
    logout,
    isAuthenticated,
  }
}