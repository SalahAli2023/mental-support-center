<template>
  <div class="space-y-4 p-3 sm:p-4">
    <!-- العنوان والأزرار -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl sm:text-2xl font-semibold text-primary">الفعاليات</h1>
        <p class="text-xs sm:text-sm text-secondary mt-1">إدارة الفعاليات والنشاطات</p>
      </div>
      <Button variant="primary" @click="showCreateForm = true" class="w-full sm:w-auto">
        <i class="fas fa-plus ml-2"></i>
        إضافة فعالية
      </Button>
    </div>

    <!-- رسائل التنبيه -->
    <div v-if="error" class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-lg text-sm">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ error }}</span>
      </div>
    </div>
    <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-600 px-3 py-2 rounded-lg text-sm">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span>{{ successMessage }}</span>
      </div>
    </div>

    <!-- أدوات البحث والتصفية -->
    <Card>
      <div class="space-y-3">
        <!-- بحث -->
        <div class="relative">
          <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-secondary text-sm"></i>
          <input
            v-model="searchQuery"
            type="text"
            class="input w-full pr-10"
            placeholder="ابحث في الفعاليات..."
          />
        </div>

        <!-- الفلاتر -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
          <select v-model="categoryFilter" class="input w-full text-sm">
            <option value="">كل الأنواع</option>
            <option value="أمسيات">أمسيات</option>
            <option value="ورش عمل">ورش عمل</option>
            <option value="فعاليات">فعاليات</option>
            <option value="ندوات">ندوات</option>
          </select>

          <select v-model="statusFilter" class="input w-full text-sm">
            <option value="">كل الحالات</option>
            <option value="active">منشورة</option>
            <option value="inactive">مسودة</option>
          </select>

          <button 
            @click="clearFilters"
            class="btn-outline btn-ghost col-span-2 sm:col-span-1 text-sm"
          >
            <i class="fas fa-times ml-1"></i>
            مسح الفلاتر
          </button>
        </div>
      </div>
    </Card>

    <!-- جدول الفعاليات -->
    <Card>
      <template #header>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
          <div class="text-base sm:text-lg font-semibold text-primary">قائمة الفعاليات</div>
          <div class="text-sm text-secondary">
            الصفحة {{ currentPage }} من {{ totalPages }}
          </div>
        </div>
      </template>

      <!-- Mobile Cards View -->
      <div class="sm:hidden space-y-3">
        <!-- Loading State Mobile -->
        <div v-if="loading" class="py-6 text-center">
          <div class="inline-flex flex-col items-center gap-2">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-500"></div>
            <p class="text-xs text-secondary">جاري تحميل الفعاليات...</p>
          </div>
        </div>

        <!-- Empty State Mobile -->
        <div v-else-if="filteredEvents.length === 0" class="py-8 text-center">
          <div class="flex flex-col items-center justify-center gap-3">
            <div class="w-12 h-12 rounded-full bg-tertiary flex items-center justify-center">
              <i class="fas fa-calendar text-xl text-tertiary"></i>
            </div>
            <div>
              <p class="text-sm font-medium text-primary">لا توجد فعاليات</p>
              <p class="text-xs text-secondary mt-1">لم نتمكن من العثور على فعاليات مطابقة</p>
            </div>
            <Button @click="showCreateForm = true" variant="outline" class="text-xs mt-2">
              <i class="fas fa-plus ml-1"></i>
              إضافة فعالية جديدة
            </Button>
          </div>
        </div>

        <!-- Events Cards Mobile -->
        <div v-else class="space-y-3">
          <div v-for="(event, index) in paginatedEvents" :key="event.id" 
               class="card-secondary p-4 space-y-3">
            <!-- رأس البطاقة -->
            <div class="flex items-start justify-between">
              <!-- الصورة والمعلومات الأساسية -->
              <div class="flex items-start gap-3 flex-1">
                <div class="w-12 h-12 rounded-lg overflow-hidden bg-secondary flex items-center justify-center flex-shrink-0">
                  <img v-if="getEventImage(event)" :src="getEventImage(event)" :alt="event.title_ar"
                    class="w-full h-full object-cover" @error="handleImageError($event)" />
                  <div v-else class="w-full h-full bg-tertiary flex items-center justify-center">
                    <i class="fas fa-calendar text-secondary text-sm"></i>
                  </div>
                </div>
                
                <div class="flex-1">
                  <div class="font-semibold text-primary text-sm">{{ event.title_ar }}</div>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="badge text-xs" :class="typeBadgeClass(event.type)">
                      {{ getTypeLabel(event.type) }}
                    </span>
                    <span class="badge text-xs" :class="event.is_published ? 'badge-success' : 'badge-neutral'">
                      {{ event.is_published ? 'منشور' : 'مسودة' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- رقم التسلسل -->
              <div class="text-xs text-secondary bg-tertiary rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0">
                {{ startIndex + index + 1 }}
              </div>
            </div>

            <!-- تفاصيل الفعالية -->
            <div class="space-y-2 text-sm">
              <!-- التاريخ والوقت -->
              <div class="flex items-center gap-2">
                <i class="fas fa-calendar-alt text-secondary text-xs"></i>
                <span class="text-primary">{{ formatDate(event.date) }}</span>
                <span v-if="event.time" class="text-secondary text-xs">• {{ event.time }}</span>
              </div>

              <!-- الموقع -->
              <div class="flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-secondary text-xs"></i>
                <span class="text-primary text-sm truncate">{{ event.location_ar }}</span>
              </div>
            </div>

            <!-- الأزرار -->
            <div class="flex gap-2 pt-3 border-t border-primary">
              <Button size="sm" variant="ghost" @click="handleEdit(event)" class="flex-1 text-xs">
                <i class="fas fa-edit ml-1"></i>
                تعديل
              </Button>
              <Button size="sm" variant="outline" @click="handleTogglePublish(event)" class="flex-1 text-xs">
                <i class="fas" :class="event.is_published ? 'fa-eye-slash' : 'fa-eye'"></i>
                {{ event.is_published ? 'إخفاء' : 'نشر' }}
              </Button>
              <Button size="sm" variant="outline" @click="handleDelete(event.id)"
                class="flex-1 text-xs text-accent-500 border-accent-500/30 hover:bg-accent-500/10">
                <i class="fas fa-trash ml-1"></i>
                حذف
              </Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Desktop Table View -->
      <div class="hidden sm:block overflow-x-auto -mx-2 sm:mx-0">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-start text-secondary bg-secondary">
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">#</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">الصورة</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">الفعالية</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">النوع</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">التاريخ</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">الموقع</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">الحالة</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loading State Desktop -->
            <tr v-if="loading">
              <td colspan="8" class="px-4 py-8 text-center">
                <div class="flex flex-col items-center justify-center gap-3">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-500"></div>
                  <p class="text-sm text-secondary">جاري تحميل الفعاليات...</p>
                </div>
              </td>
            </tr>

            <!-- Empty State Desktop -->
            <tr v-else-if="filteredEvents.length === 0">
              <td colspan="8" class="px-4 py-8 text-center">
                <div class="flex flex-col items-center justify-center gap-3">
                  <div class="w-12 h-12 rounded-full bg-tertiary flex items-center justify-center">
                    <i class="fas fa-calendar text-xl text-tertiary"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-primary">لا توجد فعاليات</p>
                    <p class="text-xs text-secondary mt-1">لم نتمكن من العثور على فعاليات مطابقة</p>
                  </div>
                  <Button @click="showCreateForm = true" variant="outline" class="text-xs mt-2">
                    <i class="fas fa-plus ml-1"></i>
                    إضافة فعالية جديدة
                  </Button>
                </div>
              </td>
            </tr>

            <!-- Events Rows Desktop -->
            <tr v-else v-for="(event, index) in paginatedEvents" :key="event.id"
                class="border-t border-primary hover:bg-secondary transition-colors">
              <td class="px-3 sm:px-4 py-3 text-primary font-medium text-center">
                {{ startIndex + index + 1 }}
              </td>
              <td class="px-3 sm:px-4 py-3">
                <div class="w-12 h-12 rounded-lg overflow-hidden bg-secondary flex items-center justify-center">
                  <img v-if="getEventImage(event)" :src="getEventImage(event)" :alt="event.title_ar"
                    class="w-full h-full object-cover" @error="handleImageError($event)" />
                  <div v-else class="w-full h-full bg-tertiary flex items-center justify-center">
                    <i class="fas fa-calendar text-secondary text-sm"></i>
                  </div>
                </div>
              </td>
              <td class="px-3 sm:px-4 py-3 text-primary">
                <div class="font-semibold text-sm">{{ event.title_ar }}</div>
                <div class="text-xs text-secondary truncate max-w-[200px]">{{ event.location_ar }}</div>
              </td>
              <td class="px-3 sm:px-4 py-3">
                <span class="badge text-xs" :class="typeBadgeClass(event.type)">
                  {{ getTypeLabel(event.type) }}
                </span>
              </td>
              <td class="px-3 sm:px-4 py-3 text-primary text-sm">
                <div>{{ formatDate(event.date) }}</div>
                <div v-if="event.time" class="text-xs text-secondary">{{ event.time }}</div>
              </td>
              <td class="px-3 sm:px-4 py-3 text-primary text-sm truncate max-w-[150px]">
                {{ event.location_ar }}
              </td>
              <td class="px-3 sm:px-4 py-3">
                <span class="badge text-xs" :class="event.is_published ? 'badge-success' : 'badge-neutral'">
                  {{ event.is_published ? 'منشور' : 'مسودة' }}
                </span>
              </td>
              <td class="px-3 sm:px-4 py-3">
                <div class="flex gap-2">
                  <Button size="sm" variant="ghost" @click="handleEdit(event)" class="text-xs">
                    <i class="fas fa-edit ml-1"></i>
                    تعديل
                  </Button>
                  <Button size="sm" variant="outline" @click="handleTogglePublish(event)" class="text-xs">
                    <i class="fas" :class="event.is_published ? 'fa-eye-slash' : 'fa-eye'"></i>
                  </Button>
                  <Button size="sm" variant="outline" @click="handleDelete(event.id)"
                    class="text-xs text-accent-500 border-accent-500/30 hover:bg-accent-500/10">
                    <i class="fas fa-trash"></i>
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- الترقيم -->
      <div v-if="!loading && filteredEvents.length > 0"
        class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-primary">
        <!-- معلومات الصفحة -->
        <div class="text-sm text-secondary order-2 sm:order-1">
          عرض {{ startIndex + 1 }}-{{ Math.min(startIndex + itemsPerPage, filteredEvents.length) }} من {{ filteredEvents.length }} فعالية
        </div>

        <!-- أزرار التحكم -->
        <div class="flex items-center gap-2 order-1 sm:order-2">
          <!-- السابق -->
          <button 
            @click="changePage(currentPage - 1)" 
            :disabled="currentPage === 1"
            class="btn-outline btn-ghost px-3 py-1.5 text-sm"
            :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
          >
            <i class="fas fa-chevron-right ml-1"></i>
            السابق
          </button>

          <!-- صفحات -->
          <div class="flex gap-1">
            <button 
              v-for="page in visiblePages" 
              :key="page" 
              @click="typeof page === 'number' && changePage(page)"
              :disabled="typeof page !== 'number'"
              class="w-8 h-8 rounded-lg text-sm flex items-center justify-center"
              :class="[
                page === currentPage
                  ? 'bg-brand-500 text-white'
                  : 'border border-primary text-primary hover:bg-secondary',
                typeof page !== 'number' && 'cursor-default'
              ]"
            >
              {{ page }}
            </button>
          </div>

          <!-- التالي -->
          <button 
            @click="changePage(currentPage + 1)" 
            :disabled="currentPage === totalPages"
            class="btn-outline btn-ghost px-3 py-1.5 text-sm"
            :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
          >
            التالي
            <i class="fas fa-chevron-left ml-1"></i>
          </button>
        </div>

        <!-- معلومات الصفحة للشاشات الكبيرة -->
        <div class="hidden sm:block text-sm text-secondary order-3">
          الصفحة {{ currentPage }} من {{ totalPages }}
        </div>
      </div>
    </Card>

    <!-- نموذج إنشاء/تعديل الفعالية -->
    <div v-if="showCreateForm || editingEvent" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3 sm:p-4">
      <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <EventForm 
          :event="editingEvent" 
          @save="handleSave"
          @cancel="handleCancelForm" 
        />
      </div>
    </div>

    <!-- تأكيد الحذف -->
    <DeleteConfirmModal 
      :show="showDeleteConfirm"
      message="هل أنت متأكد من حذف هذه الفعالية؟ لا يمكن التراجع عن هذا الإجراء."
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false" 
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import Button from '@/components/dashboard/component/ui/Button.vue'
import Card from '@/components/dashboard/component/ui/Card.vue'
import EventForm from './EventForm.vue'
import DeleteConfirmModal from '../../../components/dashboard/events/DeleteConfirmModal.vue'
import { useEventStore } from '@/stores/events'
import type { Event } from '@/types/event'
import { resolveMediaUrl } from '@/utils/media'

const eventStore = useEventStore()

// البيانات التفاعلية
const loading = ref(false)
const showCreateForm = ref(false)
const editingEvent = ref<Event | null>(null)
const error = ref('')
const successMessage = ref('')
const showDeleteConfirm = ref(false)
const deleteTargetId = ref<string | null>(null)

// البحث والتصفية
const searchQuery = ref('')
const categoryFilter = ref('')
const statusFilter = ref('')

// الترقيم
const currentPage = ref(1)
const itemsPerPage = ref(10)

// استخدام ref منفصل لتتبع حالة النشر
const publishStates = ref<Record<string, boolean>>({})

// الحوسبة
const events = computed(() => {
  return eventStore.events.map(event => ({
    ...event,
    is_published: publishStates.value[event.id] ?? event.is_published
  }))
})

// تصفية الفعاليات
const filteredEvents = computed(() => {
  return events.value.filter(event => {
    const matchesSearch = !searchQuery.value ||
      event.title_ar.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      event.title_en.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      event.description_ar.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      event.description_en.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesCategory = !categoryFilter.value || event.type === categoryFilter.value

    const matchesStatus = !statusFilter.value ||
      (statusFilter.value === 'active' && event.is_published) ||
      (statusFilter.value === 'inactive' && !event.is_published)

    return matchesSearch && matchesCategory && matchesStatus
  })
})

const totalPages = computed(() => Math.ceil(filteredEvents.value.length / itemsPerPage.value))

const paginatedEvents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredEvents.value.slice(start, end)
})

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value)

