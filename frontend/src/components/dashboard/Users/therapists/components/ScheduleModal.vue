<template>
  <div 
    v-if="open" 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-2 sm:p-4"
  >
    <div class="w-full max-w-4xl max-h-[95vh] overflow-y-auto bg-primary rounded-xl border border-primary p-4 sm:p-6 shadow-lg">
      <!-- Header -->
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-lg sm:text-xl font-semibold text-primary">
            جدول {{ selectedTherapist?.name_ar || selectedTherapist?.name_en || 'المعالج' }}
          </h2>
          <p class="text-xs sm:text-sm text-secondary mt-1">إدارة الأوقات المتاحة للمواعيد</p>
        </div>
        <button 
          @click="$emit('close')"
          class="bg-tertiary hover:bg-secondary text-primary w-7 h-7 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center transition-colors"
        >
          ✕
        </button>
      </div>

      <!-- Schedule Controls -->
      <div class="flex flex-col sm:flex-row gap-3 mb-4 p-3 bg-secondary rounded-lg">
        <div class="flex-1">
          <label class="block text-xs sm:text-sm font-medium text-primary mb-1">مدة الجلسة</label>
          <select 
            v-model="sessionDuration"
            class="w-full rounded-lg border border-primary bg-primary px-2 py-1 sm:px-3 sm:py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
          >
            <option value="30">30 دقيقة</option>
            <option value="45">45 دقيقة</option>
            <option value="60">60 دقيقة</option>
          </select>
        </div>
        
        <div class="flex-1">
          <label class="block text-xs sm:text-sm font-medium text-primary mb-1">الفترة</label>
          <select 
            v-model="selectedPeriod"
            class="w-full rounded-lg border border-primary bg-primary px-2 py-1 sm:px-3 sm:py-2 text-sm text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 transition-colors"
          >
            <option value="morning">الصباح (9:00 - 12:00)</option>
            <option value="evening">المساء (14:00 - 17:00)</option>
            <option value="custom">مخصص</option>
          </select>
        </div>
      </div>

      <!-- Weekly Schedule -->
      <div class="space-y-3">
        <div 
          v-for="day in weekDays" 
          :key="day.key"
          class="border border-primary rounded-lg p-3"
        >
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <input 
                type="checkbox" 
                :id="`day-${day.key}`"
                v-model="localSchedule[day.key].enabled"
                class="rounded border-primary text-brand-500 focus:ring-brand-500"
              />
              <label :for="`day-${day.key}`" class="text-sm sm:text-base font-medium text-primary">
                {{ day.label }}
              </label>
            </div>
            
            <button 
              v-if="localSchedule[day.key].enabled"
              @click="addTimeSlot(day.key)"
              class="bg-tertiary hover:bg-secondary text-primary px-2 py-1 rounded-lg flex items-center gap-1 text-xs transition-colors"
            >
              <PlusIcon class="h-3 w-3" />
              إضافة وقت
            </button>
          </div>

          <!-- Time Slots -->
          <div v-if="localSchedule[day.key].enabled" class="space-y-2">
            <div 
              v-for="(slot, index) in localSchedule[day.key].slots" 
              :key="index"
              class="flex flex-col sm:flex-row items-center gap-2 p-2 bg-secondary rounded-lg"
            >
              <div class="flex items-center gap-2 flex-1 w-full">
                <input 
                  type="time" 
                  v-model="slot.start"
                  class="flex-1 rounded border border-primary bg-primary px-2 py-1 text-xs text-primary focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors"
                />
                <span class="text-secondary text-xs">إلى</span>
                <input 
                  type="time" 
                  v-model="slot.end"
                  class="flex-1 rounded border border-primary bg-primary px-2 py-1 text-xs text-primary focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors"
                />
              </div>
              
              <div class="flex items-center gap-2 w-full sm:w-auto">
                <label class="flex items-center gap-1 text-xs text-primary">
                  <input 
                    type="checkbox" 
                    v-model="slot.available"
                    class="rounded border-primary text-brand-500 focus:ring-brand-500"
                  />
                  متاح للحجز
                </label>
                
                <button 
                  @click="removeTimeSlot(day.key, index)"
                  class="text-red-500 hover:text-red-700 p-1 transition-colors"
                >
                  ✕
                </button>
              </div>
            </div>
            
            <div v-if="localSchedule[day.key].slots.length === 0" class="text-center py-3 text-secondary text-xs">
              لا توجد أوقات مضافة لهذا اليوم
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-primary">
        <button 
          @click="$emit('close')"
          class="bg-tertiary hover:bg-secondary text-primary px-3 py-2 rounded-lg transition-colors text-sm"
        >
          إلغاء
        </button>
        <button 
          @click="$emit('save')"
          class="bg-brand-500 hover:bg-brand-600 text-white px-3 py-2 rounded-lg transition-colors text-sm"
        >
          حفظ التغييرات
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const props = defineProps({
  open: {
    type: Boolean,
    required: true
  },
  selectedTherapist: {
    type: Object,
    default: () => ({})
  },
  localSchedule: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close', 'save', 'add-time-slot', 'remove-time-slot'])

const sessionDuration = ref('30')
const selectedPeriod = ref('morning')

const weekDays = [
  { key: 'saturday', label: 'السبت' },
  { key: 'sunday', label: 'الأحد' },
  { key: 'monday', label: 'الاثنين' },
  { key: 'tuesday', label: 'الثلاثاء' },
  { key: 'wednesday', label: 'الأربعاء' },
  { key: 'thursday', label: 'الخميس' },
  { key: 'friday', label: 'الجمعة' }
]

// Methods
const addTimeSlot = (dayKey) => {
  emit('add-time-slot', dayKey)
}

const removeTimeSlot = (dayKey, index) => {
  emit('remove-time-slot', dayKey, index)
}
</script>