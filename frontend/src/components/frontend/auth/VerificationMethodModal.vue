<template>
    <div v-if="show" class="modal-overlay overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[60] flex justify-center items-center w-full md:inset-0 h-full max-h-full transition-all duration-300">
        <div class="relative p-4 w-full max-h-full max-w-md">
            <div class="relative bg-white rounded-2xl shadow-xl sliding-down" :class="isRTL ? 'text-right' : 'text-left'">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 rounded-t-2xl">
                    <h3 class="text-xl font-bold text-gray-800">{{ translate('verify.title') }}</h3>
                    <button 
                        type="button" 
                        @click="closeModal"
                        class="text-gray-500 hover:text-gray-700 transition-colors p-2 rounded-lg hover:bg-gray-100"
                    >
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-4">
                    <!-- SMS Option -->
                    <button 
                        @click="selectMethod('sms')"
                        class="w-full flex items-center border-2 rounded-xl p-4 transition-all duration-200"
                        :class="[
                            selectedMethod === 'sms' ? 'border-primary-green bg-green-50 shadow-sm' : 'border-gray-300 hover:border-primary-green hover:bg-gray-50',
                            isRTL ? 'flex-row-reverse' : ''
                        ]"
                    >
                        <div :class="isRTL ? 'ml-4' : 'mr-4'" class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.1768 10.3232C12.2744 10.4208 12.2744 10.5791 12.1768 10.6767C12.0791 10.7744 11.9209 10.7744 11.8232 10.6767C11.7256 10.5791 11.7256 10.4208 11.8232 10.3232C11.9209 10.2256 12.0791 10.2256 12.1768 10.3232" 
                                        :stroke="selectedMethod === 'sms' ? '#10B981' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M16.1768 10.3232C16.2744 10.4208 16.2744 10.5791 16.1768 10.6767C16.0791 10.7744 15.9209 10.7744 15.8232 10.6767C15.7256 10.5791 15.7256 10.4208 15.8232 10.3232C15.9209 10.2256 16.0791 10.2256 16.1768 10.3232" 
                                        :stroke="selectedMethod === 'sms' ? '#10B981' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8.17678 10.3232C8.27441 10.4208 8.27441 10.5791 8.17678 10.6767C8.07915 10.7744 7.92085 10.7744 7.82322 10.6767C7.72559 10.5791 7.72559 10.4208 7.82322 10.3232C7.92085 10.2256 8.07915 10.2256 8.17678 10.3232" 
                                        :stroke="selectedMethod === 'sms' ? '#10B981' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 21L8 17.001V17H5C3.895 17 3 16.105 3 15V5C3 3.895 3.895 3 5 3H19C20.105 3 21 3.895 21 5V15C21 16.105 20.105 17 19 17H16L12 20.999" 
                                        :stroke="selectedMethod === 'sms' ? '#10B981' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1" :class="isRTL ? 'text-right' : 'text-left'">
                            <div class="text-base font-medium mb-1" 
                                :class="selectedMethod === 'sms' ? 'text-primary-green' : 'text-gray-800'">
                                {{ getTranslatedTitle('verify.sms.title') }}
                            </div>
                            <div class="text-sm" 
                                :class="selectedMethod === 'sms' ? 'text-primary-green' : 'text-gray-600'">
                                {{ getTranslatedDescription('verify.sms.description') }}
                            </div>
                        </div>
                        <div v-if="selectedMethod === 'sms'" class="w-6 h-6 rounded-full bg-primary-green flex items-center justify-center flex-shrink-0">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M11.5 4L6 9.5L3.5 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </button>

                    <!-- WhatsApp Option -->
                    <button 
                        @click="selectMethod('whatsapp')"
                        class="w-full flex items-center border-2 rounded-xl p-4 transition-all duration-200"
                        :class="[
                            selectedMethod === 'whatsapp' ? 'border-blue-500 bg-blue-50 shadow-sm' : 'border-gray-300 hover:border-blue-500 hover:bg-gray-50',
                            isRTL ? 'flex-row-reverse' : ''
                        ]"
                    >
                        <div :class="isRTL ? 'ml-4' : 'mr-4'" class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg width="24" height="24" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M24.2719 7.68266C22.0772 5.48666 19.1585 4.27599 16.0492 4.27466C9.63986 4.27466 4.4252 9.48666 4.42386 15.8933C4.4212 17.932 4.95586 19.936 5.97453 21.7027L4.3252 27.724L10.4879 26.108C12.1932 27.036 14.1025 27.5227 16.0439 27.5227H16.0492C22.4559 27.5227 27.6705 22.3093 27.6732 15.9027C27.6745 12.7987 26.4665 9.87999 24.2719 7.68266Z" 
                                            :stroke="selectedMethod === 'whatsapp' ? '#2563EB' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17.46 18.08L18.0013 17.5427C18.4986 17.0493 19.2853 16.9867 19.8586 17.3893C20.4133 17.7787 20.9146 18.128 21.3813 18.4533C22.1226 18.968 22.212 20.024 21.5733 20.6613L21.0946 21.14" 
                                            :stroke="selectedMethod === 'whatsapp' ? '#2563EB' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10.8599 10.9053L11.3385 10.4267C11.9759 9.78934 13.0319 9.87867 13.5465 10.6187C13.8705 11.0853 14.2199 11.5867 14.6105 12.1413C15.0132 12.7147 14.9519 13.5013 14.4572 13.9987L13.9199 14.54" 
                                            :stroke="selectedMethod === 'whatsapp' ? '#2563EB' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21.095 21.14C19.1203 23.1054 15.8016 21.436 13.1816 18.8147" 
                                            :stroke="selectedMethod === 'whatsapp' ? '#2563EB' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.1841 18.8187C10.5641 16.1973 8.89476 12.88 10.8601 10.9053" 
                                            :stroke="selectedMethod === 'whatsapp' ? '#2563EB' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.9199 14.54C14.3453 15.2106 14.8906 15.8746 15.5066 16.4906L15.5093 16.4933C16.1253 17.1093 16.7893 17.6546 17.4599 18.08" 
                                            :stroke="selectedMethod === 'whatsapp' ? '#2563EB' : '#6B7280'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1" :class="isRTL ? 'text-right' : 'text-left'">
                            <div class="text-base font-medium mb-1" 
                                :class="selectedMethod === 'whatsapp' ? 'text-blue-600' : 'text-gray-800'">
                                {{ getTranslatedTitle('verify.whatsapp.title') }}
                            </div>
                            <div class="text-sm" 
                                :class="selectedMethod === 'whatsapp' ? 'text-blue-600' : 'text-gray-600'">
                                {{ getTranslatedDescription('verify.whatsapp.description') }}
                            </div>
                        </div>
                        <div v-if="selectedMethod === 'whatsapp'" class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M11.5 4L6 9.5L3.5 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </button>
                </div>

                <!-- Footer -->
                <div class="flex items-center p-6 border-t border-gray-200 rounded-b-2xl gap-3" >
                    <button 
                        type="button" 
                        @click="closeModal"
                        class="flex-1 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                    >
                        {{ getTranslatedTitle('verify.cancel') }}
                    </button>
                    <button 
                        type="button" 
                        @click="confirmMethod"
                        :disabled="!selectedMethod"
                        class="flex-1 py-3 text-white bg-primary-green rounded-lg hover:bg-opacity-90 transition-all disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                    >
                        {{ getTranslatedTitle('verify.continue') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { t } from '@/locales'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    initialMethod: {
        type: String,
        default: 'sms'
    },
    language: {
        type: String,
        default: 'ar'
    }
})

const emit = defineEmits(['close', 'confirm'])

const selectedMethod = ref(props.initialMethod)

// Check if current language is RTL
const isRTL = computed(() => {
    return props.language === 'ar'
})

// Translation functions
const translate = (key) => {
    return t(key, props.language)
}

const getTranslatedTitle = (key) => {
    const translation = t(key, props.language)
    return typeof translation === 'object' ? translation[props.language] : translation
}

const getTranslatedDescription = (key) => {
    const translation = t(key, props.language)
    return typeof translation === 'object' ? translation[props.language] : translation
}

// Watch for prop changes
watch(() => props.initialMethod, (newMethod) => {
    selectedMethod.value = newMethod
})

watch(() => props.show, (newVal) => {
    if (newVal) {
        selectedMethod.value = props.initialMethod
    }
})

const selectMethod = (method) => {
    selectedMethod.value = method
}

const confirmMethod = () => {
    emit('confirm', selectedMethod.value)
    closeModal()
}

const closeModal = () => {
    emit('close')
}
</script>

<style scoped>
    .sliding-down {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>