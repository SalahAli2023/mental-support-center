<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold text-primary">المراحل</h2>
      <button
        @click="openCreateModal"
        class="btn btn-primary flex items-center gap-2"
      >
        <PlusIcon class="w-4 h-4" />
        إضافة مرحلة
      </button>
    </div>

    <!-- Phases List -->
    <div v-if="loading" class="text-center py-8">
      <div class="text-secondary">جاري التحميل...</div>
    </div>

    <div v-else-if="phases.length === 0" class="text-center py-8 text-secondary">
      لا توجد مراحل. ابدأ بإضافة مرحلة جديدة.
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="phase in phases"
        :key="phase.id"
        class="card p-4 cursor-move"
        draggable="true"
        @dragstart="handleDragStart($event, phase)"
        @dragover.prevent
        @drop="handleDrop($event, phase)"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold">
              {{ phase.phase_order }}
            </div>
            <div>
              <h3 class="font-semibold text-primary">{{ phase.name_ar }}</h3>
              <p class="text-sm text-secondary">{{ phase.description_ar || 'لا يوجد وصف' }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="badge" :class="phase.is_active ? 'badge-success' : 'badge-secondary'">
              {{ phase.is_active ? 'نشط' : 'غير نشط' }}
            </span>
            <button @click="openEditModal(phase)" class="btn btn-sm btn-ghost">
              تعديل
            </button>
            <button @click="openDeleteModal(phase)" class="btn btn-sm btn-danger">
              حذف
            </button>
          </div>
        </div>
        <div class="mt-3 text-sm text-secondary">
          عدد الجلسات: {{ phase.sessions?.length || 0 }}
        </div>
      </div>
    </div>

    <!-- Phase Modal -->
    <PhaseModal
      v-if="showModal"
      :open="showModal"
      :phase="editingPhase"
      :program-id="programId"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Delete Confirmation -->
    <DeleteConfirmModal
      v-if="showDeleteConfirm"
      :open="showDeleteConfirm"
      :item-name="deletingPhase?.name_ar || ''"
      @confirm="handleDelete"
      @close="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { programService } from '../../../services/programService'
import PhaseModal from './PhaseModal.vue'
import DeleteConfirmModal from './DeleteConfirmModal.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

interface Phase {
  id: string
  program_id: string
  name_ar: string
  name_en: string
  description_ar?: string
  description_en?: string
  phase_order: number
  is_active: boolean
  is_hidden: boolean
  sessions?: any[]
}

interface Props {
  programId: string
}

const props = defineProps<Props>()

const phases = ref<Phase[]>([])
const loading = ref(false)
const showModal = ref(false)
const showDeleteConfirm = ref(false)
const editingPhase = ref<Phase | null>(null)
const deletingPhase = ref<Phase | null>(null)
const draggedPhase = ref<Phase | null>(null)

const fetchPhases = async () => {
  loading.value = true
  try {
    const response = await programService.getPhases(props.programId)
    if (response.data.success) {
      phases.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching phases:', error)
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  editingPhase.value = null
  showModal.value = true
}

const openEditModal = (phase: Phase) => {
  editingPhase.value = { ...phase }
  showModal.value = true
}

const openDeleteModal = (phase: Phase) => {
  deletingPhase.value = phase
  showDeleteConfirm.value = true
}

const closeModal = () => {
  showModal.value = false
  editingPhase.value = null
}

const handleSave = async () => {
  await fetchPhases()
  closeModal()
}

const handleDelete = async () => {
  if (!deletingPhase.value) return
  
  try {
    await programService.deletePhase(props.programId, deletingPhase.value.id)
    await fetchPhases()
    showDeleteConfirm.value = false
    deletingPhase.value = null
  } catch (error) {
    console.error('Error deleting phase:', error)
  }
}

const handleDragStart = (event: DragEvent, phase: Phase) => {
  draggedPhase.value = phase
  if (event.dataTransfer) {
    event.dataTransfer.effectAllowed = 'move'
  }
}

const handleDrop = async (event: DragEvent, targetPhase: Phase) => {
  event.preventDefault()
  if (!draggedPhase.value || draggedPhase.value.id === targetPhase.id) return

  const draggedIndex = phases.value.findIndex(p => p.id === draggedPhase.value!.id)
  const targetIndex = phases.value.findIndex(p => p.id === targetPhase.id)

  // إعادة ترتيب محلي
  const [removed] = phases.value.splice(draggedIndex, 1)
  phases.value.splice(targetIndex, 0, removed)

  // تحديث الأوامر
  const reorderedPhases = phases.value.map((phase, index) => ({
    id: phase.id,
    order: index + 1
  }))

  try {
    await programService.reorderPhases(props.programId, reorderedPhases)
    await fetchPhases() // إعادة تحميل للتأكد من التزامن
  } catch (error) {
    console.error('Error reordering phases:', error)
    await fetchPhases() // إعادة تحميل في حالة الخطأ
  }

  draggedPhase.value = null
}

onMounted(() => {
  fetchPhases()
})

defineExpose({
  fetchPhases
})
</script>

