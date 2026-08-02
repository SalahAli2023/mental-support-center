<template>
  <div class="space-y-4 p-3">
    <!-- Header -->
    <TherapistsHeader @create-therapist="openCreateTherapist" />
    
    <!-- Filters and Search -->
    <TherapistsFilters 
      :filters="filters"
      @update:filters="updateFilters"
      @reset-filters="resetFilters"
    />

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="text-secondary">جاري تحميل البيانات...</div>
    </div>
    
    <!-- Therapists List -->
    <TherapistsList 
      v-else
      :therapists="paginatedTherapists"
      :filtered-therapists="filteredTherapists"
      @edit-therapist="editTherapist"
      @view-profile="viewProfile"
      @delete-therapist="deleteTherapist"
      @open-schedule="openSchedule"
    />
    
    <!-- Pagination -->
    <TherapistsPagination 
      :pagination="pagination"
      :filtered-therapists="filteredTherapists"
      @update:pagination="updatePagination"
    />
    
    <!-- Modals -->
    <TherapistModal 
      v-if="therapistModalOpen"
      :open="therapistModalOpen"
      :editing-therapist="editingTherapist"
      :therapist-form="therapistForm"
      :current-step="currentStep"
      @close="closeTherapistModal"
      @save="saveTherapist"
      @update:step="updateStep"
      @add-qualification="addQualification"
      @remove-qualification="removeQualification"
      @update:avatar="handleAvatarUpdate"
    />
    
    <ScheduleModal 
      v-if="scheduleModalOpen"
      :open="scheduleModalOpen"
      :selected-therapist="selectedTherapist"
      :local-schedule="localSchedule"
      @close="closeScheduleModal"
      @save="saveSchedule"
      @add-time-slot="addTimeSlot"
      @remove-time-slot="removeTimeSlot"
    />
    
    <ProfileModal 
      v-if="profileModalOpen"
      :open="profileModalOpen"
      :viewing-therapist="viewingTherapist"
      @close="closeProfileModal"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { useTherapistStore } from '@/stores/therapists'
import { useToast } from '@/composables/useToast'
import TherapistsHeader from './components/TherapistsHeader.vue'
import TherapistsFilters from './components/TherapistsFilters.vue'
import TherapistsList from './components/TherapistsList.vue'
import TherapistsPagination from './components/TherapistsPagination.vue'
import TherapistModal from './components/TherapistModal.vue'
import ScheduleModal from './components/ScheduleModal.vue'
import ProfileModal from './components/ProfileModal.vue'

const therapistStore = useTherapistStore()
const toast = useToast()

// الفلاتر
const filters = reactive({
  search: '',
  status: '',
  specialty: '',
  sort: 'name-asc'
})

// التقسيم
const pagination = reactive({
  currentPage: 1,
  itemsPerPage: 10
})

// Modals
const therapistModalOpen = ref(false)
const scheduleModalOpen = ref(false)
const profileModalOpen = ref(false)
const editingTherapist = ref(null)
const viewingTherapist = ref(null)
const selectedTherapist = ref(null)
const loading = ref(false)

// Form
const currentStep = ref(0)
const avatarFile = ref(null)

const therapistForm = reactive({
  name_ar: '',
  name_en: '',
  email: '',
  password: '',
  phone: '',
  title_ar: '',
  title_en: '',
  methodologies_ar: '',
  methodologies_en: '',
  specialty_ar: '',
  specialty_en: '',
  session_duration: 45,
  experience: 0,
  hourly_rate: 0,
  date_of_birth: '',
  gender: 'male',
  bio_ar: '',
  bio_en: '',
  status: 'active',
  qualifications: []
})

const localSchedule = ref({})

// Computed Properties
const therapists = computed(() => therapistStore.therapists)

