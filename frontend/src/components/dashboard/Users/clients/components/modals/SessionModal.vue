<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-4xl max-h-[90vh] overflow-y-auto bg-primary rounded-2xl shadow-xl border border-primary">
      <div class="flex items-center justify-between p-6 border-b border-primary sticky top-0 bg-primary z-10">
        <h2 class="text-xl font-semibold text-primary">
          {{ session ? 'تعديل الجلسة / Edit Session' : 'إضافة جلسة جديدة / Add New Session' }}
        </h2>
        <button @click="$emit('close')" class="p-2 hover:bg-tertiary rounded-lg text-primary">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- معلومات الجلسة الأساسية -->
        <div class="grid grid-cols-1 gap-4">
          <!-- عنوان الجلسة -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">عنوان الجلسة (عربي) *</label>
              <input 
                v-model="form.title_ar"
                type="text"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="أدخل عنوان الجلسة بالعربية"
                dir="rtl"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-primary mb-2">عنوان الجلسة (إنجليزي) *</label>
              <input 
                v-model="form.title_en"
                type="text"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="Enter session title in English"
                dir="ltr"
              />
            </div>
          </div>

          <!-- التاريخ والوقت -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">التاريخ *</label>
              <input 
                v-model="form.session_date"
                type="date"
                required
                :min="minDate"
                @change="loadAvailableSlots"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-primary mb-2">الوقت *</label>
              <select 
                v-model="form.session_time"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                :disabled="!availableSlots.length || loadingSlots"
              >
                <option value="">اختر الوقت المناسب</option>
                <option 
                  v-for="slot in availableSlots" 
                  :key="slot.time"
                  :value="slot.time"
                  :disabled="!slot.available"
                >
                  {{ slot.time }} {{ !slot.available ? '(محجوز)' : '' }}
                </option>
              </select>
              <div v-if="form.session_date && form.therapist_id" class="mt-2 text-xs">
                <span v-if="loadingSlots" class="text-blue-500">
                  جاري تحميل المواعيد المتاحة...
                </span>
                <span v-else-if="availableSlots.length" class="text-green-600">
                  {{ availableSlots.filter(slot => slot.available).length }} مواعيد متاحة
                </span>
                <span v-else class="text-red-500">
                  لا توجد مواعيد متاحة في هذا التاريخ
                </span>
              </div>
            </div>
          </div>

          <!-- المعالج وحالة الجلسة -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">المعالج *</label>
              <select 
                v-model="form.therapist_id"
                @change="onTherapistChange"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                :disabled="loadingTherapists"
              >
                <option value="">اختر المعالج</option>
                <option 
                  v-for="therapist in therapists" 
                  :key="therapist.id"
                  :value="therapist.id"
                >
                  {{ therapist.name_ar || therapist.name_en || therapist.name }} - {{ therapist.specialty_ar || therapist.specialty_en || 'بدون تخصص' }}
                </option>
              </select>
              <div v-if="loadingTherapists" class="mt-2 text-xs text-blue-500">
                جاري تحميل قائمة المعالجين...
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-primary mb-2">حالة الجلسة *</label>
              <select 
                v-model="form.status"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
              >
                <option value="scheduled">مجدولة / Scheduled</option>
                <option value="completed">مكتملة / Completed</option>
                <option value="cancelled">ملغاة / Cancelled</option>
              </select>
            </div>
          </div>

          <!-- التقدم ونوع الجلسة -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">التقدم (%)</label>
              <input 
                v-model="form.progress"
                type="number"
                min="0"
                max="100"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
                placeholder="0-100"
                dir="ltr"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-primary mb-2">نوع الجلسة *</label>
              <select 
                v-model="form.type"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
              >
                <option value="individual">فردية / Individual</option>
                <option value="group">جماعية / Group</option>
                <option value="family">عائلية / Family</option>
                <option value="assessment">تقييم / Assessment</option>
                <option value="followup">متابعة / Follow-up</option>
              </select>
            </div>
          </div>

          <!-- المكان والمدة -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">المكان *</label>
              <select 
                v-model="form.location"
                required
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
              >
                <option value="clinic">العيادة / Clinic</option>
                <option value="online">أونلاين / Online</option>
                <option value="home">منزل المريض / Patient's Home</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-primary mb-2">مدة الجلسة (دقائق)</label>
              <input 
                v-model="form.duration"
                type="number"
                min="15"
                max="240"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 direction-ltr"
                placeholder="60"
                dir="ltr"
              />
            </div>
          </div>

          <!-- الملاحظات -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">ملاحظات (عربي)</label>
              <textarea 
                v-model="form.notes_ar"
                rows="3"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="أدخل ملاحظات الجلسة بالعربية..."
                dir="rtl"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-primary mb-2">ملاحظات (إنجليزي)</label>
              <textarea 
                v-model="form.notes_en"
                rows="3"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="Enter session notes in English..."
                dir="ltr"
              />
            </div>
          </div>

          <!-- تقرير الجلسة (يظهر فقط إذا كانت مكتملة) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4" v-if="form.status === 'completed'">
            <div>
              <label class="block text-sm font-medium text-primary mb-2">تقرير الجلسة (عربي)</label>
              <textarea 
                v-model="form.report_ar"
                rows="3"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="أدخل تقرير الجلسة بالعربية..."
                dir="rtl"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-primary mb-2">تقرير الجلسة (إنجليزي)</label>
              <textarea 
                v-model="form.report_en"
                rows="3"
                class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500"
                placeholder="Enter session report in English..."
                dir="ltr"
              />
            </div>
          </div>

          <!-- المرفقات -->
          <div>
            <label class="block text-sm font-medium text-primary mb-2">المرفقات</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
              <input 
                type="file"
                multiple
                @change="handleFileUpload"
                class="hidden"
                id="file-upload"
                ref="fileInput"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
              />
              <label for="file-upload" class="cursor-pointer">
                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <p class="text-sm text-gray-600">اسحب الملفات هنا أو انقر للرفع</p>
                <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, JPG, PNG (الحد الأقصى 10MB)</p>
              </label>
            </div>
            
            <!-- قائمة الملفات المرفوعة -->
            <div v-if="form.attachments.length > 0" class="mt-3 space-y-2">
              <div v-for="(file, index) in form.attachments" :key="index" class="flex items-center justify-between bg-gray-50 rounded-lg p-2">
                <div class="flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <span class="text-sm text-gray-700">{{ file.name || file.file_name }}</span>
                  <span class="text-xs text-gray-500">({{ formatFileSize(file.size || file.file_size) }})</span>
                </div>
                <button 
                  type="button"
                  @click="removeAttachment(index)"
                  class="text-red-500 hover:text-red-700"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- رسائل الخطأ -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ error }}
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-primary sticky bottom-0 bg-primary pb-4">
          <button 
            type="button"
            @click="$emit('close')"
            class="bg-tertiary hover:bg-primary text-primary px-4 py-2 rounded-lg text-sm"
            :disabled="loading"
          >
            إلغاء
          </button>
          <button 
            type="submit"
            class="bg-brand-500 hover:bg-[#8FAE2F] text-white px-4 py-2 rounded-lg text-sm"
            :disabled="loading"
          >
            <span v-if="loading">جاري الحفظ...</span>
            <span v-else>{{ session ? 'تحديث' : 'إضافة' }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, computed, nextTick } from 'vue'
