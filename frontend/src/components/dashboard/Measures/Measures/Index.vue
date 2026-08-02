<template>
  <div class="space-y-4 p-2 sm:p-4">
    <!-- العنوان والأزرار -->
    <ScaleHeader @create="openCreate" />
    
    <!-- أدوات البحث والتصفية -->
    <SearchFilters
      :search-query="searchQuery"
      :category-filter="categoryFilter"
      :status-filter="statusFilter"
      @update:searchQuery="searchQuery = $event"
      @update:categoryFilter="categoryFilter = $event"
      @update:statusFilter="statusFilter = $event"
      @clear="clearFilters"
    />

    <!-- حالة التحميل -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-500 mx-auto"></div>
      <p class="text-secondary mt-2">جاري تحميل البيانات...</p>
    </div>

    <!-- رسالة الخطأ -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <div class="flex items-center gap-2 text-red-700">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        <span>{{ error }}</span>
      </div>
      <button @click="fetchScales" class="mt-2 text-sm text-red-600 hover:text-red-800">
        إعادة المحاولة
      </button>
    </div>

    <!-- جدول المقاييس -->
    <ScalesTable
      v-else
      :scales="paginatedScales"
      :filtered-scales="filteredScales"
      :current-page="currentPage"
      :total-pages="totalPages"
      :start-index="startIndex"
      :end-index="endIndex"
      :items-per-page="itemsPerPage"
      :visible-pages="visiblePages"
      @edit="edit"
      @preview="openPreview"
      @delete="deleteScale"
      @page-change="goToPage"
      @prev-page="prevPage"
      @next-page="nextPage"
      @update:itemsPerPage="itemsPerPage = $event"
      @clear="clearFilters"
    />

    <!-- Modal إنشاء/تعديل المقياس -->
    <ScaleModal
      :show="modal"
      :current="current"
      :current-step="currentStep"
      :form="form"
      :submitted="submitted"
      @close="close"
      @prev-step="prevStep"
      @next-step="nextStep"
      @save="save"
      @add-question="addQuestion"
      @remove-question="removeQuestion"
      @add-option="addOption"
      @remove-option="removeOption"
      @add-interpretation="addInterpretation"
      @remove-interpretation="removeInterpretation"
      @image-upload="handleImageUpload"
      @image-remove="removeImage"
    />

    <!-- Modal معاينة المقياس -->
    <PreviewModal
      :show="previewModal"
      :preview-data="previewData"
      @close="closePreview"
    />

    <!-- تأكيد الحذف -->
    <DeleteConfirmModal
      :show="showDeleteConfirm"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted, nextTick } from 'vue';
import ScaleHeader from './ScaleHeader.vue';
import SearchFilters from './SearchFilters.vue';
import ScalesTable from './ScalesTable.vue';
import ScaleModal from './ScaleModal.vue';
import PreviewModal from './PreviewModal.vue';
import DeleteConfirmModal from './DeleteConfirmModal.vue';
import { useScalesStore } from '@/stores/scales.ts';
import type { Scale, Question, Interpretation, ScaleForm } from './types';
import api from '@/utils/api';
import { useToast } from '@/composables/useToast';

// استخدام الـ store
const scalesStore = useScalesStore();
const toast = useToast();

// البيانات الأولية
const scales = computed(() => scalesStore.scales);
const loading = computed(() => scalesStore.loading);
const error = computed(() => scalesStore.error);

// Refs
const modal = ref(false);
const previewModal = ref(false);
const showDeleteConfirm = ref(false);
const current = ref<Scale | null>(null);
const previewData = ref<Scale | null>(null);
const deleteTargetId = ref<string | null>(null);
const currentStep = ref(1);
const saving = ref(false);
const submitted = ref(false);

const searchQuery = ref('');
const categoryFilter = ref('');
const statusFilter = ref('');

const currentPage = ref(1);
const itemsPerPage = ref(10);

// Form - تحديث الهيكل ليتوافق مع الـ API
const form = reactive<ScaleForm>({
  name_ar: '',
  name_en: '',
  description_ar: '',
  description_en: '',
  category_id: '',
  image_url: '',
  max_score: 100,
  is_active: true,
  questions: [createDefaultQuestion()],
  interpretations: [createDefaultInterpretation()]
});

