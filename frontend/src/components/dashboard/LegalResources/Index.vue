<template>
  <div class="space-y-4 p-2 sm:p-4">
    <!-- العنوان والأزرار -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl sm:text-2xl font-semibold text-primary">الموارد القانونية</h1>
        <p class="text-xs sm:text-sm text-secondary mt-1">إدارة المواد والنصوص القانونية</p>
      </div>
      <Button variant="primary" @click="showCreateForm = true" class="w-full sm:w-auto">
        <i class="fas fa-plus ml-2"></i>
        إضافة مورد جديد
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
    <Card class="p-3 sm:p-4">
      <div class="space-y-3 sm:space-y-0 sm:grid sm:grid-cols-3 sm:gap-3">
        <!-- بحث -->
        <div class="relative sm:col-span-1">
          <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-secondary text-sm"></i>
          <input
            v-model="searchQuery"
            type="text"
            class="input w-full pr-10"
            placeholder="ابحث في النصوص القانونية..."
          />
        </div>

        <!-- الفلاتر -->
        <select v-model="typeFilter" class="input w-full text-sm">
          <option value="">كل الأنواع</option>
          <option value="قانون">قانون</option>
          <option value="مرسوم">مرسوم</option>
          <option value="نظام">نظام</option>
          <option value="لائحة">لائحة</option>
        </select>

        <select v-model="categoryFilter" class="input w-full text-sm">
          <option value="">كل التصنيفات</option>
          <option
            v-for="category in categories"
            :key="category.id"
            :value="category.id"
          >
            {{ category.name }}
          </option>
        </select>
      </div>
      
      <!-- زر مسح الفلاتر -->
      <button 
        @click="clearFilters"
        class="text-xs text-secondary hover:text-primary w-full text-center py-2 mt-3 sm:mt-0 sm:w-auto sm:absolute sm:left-4 sm:top-1/2 sm:transform sm:-translate-y-1/2"
      >
        <i class="fas fa-times ml-1"></i>
        مسح الفلاتر
      </button>
    </Card>

    <!-- جدول الموارد -->
    <Card>
      <template #header>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
          <div class="text-base sm:text-lg font-semibold text-primary">قائمة الموارد القانونية</div>
          <div class="text-sm text-secondary">
            الصفحة {{ currentPage }} من {{ totalPages }}
          </div>
        </div>
      </template>

      <!-- Mobile Cards View -->
      <div class="sm:hidden space-y-3">
        <!-- حالة التحميل للجوال -->
        <div v-if="loading" class="py-6 text-center">
          <div class="inline-flex flex-col items-center gap-2">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-brand-500"></div>
            <p class="text-xs text-secondary">جاري التحميل...</p>
          </div>
        </div>

        <!-- لا توجد موارد للجوال -->
        <div v-else-if="filteredResources.length === 0" class="py-8 text-center">
          <div class="flex flex-col items-center justify-center gap-3">
            <div class="w-12 h-12 rounded-full bg-tertiary flex items-center justify-center">
              <i class="fas fa-gavel text-xl text-tertiary"></i>
            </div>
            <div>
              <p class="text-sm font-medium text-primary">لا توجد موارد قانونية</p>
              <p class="text-xs text-secondary mt-1">لم نتمكن من العثور على موارد مطابقة</p>
            </div>
            <Button @click="showCreateForm = true" variant="outline" class="text-xs mt-2">
              <i class="fas fa-plus ml-1"></i>
              إضافة مورد جديد
            </Button>
          </div>
        </div>

        <!-- بطاقات الموارد للجوال -->
        <div v-else class="space-y-3">
          <div v-for="(resource, index) in paginatedResources" :key="resource.id" 
               class="card-secondary p-4 space-y-3">
            <!-- رأس البطاقة -->
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <div class="w-8 h-8 rounded-full bg-brand-500/10 flex items-center justify-center">
                    <i class="fas fa-file-alt text-brand-500 text-sm"></i>
                  </div>
                  <div>
                    <div class="font-semibold text-primary text-sm">المادة {{ resource.article_number_ar }}</div>
                    <div class="text-xs text-secondary">{{ resource.article_number_en }}</div>
                  </div>
                </div>
              </div>

              <!-- رقم التسلسل -->
              <div class="text-xs text-secondary bg-tertiary rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0">
                {{ startIndex + index + 1 }}
              </div>
            </div>

            <!-- تفاصيل المورد -->
            <div class="space-y-2 text-sm">
              <!-- النص -->
              <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-primary line-clamp-3" :title="stripHtml(resource.text_ar)">
                  {{ truncateText(resource.text_ar, 80) }}
                </div>
                <div v-if="resource.text_en" class="text-xs text-secondary mt-2 line-clamp-2" :title="stripHtml(resource.text_en)">
                  {{ truncateText(resource.text_en, 60) }}
                </div>
              </div>

              <!-- النوع والتصنيف -->
              <div class="flex items-center gap-2">
                <span class="badge text-xs" :class="typeBadgeClass(resource.law_type)">
                  {{ resource.law_type || 'بدون نوع' }}
                </span>
                <span class="badge text-xs badge-success">
                  {{ resource.category?.name || 'بدون تصنيف' }}
                </span>
              </div>

              <!-- تاريخ الإضافة -->
              <div class="flex items-center gap-2 text-xs text-secondary">
                <i class="fas fa-calendar"></i>
                {{ formatDate(resource.created_at) }}
              </div>
            </div>

            <!-- الأزرار -->
            <div class="flex gap-2 pt-3 border-t border-primary">
              <Button size="sm" variant="ghost" @click="handleEdit(resource)" class="flex-1 text-xs">
                <i class="fas fa-edit ml-1"></i>
                تعديل
              </Button>
              <Button 
                size="sm" 
                variant="outline" 
                @click="handleDelete(resource.id)" 
                class="flex-1 text-xs text-accent-500 border-accent-500/30 hover:bg-accent-500/10"
              >
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
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">رقم المادة</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">النص القانوني</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">نوع القانون</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">التصنيف</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">تاريخ الإضافة</th>
              <th class="px-3 sm:px-4 py-3 text-start font-semibold text-xs sm:text-sm">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <!-- حالة التحميل لللاب -->
            <tr v-if="loading">
              <td colspan="7" class="px-4 py-8 text-center">
                <div class="flex flex-col items-center justify-center gap-3">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-500"></div>
                  <p class="text-sm text-secondary">جاري تحميل الموارد القانونية...</p>
                </div>
              </td>
            </tr>

            <!-- لا توجد موارد لللاب -->
            <tr v-else-if="filteredResources.length === 0">
              <td colspan="7" class="px-4 py-8 text-center">
                <div class="flex flex-col items-center justify-center gap-3">
                  <div class="w-12 h-12 rounded-full bg-tertiary flex items-center justify-center">
                    <i class="fas fa-gavel text-xl text-tertiary"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-primary">لا توجد موارد قانونية</p>
                    <p class="text-xs text-secondary mt-1">لم نتمكن من العثور على موارد مطابقة</p>
                  </div>
                  <Button @click="showCreateForm = true" variant="outline" class="text-xs mt-2">
                    <i class="fas fa-plus ml-1"></i>
                    إضافة مورد جديد
                  </Button>
                </div>
              </td>
            </tr>

            <!-- صفوف الموارد لللاب -->
            <tr v-else v-for="(resource, index) in paginatedResources" :key="resource.id"
                class="border-t border-primary hover:bg-secondary transition-colors">
              <td class="px-3 sm:px-4 py-3 text-primary font-medium text-center">
                {{ startIndex + index + 1 }}
              </td>
              
              <td class="px-3 sm:px-4 py-3 text-primary">
                <div class="flex flex-col">
                  <span class="font-medium text-primary">{{ resource.article_number_ar }}</span>
                  <span class="text-xs text-secondary">{{ resource.article_number_en }}</span>
                </div>
              </td>
              
              <td class="px-3 sm:px-4 py-3 text-primary">
                <div class="max-w-xs">
                  <div class="text-primary mb-1" :title="stripHtml(resource.text_ar)">
                    {{ truncateText(resource.text_ar, 100) }}
                  </div>
                  <div class="text-xs text-secondary" :title="stripHtml(resource.text_en)">
                    {{ truncateText(resource.text_en, 80) }}
                  </div>
                </div>
              </td>
              
              <td class="px-3 sm:px-4 py-3">
                <span class="badge text-xs" :class="typeBadgeClass(resource.law_type)">
                  {{ resource.law_type || 'بدون نوع' }}
                </span>
              </td>
              
              <td class="px-3 sm:px-4 py-3">
                <span class="badge text-xs badge-success">
                  {{ resource.category?.name || 'بدون تصنيف' }}
                </span>
              </td>
              
              <td class="px-3 sm:px-4 py-3 text-primary text-sm">
                {{ formatDate(resource.created_at) }}
              </td>
              
              <td class="px-3 sm:px-4 py-3">
                <div class="flex gap-2">
                  <Button size="sm" variant="ghost" @click="handleEdit(resource)" class="text-xs">
                    <i class="fas fa-edit ml-1"></i>
                    تعديل
                  </Button>
                  <Button 
                    size="sm" 
                    variant="outline" 
                    @click="handleDelete(resource.id)" 
                    class="text-xs text-accent-500 border-accent-500/30 hover:bg-accent-500/10"
                  >
                    <i class="fas fa-trash"></i>
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- الترقيم -->
      <div v-if="!loading && filteredResources.length > 0"
        class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-primary">
        <!-- معلومات الصفحة -->
        <div class="text-sm text-secondary order-2 sm:order-1">
          عرض {{ startIndex + 1 }}-{{ Math.min(startIndex + itemsPerPage, filteredResources.length) }} من {{ filteredResources.length }} مورد
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

    <!-- نموذج إنشاء/تعديل المورد -->
    <div v-if="showCreateForm || editingResource" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3 sm:p-4">
      <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <LegalResourceForm 
          :resource="editingResource" 
          @save="handleSave"
          @cancel="handleCancelForm" 
        />
      </div>
    </div>

    <!-- تأكيد الحذف -->
    <DeleteConfirmModal 
      :show="showDeleteConfirm"
      message="هل أنت متأكد من حذف هذا المورد القانوني؟ لا يمكن التراجع عن هذا الإجراء."
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false" 
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import Button from '@/components/dashboard/component/ui/Button.vue'
import Card from '@/components/dashboard/component/ui/Card.vue'
import LegalResourceForm from './LegalResourceForm.vue'
import DeleteConfirmModal from './DeleteConfirmModal.vue'
import { useLegalResourceStore } from '@/stores/legalResources'

