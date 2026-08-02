<template>
  <div class="mb-4">
    <h3 class="text-base font-bold text-[#065f46] mb-3">
      {{ translate('therapists.filters.specializations') }}
    </h3>
    <div :class="mobile ? 'grid grid-cols-1 gap-2 max-h-32 overflow-y-auto' : 'grid grid-cols-2 gap-3'">
      <label 
        v-for="(specialization, index) in translatedSpecializations" 
        :key="specialization.key" 
        class="flex items-center p-2 text-xs border-2 rounded-xl hover:bg-gray-50 cursor-pointer transition-all duration-200 bg-white"
        :class="getSpecializationClass(index)"
      >
        <input 
          :value="specialization.key" 
          :checked="filters.specializations.includes(specialization.key)"
          @change="toggleSpecialization(specialization.key)"
          type="checkbox" 
          class="w-3 h-3 rounded" 
          :class="[isRTL ? 'ml-2' : 'mr-2']" 
        /> 
        <span class="text-gray-700 font-medium" :class="isRTL ? 'mr-2' : 'ml-2'">
          {{ specialization.translated }}
        </span>
      </label>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SpecializationsFilter',
  props: {
    filters: Object,
    translatedSpecializations: Array,
    isRTL: Boolean,
    translate: Function,
    mobile: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:filters'],
  methods: {
    getSpecializationClass(index) {
      const colorClass = index % 2 === 0 ? 'border-primary-green hover:border-primary-green' : 'border-primary-pink hover:border-primary-pink'
      return colorClass
    },
    toggleSpecialization(spec) {
      const currentSpecializations = [...this.filters.specializations]
      const index = currentSpecializations.indexOf(spec)
      
      if (index > -1) {
        currentSpecializations.splice(index, 1)
      } else {
        currentSpecializations.push(spec)
      }
      
      this.$emit('update:filters', {
        ...this.filters,
        specializations: currentSpecializations
      })
    }
  }
}
</script>