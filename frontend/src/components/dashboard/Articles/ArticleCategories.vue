<template>
  <div class="space-y-4 p-3 sm:p-4">
    <!-- العنوان والأزرار -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-lg font-semibold text-primary sm:text-2xl">تصنيفات المقالات</h1>
        <p class="text-sm text-secondary mt-1">إدارة التصنيفات والمجالات</p>
      </div>
      
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
        <!-- حقل البحث -->
        <div class="relative w-full sm:w-48">
          <input 
            v-model="searchQuery" 
            placeholder="ابحث عن تصنيف..." 
            class="w-full input" 
            @input="handleSearch"
          />
          <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <!-- زر إلغاء البحث -->
          <button 
            v-if="searchQuery" 
            @click="clearFilters"
            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
            aria-label="مسح البحث"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- زر مسح الفلاتر -->
        <Button @click="clearFilters" variant="outline" class="whitespace-nowrap w-full sm:w-auto">
          مسح الفلاتر
        </Button>
        
        <!-- زر إضافة تصنيف -->
        <Button variant="primary" @click="handleCreate" class="w-full sm:w-auto">
          <PlusIcon class="h-4 w-4 mr-2" />
          إضافة تصنيف
        </Button>
      </div>
    </div>

    <!-- رسائل التنبيه -->
    <div v-if="error" class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
      {{ error }}
    </div>

    <div v-if="successMessage" class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
      {{ successMessage }}
    </div>

    <!-- البحث والتصفية -->
    <Card>
     

      <!-- جدول التصنيفات -->
    
        <div class="min-w-full inline-block align-middle">
          <!-- عرض الجدول على الشاشات الكبيرة -->
          <table class="min-w-full text-sm hidden md:table">
            <thead>
              <tr class="text-start text-secondary">
                <th class="px-2 py-2 min-w-[120px] sm:px-3 sm:py-3 text-start">التصنيف</th>
                <th class="px-2 py-2 min-w-[150px] sm:px-3 sm:py-3 text-start">الوصف</th>
                <th class="px-2 py-2 min-w-[100px] sm:px-3 sm:py-3 text-start">عدد المقالات</th>
                <th class="px-2 py-2 min-w-[130px] sm:px-3 sm:py-3 text-start">الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="category in filteredCategories" 
                :key="category.id" 
                class="border-t border-primary hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
              >
                <td class="px-2 py-2 text-primary whitespace-nowrap sm:px-3 sm:py-1">
                  <div class="flex items-center gap-2">
                    <div class="flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center"
                      :style="{ backgroundColor: category.color || '#6b7280' }"></div>
                    <div class="text-right">
                      <div class="font-medium text-primary">{{ category.name_ar }}</div>
                      <div class="text-xs text-tertiary">{{ category.name_en }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-2 py-2 text-primary sm:px-3 sm:py-3">
                  <div class="max-w-xs truncate">
                    {{ category.description_ar || 'لا يوجد وصف' }}
                  </div>
                </td>
                <td class="px-2 py-2 sm:px-3 sm:py-3">
                  <span class="badge badge-neutral text-xs whitespace-nowrap">
                    {{ getArticlesCount(category.id) }} مقال
                  </span>
                </td>
                <td class="px-2 py-2 sm:px-3 sm:py-3">
                  <div class="flex gap-2">
                    <Button size="sm" variant="outline" class="w-full sm:w-auto" @click="handleEdit(category)">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      تعديل
                    </Button>
                    <Button 
                      size="sm" 
                      variant="outline" 
                      class="w-full sm:w-auto text-red-600 dark:text-red-400 border-red-600 dark:border-red-400 hover:bg-red-600 dark:hover:bg-red-600 hover:text-white"
                      @click="handleDelete(category.id)"
                    >
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      حذف
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- عرض البطاقات على الشاشات الصغيرة -->
          <div class="space-y-3 md:hidden">
            <div 
              v-for="category in filteredCategories" 
              :key="category.id" 
              class="bg-primary rounded-lg border border-primary p-3 space-y-2"
            >
              <div class="flex justify-between items-start">
                <div class="flex items-center gap-2">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center"
                    :style="{ backgroundColor: category.color || '#6b7280' }"></div>
                  <div class="text-right">
                    <div class="font-medium text-primary">{{ category.name_ar }}</div>
                    <div class="text-xs text-tertiary">{{ category.name_en }}</div>
                  </div>
                </div>
                <span class="badge badge-neutral text-xs whitespace-nowrap">
                  {{ getArticlesCount(category.id) }} مقال
                </span>
              </div>
              <div class="grid grid-cols-1 gap-2 text-sm">
                <div>
                  <div class="text-tertiary text-xs">الوصف</div>
                  <div class="text-primary line-clamp-2">
                    {{ category.description_ar || 'لا يوجد وصف' }}
                  </div>
                </div>
              </div>
              <div class="pt-2 space-y-2">
                <Button size="sm" variant="outline" class="w-full" @click="handleEdit(category)">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                  تعديل
                </Button>
                <Button 
                  size="sm" 
                  variant="outline" 
                  class="w-full text-red-600 dark:text-red-400 border-red-600 dark:border-red-400 hover:bg-red-600 dark:hover:bg-red-600 hover:text-white"
                  @click="handleDelete(category.id)"
                >
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  حذف
                </Button>
              </div>
            </div>
          </div>
        </div>
      

      <!-- لا توجد تصنيفات -->
      <div v-if="!loading && filteredCategories.length === 0" class="text-center py-8 text-secondary">
        <TagIcon class="h-16 w-16 mx-auto mb-4 text-gray-300 dark:text-gray-600" />
        <h3 class="text-base sm:text-lg font-medium text-primary mb-2">لا توجد تصنيفات</h3>
        <p class="text-secondary mb-4 text-sm sm:text-base">
          {{ searchQuery ? 'لم نتمكن من العثور على تصنيفات مطابقة لبحثك' : 'لم تقم بإضافة أي تصنيفات بعد' }}
        </p>
        <Button @click="handleCreate" variant="outline" class="text-sm">
          إضافة تصنيف جديد
        </Button>
      </div>

      <!-- حالة التحميل -->
      <div v-if="loading" class="flex justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-500"></div>
      </div>
    </Card>

    <!-- نموذج إنشاء/تعديل التصنيف -->
    <ArticleCategoryForm 
      v-if="showForm" 
      :category="editingCategory" 
      @save="handleSave" 
      @cancel="handleCancelForm" 
    />

    <!-- تأكيد الحذف -->
    <DeleteConfirmModal 
      :show="showDeleteConfirm"
      message="هل أنت متأكد من رغبتك في حذف هذا التصنيف؟ لا يمكن التراجع عن هذا الإجراء." 
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false" 
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, TagIcon } from '@heroicons/vue/24/outline'
import Button from '@/components/dashboard/component/ui/Button.vue'
import Card from '@/components/dashboard/component/ui/Card.vue'
import ArticleCategoryForm from './ArticleCategoryForm.vue'
import DeleteConfirmModal from '../../../components/dashboard/events/DeleteConfirmModal.vue'
import { useArticleStore } from '@/stores/articles'
import type { ArticleCategory } from '@/types/article'

