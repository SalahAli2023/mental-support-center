<template>
  <div>
    <!-- شبكة الفعاليات -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
      <ArticleCard v-for="event in paginatedEvents" :key="event.id" :article="formatEventForArticleCard(event)"
        :disable-navigation="true" @click="handleEventClick(event)" />
    </div>

    <!-- رسالة عدم وجود نتائج -->
    <div v-if="filteredEvents.length === 0 && !eventStore.loading" class="text-center py-12">
      <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
      <h3 class="text-xl font-bold text-gray-700 mb-2">{{ translate('events.list.noResults') }}</h3>
      <p class="text-gray-500">{{ translate('events.list.noResultsMessage') }}</p>
    </div>

    <!-- Loading State -->
    <div v-if="eventStore.loading" class="text-center py-12">
      <i class="fas fa-spinner fa-spin text-3xl text-primary-green mb-4"></i>
      <p class="text-gray-600">{{ translate('loading') }}</p>
    </div>

    <!-- Pagination -->
    <div v-if="filteredEvents.length > 0 && !eventStore.loading" class="flex justify-center items-center gap-4 py-8">
      <!-- زر الصفحة السابقة -->
      <button @click="previousPage" :disabled="eventStore.currentPage === 1"
        class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
        :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'">
        <i class="fas fa-chevron-right" v-if="currentLanguage === 'ar'"></i>
        <i class="fas fa-chevron-left" v-else></i>
        <span>{{ translate('events.list.previous') }}</span>
      </button>

      <!-- أرقام الصفحات -->
      <div class="flex gap-2">
        <button v-for="page in visiblePages" :key="page" @click="goToPage(page)"
          class="w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300" :class="page === eventStore.currentPage
            ? 'bg-primary-green text-white shadow-1xl'
            : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'">
          {{ page }}
        </button>
      </div>

      <!-- زر الصفحة التالية -->
      <button @click="nextPage" :disabled="eventStore.currentPage === eventStore.totalPages"
        class="flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300"
        :dir="currentLanguage === 'ar' ? 'rtl' : 'ltr'">
        <span>{{ translate('events.list.next') }}</span>
        <i class="fas fa-chevron-left" v-if="currentLanguage === 'ar'"></i>
        <i class="fas fa-chevron-right" v-else></i>
      </button>
    </div>

    <!-- معلومات الصفحة -->
    <div v-if="filteredEvents.length > 0 && !eventStore.loading" class="text-center text-gray-600 text-sm">
      {{ getShowingText() }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import ArticleCard from '@/components/frontend/layouts/ArticleCard.vue'
import { useTranslations } from '@/composables/useTranslations'
import { useEventStore } from '@/stores/events'
import { resolveMediaUrl } from '@/utils/media'
import defaultArticleImage from '@/assets/images/dashboard/images.jpg'

// استخدام composable الترجمة و store
const { currentLanguage, translate } = useTranslations()
const eventStore = useEventStore()

// تعريف الأحداث
const emit = defineEmits(['event-selected'])

// فلترة الفعاليات
const filterCriteria = ref({
  search: '',
  category: 'all'
})

// استلام معايير الفلترة من الوالد
const props = defineProps({
  filter: {
    type: Object,
    default: () => ({ search: '', category: 'all' })
  }
})

// تحميل الفعاليات من API
const loadEvents = async () => {
  try {
    const filters = {}

    if (filterCriteria.value.search) {
      filters.search = filterCriteria.value.search
    }

    if (filterCriteria.value.category && filterCriteria.value.category !== 'all') {
      // تحويل أنواع الفعاليات لتتناسب مع البيانات
      const categoryMap = {
        'evenings': 'أمسيات',
        'events': 'فعاليات',
        'workshops': 'ورش عمل'
      }

      filters.type = categoryMap[filterCriteria.value.category] || filterCriteria.value.category
    }

    filters.locale = currentLanguage.value || 'ar'

    await eventStore.fetchEvents(filters)
  } catch (error) {
    console.error('Error loading events:', error)
  }
}

// مراقبة تغييرات الفلترة
watch(() => props.filter, (newFilter) => {
  filterCriteria.value = { ...newFilter }
  eventStore.currentPage = 1
  loadEvents()
}, { deep: true, immediate: true })

watch(currentLanguage, () => {
  loadEvents()
})

// الفعاليات المصفاة
const filteredEvents = computed(() => {
  return eventStore.events
})

// الفعاليات المعروضة في الصفحة الحالية
const paginatedEvents = computed(() => {
  return filteredEvents.value
})

// إحصائيات التقسيم
const totalPages = computed(() => eventStore.totalPages)
const startIndex = computed(() => {
  const perPage = Number(eventStore.perPage) || 0
  return (eventStore.currentPage - 1) * perPage
})
const endIndex = computed(() => {
  const perPage = Number(eventStore.perPage) || 0
  return Math.min(eventStore.currentPage * perPage, filteredEvents.value.length)
})

// الصفحات المرئية في الـ Pagination
const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const current = eventStore.currentPage
  const delta = 2

  for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) {
    pages.push(i)
  }

  return pages
})

// دالة للحصول على نص عرض النتائج
const getShowingText = () => {
  const total = filteredEvents.value.length
  if (!total) {
    return currentLanguage.value === 'ar'
      ? 'لا توجد فعاليات متاحة حالياً'
      : 'No events available'
  }

  const start = Math.min(startIndex.value + 1, total)
  const end = Math.min(endIndex.value, total)

  if (currentLanguage.value === 'ar') {
    return `عرض ${start}-${end} من أصل ${total} فعالية`
  } else {
    return `Showing ${start}-${end} of ${total} events`
  }
}

// دوال التنقل بين الصفحات
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    eventStore.setPage(page)
    loadEvents()
    scrollToTop()
  }
}

const nextPage = () => {
  if (eventStore.currentPage < totalPages.value) {
    eventStore.currentPage++
    loadEvents()
    scrollToTop()
  }
}

const previousPage = () => {
  if (eventStore.currentPage > 1) {
    eventStore.currentPage--
    loadEvents()
    scrollToTop()
  }
}

// التمرير للأعلى عند تغيير الصفحة
const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// تحويل بيانات الفعالية لتتناسب مع ArticleCard
const stripHtml = (value = '') => {
  return value.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim()
}

const formatEventForArticleCard = (event) => {
  // ترجمة فئة الفعالية حسب اللغة الحالية مع تطبيع النص
  const rawType = (event.type || '').toString().trim()
  const typeKey = rawType.toLowerCase()

  const categoryMap = {
    evenings: translate('events.categories.evenings'),
    events: translate('events.categories.events'),
    workshops: translate('events.categories.workshops'),
    'أمسيات': translate('events.categories.evenings'),
    'فعاليات': translate('events.categories.events'),
    'ورش عمل': translate('events.categories.workshops')
  }

  return {
    id: event.id,
    title: event.title,
    description: stripHtml(event.description),
    image: resolveMediaUrl(event.media, defaultArticleImage),
    category: categoryMap[typeKey] || rawType,
    author:
      event.speakers && event.speakers.length > 0
        ? event.speakers[0].name
        : translate('events.details.speakerDefault'),
    date: event.date,
    duration: event.duration,
    location: event.location,
    speakers: event.speakers,
    readMoreText: translate('buttons.readMore')
  }
}

// معالجة النقر على الفعالية
const handleEventClick = (event) => {
  emit('event-selected', event)
}

// تحميل البيانات عند التهيئة
onMounted(() => {
  loadEvents()
})
</script>