import { usePatientSessionsStore } from '@/stores/patientSessions'

const sessionsStore = usePatientSessionsStore()

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  session: {
    type: Object,
    default: null
  },
  patientId: {
    type: [String, Number],
    required: true
  }
})

const emit = defineEmits(['close', 'save'])

const form = reactive({
  title_ar: '',
  title_en: '',
  session_date: '',
  session_time: '',
  therapist_id: '',
  status: 'scheduled',
  progress: 0,
  type: 'individual',
  location: 'clinic',
  notes_ar: '',
  notes_en: '',
  report_ar: '',
  report_en: '',
  duration: 60,
  attachments: []
})

// البيانات التفاعلية
const availableSlots = ref([])
const therapists = ref([])
const loadingSlots = ref(false)
const loadingTherapists = ref(false)
const loading = ref(false)
const error = ref('')
const fileInput = ref(null)

// الحد الأدنى للتاريخ (اليوم)
const minDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

// جلب قائمة المعالجين عند فتح النافذة
const loadTherapists = async () => {
  try {
    loadingTherapists.value = true
    error.value = ''
    
    console.log('👨‍⚕️ جلب المعالجين...')
    
    await sessionsStore.fetchTherapists()
    therapists.value = sessionsStore.therapists || []
    
    console.log('✅ المعالجين المحملين:', therapists.value)
    
    if (therapists.value.length === 0) {
      error.value = 'لا توجد معالجين متاحين'
    }
  } catch (err) {
    console.error('❌ خطأ في تحميل المعالجين:', err)
    error.value = 'حدث خطأ في تحميل قائمة المعالجين: ' + (err.response?.data?.message || err.message)
  } finally {
    loadingTherapists.value = false
  }
}

// تحميل المواعيد المتاحة من الـ API
const loadAvailableSlots = async () => {
  if (!form.session_date || !form.therapist_id) {
    availableSlots.value = []
    return
  }

  try {
    loadingSlots.value = true
    error.value = ''
    
    console.log('📅 جلب المواعيد المتاحة للمعالج:', form.therapist_id, 'التاريخ:', form.session_date)
    
    await sessionsStore.fetchAvailableSlots(
      props.patientId,
      form.therapist_id,
      form.session_date,
      form.duration || 60
    )
    
    availableSlots.value = sessionsStore.availableSlots || []
    
    console.log('✅ المواعيد المتاحة:', availableSlots.value)
    
    // إعادة تعيين الوقت إذا لم يكن متاحاً
    if (form.session_time && !availableSlots.value.find(slot => slot.time === form.session_time && slot.available)) {
      form.session_time = ''
    }
    
  } catch (err) {
    console.error('❌ خطأ في تحميل المواعيد المتاحة:', err)
    error.value = 'حدث خطأ في تحميل المواعيد المتاحة: ' + (err.response?.data?.message || err.message)
    availableSlots.value = []
  } finally {
    loadingSlots.value = false
  }
}

