<template>
  <div class="space-y-4 p-2 sm:p-4">
    <!-- العنوان والأزرار -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-primary">إدارة التصنيفات</h1>
        <p class="text-secondary text-sm mt-1">إدارة تصنيفات المقاييس النفسية</p>
      </div>
      <Button variant="primary" @click="openCreate" class="w-full sm:w-auto">
        + إضافة تصنيف جديد
      </Button>
    </div>

    <!-- أدوات البحث والتصفية -->
    <div class="flex flex-col sm:flex-row gap-3">
      <!-- حقل البحث -->
      <div class="relative flex-1">
        <input
          v-model="searchQuery"
          placeholder="ابحث باسم التصنيف..."
          class="w-full rounded-lg border border-primary bg-primary px-4 py-2 text-sm text-primary"
        />
        <svg
          class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-secondary"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          ></path>
        </svg>
      </div>

      <!-- تصفية الحالة -->
      <select
        v-model="statusFilter"
        class="w-full sm:w-48 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
      >
        <option value="">جميع الحالات</option>
        <option value="active">نشط</option>
        <option value="inactive">غير نشط</option>
      </select>

      <!-- زر مسح الفلاتر -->
      <button
        v-if="searchQuery || statusFilter"
        @click="clearFilters"
        class="text-sm text-accent-500 hover:text-accent-600 whitespace-nowrap px-4 py-2 border border-gray-300 rounded-lg"
      >
        مسح الفلاتر
      </button>
    </div>

    <!-- حالة التحميل -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-500 mx-auto"></div>
      <p class="text-secondary mt-2">جاري تحميل البيانات...</p>
    </div>

    <!-- رسالة الخطأ -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex items-center gap-2 text-red-700">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        <span>{{ error }}</span>
      </div>
      <button @click="fetchCategories" class="mt-2 text-sm text-red-600 hover:text-red-800">
        إعادة المحاولة
      </button>
    </div>

    <!-- جدول التصنيفات -->
    <CategoriesTable
      v-else
      :categories="paginatedCategories"
      :filtered-categories="filteredCategories"
      :current-page="currentPage"
      :total-pages="totalPages"
      :start-index="startIndex"
      :end-index="endIndex"
      :items-per-page="itemsPerPage"
      :visible-pages="visiblePages"
      @edit="edit"
      @toggle-status="toggleStatus"
      @delete="deleteCategory"
      @page-change="goToPage"
      @prev-page="prevPage"
      @next-page="nextPage"
      @update:itemsPerPage="itemsPerPage = $event"
      @clear="clearFilters"
    />

    <!-- Modal إنشاء/تعديل التصنيف -->
    <CategoryModal
      :show="modal"
      :current="current"
      :form="form"
      @close="close"
      @save="save"
    />

    <!-- تأكيد الحذف -->
    <DeleteConfirmModal
      :show="showDeleteConfirm"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted } from 'vue';
import Button from '@/components/dashboard/component/ui/Button.vue';
import CategoriesTable from './CategoriesTable.vue';
import CategoryModal from './CategoryModal.vue';
import DeleteConfirmModal from './DeleteConfirmModal.vue';
import { useScalesStore } from '@/stores/scales.ts';
import type { ScaleCategory, CategoryForm } from './types';
import api from '@/utils/api';

// استخدام الـ store
const scalesStore = useScalesStore();

// البيانات الأولية
const categories = computed(() => scalesStore.categories);
const loading = computed(() => scalesStore.loading);
const error = computed(() => scalesStore.error);

// Refs
const modal = ref(false);
const showDeleteConfirm = ref(false);
const current = ref<ScaleCategory | null>(null);
const deleteTargetId = ref<string | null>(null);
const saving = ref(false);

const searchQuery = ref('');
const statusFilter = ref('');

const currentPage = ref(1);
const itemsPerPage = ref(10);

// Form
const form = reactive<CategoryForm>({
  name_ar: '',
  name_en: '',
  description_ar: '',
  description_en: '',
  color: '#3B82F6', // لون افتراضي
  is_active: true
});

// Computed
const filteredCategories = computed(() => {
  return categories.value.filter(category => {
    const matchesSearch = !searchQuery.value || 
      category.name_ar?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      category.name_en?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      category.description_ar?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      category.description_en?.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    const matchesStatus = !statusFilter.value || 
      (statusFilter.value === 'active' && category.is_active) ||
      (statusFilter.value === 'inactive' && !category.is_active);
    
    return matchesSearch && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredCategories.value.length / itemsPerPage.value));

const paginatedCategories = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredCategories.value.slice(start, end);
});

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => Math.min(currentPage.value * itemsPerPage.value, filteredCategories.value.length));

const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) pages.push(i);
      pages.push('...');
      pages.push(total);
    } else if (current >= total - 3) {
      pages.push(1);
      pages.push('...');
      for (let i = total - 4; i <= total; i++) pages.push(i);
    } else {
      pages.push(1);
      pages.push('...');
      for (let i = current - 1; i <= current + 1; i++) pages.push(i);
      pages.push('...');
      pages.push(total);
    }
  }
  return pages;
});

