[file name]: PatientsManagement.vue
[file content begin]
<template>
  <div class="space-y-6 p-4">
    <!-- Header -->
    <PatientsHeader @create-patient="openCreatePatient" />
    
    <!-- Filters -->
    <PatientsFilters 
      :filters="localFilters"
      @apply-filters="applyFilters"
      @reset-filters="resetFilters"
      @update:filters="updateFilters"
    />
    
    <!-- Table -->
    <PatientsTable 
      :patients="patientsStore.patients"
      :loading="patientsStore.loading"
      @view-profile="viewProfile"
      @edit-patient="editPatient"
      @delete-patient="deletePatient"
      @open-sessions="openSessionsModal"
    />
    
    <!-- Empty State -->
    <PatientsEmptyState 
      v-if="!patientsStore.loading && patientsStore.patients.length === 0"
      @create-patient="openCreatePatient"
    />
    
    <!-- Pagination -->
    <PatientsPagination 
      v-if="patientsStore.patients.length > 0"
      :current-page="patientsStore.currentPage"
      :total-pages="patientsStore.totalPages"
      :start-item="startItem"
      :end-item="endItem"
      :total-items="patientsStore.totalItems"
      :items-per-page="patientsStore.perPage"
      :visible-pages="visiblePages"
      @go-to-page="goToPage"
      @update-items-per-page="updateItemsPerPage"
    />
    
    <!-- Modals -->
    <PatientModal
      :open="patientModalOpen"
      :patient="editingPatient"
      @close="closePatientModal"
      @save="savePatient"
    />
    
    <ProfileModal
      :open="profileModalOpen"
      :patient="viewingPatient"
      @close="closeProfileModal"
    />
    
    <SessionsModal
      :open="sessionsModalOpen"
      :patient="selectedPatientForSessions"
      @close="closeSessionsModal"
      @add-session="openAddSessionModal"
      @edit-session="editSession"
      @delete-session="deleteSession"
    />
    
    <SessionModal
  :open="sessionModalOpen"
  :session="editingSession"
  :patient-id="selectedPatientForSessions?.id"
  @close="closeSessionModal"
  @save="saveSession"
/>
    
    <!-- Delete Confirmation Modals -->
    <DeleteConfirmationModal
      :open="deletePatientModalOpen"
      title="حذف المريض"
      :message="`هل أنت متأكد من حذف ${patientToDelete?.name}؟`"
      @confirm="confirmDeletePatient"
      @cancel="cancelDeletePatient"
    />
    
    <DeleteConfirmationModal
      :open="deleteSessionModalOpen"
      title="حذف الجلسة"
      :message="deleteSessionMessage"
      @confirm="confirmDeleteSession"
      @cancel="cancelDeleteSession"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { usePatientsStore } from '@/stores/patients'
import { usePatientSessionsStore } from '@/stores/patientSessions'
import PatientsHeader from './components/PatientsHeader.vue'
import PatientsFilters from './components/PatientsFilters.vue'
import PatientsTable from './components/PatientsTable.vue'
import PatientsEmptyState from './components/PatientsEmptyState.vue'
import PatientsPagination from './components/PatientsPagination.vue'
import PatientModal from './components/modals/PatientModal.vue'
import ProfileModal from './components/modals/ProfileModal.vue'
import SessionsModal from './components/modals/SessionsModal.vue'
import SessionModal from './components/modals/SessionModal.vue'
import DeleteConfirmationModal from './components/modals/DeleteConfirmationModal.vue'

// استخدام stores
const patientsStore = usePatientsStore()
const sessionsStore = usePatientSessionsStore()

// الفلاتر المحلية
const localFilters = reactive({
  search: '',
  status: '',
  condition: '',
  sort: 'date-desc'
})

// النوافذ المنبثقة
const patientModalOpen = ref(false)
const profileModalOpen = ref(false)
const sessionsModalOpen = ref(false)
const sessionModalOpen = ref(false)
const deletePatientModalOpen = ref(false)
const deleteSessionModalOpen = ref(false)

const editingPatient = ref(null)
const viewingPatient = ref(null)
const selectedPatientForSessions = ref(null)
const editingSession = ref(null)
const patientToDelete = ref(null)
const sessionToDelete = ref(null)

// رسالة حذف الجلسة
const deleteSessionMessage = computed(() => {
  if (sessionToDelete.value?.title_ar) {
    return `هل أنت متأكد من حذف الجلسة "${sessionToDelete.value.title_ar}"؟`
  }
  return 'هل أنت متأكد من حذف هذه الجلسة؟'
})

// الحسابات
const startItem = computed(() => {
  return ((patientsStore.currentPage - 1) * patientsStore.perPage) + 1
})

const endItem = computed(() => {
  return Math.min(patientsStore.currentPage * patientsStore.perPage, patientsStore.totalItems)
})

