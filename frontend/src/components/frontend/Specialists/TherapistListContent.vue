<template>
  <div class="max-w-7xl mx-auto px-3 sm:px-4 py-4 sm:py-8">
    <!-- Mobile Filter Button -->
    <MobileFilterButton :showMobileFilter="showMobileFilter" :isAnyFilterActive="isAnyFilterActive"
      :activeFiltersCount="activeFiltersCount" :translate="translate" @toggle="showMobileFilter = !showMobileFilter" />

    <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
      <!-- Main content -->
      <div class="flex-1">
        <!-- Mobile Filter Overlay -->
        <MobileFilterOverlay v-if="showMobileFilter && windowWidth < 1024" :filters="filters" :searchQuery="searchQuery"
          :translatedSpecializations="translatedSpecializations" :isRTL="isRTL" :translate="translate"
          @update:searchQuery="searchQuery = $event" @update:filters="filters = $event"
          @removeSpecialization="removeSpecialization" @resetFilters="resetFilters" @applyFilters="applyFilters"
          @close="showMobileFilter = false" />

        <!-- Results Count -->
        <ResultsCount :showingResultsText="showingResultsText" />

        <!-- Therapists List -->
        <TherapistsList :paginatedTherapists="paginatedTherapists" :filteredTherapists="filteredTherapists"
          :isRTL="isRTL" :translate="translate" @resetFilters="resetFilters" />

        <!-- Pagination -->
        <TherapistPagination v-if="totalPages > 1" :currentPage="currentPage" :totalPages="totalPages"
          :visiblePages="visiblePages" :isRTL="isRTL" @previousPage="previousPage" @nextPage="nextPage"
          @goToPage="goToPage" />
      </div>

      <!-- Filter Sidebar - Desktop -->
      <DesktopFilterSidebar :filters="filters" :searchQuery="searchQuery"
        :translatedSpecializations="translatedSpecializations" :isAnyFilterActive="isAnyFilterActive" :isRTL="isRTL"
        :translate="translate" @update:searchQuery="searchQuery = $event" @update:filters="filters = $event"
        @removeSpecialization="removeSpecialization" @resetFilters="resetFilters" @applyFilters="applyFilters" />
    </div>
  </div>
</template>

<script>
import MobileFilterButton from './MobileFilterButton.vue'
import MobileFilterOverlay from './MobileFilterOverlay.vue'
import DesktopFilterSidebar from './DesktopFilterSidebar.vue'
import ResultsCount from './ResultsCount.vue'
import TherapistsList from './TherapistsList.vue'
import TherapistPagination from './TherapistPagination.vue'

export default {
  name: 'TherapistListContent',
  components: {
    MobileFilterButton,
    MobileFilterOverlay,
    DesktopFilterSidebar,
    ResultsCount,
    TherapistsList,
    TherapistPagination
  },
  props: {
    therapists: Array,
    specializations: Array,
    isRTL: Boolean,
    translate: Function
  },
  data() {
    return {
      searchQuery: '',
      filters: { gender: 'all', specializations: [] },
      showMobileFilter: false,
      windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1280,
      currentPage: 1,
      therapistsPerPage: 6
    }
  },
  computed: {
    filteredTherapists() {
      let filtered = this.therapists;
      if (this.searchQuery) filtered = filtered.filter(t =>
        t.name.includes(this.searchQuery) || t.title.includes(this.searchQuery));
      if (this.filters.gender !== 'all') filtered = filtered.filter(t => t.gender === this.filters.gender);
      if (this.filters.specializations.length) filtered = filtered.filter(t =>
        this.filters.specializations.some(spec => t.specializations.includes(spec)));
      return filtered;
    },
    paginatedTherapists() {
      const startIndex = (this.currentPage - 1) * this.therapistsPerPage;
      const endIndex = startIndex + this.therapistsPerPage;
      return this.filteredTherapists.slice(startIndex, endIndex);
    },
    totalPages() {
      return Math.ceil(this.filteredTherapists.length / this.therapistsPerPage);
    },
    isAnyFilterActive() {
      return this.searchQuery.trim() !== '' || this.filters.gender !== 'all' || this.filters.specializations.length > 0;
    },
    activeFiltersCount() {
      let count = 0;
      if (this.searchQuery.trim() !== '') count++;
      if (this.filters.gender !== 'all') count++;
      count += this.filters.specializations.length;
      return count;
    },
    showingResultsText() {
      const count = this.filteredTherapists.length;
      return this.translate('therapists.results.showing').replace('{count}', count);
    },
    translatedSpecializations() {
      return this.specializations.map(spec => ({
        key: spec.key,
        translated: this.getTranslatedSpecialization(spec.key)
      }));
    },
    visiblePages() {
      const total = this.totalPages;
      const current = this.currentPage;
      const delta = 2;
      const range = [];
      const rangeWithDots = [];

      for (let i = 1; i <= total; i++) {
        if (i === 1 || i === total || (i >= current - delta && i <= current + delta)) {
          range.push(i);
        }
      }

      let prev = 0;
      for (let i of range) {
        if (prev) {
          if (i - prev === 2) {
            rangeWithDots.push(prev + 1);
          } else if (i - prev !== 1) {
            rangeWithDots.push('...');
          }
        }
        rangeWithDots.push(i);
        prev = i;
      }

      return rangeWithDots;
    }
  },
  methods: {
    getTranslatedSpecialization(key) {
      const spec = this.specializations.find(s => s.key === key);
      return spec ? this.translate(`therapists.specializations.${key}`) : key;
    },
    removeSpecialization(spec) {
      this.filters.specializations = this.filters.specializations.filter(s => s !== spec);
    },
    resetFilters() {
      this.searchQuery = '';
      this.filters = { gender: 'all', specializations: [] };
      this.currentPage = 1;
      this.showMobileFilter = false;
    },
    applyFilters() {
      this.currentPage = 1;
      this.showMobileFilter = false;
    },
    previousPage() {
      if (this.currentPage > 1) this.currentPage--;
    },
    nextPage() {
      if (this.currentPage < this.totalPages) this.currentPage++;
    },
    goToPage(page) {
      if (page !== '...' && page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    handleResize() {
      this.windowWidth = window.innerWidth;
      if (this.windowWidth >= 1024) this.showMobileFilter = false;
    }
  },
  mounted() {
    if (typeof window !== 'undefined') {
      window.addEventListener('resize', this.handleResize);
    }
  },
  beforeUnmount() {
    if (typeof window !== 'undefined') {
      window.removeEventListener('resize', this.handleResize);
    }
  }
}
</script>