const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const current = currentPage.value

  if (total <= 5) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    if (current <= 3) {
      for (let i = 1; i <= 4; i++) pages.push(i)
      pages.push('...')
      pages.push(total)
    } else if (current >= total - 2) {
      pages.push(1)
      pages.push('...')
      for (let i = total - 3; i <= total; i++) pages.push(i)
    } else {
      pages.push(1)
      pages.push('...')
      for (let i = current - 1; i <= current + 1; i++) pages.push(i)
      pages.push('...')
      pages.push(total)
    }
  }
  return pages
})

// Watchers
watch([searchQuery, categoryFilter, statusFilter], () => {
  currentPage.value = 1
})

watch(itemsPerPage, () => {
  currentPage.value = 1
})

// الدوال
const clearFilters = () => {
  searchQuery.value = ''
  categoryFilter.value = ''
  statusFilter.value = ''
}

const fetchEvents = async () => {
  loading.value = true
  error.value = ''
  try {
    await eventStore.fetchEvents()
  } catch (err: any) {
    error.value = 'فشل في تحميل الفعاليات'
    console.error('Failed to fetch events:', err)
  } finally {
    loading.value = false
  }
}

const handleEdit = (event: Event) => {
  editingEvent.value = { ...event }
}

const handleDelete = async (eventId: string) => {
  deleteTargetId.value = eventId
  showDeleteConfirm.value = true
}

