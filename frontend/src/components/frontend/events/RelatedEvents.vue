<template>
  <div>
    <!-- فعاليات ذات صلة - تصميم عمودي -->
    <div class="bg-white rounded-2xl shadow p-6">
      <h3 class="text-xl font-bold text-gray-900 mb-4 pb-3 border-b-2 border-primary-green inline-block">
        {{ translate('events.details.relatedEvents') }}
      </h3>
      <div class="space-y-4">
        <article v-for="event in filteredEvents" :key="event.id"
          class="group bg-gray-50 rounded-xl p-4 hover:bg-white hover:shadow transition-all duration-300 cursor-pointer border border-gray-200"
          @click="handleEventClick(event)">
          <div class="flex gap-4">
            <img :src="event.media" :alt="event.title" class="w-20 h-20 rounded-lg object-cover flex-shrink-0" />
            <div class="flex-1">
              <h4
                class="font-bold text-gray-900 group-hover:text-primary-pink transition-colors duration-300 line-clamp-2 text-sm mb-1">
                {{ event.title }}
              </h4>
              <p class="text-gray-500 text-xs mb-2">
                <i class="fas fa-calendar text-primary-green ml-1"></i>
                {{ event.date }}
              </p>
              <span :class="`inline-block text-xs px-2 py-1 rounded-full ${getCategoryStyle(event.type)}`">
                {{ getTranslatedCategory(event.type) }}
              </span>
            </div>
          </div>
        </article>
      </div>
    </div>

    <!-- رسالة عدم وجود فعاليات ذات صلة -->
    <div v-if="filteredEvents.length === 0" class="text-center py-8">
      <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
      <h3 class="text-lg font-bold text-gray-700 mb-1">{{ translate('events.details.noRelatedEvents') }}</h3>
      <p class="text-gray-500 text-sm">{{ translate('events.details.noRelatedMessage') }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'

// استخدام composable الترجمة
const { currentLanguage, translate } = useTranslations()

// بيانات الفعاليات
const props = defineProps({
  events: {
    type: Array,
    default: () => []
  },
  currentEvent: {
    type: Object,
    default: null
  }
})

// إرسال الأحداث للوالد
const emit = defineEmits(['event-click'])

// الفعاليات المصفاة (استبعاد الفعالية الحالية + نفس التصنيف فقط)
const filteredEvents = computed(() => {
  const currentTypeKey = (props.currentEvent?.type || '').toString().trim().toLowerCase()

  return props.events
    .filter(event => {
      if (event.id === props.currentEvent?.id) return false
      if (!currentTypeKey) return true
      const eventTypeKey = (event.type || '').toString().trim().toLowerCase()
      return eventTypeKey === currentTypeKey
    })
    .slice(0, 3)
})

// دالة للحصول على نمط التصنيف
const getCategoryStyle = (type) => {
  const key = (type || '').toString().trim().toLowerCase()
  const styles = {
    evenings: 'bg-green-100 text-green-700',
    events: 'bg-blue-100 text-blue-700',
    workshops: 'bg-purple-100 text-purple-700',
    'أمسيات': 'bg-green-100 text-green-700',
    'فعاليات': 'bg-blue-100 text-blue-700',
    'ورش عمل': 'bg-purple-100 text-purple-700'
  }
  return styles[key] || 'bg-gray-100 text-gray-700'
}

// دالة لترجمة التصنيف
const getTranslatedCategory = (type) => {
  const key = (type || '').toString().trim().toLowerCase()
  const categories = {
    evenings: translate('events.categories.evenings'),
    events: translate('events.categories.events'),
    workshops: translate('events.categories.workshops'),
    'أمسيات': translate('events.categories.evenings'),
    'فعاليات': translate('events.categories.events'),
    'ورش عمل': translate('events.categories.workshops')
  }
  return categories[key] || type
}

// النقر على فعالية
const handleEventClick = (event) => {
  emit('event-click', event)
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>