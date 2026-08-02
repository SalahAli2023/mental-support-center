<template>
  <div class="font-almarai">
    <!-- Header -->
    <Header />

    <!-- Hero Section -->
    <Hero :title="therapist.name" :subtitle="translate('therapistProfile.hero.subtitle')" :buttons="heroButtons" />

    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-8">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-16">
        <i class="fas fa-spinner fa-spin text-4xl text-brand-500 mb-4"></i>
        <p class="text-gray-600">جاري تحميل بيانات المعالج...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-16">
        <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
        <p class="text-red-600 mb-4">{{ error }}</p>
        <router-link to="/Specialists"
          class="px-6 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 inline-block">
          العودة إلى قائمة المعالجين
        </router-link>
      </div>

      <!-- Content -->
      <template v-else>
        <!-- Breadcrumbs -->
        <nav class="mb-4 sm:mb-6">
          <ol class="flex items-center space-x-1 sm:space-x-2 space-x-reverse text-xs sm:text-sm">
            <li><router-link to="/" class="text-[#065f46] hover:text-[#047857]">{{
              translate('therapistProfile.breadcrumb.home') }}</router-link></li>
            <li><i class="fas fa-chevron-left text-gray-400 text-xs"></i></li>
            <li><router-link to="/Specialists" class="text-[#065f46] hover:text-[#047857]">{{
              translate('therapistProfile.breadcrumb.therapists') }}</router-link></li>
            <li><i class="fas fa-chevron-left text-gray-400 text-xs"></i></li>
            <li class="text-gray-600">{{ therapist.name || 'المعالج' }}</li>
          </ol>
        </nav>

        <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
          <!-- Main Content -->
          <div class="flex-1">
            <!-- Therapist Profile -->
            <div class="p-4 sm:p-6 bg-white rounded-xl shadow-sm mb-6 sm:mb-8">
              <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                <!-- Profile Image -->
                <div class="flex  sm:justify-start">
                  <img :src="therapist.image || '/images/default-female-avatar.png'" :alt="therapist.name"
                    class="w-24 h-24 sm:w-32 sm:h-32 rounded-lg object-cover ring-4 ring-primary-green"
                    @error="$event.target.src = '/images/default-female-avatar.png'">
                </div>

                <!-- Therapist Info -->
                <div class="flex-1 ">
                  <h1 class="text-2xl sm:text-3xl font-bold text-[#065f46] mb-2">
                    {{ therapist.name }}
                  </h1>
                  <p class="text-lg sm:text-xl text-[#047857] font-semibold mb-3 sm:mb-4">
                    {{ therapist.title }}
                  </p>

                  <!-- Rating -->
                  <div v-if="therapist.rating && therapist.rating > 0"
                    class="flex items-center sm:justify-start gap-3 mb-3 sm:mb-4">
                    <div class="flex">
                      <i v-for="i in 5" :key="i" class="fas fa-star text-sm sm:text-lg"
                        :class="i <= Math.round(therapist.rating) ? 'text-yellow-400' : 'text-gray-300'"></i>
                    </div>
                    <span class="text-[#059669] font-bold text-sm sm:text-lg">
                      {{ parseFloat(therapist.rating).toFixed(1) }} / 5
                      <span v-if="therapist.rating_count && therapist.rating_count > 0"
                        class="text-gray-600 text-sm mr-1">
                        ({{ therapist.rating_count }} {{ translate('therapistProfile.profile.reviews') || 'تقييم' }})
                      </span>
                      <span v-if="therapist.total_sessions && therapist.total_sessions > 0"
                        class="text-gray-600 text-sm mr-1">
                        - {{ therapist.total_sessions }} {{ translate('therapistProfile.profile.sessions') || 'جلسة' }}
                      </span>
                    </span>
                  </div>

                  <!-- No Rating -->
                  <div v-else class="flex items-center sm:justify-start gap-3 mb-3 sm:mb-4">
                    <span class="text-gray-500 text-sm sm:text-base">{{ translate('therapistProfile.profile.noRating')
                      || 'لا يوجد تقييم بعد' }}</span>
                    <span v-if="therapist.total_sessions && therapist.total_sessions > 0"
                      class="text-gray-600 text-sm sm:text-base">
                      - {{ therapist.total_sessions }} {{ translate('therapistProfile.profile.sessions') || 'جلسة' }}
                    </span>
                  </div>

                  <!-- Affiliation -->
                  <p class="text-[#065f46] font-semibold mb-2 text-sm sm:text-base">
                    {{ translate('therapistProfile.profile.affiliation') }}
                  </p>


                  <!-- Session Duration -->
                  <p class="text-[#059669] font-medium mb-3 sm:mb-4 text-sm sm:text-base">
                    {{ translate('therapistProfile.profile.sessionDuration') }} : {{ therapist.session_duration || 45 }}
                    {{ translate('therapistProfile.profile.minutes') }}
                  </p>

                  <!-- Biography -->
                  <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
                    {{ therapist.biography }}
                  </p>
                </div>
              </div>
            </div>

            <!-- About the Expert -->
            <div class="p-4 sm:p-6 bg-white rounded-xl shadow-sm mb-6 sm:mb-8">
              <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-info-circle text-primary-green text-xl"></i>
                <h2 class="text-xl sm:text-2xl font-bold text-[#065f46]">{{
                  translate('therapistProfile.profile.aboutExpert') }}</h2>
              </div>

              <!-- Biography Text -->
              <div class="mb-6" v-if="therapist.biography">
                <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
                  {{ therapist.biography }}
                </p>
              </div>
              <div class="mb-6" v-else>
                <p class="text-gray-500 text-sm sm:text-base italic">
                  لا توجد معلومات متاحة عن الخبير
                </p>
              </div>

              <!-- Qualifications -->
              <div class="mb-6">
                <h3 class="text-lg font-semibold text-[#047857] mb-3">{{
                  translate('therapistProfile.profile.qualifications') }}</h3>
                <ul class="space-y-2" v-if="therapist.qualifications && therapist.qualifications.length > 0 && visibleQualifications.length > 0">
                  <li v-for="(qualification, index) in visibleQualifications" :key="index"
                    class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-primary-green mt-1"></i>
                    <span class="text-gray-700 text-sm sm:text-base">{{ qualification }}</span>
                  </li>
                </ul>
                <div v-else class="text-gray-500 text-sm sm:text-base italic">
                  لا توجد مؤهلات علمية متاحة
                </div>
              </div>

              <div v-if="therapist.qualifications.length > maxVisibleQualifications">
                <a href="#" @click.prevent="toggleAboutMore"
                  class="inline-flex items-center gap-2 font-semibold text-[#047857] hover:text-[#065f46]">
                  {{ showMoreAbout ? translate('therapistProfile.profile.showLess') :
                    translate('therapistProfile.profile.showMore') }}
                  <i :class="['fas', showMoreAbout ? 'fa-chevron-up' : 'fa-chevron-left', 'text-sm']"></i>
                </a>
              </div>
            </div>

            <!-- Testimonials with Responsive Scroll -->
            <div class="p-4 sm:p-6 bg-white rounded-xl shadow-sm">
              <div class="flex items-center justify-between mb-4 sm:mb-6">
                <div class="flex items-center gap-3">
                  <i class="fas fa-star text-primary-pink text-xl"></i>
                  <h2 class="text-xl sm:text-2xl font-bold text-[#065f46]">{{
                    translate('therapistProfile.profile.testimonials') }}</h2>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500 sm:hidden">
                  <i class="fas fa-arrows-left-right text-xs"></i>
                  <span>{{ translate('therapistProfile.profile.swipeHint') }}</span>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500">
                  <i class="fas fa-mouse text-xs"></i>
                  <span>{{ translate('therapistProfile.profile.scrollHint') }}</span>
                </div>
              </div>

              <!-- Responsive Scroll Container -->
              <div class="relative">
                <div v-if="therapist.reviews.length === 0" class="text-center py-12 text-gray-500">
                  لا توجد تقييمات حتى الآن
                </div>

                <template v-else>
                  <!-- Mobile: Horizontal Scroll -->
                  <div class="sm:hidden overflow-x-auto pb-6 scrollbar-hide">
                    <div class="flex gap-4 min-w-max">
                      <div v-for="review in therapist.reviews" :key="review.id"
                        class="rounded-lg p-4 hover:shadow-sm transition-shadow w-80 flex-shrink-0 bg-gray-50">
                        <div class="flex items-center gap-2 mb-3">
                          <div class="w-8 h-8 bg-primary-green rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-xs"></i>
                          </div>
                          <div>
                            <span class="text-[#065f46] font-semibold text-sm block">{{ review.user }}</span>
                            <span class="text-gray-500 text-xs">{{ formatReviewDate(review.date) }}</span>
                          </div>
                        </div>

                        <div class="flex mb-3">
                          <i v-for="i in 5" :key="i" class="fas fa-star text-xs"
                            :class="i <= review.rating ? 'text-primary-pink' : 'text-gray-300'"></i>
                        </div>

                        <p class="text-gray-700 mb-3 text-sm leading-relaxed line-clamp-3">
                          "{{ review.comment || translate('therapistProfile.profile.noComment') || 'لا توجد ملاحظات' }}"
                        </p>

                        <div class="flex justify-between items-center">
                          <div class="flex gap-1">
                            <i class="fas fa-quote-right text-primary-green text-xs"></i>
                            <i class="fas fa-quote-right text-primary-green text-xs"></i>
                          </div>
                          <span class="text-xs text-gray-400">{{ formatReviewDate(review.date) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Desktop: Vertical Scroll Layout -->
                  <div class="hidden sm:block max-h-96 overflow-y-auto pr-2 scrollbar-custom">
                    <div class="space-y-4">
                      <div v-for="review in therapist.reviews" :key="review.id"
                        class="rounded-lg p-4 hover:shadow-sm transition-shadow bg-gray-50">
                        <div class="flex items-center gap-2 mb-3">
                          <div class="w-8 h-8 bg-primary-green rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-xs"></i>
                          </div>
                          <div>
                            <span class="text-[#065f46] font-semibold text-sm block">{{ review.user }}</span>
                            <span class="text-gray-500 text-xs">{{ formatReviewDate(review.date) }}</span>
                          </div>
                        </div>

                        <div class="flex mb-3">
                          <i v-for="i in 5" :key="i" class="fas fa-star text-xs"
                            :class="i <= review.rating ? 'text-primary-pink' : 'text-gray-300'"></i>
                        </div>

                        <p class="text-gray-700 mb-3 text-sm leading-relaxed">
                          "{{ review.comment || translate('therapistProfile.profile.noComment') || 'لا توجد ملاحظات' }}"
                        </p>

                        <div class="flex justify-between items-center">
                          <div class="flex gap-1">
                            <i class="fas fa-quote-right text-primary-green text-xs"></i>
                            <i class="fas fa-quote-right text-primary-green text-xs"></i>
                          </div>
                          <span class="text-xs text-gray-400">{{ formatReviewDate(review.date) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Scroll Indicators (Mobile Only) -->
                  <div class="flex justify-center gap-1 mt-4 sm:hidden">
                    <div v-for="n in Math.ceil(therapist.reviews.length / 2)" :key="n"
                      class="w-2 h-2 rounded-full bg-gray-300 transition-all duration-300"
                      :class="{ 'bg-primary-green w-4': currentTestimonialPage === n }"></div>
                  </div>
                </template>
              </div>
            </div>
          </div>

          <!-- Booking Sidebar - Mobile Optimized -->
          <div class="w-full lg:w-80">
            <div class="sticky top-4 lg:top-8 p-4 sm:p-6 bg-white rounded-xl shadow-sm">
              <div class="mb-4">
                <h2
                  class="text-xl text-center sm:text-2xl font-bold text-[#065f46] bg-primary-green text-white p-3 rounded-lg">
                  {{ translate('therapistProfile.booking.title') }}
                </h2>
                <p class="text-xs sm:text-sm text-[#047857] mt-2 bg-gray-50 p-2 rounded-lg">
                  {{ translate('therapistProfile.booking.subtitle') }}
                </p>
              </div>

              <!-- Calendar - بدون حدود -->
              <div class="mb-6 bg-gray-50 rounded-lg p-3">
                <div class="flex   items-center justify-between mb-4">
                  <button @click="previousMonth"
                    class="p-2 rounded-lg hover:bg-primary-green hover:text-white text-[#065f46] transition-colors">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                  <div class="text-center bg-white rounded-lg px-2 sm:px-4 py-2">
                    <div class="text-sm sm:text-lg font-bold text-gray-800">{{ currentMonthName }}</div>
                    <div class="text-xs sm:text-sm font-medium text-gray-600">{{ currentYear }}</div>
                  </div>
                  <button @click="nextMonth"
                    class="p-2 rounded-lg hover:bg-primary-green hover:text-white text-[#065f46] transition-colors">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                </div>

                <!-- Days of week -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                  <div v-for="day in translatedDaysOfWeek" :key="day"
                    class="py-1 sm:py-2 text-xs sm:text-sm text-center text-gray-600 font-bold">
                    {{ day }}
                  </div>
                </div>

                <!-- Calendar days -->
                <div class="grid grid-cols-7 gap-1">
                  <button v-for="day in calendarDays" :key="day.date" @click="selectDate(day.date)" :class="[
                    'p-1 sm:p-2 text-xs sm:text-sm rounded-lg   transition-all font-medium',
                    day.isCurrentMonth
                      ? day.isSelected
                        ? 'bg-primary-green text-white shadow-sm'
                        : day.isToday
                          ? 'bg-gray-300 text-gray-800'
                          : 'text-gray-700 hover:bg-gray-200'
                      : 'text-gray-300',
                    day.isSelected ? 'ring-2 ring-primary-green' : ''
                  ]">
                    {{ day.day }}
                  </button>
                </div>
              </div>

              <!-- Time Slots - بدون حدود -->
              <div v-if="selectedDate" class="bg-gray-50 rounded-lg p-3 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-bold text-[#065f46]">{{ translate('therapistProfile.booking.chooseTime') }}
                  </h3>
                  <span class="text-xs sm:text-sm text-gray-600 bg-gray-200 px-2 py-1 rounded-full">
                    {{ translate('therapistProfile.booking.duration') }}
                  </span>
                </div>
                <div class="grid grid-cols-1 gap-2 max-h-40 sm:max-h-60 overflow-y-auto">
                  <button v-for="slot in timeSlots" :key="slot.time" @click="selectTimeSlot(slot)" :class="[
                    'p-2 sm:p-3 rounded-lg transition-all flex items-center justify-between',
                    slot === selectedTimeSlot
                      ? 'bg-primary-green text-white shadow-sm'
                      : 'bg-white hover:bg-gray-100'
                  ]">
                    <div class="flex items-center gap-2">
                      <i class="far fa-clock text-gray-600 text-xs sm:text-sm"></i>
                      <span class="font-semibold text-gray-800 text-xs sm:text-sm">{{ slot.time }}</span>
                    </div>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ slot.duration }}</span>
                  </button>
                </div>

                <!-- Book Button -->
                <button v-if="selectedTimeSlot" @click="bookAppointment" :disabled="bookingLoading"
                  class="w-full mt-4 bg-primary-green hover:bg-[#8cad35] text-white py-3 rounded-xl font-bold shadow-sm hover:shadow transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                  <div class="flex items-center justify-center gap-2">
                    <i v-if="!bookingLoading" class="fas fa-calendar-check"></i>
                    <i v-else class="fas fa-spinner fa-spin"></i>
                    <span v-if="!bookingLoading">{{ translate('therapistProfile.booking.confirmBooking') }}</span>
                    <span v-else>جاري الحجز...</span>
                    <i v-if="!bookingLoading" class="fas fa-arrow-left text-sm"></i>
                  </div>
                </button>
              </div>

              <!-- No Selection Message -->
              <div v-if="!selectedDate" class="text-center p-4 bg-gray-50 rounded-lg">
                <i class="fas fa-calendar-day text-2xl text-gray-400 mb-2"></i>
                <p class="text-gray-600 text-sm">{{ translate('therapistProfile.booking.noDateSelected') }}</p>

              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- Footer -->
    <Footer />
  </div>
