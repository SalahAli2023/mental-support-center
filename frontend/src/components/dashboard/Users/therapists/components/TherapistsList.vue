<template>
  <div class="bg-primary border border-primary rounded-lg overflow-hidden">
    <!-- Mobile View - Cards -->
    <div class="sm:hidden space-y-3 p-3">
      <TherapistCard 
        v-for="therapist in therapists" 
        :key="therapist.id"
        :therapist="therapist"
        @edit-therapist="$emit('edit-therapist', therapist)"
        @view-profile="$emit('view-profile', therapist)"
        @delete-therapist="$emit('delete-therapist', therapist)"
        @open-schedule="$emit('open-schedule', therapist)"
      />
    </div>

    <!-- Desktop View - Table -->
    <div class="hidden sm:block overflow-x-auto">
      <TherapistsTable 
        :therapists="therapists"
        @edit-therapist="$emit('edit-therapist', $event)"
        @view-profile="$emit('view-profile', $event)"
        @delete-therapist="$emit('delete-therapist', $event)"
        @open-schedule="$emit('open-schedule', $event)"
      />
    </div>

    <!-- Empty State -->
    <div v-if="filteredTherapists.length === 0" class="text-center py-8">
      <div class="text-secondary mb-2 text-sm">لا توجد نتائج</div>
      <div class="text-xs text-secondary">جرب تعديل عوامل التصفية أو البحث</div>
    </div>
  </div>
</template>

<script setup>
import TherapistCard from './TherapistCard.vue'
import TherapistsTable from './TherapistsTable.vue'

defineProps({
  therapists: {
    type: Array,
    required: true
  },
  filteredTherapists: {
    type: Array,
    required: true
  }
})

defineEmits(['edit-therapist', 'view-profile', 'delete-therapist', 'open-schedule'])
</script>