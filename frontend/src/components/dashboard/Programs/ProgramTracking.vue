<template>
  <div class="space-y-6 p-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">متابعة البرامج</h1>
        <p class="text-sm text-gray-500 mt-1">عرض ومتابعة تقدم المشتركين في جميع البرامج</p>
      </div>
      
      <!-- Filters -->
      <div class="flex flex-wrap gap-3">
        <!-- Search -->
        <div class="relative">
          <input 
            v-model="filters.search"
            type="text" 
            placeholder="بحث عن مستخدم..." 
            class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm w-48 sm:w-64"
            @input="handleSearch"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        <!-- Status Filter -->
        <select 
          v-model="filters.status" 
          class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
          @change="fetchEnrollments"
        >
          <option value="">جميع الحالات</option>
          <option value="enrolled">مسجل</option>
          <option value="in_progress">قيد التقدم</option>
          <option value="completed">مكتمل</option>
          <option value="dropped">منسحب</option>
        </select>

        <!-- Program Filter -->
        <select 
          v-model="filters.program_id" 
          class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none max-w-[200px]"
          @change="fetchEnrollments"
        >
          <option value="">جميع البرامج</option>
          <option v-for="prog in programs" :key="prog.id" :value="prog.id">
            {{ prog.name_ar }}
          </option>
        </select>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <div class="inline-flex items-center gap-2 text-gray-500">
          <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span>جاري التحميل...</span>
        </div>
      </div>

      <!-- Content -->
      <div v-else>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                  المشترك
                </th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                  البرنامج
                </th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                  التقدم
                </th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                  الحالة
                </th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                  التواريخ
                </th>
                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                  الإجراءات
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="enrollment in enrollments" :key="enrollment.id" class="hover:bg-gray-50 transition-colors">
                <!-- User -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                      {{ getUserInitials(enrollment.user?.name || '') }}
                    </div>
                    <div class="mr-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ enrollment.user?.name || 'مستخدم محذوف' }}
                      </div>
                      <div class="text-sm text-gray-500">
                        {{ enrollment.user?.email || '-' }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Program -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    {{ enrollment.program?.name_ar || 'برنامج محذوف' }}
                  </div>
                  <div class="text-xs text-gray-500 mt-1">
                    {{ enrollment.program?.name_en }}
                  </div>
                </td>

                <!-- Progress -->
                <td class="px-6 py-4 whitespace-nowrap align-middle">
                  <div class="w-full max-w-[140px]">
                    <div class="flex items-center justify-between text-xs mb-1">
                      <span class="font-medium text-gray-700">{{ enrollment.progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div 
                        class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                        :style="{ width: `${enrollment.progress_percentage}%` }"
                        :class="getProgressColor(enrollment.progress_percentage)"
                      ></div>
                    </div>
                  </div>
                </td>

                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                    :class="getStatusClass(enrollment.status)"
                  >
                    {{ getStatusLabel(enrollment.status) }}
                  </span>
                </td>

                <!-- Dates -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <div>
                    <span class="text-xs text-gray-400">التسجيل:</span>
                    {{ formatDate(enrollment.enrollment_date) }}
                  </div>
                  <div v-if="enrollment.updated_at">
                    <span class="text-xs text-gray-400">آخر نشاط:</span>
                    {{ formatDate(enrollment.updated_at) }}
                  </div>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button 
                    @click="viewDetails(enrollment)"
                    class="text-blue-600 hover:text-blue-900 ml-3"
                    title="عرض التفاصيل"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button 
                    v-if="enrollment.user"
                    @click="$router.push(`/dashboard/users/${enrollment.user.id}`)"
                    class="text-gray-600 hover:text-gray-900"
                    title="ملف المستخدم"
                  >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="enrollments.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد بيانات</h3>
          <p class="mt-1 text-sm text-gray-500">لم يتم العثور على أي مشتركين بناءً على الفلاتر الحالية.</p>
        </div>

        <!-- Pagination -->
        <div v-if="enrollments.length > 0" class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <button 
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
            >
              السابق
            </button>
            <button 
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
            >
              التالي
            </button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                عرض <span class="font-medium">{{ (pagination.current_page - 1) * pagination.per_page + 1 }}</span> إلى <span class="font-medium">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span> من <span class="font-medium">{{ pagination.total }}</span> نتيجة
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  @click="changePage(pagination.current_page - 1)"
                  :disabled="pagination.current_page === 1"
                  class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                >
                  <span class="sr-only">Previous</span>
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                  صفحة {{ pagination.current_page }} من {{ pagination.last_page }}
                </span>
                <button
                  @click="changePage(pagination.current_page + 1)"
                  :disabled="pagination.current_page === pagination.last_page"
                  class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                >
                  <span class="sr-only">Next</span>
                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import api from '@/services/api';
import { getAllPrograms } from '@/services/programService';

const loading = ref(false);
const enrollments = ref([]);
const programs = ref([]);
const filters = reactive({
  search: '',
  status: '',
  program_id: '',
  page: 1
});

const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
});

let searchTimeout;

// Fetch enrollments list
const fetchEnrollments = async () => {
  loading.value = true;
  try {
    const response = await api.get('/api/admin/enrollments', { params: filters });
    if (response.data && response.data.success) {
      enrollments.value = response.data.data;
      pagination.value = response.data.meta;
    }
  } catch (error) {
    console.error('Error fetching enrollments:', error);
  } finally {
    loading.value = false;
  }
};

// Fetch programs for filter dropdown
const fetchPrograms = async () => {
  try {
    const response = await getAllPrograms({ all: true }); // Assuming getAllPrograms can return all without pagination or handled here
    if (response.data && response.data.success) {
      programs.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching programs:', error);
  }
};

const handleSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    filters.page = 1;
    fetchEnrollments();
  }, 300);
};

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    filters.page = page;
    fetchEnrollments();
  }
};

const getUserInitials = (name) => {
  if (!name) return '??';
  return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
};

const getStatusLabel = (status) => {
  const labels = {
    'enrolled': 'مسجل',
    'in_progress': 'قيد التقدم',
    'completed': 'مكتمل',
    'dropped': 'منسحب'
  };
  return labels[status] || status;
};

const getStatusClass = (status) => {
  const classes = {
    'enrolled': 'bg-blue-100 text-blue-800',
    'in_progress': 'bg-yellow-100 text-yellow-800',
    'completed': 'bg-green-100 text-green-800',
    'dropped': 'bg-red-100 text-red-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getProgressColor = (percent) => {
  if (percent >= 100) return 'bg-green-500';
  if (percent >= 50) return 'bg-blue-500';
  return 'bg-yellow-500';
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('ar-SA');
};

const viewDetails = (enrollment) => {
  // Can be expanded to show more details or navigate to a detailed view
  console.log('View enrollment details', enrollment);
};

onMounted(() => {
  fetchPrograms();
  fetchEnrollments();
});
</script>
