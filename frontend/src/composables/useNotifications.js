import { useToast } from './useToast'

export const useNotifications = () => {
    // استخدام نظام التوست الموحد في المشروع (ESM import)
    const { success, error, handleApiError } = useToast()

    const showSuccess = (message) => {
        // توست نجاح بسيط
        success(message)
    }

    /**
     * showError يمكن أن تستقبل:
     * - نص رسالة جاهز
     * - كائن خطأ من axios مع رسالة افتراضية
     */
    const showError = (errOrMessage, fallbackMessage) => {
        // إذا كان كائن خطأ من API → نستخدم handleApiError لعرض سبب أكثر وضوحاً
        if (errOrMessage && (errOrMessage.response || errOrMessage.request || errOrMessage instanceof Error)) {
            return handleApiError(errOrMessage, fallbackMessage || 'حدث خطأ غير متوقع')
        }

        // إذا كانت مجرد رسالة نصية
        return error(errOrMessage || fallbackMessage || 'حدث خطأ غير متوقع')
    }

    return {
        showSuccess,
        showError,
        handleApiError,
    }
}