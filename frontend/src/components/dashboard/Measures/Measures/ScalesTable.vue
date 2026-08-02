<template>
  <Card>
    <template #header>
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <div class="text-base sm:text-lg">إدارة المقاييس</div>
        <div class="text-sm text-secondary">
          الصفحة {{ currentPage }} من {{ totalPages }}
        </div>
      </div>
    </template>
    
    <div class="overflow-x-auto -mx-2 sm:mx-0">
      <div class="min-w-full inline-block align-middle">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-start text-secondary bg-secondary">
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm">اسم المقياس</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm hidden sm:table-cell">الصورة</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm hidden sm:table-cell">الفئة</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm">الأسئلة</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm hidden md:table-cell">التفسير</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm">الحالة</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="scale in scales" 
              :key="scale.id" 
              class="border-t border-primary hover:bg-secondary transition-colors"
            >
              <td class="px-2 sm:px-4 py-3 text-primary font-medium text-xs sm:text-sm">
                <div class="flex flex-col">
                  <span>{{ scale.name_ar }}</span>
                  <span class="text-xs text-secondary sm:hidden mt-1">{{ getCategoryName(scale.category_id) }}</span>
                </div>
              </td>
              <td class="px-2 sm:px-4 py-3 hidden sm:table-cell">
                <div class="w-12 h-12 rounded-lg border border-secondary bg-secondary overflow-hidden">
                  <img
                    :src="getScaleImage(scale)"
                    alt="صورة المقياس"
                    class="w-full h-full object-cover"
                    @error="onImageError"
                  />
                </div>
              </td>
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm hidden sm:table-cell">
                <span class="badge badge-neutral">{{ getCategoryName(scale.category_id) }}</span>
              </td>
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm">
                <!-- عدد الأسئلة -->
                {{ scale.questions?.length || scale.questions_count || 0 }}
              </td>
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm hidden md:table-cell">
                <!-- عدد مستويات التفسير -->
                <span class="badge" :class="getInterpretationBadgeClass(scale.interpretations?.length || scale.interpretations_count || 0)">
                  {{ scale.interpretations?.length || scale.interpretations_count || 0 }} مستوى
                </span>
              </td>
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm">
                <span :class="['badge', scale.is_active ? 'badge-brand' : 'badge-neutral']">
                  {{ scale.is_active ? 'نشط' : 'غير نشط' }}
                </span>
              </td>
              <td class="px-2 sm:px-4 py-3">
                <div class="flex gap-1 sm:gap-2 flex-wrap">
                  <Button size="sm" variant="outline" @click="$emit('edit', scale)" class="text-xs px-2 py-1">
                    تعديل
                  </Button>
                  <Button 
                    size="sm" 
                    variant="outline" 
                    @click="$emit('preview', scale)" 
                    class="text-xs px-2 py-1 text-green-600 border-green-600 hover:bg-green-600 hover:text-white"
                  >
                    معاينة
                  </Button>
                  <Button 
                    size="sm" 
                    variant="outline" 
                    @click="$emit('delete', scale.id)" 
                    class="text-xs px-2 py-1 text-accent-500 border-accent-500 hover:bg-accent-500 hover:text-white"
                  >
                    حذف
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- رسالة عدم وجود نتائج -->
    <div v-if="filteredScales.length === 0" class="text-center py-8 sm:py-12 text-secondary">
      <h3 class="text-base sm:text-lg font-medium text-primary mb-2">لا توجد نتائج</h3>
      <p class="text-secondary mb-4 text-sm sm:text-base">لم نتمكن من العثور على مقاييس مطابقة لبحثك</p>
      <Button variant="outline" @click="$emit('clear')" class="text-sm">
        مسح جميع الفلاتر
      </Button>
    </div>

    <!-- الترقيم -->
    <Pagination
      v-if="filteredScales.length > 0"
      :current-page="currentPage"
      :total-pages="totalPages"
      :start-index="startIndex"
      :end-index="endIndex"
      :items-per-page="itemsPerPage"
      :visible-pages="visiblePages"
      :total-items="filteredScales.length"
      @page-change="$emit('pageChange', $event)"
      @prev-page="$emit('prevPage')"
      @next-page="$emit('nextPage')"
      @update:itemsPerPage="$emit('update:itemsPerPage', $event)"
    />
  </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Card from '@/components/dashboard/component/ui/Card.vue';
import Button from '@/components/dashboard/component/ui/Button.vue';
import Pagination from './Pagination.vue';
import { useScalesStore } from '@/stores/scales';

import type { Scale } from './types';

const scalesStore = useScalesStore();
const FALLBACK_IMAGE = 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?auto=format&fit=crop&w=200&q=60';

defineProps<{
  scales: Scale[];
  filteredScales: Scale[];
  currentPage: number;
  totalPages: number;
  startIndex: number;
  endIndex: number;
  itemsPerPage: number;
  visiblePages: (number | string)[];
}>();

defineEmits<{
  edit: [scale: Scale];
  preview: [scale: Scale];
  delete: [id: string];
  pageChange: [page: number | string];
  prevPage: [];
  nextPage: [];
  'update:itemsPerPage': [value: number];
  clear: [];
}>();

function getCategoryName(categoryId: string) {
  return scalesStore.getCategoryName(categoryId);
}

function getInterpretationBadgeClass(count: number) {
  if (count === 0) return 'bg-red-500 text-white';
  if (count === 1) return 'bg-yellow-500 text-white';
  if (count === 2) return 'bg-blue-500 text-white';
  return 'bg-green-500 text-white';
}

function getScaleImage(scale: Scale) {
  if (scale.image_url) {
    return scale.image_url;
  }
  return FALLBACK_IMAGE;
}

function onImageError(event: Event) {
  const target = event.target as HTMLImageElement;
  target.src = FALLBACK_IMAGE;
}
</script>