const filteredTherapists = computed(() => {
  let filtered = therapists.value.filter(therapist => {
    const matchesSearch = !filters.search || 
      therapist.name_ar?.toLowerCase().includes(filters.search.toLowerCase()) ||
      therapist.name_en?.toLowerCase().includes(filters.search.toLowerCase()) ||
      therapist.specialty_ar?.toLowerCase().includes(filters.search.toLowerCase()) ||
      therapist.specialty_en?.toLowerCase().includes(filters.search.toLowerCase()) ||
      therapist.email?.toLowerCase().includes(filters.search.toLowerCase())
    
    const matchesStatus = !filters.status || therapist.status === filters.status
    const matchesSpecialty = !filters.specialty || therapist.specialty_ar === filters.specialty
    
    return matchesSearch && matchesStatus && matchesSpecialty
  })

  // الترتيب
  if (filters.sort === 'name-asc') {
    filtered.sort((a, b) => (a.name_ar || '').localeCompare(b.name_ar || ''))
  } else if (filters.sort === 'name-desc') {
    filtered.sort((a, b) => (b.name_ar || '').localeCompare(a.name_ar || ''))
  } else if (filters.sort === 'experience-desc') {
    filtered.sort((a, b) => (b.experience || 0) - (a.experience || 0))
  } else if (filters.sort === 'experience-asc') {
    filtered.sort((a, b) => (a.experience || 0) - (b.experience || 0))
  }

  return filtered
})

const paginatedTherapists = computed(() => {
  const start = (pagination.currentPage - 1) * pagination.itemsPerPage
  const end = start + pagination.itemsPerPage
  return filteredTherapists.value.slice(start, end)
})

// Methods
const updateFilters = (newFilters) => {
  Object.assign(filters, newFilters)
  pagination.currentPage = 1
  loadTherapists()
}

const resetFilters = () => {
  Object.assign(filters, {
    search: '',
    status: '',
    specialty: '',
    sort: 'name-asc'
  })
  pagination.currentPage = 1
  loadTherapists()
}

const updatePagination = (newPagination) => {
  Object.assign(pagination, newPagination)
  loadTherapists()
}

const updateStep = (step) => {
  currentStep.value = step
}

const openCreateTherapist = () => {
  editingTherapist.value = null
  currentStep.value = 0
  avatarFile.value = null
  
  // إعادة تعيين النموذج
  Object.assign(therapistForm, {
    name_ar: '',
    name_en: '',
    email: '',
    password: '',
    phone: '',
    title_ar: '',
    title_en: '',
    methodologies_ar: '',
    methodologies_en: '',
    specialty_ar: '',
    specialty_en: '',
    session_duration: 45,
    experience: 0,
    hourly_rate: 0,
    date_of_birth: '',
    gender: 'male',
    bio_ar: '',
    bio_en: '',
    status: 'active',
    qualifications: []
  })
  
  therapistModalOpen.value = true
}

const editTherapist = async (therapist) => {
  editingTherapist.value = therapist
  currentStep.value = 0
  
  try {
    // جلب المؤهلات من API
    const qualifications = await therapistStore.fetchQualifications(therapist.id)
    
    // تحميل بيانات المعالج للنموذج
    Object.assign(therapistForm, {
      name_ar: therapist.name_ar || '',
      name_en: therapist.name_en || '',
      email: therapist.user?.email || '',
      password: '', // لا نعيد كلمة المرور
      phone: therapist.phone || '',
      title_ar: therapist.title_ar || '',
      title_en: therapist.title_en || '',
      methodologies_ar: therapist.methodologies_ar || '',
      methodologies_en: therapist.methodologies_en || '',
      specialty_ar: therapist.specialty_ar || '',
      specialty_en: therapist.specialty_en || '',
      session_duration: therapist.session_duration || 45,
      experience: therapist.experience || 0,
      hourly_rate: therapist.hourly_rate || 0,
      date_of_birth: therapist.date_of_birth || '',
      gender: therapist.gender || 'male',
      bio_ar: therapist.bio_ar || '',
      bio_en: therapist.bio_en || '',
      status: therapist.status || 'active',
      qualifications: qualifications || []
    })
  } catch (error) {
    console.error('Error loading qualifications:', error)
    // استخدام البيانات المحلية كبديل
    Object.assign(therapistForm, {
      // ... البيانات الأساسية
      qualifications: therapist.qualifications || []
    })
  }
  
  therapistModalOpen.value = true
}

