<template>
  <div class="min-h-screen  ">
    <!-- مسار التنقل (Breadcrumb) - Mobile Optimized -->
    <div class="bg-gray-150 ">
      <div class="max-w-7xl mx-auto px-3 sm:px-6 py-2 sm:py-1">
        <nav class="flex items-center gap-1 sm:gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap scrollbar-hide" dir="rtl">
          <router-link to="/" class="hover:text-primary-green transition-colors duration-300 px-1 flex-shrink-0">
            {{ currentLanguage === 'ar' ? 'الرئيسية' : 'Home' }}
          </router-link>
          <i class="fas fa-chevron-left text-xs text-gray-400 flex-shrink-0"></i>
          <span class="hover:text-primary-green transition-colors duration-300 cursor-pointer px-1 flex-shrink-0"
            @click="handleBackToEvents">
            {{ currentLanguage === 'ar' ? 'الفعاليات' : 'Events' }}
          </span>
          <i class="fas fa-chevron-left text-xs text-gray-400 flex-shrink-0"></i>
          <span class="text-primary-green font-medium px-1 truncate flex-shrink-0 max-w-[120px] sm:max-w-none">{{ event.title }}</span>
        </nav>
      </div>
    </div>

    <!-- محتوى الصفحة الرئيسي -->
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-4 lg:py-6">
      <div class="flex flex-col lg:grid lg:grid-cols-3 gap-4 sm:gap-4 lg:gap-6">
        <!-- العمود الأيسر - محتوى الفعالية -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6 lg:space-y-8">
          <!-- الصورة الرئيسية أولاً -->
          <div class="bg-white rounded-xl overflow-hidden">
            <img 
              :src="event.media" 
              :alt="event.title" 
              class="w-full h-40 sm:h-48 md:h-64 lg:h-80 object-cover"
            />
          </div>

          <!-- رأس الفعالية -->
          <div class="bg-white rounded-xl p-3 sm:p-4 lg:p-6">
            <div class="flex flex-col gap-4">
              <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div class="flex-1">
                  <!-- العنوان -->
                  <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 text-right">
                    {{ event.title }}
                  </h1>

                  <!-- معلومات الفعالية -->
                  <div class="space-y-2">
                    <!-- التاريخ -->
                    <div class="flex items-center gap-2 sm:gap-3 text-gray-700">
                      <div class="w-7 h-7 sm:w-8 sm:h-8 flex-shrink-0 bg-primary-green bg-opacity-10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar text-primary-green text-xs sm:text-sm"></i>
                      </div>
                      <div class="flex-1">
                        <p class="text-xs text-gray-500">{{ currentLanguage === 'ar' ? 'التاريخ والوقت' : 'Date & Time' }}</p>
                        <p class="font-medium text-xs sm:text-sm">{{ event.date }}</p>
                      </div>
                    </div>

                    <!-- الموقع -->
                    <div class="flex items-center gap-2 sm:gap-3 text-gray-700">
                      <div class="w-7 h-7 sm:w-8 sm:h-8 flex-shrink-0 bg-primary-pink bg-opacity-10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-primary-pink text-xs sm:text-sm"></i>
                      </div>
                      <div class="flex-1">
                        <p class="text-xs text-gray-500">{{ currentLanguage === 'ar' ? 'الموقع' : 'Location' }}</p>
                        <p class="font-medium text-xs sm:text-sm">{{ event.location }}</p>
                      </div>
                    </div>

                    <!-- المدة -->
                    <div class="flex items-center gap-2 sm:gap-3 text-gray-700">
                      <div class="w-7 h-7 sm:w-8 sm:h-8 flex-shrink-0 bg-primary-green bg-opacity-10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-primary-green text-xs sm:text-sm"></i>
                      </div>
                      <div class="flex-1">
                        <p class="text-xs text-gray-500">{{ currentLanguage === 'ar' ? 'المدة' : 'Duration' }}</p>
                        <p class="font-medium text-xs sm:text-sm">{{ event.duration }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- شارة النوع -->
                <div class="flex-shrink-0 self-start mt-2 sm:mt-0">
                  <span :class="`inline-block text-xs font-semibold px-2 sm:px-3 py-1 rounded-full ${getCategoryStyle(event.type)}`">
                    {{ getTranslatedCategory(event.type) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- محتوى الفعالية -->
          <div class="bg-white rounded-xl p-3 sm:p-4 lg:p-6">
            <!-- النبذة العامة -->
            <div class="mb-4 sm:mb-6">
              <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-2 sm:mb-3 pb-2 border-b-2 border-primary-green inline-block text-right">
                {{ currentLanguage === 'ar' ? 'نبذة عن الفعالية' : 'Event Overview' }}
              </h2>
              <p class="text-gray-700 leading-relaxed text-xs sm:text-sm text-right">
                {{ event.fullDescription }}
              </p>
            </div>

            <!-- المواضيع المغطاة -->
            <div class="mb-4 sm:mb-6">
              <h2 class="text-base sm:text-lg font-bold text-gray-900 mb-2 sm:mb-3 pb-2 border-b-2 border-primary-pink inline-block text-right">
                {{ currentLanguage === 'ar' ? 'المواضيع المغطاة' : 'Covered Topics' }}
              </h2>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3">
                <div v-for="(topic, index) in event.topics" :key="index"
                  class="flex items-start gap-2 p-2 sm:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-300">
                  <i class="fas fa-check text-primary-green mt-0.5 flex-shrink-0 text-xs"></i>
                  <span class="text-gray-700 text-xs sm:text-sm text-right flex-1">{{ topic }}</span>
                </div>
              </div>
            </div>

            <!-- معلومات إضافية للجوال فقط -->
            <div class="sm:hidden space-y-3">
              <!-- التكلفة (إذا كانت متاحة) -->
              <div v-if="event.price" class="flex items-center justify-between p-2 bg-gradient-to-r from-primary-green/5 to-primary-pink/5 rounded-lg">
                <span class="text-gray-600 text-xs">{{ currentLanguage === 'ar' ? 'التكلفة' : 'Price' }}</span>
                <span class="text-primary-green font-bold text-sm">{{ event.price }}</span>
              </div>
              
              <!-- عدد الحضور (إذا كان متاحاً) -->
              <div v-if="event.attendees" class="flex items-center gap-1 text-gray-600 text-xs">
                <i class="fas fa-users text-primary-green"></i>
                <span>{{ event.attendees }} {{ currentLanguage === 'ar' ? 'مشارك' : 'attendees' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- العمود الأيمن - Sidebar -->
        <div class="lg:col-span-1">
          <div class="sticky top-2 sm:top-4 lg:top-8 space-y-3 sm:space-y-4 lg:space-y-6">
            <!-- استخدام مكون RelatedEvents -->
            <RelatedEvents 
              :events="allEvents" 
              :currentEvent="event" 
              @event-click="handleRelatedEventClick"
              class="w-full"
            />

            <!-- زر الحجز (للجوال فقط) -->
            <div class="sm:hidden bg-gradient-to-r from-primary-green to-primary-pink rounded-xl p-3">
              <div class="flex flex-col items-center text-center space-y-2">
                <i class="fas fa-ticket-alt text-white text-lg"></i>
                <h3 class="text-white font-bold text-sm">{{ currentLanguage === 'ar' ? 'احجز مكانك الآن' : 'Book Your Spot Now' }}</h3>
                <p class="text-white/90 text-xs">{{ currentLanguage === 'ar' ? 'مقاعد محدودة' : 'Limited Seats Available' }}</p>
                <button 
                  @click="handleBooking"
                  class="bg-white text-primary-green font-bold px-3 py-2 rounded-lg w-full hover:bg-gray-100 transition-colors duration-300 text-xs"
                >
                  {{ currentLanguage === 'ar' ? 'احجز الآن' : 'Book Now' }}
                </button>
              </div>
            </div>

            <!-- معلومات الاتصال (للجوال فقط) -->
            <div class="sm:hidden bg-white rounded-xl p-3 border border-gray-200">
              <h3 class="text-gray-900 font-bold mb-2 text-right text-xs">{{ currentLanguage === 'ar' ? 'معلومات الاتصال' : 'Contact Info' }}</h3>
              <div class="space-y-1 text-right">
                <div class="flex items-center justify-end gap-1 text-gray-600 text-xs">
                  <span>+966 50 123 4567</span>
                  <i class="fas fa-phone text-primary-green"></i>
                </div>
                <div class="flex items-center justify-end gap-1 text-gray-600 text-xs">
                  <span>info@mentalhealth.com</span>
                  <i class="fas fa-envelope text-primary-green"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- زر الحجز الثابت للجوال -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 z-50 lg:hidden">
      <div class="flex items-center justify-between max-w-7xl mx-auto">
        <div>
          <div class="text-primary-green font-bold text-sm" v-if="event.price">{{ event.price }}</div>
          <div class="text-gray-500 text-xs" v-else>{{ currentLanguage === 'ar' ? 'مجاني' : 'Free' }}</div>
        </div>
        <button 
          @click="handleBooking"
          class="bg-gradient-to-r from-primary-green to-primary-pink text-white font-bold px-3 py-2 rounded-lg hover:opacity-90 transition-opacity duration-300 flex items-center gap-1 text-xs"
        >
          <i class="fas fa-ticket-alt text-xs"></i>
          {{ currentLanguage === 'ar' ? 'احجز الآن' : 'Book Now' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import RelatedEvents from '@/components/frontend/events/RelatedEvents.vue'
import { useTranslations } from '@/composables/useTranslations'
import { useEventStore } from '@/stores/events'

// استخدام composable الترجمة و store
const { currentLanguage, translate } = useTranslations()
const eventStore = useEventStore()

// تعريف الـ props والأحداث
const props = defineProps({
  event: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'navigate-to-event', 'book'])

// استخدام بيانات الفعاليات من الـ store للفعاليات ذات الصلة
const allEvents = computed(() => eventStore.events)

// دالة للحصول على نمط التصنيف
const getCategoryStyle = (type) => {
  const key = (type || '').toString().trim().toLowerCase()
  const styles = {
    evenings: 'bg-green-100 text-green-700',
    events: 'bg-green-100 text-green-700',
    workshops: 'bg-green-100 text-green-700',
    'أمسيات': 'bg-green-100 text-green-700',
    'فعاليات': 'bg-green-100 text-green-700',
    'ورش عمل': 'bg-green-100 text-green-700'
  }
  return styles[key] || 'bg-gray-100 text-gray-700'
}

// دالة لترجمة التصنيف
const getTranslatedCategory = (type) => {
  const key = (type || '').toString().trim().toLowerCase()
  const categories = {
    evenings: currentLanguage === 'ar' ? 'أمسيات' : 'Evenings',
    events: currentLanguage === 'ar' ? 'فعاليات' : 'Events',
    workshops: currentLanguage === 'ar' ? 'ورش عمل' : 'Workshops',
    'أمسيات': currentLanguage === 'ar' ? 'أمسيات' : 'Evenings',
    'فعاليات': currentLanguage === 'ar' ? 'فعاليات' : 'Events',
    'ورش عمل': currentLanguage === 'ar' ? 'ورش عمل' : 'Workshops'
  }
  return categories[key] || type
}

// معالجة النقر على فعالية ذات صلة
const handleRelatedEventClick = (relatedEvent) => {
  emit('navigate-to-event', relatedEvent)
}

// معالجة الحجز
const handleBooking = () => {
  emit('book', props.event)
}

// معالجة العودة إلى صفحة الفعاليات عبر مسار التنقل
const handleBackToEvents = () => {
  emit('close')
}

// التأكد من أن الصفحة تبدأ من الأعلى
onMounted(() => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* تحسينات للتصميم المتجاوب */
@media (max-width: 1024px) {
  .sticky {
    position: static;
  }
}

/* تحسينات للشاشات الصغيرة جداً */
@media (max-width: 640px) {
  .text-xl {
    font-size: 1.25rem;
  }
}

/* تلميحات تفاعلية للجوال */
@media (max-width: 768px) {
  button, [role="button"] {
    min-height: 44px;
    min-width: 44px;
  }
  
  button:active {
    transform: scale(0.98);
    transition: transform 0.1s;
  }
}

/* تحسينات للنصوص على الجوال */
@media (max-width: 640px) {
  p, span, div {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }
}

/* منع التمرير الأفقي */
* {
  max-width: 100%;
}

/* تحسينات للشريط الثابت في الأسفل */
.fixed {
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

/* تحسينات للـ breadcrumb */
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>