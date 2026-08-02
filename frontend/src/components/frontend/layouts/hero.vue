<template>
  <section class="relative pt-30 pb-10 hero-gradient">
    <div class="container mx-auto px-4 text-center relative z-10">
      <!-- العنوان الرئيسي مع الكلمة الأخيرة خضراء -->
      <h1 class="hero-title text-center">
        <span class="text-black">{{ displayTitle }}</span>
        <span v-if="displayHighlight" class="text-primary-green"> {{ displayHighlight }}</span>
      </h1>

      <!-- الوصف -->
      <p v-if="resolvedSubtitle" class="text-gray-600 text-xl max-w-2xl mx-auto mb-8 text-center">
        {{ resolvedSubtitle }}
      </p>

    </div>
  </section>
</template>

<script setup>
import { computed, inject } from 'vue'

const { currentLanguage, translate } = inject('languageState')

const props = defineProps({
  // دعم للنصوص المباشرة أو مفاتيح الترجمة
  title: { type: String, default: '' },
  titleKey: { type: String, default: '' },
  highlight: { type: String, default: '' },
  highlightKey: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  subtitleKey: { type: String, default: '' },
  buttons: { type: Array, default: () => [] },
  floatingShapes: { type: Boolean, default: true },
  autoSplit: { type: Boolean, default: true }
})

// تحديد اتجاه النص
const isRTL = computed(() => currentLanguage.value === 'ar')

// استخدام النص المباشر أو الترجمة
const resolvedTitle = computed(() => {
  if (props.title) return props.title
  if (props.titleKey) return translate(props.titleKey)
  return ''
})

const resolvedHighlight = computed(() => {
  if (props.highlight) return props.highlight
  if (props.highlightKey) return translate(props.highlightKey)
  return ''
})

const resolvedSubtitle = computed(() => {
  if (props.subtitle) return props.subtitle
  if (props.subtitleKey) return translate(props.subtitleKey)
  return ''
})

// تقسيم العنوان تلقائياً إذا لم يتم提供 highlight
const titleWords = computed(() => resolvedTitle.value.split(' '))
const lastWord = computed(() => titleWords.value[titleWords.value.length - 1])
const titleWithoutLastWord = computed(() => {
  const words = [...titleWords.value]
  words.pop()
  return words.join(' ')
})

// تحديد ما سيتم عرضه
const displayTitle = computed(() => {
  // إذا كان هناك highlight محدد، استخدم العنوان كاملاً
  if (resolvedHighlight.value) return resolvedTitle.value
  // إذا كان التقسيم التلقائي مفعلاً، استخدم العنوان بدون الكلمة الأخيرة
  if (props.autoSplit) return titleWithoutLastWord.value
  // إذا لم يكن هناك تقسيم، استخدم العنوان كاملاً
  return resolvedTitle.value
})

const displayHighlight = computed(() => {
  // إذا كان هناك highlight محدد، استخدمه
  if (resolvedHighlight.value) return resolvedHighlight.value
  // إذا كان التقسيم التلقائي مفعلاً، استخدم الكلمة الأخيرة
  if (props.autoSplit) return lastWord.value
  // إذا لم يكن هناك تقسيم، لا تعرض anything
  return ''
})

const buttonClasses = (btn) => {
  const base = 'px-8 py-3 rounded-lg font-medium flex items-center transition-colors duration-300'
  if (btn.primary) return `${base} btn-primary text-white hover:shadow-lg transform hover:scale-105`
  return `${base} border-2 border-gray-300 text-gray-700 hover:border-primary-green hover:text-primary-green hover:shadow-md`
}

defineEmits(['cta'])
</script>

<style scoped>
.hero-gradient {
  background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
  position: relative;
}

.hero-title {
  font-size: 2.4rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  line-height: 1.3;
  text-align: center;
}

.btn-primary {
  background: linear-gradient(to right, #9EBF3B, #8aab34);
  box-shadow: 0 4px 15px rgba(158, 191, 59, 0.3);
}

.btn-primary:hover {
  background: linear-gradient(to right, #8aab34, #7a9a2e);
  box-shadow: 0 6px 20px rgba(158, 191, 59, 0.4);
}

@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(180deg); }
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .flex.justify-center.gap-4 {
    flex-direction: column;
    align-items: center;
  }

  button {
    width: 100%;
    max-width: 280px;
    justify-content: center;
  }
}

/* تحسينات إضافية للتنسيق */
.container {
  direction: ltr; /* نسيطر على الاتجاه من الكلاسات */
}

.text-center {
  text-align: center !important;
}

/* تأكد من أن كل العناصر في المركز */
.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

/* تحسين المسافات بين الكلمات في العنوان */
.hero-title span {
  display: inline;
}
</style>
