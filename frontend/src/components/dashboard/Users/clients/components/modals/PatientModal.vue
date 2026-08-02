<template>
  <div 
    v-if="open" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2 sm:p-4"
  >
    <div class="w-full max-w-4xl max-h-[95vh] overflow-y-auto bg-primary rounded-xl border border-primary p-4 sm:p-6 shadow-lg">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg sm:text-xl font-semibold text-primary">
          {{ patient ? 'تعديل بيانات المريض' : 'إضافة مريض جديد' }}
        </h2>
        <button 
          @click="$emit('close')"
          class="bg-tertiary hover:bg-primary text-primary w-8 h-8 rounded-lg flex items-center justify-center"
        >
          ✕
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- بيانات تسجيل الدخول -->
        <div class="bg-secondary rounded-lg p-4">
          <h3 class="text-md font-semibold text-primary mb-4">بيانات تسجيل الدخول</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">اسم المستخدم *</label>
              <input 
                v-model="form.login.username"
                type="text"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
                placeholder="اسم المستخدم"
                dir="ltr"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-primary mb-2">كلمة المرور *</label>
              <input 
                v-model="form.login.password"
                type="password"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
                placeholder="كلمة المرور"
                dir="ltr"
              />
            </div>
          </div>
        </div>

        <!-- المعلومات الأساسية -->
        <div class="bg-secondary rounded-lg p-4">
          <h3 class="text-md font-semibold text-primary mb-4">المعلومات الأساسية</h3>
          <div class="grid grid-cols-1 gap-4">
            <!-- الاسم الكامل -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-primary mb-2">الاسم الكامل (عربي) *</label>
                <input 
                  v-model="form.profile.name.ar"
                  type="text"
                  required
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                  placeholder="أدخل الاسم الكامل بالعربية"
                  dir="rtl"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-primary mb-2">الاسم الكامل (إنجليزي) *</label>
                <input 
                  v-model="form.profile.name.en"
                  type="text"
                  required
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                  placeholder="Enter full name in English"
                  dir="ltr"
                />
              </div>
            </div>

            <!-- البريد الإلكتروني والهاتف -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-primary mb-2">البريد الإلكتروني *</label>
                <input 
                  v-model="form.profile.email"
                  type="email"
                  required
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
                  placeholder="example@email.com"
                  dir="ltr"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-primary mb-2">رقم الهاتف *</label>
                <input 
                  v-model="form.profile.phone"
                  type="tel"
                  required
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
                  placeholder="+966 5X XXX XXXX"
                  dir="ltr"
                />
              </div>
            </div>

            <!-- تاريخ الميلاد والجنس -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-primary mb-2">تاريخ الميلاد</label>
                <input 
                  v-model="form.profile.dateOfBirth"
                  type="date"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-primary mb-2">الجنس</label>
                <select 
                  v-model="form.profile.gender"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                >
                  <option value="">اختر الجنس</option>
                  <option value="male">ذكر</option>
                  <option value="female">أنثى</option>
                </select>
              </div>
            </div>

            <!-- العنوان -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-primary mb-2">العنوان (عربي)</label>
                <textarea 
                  v-model="form.profile.address.ar"
                  rows="2"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                  placeholder="أدخل العنوان الكامل بالعربية"
                  dir="rtl"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-primary mb-2">العنوان (إنجليزي)</label>
                <textarea 
                  v-model="form.profile.address.en"
                  rows="2"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                  placeholder="Enter full address in English"
                  dir="ltr"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- المعلومات الطبية -->
        <div class="bg-secondary rounded-lg p-4">
          <h3 class="text-md font-semibold text-primary mb-4">المعلومات الطبية</h3>
          <div class="grid grid-cols-1 gap-4">
            <!-- الحالات الصحية -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-primary mb-2">الحالات الصحية (عربي)</label>
                <div class="flex flex-wrap gap-2 mb-2">
                  <span 
                    v-for="condition in form.medical.conditions.ar" 
                    :key="condition"
                    class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs flex items-center gap-1"
                    dir="rtl"
                  >
                    {{ condition }}
                    <button 
                      type="button"
                      @click="removeCondition('ar', condition)"
                      class="hover:text-blue-900"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </span>
                </div>
                <div class="flex gap-2">
                  <input 
                    v-model="newCondition.ar"
                    type="text"
                    class="flex-1 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                    placeholder="أضف حالة صحية بالعربية"
                    dir="rtl"
                    @keypress.enter="addCondition('ar')"
                  />
                  <button 
                    type="button"
                    @click="addCondition('ar')"
                    class="bg-brand-500 hover:bg-[#8FAE2F] text-white px-3 py-2 rounded-lg text-sm"
                  >
                    إضافة
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-primary mb-2">الحالات الصحية (إنجليزي)</label>
                <div class="flex flex-wrap gap-2 mb-2">
                  <span 
                    v-for="condition in form.medical.conditions.en" 
                    :key="condition"
                    class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs flex items-center gap-1"
                    dir="ltr"
                  >
                    {{ condition }}
                    <button 
                      type="button"
                      @click="removeCondition('en', condition)"
                      class="hover:text-green-900"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </span>
                </div>
                <div class="flex gap-2">
                  <input 
                    v-model="newCondition.en"
                    type="text"
                    class="flex-1 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                    placeholder="Add health condition in English"
                    dir="ltr"
                    @keypress.enter="addCondition('en')"
                  />
                  <button 
                    type="button"
                    @click="addCondition('en')"
                    class="bg-brand-500 hover:bg-[#8FAE2F] text-white px-3 py-2 rounded-lg text-sm"
                  >
                    Add
                  </button>
                </div>
              </div>
            </div>

            <!-- أهداف العلاج -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-primary mb-2">أهداف العلاج (عربي)</label>
                <textarea 
                  v-model="form.medical.therapyGoals.ar"
                  rows="3"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                  placeholder="ما هي أهداف المريض من العلاج؟"
                  dir="rtl"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-primary mb-2">أهداف العلاج (إنجليزي)</label>
                <textarea 
                  v-model="form.medical.therapyGoals.en"
                  rows="3"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                  placeholder="What are the patient therapy goals?"
                  dir="ltr"
                />
              </div>
            </div>

            <!-- الحالة -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-primary mb-2">حالة المريض</label>
                <select 
                  v-model="form.medical.status"
                  class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                >
                  <option value="active">نشط</option>
                  <option value="inactive">غير نشط</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-primary">
          <button 
            type="button"
            @click="$emit('close')"
            class="bg-tertiary hover:bg-primary text-primary px-4 py-2 rounded-lg text-sm"
          >
            إلغاء
          </button>
          <button 
            type="submit"
            class="bg-brand-500 hover:bg-[#8FAE2F] text-white px-4 py-2 rounded-lg text-sm"
          >
            {{ patient ? 'تحديث' : 'إضافة' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  patient: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'save'])

