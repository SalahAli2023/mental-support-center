<template>
  <div class="space-y-4">
    <Card>
      <div class="flex flex-col gap-4">
        <header class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
          <div>
            <h2 class="text-xl font-semibold text-primary">رسائل المستخدمين</h2>
            <p class="text-sm text-secondary">
              تابع استفسارات، شكاوى، وتقييمات المستخدمين القادمة من نموذج
              التواصل.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <Button variant="outline" @click="handleRefresh" :disabled="loading">
              <i class="fas fa-sync-alt mr-2"></i>
              تحديث
            </Button>
          </div>
        </header>

        <!-- الفلاتر -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <div>
            <input
              v-model="search"
              type="text"
              class="input w-full"
              placeholder="ابحث بالاسم أو البريد أو الموضوع..."
            />
          </div>
          <div>
            <select
              v-model="typeFilter"
              class="input w-full"
            >
              <option value="all">كل الأنواع</option>
              <option
                v-for="option in messageTypeOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>
          </div>
          <div>
            <select
              v-model="statusFilter"
              class="input w-full"
            >
              <option value="all">جميع الحالات</option>
              <option value="new">جديدة</option>
              <option value="in_progress">قيد المتابعة</option>
              <option value="resolved">مغلقة</option>
            </select>
          </div>
          <div>
            <select
              v-model="readFilter"
              class="input w-full"
            >
              <option value="all">جميع الرسائل</option>
              <option value="read">تمت قراءتها</option>
              <option value="unread">غير مقروءة</option>
            </select>
          </div>
        </div>

        <!-- Mobile Cards View -->
        <div class="sm:hidden space-y-3">
          <div v-for="message in messages" :key="message.id" 
               class="card-secondary p-4 space-y-3">
            <!-- Header with status badge -->
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <div class="w-8 h-8 rounded-full bg-brand-500/10 flex items-center justify-center">
                    <i class="fas fa-envelope text-brand-500 text-sm"></i>
                  </div>
                  <div>
                    <div class="font-semibold text-primary text-sm">{{ message.name }}</div>
                    <div class="text-xs text-secondary">{{ message.email }}</div>
                  </div>
                </div>
                
                <!-- Type and date -->
                <div class="flex items-center justify-between mb-2">
                  <span class="badge text-xs" :class="typeBadgeClass(message.message_type)">
                    {{ formatType(message.message_type) }}
                  </span>
                  <span class="text-xs text-secondary">{{ formatDate(message.created_at) }}</span>
                </div>
                
                <!-- Subject -->
                <div class="text-primary text-sm mb-2">
                  {{ message.subject || '—' }}
                </div>
              </div>
            </div>

            <!-- Status indicators -->
            <div class="flex flex-wrap gap-2">
              <span class="text-xs px-2 py-1 rounded" :class="message.is_read ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'">
                {{ message.is_read ? 'تمت قراءتها' : 'غير مقروءة' }}
              </span>
              <span class="text-xs px-2 py-1 rounded" :class="message.response ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700'">
                {{ message.response ? 'تم الرد' : 'بانتظار الرد' }}
              </span>
            </div>

            <!-- Status select -->
            <div>
              <label class="block text-xs text-secondary mb-1">حالة المتابعة</label>
              <select
                v-model="localStatus[message.id]"
                class="input w-full text-xs py-2"
                @change="updateStatus(message.id)"
              >
                <option value="new">جديدة</option>
                <option value="in_progress">قيد المتابعة</option>
                <option value="resolved">مغلقة</option>
              </select>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-2 pt-2 border-t border-primary">
              <Button size="sm" variant="ghost" @click="openDetails(message)" class="flex-1">
                <i class="fas fa-eye mr-1"></i>
                عرض
              </Button>
              <Button size="sm" variant="outline" @click="openResponseModal(message)" class="flex-1">
                <i class="fas fa-reply mr-1"></i>
                {{ message.response ? 'تعديل' : 'رد' }}
              </Button>
              <Button size="sm" variant="ghost" @click="toggleRead(message)" class="flex-1">
                <i class="fas" :class="message.is_read ? 'fa-envelope' : 'fa-envelope-open'"></i>
              </Button>
            </div>
          </div>

          <!-- Empty state for mobile -->
          <div v-if="!messages.length && !loading" class="card-secondary p-6 text-center">
            <div class="flex flex-col items-center justify-center gap-3">
              <div class="w-12 h-12 rounded-full bg-tertiary flex items-center justify-center">
                <i class="fas fa-envelope text-xl text-tertiary"></i>
              </div>
              <div>
                <p class="text-sm font-medium text-primary">لا توجد رسائل مطابقة للبحث</p>
                <p class="text-xs text-secondary mt-1">جرب تغيير معايير البحث</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-x-auto rounded-xl border border-primary bg-primary">
          <table class="min-w-full divide-y divide-primary text-sm">
            <thead class="bg-secondary text-primary font-semibold">
              <tr>
                <th class="px-4 py-3 text-right">المرسل</th>
                <th class="px-4 py-3 text-right">نوع الرسالة</th>
                <th class="px-4 py-3 text-right">الموضوع والحالة</th>
                <th class="px-4 py-3 text-right">الرد</th>
                <th class="px-4 py-3 text-right">تاريخ الإرسال</th>
                <th class="px-4 py-3 text-right">الإجراءات</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-primary">
              <tr v-if="!messages.length && !loading">
                <td colspan="6" class="px-4 py-8 text-center text-secondary">
                  <div class="flex flex-col items-center justify-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-tertiary flex items-center justify-center">
                      <i class="fas fa-envelope text-xl text-tertiary"></i>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-primary">لا توجد رسائل مطابقة للبحث</p>
                      <p class="text-xs text-secondary mt-1">جرب تغيير معايير البحث</p>
                    </div>
                  </div>
                </td>
              </tr>
              <tr v-for="message in messages" :key="message.id">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-500/10 flex items-center justify-center">
                      <i class="fas fa-user text-brand-500 text-sm"></i>
                    </div>
                    <div>
                      <div class="font-semibold text-primary">
                        {{ message.name }}
                      </div>
                      <div class="text-xs text-secondary">{{ message.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <span class="badge" :class="typeBadgeClass(message.message_type)">
                    {{ formatType(message.message_type) }}
                  </span>
                </td>
                <td class="px-4 py-3 text-primary">
                  <div class="font-medium mb-2">{{ message.subject || '—' }}</div>
                  <div class="flex flex-col gap-1">
                    <select
                      v-model="localStatus[message.id]"
                      class="input text-xs py-1 w-32"
                      @change="updateStatus(message.id)"
                    >
                      <option value="new">جديدة</option>
                      <option value="in_progress">قيد المتابعة</option>
                      <option value="resolved">مغلقة</option>
                    </select>
                    <span class="text-xs" :class="message.is_read ? 'text-green-600' : 'text-yellow-600'">
                      <i class="fas" :class="message.is_read ? 'fa-check-circle' : 'fa-clock'"></i>
                      {{ message.is_read ? 'تمت قراءتها' : 'غير مقروءة' }}
                    </span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex flex-col gap-1">
                    <span class="badge" :class="message.response ? 'badge-success' : 'badge-neutral'">
                      <i class="fas" :class="message.response ? 'fa-check' : 'fa-hourglass-half'"></i>
                      {{ message.response ? 'تم الرد' : 'بانتظار الرد' }}
                    </span>
                    <span
                      v-if="message.message_type === 'inquiry'"
                      class="badge text-xs" :class="message.is_public ? 'badge-info' : 'badge-neutral'"
                    >
                      <i class="fas" :class="message.is_public ? 'fa-eye' : 'fa-eye-slash'"></i>
                      {{ message.is_public ? 'ظاهر' : 'مخفي' }}
                    </span>
                  </div>
                </td>
                <td class="px-4 py-3 text-primary">
                  <div class="flex flex-col">
                    <span class="text-sm">{{ formatDate(message.created_at) }}</span>
                    <span class="text-xs text-secondary">{{ formatTime(message.created_at) }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-2">
                    <Button size="sm" variant="ghost" @click="openDetails(message)" title="عرض التفاصيل">
                      <i class="fas fa-eye"></i>
                    </Button>
                    <Button
                      size="sm"
                      variant="outline"
                      @click="openResponseModal(message)"
                      title="الرد على الرسالة"
                    >
                      <i class="fas fa-reply"></i>
                    </Button>
                    <Button
                      v-if="message.message_type === 'inquiry'"
                      size="sm"
                      variant="outline"
                      :disabled="!message.response"
                      @click="togglePublic(message)"
                      :title="message.is_public ? 'إخفاء من الموقع' : 'نشر بالموقع'"
                    >
                      <i class="fas" :class="message.is_public ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </Button>
                    <Button
                      size="sm"
                      variant="ghost"
                      @click="toggleRead(message)"
                      :title="message.is_read ? 'تعليم كغير مقروءة' : 'تعليم كمقروءة'"
                    >
                      <i class="fas" :class="message.is_read ? 'fa-envelope' : 'fa-envelope-open'"></i>
                    </Button>
                    <Button
                      size="sm"
                      variant="outline"
                      class="text-accent-500 border-accent-500/30 hover:bg-accent-500/10"
                      @click="deleteMessage(message.id)"
                      title="حذف الرسالة"
                    >
                      <i class="fas fa-trash"></i>
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          class="flex flex-wrap items-center justify-between gap-3 text-sm text-secondary"
          v-if="meta.total > meta.per_page"
        >
          <div>
            عرض {{ meta.from }}-{{ meta.to }} من {{ meta.total }} رسالة
          </div>
          <div class="flex items-center gap-2">
            <button
              class="btn-outline btn-ghost px-3 py-1 text-xs"
              :disabled="meta.current_page === 1 || loading"
              @click="changePage(meta.current_page - 1)"
            >
              <i class="fas fa-chevron-right"></i>
              السابق
            </button>
            <span class="text-primary">
              الصفحة {{ meta.current_page }} من {{ meta.last_page }}
            </span>
            <button
              class="btn-outline btn-ghost px-3 py-1 text-xs"
              :disabled="meta.current_page >= meta.last_page || loading"
              @click="changePage(meta.current_page + 1)"
            >
              التالي
              <i class="fas fa-chevron-left"></i>
            </button>
          </div>
        </div>
      </div>
    </Card>

    <!-- Message Details Modal -->
    <div
      v-if="selectedMessage"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
    >
      <div class="w-full max-w-2xl rounded-2xl bg-primary p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-xl font-semibold text-primary">
              تفاصيل الرسالة
            </h3>
            <p class="text-sm text-secondary">
              <span class="badge mr-2" :class="typeBadgeClass(selectedMessage.message_type)">
                {{ formatType(selectedMessage.message_type) }}
              </span>
              • {{ formatDate(selectedMessage.created_at) }}
            </p>
          </div>
          <button
            class="text-secondary hover:text-primary p-2 rounded-lg hover:bg-secondary"
            @click="selectedMessage = null"
          >
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>

        <div class="space-y-6">
          <!-- Sender Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-secondary rounded-lg p-4">
              <div class="text-sm text-secondary mb-2">المرسل</div>
              <div class="font-semibold text-primary">{{ selectedMessage.name }}</div>
              <div class="text-sm text-primary mt-1">{{ selectedMessage.email }}</div>
            </div>
            <div class="bg-secondary rounded-lg p-4">
              <div class="text-sm text-secondary mb-2">الحالة</div>
              <div class="flex items-center gap-2">
                <span class="text-sm" :class="selectedMessage.is_read ? 'text-green-600' : 'text-yellow-600'">
                  <i class="fas" :class="selectedMessage.is_read ? 'fa-check-circle' : 'fa-clock'"></i>
                  {{ selectedMessage.is_read ? 'تمت قراءتها' : 'غير مقروءة' }}
                </span>
                <span class="text-sm" :class="selectedMessage.response ? 'text-blue-600' : 'text-gray-600'">
                  <i class="fas" :class="selectedMessage.response ? 'fa-check' : 'fa-hourglass-half'"></i>
                  {{ selectedMessage.response ? 'تم الرد' : 'بانتظار الرد' }}
                </span>
              </div>
              <div class="mt-2">
                <select
                  v-model="localStatus[selectedMessage.id]"
                  class="input text-xs py-1 w-full"
                  @change="updateStatus(selectedMessage.id)"
                >
                  <option value="new">جديدة</option>
                  <option value="in_progress">قيد المتابعة</option>
                  <option value="resolved">مغلقة</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Message Content -->
          <div>
            <div class="text-sm text-secondary mb-2">الموضوع</div>
            <div class="font-medium text-primary bg-secondary rounded-lg p-4">
              {{ selectedMessage.subject || '—' }}
            </div>
          </div>

          <div>
            <div class="text-sm text-secondary mb-2">محتوى الرسالة</div>
            <div class="whitespace-pre-line leading-relaxed bg-secondary rounded-lg p-4 text-primary">
              {{ selectedMessage.message }}
            </div>
          </div>

          <!-- Response if exists -->
          <div v-if="selectedMessage.response">
            <div class="text-sm text-secondary mb-2">الرد</div>
            <div class="whitespace-pre-line leading-relaxed bg-tertiary rounded-lg p-4 text-primary border border-primary">
              {{ selectedMessage.response }}
              <div class="mt-4 pt-3 border-t border-primary text-xs text-secondary">
                <span v-if="selectedMessage.is_public">
                  <i class="fas fa-eye mr-1"></i>
                  ظاهر في الموقع
                </span>
                <span v-else>
                  <i class="fas fa-eye-slash mr-1"></i>
                  غير ظاهر في الموقع
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
          <Button variant="ghost" @click="selectedMessage = null" class="w-full sm:w-auto">
            <i class="fas fa-times mr-2"></i>
            إغلاق
          </Button>
          <Button @click="openResponseModal(selectedMessage)" class="w-full sm:w-auto">
            <i class="fas fa-reply mr-2"></i>
            {{ selectedMessage.response ? 'تعديل الرد' : 'الرد على الرسالة' }}
          </Button>
          <Button variant="ghost" @click="toggleRead(selectedMessage)" class="w-full sm:w-auto">
            <i class="fas mr-2" :class="selectedMessage.is_read ? 'fa-envelope' : 'fa-envelope-open'"></i>
            {{ selectedMessage.is_read ? 'تعليم كغير مقروءة' : 'تعليم كمقروءة' }}
          </Button>
        </div>
      </div>
    </div>

    <!-- Response Modal -->
    <div
      v-if="responseModal.visible"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
    >
      <div class="w-full max-w-xl rounded-2xl bg-primary p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-xl font-semibold text-primary">
              الرد على الرسالة
            </h3>
            <p class="text-sm text-secondary">
              {{ responseModal.message?.name }} • {{ formatType(responseModal.message?.message_type || '') }}
            </p>
          </div>
          <button
            class="text-secondary hover:text-primary p-2 rounded-lg hover:bg-secondary"
            @click="closeResponseModal"
          >
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>

        <div class="space-y-6">
          <!-- Message Preview -->
          <div class="bg-secondary rounded-lg p-4">
            <div class="text-sm text-secondary mb-2">رسالة المستخدم</div>
            <div class="text-primary text-sm line-clamp-3">{{ responseModal.message?.message }}</div>
          </div>

          <!-- Response Form -->
          <div>
            <label class="block text-sm text-secondary mb-3">الرد</label>
            <textarea
              v-model="responseForm.response"
              rows="6"
              class="input w-full"
              placeholder="أدخل الرد على الرسالة..."
            ></textarea>
          </div>

          <!-- Public Toggle for Inquiries -->
          <div v-if="responseModal.message?.message_type === 'inquiry'" class="bg-secondary rounded-lg p-4">
            <label class="inline-flex items-center gap-3 text-sm text-primary cursor-pointer">
              <input
                type="checkbox"
                class="rounded border-primary text-brand-500 w-5 h-5"
                v-model="responseForm.is_public"
                :disabled="!responseForm.response?.trim().length"
              />
              <div>
                <div class="font-medium">نشر هذا الاستفسار في قسم الأسئلة الشائعة</div>
                <div class="text-xs text-secondary mt-1">
                  لا يمكن نشر السؤال في الموقع بدون رد.
                </div>
              </div>
            </label>
          </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
          <Button variant="ghost" @click="closeResponseModal" class="w-full sm:w-auto">
            <i class="fas fa-times mr-2"></i>
            إلغاء
          </Button>
          <Button :disabled="responseLoading" @click="saveResponse" class="w-full sm:w-auto">
            <span v-if="responseLoading" class="inline-flex items-center gap-2">
              <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
              جاري الحفظ...
            </span>
            <span v-else class="inline-flex items-center gap-2">
              <i class="fas fa-save"></i>
              حفظ الرد
            </span>
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue'
import Card from '@/components/dashboard/component/ui/Card.vue'
import Button from '@/components/dashboard/component/ui/Button.vue'
import { useUserMessageStore } from '@/stores/userMessages'
import { useToast } from '@/composables/useToast'
import { useI18n } from 'vue-i18n'

const messageStore = useUserMessageStore()
const toast = useToast()
const { t } = useI18n()

const search = ref('')
const typeFilter = ref('all')
const statusFilter = ref('all')
const readFilter = ref('all')
const selectedMessage = ref(null)
const responseModal = reactive({
  visible: false,
  message: null as any
})
const responseForm = reactive({
  response: '',
  is_public: false
})
const responseLoading = ref(false)
const localStatus = ref<Record<number, string>>({})
const loading = computed(() => messageStore.loading)
const messages = computed(() => messageStore.messages)
const meta = computed(() => messageStore.meta)

const messageTypeOptions = computed(() => [
  { value: 'complaint', label: t('contact.form.types.complaint') },
  { value: 'inquiry', label: t('contact.form.types.inquiry') },
  { value: 'review', label: t('contact.form.types.review') }
])

const fetchMessages = async (page = 1) => {
  const params: Record<string, any> = {
    page,
    per_page: messageStore.meta.per_page || 10
  }
  if (search.value) params.search = search.value
  if (typeFilter.value !== 'all') params.message_type = typeFilter.value
  if (statusFilter.value !== 'all') params.status = statusFilter.value
  if (readFilter.value !== 'all') params.is_read = readFilter.value === 'read'

  await messageStore.fetchMessages(params)

  // sync status select values
  const statusMap: Record<number, string> = {}
  messageStore.messages.forEach((msg) => {
    statusMap[msg.id] = msg.status
  })
  localStatus.value = statusMap
}

const debouncedFetch = (() => {
  let timeout: ReturnType<typeof setTimeout> | null = null
  return () => {
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(() => fetchMessages(), 400)
  }
})()

watch([search, typeFilter, statusFilter, readFilter], () => {
  debouncedFetch()
})

const changePage = (page: number) => {
  fetchMessages(page)
}

const handleRefresh = () => {
  fetchMessages(meta.value.current_page || 1)
}

const updateStatus = async (id: number) => {
  try {
    await messageStore.updateMessage(id, { status: localStatus.value[id] })
    toast.success('تم تحديث حالة الرسالة بنجاح')
  } catch (error) {
    toast.error('حدث خطأ أثناء تحديث الحالة')
  }
}

const toggleRead = async (message: any) => {
  try {
    await messageStore.updateMessage(message.id, { is_read: !message.is_read })
    toast.success(message.is_read ? 'تم التعليم كغير مقروءة' : 'تم التعليم كمقروءة')
    fetchMessages(meta.value.current_page || 1)
  } catch (error) {
    toast.error('حدث خطأ أثناء تحديث حالة القراءة')
  }
}

const deleteMessage = async (id: number) => {
  if (!confirm('هل أنت متأكد من حذف هذه الرسالة؟')) {
    return
  }
  try {
    await messageStore.deleteMessage(id)
    toast.success('تم حذف الرسالة بنجاح')
    fetchMessages(meta.value.current_page || 1)
  } catch (error) {
    toast.error('تعذر حذف الرسالة')
  }
}

const formatType = (type: string) => {
  const map: Record<string, string> = {
    complaint: t('contact.form.types.complaint'),
    inquiry: t('contact.form.types.inquiry'),
    review: t('contact.form.types.review')
  }
  return map[type] || type
}

const typeBadgeClass = (type: string) => {
  switch (type) {
    case 'complaint':
      return 'badge-accent'
    case 'review':
      return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'
    default:
      return 'badge-success'
  }
}

const formatDate = (value?: string) => {
  if (!value) return '—'
  return new Date(value).toLocaleDateString('ar-EG', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatTime = (value?: string) => {
  if (!value) return ''
  return new Date(value).toLocaleTimeString('ar-EG', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const openDetails = (message: any) => {
  selectedMessage.value = message
  if (!message.is_read) {
    toggleRead(message)
  }
}

const openResponseModal = (message: any) => {
  responseModal.visible = true
  responseModal.message = message
  responseForm.response = message.response || ''
  responseForm.is_public = Boolean(message.is_public)
}

const closeResponseModal = () => {
  responseModal.visible = false
  responseModal.message = null
  responseForm.response = ''
  responseForm.is_public = false
}

const saveResponse = async () => {
  if (!responseModal.message) return
  responseLoading.value = true
  try {
    await messageStore.updateMessage(responseModal.message.id, {
      response: responseForm.response,
      is_public:
        responseModal.message.message_type === 'inquiry'
          ? responseForm.is_public
          : false
    })
    toast.success('تم حفظ الرد بنجاح')
    closeResponseModal()
    fetchMessages(meta.value.current_page || 1)
  } catch (error: any) {
    toast.handleApiError(error, 'تعذر حفظ الرد')
  } finally {
    responseLoading.value = false
  }
}

const togglePublic = async (message: any) => {
  if (!message.response) {
    toast.error('لا يمكن نشر سؤال بدون رد')
    return
  }
  try {
    await messageStore.updateMessage(message.id, { is_public: !message.is_public })
    toast.success(message.is_public ? 'تم إخفاء الرسالة من الموقع' : 'تم نشر السؤال في الموقع')
    fetchMessages(meta.value.current_page || 1)
  } catch (error: any) {
    toast.handleApiError(error, 'تعذر تحديث حالة النشر')
  }
}

onMounted(() => {
  fetchMessages()
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* تحسينات للجوال */
@media (max-width: 640px) {
  .input {
    padding: 0.75rem;
    font-size: 16px;
  }
  
  button {
    min-height: 44px;
  }
}

/* تحسينات شريط التمرير للمودال */
div[class*="max-h-[90vh]"] {
  scrollbar-width: thin;
  scrollbar-color: var(--text-tertiary) transparent;
}

div[class*="max-h-[90vh]"]::-webkit-scrollbar {
  width: 6px;
}

div[class*="max-h-[90vh]"]::-webkit-scrollbar-track {
  background: transparent;
}

div[class*="max-h-[90vh]"]::-webkit-scrollbar-thumb {
  background: var(--text-tertiary);
  border-radius: 3px;
}

.dark div[class*="max-h-[90vh]"]::-webkit-scrollbar-thumb {
  background: var(--text-tertiary);
}

/* Badge classes */
.badge-success {
  background-color: #10b981;
  color: white;
}

.badge-info {
  background-color: #3b82f6;
  color: white;
}

.badge-accent {
  background-color: var(--accent-500);
  color: white;
}

.badge-neutral {
  background-color: var(--bg-secondary);
  color: var(--text-secondary);
  border: 1px solid var(--border-primary);
}

.dark .badge-neutral {
  background-color: var(--bg-secondary);
  color: var(--text-secondary);
  border-color: var(--border-primary);
}

/* Line clamp for text */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>