const closeTherapistModal = () => {
  therapistModalOpen.value = false
  editingTherapist.value = null
  currentStep.value = 0
  avatarFile.value = null
}

const handleAvatarUpdate = (file) => {
  avatarFile.value = file
}


const buildTherapistFormData = () => {
  const formData = new FormData()

  const therapistData = {
    name_ar: therapistForm.name_ar,
    name_en: therapistForm.name_en,
    phone: therapistForm.phone,
    title_ar: therapistForm.title_ar,
    title_en: therapistForm.title_en,
    methodologies_ar: Array.isArray(therapistForm.methodologies_ar)
      ? therapistForm.methodologies_ar
      : (therapistForm.methodologies_ar ? [therapistForm.methodologies_ar] : []),
    methodologies_en: Array.isArray(therapistForm.methodologies_en)
      ? therapistForm.methodologies_en
      : (therapistForm.methodologies_en ? [therapistForm.methodologies_en] : []),
    specialty_ar: therapistForm.specialty_ar,
    specialty_en: therapistForm.specialty_en,
    session_duration: parseInt(therapistForm.session_duration) || 45,
    experience: parseInt(therapistForm.experience) || 0,
    hourly_rate: parseFloat(therapistForm.hourly_rate) || 0,
    date_of_birth: therapistForm.date_of_birth,
    gender: therapistForm.gender,
    bio_ar: therapistForm.bio_ar,
    bio_en: therapistForm.bio_en,
    status: therapistForm.status
  }

  if (!editingTherapist.value) {
    therapistData.email = therapistForm.email
    therapistData.password = therapistForm.password
  } else {
    if (therapistForm.email && therapistForm.email !== editingTherapist.value.user?.email) {
      therapistData.email = therapistForm.email
    }
    if (therapistForm.password) {
      therapistData.password = therapistForm.password
    }
  }

  Object.keys(therapistData).forEach((key) => {
    const value = therapistData[key]
    if (value === undefined || value === null || value === '') {
      return
    }

    if (Array.isArray(value)) {
      value
        .filter((item) => item !== undefined && item !== null && item !== '')
        .forEach((item) => {
          formData.append(`${key}[]`, item)
        })
    } else {
      formData.append(key, value)
    }
  })

  if (avatarFile.value instanceof File) {
    formData.append('avatar', avatarFile.value, avatarFile.value.name || 'avatar.jpg')
  } else if (avatarFile.value === null && editingTherapist.value) {
    formData.append('remove_avatar', '1')
  }

  return formData
}

