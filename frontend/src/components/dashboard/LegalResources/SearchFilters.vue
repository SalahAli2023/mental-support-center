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
            placeholder="ابحث في النصوص أو أرقام المواد..." 
            class="input pr-10 text-sm sm:text-base" 
          />
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-primary mb-2">نوع القانون</label>
        <select 
          :value="typeFilter"
          @change="$emit('update:typeFilter', ($event.target as HTMLSelectElement).value)"
          class="input text-sm sm:text-base"
        >
          <option value="">جميع الأنواع</option>
          <option v-for="type in lawTypes" :key="type" :value="type">{{ type }}</option>
        </select>
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
            {{ category.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- نتائج البحث -->
    <div v-if="searchQuery || typeFilter || categoryFilter" class="mt-4 p-3 bg-secondary rounded-lg">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <div class="text-sm text-primary">
          عرض {{ filteredCount }} مورد
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
import { computed } from 'vue' // 🔥 أضف استيراد computed
import Card from '@/components/dashboard/component/ui/Card.vue'
import { useLegalResourceStore } from '@/stores/legalResources'

const legalResourceStore = useLegalResourceStore()

const props = defineProps<{
  searchQuery: string
  typeFilter: string
  categoryFilter: string
  filteredCount?: number
}>()

defineEmits<{
  'update:searchQuery': [value: string]
  'update:typeFilter': [value: string]
  'update:categoryFilter': [value: string]
  clear: []
}>()

// الحصول على أنواع القوانين الفريدة من المتجر
const lawTypes = computed(() => {
  const types = [...new Set(legalResourceStore.resources.map(resource => resource.law_type))]
  return types.filter(type => type) // إزالة القيم الفارغة
})

// الحصول على التصنيفات من المتجر
const categories = computed(() => legalResourceStore.categories)
</script>