<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold text-primary">الجلسات</h2>
      <button @click="openCreateModal" class="btn btn-primary flex items-center gap-2">
        <PlusIcon class="w-4 h-4" />
        إضافة جلسة
      </button>
    </div>

    <div v-if="loading" class="text-center py-8">
      <div class="text-secondary">جاري التحميل...</div>
    </div>

    <div v-else-if="sessions.length === 0" class="text-center py-8 text-secondary">
      لا توجد جلسات. ابدأ بإضافة جلسة جديدة.
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="session in sessions"
        :key="session.id"
        class="card p-4"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold">
              {{ session.session_order }}
            </div>
            <div>
              <h3 class="font-semibold text-primary">{{ session.title_ar }}</h3>
              <p class="text-sm text-secondary">
                {{ session.phase?.name_ar || 'بدون مرحلة' }} • 
                {{ session.activities_count || 0 }} نشاط
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button @click="openEditModal(session)" class="btn btn-sm btn-ghost">تعديل</button>
            <button @click="viewSessionDetails(session)" class="btn btn-sm btn-primary">عرض التفاصيل</button>
            <button @click="openDeleteModal(session)" class="btn btn-sm btn-danger">حذف</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Session Modal -->
    <SessionModal
      v-if="showModal"
      :open="showModal"
      :session="editingSession"
      :program-id="programId"
      :phases="phases"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Delete Confirmation -->
    <DeleteConfirmModal
      v-if="showDeleteConfirm"
      :open="showDeleteConfirm"
      :item-name="deletingSession?.title_ar || ''"
      @confirm="handleDelete"
      @close="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { programService } from '../../../services/programService'
import SessionModal from './SessionModal.vue'
import DeleteConfirmModal from './DeleteConfirmModal.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'
import { useRouter } from 'vue-router'

interface Session {
  id: string
  program_id: string
  phase_id?: string
  title_ar: string
  session_order: number
  activities_count?: number
  phase?: {
    name_ar: string
  }
}

interface Props {
  programId: string
  phases?: any[]
}

const props = defineProps<Props>()
const router = useRouter()

const sessions = ref<Session[]>([])
const loading = ref(false)
const showModal = ref(false)
const showDeleteConfirm = ref(false)
const editingSession = ref<Session | null>(null)
const deletingSession = ref<Session | null>(null)

const fetchSessions = async () => {
  loading.value = true
  try {
    const response = await programService.getAllSessions({ program_id: props.programId })
    if (response.data.success) {
      sessions.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching sessions:', error)
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  editingSession.value = null
  showModal.value = true
}

const openEditModal = (session: Session) => {
  editingSession.value = { ...session }
  showModal.value = true
}

const viewSessionDetails = (session: Session) => {
  router.push(`/admin/programs/${props.programId}/sessions/${session.id}`)
}

const openDeleteModal = (session: Session) => {
  deletingSession.value = session
  showDeleteConfirm.value = true
}

const closeModal = () => {
  showModal.value = false
  editingSession.value = null
}

const handleSave = async () => {
  await fetchSessions()
  closeModal()
}

const handleDelete = async () => {
  if (!deletingSession.value) return
  
  try {
    await programService.deleteSession(deletingSession.value.id)
    await fetchSessions()
    showDeleteConfirm.value = false
    deletingSession.value = null
  } catch (error) {
    console.error('Error deleting session:', error)
  }
}

onMounted(() => {
  fetchSessions()
})

defineExpose({
  fetchSessions
})
</script>




