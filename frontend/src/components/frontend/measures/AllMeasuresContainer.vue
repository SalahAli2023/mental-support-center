<template>
  <section class="py-16 pb-24 bg-gray-50 font-almarai">
    <div class="container mx-auto px-4">
      <!-- عرض المقاييس -->
      <div v-if="paginatedMeasures.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <AllMeasures
          v-for="measure in paginatedMeasures"
          :key="measure.id"
          :measure="measure"
          :language="language"
          @measure-click="$emit('measure-click', measure)"
        />
      </div>

      <!-- رسالة عدم وجود نتائج -->
      <div v-else class="text-center py-12">
        <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center bg-[#9EBF3B20]">
          <i class="fas fa-search text-3xl text-primary-green"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">
          {{ translate('allMeasures.noResults.title') }}
        </h3>
        <p class="text-gray-500">
          {{ translate('allMeasures.noResults.desc') }}
        </p>
      </div>

      <!-- التحكم في الصفحات -->
      <div v-if="totalPages > 1" class="flex justify-center mt-12">
        <nav class="flex items-center space-x-2 space-x-reverse">
          <button @click="prevPage" :disabled="currentPage === 1"
            class="px-3 py-1 rounded-md border text-sm font-medium transition-all duration-300"
            :class="currentPage === 1
              ? 'text-gray-400 border-gray-200 cursor-not-allowed'
              : 'text-primary-green border-primary-green hover:bg-primary-green hover:text-white'">
            {{ translate('allMeasures.pagination.prev') }}
          </button>

          <span class="px-4 text-gray-700 text-sm">
            {{ translate('allMeasures.pagination.page') }} {{ currentPage }} 
            {{ translate('allMeasures.pagination.of') }} {{ totalPages }}
          </span>

          <button @click="nextPage" :disabled="currentPage === totalPages"
            class="px-3 py-1 rounded-md border text-sm font-medium transition-all duration-300"
            :class="currentPage === totalPages
              ? 'text-gray-400 border-gray-200 cursor-not-allowed'
              : 'text-primary-green border-primary-green hover:bg-primary-green hover:text-white'">
            {{ translate('allMeasures.pagination.next') }}
          </button>
        </nav>
      </div>
    </div>
  </section>
</template>

<script>
import { ref, computed } from "vue";
import { useTranslations } from '@/composables/useTranslations'
import AllMeasures from './AllMeasures.vue'

export default {
  name: "AllMeasuresContainer",
  components: {
    AllMeasures
  },
  props: {
    measures: { type: Array, default: () => [] },
    language: { type: String, default: "ar" },
  },
  emits: ["measure-click"],
  setup(props) {
    const currentPage = ref(1);
    const itemsPerPage = 9;
    const { translate } = useTranslations()

    const totalPages = computed(() =>
      Math.ceil(props.measures.length / itemsPerPage)
    );

    const paginatedMeasures = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage;
      return props.measures.slice(start, start + itemsPerPage);
    });

    const nextPage = () => {
      if (currentPage.value < totalPages.value) {
        currentPage.value++;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    };

    const prevPage = () => {
      if (currentPage.value > 1) {
        currentPage.value--;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    };

    return {
      currentPage,
      totalPages,
      paginatedMeasures,
      nextPage,
      prevPage,
      translate,
    };
  },
}
</script>