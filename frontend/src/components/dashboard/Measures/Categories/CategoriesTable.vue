<template>
  <Card>
    <template #header>
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
        <div class="text-base sm:text-lg">قائمة التصنيفات</div>
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
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm">اسم التصنيف</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm hidden sm:table-cell">الوصف</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm">عدد المقاييس</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm">الحالة</th>
              <th class="px-2 sm:px-4 py-3 text-start font-bold text-xs sm:text-sm">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr 
              v-for="category in categories" 
              :key="category.id" 
              class="border-t border-primary hover:bg-secondary transition-colors"
            >
              <td class="px-2 sm:px-4 py-3 text-primary font-medium text-xs sm:text-sm">
                <div class="flex items-center gap-2">
                  <div 
                    class="w-3 h-3 rounded-full flex-shrink-0" 
                    :style="{ backgroundColor: category.color || '#3B82F6' }"
                  ></div>
                  <div class="flex flex-col">
                    <span>{{ category.name_ar }}</span>
                    <span class="text-xs text-secondary">{{ category.name_en }}</span>
                  </div>
                </div>
              </td>
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm hidden sm:table-cell">
                <span class="truncate max-w-xs block">{{ category.description_ar || 'لا يوجد وصف' }}</span>
              </td>
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm">
                <span class="badge badge-neutral">{{ category.scales_count || 0 }}</span>
              </td>
              <td class="px-2 sm:px-4 py-3 text-primary text-xs sm:text-sm">
                <span :class="['badge', category.is_active ? 'badge-brand' : 'badge-neutral']">
                  {{ category.is_active ? 'نشط' : 'غير نشط' }}
                </span>
              </td>
              <td class="px-2 sm:px-4 py-3">
                <div class="flex gap-1 sm:gap-2 flex-wrap">
                  <Button size="sm" variant="outline" @click="$emit('edit', category)" class="text-xs px-2 py-1">
                    تعديل
                  </Button>
                  <Button 
                    size="sm" 
                    variant="outline" 
                    @click="$emit('toggle-status', category)" 
                    :class="['text-xs px-2 py-1', category.is_active ? 'text-yellow-600 border-yellow-600' : 'text-green-600 border-green-600']"
                  >
                    {{ category.is_active ? 'تعطيل' : 'تفعيل' }}
                  </Button>
                  <Button 
                    size="sm" 
                    variant="outline" 
                    @click="$emit('delete', category.id)" 
                    class="text-xs px-2 py-1 text-accent-500 border-accent-500 hover:bg-accent-500 hover:text-white"
                    :disabled="(category.scales_count || 0) > 0"
                  >
                    حذف
                  </Button>
                </div>
                <p v-if="(category.scales_count || 0) > 0" class="text-xs text-red-500 mt-1">
                  لا يمكن الحذف - يحتوي على مقاييس
                </p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- رسالة عدم وجود نتائج -->
    <div v-if="filteredCategories.length === 0" class="text-center py-8 sm:py-12 text-secondary">
      <h3 class="text-base sm:text-lg font-medium text-primary mb-2">لا توجد نتائج</h3>
      <p class="text-secondary mb-4 text-sm sm:text-base">لم نتمكن من العثور على تصنيفات مطابقة لبحثك</p>
      <Button variant="outline" @click="$emit('clear')" class="text-sm">
        مسح جميع الفلاتر
      </Button>
    </div>

    <!-- الترقيم -->
    <Pagination
      v-if="filteredCategories.length > 0"
      :current-page="currentPage"
      :total-pages="totalPages"
      :start-index="startIndex"
      :end-index="endIndex"
      :items-per-page="itemsPerPage"
      :visible-pages="visiblePages"
      :total-items="filteredCategories.length"
      @page-change="$emit('pageChange', $event)"
      @prev-page="$emit('prevPage')"
      @next-page="$emit('nextPage')"
      @update:itemsPerPage="$emit('update:itemsPerPage', $event)"
    />
  </Card>
</template>

<script setup lang="ts">
import Card from '@/components/dashboard/component/ui/Card.vue';
import Button from '@/components/dashboard/component/ui/Button.vue';
import Pagination from './Pagination.vue';

import type { ScaleCategory } from './types';

defineProps<{
  categories: ScaleCategory[];
  filteredCategories: ScaleCategory[];
  currentPage: number;
  totalPages: number;
  startIndex: number;
  endIndex: number;
  itemsPerPage: number;
  visiblePages: (number | string)[];
}>();

// إصلاح defineEmits - استخدام الصيغة المتوافقة
const emit = defineEmits<{
  (e: 'edit', category: ScaleCategory): void;
  (e: 'toggle-status', category: ScaleCategory): void;
  (e: 'delete', id: string): void;
  (e: 'pageChange', page: number | string): void;
  (e: 'prevPage'): void;
  (e: 'nextPage'): void;
  (e: 'update:itemsPerPage', value: number): void;
  (e: 'clear'): void;
}>();

// أو بدلاً من ذلك، يمكنك استخدام الصيغة المبسطة:
// const emit = defineEmits([
//   'edit',
//   'toggle-status', 
//   'delete',
//   'pageChange',
//   'prevPage',
//   'nextPage',
//   'update:itemsPerPage',
//   'clear'
// ]);
</script>