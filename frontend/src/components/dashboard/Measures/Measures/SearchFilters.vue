<template>
  <!-- أدوات البحث والتصفية -->
  <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
    <!-- حقل البحث -->
    <div class="relative w-full sm:w-56">
      <input
        :value="searchQuery"
        @input="$emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
        placeholder="ابحث باسم المقياس أو الوصف..."
        class="w-full rounded-lg border border-primary bg-primary px-8 py-2 text-sm text-primary"
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

    <!-- الفئة -->
    <select
      :value="categoryFilter"
      @change="$emit('update:categoryFilter', ($event.target as HTMLSelectElement).value)"
      class="w-full sm:w-48 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
    >
      <option value="">جميع الفئات</option>
      <option v-for="category in categories" :key="category.id" :value="category.name_ar">
        {{ category.name_ar }}
      </option>
    </select>

    <!-- الحالة -->
    <select
      :value="statusFilter"
      @change="$emit('update:statusFilter', ($event.target as HTMLSelectElement).value)"
      class="w-full sm:w-48 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
    >
      <option value="">جميع الحالات</option>
      <option value="active">نشط</option>
      <option value="inactive">غير نشط</option>
    </select>

    <!-- زر مسح الفلاتر -->
    <button
      v-if="searchQuery || categoryFilter || statusFilter"
      @click="$emit('clear')"
      class="text-sm text-accent-500 hover:text-accent-600 whitespace-nowrap px-2"
    >
      مسح الفلاتر
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useScalesStore } from '@/stores/scales.ts';

const scalesStore = useScalesStore();
const categories = computed(() => scalesStore.categories);

defineProps<{
  searchQuery: string;
  categoryFilter: string;
  statusFilter: string;
}>();

defineEmits<{
  'update:searchQuery': [value: string];
  'update:categoryFilter': [value: string];
  'update:statusFilter': [value: string];
  clear: [];
}>();
</script>