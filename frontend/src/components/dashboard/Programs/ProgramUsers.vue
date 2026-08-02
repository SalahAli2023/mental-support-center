<template>
  <div class="space-y-4">
    <!-- Header & Search -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
      <div class="relative w-full sm:w-64">
        <input 
          v-model="searchQuery" 
          placeholder="بحث عن عضو..." 
          class="input w-full pl-10 pr-4"
          @input="handleSearch"
        />
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <i class="fas fa-search text-gray-400"></i>
        </div>
      </div>

      <div class="flex gap-2">
        <select v-model="statusFilter" class="input w-full sm:w-auto" @change="fetchUsers">
          <option value="">جميع الحالات</option>
          <option value="enrolled">مسجل</option>
          <option value="in_progress">قيد التقدم</option>
          <option value="completed">مكتمل</option>
          <option value="dropped">منسحب</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="py-12 flex justify-center">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
    </div>

    <!-- Users Table -->
    <div v-else-if="users.length > 0" class="overflow-x-auto bg-white rounded-lg shadow border border-gray-100">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
              المستخدم
            </th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
              تاريخ التسجيل
            </th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
              التقدم
            </th>
             <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
              آخر نشاط
            </th>
            <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
              الحالة
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                    {{ getUserInitials(user.name) }}
                  </div>
                </div>
                <div class="mr-4">
                  <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.enrollment_date) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-1 w-24 bg-gray-200 rounded-full h-2 ml-2">
                  <div class="bg-primary h-2 rounded-full" :style="{ width: `${user.progress_percentage}%` }"></div>
                </div>
                <span class="text-xs font-medium text-gray-600">{{ user.progress_percentage }}%</span>
              </div>
            </td>
             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.last_active) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                :class="getStatusClass(user.status)">
                {{ getStatusLabel(user.status) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 bg-white rounded-lg border border-gray-100">
      <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
      <p class="text-gray-500">لا يوجد مشتركين في هذا البرنامج حالياً</p>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > pagination.per_page" class="flex justify-center mt-4 space-x-2 space-x-reverse">
      <button 
        @click="changePage(pagination.current_page - 1)"
        :disabled="pagination.current_page === 1"
        class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50"
      >
        السابق
      </button>
      <span class="px-3 py-1">
        صفحة {{ pagination.current_page }} من {{ pagination.last_page }}
      </span>
      <button 
        @click="changePage(pagination.current_page + 1)"
        :disabled="pagination.current_page === pagination.last_page"
        class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50"
      >
        التالي
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { programService } from '@/services/programService';

const props = defineProps({
  programId: {
    type: String,
    required: true
  }
});

const loading = ref(false);
const users = ref([]);
const searchQuery = ref('');
const statusFilter = ref('');
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
});

let searchTimeout;

const fetchUsers = async (page = 1) => {
  loading.value = true;
  try {
    const params = {
      page,
      search: searchQuery.value,
      status: statusFilter.value
    };
    
    const response = await programService.getProgramUsers(props.programId, params);
    
    if (response.data.success) {
      users.value = response.data.data;
      pagination.value = response.data.meta;
    }
  } catch (error) {
    console.error('Error fetching program users:', error);
  } finally {
    loading.value = false;
  }
};

const handleSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchUsers(1);
  }, 300);
};

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchUsers(page);
  }
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('ar-SA');
};

const getUserInitials = (name) => {
  return name
    .split(' ')
    .map(n => n[0])
    .join('')
    .substring(0, 2)
    .toUpperCase();
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

onMounted(() => {
  fetchUsers();
});
</script>