const visiblePages = computed(() => {
  const pages = []
  const total = patientsStore.totalPages
  const current = patientsStore.currentPage
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) pages.push(i)
      pages.push('...')
      pages.push(total)
    } else if (current >= total - 3) {
      pages.push(1)
      pages.push('...')
      for (let i = total - 4; i <= total; i++) pages.push(i)
    } else {
      pages.push(1)
      pages.push('...')
      for (let i = current - 1; i <= current + 1; i++) pages.push(i)
      pages.push('...')
      pages.push(total)
    }
  }
  
  return pages
})

// دوال الفلترة
const resetFilters = () => {
  Object.assign(localFilters, {
    search: '',
    status: '',
    condition: '',
    sort: 'date-desc'
  })
  patientsStore.resetFilters()
  patientsStore.setPage(1)
  patientsStore.fetchPatients()
}

const applyFilters = () => {
  patientsStore.setFilters(localFilters)
  patientsStore.setPage(1)
  patientsStore.fetchPatients()
}

const updateFilters = (newFilters) => {
  Object.assign(localFilters, newFilters)
}

// دوال التقسيم الصفحي
const goToPage = (page) => {
  if (page >= 1 && page <= patientsStore.totalPages && page !== patientsStore.currentPage) {
    patientsStore.setPage(page)
    patientsStore.fetchPatients()
  }
}

const updateItemsPerPage = (value) => {
  patientsStore.setPerPage(parseInt(value))
  patientsStore.fetchPatients()
}

// دوال المرضى
const openCreatePatient = () => {
  editingPatient.value = null
  patientModalOpen.value = true
}

const editPatient = async (patient) => {
  try {
    // جلب بيانات المريض الكاملة من الـ API
    await patientsStore.fetchPatientById(patient.id)
    editingPatient.value = patientsStore.currentPatient
    patientModalOpen.value = true
  } catch (error) {
    console.error('Error fetching patient details:', error)
    editingPatient.value = patient
    patientModalOpen.value = true
  }
}

const viewProfile = async (patient) => {
  try {
    // جلب بيانات المريض الكاملة من الـ API
    await patientsStore.fetchPatientById(patient.id)
    viewingPatient.value = patientsStore.currentPatient
    profileModalOpen.value = true
  } catch (error) {
    console.error('Error fetching patient profile:', error)
    viewingPatient.value = patient
    profileModalOpen.value = true
  }
}

const closePatientModal = () => {
  patientModalOpen.value = false
  editingPatient.value = null
  patientsStore.clearError()
}

const closeProfileModal = () => {
  profileModalOpen.value = false
  viewingPatient.value = null
}

const savePatient = async (patientData) => {
  try {
    if (editingPatient.value) {
      // تحديث المريض الموجود
      await patientsStore.updatePatient(editingPatient.value.id, patientData)
    } else {
      // إضافة مريض جديد
      await patientsStore.createPatient(patientData)
    }
    
    patientModalOpen.value = false
  } catch (error) {
    console.error('Error saving patient:', error)
    // الخطأ سيتم عرضه في الـ store
  }
}

// دوال حذف المريض
const deletePatient = (patient) => {
  patientToDelete.value = patient
  deletePatientModalOpen.value = true
}

const confirmDeletePatient = async () => {
  if (patientToDelete.value) {
    try {
      await patientsStore.deletePatient(patientToDelete.value.id)
      deletePatientModalOpen.value = false
      patientToDelete.value = null
    } catch (error) {
      console.error('Error deleting patient:', error)
    }
  }
}

const cancelDeletePatient = () => {
  deletePatientModalOpen.value = false
  patientToDelete.value = null
}

// دوال الجلسات
const openSessionsModal = async (patient) => {
  try {
    selectedPatientForSessions.value = patient
    sessionsModalOpen.value = true
    
    // جلب جلسات المريض من الـ API
    await sessionsStore.fetchSessions(patient.id)
    
    // جلب إحصائيات الجلسات
    await sessionsStore.fetchStats(patient.id)
    
    // جلب قائمة المعالجين
    await sessionsStore.fetchTherapists()
    
  } catch (error) {
    console.error('Error opening sessions modal:', error)
  }
}

const closeSessionsModal = () => {
  sessionsModalOpen.value = false
  selectedPatientForSessions.value = null
  sessionsStore.clearSessions()
  sessionsStore.clearError()
}

const refreshSessions = async () => {
  if (selectedPatientForSessions.value) {
    try {
      await sessionsStore.fetchSessions(selectedPatientForSessions.value.id)
      await sessionsStore.fetchStats(selectedPatientForSessions.value.id)
    } catch (error) {
      console.error('Error refreshing sessions:', error)
    }
  }
}

const openAddSessionModal = () => {
  editingSession.value = null
  sessionModalOpen.value = true
}

const editSession = async (session) => {
  try {
    // جلب بيانات الجلسة الكاملة من الـ API
    await sessionsStore.fetchSession(selectedPatientForSessions.value.id, session.id)
    editingSession.value = sessionsStore.formatSessionForForm(sessionsStore.currentSession)
    sessionModalOpen.value = true
  } catch (error) {
    console.error('Error fetching session details:', error)
    editingSession.value = sessionsStore.formatSessionForForm(session)
    sessionModalOpen.value = true
  }
}