</template>


<script>
import Header from '@/components/frontend/layouts/header.vue'
import Footer from '@/components/frontend/layouts/footer.vue'
import Hero from '@/components/frontend/layouts/hero.vue'
import { useTranslations } from '@/composables/useTranslations'
import { inject, computed } from 'vue'
import { resolveMediaUrl } from '@/utils/media'

export default {
  name: 'TherapistProfile',
  components: {
    Header,
    Footer,
    Hero
  },
  props: {
    id: {
      type: String,
      required: true
    }
  },
  setup() {
    const { translate } = useTranslations()
    const { currentLanguage } = inject('languageState')

    const isRTL = computed(() => currentLanguage.value === 'ar')

    // استخدام computed للترجمة الديناميكية
    const heroButtons = computed(() => [
      {
        text: translate('buttons.startJourney'),
        icon: 'fas fa-play-circle',
        primary: true
      },
      {
        text: translate('buttons.learnMore'),
        icon: 'fas fa-info-circle',
        primary: false
      }
    ])

    return {
      translate,
      isRTL,
      heroButtons,
      currentLanguage
    }
  },
  data() {
    return {
      showMoreAbout: false,
      maxVisibleQualifications: 2,
      selectedDate: null,
      selectedTimeSlot: null,
      currentMonth: new Date().getMonth(),
      currentYear: new Date().getFullYear(),
      currentTestimonialPage: 1,
      timeSlots: [],
      therapist: {
        id: null,
        name: '',
        title: '',
        image: '',
        rating: null,
        rating_count: 0,
        total_sessions: 0,
        session_duration: 45,
        biography: '',
        qualifications: [],
        reviews: [],
        schedules: []
      },
      loading: false,
      error: null,
      bookingLoading: false
    }
  },
  computed: {
    visibleQualifications() {
      if (!this.therapist.qualifications || !Array.isArray(this.therapist.qualifications)) {
        return [];
      }
      if (this.showMoreAbout) return this.therapist.qualifications;
      return this.therapist.qualifications.slice(0, this.maxVisibleQualifications);
    },
    currentMonthName() {
      const months = this.translate('therapistProfile.calendar.months');
      // إذا كانت months نصاً، قم بتحويله إلى مصفوفة
      if (typeof months === 'string') {
        return months.split(',')[this.currentMonth];
      }
      return months[this.currentMonth];
    },
    translatedDaysOfWeek() {
      const days = this.translate('therapistProfile.calendar.daysOfWeek');
      // إذا كانت days نصاً، قم بتحويله إلى مصفوفة
      if (typeof days === 'string') {
        return days.split(',');
      }
      // إذا كانت days مصفوفة، استخدمها
      if (Array.isArray(days)) {
        return days;
      }
      // Fallback إلى أيام الأسبوع بالعربية
      return ['سبت', 'أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة'];
    },
    calendarDays() {
      const year = this.currentYear;
      const month = this.currentMonth;
      const firstDay = new Date(year, month, 1);
      const lastDay = new Date(year, month + 1, 0);
      const startDate = new Date(firstDay);
      startDate.setDate(startDate.getDate() - (firstDay.getDay() + 2) % 7);

      const days = [];
      const today = new Date();

      for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);

        days.push({
          date: date.toISOString().split('T')[0],
          day: date.getDate(),
          isCurrentMonth: date.getMonth() === month,
          isToday: date.toDateString() === today.toDateString(),
          isSelected: this.selectedDate === date.toISOString().split('T')[0]
        });
      }

      return days;
    }
  },
  methods: {
    toggleAboutMore() {
      this.showMoreAbout = !this.showMoreAbout;
    },
    selectDate(date) {
      this.selectedDate = date;
      this.selectedTimeSlot = null;
      // تحديث timeSlots بناءً على اليوم المحدد
      this.updateTimeSlotsForDate(date);
    },
    updateTimeSlotsForDate(date) {
      console.log('updateTimeSlotsForDate called with date:', date);
      
      if (!date || !this.therapist.schedules || this.therapist.schedules.length === 0) {
        console.log('No date or schedules available', {
          date,
          hasSchedules: !!this.therapist.schedules,
          schedulesLength: this.therapist.schedules?.length || 0
        });
        this.timeSlots = [];
        return;
      }

      // الحصول على يوم الأسبوع من التاريخ
      // استخدام UTC لتجنب مشاكل المنطقة الزمنية
      let dateObj;
      
      if (date.includes('T')) {
        // إذا كان التاريخ يحتوي على وقت
        dateObj = new Date(date);
      } else {
        // إذا كان التاريخ بصيغة YYYY-MM-DD فقط
        // استخدام UTC لتجنب مشاكل المنطقة الزمنية
        const [year, month, day] = date.split('-').map(Number);
        dateObj = new Date(Date.UTC(year, month - 1, day));
      }
      
      // التحقق من صحة التاريخ
      if (isNaN(dateObj.getTime())) {
        console.error('Invalid date:', date);
        this.timeSlots = [];
        return;
      }
      
      const dayOfWeek = dateObj.getUTCDay(); // استخدام UTC لتجنب مشاكل المنطقة الزمنية
      
      console.log('Date info:', {
        date,
        dateObj: dateObj.toISOString(),
        dayOfWeek,
        dayName: ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'][dayOfWeek]
      });
      
      // تحويل يوم الأسبوع إلى اسم اليوم (JavaScript getDay() يرجع 0 للأحد)
      // لكن في قاعدة البيانات الأيام: saturday, sunday, monday, tuesday, wednesday, thursday, friday
      const dayNames = {
        0: 'sunday',    // الأحد
        1: 'monday',    // الاثنين
        2: 'tuesday',   // الثلاثاء
        3: 'wednesday', // الأربعاء
        4: 'thursday', // الخميس
        5: 'friday',    // الجمعة
        6: 'saturday'   // السبت
      };
      
      const dayName = dayNames[dayOfWeek];
      
      console.log('Looking for schedules for day:', dayName);
      console.log('All schedules:', this.therapist.schedules);
      
      // البحث عن الجداول الزمنية لهذا اليوم
      const daySchedules = this.therapist.schedules.filter(schedule => {
        const matches = schedule.day === dayName && schedule.available !== false;
        console.log('Schedule check:', {
          scheduleDay: schedule.day,
          targetDay: dayName,
          available: schedule.available,
          matches
        });
        return matches;
      });
      
      console.log('Found schedules for', dayName, ':', daySchedules);
      
      if (daySchedules.length === 0) {
        console.warn('No schedules found for day:', dayName);
        this.timeSlots = [];
        return;
      }
      
      // تحويل الجداول إلى timeSlots
      const slots = [];
      const sessionDuration = this.therapist.session_duration || 45;
      
      daySchedules.forEach(schedule => {
        if (schedule.start_time && schedule.end_time) {
          // تحويل الوقت من 24 ساعة إلى 12 ساعة
          const startTime = this.formatTime12Hour(schedule.start_time);
          const endTime = this.formatTime12Hour(schedule.end_time);
          
          // التحقق من أن التحويل نجح
          if (!startTime || !endTime) {
            console.warn('Failed to format time:', {
              start_time: schedule.start_time,
              end_time: schedule.end_time,
              schedule: schedule
            });
            return; // تخطي هذا الجدول
          }
          
          const duration = schedule.slot_duration || sessionDuration;
          
          // التأكد من أن start_time و end_time بصيغة صحيحة (HH:MM:SS أو HH:MM)
          let startTime24 = String(schedule.start_time).trim();
          let endTime24 = String(schedule.end_time).trim();
          
          // إزالة الثواني إذا كانت موجودة للاستخدام في bookAppointment
          if (startTime24.includes(':')) {
            const startParts = startTime24.split(':');
            startTime24 = `${startParts[0]}:${startParts[1]}`;
          }
          
          if (endTime24.includes(':')) {
            const endParts = endTime24.split(':');
            endTime24 = `${endParts[0]}:${endParts[1]}`;
          }
          
          slots.push({
            time: `${startTime} - ${endTime}`,
            duration: `${duration} ${this.translate('therapistProfile.profile.minutes') || 'دقيقة'}`,
            start_time: startTime24,
            end_time: endTime24
          });
        }
      });
      
      this.timeSlots = slots;
    },
    formatTime12Hour(time24) {
      if (!time24) return '';
      
      // إذا كان الوقت بصيغة 12 ساعة بالفعل، إرجاعه كما هو
      if (typeof time24 === 'string' && (time24.includes('ص') || time24.includes('م'))) {
        return time24;
      }
      
      // تحويل الوقت إلى string إذا لم يكن
      const timeStr = String(time24).trim();
      
      // إذا كان الوقت فارغاً أو غير صالح
      if (!timeStr || timeStr === 'null' || timeStr === 'undefined') {
        return '';
      }
      
      // تقسيم الوقت (قد يكون HH:MM أو HH:MM:SS)
      const timeParts = timeStr.split(':');
      
      if (timeParts.length < 2) {
        console.warn('Invalid time format:', time24);
        return '';
      }
      
      const hour24 = parseInt(timeParts[0], 10);
      const minutes = timeParts[1] || '00';
      
      // التحقق من صحة الأرقام
      if (isNaN(hour24) || hour24 < 0 || hour24 > 23) {
        console.warn('Invalid hour:', hour24, 'from time:', time24);
        return '';
      }
      
      // تحويل من 24 ساعة إلى 12 ساعة
      let hour12 = hour24;
      const period = hour24 >= 12 ? 'م' : 'ص';
      
      if (hour24 === 0) {
        hour12 = 12;
      } else if (hour24 > 12) {
        hour12 = hour24 - 12;
      }
      
      return `${hour12}:${minutes} ${period}`;
    },
    selectTimeSlot(slot) {
      this.selectedTimeSlot = slot;
    },
    previousMonth() {
      if (this.currentMonth === 0) {
        this.currentMonth = 11;
        this.currentYear--;
      } else {
        this.currentMonth--;
      }
    },
    nextMonth() {
      if (this.currentMonth === 11) {
        this.currentMonth = 0;
        this.currentYear++;
      } else {
        this.currentMonth++;
      }
    },
    async bookAppointment() {
      if (!this.selectedDate || !this.selectedTimeSlot) {
        alert(this.translate('therapistProfile.booking.noDateSelected'));
        return;
      }

      // التحقق من تسجيل الدخول للواجهة العامة (استخدام frontend_token و useProfile)
      const frontendToken = localStorage.getItem('frontend_token');
      const { useProfile } = await import('@/composables/useProfile');
      const { user, isAuthenticated } = useProfile();

      // التحقق من وجود token و user
      if (!frontendToken || !isAuthenticated()) {
        if (confirm('يرجى تسجيل الدخول أولاً. هل تريد الانتقال إلى صفحة تسجيل الدخول؟')) {
          this.$router.push('/login');
        }
        return;
      }

      this.bookingLoading = true;

      try {
        // استخدام start_time و end_time من selectedTimeSlot إذا كانت متوفرة
        let startsAt, endsAt;
        
        if (this.selectedTimeSlot.start_time && this.selectedTimeSlot.end_time) {
          // استخدام الأوقات من الجدول الزمني
          // التأكد من أن الوقت بصيغة HH:MM (بدون ثواني)
          let startTime = String(this.selectedTimeSlot.start_time).trim();
          let endTime = String(this.selectedTimeSlot.end_time).trim();
          
          // إزالة الثواني إذا كانت موجودة
          if (startTime.includes(':')) {
            const startParts = startTime.split(':');
            startTime = `${startParts[0].padStart(2, '0')}:${startParts[1].padStart(2, '0')}`;
          }
          
          if (endTime.includes(':')) {
            const endParts = endTime.split(':');
            endTime = `${endParts[0].padStart(2, '0')}:${endParts[1].padStart(2, '0')}`;
          }
          
          // إضافة الثواني إذا لم تكن موجودة
          if (!startTime.includes(':')) {
            startTime = '00:00';
          } else if (startTime.split(':').length === 2) {
            startTime = `${startTime}:00`;
          }
          
          if (!endTime.includes(':')) {
            endTime = '00:00';
          } else if (endTime.split(':').length === 2) {
            endTime = `${endTime}:00`;
          }
          
          const dateTimeString = `${this.selectedDate}T${startTime}`;
          const endDateTimeString = `${this.selectedDate}T${endTime}`;
          
          const startDate = new Date(dateTimeString);
          const endDate = new Date(endDateTimeString);
          
          // التحقق من صحة التاريخ
          if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            console.error('Invalid date/time:', {
              dateTimeString,
              endDateTimeString,
              startTime,
              endTime,
              selectedDate: this.selectedDate
            });
            throw new Error('تاريخ أو وقت غير صالح');
          }
          
          startsAt = startDate.toISOString();
          endsAt = endDate.toISOString();
        } else {
          // تحويل الوقت من صيغة 12:00 م إلى 24 ساعة (fallback)
          const timeStr = this.selectedTimeSlot.time.split(' - ')[0].trim();
          const [time, period] = timeStr.split(' ');
          const [hours, minutes] = time.split(':');
          let hour24 = parseInt(hours);

          if (period === 'م' && hour24 !== 12) {
            hour24 += 12;
          } else if (period === 'ص' && hour24 === 12) {
            hour24 = 0;
          }

          // إنشاء تاريخ ووقت كامل
          const dateTimeString = `${this.selectedDate}T${String(hour24).padStart(2, '0')}:${minutes}:00`;
          startsAt = new Date(dateTimeString).toISOString();

          // حساب وقت الانتهاء (45 دقيقة افتراضياً أو من session_duration)
          const duration = this.therapist.session_duration || 45;
          endsAt = new Date(new Date(startsAt).getTime() + duration * 60000).toISOString();
        }

        // إرسال طلب API
        const api = (await import('@/utils/api')).default;
        const response = await api.post('/appointments', {
          therapist_id: this.therapist.id,
          starts_at: startsAt,
          ends_at: endsAt,
          notes: `حجز من الموقع العام - ${this.selectedTimeSlot.time}`
        });

        if (response.data) {
          const dateObj = new Date(this.selectedDate);
          const formattedDate = dateObj.toLocaleDateString('ar-SA');

          const successMessage = `${this.translate('therapistProfile.booking.bookingSuccess')}\n${this.translate('therapistProfile.booking.date')}: ${formattedDate}\n${this.translate('therapistProfile.booking.time')}: ${this.selectedTimeSlot.time}\n${this.translate('therapistProfile.profile.sessionDuration')}: ${this.selectedTimeSlot.duration}`;

          alert(successMessage);

          // Reset selection
          this.selectedDate = null;
          this.selectedTimeSlot = null;
        }
      } catch (error) {
        console.error('Error booking appointment:', error);
        const errorMessage = error.response?.data?.message || error.message || 'حدث خطأ أثناء الحجز';
        alert('فشل الحجز: ' + errorMessage);
      } finally {
        this.bookingLoading = false;
      }
    },
    updateTestimonialPage() {
      const scrollContainer = this.$el.querySelector('.overflow-x-auto');
      if (scrollContainer) {
        const scrollLeft = scrollContainer.scrollLeft;
        const containerWidth = scrollContainer.clientWidth;
        this.currentTestimonialPage = Math.floor(scrollLeft / containerWidth) + 1;
      }
    },
    formatReviewDate(dateString) {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },
    async fetchTherapist() {
      // الحصول على ID من props أو route
      const therapistId = this.id || this.$route.params.id
      if (!therapistId) {
        this.error = 'معرف المعالج غير موجود'
        return
      }

      this.loading = true
      this.error = null

      try {
        const api = (await import('@/utils/api')).default
        const response = await api.get(`/therapists/${therapistId}`)

        console.log('Therapist API Response:', response.data)
        console.log('Full API Response:', JSON.stringify(response.data, null, 2))
        
        // التحقق من وجود schedules في الـ response
        if (response.data?.data?.schedules) {
          console.log('Schedules in API response:', response.data.data.schedules)
          console.log('Schedules type:', typeof response.data.data.schedules)
          console.log('Is array?', Array.isArray(response.data.data.schedules))
        } else {
          console.warn('No schedules in API response!')
          console.warn('Response data structure:', Object.keys(response.data?.data || {}))
        }

        if (response.data?.success && response.data?.data) {
          const therapistData = response.data.data
          // الحصول على اللغة من setup() أو localStorage
          const savedLang = localStorage.getItem('preferredLanguage') || 'ar'
          const lang = this.currentLanguage?.value || savedLang

          // تحويل بيانات المؤهلات
          console.log('Qualifications from API:', therapistData.qualifications)
          console.log('Qualifications type:', typeof therapistData.qualifications)
          console.log('Is array?', Array.isArray(therapistData.qualifications))
          
          const qualifications = []
          
          // التحقق من وجود المؤهلات
          if (therapistData.qualifications) {
            // تحويل collection إلى array إذا لزم الأمر
            const qualsArray = Array.isArray(therapistData.qualifications) 
              ? therapistData.qualifications 
              : Object.values(therapistData.qualifications)
            
            console.log('Quals array:', qualsArray)
            console.log('Quals array length:', qualsArray.length)
            
            qualsArray.forEach((q, index) => {
              console.log(`Qualification ${index}:`, q)
              
              const qualName = lang === 'ar'
                ? (q.name_ar || q.name_en || '')
                : (q.name_en || q.name_ar || '')
              const institution = lang === 'ar'
                ? (q.institution_ar || q.institution_en || '')
                : (q.institution_en || q.institution_ar || '')
              const year = q.year ? ` - ${q.year}` : ''
              
              console.log(`Qual ${index} processed:`, {
                qualName,
                institution,
                year,
                name_ar: q.name_ar,
                name_en: q.name_en,
                institution_ar: q.institution_ar,
                institution_en: q.institution_en
              })
              
              // إضافة المؤهل حتى لو كان الاسم أو المؤسسة فارغة
              if (qualName || institution) {
                const qualText = institution 
                  ? `${qualName}${qualName ? ' - ' : ''}${institution}${year}` 
                  : `${qualName}${year}`
                
                if (qualText.trim() !== '') {
                  qualifications.push(qualText)
                }
              }
            })
          }
          
          console.log('Processed qualifications:', qualifications)
          console.log('Processed qualifications count:', qualifications.length)
          console.log('Biography from API:', {
            bio_ar: therapistData.bio_ar,
            bio_en: therapistData.bio_en,
            selected_lang: lang
          })

          const reviews = Array.isArray(therapistData.reviews)
            ? therapistData.reviews.map(review => ({
              id: review.id,
              user: review.client?.name || 'مراجع',
              rating: review.rating,
              comment: review.comment,
              date: review.created_at,
              avatar: resolveMediaUrl(review.client?.avatar, '')
            }))
            : []

          this.therapist = {
            id: therapistData.id,
            name: lang === 'ar'
              ? (therapistData.name_ar || therapistData.name_en || '')
              : (therapistData.name_en || therapistData.name_ar || ''),
            title: lang === 'ar'
              ? (therapistData.title_ar || therapistData.title_en || '')
              : (therapistData.title_en || therapistData.title_ar || ''),
            image: (() => {
              const avatarPath = therapistData.avatar || therapistData.user?.avatar;
              console.log('Therapist avatar path:', {
                therapistData_avatar: therapistData.avatar,
                user_avatar: therapistData.user?.avatar,
                final_path: avatarPath,
                resolved_url: resolveMediaUrl(avatarPath, '/images/default-female-avatar.png')
              });
              return resolveMediaUrl(avatarPath, '/images/default-female-avatar.png');
            })(),
            rating: therapistData.rating ? parseFloat(therapistData.rating) : null,
            rating_count: therapistData.rating_count || 0,
            total_sessions: therapistData.total_sessions || 0,
            session_duration: therapistData.session_duration || 45,
            biography: lang === 'ar'
              ? (therapistData.bio_ar || therapistData.bio_en || '')
              : (therapistData.bio_en || therapistData.bio_ar || ''),
            qualifications: qualifications,
            reviews,
            schedules: Array.isArray(therapistData.schedules) ? therapistData.schedules : []
          }
          
          console.log('Therapist loaded:', this.therapist)
          console.log('Biography:', this.therapist.biography)
          console.log('Qualifications:', this.therapist.qualifications)
          console.log('Qualifications count:', this.therapist.qualifications.length)
          console.log('Schedules data:', this.therapist.schedules)
          console.log('Schedules count:', this.therapist.schedules.length)
          
          // عرض عينة من الجداول للتحقق
          if (this.therapist.schedules.length > 0) {
            console.log('All schedules details:')
            this.therapist.schedules.forEach((schedule, index) => {
              console.log(`Schedule ${index}:`, {
                day: schedule.day,
                start_time: schedule.start_time,
                end_time: schedule.end_time,
                available: schedule.available,
                start_time_type: typeof schedule.start_time,
                end_time_type: typeof schedule.end_time,
                full_schedule: schedule
              })
            })
          } else {
            console.warn('No schedules found in therapist data')
          }
          
          // تحديث الجداول الزمنية إذا كان هناك تاريخ محدد
          if (this.selectedDate) {
            this.updateTimeSlotsForDate(this.selectedDate);
          }
        } else {
          this.error = 'المعالج غير موجود'
        }
      } catch (error) {
        console.error('Error fetching therapist:', error)
        this.error = 'فشل تحميل بيانات المعالج'
      } finally {
        this.loading = false
      }
    }
  },
  watch: {
    // إعادة جلب البيانات عند تغيير ID في الـ route
    '$route.params.id': {
      handler(newId) {
        if (newId) {
          this.fetchTherapist()
        }
      },
      immediate: false
    }
  },
  async mounted() {
    // جلب بيانات المعالج من API
    await this.fetchTherapist()

    // Add scroll event listener for testimonials (mobile only)
    const scrollContainer = this.$el.querySelector('.overflow-x-auto');
    if (scrollContainer) {
      scrollContainer.addEventListener('scroll', this.updateTestimonialPage);
    }
  },
  beforeUnmount() {
    // Remove event listener
    const scrollContainer = this.$el.querySelector('.overflow-x-auto');
    if (scrollContainer) {
      scrollContainer.removeEventListener('scroll', this.updateTestimonialPage);
    }
  }
}
</script>

<style scoped>
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

.scrollbar-custom {
  scrollbar-width: thin;
  scrollbar-color: #9EBF3B #f1f1f1;
}

.scrollbar-custom::-webkit-scrollbar {
  width: 6px;
}

.scrollbar-custom::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
  background: #9EBF3B;
  border-radius: 10px;
}

.scrollbar-custom::-webkit-scrollbar-thumb:hover {
  background: #8cad35;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* تحسينات للشاشات الصغيرة */
@media (max-width: 640px) {
  .min-w-max {
    min-width: max-content;
  }
}
</style>