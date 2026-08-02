<template>
  <div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-semibold text-primary">إحصائيات أرقام النجاح</h1>
        <p class="text-sm text-secondary mt-1">قم بإدارة الأرقام الظاهرة في الصفحة الرئيسية.</p>
      </div>
      <button
        class="btn btn-primary w-full sm:w-auto mt-2 sm:mt-0"
        @click="openForm()"
      >
        <i class="fas fa-plus text-sm"></i>
        <span>إضافة إحصائية</span>
      </button>
    </div>

    <!-- Messages -->
    <div v-if="error" class="rounded-xl border border-accent-500/30 bg-accent-500/10 dark:bg-accent-500/20 px-4 py-3 text-accent-500 dark:text-accent-500/90 text-sm">
      <div class="flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ error }}</span>
      </div>
    </div>
    <div v-if="success" class="rounded-xl border border-brand-500/30 bg-brand-500/10 dark:bg-brand-500/20 px-4 py-3 text-brand-500 dark:text-brand-500/90 text-sm">
      <div class="flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span>{{ success }}</span>
      </div>
    </div>

    <!-- Table Section -->
    <div class="card">
      <div class="overflow-x-auto -mx-1 sm:mx-0">
        <div class="min-w-full">
          <!-- Mobile Cards View -->
          <div class="sm:hidden space-y-3">
            <div v-for="stat in stats" :key="stat.id" 
                 class="card-secondary p-4 space-y-3">
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-500/10 dark:bg-brand-500/20 flex items-center justify-center border border-brand-500/20">
                      <i class="fas fa-chart-line text-brand-500 text-sm"></i>
                    </div>
                    <div>
                      <div class="font-semibold text-primary text-sm">{{ stat.label_ar }}</div>
                      <div class="text-xs text-secondary">{{ stat.key }}</div>
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                      <div class="text-secondary mb-1">القيمة</div>
                      <div class="font-semibold text-primary text-lg">{{ stat.value }}</div>
                    </div>
                    <div>
                      <div class="text-secondary mb-1">الترتيب</div>
                      <div class="font-semibold text-primary">{{ stat.display_order }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex gap-2 pt-2 border-t border-primary">
                <button
                  class="btn-outline btn-ghost flex-1 text-xs py-2"
                  @click="openForm(stat)"
                >
                  <i class="fas fa-edit ml-1"></i>
                  تعديل
                </button>
                <button
                  class="border border-accent-500/30 text-accent-500 hover:bg-accent-500/10 rounded-lg px-3 py-2 text-xs flex-1 transition-all duration-200"
                  @click="confirmDelete(stat)"
                >
                  <i class="fas fa-trash ml-1"></i>
                  حذف
                </button>
              </div>
            </div>
            
            <!-- Empty State for Mobile -->
            <div v-if="!stats.length && !loading" class="card-secondary p-6 text-center">
              <div class="flex flex-col items-center justify-center gap-3">
                <div class="w-12 h-12 rounded-full bg-tertiary flex items-center justify-center">
                  <i class="fas fa-chart-bar text-xl text-tertiary"></i>
                </div>
                <div>
                  <p class="text-sm font-medium text-primary">لا توجد بيانات حتى الآن</p>
                  <p class="text-xs text-secondary mt-1">قم بإضافة أول إحصائية</p>
                </div>
                <button
                  class="btn btn-primary mt-2"
                  @click="openForm()"
                >
                  <i class="fas fa-plus text-xs"></i>
                  إضافة إحصائية
                </button>
              </div>
            </div>
          </div>

          <!-- Desktop Table -->
          <table class="min-w-full text-sm hidden sm:table">
            <thead class="bg-secondary text-primary">
              <tr>
                <th class="px-4 py-3 text-start font-medium text-xs sm:text-sm">المفتاح</th>
                <th class="px-4 py-3 text-start font-medium text-xs sm:text-sm">الاسم (عربي)</th>
                <th class="px-4 py-3 text-start font-medium text-xs sm:text-sm">الاسم (إنجليزي)</th>
                <th class="px-4 py-3 text-start font-medium text-xs sm:text-sm">القيمة</th>
                <th class="px-4 py-3 text-start font-medium text-xs sm:text-sm">الترتيب</th>
                <th class="px-4 py-3 text-start font-medium text-xs sm:text-sm">الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="stat in stats" :key="stat.id" 
                  class="border-b border-primary hover:bg-secondary transition-all duration-200">
                <td class="px-4 py-3 font-semibold text-primary text-xs sm:text-sm">
                  <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-md bg-brand-500/10 flex items-center justify-center border border-brand-500/20">
                      <i class="fas fa-hashtag text-brand-500 text-xs"></i>
                    </div>
                    {{ stat.key }}
                  </div>
                </td>
                <td class="px-4 py-3 text-primary text-xs sm:text-sm">{{ stat.label_ar }}</td>
                <td class="px-4 py-3 text-primary text-xs sm:text-sm">{{ stat.label_en }}</td>
                <td class="px-4 py-3 text-primary text-xs sm:text-sm">
                  <span class="font-bold text-brand-500">{{ stat.value }}</span>
                </td>
                <td class="px-4 py-3 text-primary text-xs sm:text-sm">{{ stat.display_order }}</td>
                <td class="px-4 py-3 space-x-2 space-x-reverse">
                  <button
                    class="btn-outline btn-ghost text-xs py-1.5 px-3"
                    @click="openForm(stat)"
                  >
                    <i class="fas fa-edit ml-1"></i>
                    تعديل
                  </button>
                  <button
                    class="border border-accent-500/30 text-accent-500 hover:bg-accent-500/10 rounded-lg px-3 py-1.5 text-xs transition-all duration-200"
                    @click="confirmDelete(stat)"
                  >
                    <i class="fas fa-trash ml-1"></i>
                    حذف
                  </button>
                </td>
              </tr>
              <tr v-if="!stats.length && !loading">
                <td colspan="6" class="px-4 py-8 text-center text-secondary">
                  <div class="flex flex-col items-center justify-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-tertiary flex items-center justify-center">
                      <i class="fas fa-chart-bar text-xl text-tertiary"></i>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-primary">لا توجد بيانات حتى الآن</p>
                      <p class="text-xs text-secondary mt-1">قم بإضافة أول إحصائية</p>
                    </div>
                    <button
                      class="btn btn-primary mt-2"
                      @click="openForm()"
                    >
                      <i class="fas fa-plus text-xs"></i>
                      إضافة إحصائية
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-if="loading" class="py-8 text-center text-secondary">
        <div class="flex flex-col items-center justify-center gap-3">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-500"></div>
          <p class="text-sm">جاري تحميل البيانات...</p>
        </div>
      </div>
    </div>

    <!-- Form Modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-4 sm:py-0">
      <div class="w-full max-w-2xl rounded-2xl bg-primary shadow-2xl border border-primary max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-primary px-4 sm:px-5 py-3 sticky top-0 bg-primary rounded-t-2xl">
          <h3 class="text-base sm:text-lg font-semibold text-primary">
            {{ editingStat ? 'تعديل الإحصائية' : 'إضافة إحصائية' }}
          </h3>
          <button class="text-secondary hover:text-primary p-1" @click="closeForm">
            <i class="fas fa-times text-lg"></i>
          </button>
        </div>

        <form class="space-y-4 px-4 sm:px-5 py-4 sm:py-6" @submit.prevent="submitForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col">
              <label class="text-sm font-medium text-primary mb-2">
                المفتاح
              </label>
              <input 
                v-model="form.key" 
                type="text" 
                class="input"
                :class="{'opacity-60 cursor-not-allowed': !!editingStat}"
                required 
                :disabled="!!editingStat" 
                placeholder="أدخل مفتاح الإحصائية"
              />
            </div>
            <div class="flex flex-col">
              <label class="text-sm font-medium text-primary mb-2">
                الترتيب في الواجهة
              </label>
              <input 
                v-model.number="form.display_order" 
                type="number" 
                min="0" 
                class="input" 
                required 
                placeholder="أدخل رقم الترتيب"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col">
              <label class="text-sm font-medium text-primary mb-2">
                الاسم بالعربية
              </label>
              <input 
                v-model="form.label_ar" 
                type="text" 
                class="input" 
                required 
                placeholder="أدخل الاسم بالعربية"
              />
            </div>
            <div class="flex flex-col">
              <label class="text-sm font-medium text-primary mb-2">
                الاسم بالإنجليزية
              </label>
              <input 
                v-model="form.label_en" 
                type="text" 
                class="input" 
                required 
                placeholder="أدخل الاسم بالإنجليزية"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col">
              <label class="text-sm font-medium text-primary mb-2">
                القيمة الرقمية
              </label>
              <input 
                v-model.number="form.value" 
                type="number" 
                min="0" 
                class="input" 
                required 
                placeholder="أدخل القيمة"
              />
            </div>
            <div class="flex flex-col">
              <label class="text-sm font-medium text-primary mb-2">
                مسار الأيقونة (اختياري)
              </label>
              <input 
                v-model="form.icon" 
                type="text" 
                class="input" 
                placeholder="storage/path.png أو اتركه فارغاً"
              />
            </div>
          </div>

          <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-primary">
            <button 
              type="button" 
              class="btn-outline btn-ghost w-full sm:w-auto"
              @click="closeForm"
            >
              إلغاء
            </button>
            <button 
              type="submit" 
              class="btn btn-primary w-full sm:w-auto shadow-soft"
            >
              {{ editingStat ? 'تحديث' : 'حفظ' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'
import { useToast } from '@/composables/useToast'

const stats = ref([])
const loading = ref(false)
const error = ref(null)
const success = ref('')
const showForm = ref(false)
const editingStat = ref(null)
const form = ref({
  key: '',
  label_ar: '',
  label_en: '',
  value: 0,
  icon: '',
  display_order: 0
})

const toast = useToast()

const fetchStats = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await api.get('/site-statistics')
    stats.value = response.data?.data || response.data || []
  } catch (err) {
    console.error('Failed to fetch site statistics:', err)
    error.value = err.response?.data?.message || 'فشل في تحميل البيانات.'
  } finally {
    loading.value = false
  }
}

const openForm = (stat = null) => {
  editingStat.value = stat
  if (stat) {
    form.value = { ...stat }
  } else {
    form.value = {
      key: '',
      label_ar: '',
      label_en: '',
      value: 0,
      icon: '',
      display_order: (stats.value.length || 0) + 1
    }
  }
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  editingStat.value = null
}

const submitForm = async () => {
  try {
    if (editingStat.value) {
      await api.put(`/site-statistics/${editingStat.value.id}`, form.value)
      toast.success('تم تحديث الإحصائية بنجاح')
      success.value = 'تم تحديث الإحصائية بنجاح'
    } else {
      await api.post('/site-statistics', form.value)
      toast.success('تم إضافة الإحصائية بنجاح')
      success.value = 'تم إضافة الإحصائية بنجاح'
    }
    closeForm()
    fetchStats()
  } catch (err) {
    console.error('Failed to save statistic:', err)
    error.value = err.response?.data?.message || 'حدث خطأ أثناء الحفظ.'
    toast.error(error.value)
  }
}

const confirmDelete = async (stat) => {
  if (!confirm(`هل أنت متأكد من حذف "${stat.label_ar}"؟`)) return

  try {
    await api.delete(`/site-statistics/${stat.id}`)
    toast.success('تم حذف الإحصائية بنجاح')
    success.value = 'تم حذف الإحصائية بنجاح'
    fetchStats()
  } catch (err) {
    console.error('Failed to delete statistic:', err)
    error.value = err.response?.data?.message || 'حدث خطأ أثناء الحذف.'
    toast.error(error.value)
  }
}

onMounted(fetchStats)
</script>

<style scoped>
/* تحسينات للجوال */
@media (max-width: 640px) {
  .input {
    padding: 0.875rem;
    font-size: 16px; /* منع التكبير التلقائي في iOS */
  }
  
  button {
    min-height: 44px; /* تحسين قابلية النقر على الجوال */
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
</style>