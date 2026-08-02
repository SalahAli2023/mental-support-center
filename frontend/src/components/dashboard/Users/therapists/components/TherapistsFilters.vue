<template>
  <div class="bg-primary border border-primary rounded-lg p-3">
    <div class="flex flex-col sm:flex-row gap-3 items-end sm:items-center justify-between">
      <!-- Search - Left Side -->
      <div class="w-full sm:w-64 order-1 sm:order-1">
        <div class="relative">
          <MagnifyingGlassIcon class="absolute right-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-secondary" />
          <input 
            :value="filters.search"
            @input="updateFilter('search', $event.target.value)"
            type="text"
            placeholder="ابحث باسم المعالج أو التخصص..."
            class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary pr-10 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
          />
        </div>
      </div>

      <!-- Filters - Right Side -->
      <div class="flex flex-wrap gap-2 w-full sm:w-auto order-2 sm:order-2">
        <!-- Status Filter -->
        <select 
          :value="filters.status"
          @change="updateFilter('status', $event.target.value)"
          class="flex-1 sm:flex-none min-w-[120px] rounded-lg border border-primary bg-primary px-2 py-2 text-xs sm:text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
        >
          <option value="">جميع الحالات</option>
          <option value="active">نشط</option>
          <option value="busy">مشغول</option>
          <option value="inactive">غير نشط</option>
        </select>

        <!-- Specialty Filter -->
        <select 
          :value="filters.specialty"
          @change="updateFilter('specialty', $event.target.value)"
          class="flex-1 sm:flex-none min-w-[120px] rounded-lg border border-primary bg-primary px-2 py-2 text-xs sm:text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
        >
          <option value="">جميع التخصصات</option>
          <option value="العلاج النفسي">العلاج النفسي</option>
          <option value="العلاج الأسري">العلاج الأسري</option>
          <option value="العلاج الزوجي">العلاج الزوجي</option>
          <option value="العلاج السلوكي">العلاج السلوكي</option>
        </select>

        <!-- Sort -->
        <select 
          :value="filters.sort"
          @change="updateFilter('sort', $event.target.value)"
          class="flex-1 sm:flex-none min-w-[120px] rounded-lg border border-primary bg-primary px-2 py-2 text-xs sm:text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
        >
          <option value="name-asc">الاسم (أ-ي)</option>
          <option value="name-desc">الاسم (ي-أ)</option>
          <option value="experience-desc">الأكثر خبرة</option>
          <option value="experience-asc">الأقل خبرة</option>
        </select>

        <!-- Reset Filters -->
        <button 
          @click="$emit('reset-filters')"
          class="flex-1 sm:flex-none min-w-[120px] bg-tertiary hover:bg-secondary text-primary px-2 py-2 rounded-lg text-xs sm:text-sm flex items-center gap-1 justify-center transition-colors"
        >
          <ArrowPathIcon class="h-3 w-3 sm:h-4 sm:w-4" />
          إعادة تعيين
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { MagnifyingGlassIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  filters: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['update:filters', 'reset-filters'])

const updateFilter = (key, value) => {
  emit('update:filters', { ...props.filters, [key]: value })
}
</script>
