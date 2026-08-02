<template>
  <div class="bg-white p-3 sm:p-4 rounded-xl shadow mb-4">
    <p class="text-sm sm:text-base text-gray-600">
      {{ showingResultsText }}
    </p>
  </div>
</template>

<script>
export default {
  name: 'ResultsCount',
  props: {
    showingResultsText: String
  }
}
</script>