// دوال مساعدة لإنشاء بيانات فارغة
function createDefaultQuestion(): Question {
  return {
    question_text_ar: '',
    question_text_en: '',
    question_order: 1,
    options: [createDefaultOption()]
  };
}

function createDefaultInterpretation(): Interpretation {
  return {
    min_score: 0,
    max_score: 10,
    interpretation_label_ar: '',
    interpretation_label_en: '',
    description_ar: '',
    description_en: '',
    color: 'green'
  };
}

function createDefaultOption() {
  return {
    option_text_ar: '',
    option_text_en: '',
    score_value: 0,
    option_order: 1
  };
}

// دوال التحقق من الصحة - نسخة مرنة

const validateStep1 = (): string[] => {
  const errors: string[] = [];

  if (!form.name_ar.trim()) errors.push('اسم المقياس (العربية) مطلوب');
  if (!form.name_en.trim()) errors.push('اسم المقياس (الإنجليزية) مطلوب');
  if (!form.category_id) errors.push('الفئة مطلوبة');
  
  return errors;
};

const validateStep2 = (): string[] => {
  const errors: string[] = [];

  if (!form.questions || form.questions.length === 0) {
    errors.push('يجب إضافة سؤال واحد على الأقل');
    return errors;
  }
  
  // التحقق فقط من الأسئلة التي تحتوي على بيانات
  const questionsWithData = form.questions.filter(q => 
    q.question_text_ar?.trim() || q.question_text_en?.trim()
  );
  
  if (questionsWithData.length === 0) {
    errors.push('يجب إدخال بيانات لسؤال واحد على الأقل');
    return errors;
  }
  
  // التحقق من الأسئلة التي تحتوي على بيانات
  questionsWithData.forEach((question, index) => {
    const originalIndex = form.questions.indexOf(question) + 1;
    
    if (!question.question_text_ar?.trim()) {
      errors.push(`نص السؤال ${originalIndex} (العربية) مطلوب`);
    }
    if (!question.question_text_en?.trim()) {
      errors.push(`نص السؤال ${originalIndex} (الإنجليزية) مطلوب`);
    }
    
    // التحقق من الخيارات فقط إذا كان السؤال يحتوي على بيانات
    if (question.options && question.options.length > 0) {
      const optionsWithData = question.options.filter(opt => 
        opt.option_text_ar?.trim()
      );
      
      if (optionsWithData.length === 0) {
        errors.push(`يجب إدخال بيانات لخيار واحد على الأقل للسؤال ${originalIndex}`);
      } else {
        optionsWithData.forEach((option, optIndex) => {
          if (!option.option_text_ar?.trim()) {
            errors.push(`نص الخيار ${optIndex + 1} للسؤال ${originalIndex} (العربية) مطلوب`);
          }
        });
      }
    }
  });
  
  return errors;
};

const validateStep3 = (): string[] => {
  const errors: string[] = [];

  if (!form.interpretations || form.interpretations.length === 0) {
    errors.push('يجب إضافة مستوى تفسير واحد على الأقل');
    return errors;
  }
  
  // التحقق من وجود تفسير واحد صالح على الأقل
  const hasValidInterpretation = form.interpretations.some(int => 
    (int.interpretation_label_ar?.trim() || int.interpretation_label_en?.trim()) &&
    int.min_score !== undefined && 
    int.max_score !== undefined &&
    int.max_score > int.min_score
  );
  
  if (!hasValidInterpretation) {
    errors.push('يجب إدخال بيانات صالحة لمستوى تفسير واحد على الأقل (تصنيف + نطاق نقاط صحيح)');
  }
  
  return errors;
};

// Computed
const filteredScales = computed(() => {
  return scales.value.filter((scale: Scale) => {
    const matchesSearch = !searchQuery.value || 
      scale.name_ar?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      scale.name_en?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      scale.description_ar?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      scale.description_en?.toLowerCase().includes(searchQuery.value.toLowerCase());
    
    const matchesCategory = !categoryFilter.value || 
      scale.category?.name_ar === categoryFilter.value;
    
    const matchesStatus = !statusFilter.value || 
      (statusFilter.value === 'active' && scale.is_active) ||
      (statusFilter.value === 'inactive' && !scale.is_active);
    
    return matchesSearch && matchesCategory && matchesStatus;
  });
});

const totalPages = computed(() => Math.ceil(filteredScales.value.length / itemsPerPage.value));