const articleStore = useArticleStore()

// البيانات التفاعلية
const loading = ref(false)
const showForm = ref(false)
const editingCategory = ref<ArticleCategory | null>(null)
const error = ref('')
const successMessage = ref('')
const showDeleteConfirm = ref(false)
const deleteTargetId = ref<string | null>(null)
const searchQuery = ref('')
let searchTimeout: ReturnType<typeof setTimeout>

// الحوسبة
const categories = computed(() => articleStore.categories)
const articles = computed(() => articleStore.articles)

const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value

  const query = searchQuery.value.toLowerCase()
  return categories.value.filter(category =>
    category.name_ar?.toLowerCase().includes(query) ||
    category.name_en?.toLowerCase().includes(query) ||
    category.description_ar?.toLowerCase().includes(query) ||
    category.description_en?.toLowerCase().includes(query)
  )
})

// الدوال
const getArticlesCount = (categoryId: string) => {
  return articles.value.filter(article => article.category_id === categoryId).length
}

const clearFilters = () => {
  searchQuery.value = ''
}

const handleSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    // البحث يتم تلقائياً عبر computed property
  }, 300)
}

const fetchData = async () => {
  loading.value = true
  error.value = ''
  try {
    await articleStore.fetchCategories()
    await articleStore.fetchArticles() // لمعرفة عدد المقالات في كل تصنيف
  } catch (err: any) {
    error.value = err.response?.data?.message || 'فشل في تحميل التصنيفات'
    console.error('Failed to fetch categories:', err)
  } finally {
    loading.value = false
  }
}

