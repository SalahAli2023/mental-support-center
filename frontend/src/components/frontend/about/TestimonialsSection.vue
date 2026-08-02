<template>
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
      
      <TitleSection
        :mainText="translate('testimonials.title')"
        :highlightText="translate('testimonials.highlight')"
        :subtitle="translate('testimonials.subtitle')"
        textColor="text-gray-900"
        highlightColor="text-primary-green"
        gradientClass="bg-primary-green"
      />

      <!-- حالة التحميل -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-green"></div>
      </div>

      <!-- السلايدر -->
      <div v-else-if="testimonialsList.length > 0" class="relative overflow-hidden">
        <div 
          class="flex transition-transform duration-500 ease-in-out"
          :style="sliderStyle"
          :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'"
        >
          <div 
            v-for="(group, groupIndex) in testimonialGroups" 
            :key="groupIndex"
            class="w-full flex-shrink-0 px-2"
          >
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div 
                v-for="testimonial in group" 
                :key="testimonial.id || testimonial.name_ar"
                class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:shadow-md transition-all duration-300 h-full"
              >
                <!-- التقييم -->
                <div class="flex justify-center mb-3">
                  <div class="flex gap-1">
                    <i 
                      v-for="star in 5" 
                      :key="star"
                      class="fas fa-star text-xs"
                      :class="star <= testimonial.rating ? 'text-yellow-400' : 'text-gray-300'"
                    ></i>
                  </div>
                </div>
                
                <!-- النص -->
                <p class="text-gray-700 text-sm leading-relaxed mb-3 text-center line-clamp-3">
                  "{{ currentLanguage === 'ar' ? testimonial.text_ar : testimonial.text_en }}"
                </p>
                
                <!-- المعلومات -->
                <div class="flex items-center gap-2 justify-center" :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'">
                  <div class="w-8 h-8 bg-gradient-to-br from-primary-green to-emerald-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-xs">{{ getInitials(testimonial) }}</span>
                  </div>
                  <div class="text-right">
                    <div class="font-bold text-gray-900 text-xs">{{ currentLanguage === 'ar' ? testimonial.name_ar : testimonial.name_en }}</div>
                    <div class="text-gray-500 text-xs">{{ currentLanguage === 'ar' ? testimonial.role_ar : testimonial.role_en }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- أزرار التنقل -->
        <button 
          v-if="totalGroups > 1"
          @click="currentLanguage === 'ar' ? nextSlide() : prevSlide()"
          class="hidden md:flex absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white rounded-full shadow-lg border border-gray-200 items-center justify-center hover:bg-primary-green hover:text-white transition-all duration-300 z-10"
        >
          <i class="fas fa-chevron-left text-xs"></i>
        </button>

        <button 
          v-if="totalGroups > 1"
          @click="currentLanguage === 'ar' ? prevSlide() : nextSlide()"
          class="hidden md:flex absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white rounded-full shadow-lg border border-gray-200 items-center justify-center hover:bg-primary-green hover:text-white transition-all duration-300 z-10"
        >
          <i class="fas fa-chevron-right text-xs"></i>
        </button>
        
        <!-- النقاط الإرشادية -->
        <div v-if="totalGroups > 1" class="flex justify-center gap-1 mt-6">
          <button 
            v-for="index in totalGroups" 
            :key="index"
            @click="goToSlide(index - 1)"
            class="w-2 h-2 rounded-full transition-all duration-300"
            :class="currentIndex === index - 1 ? 'bg-primary-green w-3' : 'bg-gray-300'"
          ></button>
        </div>
      </div>

      <!-- رسالة عدم وجود توصيات -->
      <div v-else class="text-center py-12 text-tertiary">
        <i class="fas fa-comment-dots text-4xl mb-3 block opacity-30"></i>
        <p>{{ translate('testimonials.empty') || 'لا توجد توصيات حالياً' }}</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useTranslations } from '@/composables/useTranslations'
import { useSettings } from '@/composables/useSettings'
import TitleSection from '@/components/frontend/layouts/TitleSection.vue'

const { currentLanguage, translate } = useTranslations()
const { contactSettings, loading, getArray } = useSettings()

