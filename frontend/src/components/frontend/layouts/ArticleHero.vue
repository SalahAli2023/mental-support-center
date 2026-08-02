<template>
  <!-- Hero Section -->
  <Hero 
    title="فعاليات ومؤتمرات الصحة النفسية"
    highlight=""
    subtitle="انضم إلى رحلتنا التفاعلية لاكتساب المعرفة وتبادل الخبرات في مجال الصحة النفسية"
    :buttons="[
      { text: 'استكشف الفعاليات', icon: 'fas fa-calendar-alt', primary: true },
      { text: 'المزيد عنا', icon: 'fas fa-info-circle', primary: false }
    ]"
  />
</template>

<script setup>
import Hero from '@/components/frontend/layouts/hero.vue'
</script>