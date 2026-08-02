<template>
  <div 
    v-if="open" 
    class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-black/40"
  >
    <div class="w-full max-w-2xl max-h-[95vh] overflow-y-auto rounded-xl shadow-lg bg-secondary border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg sm:text-xl font-semibold text-primary">
          {{ user ? 'تعديل بيانات المستخدم' : 'إضافة مشرف جديد' }}
        </h2>
        <button 
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 dark:hover:bg-gray-800"
          aria-label="إغلاق"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- الصورة الشخصية -->
        <div class="space-y-4">
          <h3 class="text-md font-semibold text-primary border-b pb-2 border-gray-200 dark:border-gray-700">
            الصورة الشخصية
          </h3>
          
          <div class="flex flex-col items-center gap-4">
            <!-- معاينة الصورة -->
            <div class="relative">
              <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-brand-500/30 bg-gray-100 dark:bg-gray-800">
                <img 
                  :src="imagePreview || defaultAvatar" 
                  alt="صورة المستخدم"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                />
              </div>
              
              <!-- زر تغيير الصورة -->
              <label 
                for="avatar-upload"
                class="absolute bottom-0 end-0 w-10 h-10 rounded-full bg-brand-500 text-white flex items-center justify-center cursor-pointer hover:bg-brand-600 transition-colors shadow-lg"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </label>
              <input 
                id="avatar-upload"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleImageUpload"
              />
            </div>

            <!-- معلومات الصورة -->
            <div class="text-center">
              <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ imageFile ? imageFile.name : 'لم يتم اختيار صورة' }}
              </p>
              <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                الحد الأقصى للحجم: 5 ميجابايت
              </p>
            </div>

            <!-- زر إزالة الصورة -->
            <button 
              v-if="imagePreview && imagePreview !== defaultAvatar"
              type="button"
              @click="removeImage"
              class="text-xs text-red-500 hover:text-red-600 transition-colors"
            >
              إزالة الصورة
            </button>
          </div>
        </div>

        <!-- المعلومات الأساسية -->
        <div class="space-y-4">
          <h3 class="text-md font-semibold text-primary border-b pb-2 border-gray-200 dark:border-gray-700">
            المعلومات الأساسية
          </h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">
                الاسم الكامل *
              </label>
              <input 
                v-model="form.name"
                type="text"
                required
                class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                placeholder="أدخل الاسم الكامل"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-primary mb-2">
                البريد الإلكتروني *
              </label>
              <input 
                v-model="form.email"
                type="email"
                required
                class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent direction-ltr"
                placeholder="example@email.com"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">
                الدور *
              </label>
              <div class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm text-primary flex items-center">
                <span class="badge badge-error">مدير (Admin)</span>
                <span class="mr-2 text-gray-500 text-xs">(سياق إضافة المشرفين)</span>
              </div>
              <input 
                type="hidden" 
                v-model="form.role"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-primary mb-2">
                رقم الهاتف
              </label>
              <input 
                v-model="form.phone"
                type="tel"
                class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent direction-ltr"
                placeholder="+966 5X XXX XXXX"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">
                كلمة المرور {{ user ? '(اتركه فارغاً للحفاظ على كلمة المرور الحالية)' : '*' }}
              </label>
              <div class="relative">
                <input 
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  :required="!user"
                  class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent direction-ltr pr-10"
                  placeholder="أدخل كلمة المرور"
                  minlength="6"
                />
                <button 
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                  aria-label="تبديل إظهار كلمة المرور"
                >
                  <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
              </div>
              <button 
                type="button"
                @click="showPassword = !showPassword"
                class="mt-1 text-xs text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
              >
                {{ showPassword ? 'إخفاء' : 'إظهار' }} كلمة المرور
              </button>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-primary mb-2">
              السيرة الذاتية (اختياري)
            </label>
            <textarea 
              v-model="form.bio"
              rows="3"
              class="input w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
              placeholder="أدخل وصفاً عن المشرف..."
            />
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
          <button 
            type="button"
            @click="$emit('close')"
            class="btn btn-outline px-6 py-2 rounded-lg text-sm font-medium"
          >
            إلغاء
          </button>
          <button 
            type="submit"
            :disabled="isSubmitting"
            class="btn btn-primary px-6 py-2 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isSubmitting" class="flex items-center gap-2">
              <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              جاري الحفظ...
            </span>
            <span v-else>{{ user ? 'تحديث' : 'إضافة مشرف' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import type { User } from '../../../types/user'

interface Props {
  open: boolean
  user?: User | null
  isAdminContext?: boolean
}

const props = defineProps<Props>()
const emit = defineEmits(['close', 'save'])

const showPassword = ref(false)
const isSubmitting = ref(false)

// 🔹 دوال الصورة
const imageFile = ref<File | null>(null)
const imagePreview = ref<string>('')
const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=8FAE2F&color=fff&size=128'

const form = reactive({
  name: '',
  email: '',
  role: 'Admin', // ⭐ الدور الافتراضي دائمًا Admin
  phone: '',
  password: '',
  bio: '',
  avatar: ''
})

// معالجة اختيار الصورة
const handleImageUpload = (event: Event) => {
  const input = event.target as HTMLInputElement
  if (input.files && input.files[0]) {
    const file = input.files[0]
    
    if (file.size > 5 * 1024 * 1024) {
      alert('حجم الصورة كبير جداً. الحد الأقصى 5 ميجابايت')
      input.value = ''
      return
    }
    
    if (!file.type.startsWith('image/')) {
      alert('يرجى اختيار ملف صورة صحيح')
      input.value = ''
      return
    }
    
    imageFile.value = file
    
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target?.result as string
      form.avatar = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

// إزالة الصورة
const removeImage = () => {
  imageFile.value = null
  imagePreview.value = defaultAvatar
  form.avatar = ''
  
  const input = document.getElementById('avatar-upload') as HTMLInputElement
  if (input) input.value = ''
}

// معالجة خطأ تحميل الصورة
const handleImageError = (event: Event) => {
  const img = event.target as HTMLImageElement
  img.src = defaultAvatar
}

// تحديث النموذج عند تغيير المستخدم
watch(() => props.user, (user) => {
  if (user) {
    Object.assign(form, {
      name: user.name || '',
      email: user.email || '',
      role: user.role || 'Admin',
      phone: user.phone || '',
      password: '',
      bio: user.bio || '',
      avatar: user.avatar || ''
    })
    
    if (user.avatar) {
      imagePreview.value = user.avatar
    } else {
      imagePreview.value = defaultAvatar
    }
  } else {
    Object.assign(form, {
      name: '',
      email: '',
      role: 'Admin',
      phone: '',
      password: '',
      bio: '',
      avatar: ''
    })
    imagePreview.value = defaultAvatar
    imageFile.value = null
  }
}, { immediate: true })

// ✅ handleSubmit المعدل
const handleSubmit = () => {
  console.log('🔍 تحقق من البيانات:', form)
  
  // تحقق من صحة البيانات
  if (!form.name.trim()) {
    alert('يرجى إدخال الاسم الكامل')
    return
  }
  
  if (!form.email.trim()) {
    alert('يرجى إدخال البريد الإلكتروني')
    return
  }
  
  if (form.role !== 'Admin') {
    form.role = 'Admin'
  }
  
  if (!props.user && !form.password.trim()) {
    alert('كلمة المرور مطلوبة للمستخدمين الجدد')
    return
  }
  
  if (!props.user && form.password.length < 6) {
    alert('كلمة المرور يجب أن تكون على الأقل 6 أحرف')
    return
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(form.email.trim())) {
    alert('يرجى إدخال بريد إلكتروني صحيح')
    return
  }
  
  // ✅ إنشاء FormData
  const formData = new FormData()
  
  // البيانات النصية
  formData.append('name', form.name.trim())
  formData.append('email', form.email.trim())
  formData.append('role', 'Admin')
  formData.append('phone', form.phone.trim() || '')
  formData.append('bio', form.bio.trim() || '')
  
  if (form.password.trim()) {
    formData.append('password', form.password.trim())
  }
  
  // ✅ إضافة الصورة
  if (imageFile.value) {
    formData.append('avatar', imageFile.value)
  }
  
  // ✅ ✅ ✅ أهم شيء: إضافة _method للتعديل
  if (props.user) {
    formData.append('_method', 'PUT')
  }
  
  console.log('📤 إرسال البيانات كـ FormData')
  console.log('📎 الصورة:', imageFile.value ? imageFile.value.name : 'لا توجد')
  console.log('🔄 طريقة الطلب:', props.user ? 'PUT (via _method)' : 'POST')
  
  // إرسال FormData
  emit('save', formData)
}
</script>

<style scoped>
.direction-ltr {
  direction: ltr;
  text-align: left;
}

/* تحسينات للوضع الداكن */
:deep(input),
:deep(textarea),
:deep(div[class*="bg-"]) {
  background-color: var(--bg-primary) !important;
}

.dark :deep(input),
.dark :deep(textarea) {
  background-color: var(--bg-tertiary) !important;
  border-color: var(--border-primary);
}

.dark :deep(.bg-gray-50) {
  background-color: var(--bg-secondary) !important;
}

:deep(.input) {
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

:deep(.input:focus) {
  box-shadow: 0 0 0 3px rgba(158, 191, 59, 0.1);
}

.dark :deep(.input:focus) {
  box-shadow: 0 0 0 3px rgba(158, 191, 59, 0.2);
}
</style>