// استخدام المتجر
const legalResourceStore = useLegalResourceStore()

// البيانات التفاعلية
const loading = ref(false)
const showCreateForm = ref(false)
const editingResource = ref(null)
const error = ref('')
const successMessage = ref('')
const showDeleteConfirm = ref(false)
const deleteTargetId = ref(null)

// البحث والتصفية
const searchQuery = ref('')
const typeFilter = ref('')
const categoryFilter = ref('')

// الترقيم
const currentPage = computed(() => legalResourceStore.currentPage || 1)
const totalPages = computed(() => legalResourceStore.totalPages || 1)
const itemsPerPage = computed(() => legalResourceStore.perPage || 10)

// 🔥 دالة لإزالة علامات HTML من النص
const stripHtml = (html) => {
  if (!html) return ''
  // إنشاء عنصر مؤقت
  const tmp = document.createElement('DIV')
  tmp.innerHTML = html
  // إرجاع النص فقط بدون علامات HTML
  return tmp.textContent || tmp.innerText || ''
}

// 🔥 دالة لتقصير النص مع الحفاظ على الكلمات
const truncateText = (text, maxLength = 100) => {
  if (!text) return ''
  const cleanText = stripHtml(text)
  if (cleanText.length <= maxLength) return cleanText
  return cleanText.substring(0, maxLength) + '...'
}