const closeSessionModal = () => {
  sessionModalOpen.value = false
  editingSession.value = null
  sessionsStore.clearError()
}

const saveSession = async (sessionData) => {
  try {
    if (editingSession.value && sessionsStore.currentSession) {
      // تحديث الجلسة
      await sessionsStore.updateSession(
        selectedPatientForSessions.value.id, 
        sessionsStore.currentSession.id, 
        sessionData
      )
    } else {
      // إضافة جلسة جديدة
      await sessionsStore.createSession(
        selectedPatientForSessions.value.id, 
        sessionData
      )
    }
    
    sessionModalOpen.value = false
    
    // تحديث الإحصائيات بعد الحفظ
    await sessionsStore.fetchStats(selectedPatientForSessions.value.id)
    
    // تحديث قائمة الجلسات
    await sessionsStore.fetchSessions(selectedPatientForSessions.value.id)
    
  } catch (error) {
    console.error('Error saving session:', error)
    // الخطأ سيتم عرضه في الـ store
  }
}

// دوال حذف الجلسة
const deleteSession = (session) => {
  sessionToDelete.value = session
  deleteSessionModalOpen.value = true
}

const confirmDeleteSession = async () => {
  if (sessionToDelete.value && selectedPatientForSessions.value) {
    try {
      await sessionsStore.deleteSession(
        selectedPatientForSessions.value.id, 
        sessionToDelete.value.id
      )
      
      // تحديث الإحصائيات بعد الحذف
      await sessionsStore.fetchStats(selectedPatientForSessions.value.id)
      
      deleteSessionModalOpen.value = false
      sessionToDelete.value = null
    } catch (error) {
      console.error('Error deleting session:', error)
    }
  }
}

const cancelDeleteSession = () => {
  deleteSessionModalOpen.value = false
  sessionToDelete.value = null
}

// جلب المواعيد المتاحة
const fetchAvailableSlots = async (therapistId, sessionDate) => {
  if (selectedPatientForSessions.value) {
    try {
      await sessionsStore.fetchAvailableSlots(
        selectedPatientForSessions.value.id,
        therapistId,
        sessionDate
      )
    } catch (error) {
      console.error('Error fetching available slots:', error)
    }
  }
}

// تحديث حالة الجلسة
const updateSessionStatus = async (sessionId, status) => {
  if (selectedPatientForSessions.value) {
    try {
      await sessionsStore.updateSessionStatus(
        selectedPatientForSessions.value.id,
        sessionId,
        status
      )
    } catch (error) {
      console.error('Error updating session status:', error)
    }
  }
}

// تحديث تقدم الجلسة
const updateSessionProgress = async (sessionId, progress) => {
  if (selectedPatientForSessions.value) {
    try {
      await sessionsStore.updateSessionProgress(
        selectedPatientForSessions.value.id,
        sessionId,
        progress
      )
    } catch (error) {
      console.error('Error updating session progress:', error)
    }
  }
}

// إضافة ملاحظات للجلسة
const addSessionNotes = async (sessionId, notes) => {
  if (selectedPatientForSessions.value) {
    try {
      await sessionsStore.addSessionNotes(
        selectedPatientForSessions.value.id,
        sessionId,
        notes
      )
    } catch (error) {
      console.error('Error adding session notes:', error)
    }
  }
}

// إضافة تقرير للجلسة
const addSessionReport = async (sessionId, report) => {
  if (selectedPatientForSessions.value) {
    try {
      await sessionsStore.addSessionReport(
        selectedPatientForSessions.value.id,
        sessionId,
        report
      )
    } catch (error) {
      console.error('Error adding session report:', error)
    }
  }
}

// رفع مرفقات للجلسة
const uploadSessionAttachments = async (sessionId, attachments) => {
  if (selectedPatientForSessions.value) {
    try {
      await sessionsStore.uploadSessionAttachments(
        selectedPatientForSessions.value.id,
        sessionId,
        attachments
      )
    } catch (error) {
      console.error('Error uploading session attachments:', error)
    }
  }
}

// حذف مرفق من الجلسة
const deleteSessionAttachment = async (sessionId, attachmentIndex) => {
  if (selectedPatientForSessions.value) {
    try {
      await sessionsStore.deleteSessionAttachment(
        selectedPatientForSessions.value.id,
        sessionId,
        attachmentIndex
      )
    } catch (error) {
      console.error('Error deleting session attachment:', error)
    }
  }
}

// جلب البيانات عند التحميل
onMounted(() => {
  patientsStore.fetchPatients()
})

// مراقبة تغييرات الفلاتر للتحديث التلقائي
watch(localFilters, () => {
  applyFilters()
}, { deep: true, immediate: false })
</script>
