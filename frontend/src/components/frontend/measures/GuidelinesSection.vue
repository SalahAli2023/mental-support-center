<template>
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div class="rounded-2xl p-8 max-w-4xl mx-auto bg-[rgba(158,191,59,0.05)] border-r-4 border-primary-green">
        <div class="text-center mb-8">
          <h2 class="text-3xl font-bold mb-4 text-gray-800">
            {{ translate('guidelines.title') }}
          </h2>
          <p class="text-gray-600">
            {{ translate('guidelines.subtitle') }}
          </p>
        </div>

        <ul class="space-y-4 text-gray-700 text-lg">
          <li 
            v-for="(item, index) in getGuidelinesList()" 
            :key="index"
            class="relative"
            :class="isRTL ? 'pr-8' : 'pl-8'"
          >
            <span 
              class="absolute text-primary-green font-bold"
              :class="isRTL ? 'right-0 top-0' : 'left-0 top-0'"
            >
              ✓
            </span>
            {{ item }}
          </li>
        </ul>
      </div>
    </div>
  </section>
</template>

<script>
import { ref, computed } from 'vue'
import { useTranslations } from '@/composables/useTranslations'
export default {
  name: 'GuidelinesSection',
  props: {
    language: { 
      type: String, 
      default: 'ar' 
    }
  },
  setup(props) {
    const currentLanguage = ref(props.language)

    const isRTL = computed(() => {
      return currentLanguage.value === 'ar'
    })

    const {translate} = useTranslations()
    
    const getGuidelinesList = () => {
      const list = translate('guidelines.list', currentLanguage.value)
      return Array.isArray(list) ? list : []
    }

    return {
      isRTL,
      translate,
      getGuidelinesList
    }
  }
}
</script>