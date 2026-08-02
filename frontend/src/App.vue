<template>
  <div :key="currentLanguage">
    <HeaderComponent />
    <router-view />
    <!-- حاوية التوست العامة لتطبيق كامل -->
    <ToastContainer />
  </div>
</template>

<script setup>
import { useTranslations } from '@/composables/useTranslations'
import ToastContainer from '@/components/dashboard/component/ui/ToastContainer.vue'


const { currentLanguage } = useTranslations()
</script>
