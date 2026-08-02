<template>
  <div class="transition-all duration-400 ease-[cubic-bezier(0.4,0,0.2,1)] flex flex-col bg-white rounded-2xl overflow-hidden relative shadow-lg h-full hover:-translate-y-4 hover:shadow-2xl group">
    <!-- Gradient Top Border -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-primary-green to-primary-pink opacity-80 transition-all duration-400 ease-in-out z-10 group-hover:opacity-100 group-hover:h-1.5"></div>

    <!-- Image Container -->
    <div class="overflow-hidden h-60 relative bg-gradient-to-br from-gray-100 to-gray-300">
      <div class="absolute bottom-0 left-0 right-0 h-2/5 bg-gradient-to-t from-black/30 to-transparent z-10"></div>
      <img
        :src="measureImage"
        :alt="measureTitle"
        class="w-full h-full object-cover transition-transform duration-600 ease-in-out group-hover:scale-112"
        @error="handleImageError"
      />
      
      <!-- حالة عدم النشاط -->
      <div v-if="!measure.is_active" class="absolute inset-0 bg-black/50 flex items-center justify-center z-20">
        <span class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm font-bold">
          {{ translate('allMeasures.inactive') }}
        </span>
      </div>
    </div>

    <!-- Badge - الفئة -->
    <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full text-xs font-bold bg-white text-gray-700 shadow-lg absolute top-3 right-3 z-10 transition-all duration-300 ease-in-out group-hover:-translate-y-1 group-hover:shadow-xl">
      <i :class="['fas', categoryIcon, 'text-primary-green text-xs']"></i>
      {{ categoryName }}
    </div>

    <!-- Content -->
    <div class="p-7 flex flex-col flex-1">
      <!-- Title -->
      <h3 class="text-xl font-extrabold text-gray-800 mb-3 line-clamp-2 leading-relaxed">{{ measureTitle }}</h3>

      <!-- Description -->
      <p class="text-sm text-gray-500 leading-relaxed mb-4 line-clamp-3">{{ measureDescription }}</p>

      <!-- Stats and Start Button -->
      <div class="flex flex-col gap-4 mt-auto pt-4 border-t border-gray-100">
        <!-- الإحصائيات -->
        <div class="flex justify-between items-center text-xs text-gray-500">
          <div class="flex items-center gap-2">
            <i class="fas fa-question-circle text-primary-green"></i>
            <span>{{ questionsCount }} {{ translate('allMeasures.questions') }}</span>
          </div>
          <div class="flex items-center gap-2">
            <i class="fas fa-clock text-primary-green"></i>
            <span>{{ estimatedTime }} {{ translate('allMeasures.minutes') }}</span>
          </div>
        </div>

        <!-- زر البدء -->
        <button
          :disabled="!measure.is_active"
          class="inline-flex items-center justify-center gap-2 text-white bg-primary-green border-none cursor-pointer text-sm font-bold transition-all duration-300 ease-in-out px-4 py-2.5 rounded-lg hover:gap-3 hover:bg-primary-pink hover:-translate-x-1 disabled:bg-gray-400 disabled:cursor-not-allowed disabled:hover:translate-x-0 w-full"
          @click="$emit('measure-click', measure)"
        >
          <i class="fas fa-play-circle"></i>
          <span>{{ buttonText }}</span>
        </button>
      </div>
    </div>
  </div>
</template>

