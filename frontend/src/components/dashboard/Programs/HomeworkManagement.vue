<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold text-primary">المهام المنزلية</h2>
      <button @click="openCreateModal" class="btn btn-primary flex items-center gap-2">
        <PlusIcon class="w-4 h-4" />
        إضافة مهمة
      </button>
    </div>

    <div v-if="loading" class="text-center py-8">
      <div class="text-secondary">جاري التحميل...</div>
    </div>

    <div v-else-if="homework.length === 0" class="text-center py-8 text-secondary">
      لا توجد مهام منزلية. ابدأ بإضافة مهمة جديدة.
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="item in homework"
        :key="item.id"
        class="card p-4"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold">
              {{ item.homework_order }}
            </div>
            <div>
              <h3 class="font-semibold text-primary">{{ item.title_ar }}</h3>
              <p class="text-sm text-secondary">
                {{ getCompletionTypeLabel(item.completion_type) }}
                {{ item.is_mandatory ? '• إلزامي' : '• اختياري' }}
              </p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="badge" :class="item.is_active ? 'badge-success' : 'badge-secondary'">
              {{ item.is_active ? 'نشط' : 'غير نشط' }}
            </span>
            <button @click="openEditModal(item)" class="btn btn-sm btn-ghost">تعديل</button>
            <button @click="viewSubmissions(item)" class="btn btn-sm btn-primary">عرض التسليمات</button>
            <button @click="openDeleteModal(item)" class="btn btn-sm btn-danger">حذف</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Homework Modal -->
    <HomeworkModal
      v-if="showModal"
      :open="showModal"
      :homework="editingHomework"
      :session-id="sessionId"
      @close="closeModal"
      @save="handleSave"
    />

    <!-- Delete Confirmation -->
    <DeleteConfirmModal
      v-if="showDeleteConfirm"
      :open="showDeleteConfirm"
      :item-name="deletingHomework?.title_ar || ''"
      @confirm="handleDelete"
      @close="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { programService } from '@/services/programService'
import HomeworkModal from './HomeworkModal.vue'
import DeleteConfirmModal from './DeleteConfirmModal.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'

interface Homework {
  id: string
  session_id: string
  title_ar: string
  title_en: string
  completion_type: string
  is_mandatory: boolean
  is_active: boolean
  homework_order: number
}

interface Props {
  sessionId: string
}

const props = defineProps<Props>()

const homework = ref<Homework[]>([])
const loading = ref(false)
const showModal = ref(false)
const showDeleteConfirm = ref(false)
const editingHomework = ref<Homework | null>(null)
const deletingHomework = ref<Homework | null>(null)

const getCompletionTypeLabel = (type: string) => {
  const labels: Record<string, string> = {
    confirmation: 'تأكيد',
    text_input: 'إدخال نص',
    file_upload: 'رفع ملف',
    form: 'نموذج'
  }
  return labels[type] || type
}

const fetchHomework = async () => {
  loading.value = true
  try {
    const response = await programService.getHomework(props.sessionId)
    if (response.data.success) {
      homework.value = response.data.data
    }
  } catch (error) {
    console.error('Error fetching homework:', error)
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  editingHomework.value = null
  showModal.value = true
}

const openEditModal = (item: Homework) => {
  editingHomework.value = { ...item }
  showModal.value = true
}

const viewSubmissions = (item: Homework) => {
  // يمكن إضافة صفحة لعرض التسليمات
  console.log('View submissions for:', item.id)
}

const openDeleteModal = (item: Homework) => {
  deletingHomework.value = item
  showDeleteConfirm.value = true
}

const closeModal = () => {
  showModal.value = false
  editingHomework.value = null
}

const handleSave = async () => {
  await fetchHomework()
  closeModal()
}

const handleDelete = async () => {
  if (!deletingHomework.value) return
  
  try {
    await programService.deleteHomework(props.sessionId, deletingHomework.value.id)
    await fetchHomework()
    showDeleteConfirm.value = false
    deletingHomework.value = null
  } catch (error) {
    console.error('Error deleting homework:', error)
  }
}

onMounted(() => {
  fetchHomework()
})

defineExpose({
  fetchHomework
})
</script>