const saveTherapist = async () => {
  try {
    const formData = buildTherapistFormData()

    if (editingTherapist.value) {
      await therapistStore.updateTherapist(editingTherapist.value.id, formData)
      toast.success('تم تحديث المعالج بنجاح')
      
      // حفظ المؤهلات بعد تحديث المعالج
      if (therapistForm.qualifications && therapistForm.qualifications.length > 0) {
        // حذف المؤهلات القديمة أولاً
        try {
          const existingQualifications = await therapistStore.fetchQualifications(editingTherapist.value.id)
          for (const qualification of existingQualifications) {
            try {
              await therapistStore.deleteQualification(editingTherapist.value.id, qualification.id)
            } catch (deleteError) {
              // تجاهل خطأ 404 (المؤهل غير موجود بالفعل)
              if (deleteError.response?.status !== 404) {
                console.warn('Error deleting qualification:', deleteError)
              }
            }
          }
        } catch (fetchError) {
          console.warn('Error fetching qualifications for deletion:', fetchError)
        }
        
        // إضافة المؤهلات الجديدة
        for (const qualification of therapistForm.qualifications) {
          if (qualification.name_ar && qualification.name_en) {
            await therapistStore.createQualification(editingTherapist.value.id, {
              name_ar: qualification.name_ar,
              name_en: qualification.name_en,
              institution_ar: qualification.institution_ar,
              institution_en: qualification.institution_en,
              year: qualification.year
            })
          }
        }
      }
    } else {
      const newTherapist = await therapistStore.createTherapist(formData)
      toast.success('تم إنشاء المعالج بنجاح')
      
      // حفظ المؤهلات بعد إنشاء المعالج
      if (newTherapist && newTherapist.data && therapistForm.qualifications && therapistForm.qualifications.length > 0) {
        const therapistId = newTherapist.data.id
        
        for (const qualification of therapistForm.qualifications) {
          if (qualification.name_ar && qualification.name_en) {
            await therapistStore.createQualification(therapistId, {
              name_ar: qualification.name_ar,
              name_en: qualification.name_en,
              institution_ar: qualification.institution_ar,
              institution_en: qualification.institution_en,
              year: qualification.year
            })
          }
        }
      }
    }
    
    therapistModalOpen.value = false
    currentStep.value = 0
    await loadTherapists()
  } catch (error) {
    console.error('Error saving therapist:', error)
    toast.handleApiError(error, 'حدث خطأ أثناء حفظ بيانات المعالج')
  }
}
const addQualification = () => {
  therapistForm.qualifications.push({
    name_ar: '',
    name_en: '',
    institution_ar: '',
    institution_en: '',
    year: new Date().getFullYear()
  })
}

const removeQualification = (index) => {
  therapistForm.qualifications.splice(index, 1)
}

const viewProfile = async (therapist) => {
  try {
    viewingTherapist.value = { ...therapist }
    
    // محاولة جلب البيانات الكاملة من API
    try {
      const fullTherapist = await therapistStore.fetchTherapist(therapist.id)
      if (fullTherapist) {
        viewingTherapist.value = fullTherapist
      }
    } catch (apiError) {
      console.warn('Could not fetch full therapist data, using local data:', apiError)
    }
    
    // استخدام setTimeout لتجنب أخطاء التحديث المتزامن
    setTimeout(() => {
      profileModalOpen.value = true
    }, 100)
    
  } catch (error) {
    console.error('Error in viewProfile:', error)
    viewingTherapist.value = therapist
    setTimeout(() => {
      profileModalOpen.value = true
    }, 100)
  }
}

const closeProfileModal = () => {
  profileModalOpen.value = false
  viewingTherapist.value = null
}

const openSchedule = async (therapist) => {
  try {
    selectedTherapist.value = therapist
    
    // تهيئة الجدول المحلي
    localSchedule.value = getEmptySchedule()
    
    if (therapist.schedules && therapist.schedules.length > 0) {
      localSchedule.value = convertSchedulesToModalFormat(therapist.schedules)
    } else {
      try {
        const schedules = await therapistStore.fetchSchedules(therapist.id)
        localSchedule.value = convertSchedulesToModalFormat(schedules)
      } catch (apiError) {
        console.error('Error fetching schedules from API:', apiError)
        localSchedule.value = getEmptySchedule()
      }
    }
    
    scheduleModalOpen.value = true
  } catch (error) {
    console.error('Error in openSchedule:', error)
    selectedTherapist.value = therapist
    localSchedule.value = getEmptySchedule()
    scheduleModalOpen.value = true
  }
}

const convertSchedulesToModalFormat = (schedules) => {
  const scheduleFormat = getEmptySchedule()
  
  if (schedules && Array.isArray(schedules)) {
    schedules.forEach(schedule => {
      if (scheduleFormat[schedule.day]) {
        if (!scheduleFormat[schedule.day].slots) {
          scheduleFormat[schedule.day].slots = []
        }
        
        scheduleFormat[schedule.day].enabled = schedule.available !== false
        scheduleFormat[schedule.day].slots.push({
          start: schedule.start_time || '09:00',
          end: schedule.end_time || '10:00',
          available: schedule.available !== false
        })
      }
    })
  }
  
  return scheduleFormat
}

