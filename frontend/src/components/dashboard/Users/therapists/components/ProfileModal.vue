<template>
  <div 
    v-if="open" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2 sm:p-4"
  >
    <div class="w-full max-w-4xl max-h-[95vh] overflow-y-auto bg-primary rounded-xl border border-primary p-4 sm:p-6 shadow-lg">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg sm:text-2xl font-semibold text-primary">الملف الشخصي - {{ viewingTherapist.name_ar }}</h2>
        <button 
          @click="$emit('close')"
          class="bg-tertiary hover:bg-secondary text-primary w-7 h-7 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center transition-colors"
        >
          ✕
        </button>
      </div>

      <!-- شبكة مكونة من عمودين -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
        <!-- العمود الأيسر -->
        <div class="space-y-4">
          <!-- بطاقة الملف الشخصي -->
          <div class="bg-secondary rounded-xl border border-primary p-4">
            <div class="text-center">
              <img 
                :src="getAvatarUrl(viewingTherapist)" 
                :alt="viewingTherapist.name_ar"
                class="w-16 h-16 sm:w-24 sm:h-24 rounded-full mx-auto mb-3 border-4 border-brand-500"
                @error="handleImageError"
              />
              <h2 class="text-lg sm:text-xl font-semibold text-primary">{{ viewingTherapist.name_ar }}</h2>
              <p class="text-secondary mt-1 text-sm">{{ viewingTherapist.title_ar }}</p>
              
              <!-- التقييم -->
              <div class="flex items-center justify-center gap-2 mt-2">
                <div class="flex items-center gap-1">
                  <span class="text-yellow-400 text-sm">★</span>
                  <span class="text-primary font-medium text-sm">{{ viewingTherapist.rating || '5.0' }}</span>
                </div>
                <span class="text-secondary text-xs">•</span>
                <span class="text-secondary text-xs">{{ viewingTherapist.total_sessions || 0 }} جلسة</span>
              </div>

              <!-- الشارة -->
              <div class="mt-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full bg-accent-500 text-white text-xs font-medium">
                  <span class="ml-1">✓</span>
                  {{ viewingTherapist.certifications?.[0]?.name || 'معتمد' }}
                </span>
              </div>

              <!-- مدة الجلسة -->
              <div class="mt-3 p-2 bg-primary rounded-lg border border-primary">
                <div class="text-xs text-secondary">مدة الجلسة</div>
                <div class="text-base font-semibold text-primary">{{ viewingTherapist.session_duration || 45 }} دقيقة</div>
              </div>
            </div>
          </div>

          <!-- قسم عن الخبير -->
          <div class="bg-secondary rounded-xl border border-primary p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">عن الخبير</h3>
            <p class="text-secondary leading-relaxed text-sm">
              {{ viewingTherapist.bio_ar }}
            </p>
          </div>

          <!-- المؤهلات العلمية -->
          <div class="bg-secondary rounded-xl border border-primary p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">المؤهلات العلمية</h3>
            <div class="space-y-3">
              <div 
                v-for="(qualification, index) in viewingTherapist.qualifications" 
                :key="index"
                class="border border-primary rounded-lg p-3 bg-primary"
              >
                <div class="space-y-2">
                  <div>
                    <h4 class="text-sm font-medium text-primary mb-1">اسم المؤهل:</h4>
                    <p class="text-primary text-sm">{{ qualification.name_ar }}</p>
                  </div>
                  <div>
                    <h4 class="text-sm font-medium text-primary mb-1">المؤسسة:</h4>
                    <p class="text-primary text-sm">{{ qualification.institution_ar }}</p>
                  </div>
                  <div v-if="qualification.year">
                    <h4 class="text-sm font-medium text-primary mb-1">سنة التخرج:</h4>
                    <p class="text-primary text-sm">{{ qualification.year }}</p>
                  </div>
                </div>
              </div>
              <div v-if="!viewingTherapist.qualifications || viewingTherapist.qualifications.length === 0" class="text-center py-4 text-secondary text-sm">
                لا توجد مؤهلات مضافة
              </div>
            </div>
          </div>
        </div>

        <!-- العمود الأيمن -->
        <div class="space-y-4">
          <!-- المنهجيات المتبعة -->
          <div class="bg-primary rounded-xl border border-primary p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">المنهجيات المتبعة</h3>
            <p class="text-secondary leading-relaxed text-sm">
              {{ viewingTherapist.methodologies_ar }}
            </p>
          </div>

          <!-- الخبرة العملية -->
          <div class="bg-primary rounded-xl border border-primary p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">الخبرة العملية</h3>
            <div class="flex items-center justify-between gap-3">
              <div class="text-center flex-1">
                <div class="text-lg sm:text-2xl font-bold text-brand-500">{{ viewingTherapist.experience || 0 }}+</div>
                <div class="text-xs text-secondary">سنوات خبرة</div>
              </div>
              <div class="text-center flex-1">
                <div class="text-lg sm:text-2xl font-bold text-brand-500">{{ viewingTherapist.total_sessions || 0 }}+</div>
                <div class="text-xs text-secondary">جلسة مكتملة</div>
              </div>
              <div class="text-center flex-1">
                <div class="text-lg sm:text-2xl font-bold text-brand-500">{{ viewingTherapist.rating_count || 0 }}</div>
                <div class="text-xs text-secondary">تقييم</div>
              </div>
            </div>
          </div>

          <!-- الجدول الأسبوعي -->
          <div class="bg-primary rounded-xl border border-primary p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">الجدول الأسبوعي</h3>
            <div class="grid grid-cols-7 gap-1">
              <div 
                v-for="day in weekDays" 
                :key="day.key"
                class="text-center"
              >
                <div class="text-xs font-medium text-primary mb-1">{{ day.label }}</div>
                <div 
                  :class="[
                    'w-8 h-8 rounded-full flex items-center justify-center text-xs transition-colors',
                    hasDaySchedule(viewingTherapist, day.key) ? 
                    'bg-brand-500 text-white' : 'bg-tertiary text-secondary'
                  ]"
                >
                  {{ hasDaySchedule(viewingTherapist, day.key) ? '✓' : '✗' }}
                </div>
              </div>
            </div>
          </div>

          <!-- معلومات الاتصال -->
          <div class="bg-primary rounded-xl border border-primary p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">معلومات الاتصال</h3>
            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <span class="text-secondary text-sm">📧</span>
                <span class="text-secondary text-sm">{{ viewingTherapist.email }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-secondary text-sm">📞</span>
                <span class="text-secondary text-sm">{{ viewingTherapist.phone }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-secondary text-sm">⏰</span>
                <span class="text-secondary text-sm">الرد خلال ٢٤ ساعة</span>
              </div>
            </div>
          </div>

          <!-- الشهادات -->
          <div class="bg-primary rounded-xl border border-primary p-4">
            <h3 class="text-base sm:text-lg font-semibold text-primary mb-2">الشهادات</h3>
            <div class="space-y-2">
              <div 
                v-for="(certification, index) in viewingTherapist.certifications" 
                :key="index"
                class="border border-primary rounded-lg p-2 bg-secondary"
              >
                <div class="text-sm text-primary">{{ certification.name }}</div>
                <div v-if="certification.issuing_authority" class="text-xs text-secondary">
                  من: {{ certification.issuing_authority }}
                </div>
                <div v-if="certification.year_obtained" class="text-xs text-secondary">
                  سنة: {{ certification.year_obtained }}
                </div>
              </div>
              <div v-if="!viewingTherapist.certifications || viewingTherapist.certifications.length === 0" class="text-center py-2 text-secondary text-sm">
                لا توجد شهادات مضافة
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  viewingTherapist: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])

