<template>
  <div class="space-y-4 sm:space-y-6">
    <TherapistCard 
      v-for="(therapist, index) in paginatedTherapists" 
      :key="therapist.id" 
      :therapist="therapist" 
      :index="index"
      :isRTL="isRTL"
      :translate="translate"
    />

    <!-- No Results Message -->
    <div v-if="filteredTherapists.length === 0" class="text-center py-8 sm:py-12 bg-white rounded-xl shadow">
      <i class="fas fa-search text-4xl sm:text-6xl text-gray-300 mb-3 sm:mb-4"></i>
      <h3 class="text-xl sm:text-2xl font-bold text-gray-600 mb-2">{{ translate('therapists.results.noResults') }}</h3>
      <p class="text-gray-500 text-sm sm:text-base mb-4">{{ translate('therapists.results.noResultsMessage') }}</p>
      <button 
        @click="$emit('resetFilters')" 
        class="bg-primary-pink hover:bg-secondary-pink text-white px-6 py-3 rounded-xl font-semibold hover:shadow-md transition-all text-sm sm:text-base"
      >
        {{ translate('therapists.results.showAll') }}
      </button>
    </div>
  </div>
</template>

<script>
import TherapistCard from './TherapistCard.vue'

export default {
  name: 'TherapistsList',
  components: {
    TherapistCard
  },
  props: {
    paginatedTherapists: Array,
    filteredTherapists: Array,
    isRTL: Boolean,
    translate: Function
  },
  emits: ['resetFilters']
}
</script>