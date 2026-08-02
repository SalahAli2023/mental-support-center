<template>
  <div class="lg:hidden mb-4 relative z-40">
    <div class="bg-white rounded-2xl shadow-lg border border-primary-green p-4 max-h-96 overflow-y-auto">
      <!-- Mobile Header -->
      <div class="flex items-center justify-between mb-4 pb-4 border-b border-primary-green sticky top-0 bg-white">
        <h2 class="text-xl font-bold text-[#065f46]">{{ translate('therapists.filters.title') }}</h2>
        <button @click="$emit('close')" class="p-2 text-gray-500 hover:text-gray-700">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <!-- Selected chips -->
      <SelectedSpecializationsChips 
        :filters="filters"
        :getTranslatedSpecialization="getTranslatedSpecialization"
        @removeSpecialization="$emit('removeSpecialization', $event)"
      />

      <!-- Search -->
      <FilterSearch 
        :searchQuery="searchQuery"
        :isRTL="isRTL"
        :translate="translate"
        @update:searchQuery="$emit('update:searchQuery', $event)"
      />

      <!-- Gender Filter -->
      <GenderFilter 
        :filters="filters"
        :isRTL="isRTL"
        :translate="translate"
        @update:filters="$emit('update:filters', $event)"
      />

      <!-- Specializations -->
      <SpecializationsFilter 
        :filters="filters"
        :translatedSpecializations="translatedSpecializations"
        :isRTL="isRTL"
        :translate="translate"
        :mobile="true"
        @update:filters="$emit('update:filters', $event)"
      />

      <!-- Action Buttons -->
      <FilterActions 
        :translate="translate"
        @resetFilters="$emit('resetFilters')"
        @applyFilters="$emit('applyFilters')"
      />
    </div>
  </div>
</template>

<script>
import SelectedSpecializationsChips from './filters/SelectedSpecializationsChips.vue'
import FilterSearch from './filters/FilterSearch.vue'
import GenderFilter from './filters/GenderFilter.vue'
import SpecializationsFilter from './filters/SpecializationsFilter.vue'
import FilterActions from './filters/FilterActions.vue'

export default {
  name: 'MobileFilterOverlay',
  components: {
    SelectedSpecializationsChips,
    FilterSearch,
    GenderFilter,
    SpecializationsFilter,
    FilterActions
  },
  props: {
    filters: Object,
    searchQuery: String,
    translatedSpecializations: Array,
    isRTL: Boolean,
    translate: Function
  },
  emits: [
    'update:searchQuery',
    'update:filters',
    'removeSpecialization',
    'resetFilters',
    'applyFilters',
    'close'
  ],
  methods: {
    getTranslatedSpecialization(key) {
      return this.translate(`therapists.specializations.${key}`)
    }
  }
}
</script>