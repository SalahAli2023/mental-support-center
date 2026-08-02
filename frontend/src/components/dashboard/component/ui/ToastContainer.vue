<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[9999] space-y-2 pointer-events-none">
      <TransitionGroup name="toast" tag="div">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="[
            'pointer-events-auto min-w-[320px] max-w-md rounded-lg shadow-lg p-4 flex items-start gap-3 animate-slide-in',
            toastClasses[toast.type]
          ]"
        >
          <!-- الأيقونة -->
          <div :class="iconClasses[toast.type]">
            <component :is="icons[toast.type]" class="w-5 h-5" />
          </div>
          
          <!-- الرسالة -->
          <div class="flex-1">
            <p class="text-sm font-medium" v-html="toast.message"></p>
          </div>
          
          <!-- زر الإغلاق -->
          <button
            @click="removeToast(toast.id)"
            :class="closeButtonClasses[toast.type]"
            class="flex-shrink-0 rounded-md p-1 hover:opacity-70 transition-opacity"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useToast } from '@/composables/useToast'
import { 
  CheckCircleIcon, 
  XCircleIcon, 
  ExclamationTriangleIcon,
  InformationCircleIcon 
} from '@heroicons/vue/24/solid'

const { toasts, removeToast } = useToast()

const icons = {
  success: CheckCircleIcon,
  error: XCircleIcon,
  warning: ExclamationTriangleIcon,
  info: InformationCircleIcon
}

const toastClasses = {
  success: 'bg-green-50 border border-green-200 text-green-800',
  error: 'bg-red-50 border border-red-200 text-red-800',
  warning: 'bg-yellow-50 border border-yellow-200 text-yellow-800',
  info: 'bg-blue-50 border border-blue-200 text-blue-800'
}

const iconClasses = {
  success: 'text-green-500 flex-shrink-0',
  error: 'text-red-500 flex-shrink-0',
  warning: 'text-yellow-500 flex-shrink-0',
  info: 'text-blue-500 flex-shrink-0'
}

const closeButtonClasses = {
  success: 'text-green-600 hover:text-green-800',
  error: 'text-red-600 hover:text-red-800',
  warning: 'text-yellow-600 hover:text-yellow-800',
  info: 'text-blue-600 hover:text-blue-800'
}
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-move {
  transition: transform 0.3s ease;
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}
</style>