// الحوسبة باستخدام بيانات المتجر
const resources = computed(() => legalResourceStore.resources)
const categories = computed(() => legalResourceStore.categories)

const filteredResources = computed(() => {
  return resources.value.filter(resource => {
    // 🔥 البحث في النص بدون علامات HTML
    const cleanTextAr = stripHtml(resource.text_ar || '')
    const cleanTextEn = stripHtml(resource.text_en || '')
    
    const matchesSearch = !searchQuery.value || 
      cleanTextAr.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      cleanTextEn.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      resource.article_number_ar?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      resource.article_number_en?.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    const matchesType = !typeFilter.value || resource.law_type === typeFilter.value
    
    const matchesCategory = !categoryFilter.value || 
      resource.category_id?.toString() === categoryFilter.value
    
    return matchesSearch && matchesType && matchesCategory
  })
})

const paginatedResources = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredResources.value.slice(start, end)
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
watch([searchQuery, typeFilter, categoryFilter], () => {
  if (legalResourceStore.setPage) {
    legalResourceStore.setPage(1)
  }
})

// الدوال
const clearFilters = () => {
  searchQuery.value = ''
  typeFilter.value = ''
  categoryFilter.value = ''
}

const fetchResources = async () => {
  loading.value = true
  error.value = ''
  try {
    await legalResourceStore.fetchResources()
  } catch (err) {
    error.value = 'فشل في تحميل الموارد القانونية'
    console.error('Failed to fetch resources:', err)
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    await legalResourceStore.fetchCategories()
  } catch (err) {
    console.error('Failed to fetch categories:', err)
  }
}

