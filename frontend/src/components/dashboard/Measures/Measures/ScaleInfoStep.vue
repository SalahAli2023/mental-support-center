<template>
  <div class="grid gap-3 sm:gap-4">
    <!-- حقل الصورة -->
    <ImageUpload
      :image="form.image_url"
      @upload="$emit('imageUpload', $event)"
      @remove="$emit('imageRemove')"
    />

    <!-- اسم المقياس -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
      <FormField label="اسم المقياس (العربية) *">
        <input 
          v-model="form.name_ar" 
          placeholder="أدخل اسم المقياس بالعربية..." 
          class="input text-sm sm:text-base" 
          required
          :class="{ 'border-red-500': submitted && !form.name_ar }"
        />
        <p v-if="submitted && !form.name_ar" class="text-red-500 text-xs mt-1">هذا الحقل مطلوب</p>
      </FormField>
      <FormField label="اسم المقياس (الإنجليزية) *">
        <input 
          v-model="form.name_en" 
          placeholder="Enter scale name in English..." 
          class="input text-sm sm:text-base" 
          required
          :class="{ 'border-red-500': submitted && !form.name_en }"
        />
        <p v-if="submitted && !form.name_en" class="text-red-500 text-xs mt-1">هذا الحقل مطلوب</p>
      </FormField>
    </div>

    <!-- وصف المقياس -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
      <FormField label="وصف المقياس (العربية)">
        <textarea 
          v-model="form.description_ar" 
          placeholder="أدخل وصف المقياس بالعربية..." 
          rows="3"
          class="input text-sm sm:text-base"
        ></textarea>
      </FormField>
      <FormField label="وصف المقياس (الإنجليزية)">
        <textarea 
          v-model="form.description_en" 
          placeholder="Enter scale description in English..." 
          rows="3"
          class="input text-sm sm:text-base"
        ></textarea>
      </FormField>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
      <FormField label="الفئة *">
        <select 
          v-model="form.category_id" 
          class="input text-sm sm:text-base" 
          required
          :class="{ 'border-red-500': submitted && !form.category_id }"
        >
          <option value="">اختر الفئة</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name_ar }}
          </option>
        </select>
        <p v-if="submitted && !form.category_id" class="text-red-500 text-xs mt-1">هذا الحقل مطلوب</p>
      </FormField>
      <FormField label="الدرجة القصوى *">
        <input 
          v-model.number="form.max_score"
          type="number"
          class="input text-sm sm:text-base"
          placeholder="الدرجة العظمى للمقياس"
          min="1"
          required
          :class="{ 'border-red-500': submitted && !form.max_score }"
        />
        <p v-if="submitted && !form.max_score" class="text-red-500 text-xs mt-1">هذا الحقل مطلوب</p>
      </FormField>
    </div>
    
    <div class="flex items-center gap-2">
      <input 
        v-model="form.is_active"
        type="checkbox"
        id="active"
        class="rounded border-primary text-brand-500 focus:ring-brand-500"
      />
      <label for="active" class="text-sm text-primary">نشط</label>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import ImageUpload from './ImageUpload.vue';
import FormField from './FormField.vue';
import { useScalesStore } from '@/stores/scales';

const scalesStore = useScalesStore();
const categories = computed(() => scalesStore.categories);

defineProps<{
  form: any;
  submitted: boolean;
}>();

defineEmits<{
  imageUpload: [event: Event];
  imageRemove: [];
}>();
</script>