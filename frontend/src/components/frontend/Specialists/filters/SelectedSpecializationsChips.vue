<template>
  <div v-if="filters.specializations.length" class="flex flex-wrap gap-2 mb-4 p-3 bg-primary-pink/10 rounded-xl">
    <span 
      v-for="spec in filters.specializations" 
      :key="`chip-${spec}`" 
      class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-white text-[#065f46] border border-primary-pink"
    >
      {{ getTranslatedSpecialization(spec) }}
      <button 
        @click="$emit('removeSpecialization', spec)" 
        class="hover:text-red-600 transition-colors pr-1"
      >
        <i class="fas fa-times text-xs"></i>
      </button>
    </span>
  </div>
</template>

<script>
export default {
  name: 'SelectedSpecializationsChips',
  props: {
    filters: Object,
    getTranslatedSpecialization: Function
  },
  emits: ['removeSpecialization']
}
</script>