<!-- <script>
import { computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

export default {
  name: 'AllMeasures',
  props: {
    measure: {
      type: Object,
      required: true
    },
    language: {
      type: String,
      default: 'ar'
    }
  },
  emits: ['measure-click'],
  setup(props) {
    const { translate } = useTranslations()
    
    // بناء رابط الصورة بشكل صحيح - مثل طريقة الـ ArticleCard
    const measureImage = computed(() => {
      // إذا كان هناك صورة في قاعدة البيانات
      if (props.measure.image_url) {
        // إذا كانت الصورة رابط كامل (يبدأ بـ http)
        if (props.measure.image_url.startsWith('http')) {
          return props.measure.image_url
        }
        
        // إذا كانت الصورة مخزنة في storage/public
        // نستخدم نفس منطق الـ API base URL
        const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
        const baseUrl = apiBaseUrl.replace('/api', '')
        
        // إزالة الـ slashes الزائدة
        const imagePath = props.measure.image_url.replace(/^\/+/, '')
        
        return `${baseUrl}/storage/${imagePath}`
      }
      
      // صورة افتراضية إذا لم توجد صورة
      return getDefaultImage()
    })
    
    // عنوان المقياس حسب اللغة
    const measureTitle = computed(() => {
      if (props.language === 'ar') {
        return props.measure.name_ar || props.measure.name_en || translate('allMeasures.noTitle')
      }
      return props.measure.name_en || props.measure.name_ar || translate('allMeasures.noTitle')
    })
    
    // وصف المقياس حسب اللغة
    const measureDescription = computed(() => {
      if (props.language === 'ar') {
        return props.measure.description_ar || props.measure.description_en || translate('allMeasures.noDescription')
      }
      return props.measure.description_en || props.measure.description_ar || translate('allMeasures.noDescription')
    })
    
    // اسم الفئة حسب اللغة
    const categoryName = computed(() => {
      if (!props.measure.category) return translate('allMeasures.general')
      
      if (props.language === 'ar') {
        return props.measure.category.name_ar || props.measure.category.name_en || translate('allMeasures.general')
      }
      return props.measure.category.name_en || props.measure.category.name_ar || translate('allMeasures.general')
    })
    
    // أيقونة الفئة
    const categoryIcon = computed(() => {
      const category = props.measure.category
      if (!category) return 'fa-chart-bar'
      
      const iconMap = {
        'women': 'fa-female',
        'نساء': 'fa-female',
        'children': 'fa-child',
        'أطفال': 'fa-child',
        'specialists': 'fa-user-md',
        'متخصصين': 'fa-user-md',
        'anxiety': 'fa-heart-pulse',
        'قلق': 'fa-heart-pulse',
        'depression': 'fa-cloud-rain',
        'اكتئاب': 'fa-cloud-rain',
        'self-development': 'fa-rocket',
        'تطوير': 'fa-rocket',
        'relationships': 'fa-handshake',
        'علاقات': 'fa-handshake',
        'mental': 'fa-brain',
        'صحة': 'fa-heart'
      }
      
      const categoryName = (category.name_ar + ' ' + category.name_en).toLowerCase()
      
      for (const [key, icon] of Object.entries(iconMap)) {
        if (categoryName.includes(key.toLowerCase())) {
          return icon
        }
      }
      
      return 'fa-chart-bar'
    })
    
    // عدد الأسئلة
    const questionsCount = computed(() => {
      return props.measure.questions_count || props.measure.questions?.length || 0
    })
    
    // الوقت المقدر
    const estimatedTime = computed(() => {
      if (props.measure.estimated_time) return props.measure.estimated_time
      if (props.measure.time) return props.measure.time
      
      // تقدير الوقت بناءً على عدد الأسئلة
      const count = questionsCount.value
      if (!count) return 5
      return Math.max(5, Math.min(20, Math.ceil(count * 0.8)))
    })
    
    // نص الزر حسب حالة المقياس
    const buttonText = computed(() => {
      return props.measure.is_active 
        ? translate('allMeasures.start') 
        : translate('allMeasures.unavailable')
    })
    
    // صورة افتراضية حسب الفئة
    const getDefaultImage = () => {
      const category = props.measure.category
      const defaultImages = {
        'women': 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=800&q=80',
        'children': 'https://images.unsplash.com/photo-1536623975707-c4b3b2af565d?auto=format&fit=crop&w=800&q=80',
        'specialists': 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?auto=format&fit=crop&w=800&q=80'
      }
      
      if (category) {
        const categoryName = (category.name_ar + ' ' + category.name_en).toLowerCase()
        for (const [key, url] of Object.entries(defaultImages)) {
          if (categoryName.includes(key)) {
            return url
          }
        }
      }
      
      return 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?auto=format&fit=crop&w=800&q=80'
    }
    
    // معالجة خطأ تحميل الصورة
    const handleImageError = (event) => {
      event.target.src = getDefaultImage()
    }
    
    return {
      measureImage,
      measureTitle,
      measureDescription,
      categoryName,
      categoryIcon,
      questionsCount,
      estimatedTime,
      buttonText,
      translate,
      handleImageError
    }
  }
}
</script> -->


<script>
import { computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

export default {
  name: 'AllMeasures',
  props: {
    measure: {
      type: Object,
      required: true
    },
    language: {
      type: String,
      default: 'ar'
    }
  },
  emits: ['measure-click'],
  setup(props) {
    const { translate } = useTranslations()
    
    // 🔹 دالة محسّنة للصورة
    const measureImage = computed(() => {
      const imageUrl = props.measure.image_url
      
      // 1. إذا لم توجد صورة → صورة افتراضية
      if (!imageUrl) {
        return getDefaultImage()
      }
      
      // 2. إذا كانت بيانات Base64 (تبدأ بـ data:image)
      if (imageUrl.startsWith('data:image')) {
        return imageUrl
      }
      
      // 3. إذا كانت رابط كامل (يبدأ بـ http)
      if (imageUrl.startsWith('http')) {
        return imageUrl
      }
      
      // 4. إذا كانت مسار تخزين نسبي
      const apiBaseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
      const baseUrl = apiBaseUrl.replace('/api', '')
      const imagePath = imageUrl.replace(/^\/+/, '')
      return `${baseUrl}/storage/${imagePath}`
    })
    
    // عنوان المقياس حسب اللغة
    const measureTitle = computed(() => {
      if (props.language === 'ar') {
        return props.measure.name_ar || props.measure.name_en || translate('allMeasures.noTitle')
      }
      return props.measure.name_en || props.measure.name_ar || translate('allMeasures.noTitle')
    })
    
    // وصف المقياس حسب اللغة
    const measureDescription = computed(() => {
      if (props.language === 'ar') {
        return props.measure.description_ar || props.measure.description_en || translate('allMeasures.noDescription')
      }
      return props.measure.description_en || props.measure.description_ar || translate('allMeasures.noDescription')
    })
    
    // اسم الفئة حسب اللغة
    const categoryName = computed(() => {
      if (!props.measure.category) return translate('allMeasures.general')
      
      if (props.language === 'ar') {
        return props.measure.category.name_ar || props.measure.category.name_en || translate('allMeasures.general')
      }
      return props.measure.category.name_en || props.measure.category.name_ar || translate('allMeasures.general')
    })
    
    // أيقونة الفئة
    const categoryIcon = computed(() => {
      const category = props.measure.category
      if (!category) return 'fa-chart-bar'
      
      const iconMap = {
        'women': 'fa-female',
        'نساء': 'fa-female',
        'children': 'fa-child',
        'أطفال': 'fa-child',
        'specialists': 'fa-user-md',
        'متخصصين': 'fa-user-md',
        'anxiety': 'fa-heart-pulse',
        'قلق': 'fa-heart-pulse',
        'depression': 'fa-cloud-rain',
        'اكتئاب': 'fa-cloud-rain',
        'self-development': 'fa-rocket',
        'تطوير': 'fa-rocket',
        'relationships': 'fa-handshake',
        'علاقات': 'fa-handshake',
        'mental': 'fa-brain',
        'صحة': 'fa-heart'
      }
      
      const categoryName = (category.name_ar + ' ' + category.name_en).toLowerCase()
      
      for (const [key, icon] of Object.entries(iconMap)) {
        if (categoryName.includes(key.toLowerCase())) {
          return icon
        }
      }
      
      return 'fa-chart-bar'
    })
    
    // عدد الأسئلة
    const questionsCount = computed(() => {
      return props.measure.questions_count || props.measure.questions?.length || 0
    })
    
    // الوقت المقدر
    const estimatedTime = computed(() => {
      if (props.measure.estimated_time) return props.measure.estimated_time
      if (props.measure.time) return props.measure.time
      
      const count = questionsCount.value
      if (!count) return 5
      return Math.max(5, Math.min(20, Math.ceil(count * 0.8)))
    })
    
    // نص الزر حسب حالة المقياس
    const buttonText = computed(() => {
      return props.measure.is_active 
        ? translate('allMeasures.start') 
        : translate('allMeasures.unavailable')
    })
    
    // صورة افتراضية حسب الفئة
    const getDefaultImage = () => {
      const category = props.measure.category
      const defaultImages = {
        'women': 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=800&q=80',
        'children': 'https://images.unsplash.com/photo-1536623975707-c4b3b2af565d?auto=format&fit=crop&w=800&q=80',
        'specialists': 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?auto=format&fit=crop&w=800&q=80'
      }
      
      if (category) {
        const categoryName = (category.name_ar + ' ' + category.name_en).toLowerCase()
        for (const [key, url] of Object.entries(defaultImages)) {
          if (categoryName.includes(key)) {
            return url
          }
        }
      }
      
      return 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?auto=format&fit=crop&w=800&q=80'
    }
    
    // معالجة خطأ تحميل الصورة
    const handleImageError = (event) => {
      event.target.src = getDefaultImage()
    }
    
    return {
      measureImage,
      measureTitle,
      measureDescription,
      categoryName,
      categoryIcon,
      questionsCount,
      estimatedTime,
      buttonText,
      translate,
      handleImageError
    }
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

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.group-hover\:scale-112:hover {
  transform: scale(1.12);
}
</style>