const confirmDelete = async () => {
  if (!deleteTargetId.value) return

  loading.value = true
  error.value = ''

  try {
    await eventStore.deleteEvent(deleteTargetId.value)
    delete publishStates.value[deleteTargetId.value]
    successMessage.value = 'تم حذف الفعالية بنجاح'

    setTimeout(() => {
      successMessage.value = ''
    }, 3000)

  } catch (err: any) {
    error.value = 'فشل في حذف الفعالية'
    console.error('Failed to delete event:', err)
  } finally {
    loading.value = false
    showDeleteConfirm.value = false
    deleteTargetId.value = null
  }
}

const handleTogglePublish = async (event: Event) => {
  loading.value = true
  error.value = ''

  const currentState = publishStates.value[event.id] ?? event.is_published
  const newState = !currentState

  try {
    publishStates.value[event.id] = newState

    const formData = new FormData()
    formData.append('is_published', newState ? '1' : '0')
    formData.append('_method', 'PUT')

    await eventStore.updateEvent(event.id, formData)

    successMessage.value = newState ? 'تم نشر الفعالية بنجاح' : 'تم إخفاء الفعالية'

    setTimeout(() => {
      successMessage.value = ''
    }, 3000)

  } catch (err: any) {
    error.value = 'فشل في تحديث حالة الفعالية'
    console.error('Failed to toggle publish:', err)

    delete publishStates.value[event.id]
  } finally {
    loading.value = false
  }
}

