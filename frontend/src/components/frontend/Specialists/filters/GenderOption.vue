<template>
  <label 
    class="flex items-center justify-between p-3 border-2 rounded-xl hover:bg-opacity-5 cursor-pointer transition-all duration-200 bg-white"
    :class="[colorClass, currentValue === value ? 'bg-opacity-10' : 'hover:bg-opacity-5']"
  >
    <div class="flex items-center flex-row">
      <input 
        :value="value" 
        :checked="currentValue === value"
        @change="$emit('update', value)"
        type="radio" 
        class="w-4 h-4" 
        :class="[isRTL ? 'ml-2' : 'mr-2']" 
      /> 
      <span class="text-gray-700 font-medium text-sm">{{ label }}</span>
    </div>
    <i :class="[icon, 'text-base']"></i>
  </label>
</template>

<script>
export default {
  name: 'GenderOption',
  props: {
    value: String,
    currentValue: String,
    label: String,
    colorClass: String,
    icon: String,
    isRTL: Boolean
  },
  emits: ['update']
}
</script>