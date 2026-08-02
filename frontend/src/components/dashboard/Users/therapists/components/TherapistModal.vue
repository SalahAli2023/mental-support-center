<template>
  <div 
    v-if="open" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2 sm:p-4"
  >
    <div class="w-full max-w-2xl max-h-[95vh] overflow-y-auto bg-primary rounded-xl border border-primary p-4 sm:p-6 shadow-lg">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg sm:text-xl font-semibold text-primary">
          {{ editingTherapist ? 'تعديل المعالج' : 'إضافة معالج جديد' }}
        </h2>
        <button 
          @click="$emit('close')"
          class="bg-tertiary hover:bg-secondary text-primary w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center transition-colors text-sm"
        >
          ✕
        </button>
      </div>

      <!-- Step Indicator - Centered -->
      <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-center">
          <div 
            v-for="(step, index) in steps" 
            :key="index"
            class="flex flex-col items-center mx-4"
          >
            <!-- Step Circle -->
            <div 
              :class="[
                'w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300 border-2',
                currentStep > index ? 'bg-brand-500 text-white border-brand-500' :
                currentStep === index ? 'bg-brand-500 text-white border-brand-500' : 
                'bg-tertiary text-secondary border-primary'
              ]"
            >
              {{ currentStep > index ? '✓' : index + 1 }}
            </div>
            
            <!-- Step Label -->
            <div 
              :class="[
                'text-sm mt-3 font-medium transition-colors text-center',
                currentStep >= index ? 'text-brand-500' : 'text-secondary'
              ]"
            >
              {{ step.label }}
            </div>
          </div>
        </div>
      </div>

      <form @submit.prevent="$emit('save')" class="space-y-6">
        <!-- Step 1: Basic Information -->
        <div v-if="currentStep === 0" class="space-y-6">
          <div class="bg-secondary border border-primary rounded-lg p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">المعلومات الأساسية</h3>
            <p class="text-xs sm:text-sm text-secondary">أدخل المعلومات الشخصية الأساسية للمعالج</p>
          </div>
          
          <div class="grid grid-cols-1 gap-4">
            <!-- Name - Arabic & English -->
            <div class="space-y-3">
              <label class="block text-sm font-medium text-primary">الاسم الكامل</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <input 
                    v-model="therapistForm.name_ar"
                    type="text"
                    required
                    class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                    placeholder="الاسم بالعربية"
                  />
                </div>
                <div>
                  <input 
                    v-model="therapistForm.name_en"
                    type="text"
                    required
                    class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                    placeholder="Name in English"
                  />
                </div>
              </div>
            </div>

            <!-- Contact Information -->
           <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-primary mb-2">البريد الإلكتروني</label>
                <input 
                  v-model="therapistForm.email"
                  type="email"
                  required
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                  placeholder="أدخل البريد الإلكتروني"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-primary mb-2">رقم الهاتف</label>
                <input 
                  v-model="therapistForm.phone"
                  type="tel"
                  required
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                  placeholder="أدخل رقم الهاتف"
                />
              </div>
               <div>
              <label class="block text-sm font-medium text-primary mb-2">الجنس</label>
              <select 
                v-model="therapistForm.gender"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
              >
                <option value="male">male</option>
                <option value="female">female</option>
                
              </select>
            </div>
             <div>
                <label class="block text-sm font-medium text-primary mb-2">تاريخ الميلاد</label>
                <input 
                  v-model="therapistForm.date_of_birth"
                  type="date"
                  required
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                  placeholder=" تاريخ ميلادك "
                />
              </div>
            </div>

            <!-- Login Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-primary mb-2">اسم المستخدم</label>
                <input 
                  v-model="therapistForm.username"
                  type="text"
                  required
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                  placeholder="أدخل اسم المستخدم"
                />
              </div>

              <div>
                 <label class="block text-sm font-medium text-primary mb-2">كلمة المرور</label>
                  <input 
                    v-model="therapistForm.password"
                    type="password"
                    :required="!editingTherapist"
                    class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                    placeholder="أدخل كلمة المرور"
                  />
              </div>
            </div>

            <!-- Avatar Upload -->
            <div class="space-y-3">
              <label class="block text-sm font-medium text-primary mb-2">صورة المعالج</label>
              <div class="flex items-center gap-4">
                <!-- Preview -->
                <div v-if="avatarPreview" class="w-24 h-24 rounded-full overflow-hidden border-2 border-primary">
                  <img :src="avatarPreview" alt="Preview" class="w-full h-full object-cover" />
                </div>
                <div v-else-if="editingTherapist && editingTherapist.user?.avatar" class="w-24 h-24 rounded-full overflow-hidden border-2 border-primary">
                  <img :src="getAvatarUrl(editingTherapist)" alt="Current" class="w-full h-full object-cover" />
                </div>
                <div v-else class="w-24 h-24 rounded-full bg-tertiary border-2 border-primary flex items-center justify-center">
                  <i class="fas fa-user text-4xl text-secondary"></i>
                </div>
                
                <!-- Upload Button -->
                <div class="flex-1">
                  <label class="cursor-pointer">
                    <input 
                      type="file"
                      accept="image/*"
                      @change="handleAvatarChange"
                      class="hidden"
                    />
                    <div class="bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-2">
                      <i class="fas fa-upload"></i>
                      {{ avatarPreview || (editingTherapist && editingTherapist.user?.avatar) ? 'تغيير الصورة' : 'رفع صورة' }}
                    </div>
                  </label>
                  <p class="text-xs text-secondary mt-2">JPG, PNG, WebP (الحد الأقصى 5MB)</p>
                  <button 
                    v-if="avatarPreview || (editingTherapist && editingTherapist.user?.avatar)"
                    type="button"
                    @click="removeAvatar"
                    class="mt-2 text-red-500 hover:text-red-700 text-xs transition-colors"
                  >
                    <i class="fas fa-trash"></i> إزالة الصورة
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 2: Professional Information -->
        <div v-if="currentStep === 1" class="space-y-6">
          <div class="bg-secondary border border-primary rounded-lg p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">المعلومات المهنية</h3>
            <p class="text-xs sm:text-sm text-secondary">أدخل المعلومات المهنية والخبرات</p>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">الحالة</label>
              <select 
                v-model="therapistForm.status"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
              >
                <option value="active">نشط</option>
                <option value="busy">مشغول</option>
                <option value="inactive">غير نشط</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-primary mb-2">مدة الجلسة (دقيقة)</label>
              <select 
                v-model="therapistForm.sessionDuration"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
              >
                <option value="30">30 دقيقة</option>
                <option value="45">45 دقيقة</option>
                <option value="60">60 دقيقة</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-primary mb-2">سنوات الخبرة</label>
              <input 
                v-model="therapistForm.experience"
                type="number"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                placeholder="عدد سنوات الخبرة"
              />
            </div>

            <!-- Specialty - Arabic & English -->
            <div class="space-y-3">
              <label class="block text-sm font-medium text-primary mb-2">التخصص</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <input 
                    v-model="therapistForm.specialty_ar"
                    type="text"
                    required
                    class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                    placeholder="التخصص بالعربية"
                  />
                </div>
                <div>
                  <input 
                    v-model="therapistForm.specialty_en"
                    type="text"
                    required
                    class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                    placeholder="Specialty in English"
                  />
                </div>
              </div>
            </div>

            <!-- Title - Arabic & English -->
            <div class="space-y-3">
              <label class="block text-sm font-medium text-primary mb-2">الرتبة المهنية</label>
              <div class="grid grid-cols-1 gap-3">
                <div>
                  <input 
                    v-model="therapistForm.title_ar"
                    type="text"
                    class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                    placeholder="الرتبة بالعربية"
                  />
                </div>
                <div>
                  <input 
                    v-model="therapistForm.title_en"
                    type="text"
                    class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                    placeholder="Title in English"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Bio - Arabic & English -->
          <div class="space-y-3">
            <label class="block text-sm font-medium text-primary mb-2">نبذة عن المعالج</label>
            <div class="grid grid-cols-1 gap-3">
              <div>
                <textarea 
                  v-model="therapistForm.bio_ar"
                  rows="3"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                  placeholder="نبذة مختصرة بالعربية"
                />
              </div>
              <div>
                <textarea 
                  v-model="therapistForm.bio_en"
                  rows="3"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                  placeholder="Brief bio in English"
                />
              </div>
            </div>
          </div>

          <!-- Methodologies - Arabic & English -->
          <div class="space-y-3">
            <label class="block text-sm font-medium text-primary mb-2">المنهجيات المتبعة</label>
            <div class="grid grid-cols-1 gap-3">
              <div>
                <textarea 
                  v-model="therapistForm.methodologies_ar"
                  rows="3"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                  placeholder="المنهجيات بالعربية"
                />
              </div>
              <div>
                <textarea 
                  v-model="therapistForm.methodologies_en"
                  rows="3"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2.5 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                  placeholder="Methodologies in English"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Step 3: Qualifications -->
        <div v-if="currentStep === 2" class="space-y-6">
          <div class="bg-secondary border border-primary rounded-lg p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">المؤهلات العلمية</h3>
            <p class="text-xs sm:text-sm text-secondary">أضف المؤهلات العلمية والشهادات للمعالج</p>
          </div>

          <div class="flex items-center justify-between mb-4">
            <h4 class="text-base font-semibold text-primary">قائمة المؤهلات</h4>
            <button 
              type="button"
              @click="$emit('add-qualification')"
              class="bg-brand-500 hover:bg-brand-600 text-white px-3 py-2 rounded-lg flex items-center gap-2 text-sm transition-colors"
            >
              <PlusIcon class="h-4 w-4" />
              إضافة مؤهل
            </button>
          </div>

          <div class="space-y-4">
            <div 
              v-for="(qualification, index) in therapistForm.qualifications" 
              :key="index"
              class="border border-primary rounded-lg p-4 bg-secondary"
            >
              <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-primary">المؤهل {{ index + 1 }}</h4>
                <button 
                  type="button"
                  @click="$emit('remove-qualification', index)"
                  class="text-red-500 hover:text-red-700 p-1 transition-colors"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
              
              <!-- Qualification Name - Arabic & English -->
              <div class="space-y-3 mb-3">
                <label class="block text-xs font-medium text-primary">اسم المؤهل</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <input 
                      v-model="qualification.name_ar"
                      type="text"
                      class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                      placeholder="اسم المؤهل بالعربية"
                    />
                  </div>
                  <div>
                    <input 
                      v-model="qualification.name_en"
                      type="text"
                      class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                      placeholder="Qualification name in English"
                    />
                  </div>
                </div>
              </div>

              <!-- Institution - Arabic & English -->
              <div class="space-y-3">
                <label class="block text-xs font-medium text-primary">المؤسسة</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <input 
                      v-model="qualification.institution_ar"
                      type="text"
                      class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                      placeholder="المؤسسة بالعربية"
                    />
                  </div>
                  <div>
                    <input 
                      v-model="qualification.institution_en"
                      type="text"
                      class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
                      placeholder="Institution in English"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div v-if="therapistForm.qualifications.length === 0" class="text-center py-8 text-secondary border-2 border-dashed border-primary rounded-lg text-sm">
              لا توجد مؤهلات مضافة
            </div>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between gap-3 pt-6 border-t border-primary">
          <button 
            v-if="currentStep > 0"
            type="button"
            @click="$emit('update:step', currentStep - 1)"
            class="bg-tertiary hover:bg-secondary text-primary px-4 py-2.5 rounded-lg transition-colors text-sm font-medium"
          >
            السابق
          </button>
          <div v-else></div>

          <div class="flex gap-3">
            <button 
              type="button"
              @click="$emit('close')"
              class="bg-tertiary hover:bg-secondary text-primary px-4 py-2.5 rounded-lg transition-colors text-sm font-medium"
            >
              إلغاء
            </button>
            <button 
              v-if="currentStep < steps.length - 1"
              type="button"
              @click="$emit('update:step', currentStep + 1)"
              class="bg-brand-500 hover:bg-brand-600 text-white px-4 py-2.5 rounded-lg transition-colors text-sm font-medium"
            >
              التالي
            </button>
            <button 
              v-else
              type="submit"
              class="bg-brand-500 hover:bg-brand-600 text-white px-4 py-2.5 rounded-lg transition-colors text-sm font-medium"
            >
              {{ editingTherapist ? 'تحديث' : 'إضافة' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  editingTherapist: {
    type: Object,
    default: null
  },
  therapistForm: {
    type: Object,
    required: true
  },
  currentStep: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['close', 'save', 'update:step', 'add-qualification', 'remove-qualification', 'update:avatar'])

const steps = [
  { label: 'المعلومات الأساسية' },
  { label: 'المعلومات المهنية' },
  { label: 'المؤهلات العلمية' }
]

const avatarPreview = ref(null)
const avatarFile = ref(null)

const getAvatarUrl = (therapist) => {
  if (therapist?.user?.avatar) {
    if (therapist.user.avatar.startsWith('http://') || therapist.user.avatar.startsWith('https://')) {
      return therapist.user.avatar
    }
    if (therapist.user.avatar.startsWith('/')) {
      return therapist.user.avatar
    }
    return `/storage/${therapist.user.avatar}`
  }
  return null
}

const handleAvatarChange = (event) => {
  const file = event.target.files[0]
  if (!file) return

  // التحقق من حجم الملف (5MB)
  if (file.size > 5 * 1024 * 1024) {
    alert('حجم الصورة يجب أن يكون أقل من 5MB')
    return
  }

  // التحقق من نوع الملف
  if (!file.type.startsWith('image/')) {
    alert('الرجاء اختيار ملف صورة')
    return
  }

  avatarFile.value = file
  
  // إنشاء preview
  const reader = new FileReader()
  reader.onload = (e) => {
    avatarPreview.value = e.target.result
  }
  reader.readAsDataURL(file)

  // إرسال الملف للـ parent component
  emit('update:avatar', file)
}

const removeAvatar = () => {
  avatarPreview.value = null
  avatarFile.value = null
  emit('update:avatar', null)
}
</script>