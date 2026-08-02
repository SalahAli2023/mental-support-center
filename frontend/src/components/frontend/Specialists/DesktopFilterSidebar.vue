<template>
  <div class="hidden lg:block w-80">
    <div class="p-6 bg-white rounded-2xl shadow-lg border border-primary-green sticky top-8">
      <!-- Desktop Header -->
      <div class="flex items-center justify-between mb-6 pb-4 border-b border-primary-green">
        <div class="flex items-center gap-3">
          <i class="fas fa-sliders-h text-primary-green text-xl"></i>
          <h2 class="text-xl font-bold text-[#065f46]">{{ translate('therapists.filters.title') }}</h2>
        </div>
        <button 
          v-if="isAnyFilterActive" 
          @click="$emit('resetFilters')" 
          class="text-sm font-semibold text-red-600 hover:text-red-700 bg-red-50 px-3 py-1 rounded-lg transition-colors"
        >
          {{ translate('therapists.filters.clearAll') }}
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
  name: 'DesktopFilterSidebar',
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
    isAnyFilterActive: Boolean,
    isRTL: Boolean,
    translate: Function
  },
  emits: [
    'update:searchQuery',
    'update:filters',
    'removeSpecialization',
    'resetFilters',
    'applyFilters'
  ],
  methods: {
    getTranslatedSpecialization(key) {
      return this.translate(`therapists.specializations.${key}`)
    }
  }
}
</script>