const getEmptySchedule = () => ({
  saturday: { enabled: false, slots: [] },
  sunday: { enabled: false, slots: [] },
  monday: { enabled: false, slots: [] },
  tuesday: { enabled: false, slots: [] },
  wednesday: { enabled: false, slots: [] },
  thursday: { enabled: false, slots: [] },
  friday: { enabled: false, slots: [] }
})

const closeScheduleModal = () => {
  scheduleModalOpen.value = false
  selectedTherapist.value = null
  localSchedule.value = {}
}

const addTimeSlot = (dayKey) => {
  if (!localSchedule.value[dayKey]) {
    localSchedule.value[dayKey] = { enabled: true, slots: [] }
  }
  
  if (!localSchedule.value[dayKey].slots) {
    localSchedule.value[dayKey].slots = []
  }
  
  localSchedule.value[dayKey].slots.push({
    start: '09:00',
    end: '10:00',
    available: true
  })
}

const removeTimeSlot = (dayKey, index) => {
  if (localSchedule.value[dayKey] && localSchedule.value[dayKey].slots) {
    localSchedule.value[dayKey].slots.splice(index, 1)
  }
}

const saveSchedule = async () => {
  try {
    if (selectedTherapist.value) {
      // تحويل الجدول المحلي إلى تنسيق API
      const schedulesToSave = []
      
      for (const day in localSchedule.value) {
        if (localSchedule.value[day].enabled && localSchedule.value[day].slots.length > 0) {
          for (const slot of localSchedule.value[day].slots) {
            if (slot.available) {
              schedulesToSave.push({
                day: day,
                start_time: slot.start,
                end_time: slot.end,
                available: true,
                slot_duration: selectedTherapist.value.session_duration || 45,
                recurrence: 'weekly'
              })
            }
          }
        }
      }
      
      // حذف الجداول القديمة أولاً
      try {
        const existingSchedules = await therapistStore.fetchSchedules(selectedTherapist.value.id)
        for (const schedule of existingSchedules) {
          await therapistStore.deleteSchedule(selectedTherapist.value.id, schedule.id)
        }
      } catch (deleteError) {
        console.warn('Error deleting old schedules:', deleteError)
      }
      
      // إضافة الجداول الجديدة
      for (const scheduleData of schedulesToSave) {
        try {
          await therapistStore.createSchedule(selectedTherapist.value.id, scheduleData)
        } catch (createError) {
          console.error('Error creating schedule:', createError)
        }
      }
      
      alert('تم حفظ الجدول بنجاح')
    }
    
    scheduleModalOpen.value = false
    await loadTherapists() // إعادة تحميل البيانات للتأكد من ظهور التغييرات
  } catch (error) {
    console.error('Error saving schedule:', error)
    alert('حدث خطأ أثناء حفظ الجدول: ' + (error.response?.data?.message || error.message))
  }
}

const loadTherapists = async () => {
  try {
    loading.value = true
    const apiFilters = {
      search: filters.search,
      status: filters.status,
      specialty: filters.specialty
    }
    
    await therapistStore.fetchTherapists(apiFilters)
    
  } catch (error) {
    console.error('Error loading therapists:', error)
  } finally {
    loading.value = false
  }
}

const deleteTherapist = async (therapist) => {
  if (confirm(`هل أنت متأكد من حذف ${therapist.name_ar}؟`)) {
    try {
      await therapistStore.deleteTherapist(therapist.id)
      toast.success('تم حذف المعالج بنجاح')
      await loadTherapists()
    } catch (error) {
      console.error('Error deleting therapist:', error)
      toast.handleApiError(error, 'حدث خطأ أثناء حذف المعالج')
    }
  }
}

// تحسينات للجوال
const isMobile = ref(false)

const checkScreenSize = () => {
  isMobile.value = window.innerWidth < 768
}

onMounted(() => {
  checkScreenSize()
  window.addEventListener('resize', checkScreenSize)
  loadTherapists()
})

onUnmounted(() => {
  window.removeEventListener('resize', checkScreenSize)
})
</script>