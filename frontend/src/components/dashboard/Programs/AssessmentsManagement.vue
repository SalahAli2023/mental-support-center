<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold text-primary">التقييمات</h2>
      <button @click="openCreateModal" class="btn btn-primary flex items-center gap-2">
        <PlusIcon class="w-4 h-4" />
        إضافة تقييم
      </button>
    </div>

    <div v-if="loading" class="text-center py-8">
      <div class="text-secondary">جاري التحميل...</div>
    </div>

    <div v-else-if="assessments.length === 0" class="text-center py-8 text-secondary">
      لا توجد تقييمات. ابدأ بإضافة تقييم جديد.
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="assessment in assessments"
        :key="assessment.id"
        class="card p-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-primary">
              {{ assessment.scale?.name_ar || 'تقييم' }}
            </h3>
            <p class="text-sm text-secondary">
              {{ assessment.assessment_type === 'pre' ? 'تقييم قبلي' : 'تقييم بعدي' }}
              {{ assessment.is_mandatory ? '• إلزامي' : '• اختياري' }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <button @click="openEditModal(assessment)" class="btn btn-sm btn-ghost">تعديل</button>
            <button @click="openDeleteModal(assessment)" class="btn btn-sm btn-danger">حذف</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Assessment Modal -->
    <AssessmentModal
      v-if="showModal"
      :open="showModal"
      :assessment="editingAssessment"
      :program-id="programId"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Delete Confirmation -->
    <DeleteConfirmModal
      v-if="showDeleteConfirm"
      :open="showDeleteConfirm"
      :item-name="deletingAssessment?.scale?.name_ar || 'التقييم'"
      @confirm="handleDelete"
      @close="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { programService } from '@/services/programService'
import AssessmentModal from './AssessmentModal.vue'
import DeleteConfirmModal from './DeleteConfirmModal.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

interface Assessment {
  id: string
  program_id: string
  scale_id: string
  assessment_type: 'pre' | 'post'
  is_mandatory: boolean
  scale?: {
    name_ar: string
  }
}

interface Props {
  programId: string
}

const props = defineProps<Props>()

const assessments = ref<Assessment[]>([])
const loading = ref(false)
const showModal = ref(false)
const showDeleteConfirm = ref(false)
const editingAssessment = ref<Assessment | null>(null)
const deletingAssessment = ref<Assessment | null>(null)

const fetchAssessments = async () => {
  loading.value = true
  try {
    const response = await programService.getAssessments(props.programId)
    if (response.data.success) {
      assessments.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching assessments:', error)
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  editingAssessment.value = null
  showModal.value = true
}

const openEditModal = (assessment: Assessment) => {
  editingAssessment.value = { ...assessment }
  showModal.value = true
}

const openDeleteModal = (assessment: Assessment) => {
  deletingAssessment.value = assessment
  showDeleteConfirm.value = true
}

const closeModal = () => {
  showModal.value = false
  editingAssessment.value = null
}

const handleSave = async () => {
  await fetchAssessments()
  closeModal()
}

const handleDelete = async () => {
  if (!deletingAssessment.value) return
  
  try {
    await programService.deleteAssessment(props.programId, deletingAssessment.value.id)
    await fetchAssessments()
    showDeleteConfirm.value = false
    deletingAssessment.value = null
  } catch (error) {
    console.error('Error deleting assessment:', error)
  }
}

onMounted(() => {
  fetchAssessments()
})

defineExpose({
  fetchAssessments
})
</script>




