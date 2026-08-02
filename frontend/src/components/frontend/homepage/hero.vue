<template>
  <section class="relative h-screen text-white overflow-hidden ">
    <!-- خلفية الفيديو -->
    <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
      <source src="@/assets/video/hipno-video.mp4" type="video/mp4">
    </video>

    <!-- طبقة شفافة فوق الفيديو -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-transparent z-10"></div>

    <!-- المحتوى الرئيسي -->
    <div
      class="absolute inset-0 z-20 flex flex-col sm:flex-row sm:items-start sm:justify-start px-4 gap-6 items-center justify-center text-center  sm:bottom-20 sm:ps-20 sm:inset-auto">
      <!-- النصوص -->
      <div class="mt-20 flex flex-col gap-6 items-center text-center">
        <h1 class="text-xl sm:text-3xl md:text-4xl font-bold leading-snug max-w-2xl animate-fadeUp">
          {{ translate('hero.title') }}
        </h1>

<button
  @click="handleHeroClick"
  class="
    bg-primary-green hover:bg-secondary-green
    text-white font-semibold
    h-10 sm:h-14
    w-[140px] sm:w-[200px]
    rounded-xl sm:rounded-2xl
    flex items-center justify-center gap-2
    transition-all duration-300
    shadow-md sm:shadow-lg hover:shadow-xl hover:scale-105
    text-sm sm:text-lg869/*
    animate-fadeUp delay-[600ms]
    mx-auto
    cursor-pointer
    border-none
    focus:outline-none focus:ring-2 focus:ring-primary-green focus:ring-opacity-50
  "
>
  {{ translate('hero.button') }}
</button>
      </div>
    </div>

    <!-- نقاط زخرفية -->
    <div class="absolute bottom-8 left-8 z-20 flex gap-2 animate-fadeUp delay-[1800ms]">
      <div class="w-3 h-3 bg-primary-green rounded-full"></div>
      <div class="w-3 h-3 bg-primary-pink rounded-full opacity-60"></div>
      <div class="w-3 h-3 bg-primary-green rounded-full opacity-40"></div>
    </div>
  </section>
</template>

<script setup>
import { inject } from 'vue'

import { computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

// استخدام inject للحصول على دالة الترجمة
const { translate } = inject('languageState')
const router = useRouter();

// دالة للتحقق مما إذا كان المستخدم مسجلاً دخولاً في الواجهة الأمامية
const isFrontendAuthenticated = () => {
  // تحقق من وجود token في localStorage
  const frontendToken = localStorage.getItem('frontend_token');
  return !!frontendToken && frontendToken !== 'null' && frontendToken !== 'undefined';
};

// دالة التعامل مع النقر على زر الهيرو
const handleHeroClick = () => {
  if (isFrontendAuthenticated()) {
    // إذا كان مسجلاً دخولاً، انتقل إلى صفحة الجلسات
    router.push('/booking');
  } else {
    // إذا لم يكن مسجلاً دخولاً، انتقل إلى صفحة التسجيل
    router.push('/register');
  }
};

</script>

<style scoped>
@keyframes fadeUp {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }

  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeUp {
  animation: fadeUp 1.2s ease-out forwards;
  opacity: 0;
}

.delay-\[600ms\] {
  animation-delay: 0.6s;
}

.delay-\[1200ms\] {
  animation-delay: 1.2s;
}

.delay-\[1800ms\] {
  animation-delay: 1.8s;
}
</style>