// 🔹 البيانات الافتراضية (في حالة عدم وجود اتصال بقاعدة البيانات)
const defaultTestimonials = [
  {
    name_ar: 'أحمد محمد',
    name_en: 'Ahmed Mohammed',
    text_ar: 'تجربة رائعة مع الفريق المتخصص، ساعدوني في تخطي أصعب المراحل بحرفية عالية واهتمام حقيقي.',
    text_en: 'Amazing experience with the specialized team, they helped me overcome the most difficult stages with high professionalism and genuine care.',
    rating: 5,
    role_ar: 'مستفيد',
    role_en: 'Beneficiary',
    is_active: true,
  },
  {
    name_ar: 'د. فاطمة علي',
    name_en: 'Dr. Fatima Ali',
    text_ar: 'الورش التدريبية ممتازة والمحتوى علمي وعملي، استفدت كثيراً في مجال عملي كأخصائي نفسي.',
    text_en: 'The training workshops are excellent and the content is scientific and practical, I benefited a lot in my work as a psychologist.',
    rating: 5,
    role_ar: 'أخصائي نفسي',
    role_en: 'Psychologist',
    is_active: true,
  },
  {
    name_ar: 'سارة عبدالله',
    name_en: 'Sara Abdullah',
    text_ar: 'السرية والاحترافية كانتا على أعلى مستوى، أشعر بالأمان والثقة في التعامل مع المنصة.',
    text_en: 'Confidentiality and professionalism were at the highest level, I feel safe and confident in dealing with the platform.',
    rating: 4,
    role_ar: 'مستفيدة',
    role_en: 'Beneficiary',
    is_active: true,
  },
  // {
  //   name_ar: 'خالد الحربي',
  //   name_en: 'Khalid Al-Harbi',
  //   text_ar: 'خدمة مميزة وفريق محترف، ساعدني في تطوير مهاراتي وتحسين أدائي الوظيفي بشكل ملحوظ.',
  //   text_en: 'Distinctive service and professional team, helped me develop my skills and significantly improve my job performance.',
  //   rating: 5,
  //   role_ar: 'مدير',
  //   role_en: 'Manager',
  //   is_active: true,
  // },
  // {
  //   name_ar: 'نورة السعد',
  //   name_en: 'Nora Al-Saad',
  //   text_ar: 'الدعم المستمر والمتابعة كانت ممتازة، أشكر الفريق على جهودهم المتميزة.',
  //   text_en: 'Continuous support and follow-up were excellent, I thank the team for their outstanding efforts.',
  //   rating: 4,
  //   role_ar: 'معلمة',
  //   role_en: 'Teacher',
  //   is_active: true,
  // },
  // {
  //   name_ar: 'محمد الشمري',
  //   name_en: 'Mohammed Al-Shammari',
  //   text_ar: 'التجربة فاقت توقعاتي، الخدمة سريعة والمحتوى قيم ومفيد للغاية.',
  //   text_en: 'The experience exceeded my expectations, the service is fast and the content is very valuable and useful.',
  //   rating: 5,
  //   role_ar: 'طالب',
  //   role_en: 'Student',
  //   is_active: true,
  // },
]

// 🔹 بيانات التوصيات من الإعدادات مع Fallback
const testimonialsList = computed(() => {
  // إذا كان في حالة تحميل، استخدم البيانات الافتراضية
  if (loading.value) {
    return defaultTestimonials
  }
  
  // جلب من الإعدادات
  // ✅ من achievements 
  const testimonials = getArray('achievements', 'testimonials')
  
  if (testimonials && testimonials.length > 0) {
    return testimonials.filter(t => t.is_active !== false)
  }
  
  // Fallback إلى البيانات الافتراضية
  return defaultTestimonials
})

// 🔹 السلايدر
const currentIndex = ref(0)
const itemsPerGroup = ref(3)
let autoPlayInterval

// مجموعات التوصيات
const testimonialGroups = computed(() => {
  const groups = []
  const items = testimonialsList.value
  
  if (items.length === 0) return groups
  
  for (let i = 0; i < items.length; i += itemsPerGroup.value) {
    groups.push(items.slice(i, i + itemsPerGroup.value))
  }
  return groups
})

const totalGroups = computed(() => testimonialGroups.value.length)

// السلايدر حسب اللغة
const sliderStyle = computed(() => {
  if (totalGroups.value === 0) return {}
  const direction = currentLanguage.value === 'ar' ? -1 : 1
  return { transform: `translateX(${currentIndex.value * -100 * direction}%)` }
})

// التنقل
const nextSlide = () => {
  if (totalGroups.value > 0) {
    currentIndex.value = (currentIndex.value + 1) % totalGroups.value
    resetAutoPlay()
  }
}

const prevSlide = () => {
  if (totalGroups.value > 0) {
    currentIndex.value = (currentIndex.value - 1 + totalGroups.value) % totalGroups.value
    resetAutoPlay()
  }
}

const goToSlide = (index) => {
  if (index >= 0 && index < totalGroups.value) {
    currentIndex.value = index
    resetAutoPlay()
  }
}

// الحصول على الحروف الأولى
const getInitials = (testimonial) => {
  const name = currentLanguage.value === 'ar' ? testimonial.name_ar : testimonial.name_en
  if (!name) return '?'
  const parts = name.trim().split(' ')
  if (parts.length === 1) return parts[0].charAt(0).toUpperCase()
  return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase()
}

// التشغيل التلقائي
const resetAutoPlay = () => {
  stopAutoPlay()
  startAutoPlay()
}

const startAutoPlay = () => {
  if (totalGroups.value > 1) {
    autoPlayInterval = setInterval(() => {
      nextSlide()
    }, 5000)
  }
}

const stopAutoPlay = () => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
    autoPlayInterval = null
  }
}

// تحديث عدد العناصر حسب حجم الشاشة
const updateItemsPerGroup = () => {
  const width = window.innerWidth
  if (width < 768) itemsPerGroup.value = 1
  else if (width < 1024) itemsPerGroup.value = 2
  else itemsPerGroup.value = 3
  currentIndex.value = 0
}

// إعادة ضبط عند تغيير اللغة
watch(currentLanguage, () => {
  // إعادة حساب المجموعات
})

// مراقبة التغييرات في القائمة
watch(testimonialsList, () => {
  currentIndex.value = 0
  resetAutoPlay()
}, { deep: true })

// دورة الحياة
onMounted(() => {
  updateItemsPerGroup()
  window.addEventListener('resize', updateItemsPerGroup)
  if (totalGroups.value > 0) {
    setTimeout(startAutoPlay, 1000)
  }
})

onUnmounted(() => {
  window.removeEventListener('resize', updateItemsPerGroup)
  stopAutoPlay()
})
</script>


<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>