const handleEdit = (resource) => {
  editingResource.value = { ...resource }
}

const handleDelete = async (resourceId) => {
  deleteTargetId.value = resourceId
  showDeleteConfirm.value = true
}

const confirmDelete = async () => {
  if (!deleteTargetId.value) return

  loading.value = true
  error.value = ''
  
  try {
    await legalResourceStore.deleteResource(deleteTargetId.value)
    successMessage.value = 'تم حذف المورد بنجاح'
    
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)
    
  } catch (err) {
    error.value = 'فشل في حذف المورد'
    console.error('Failed to delete resource:', err)
  } finally {
    loading.value = false
    showDeleteConfirm.value = false
    deleteTargetId.value = null
  }
}

const handleSave = async () => {
  error.value = ''
  successMessage.value = 'تم حفظ المورد بنجاح'
  
  setTimeout(() => {
    successMessage.value = ''
  }, 3000)
  
  await fetchResources()
  handleCancelForm()
}

const handleCancelForm = () => {
  showCreateForm.value = false
  editingResource.value = null
}

const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return
  if (legalResourceStore.setPage) {
    legalResourceStore.setPage(page)
  }
  fetchResources()
}

const typeBadgeClass = (type) => {
  switch (type) {
    case 'قانون':
      return 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
    case 'مرسوم':
      return 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
    case 'نظام':
      return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'
    case 'لائحة':
      return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
    default:
      return 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300'
  }
}

const formatDate = (dateString) => {
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('ar-EG', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch (error) {
    return dateString
  }
}

// عند التحميل
onMounted(() => {
  fetchResources()
  fetchCategories()
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

/* Line clamp for text */
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

/* تحسينات شريط التمرير للمودال */
div[class*="max-h-[90vh]"] {
  scrollbar-width: thin;
  scrollbar-color: var(--text-tertiary) transparent;
}

div[class*="max-h-[90vh]"]::-webkit-scrollbar {
  width: 6px;
}

div[class*="max-h-[90vh]"]::-webkit-scrollbar-track {
  background: transparent;
}

div[class*="max-h-[90vh]"]::-webkit-scrollbar-thumb {
  background: var(--text-tertiary);
  border-radius: 3px;
}

.dark div[class*="max-h-[90vh]"]::-webkit-scrollbar-thumb {
  background: var(--text-tertiary);
}
</style>