const form = reactive({
  // بيانات تسجيل الدخول
  login: {
    username: '',
    password: ''
  },
  // المعلومات الشخصية
  profile: {
    name: {
      ar: '',
      en: ''
    },
    email: '',
    phone: '',
    dateOfBirth: '',
    gender: '',
    address: {
      ar: '',
      en: ''
    }
  },
  // المعلومات الطبية
  medical: {
    conditions: {
      ar: [],
      en: []
    },
    therapyGoals: {
      ar: '',
      en: ''
    },
    status: 'active'
  }
})

const newCondition = ref({
  ar: '',
  en: ''
})

// تحديث النموذج عند تغيير المريض
watch(() => props.patient, (patient) => {
  if (patient) {
    // تحويل بيانات المريض من API للنموذج
    Object.assign(form, {
      login: {
        username: patient.email?.split('@')[0] || '',
        password: ''
      },
      profile: {
        name: {
          ar: patient.name || '',
          en: patient.name || ''
        },
        email: patient.email || '',
        phone: patient.phone || '',
        dateOfBirth: patient.dateOfBirth || '',
        gender: patient.gender || '',
        address: {
          ar: patient.address || '',
          en: patient.address || ''
        }
      },
      medical: {
        conditions: {
          ar: patient.conditions || [],
          en: patient.conditions || []
        },
        therapyGoals: {
          ar: patient.therapyGoals || '',
          en: patient.therapyGoals || ''
        },
        status: patient.status || 'active'
      }
    })
  } else {
    // إعادة التعيين للنموذج الجديد
    Object.assign(form, {
      login: {
        username: '',
        password: ''
      },
      profile: {
        name: {
          ar: '',
          en: ''
        },
        email: '',
        phone: '',
        dateOfBirth: '',
        gender: '',
        address: {
          ar: '',
          en: ''
        }
      },
      medical: {
        conditions: {
          ar: [],
          en: []
        },
        therapyGoals: {
          ar: '',
          en: ''
        },
        status: 'active'
      }
    })
  }
}, { immediate: true })

const addCondition = (lang) => {
  if (newCondition.value[lang].trim() && !form.medical.conditions[lang].includes(newCondition.value[lang].trim())) {
    form.medical.conditions[lang].push(newCondition.value[lang].trim())
    newCondition.value[lang] = ''
  }
}

const removeCondition = (lang, condition) => {
  form.medical.conditions[lang] = form.medical.conditions[lang].filter(c => c !== condition)
}

const handleSubmit = () => {
  // إرسال البيانات للـ API من خلال الـ store
  emit('save', { ...form })
}
</script>

<style scoped>
.direction-ltr {
  direction: ltr;
  text-align: left;
}

/* تحسين محاذاة النص في الحقول */
input, textarea, select {
  text-align: inherit;
}
</style>