<template>
  <section class="py-16 ">
    <div class="container mx-auto px-2">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold mb-4 text-gray-800">
          {{ translate('popularMeasures.title') }}
        </h2>
        <p class="text-gray-600 max-w-2xl mx-auto">
          {{ translate('popularMeasures.desc') }}
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="measure in measures" :key="measure.id"
          class="group bg-white rounded-xl shadow-lg overflow-hidden cursor-pointer border border-gray-200 transition-all duration-300 hover:shadow-xl flex flex-col h-full"
          :class="{ 'opacity-60': !measure.is_active }" @click="$emit('measure-click', measure)">
          
          <!-- صورة المقياس -->
          <div class="h-40 bg-gray-100 relative overflow-hidden">
            <img 
              :src="getMeasureImage(measure)" 
              :alt="getTranslatedTitle(measure)"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
              @error="handleImageError"
            />
            
            <!-- شارة الحالة -->

          </div>

          <!-- محتوى المقياس -->
          <div class="p-5 flex-1 flex flex-col">
            <!-- العنوان -->
            <h3 class="text-lg font-semibold mb-4 text-gray-800 line-clamp-2 flex items-center gap-2">
              <i :class="getCategoryIcon(measure)" class="text-primary-green"></i>
              {{ getTranslatedTitle(measure) }}
            </h3>

            <!-- إحصائيات المقياس -->
            <div class="flex justify-between text-sm text-gray-500 mt-auto mb-3">
              <div class="flex items-center gap-1">
                <i class="fas fa-question-circle text-primary-green"></i>
                <span>
                  {{ measure.questions_count || measure.questions?.length || 0 }}
                  {{ translate('popularMeasures.questions') }}
                </span>
              </div>
              <div class="flex items-center gap-1">
                <i class="fas fa-clock text-primary-green"></i>
                <span>
                  {{ getEstimatedTime(measure) }}
                  {{ translate('popularMeasures.minutes') }}
                </span>
              </div>
            </div>
          </div>

          <!-- زر البدء -->
          <div class="px-5 pb-5">
            <button :disabled="!measure.is_active"
              class="w-full py-2.5 bg-primary-green text-white rounded-lg hover:bg-secondary-green transition-colors text-sm font-medium disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2">
              <i class="fas fa-play-circle"></i>
              {{ measure.is_active ? translate('popularMeasures.start') : translate('popularMeasures.unavailable') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { useTranslations } from '@/composables/useTranslations'

export default {
  name: 'PopularMeasures',
  props: {
    measures: {
      type: Array,
      default: () => []
    },
    language: {
      type: String,
      default: "ar"
    }
  },
  emits: ['measure-click'],
  setup(props) {
    const { translate } = useTranslations()

    const getTranslatedTitle = (measure) => {
      if (props.language === 'ar') {
        return measure.name_ar || measure.name_en || 'بدون عنوان';
      }
      return measure.name_en || measure.name_ar || 'No Title';
    };

    const getCategoryIcon = (measure) => {
      const categoryName = measure.category?.name_ar?.toLowerCase();
      const icons = {
        'نساء': 'fas fa-female',
        'أطفال': 'fas fa-child',
        'متخصصين': 'fas fa-user-md',
        'رجال': 'fas fa-male',
        'صحة نفسية': 'fas fa-brain',
        'عائلي': 'fas fa-home',
        'اجتماعي': 'fas fa-users',
        'تعليمي': 'fas fa-graduation-cap'
      };
      
      const categoryNameEn = measure.category?.name_en?.toLowerCase();
      if (categoryNameEn) {
        const enIcons = {
          'women': 'fas fa-female',
          'children': 'fas fa-child',
          'specialists': 'fas fa-user-md',
          'men': 'fas fa-male',
          'mental health': 'fas fa-brain',
          'family': 'fas fa-home',
          'social': 'fas fa-users',
          'educational': 'fas fa-graduation-cap'
        };
        return enIcons[categoryNameEn] || 'fas fa-chart-bar';
      }
      
      return icons[categoryName] || 'fas fa-chart-bar';
    };

    const getEstimatedTime = (measure) => {
      if (measure.estimated_time) return measure.estimated_time
      if (measure.time) return measure.time

      const questionsCount = measure.questions_count || measure.questions?.length || 0
      if (!questionsCount) return 5
      return Math.max(5, Math.min(20, Math.ceil(questionsCount * 0.8)))
    };

    const getMeasureImage = (measure) => {
      // أولوية الصور من الأكثر تحديداً إلى الأقل
      if (measure.image_url) return measure.image_url
      if (measure.image) return measure.image
      if (measure.category?.image_url) return measure.category.image_url
      if (measure.category?.image) return measure.category.image
      
      // صور افتراضية حسب الفئة
      const defaultImages = {
        'نساء': '/images/measures/women.jpg',
        'women': '/images/measures/women.jpg',
        'أطفال': '/images/measures/children.jpg',
        'children': '/images/measures/children.jpg',
        'متخصصين': '/images/measures/specialists.jpg',
        'specialists': '/images/measures/specialists.jpg',
        'رجال': '/images/measures/men.jpg',
        'men': '/images/measures/men.jpg',
        'صحة نفسية': '/images/measures/mental-health.jpg',
        'mental health': '/images/measures/mental-health.jpg',
        'عائلي': '/images/measures/family.jpg',
        'family': '/images/measures/family.jpg',
        'اجتماعي': '/images/measures/social.jpg',
        'social': '/images/measures/social.jpg',
        'تعليمي': '/images/measures/educational.jpg',
        'educational': '/images/measures/educational.jpg'
      };
      
      const categoryName = measure.category?.name_ar?.toLowerCase();
      const categoryNameEn = measure.category?.name_en?.toLowerCase();
      
      if (defaultImages[categoryName]) return defaultImages[categoryName]
      if (defaultImages[categoryNameEn]) return defaultImages[categoryNameEn]
      
      // صورة افتراضية عامة
      return '/images/measures/default.jpg'
    };

    const handleImageError = (event) => {
      // عند فشل تحميل الصورة، نستخدم صورة افتراضية
      event.target.src = '/images/measures/default.jpg'
    };

    return {
      translate,
      getTranslatedTitle,
      getCategoryIcon,
      getEstimatedTime,
      getMeasureImage,
      handleImageError
    };
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* تأثير على الصورة */
.group:hover img {
  transform: scale(1.05);
}

/* ظل محسن عند التحويم */
.hover\:shadow-xl:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 
              0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>