const paginatedScales = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredScales.value.slice(start, end);
});

const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);
const endIndex = computed(() => Math.min(currentPage.value * itemsPerPage.value, filteredScales.value.length));

const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) pages.push(i);
      pages.push('...');
      pages.push(total);
    } else if (current >= total - 3) {
      pages.push(1);
      pages.push('...');
      for (let i = total - 4; i <= total; i++) pages.push(i);
    } else {
      pages.push(1);
      pages.push('...');
      for (let i = current - 1; i <= current + 1; i++) pages.push(i);
      pages.push('...');
      pages.push(total);
    }
  }
  return pages;
});

// Watchers
watch([searchQuery, categoryFilter, statusFilter], () => {
  currentPage.value = 1;
});

watch(itemsPerPage, () => {
  currentPage.value = 1;
});

// Methods
async function fetchScales() {
  try {
    await scalesStore.fetchScales();
    await scalesStore.fetchCategories();
  } catch (err) {
    console.error('Error fetching scales:', err);
  }
}

function clearFilters() {
  searchQuery.value = '';
  categoryFilter.value = '';
  statusFilter.value = '';
}

function handleImageUpload(event: Event) {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const reader = new FileReader();
    reader.onload = (e) => {
      form.image_url = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
}

function removeImage() {
  form.image_url = '';
}

function prevPage() {
  if (currentPage.value > 1) currentPage.value--;
}

function nextPage() {
  if (currentPage.value < totalPages.value) currentPage.value++;
}

function goToPage(page: number | string) {
  if (typeof page === 'number') {
    currentPage.value = page;
  }
}

function openCreate() {
  current.value = null;
  resetForm();
  currentStep.value = 1;
  submitted.value = false;
  modal.value = true;
}

async function edit(scale: Scale) {
  try {
    console.log('🔄 بدء تحميل بيانات المقياس للتعديل:', scale.id);

    // 1. أولاً، جرب الحصول على البيانات من الـ store الموجود
    const existingScale = scales.value.find((s: Scale) => s.id === scale.id);
    
    // 2. إذا كانت البيانات موجودة في الـ store مع الأسئلة
    if (existingScale && existingScale.questions && existingScale.questions.length > 0) {
      console.log('📦 استخدام البيانات الموجودة في الـ store');
      await loadScaleForEdit(existingScale);
      return;
    }
    
    // 3. إذا لم تكن موجودة، جلبها من الـ API
    console.log('🌐 جلب البيانات من الـ API...');
    const fullScale = await scalesStore.fetchScaleById(scale.id);
    
    if (!fullScale) {
      toast.error('لم يتم العثور على بيانات المقياس');
      return;
    }
    
    // 4. تحميل الأسئلة والتفسيرات إذا لم تكن موجودة
    await loadScaleForEdit(fullScale);
    
  } catch (err) {
    console.error('❌ خطأ في تحميل بيانات المقياس للتعديل:', err);
    toast.error('فشل تحميل بيانات المقياس للتعديل');
  }
}

// دالة مساعدة لتحميل البيانات للتعديل
async function loadScaleForEdit(fullScale: Scale) {
  // إعادة تعيين النموذج
  resetForm();
  
  // تعبئة البيانات الأساسية
  form.name_ar = fullScale.name_ar || '';
  form.name_en = fullScale.name_en || '';
  form.description_ar = fullScale.description_ar || '';
  form.description_en = fullScale.description_en || '';
  form.image_url = fullScale.image_url || '';
  form.max_score = fullScale.max_score || 100;
  form.is_active = fullScale.is_active !== undefined ? fullScale.is_active : true;
  
  // معالجة category_id
  if (fullScale.category_id) {
    form.category_id = String(fullScale.category_id);
  } else if (fullScale.category?.id) {
    form.category_id = String(fullScale.category.id);
  } else {
    form.category_id = '';
  }
  
  // تحميل الأسئلة والتفسيرات إذا لم تكن موجودة
  if (!fullScale.questions || fullScale.questions.length === 0) {
    console.log('🔄 جلب الأسئلة بشكل منفصل...');
    try {
      const questionsResponse = await api.get(`/psychological-scales/${fullScale.id}/questions`);
      fullScale.questions = questionsResponse.data.data || questionsResponse.data || [];
      console.log('✅ تم جلب', fullScale.questions?.length ?? 0, 'سؤال');
    } catch (err) {
      console.warn('❌ لا يمكن جلب الأسئلة:', err);
      fullScale.questions = [];
    }
  }
  
  if (!fullScale.interpretations || fullScale.interpretations.length === 0) {
    console.log('🔄 جلب التفسيرات بشكل منفصل...');
    try {
      const interpretationsResponse = await api.get(`/psychological-scales/${fullScale.id}/interpretations`);
      fullScale.interpretations = interpretationsResponse.data.data || interpretationsResponse.data || [];
      console.log('✅ تم جلب', fullScale.interpretations?.length ?? 0, 'تفسير');
    } catch (err) {
      console.warn('❌ لا يمكن جلب التفسيرات:', err);
      fullScale.interpretations = [];
    }
  }
  
  // تعبئة الأسئلة
  form.questions = [];
  if (fullScale.questions && fullScale.questions.length > 0) {
    form.questions = fullScale.questions.map((q: any, index: number) => ({
      id: q.id,
      question_text_ar: q.question_text_ar || '',
      question_text_en: q.question_text_en || '',
      question_order: q.question_order || index + 1,
      options: (q.options && q.options.length > 0) ? 
        q.options.map((o: any, optIndex: number) => ({
          id: o.id,
          option_text_ar: o.option_text_ar || '',
          option_text_en: o.option_text_en || '',
          score_value: o.score_value || 0,
          option_order: o.option_order || optIndex + 1
        })) : [createDefaultOption()]
    }));
  } else {
    form.questions = [createDefaultQuestion()];
  }
  
  // تعبئة التفسيرات
  form.interpretations = [];
  if (fullScale.interpretations && fullScale.interpretations.length > 0) {
    form.interpretations = fullScale.interpretations.map((int: any) => ({
      id: int.id,
      min_score: int.min_score || 0,
      max_score: int.max_score || 10,
      interpretation_label_ar: int.interpretation_label_ar || '',
      interpretation_label_en: int.interpretation_label_en || '',
      description_ar: int.description_ar || '',
      description_en: int.description_en || '',
      color: int.color || 'green'
    }));
  } else {
    form.interpretations = [createDefaultInterpretation()];
  }
  
  current.value = fullScale;
  
  // الانتقال لـ nextTick
  await nextTick();
  
  currentStep.value = 1;
  submitted.value = false;
  modal.value = true;
  
  console.log('✅ النموذج جاهز:', {
    questions: form.questions.length,
    interpretations: form.interpretations.length,
    sampleQuestion: form.questions[0]
  });
  
  toast.success('تم تحميل بيانات المقياس');
}

async function openPreview(scale: Scale) {
  try {
    console.log('🔄 فتح معاينة المقياس:', scale.id);
    const fullScale = await scalesStore.fetchScaleById(scale.id);
    previewData.value = fullScale;
    previewModal.value = true;
  } catch (err) {
    console.error('❌ Error loading scale for preview:', err);
  }
}

function closePreview() {
  previewModal.value = false;
  previewData.value = null;
}

function close() { 
  modal.value = false;
  resetForm();
  currentStep.value = 1;
  submitted.value = false;
}

function resetForm() {
  form.name_ar = '';
  form.name_en = '';
  form.description_ar = '';
  form.description_en = '';
  form.category_id = '';
  form.image_url = '';
  form.max_score = 100;
  form.is_active = true;
  form.questions = [createDefaultQuestion()];
  form.interpretations = [createDefaultInterpretation()];
  current.value = null;
}

function nextStep() {
  if (currentStep.value === 1) {
    const errors = validateStep1();
    if (errors.length > 0) {
      submitted.value = true;
      console.log('أخطاء الخطوة 1:', errors);
      return;
    }
  } else if (currentStep.value === 2) {
    const errors = validateStep2();
    if (errors.length > 0) {
      submitted.value = true;
      console.log('أخطاء الخطوة 2:', errors);
      return;
    }
  }
  
  if (currentStep.value < 3) {
    currentStep.value++;
  }
}

function prevStep() {
  if (currentStep.value > 1) {
    currentStep.value--;
  }
}

function addQuestion() {
  form.questions.push({
    question_text_ar: '',
    question_text_en: '',
    question_order: form.questions.length + 1,
    options: [createDefaultOption()]
  });
}

function removeQuestion(index: number) {
  if (form.questions.length > 1) {
    form.questions.splice(index, 1);
    // تحديث ترتيب الأسئلة
    form.questions.forEach((q, i) => {
      q.question_order = i + 1;
    });
  }
}

function addOption(questionIndex: number) {
  form.questions[questionIndex].options?.push(createDefaultOption());
}

function removeOption(payload: { questionIndex: number; optionIndex: number }) {
  const { questionIndex, optionIndex } = payload;
  if (form.questions[questionIndex].options && form.questions[questionIndex].options.length > 1) {

    form.questions[questionIndex].options.splice(optionIndex, 1);
    // تحديث ترتيب الخيارات
    form.questions[questionIndex].options.forEach((o, i) => {
      o.option_order = i + 1;
    });
  }
}

function addInterpretation() {
  const lastInterpretation = form.interpretations[form.interpretations.length - 1];
  form.interpretations.push({
    min_score: lastInterpretation ? ((lastInterpretation.max_score ?? 0) + 1) : 0,
    max_score: lastInterpretation ? ((lastInterpretation.max_score ?? 0) + 10) : 10,

    interpretation_label_ar: '',
    interpretation_label_en: '',
    description_ar: '',
    description_en: '',
    color: 'yellow'
  });
}

function removeInterpretation(index: number) {
  if (form.interpretations.length > 1) {
    form.interpretations.splice(index, 1);
  }
}

// دالة الحفظ المحسنة - الإصدار النهائي
async function save() {
  if (saving.value) return;
  
  saving.value = true;
  submitted.value = true;
  
  try {
    console.log('💾 بدء حفظ المقياس...');
    
    // 1. التحقق من جميع الخطوات أولاً
    const step1Errors = validateStep1();
    const step2Errors = validateStep2();
    const step3Errors = validateStep3();
    
    const allErrors = [...step1Errors, ...step2Errors, ...step3Errors];
    
    if (allErrors.length > 0) {
      console.log('❌ أخطاء التحقق:', allErrors);
      showErrorNotification('يوجد أخطاء في البيانات. يرجى مراجعة جميع الخطوات.');
      return;
    }
    
    // 2. تنظيف البيانات وتجهيزها للإرسال
    console.log('🧹 تنظيف البيانات...');
    
    // الأسئلة النظيفة
    const cleanQuestions = form.questions
      .filter(q => q.question_text_ar?.trim() && q.question_text_en?.trim())
      .map((q, index) => ({
        id: q.id || undefined, // إرسال undefined بدلاً من null
        question_text_ar: q.question_text_ar?.trim() || '',
        question_text_en: q.question_text_en?.trim() || '',

        question_order: index + 1,
        options: (q.options || [])
          .filter(opt => opt.option_text_ar?.trim())
          .map((opt, optIndex) => ({
            id: opt.id || undefined,
            option_text_ar: opt.option_text_ar?.trim() || '',
            option_text_en: opt.option_text_en?.trim() || `Option ${optIndex + 1}`,
            score_value: Number(opt.score_value) || 0,
            option_order: optIndex + 1
          }))
      }));
    
    // التفسيرات النظيفة
    const cleanInterpretations = form.interpretations
      .filter(int => 
        (int.interpretation_label_ar?.trim() || int.interpretation_label_en?.trim()) &&
        int.min_score !== undefined && 
        int.max_score !== undefined
      )
      .map((int, index) => ({
        id: int.id || undefined,
        min_score: Number(int.min_score) || 0,
        max_score: Number(int.max_score) || 10,
        interpretation_label_ar: int.interpretation_label_ar?.trim() || 'غير محدد',
        interpretation_label_en: int.interpretation_label_en?.trim() || 'Undefined',
        description_ar: int.description_ar?.trim() || '',
        description_en: int.description_en?.trim() || '',
        color: int.color || 'blue'
      }));
    
    // 3. التحقق النهائي بعد التنظيف
    if (cleanQuestions.length === 0) {
      showErrorNotification('يجب إدخال بيانات صالحة لسؤال واحد على الأقل');
      return;
    }
    
    if (cleanInterpretations.length === 0) {
      showErrorNotification('يجب إدخال بيانات صالحة لمستوى تفسير واحد على الأقل');
      return;
    }
    
    console.log('📊 البيانات النظيفة:', {
      questions: cleanQuestions.length,
      interpretations: cleanInterpretations.length
    });
    
    // 4. تحضير البيانات النهائية - متوافقة مع Laravel
    const scaleData = {
      // البيانات الأساسية (يجب أن تتوافق مع fillable في الموديل)
      category_id: form.category_id,
      name_ar: form.name_ar.trim(),
      name_en: form.name_en.trim(),
      description_ar: form.description_ar?.trim() || null,
      description_en: form.description_en?.trim() || null,
      // إرسال null بدلاً من سلسلة فارغة، وقبول base64 data URLs
      image_url: form.image_url && form.image_url.trim() !== '' ? form.image_url.trim() : null,
      max_score: Number(form.max_score) || 100,
      is_active: Boolean(form.is_active),
      
      // الأسئلة (يجب أن تتوافق مع متطلبات التحقق في الـ Controller)
      questions: cleanQuestions,
      
      // التفسيرات (يجب أن تتوافق مع متطلبات التحقق في الـ Controller)
      interpretations: cleanInterpretations
    };
    
    console.log('📤 إرسال البيانات للخادم:', scaleData);
    
    let savedScale: any;

    if (current.value) {
      // التحديث
      console.log('🔄 تحديث المقياس:', current.value.id);
      const response = await api.put(`/psychological-scales/${current.value.id}/full`, scaleData);
      savedScale = response.data.data;
      console.log('✅ المقياس محدث:', savedScale);
    } else {
      // الإنشاء - استخدم البيانات الأساسية فقط للإنشاء
      console.log('🆕 إنشاء مقياس جديد');
      const createData = {
        category_id: form.category_id,
        name_ar: form.name_ar.trim(),
        name_en: form.name_en.trim(),
        description_ar: form.description_ar?.trim() || null,
        description_en: form.description_en?.trim() || null,
        // إرسال null بدلاً من سلسلة فارغة، وقبول base64 data URLs
        image_url: form.image_url && form.image_url.trim() !== '' ? form.image_url.trim() : null,
        max_score: Number(form.max_score) || 100,
        is_active: Boolean(form.is_active)
      };
      
      console.log('📤 بيانات الإنشاء:', createData);
      savedScale = await scalesStore.createScale(createData);
      
      // بعد الإنشاء، أضف الأسئلة والتفسيرات باستخدام الـ endpoint الكامل
      if (savedScale && savedScale.id) {
        console.log('📝 إضافة الأسئلة والتفسيرات للمقياس الجديد:', savedScale.id);
        const fullData = {
          ...scaleData,
          questions: cleanQuestions.map(q => ({ ...q, scale_id: savedScale.id })),
          interpretations: cleanInterpretations.map(int => ({ ...int, scale_id: savedScale.id }))
        };
        
        const updateResponse = await api.put(`/psychological-scales/${savedScale.id}/full`, fullData);
        savedScale = updateResponse.data.data;
      }
    }
    
    // 5. النجاح
    modal.value = false;
    resetForm();
    await fetchScales();
    
    console.log('🎉 تم الحفظ بنجاح');
    showSuccessNotification('تم حفظ المقياس بنجاح');
    
  } catch (err: any) {
    console.error('❌ خطأ في الحفظ:', err);
    
    // استخدام handleApiError لعرض رسائل واضحة
    toast.handleApiError(err, 'حدث خطأ أثناء حفظ المقياس. يرجى المحاولة مرة أخرى.');
    
  } finally {
    saving.value = false;
  }
}

// دوال مساعدة للإشعارات
function showErrorNotification(message: string) {
  toast.error(message);
}

function showSuccessNotification(message: string) {
  toast.success(message);
}

function deleteScale(id: string) {
  deleteTargetId.value = id;
  showDeleteConfirm.value = true;
}

async function confirmDelete() {
  if (deleteTargetId.value) {
    try {
      await scalesStore.deleteScale(deleteTargetId.value);
      showSuccessNotification('تم حذف المقياس بنجاح');
      await fetchScales(); // إعادة تحميل القائمة
    } catch (err: any) {
      console.error('Error deleting scale:', err);
      toast.handleApiError(err, 'حدث خطأ أثناء حذف المقياس. يرجى المحاولة مرة أخرى.');
    }
  }
  showDeleteConfirm.value = false;
  deleteTargetId.value = null;
}

// دورة الحياة
onMounted(() => {
  fetchScales();
});
</script>