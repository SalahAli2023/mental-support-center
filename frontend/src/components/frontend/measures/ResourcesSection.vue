<template>
  <section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">
        {{ getTranslatedTitle('resourcesSection.title') }}
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="resource in resources" 
          :key="resource.id" 
          class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300"
        >
          <img 
            :src="resource.image" 
            :alt="getTranslatedField(resource, 'title')" 
            class="w-full h-48 object-cover"
          >
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-3 text-gray-800">
              {{ getTranslatedField(resource, 'title') }}
            </h3>
            <p class="text-gray-600 text-sm mb-4">
              {{ getTranslatedField(resource, 'excerpt') }}
            </p>
            <a 
              :href="resource.link" 
              class="text-primary-green hover:underline font-medium flex items-center gap-1 transition-colors duration-300"
              :class="isRTL ? 'flex-row' : 'flex-row-reverse'"
            >
              <span>{{ getTranslatedTitle('buttons.readMore') }}</span>
              <i 
                class="fas text-sm transition-transform duration-300 hover:translate-x-1" 
                :class="isRTL ? 'fa-chevron-left' : 'fa-chevron-right'"
              ></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { ref, computed } from 'vue'
import { t } from '@/locales'

export default {
  name: 'ResourcesSection',
  props: {
    resources: {
      type: Array,
      default: () => []
    },
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

    const translate = (key) => {
      return t(key, currentLanguage.value)
    }

    const getTranslatedTitle = (key) => {
      const translation = t(key, currentLanguage.value)
      return typeof translation === 'object' ? translation[currentLanguage.value] : translation
    }

    const getTranslatedField = (resource, field) => {
      const value = resource[field]
      return typeof value === 'object' ? value[currentLanguage.value] : value
    }

    return {
      isRTL,
      getTranslatedTitle,
      getTranslatedField
    }
  }
}
</script>