const handleCreate = () => {
  editingCategory.value = null
  showForm.value = true
}

const handleEdit = (category: ArticleCategory) => {
  editingCategory.value = { ...category }
  showForm.value = true
}

const handleDelete = async (categoryId: string) => {
  const articlesCount = getArticlesCount(categoryId)
  if (articlesCount > 0) {
    error.value = `لا يمكن حذف هذا التصنيف لأنه يحتوي على ${articlesCount} مقال. يرجى نقل المقالات أولاً.`
    return
  }

  deleteTargetId.value = categoryId
  showDeleteConfirm.value = true
}

const confirmDelete = async () => {
  if (!deleteTargetId.value) return

  loading.value = true
  error.value = ''

  try {
    await articleStore.deleteCategory(deleteTargetId.value)
    successMessage.value = 'تم حذف التصنيف بنجاح'

    setTimeout(() => {
      successMessage.value = ''
    }, 2000)

    await fetchData()

  } catch (err: any) {
    error.value = err.response?.data?.message || 'فشل في حذف التصنيف'
    console.error('Failed to delete category:', err)
  } finally {
    loading.value = false
    showDeleteConfirm.value = false
    deleteTargetId.value = null
  }
}

const handleSave = async () => {
  error.value = ''
  successMessage.value = 'تم حفظ التصنيف بنجاح'

  setTimeout(() => {
    successMessage.value = ''
  }, 2000)

  await fetchData()
  handleCancelForm()
}

const handleCancelForm = () => {
  showForm.value = false
  editingCategory.value = null
  error.value = ''
}

// عند التحميل
onMounted(() => {
  fetchData()
})
</script>

<style scoped>
/* تحسينات للوضع الداكن */
.badge {
  display: inline-flex;
  align-items: center;
  border-radius: 9999px;
  padding: 0.25rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 500;
  font-family: "Ciro", sans-serif;
}

.badge-neutral {
  background-color: rgb(254 249 195);
  color: rgb(113 63 18);
}

.dark .badge-neutral {
  background-color: rgb(120 53 15);
  color: rgb(254 249 195);
}

/* تحسينات للبحث */
.input {
  background-color: var(--bg-primary);
  border-color: var(--border-primary);
  color: var(--text-primary);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  padding-left: 2.5rem;
  padding-right: 2.5rem;
}

.input:focus {
  outline: none;
  border-color: var(--brand-500);
  box-shadow: 0 0 0 3px rgba(158, 191, 59, 0.1);
}

.dark .input:focus {
  box-shadow: 0 0 0 3px rgba(158, 191, 59, 0.2);
}

.input::placeholder {
  color: var(--text-tertiary);
}

/* تحسينات للجدول على الجوال */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* تحسينات عامة للوضع الداكن */
:deep(.card) {
  background: var(--bg-secondary);
  border-color: var(--border-primary);
}

:deep(table) {
  border-collapse: collapse;
  width: 100%;
}

:deep(th) {
  background-color: var(--bg-tertiary);
  font-weight: 600;
  color: var(--text-secondary);
}

:deep(tbody tr) {
  background-color: var(--bg-primary);
  border-color: var(--border-primary);
}

:deep(tbody tr:hover) {
  background-color: var(--bg-secondary);
}

/* تحسينات للحقول على الجوال */
@media (max-width: 640px) {
  :deep(input) {
    font-size: 16px; /* منع التكبير في iOS */
  }
  
  :deep(.button) {
    min-height: 44px; /* تحسين قابلية النقر على الجوال */
  }
}

/* تحسينات للظلال في الوضع الداكن */
.dark :deep(.card) {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3);
}

/* تحسينات للأيقونات في الوضع الداكن */
.dark :deep(svg) {
  color: var(--text-secondary);
}
</style>