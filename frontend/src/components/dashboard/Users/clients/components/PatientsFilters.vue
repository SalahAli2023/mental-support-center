<template>
  <div class="bg-primary border border-primary rounded-lg p-3">
    <div class="flex flex-col sm:flex-row gap-3 items-end sm:items-center justify-between">
      <!-- البحث - الجانب الأيسر -->
      <div class="w-full sm:w-64 order-1 sm:order-1">
        <div class="relative">
          <input 
            :value="filters.search"
            @input="updateFilter('search', $event.target.value)"
            type="text"
            placeholder="ابحث بالاسم، البريد، الهاتف..."
            class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary pr-10 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
          />
          <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </span>
        </div>
      </div>

      <!-- الفلتر - الجانب الأيمن -->
      <div class="flex flex-wrap gap-2 w-full sm:w-auto order-2 sm:order-2">
        <!-- فلترة بالحالة -->
        <select 
          :value="filters.status"
          @change="updateFilter('status', $event.target.value)"
          class="flex-1 sm:flex-none min-w-[120px] rounded-lg border border-primary bg-primary px-2 py-2 text-xs sm:text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
        >
          <option value="">جميع الحالات</option>
          <option value="active">نشط</option>
          <option value="inactive">غير نشط</option>
        </select>

        <!-- فلترة بالحالة الصحية -->
        <select 
          :value="filters.condition"
          @change="updateFilter('condition', $event.target.value)"
          class="flex-1 sm:flex-none min-w-[120px] rounded-lg border border-primary bg-primary px-2 py-2 text-xs sm:text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
        >
          <option value="">جميع الحالات</option>
          <option value="قلق">قلق</option>
          <option value="اكتئاب">اكتئاب</option>
          <option value="توتر">توتر</option>
        </select>

        <!-- الترتيب -->
        <select 
          :value="filters.sort"
          @change="updateFilter('sort', $event.target.value)"
          class="flex-1 sm:flex-none min-w-[120px] rounded-lg border border-primary bg-primary px-2 py-2 text-xs sm:text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
        >
          <option value="name-asc">الاسم (أ-ي)</option>
          <option value="name-desc">الاسم (ي-أ)</option>
          <option value="date-desc">الأحدث</option>
          <option value="date-asc">الأقدم</option>
        </select>

        <!-- إعادة تعيين -->
        <button 
          @click="$emit('reset-filters')"
          class="flex-1 sm:flex-none min-w-[120px] bg-tertiary hover:bg-secondary text-primary px-2 py-2 rounded-lg text-xs sm:text-sm flex items-center gap-1 justify-center transition-colors"
        >
          <svg class="h-3 w-3 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path>
          </svg>
          إعادة تعيين
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
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