const handleSave = async () => {
  error.value = ''
  successMessage.value = 'تم حفظ الفعالية بنجاح'

  setTimeout(() => {
    successMessage.value = ''
  }, 3000)

  await fetchEvents()
  handleCancelForm()
}

const handleCancelForm = () => {
  showCreateForm.value = false
  editingEvent.value = null
}

const changePage = (page: number) => {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}

const getTypeLabel = (type: string) => {
  const typeMap: Record<string, string> = {
    'أمسيات': 'أمسيات',
    'ورش عمل': 'ورش عمل',
    'فعاليات': 'فعاليات',
    'ندوات': 'ندوات'
  }
  return typeMap[type] || type
}

const typeBadgeClass = (type: string) => {
  switch (type) {
    case 'أمسيات':
      return 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
    case 'ورش عمل':
      return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
    case 'ندوات':
      return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'
    default:
      return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
  }
}

const getEventImage = (event: Event) => {
  if (!event.media || !event.media.trim()) {
    return null
  }

  const mediaUrl = event.media.trim()

  if (mediaUrl.startsWith('http://') || mediaUrl.startsWith('https://')) {
    return mediaUrl
  }

  if (mediaUrl.startsWith('/storage/')) {
    const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
    const baseUrl = apiBase.replace('/api', '')
    return `${baseUrl}${mediaUrl}`
  }

  if (mediaUrl.startsWith('storage/')) {
    const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
    const baseUrl = apiBase.replace('/api', '')
    return `${baseUrl}/${mediaUrl}`
  }

  const resolved = resolveMediaUrl(mediaUrl, '')
  if (resolved && !resolved.startsWith('http')) {
    const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
    const baseUrl = apiBase.replace('/api', '')
    return `${baseUrl}${resolved.startsWith('/') ? '' : '/'}${resolved}`
  }
  return resolved || null
}

