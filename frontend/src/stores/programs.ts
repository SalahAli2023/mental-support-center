import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { programService } from '@/services/programService'

export const useProgramStore = defineStore('programs', () => {
  const programs = ref<any[]>([])
  const currentProgram = ref<any>(null)
  const userProgress = ref<any>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const activePrograms = computed(() => 
    programs.value.filter(p => p.status === 'active')
  )

  const enrolledPrograms = computed(() =>
    programs.value.filter(p => userProgress.value?.program_id === p.id)
  )

  // Actions
  const fetchPrograms = async (params?: any) => {
    loading.value = true
    error.value = null
    try {
      const response = await programService.getAllPrograms(params)
      if (response.data.success) {
        programs.value = response.data.data
      }
    } catch (err: any) {
      error.value = err.message || 'حدث خطأ أثناء جلب البرامج'
    } finally {
      loading.value = false
    }
  }

  const fetchProgram = async (id: string) => {
    loading.value = true
    error.value = null
    try {
      const response = await programService.getProgram(id)
      if (response.data.success) {
        currentProgram.value = response.data.data
      }
    } catch (err: any) {
      error.value = err.message || 'حدث خطأ أثناء جلب البرنامج'
    } finally {
      loading.value = false
    }
  }

  const fetchUserProgress = async (programId: string) => {
    try {
      const response = await programService.getUserProgress(programId)
      if (response.data.success) {
        userProgress.value = response.data.data.user_program
        return response.data.data
      }
    } catch (err: any) {
      // User not enrolled
      userProgress.value = null
      return null
    }
  }

  const enrollInProgram = async (programId: string) => {
    try {
      // يمكن استخدام API مباشرة
      await fetchUserProgress(programId)
    } catch (err: any) {
      error.value = err.message || 'حدث خطأ أثناء التسجيل'
      throw err
    }
  }

  const startActivity = async (programId: string, activityId: string) => {
    try {
      await programService.startActivity(programId, activityId)
      await fetchUserProgress(programId)
    } catch (err: any) {
      error.value = err.message || 'حدث خطأ أثناء بدء النشاط'
      throw err
    }
  }

  const completeActivity = async (programId: string, activityId: string, data?: any) => {
    try {
      await programService.completeActivity(programId, activityId, data)
      await fetchUserProgress(programId)
    } catch (err: any) {
      error.value = err.message || 'حدث خطأ أثناء إكمال النشاط'
      throw err
    }
  }

  const checkActivityStatus = async (programId: string, activityId: string) => {
    try {
      const response = await programService.checkActivityStatus(programId, activityId)
      return response.data.data
    } catch (err: any) {
      return { is_unlocked: false, status: 'locked' }
    }
  }

  return {
    // State
    programs,
    currentProgram,
    userProgress,
    loading,
    error,
    // Getters
    activePrograms,
    enrolledPrograms,
    // Actions
    fetchPrograms,
    fetchProgram,
    fetchUserProgress,
    enrollInProgram,
    startActivity,
    completeActivity,
    checkActivityStatus
  }
})




