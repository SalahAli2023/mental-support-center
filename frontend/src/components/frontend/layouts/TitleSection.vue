<template>
  <div class="inline-block relative text-center w-full mb-10">
    <h2
      class="text-3xl md:text-4xl font-bold mb-4 transition-all duration-700 transform opacity-0 translate-y-6"
      :class="[textColor, titleClass]"
      ref="title"
    >
      {{ mainText }}
      <span :class="highlightColor">{{ highlightText }}</span>
    </h2>
<div
  class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-24 h-1 rounded-2xl transition-all duration-500 delay-200 opacity-0 bg-primary-green"
  ref="line"
></div>


  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'

defineProps({
  mainText: String,
  highlightText: String,
  textColor: {
    type: String,
    default: 'text-gray-900',
  },
  highlightColor: {
    type: String,
    default: 'text-primary-green',
  },
  gradientClass: {
    type: String,
    default: 'bg-primary-pink',
  },
  titleClass: String,
  lineClass: String,
})

const title = ref(null)
const line = ref(null)

onMounted(() => {
  // حركة العنوان
  setTimeout(() => {
    if(title.value) {
      title.value.classList.remove('opacity-0', 'translate-y-6')
      title.value.classList.add('opacity-100', 'translate-y-0')
    }
  }, 200)

  // حركة الخط
  setTimeout(() => {
    if(line.value) {
      line.value.classList.remove('opacity-0')
      line.value.classList.add('opacity-100')
    }
  }, 400)
})
</script>

<style scoped>
/* تحسين الحركات */
.opacity-0 {
  opacity: 0;
}
.opacity-100 {
  opacity: 1;
}
.translate-y-6 {
  transform: translateY(1.5rem); /* Tailwind's 6 */
}
.translate-y-0 {
  transform: translateY(0);
}
</style>