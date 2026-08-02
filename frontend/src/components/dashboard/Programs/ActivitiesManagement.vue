<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold text-primary">الأنشطة</h2>
      <button @click="openCreateModal" class="btn btn-primary flex items-center gap-2">
        <PlusIcon class="w-4 h-4" />
        إضافة نشاط
      </button>
    </div>

    <div v-if="loading" class="text-center py-8">
      <div class="text-secondary">جاري التحميل...</div>
    </div>

    <div v-else-if="activities.length === 0" class="text-center py-8 text-secondary">
      لا توجد أنشطة. ابدأ بإضافة نشاط جديد.
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="activity in activities"
        :key="activity.id"
        class="card p-4 cursor-move"
        draggable="true"
        @dragstart="handleDragStart($event, activity)"
        @dragover.prevent
        @drop="handleDrop($event, activity)"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold">
              {{ activity.activity_order }}
            </div>
            <div>
              <h3 class="font-semibold text-primary">{{ activity.name_ar }}</h3>
              <p class="text-sm text-secondary">
                {{ getActivityTypeLabel(activity.activity_type) }}
                {{ activity.is_mandatory ? '• إلزامي' : '• اختياري' }}
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="badge" :class="activity.is_active ? 'badge-success' : 'badge-secondary'">
              {{ activity.is_active ? 'نشط' : 'غير نشط' }}
            </span>
            <button @click="openEditModal(activity)" class="btn btn-sm btn-ghost">تعديل</button>
            <button @click="openDeleteModal(activity)" class="btn btn-sm btn-danger">حذف</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity Modal -->
    <ActivityModal
      v-if="showModal"
      :open="showModal"
      :activity="editingActivity"
      :session-id="sessionId"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Delete Confirmation -->
    <DeleteConfirmModal
      v-if="showDeleteConfirm"
      :open="showDeleteConfirm"
      :item-name="deletingActivity?.name_ar || ''"
      @confirm="handleDelete"
      @close="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { programService } from '@/services/programService'
import ActivityModal from './ActivityModal.vue'
import DeleteConfirmModal from './DeleteConfirmModal.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

interface Activity {
  id: string
  session_id: string
  name_ar: string
  name_en: string
  activity_type: string
  is_mandatory: boolean
  is_active: boolean
  activity_order: number
}

interface Props {
  sessionId: string
}

const props = defineProps<Props>()

const activities = ref<Activity[]>([])
const loading = ref(false)
const showModal = ref(false)
const showDeleteConfirm = ref(false)
const editingActivity = ref<Activity | null>(null)
const deletingActivity = ref<Activity | null>(null)
const draggedActivity = ref<Activity | null>(null)

const getActivityTypeLabel = (type: string) => {
  const labels: Record<string, string> = {
    text: 'نص',
    video: 'فيديو',
    audio: 'صوت',
    form: 'نموذج',
    exercise: 'تمرين',
    reflection_questions: 'أسئلة انعكاسية',
    quiz: 'اختبار'
  }
  return labels[type] || type
}

const fetchActivities = async () => {
  loading.value = true
  try {
    const response = await programService.getAllActivities({ session_id: props.sessionId })
    if (response.data.success) {
      activities.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching activities:', error)
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  editingActivity.value = null
  showModal.value = true
}

const openEditModal = (activity: Activity) => {
  editingActivity.value = { ...activity }
  showModal.value = true
}

const openDeleteModal = (activity: Activity) => {
  deletingActivity.value = activity
  showDeleteConfirm.value = true
}

const closeModal = () => {
  showModal.value = false
  editingActivity.value = null
}

const handleSave = async () => {
  await fetchActivities()
  closeModal()
}

const handleDelete = async () => {
  if (!deletingActivity.value) return
  
  try {
    await programService.deleteActivity(deletingActivity.value.id)
    await fetchActivities()
    showDeleteConfirm.value = false
    deletingActivity.value = null
  } catch (error) {
    console.error('Error deleting activity:', error)
  }
}

const handleDragStart = (event: DragEvent, activity: Activity) => {
  draggedActivity.value = activity
  if (event.dataTransfer) {
    event.dataTransfer.effectAllowed = 'move'
  }
}

const handleDrop = async (event: DragEvent, targetActivity: Activity) => {
  event.preventDefault()
  if (!draggedActivity.value || draggedActivity.value.id === targetActivity.id) return

  const draggedIndex = activities.value.findIndex(a => a.id === draggedActivity.value!.id)
  const targetIndex = activities.value.findIndex(a => a.id === targetActivity.id)

  const [removed] = activities.value.splice(draggedIndex, 1)
  activities.value.splice(targetIndex, 0, removed)

  const reorderedActivities = activities.value.map((activity, index) => ({
    id: activity.id,
    order: index + 1
  }))

  try {
    await programService.reorderActivities(reorderedActivities)
    await fetchActivities()
  } catch (error) {
    console.error('Error reordering activities:', error)
    await fetchActivities()
  }

  draggedActivity.value = null
}

onMounted(() => {
  fetchActivities()
})

defineExpose({
  fetchActivities
})
</script>