// Watchers
watch([searchQuery, statusFilter], () => {
  currentPage.value = 1;
});

watch(itemsPerPage, () => {
  currentPage.value = 1;
});

// Methods
async function fetchCategories() {
  try {
    await scalesStore.fetchCategories();
  } catch (err) {
    console.error('Error fetching categories:', err);
  }
}

function clearFilters() {
  searchQuery.value = '';
  statusFilter.value = '';
}

function openCreate() {
  current.value = null;
  resetForm();
  modal.value = true;
}

function edit(category: ScaleCategory) {
  current.value = category;
  
  // تعبئة النموذج بالبيانات الحالية
  form.name_ar = category.name_ar || '';
  form.name_en = category.name_en || '';
  form.description_ar = category.description_ar || '';
  form.description_en = category.description_en || '';
  form.color = category.color || '#3B82F6';
  form.is_active = category.is_active !== undefined ? category.is_active : true;
  
  modal.value = true;
}

async function toggleStatus(category: ScaleCategory) {
  try {
    await scalesStore.toggleCategoryStatus(category.id);
    console.log('✅ تم تبديل حالة التصنيف');
  } catch (err: any) {
    console.error('Error toggling category status:', err);
    showErrorNotification('فشل في تبديل حالة التصنيف');
  }
}

function close() { 
  modal.value = false;
  resetForm();
  current.value = null;
}

function resetForm() {
  form.name_ar = '';
  form.name_en = '';
  form.description_ar = '';
  form.description_en = '';
  form.color = '#3B82F6';
  form.is_active = true;
}

async function save() {
  if (saving.value) return;
  
  saving.value = true;
  
  try {
    console.log('💾 بدء حفظ التصنيف...');
    
    // التحقق من البيانات المطلوبة
    if (!form.name_ar.trim() || !form.name_en.trim()) {
      showErrorNotification('اسم التصنيف (العربية والإنجليزية) مطلوب');
      return;
    }
    
    const categoryData = {
      name_ar: form.name_ar.trim(),
      name_en: form.name_en.trim(),
      description_ar: form.description_ar?.trim() || null,
      description_en: form.description_en?.trim() || null,
      color: form.color,
      is_active: form.is_active
    };
    
    console.log('📤 بيانات الحفظ:', categoryData);
    
    let savedCategory;
    
    if (current.value) {
      // تحديث التصنيف الموجود
      console.log('🔄 تحديث التصنيف:', current.value.id);
      const response = await api.put(`/categories/${current.value.id}`, categoryData);
      savedCategory = response.data.data;
      console.log('✅ التصنيف محدث:', savedCategory);
    } else {
      // إنشاء تصنيف جديد
      console.log('🆕 إنشاء تصنيف جديد');
      const response = await api.post('/categories', categoryData);
      savedCategory = response.data.data;
      console.log('✅ التصنيف مخلوق:', savedCategory);
    }
    
    modal.value = false;
    resetForm();
    
    // إعادة تحميل البيانات
    await fetchCategories();
    
    console.log('🎉 تم الحفظ بنجاح');
    showSuccessNotification('تم حفظ التصنيف بنجاح');
    
  } catch (err: any) {
    console.error('❌ Error saving category:', err);
    toast.handleApiError(err, 'حدث خطأ أثناء حفظ التصنيف. يرجى المحاولة مرة أخرى.');
  } finally {
    saving.value = false;
  }
}

function deleteCategory(id: string) {
  deleteTargetId.value = id;
  showDeleteConfirm.value = true;
}

async function confirmDelete() {
  // إغلاق النافذة فوراً قبل الحذف
  const categoryId = deleteTargetId.value;
  showDeleteConfirm.value = false;
  deleteTargetId.value = null;
  
  if (categoryId) {
    try {
      await scalesStore.deleteCategory(categoryId);
      console.log('✅ تم حذف التصنيف بنجاح');
      showSuccessNotification('تم حذف التصنيف بنجاح');
      // إعادة تحميل البيانات
      await fetchCategories();
    } catch (err: any) {
      console.error('Error deleting category:', err);
      toast.handleApiError(err, 'حدث خطأ أثناء حذف التصنيف. يرجى المحاولة مرة أخرى.');
    }
  }
}

// استيراد useToast
import { useToast } from '@/composables/useToast';
const toast = useToast();

// دوال مساعدة للإشعارات
function showErrorNotification(message: string) {
  toast.error(message);
}

function showSuccessNotification(message: string) {
  toast.success(message);
}

// دوال الترقيم
function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function goToPage(page: number | string) {
  if (typeof page === 'number') {
    currentPage.value = page;
  }
}

// دورة الحياة
onMounted(() => {
  fetchCategories();
});
</script>