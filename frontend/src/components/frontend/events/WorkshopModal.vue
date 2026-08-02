<template>
  <!-- مودال ورشة العمل -->
  <div class="fixed inset-0 z-50 overflow-y-auto" dir="rtl">
    <!-- خلفية شفافة -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="close"></div>
    
    <!-- محتوى المودال -->
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- رأس المودال -->
        <div class="sticky top-0 bg-white px-6 py-4 border-b border-gray-200 rounded-t-2xl flex justify-between items-center z-10">
          <div>
            <span 
              :class="`inline-block text-xs font-semibold px-3 py-1 rounded-full ${getCategoryStyle('ورش عمل')}`"
            >
              ورشة عمل
            </span>
            <h2 class="text-xl font-bold text-gray-900 mt-2">{{ workshop.title }}</h2>
          </div>
          <button 
            @click="close"
            class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors duration-200"
          >
            <i class="fas fa-times text-gray-500"></i>
          </button>
        </div>
        
        <!-- محتوى المودال -->
        <div class="p-6">
          <!-- معلومات الورشة -->
          <div class="mb-6">
            <p class="text-gray-700 mb-4">{{ workshop.fullDescription }}</p>
            
            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
              <span class="flex items-center gap-1">
                <i class="fas fa-clock text-primary-pink"></i>
                {{ workshop.duration }}
              </span>
              <span class="flex items-center gap-1">
                <i class="fas fa-user text-primary-green"></i>
                {{ workshop.speaker }}
              </span>
            </div>
          </div>
          
          <!-- المواضيع -->
          <div class="mb-6" v-if="workshop.topics && workshop.topics.length">
            <h3 class="font-bold text-gray-900 mb-3">المواضيع التي سيتم تغطيتها:</h3>
            <ul class="space-y-2">
              <li 
                v-for="(topic, index) in workshop.topics" 
                :key="index"
                class="flex items-start gap-2"
              >
                <i class="fas fa-check text-primary-green mt-1 flex-shrink-0"></i>
                <span class="text-gray-700">{{ topic }}</span>
              </li>
            </ul>
          </div>
          
          <!-- أزرار الإجراءات -->
          <div class="flex gap-3 pt-4">
            <button 
              @click="handleRegistration"
              class="flex-1 bg-primary-pink hover:bg-[#c47a73] text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300"
            >
              سجل في الورشة
            </button>
            <button 
              @click="close"
              class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-xl transition-all duration-300"
            >
              إغلاق
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// دالة للحصول على نمط التصنيف
const getCategoryStyle = (type) => {
  const styles = {
    'أمسيات': 'bg-blue-100 text-blue-700',
    'فعاليات': 'bg-green-100 text-green-700',
    'ورش عمل': 'bg-pink-100 text-pink-700'
  }
  return styles[type] || 'bg-gray-100 text-gray-700'
}

// بيانات ورشة العمل
const props = defineProps({
  workshop: {
    type: Object,
    required: true
  }
})

// إرسال الأحداث للوالد
const emit = defineEmits(['close', 'register'])

// إغلاق المودال
const close = () => {
  emit('close')
}

// معالجة التسجيل
const handleRegistration = () => {
  emit('register', props.workshop)
}
</script>