const handleImageError = (errorEvent: Event) => {
  const target = errorEvent.target as HTMLImageElement | null
  if (target) {
    target.style.display = 'none'
  }
}

const formatDate = (dateString: string) => {
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('ar-EG', {
      month: 'short',
      day: 'numeric'
    })
  } catch (error) {
    return dateString
  }
}

// عند التحميل
onMounted(() => {
  fetchEvents()
})
</script>

<style scoped>
/* تحسينات للجوال */
@media (max-width: 640px) {
  .card-secondary {
    border-radius: 12px;
  }
  
  .input {
    font-size: 16px;
    padding: 12px;
  }
  
  button {
    min-height: 44px;
  }
  
  .max-w-\[200px\] {
    max-width: 200px;
  }
  
  .max-w-\[150px\] {
    max-width: 150px;
  }
}

/* Badge classes */
.badge {
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
}

.badge-success {
  background-color: #10b981;
  color: white;
}

.badge-neutral {
  background-color: var(--bg-secondary);
  color: var(--text-secondary);
  border: 1px solid var(--border-primary);
}

.dark .badge-neutral {
  background-color: var(--bg-secondary);
  color: var(--text-secondary);
  border-color: var(--border-primary);
}

/* تحسين شريط التمرير للجداول */
.overflow-x-auto {
  scrollbar-width: thin;
  scrollbar-color: var(--text-tertiary) transparent;
}

.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: var(--text-tertiary);
  border-radius: 3px;
}

.dark .overflow-x-auto::-webkit-scrollbar-thumb {
  background: var(--text-tertiary);
}
</style>