const weekDays = [
  { key: 'saturday', label: 'السبت' },
  { key: 'sunday', label: 'الأحد' },
  { key: 'monday', label: 'الاثنين' },
  { key: 'tuesday', label: 'الثلاثاء' },
  { key: 'wednesday', label: 'الأربعاء' },
  { key: 'thursday', label: 'الخميس' },
  { key: 'friday', label: 'الجمعة' }
]

const hasDaySchedule = (therapist, dayKey) => {
  if (!therapist.schedules || !Array.isArray(therapist.schedules)) {
    return false
  }
  
  return therapist.schedules.some(schedule => 
    schedule.day === dayKey && schedule.available
  )
}

const getAvatarUrl = (therapist) => {
  // التحقق من وجود الصورة في عدة أماكن محتملة
  // أولاً: therapist.avatar (URL كامل من API)
  if (therapist.avatar) {
    // إذا كانت URL كاملة (من API)
    if (therapist.avatar.startsWith('http://') || therapist.avatar.startsWith('https://')) {
      return therapist.avatar
    }
    // إذا كانت مسار نسبي
    if (therapist.avatar.startsWith('/')) {
      return therapist.avatar
    }
    // إذا كانت base64
    if (therapist.avatar.startsWith('data:')) {
      return therapist.avatar
    }
    // إذا كانت مسار ملف (يجب أن يكون URL كامل من API)
    return therapist.avatar
  }
  
  // ثانياً: user.avatar (URL كامل من API)
  if (therapist.user?.avatar) {
    if (therapist.user.avatar.startsWith('http://') || therapist.user.avatar.startsWith('https://')) {
      return therapist.user.avatar
    }
    if (therapist.user.avatar.startsWith('/')) {
      return therapist.user.avatar
    }
    if (therapist.user.avatar.startsWith('data:')) {
      return therapist.user.avatar
    }
    // إذا كانت مسار ملف (يجب أن يكون URL كامل من API)
    return therapist.user.avatar
  }
  
  // صورة افتراضية - استخدام أيقونة بديلة
  return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2U1ZTdlYiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjQwIiBmaWxsPSIjOWNhM2FmIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+8J+RiDwvdGV4dD48L3N2Zz4='
}

const handleImageError = (event) => {
  // في حالة فشل تحميل الصورة، استخدم صورة افتراضية SVG
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2U1ZTdlYiIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjQwIiBmaWxsPSIjOWNhM2FmIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+8J+RiDwvdGV4dD48L3N2Zz4='
}
</script>