// عند تغيير المعالج
const onTherapistChange = () => {
  form.session_time = '' // إعادة تعيين الوقت
  if (form.session_date) {
    loadAvailableSlots()
  }
}

// التحقق من توفر الموعد
const checkTimeSlotAvailability = async () => {
  if (!form.session_date || !form.session_time || !form.therapist_id) {
    return false
  }

  const selectedSlot = availableSlots.value.find(slot => slot.time === form.session_time)
  return selectedSlot ? selectedSlot.available : false
}

// معالجة إرسال النموذج
const handleSubmit = async () => {
  try {
    // التحقق من الحقول المطلوبة
    if (!form.title_ar || !form.title_en) {
      error.value = 'يرجى إدخال عنوان الجلسة باللغتين'
      return
    }

    if (!form.session_date) {
      error.value = 'يرجى اختيار تاريخ الجلسة'
      return
    }

    if (!form.session_time) {
      error.value = 'يرجى اختيار الوقت المناسب'
      return
    }

    if (!form.therapist_id) {
      error.value = 'يرجى اختيار المعالج'
      return
    }

    // التحقق من توفر الموعد
    const isTimeSlotAvailable = await checkTimeSlotAvailability()
    if (!isTimeSlotAvailable) {
      error.value = 'هذا الموعد غير متاح للمعالج المحدد'
      return
    }

    loading.value = true
    error.value = ''

    console.log('💾 حفظ بيانات الجلسة:', form)

    // إرسال البيانات
    emit('save', { ...form })
    
  } catch (err) {
    console.error('❌ خطأ في حفظ الجلسة:', err)
    error.value = 'حدث خطأ أثناء حفظ الجلسة: ' + (err.response?.data?.message || err.message)
  } finally {
    loading.value = false
  }
}

// تحديث النموذج عند تغيير الجلسة
watch(() => props.session, (session) => {
  if (session) {
    console.log('✏️ تحرير الجلسة:', session)
    
    // استخدام أسماء الحقول التي يتوقعها الـ API
    Object.assign(form, {
      title_ar: session.title_ar || '',
      title_en: session.title_en || '',
      session_date: session.session_date || '',
      session_time: session.session_time || '',
      therapist_id: session.therapist_id || '',
      status: session.status || 'scheduled',
      progress: session.progress || 0,
      type: session.type || 'individual',
      location: session.location || 'clinic',
      notes_ar: session.notes_ar || '',
      notes_en: session.notes_en || '',
      report_ar: session.report_ar || '',
      report_en: session.report_en || '',
      duration: session.duration || 60,
      attachments: session.attachments || []
    })
    
    console.log('📝 النموذج بعد التعبئة:', form)
    
    // تحميل المواعيد إذا كان هناك تاريخ ومعالج
    if (form.session_date && form.therapist_id) {
      nextTick(() => {
        loadAvailableSlots()
      })
    }
  } else {
    // إعادة التعيين للنموذج الجديد
    Object.assign(form, {
      title_ar: '',
      title_en: '',
      session_date: '',
      session_time: '',
      therapist_id: '',
      status: 'scheduled',
      progress: 0,
      type: 'individual',
      location: 'clinic',
      notes_ar: '',
      notes_en: '',
      report_ar: '',
      report_en: '',
      duration: 60,
      attachments: []
    })
    availableSlots.value = []
  }
  
  // مسح الخطأ عند تغيير الجلسة
  error.value = ''
}, { immediate: true })

// مراقبة تغييرات التاريخ والمعالج لتحميل المواعيد
watch([() => form.session_date, () => form.therapist_id], () => {
  if (form.session_date && form.therapist_id) {
    loadAvailableSlots()
  } else {
    availableSlots.value = []
  }
})

// مراقبة تغيير المدة لإعادة تحميل المواعيد
watch(() => form.duration, () => {
  if (form.session_date && form.therapist_id) {
    loadAvailableSlots()
  }
})

// جلب المعالجين عند فتح النافذة
watch(() => props.open, async (isOpen) => {
  if (isOpen) {
    await loadTherapists()
  }
})

// معالجة رفع الملفات
const handleFileUpload = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    if (file.size <= 10 * 1024 * 1024) { // 10MB limit
      form.attachments.push(file)
    } else {
      error.value = 'الملف كبير جداً. الحد الأقصى 10MB'
    }
  })
  
  // Reset file input
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// إزالة مرفق
const removeAttachment = (index) => {
  form.attachments.splice(index, 1)
}

// تنسيق حجم الملف
const formatFileSize = (bytes) => {
  if (!bytes) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>

<style scoped>
.direction-ltr {
  direction: ltr;
  text-align: left;
}
</style>