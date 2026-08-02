<template>
  <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-900">البرامج المسجلة</h3>
      <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1.5 rounded-full font-medium">
        {{ programs.length }} برنامج
      </span>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8 text-secondary">
      <div class="inline-flex items-center gap-2">
        <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>جاري تحميل البرامج...</span>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="programs.length === 0" class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-300">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد برامج</h3>
      <p class="mt-1 text-sm text-gray-500">هذا المستخدم غير مسجل في أي برنامج حتى الآن.</p>
    </div>

    <!-- Programs List -->
    <div v-else class="space-y-4">
      <div 
        v-for="program in programs" 
        :key="program.id" 
        class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors"
      >
        <div class="flex items-start gap-4">
          <!-- Program Image -->
          <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
            <img 
              v-if="program.image_url" 
              :src="program.image_url" 
              :alt="program.name_ar"
              class="w-full h-full object-cover"
            >
            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
              <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
          </div>

          <div class="flex-1">
            <div class="flex justify-between items-start">
              <div>
                <h4 class="text-base font-semibold text-gray-900 mb-1">{{ program.name_ar }}</h4>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                  <span 
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                    :class="getStatusClass(program.status)"
                  >
                    {{ getStatusLabel(program.status) }}
                  </span>
                  <span>•</span>
                  <span>{{ formatDate(program.enrollment_date) }}</span>
                </div>
              </div>
              <div class="text-right">
                <span class="text-lg font-bold text-gray-900">{{ program.progress_percentage }}%</span>
                <span class="block text-xs text-gray-500">مكتمل</span>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-3 relative pt-1">
              <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                <div 
                  :style="{ width: `${program.progress_percentage}%` }"
                  class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500 transition-all duration-500"
                ></div>
              </div>
            </div>

            <!-- Stats -->
            <div class="flex items-center gap-4 text-xs text-gray-500 -mt-2">
              <div>
                <span class="font-medium text-gray-900">{{ program.completed_sessions || 0 }}</span>
                / {{ program.total_sessions || 0 }} جلسة
              </div>
              <div>
                <span class="font-medium text-gray-900">{{ program.completed_activities || 0 }}</span>
                نشاط
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '@/services/api';

const props = defineProps({
  userId: {
    type: [String, Number],
    required: true
  }
});

const loading = ref(false);
const programs = ref([]);

const fetchUserPrograms = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/api/users/${props.userId}/programs`);
    if (response.data && response.data.success) {
      programs.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching user programs:', error);
  } finally {
    loading.value = false;
  }
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

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('ar-SA');
};

watch(() => props.userId, (newId) => {
  if (newId) fetchUserPrograms();
});

onMounted(() => {
  if (props.userId) fetchUserPrograms();
});
</script>
