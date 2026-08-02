<template>
  <div class="space-y-6 p-2 sm:p-4">
    <!-- رأس الصفحة -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div class="flex items-center gap-4">
        <button 
          @click="$router.back()"
          class="bg-gray-100 hover:bg-gray-200 text-gray-700 w-10 h-10 rounded-lg flex items-center justify-center transition-colors"
        >
          <ArrowLeftIcon class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">تفاصيل المستخدم</h1>
          <p class="text-sm text-gray-500">عرض كافة معلومات المستخدم</p>
        </div>
      </div>
      
      <div class="flex items-center gap-2">
        <button 
          @click="editUser"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors"
        >
          <PencilSquareIcon class="w-4 h-4" />
          تعديل
        </button>
        <button 
          @click="deleteUser"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors"
        >
          <TrashIcon class="w-4 h-4" />
          حذف
        </button>
      </div>
    </div>

    <!-- حالة التحميل -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- رسالة الخطأ -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex items-center">
        <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-red-800">{{ error }}</span>
      </div>
      <button 
        @click="$router.push('/dashboard/users')"
        class="mt-2 text-sm text-red-600 hover:text-red-800 underline"
      >
        العودة لقائمة المستخدمين
      </button>
    </div>

    <!-- محتوى تفاصيل المستخدم -->
    <div v-else-if="user" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- العمود الأول -->
      <div class="lg:col-span-2 space-y-6">
        <!-- البطاقة الشخصية -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الشخصية</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">الاسم الكامل</label>
              <p class="text-lg font-medium text-gray-900">{{ user.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">البريد الإلكتروني</label>
              <p class="text-lg text-gray-900 direction-ltr text-left">{{ user.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">رقم الهاتف</label>
              <p class="text-lg text-gray-900">{{ user.phone || 'غير محدد' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">الدور</label>
              <span :class="roleBadgeClass(user.role)" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                {{ getRoleText(user.role) }}
              </span>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ الانضمام</label>
              <p class="text-lg text-gray-900">{{ formatDate(user.joined_at) }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ الإنشاء</label>
              <p class="text-lg text-gray-900">{{ formatDateTime(user.created_at) }}</p>
            </div>
          </div>
        </div>

        <!-- السيرة الذاتية -->
        <div v-if="user.bio" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">السيرة الذاتية</h2>
          <p class="text-gray-700 leading-relaxed">{{ user.bio }}</p>
        </div>

        <!-- البرامج المسجلة -->
        <UserProgramsList :user-id="user.id" />
      </div>

      <!-- العمود الثاني -->
      <div class="space-y-6">
        <!-- صورة المستخدم -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm text-center">
          <div class="relative w-32 h-32 mx-auto mb-4">
            <img 
              :src="userAvatar" 
              :alt="user.name"
              class="w-32 h-32 rounded-full object-cover border-4 border-brand-500/30 shadow-lg"
              @error="handleAvatarError"
            />
            <div class="absolute -bottom-1 -end-1 w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center border-2 border-brand-500">
              <ShieldCheckIcon v-if="user?.role === 'Admin'" class="w-4 h-4 text-red-500" />
              <HeartIcon v-else-if="user?.role === 'Therapist'" class="w-4 h-4 text-blue-500" />
              <UserIcon v-else class="w-4 h-4 text-green-500" />
            </div>
          </div>

          <h3 class="text-lg font-semibold text-gray-900">{{ user.name }}</h3>
          <p class="text-sm text-gray-500">{{ getRoleText(user.role) }}</p>
          
          <div class="mt-3 flex justify-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
              نشط
            </span>
          </div>

          <div class="mt-4 flex justify-center gap-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
              <CheckBadgeIcon class="w-3 h-3 mr-1" />
              ID: {{ user.id }}
            </span>
          </div>
        </div>

        <!-- الإحصائيات -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات إضافية</h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">الحالة</span>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                نشط
              </span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">البريد مفعل</span>
              <CheckBadgeIcon class="w-5 h-5 text-green-500" />
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">آخر تحديث</span>
              <span class="text-sm text-gray-900">{{ formatDateTime(user.updated_at) }}</span>
            </div>
          </div>
        </div>

        <!-- الإجراءات السريعة -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">الإجراءات السريعة</h3>
          <div class="space-y-2">
            <button 
              @click="sendMessage"
              class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2 transition-colors"
            >
              <ChatBubbleLeftRightIcon class="w-4 h-4" />
              إرسال رسالة
            </button>
            <button 
              @click="resetPassword"
              class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2 transition-colors"
            >
              <KeyIcon class="w-4 h-4" />
              إعادة تعيين كلمة المرور
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- مودال التعديل -->
    <UserModal 
      :open="showEditModal"
      :user="user"
      @close="showEditModal = false"
      @save="handleUpdateUser"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '../../../stores/users'
import UserModal from './UserModal.vue'
import UserProgramsList from './UserProgramsList.vue'
import type { User } from '../../../types/user'

import { 
  UserCircleIcon,
  ShieldCheckIcon,
  HeartIcon,
  UserIcon,
  CheckBadgeIcon,
  EnvelopeIcon,
  PhoneIcon,
  CalendarIcon,
  ClockIcon,
  PencilSquareIcon,
  TrashIcon,
  ArrowLeftIcon,
  ChatBubbleLeftRightIcon,
  KeyIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const user = ref<User | null>(null)
const loading = ref(false)
const error = ref('')
const showEditModal = ref(false)

const userId = route.params.id as string

// ✅ صورة المستخدم
const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=8FAE2F&color=fff&size=128'

const userAvatar = computed(() => {
  if (!user.value) return defaultAvatar
  
  // إذا كان هناك صورة مخزنة
  if (user.value.avatar) {
    // إذا كانت تبدأ بـ http (رابط كامل)
    if (user.value.avatar.startsWith('http')) {
      return user.value.avatar
    }
    // إذا كانت تبدأ بـ data:image (Base64)
    if (user.value.avatar.startsWith('data:image')) {
      return user.value.avatar
    }
    // مسار تخزين نسبي
    const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
    const baseUrl = apiBaseUrl.replace('/api', '')
    const cleanUrl = user.value.avatar.replace(/^\/+/, '')
    return `${baseUrl}/storage/${cleanUrl}`
  }
  
  // صورة افتراضية من الأحرف الأولى
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value.name)}&background=8FAE2F&color=fff&size=128`
})

const handleAvatarError = (event: Event) => {
  const img = event.target as HTMLImageElement
  if (user.value) {
    img.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value.name)}&background=8FAE2F&color=fff&size=128`
  }
}

// ✅ أيقونة الدور
const getRoleIcon = (role: string) => {
  const icons: { [key: string]: string } = {
    'Admin': '👑',
    'Therapist': '💚',
    'Client': '🧑'
  }
  return icons[role] || '👤'
}

// جلب بيانات المستخدم
const fetchUser = async () => {
  loading.value = true
  error.value = ''
  
  try {
    user.value = await userStore.getUserById(parseInt(userId))
  } catch (err: any) {
    error.value = err.message || 'فشل في تحميل بيانات المستخدم'
    console.error('Error fetching user:', err)
  } finally {
    loading.value = false
  }
}

// تنسيق التاريخ
const formatDate = (dateString: string) => {
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) {
      return 'غير محدد'
    }
    return date.toLocaleDateString('ar-SA', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (error) {
    return 'غير محدد'
  }
}

// تنسيق التاريخ والوقت
const formatDateTime = (dateString: string) => {
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) {
      return 'غير محدد'
    }
    return date.toLocaleDateString('ar-SA', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (error) {
    return 'غير محدد'
  }
}

// ألوان البادجات حسب الدور
const roleBadgeClass = (role: string) => {
  switch (role) {
    case 'Admin':
      return 'bg-red-100 text-red-800'
    case 'Therapist':
      return 'bg-blue-100 text-blue-800'
    case 'Client':
      return 'bg-green-100 text-green-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

// نص الدور بالعربية
const getRoleText = (role: string) => {
  const roles: { [key: string]: string } = {
    'Admin': 'مدير',
    'Therapist': 'معالج',
    'Client': 'عميل'
  }
  return roles[role] || role
}

// الحروف الأولى من الاسم
const getUserInitials = (name: string) => {
  return name
    .split(' ')
    .map(part => part.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

// الإجراءات
const editUser = () => {
  showEditModal.value = true
}

const deleteUser = async () => {
  if (!user.value) return

  if (confirm(`هل أنت متأكد من حذف المستخدم "${user.value.name}"؟`)) {
    try {
      await userStore.deleteUser(user.value.id)
      router.push('/dashboard/users')
    } catch (err: any) {
      alert(err.message || 'فشل في حذف المستخدم')
    }
  }
}

const handleUpdateUser = async (userData: any) => {
  try {
    await userStore.updateUser(user.value!.id, userData)
    await fetchUser() // إعادة تحميل البيانات
    showEditModal.value = false
  } catch (err: any) {
    alert(err.message || 'فشل في تحديث المستخدم')
  }
}

const sendMessage = () => {
  if (user.value) {
    alert(`سيتم إرسال رسالة إلى ${user.value.name}`)
  }
}

const resetPassword = () => {
  if (user.value) {
    if (confirm(`هل تريد إعادة تعيين كلمة المرور للمستخدم "${user.value.name}"؟`)) {
      alert('تم إرسال رابط إعادة تعيين كلمة المرور إلى بريد المستخدم')
    }
  }
}

// جلب البيانات عند تحميل الكومبوننت
onMounted(() => {
  fetchUser()
})
</script>

<style scoped>
.direction-ltr {
  direction: ltr;
  text-align: left;
}
</style>