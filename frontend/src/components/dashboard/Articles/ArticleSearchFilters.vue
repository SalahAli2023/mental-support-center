<template>
  <Card>
    <template #header>
      أدوات البحث والتصفية
    </template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-primary mb-2">بحث</label>
        <div class="relative">
          <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input 
            :value="searchQuery"
            @input="$emit('update:searchQuery', ($event.target as HTMLInputElement).value)"
            placeholder="ابحث بعنوان المقال أو المحتوى..." 
            class="input pr-10 text-sm sm:text-base" 
          />
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-primary mb-2">التصنيف</label>
        <select 
          :value="categoryFilter"
          @change="$emit('update:categoryFilter', ($event.target as HTMLSelectElement).value)"
          class="input text-sm sm:text-base"
        >
          <option value="">جميع التصنيفات</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name_ar }}
          </option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-primary mb-2">الحالة</label>
        <select 
          :value="statusFilter"
          @change="$emit('update:statusFilter', ($event.target as HTMLSelectElement).value)"
          class="input text-sm sm:text-base"
        >
          <option value="">جميع الحالات</option>
          <option value="active">منشور</option>
          <option value="inactive">مسودة</option>
        </select>
      </div>
    </div>

    <!-- نتائج البحث -->
    <div v-if="searchQuery || categoryFilter || statusFilter" class="mt-4 p-3 bg-secondary rounded-lg">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <div class="text-sm text-primary">
          عرض {{ filteredArticles }} مقال
        </div>
        <button 
          @click="$emit('clear')"
          class="text-sm text-accent-500 hover:text-accent-500 whitespace-nowrap"
        >
          مسح الفلاتر
        </button>
      </div>
    </div>
  </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Card from '@/components/dashboard/component/ui/Card.vue'
import { useArticleStore } from '@/stores/articles'

const articleStore = useArticleStore()

defineProps<{
  searchQuery: string;
  categoryFilter: string;
  statusFilter: string;
  filteredArticles?: number;
}>();

defineEmits<{
  'update:searchQuery': [value: string];
  'update:categoryFilter': [value: string];
  'update:statusFilter': [value: string];
  clear: [];
}>();

const categories = computed(() => articleStore.categories)
</script>