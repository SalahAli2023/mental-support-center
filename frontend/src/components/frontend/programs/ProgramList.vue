<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="text-center space-y-4">
      <h1 class="text-3xl font-bold text-primary">البرامج النفسية العلاجية</h1>
      <p class="text-secondary max-w-2xl mx-auto">
        برامج علاجية متكاملة تعتمد على مبادئ العلاج المعرفي السلوكي (CBT)
      </p>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 justify-center">
      <select v-model="filters.status" class="input">
        <option value="">جميع البرامج</option>
        <option value="active">نشط</option>
      </select>
      <input
        v-model="filters.search"
        type="text"
        placeholder="ابحث عن برنامج..."
        class="input"
      />
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="text-secondary">جاري التحميل...</div>
    </div>

    <!-- Programs Grid -->
    <div v-else-if="programs.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <ProgramCard
        v-for="program in filteredPrograms"
        :key="program.id"
        :program="program"
        @click="viewProgram(program.id)"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12 text-secondary">
      لا توجد برامج متاحة حالياً
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { programService } from '@/services/programService'
import ProgramCard from './ProgramCard.vue'

const router = useRouter()
const programs = ref<any[]>([])
const loading = ref(false)
const filters = ref({
  status: '',
  search: ''
})

const filteredPrograms = computed(() => {
  let result = programs.value

  if (filters.value.status) {
    result = result.filter(p => p.status === filters.value.status)
  }

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    result = result.filter(p =>
      p.name_ar.toLowerCase().includes(search) ||
      p.description_ar?.toLowerCase().includes(search)
    )
  }

  return result
})

const fetchPrograms = async () => {
  loading.value = true
  try {
    // استخدام نفس الـ API الذي يستخدمه index.vue
    const response = await programService.getPublicPrograms({ status: 'active' })
    
    console.log('📦 ProgramList API Response:', response)
    
    if (response.data && response.data.success) {
      // استخراج البيانات - نفس المنطق المستخدم في index.vue
      const responseData = response.data.data
      
      if (responseData && Array.isArray(responseData)) {
        programs.value = responseData
      } else if (responseData && responseData.data && Array.isArray(responseData.data)) {
        programs.value = responseData.data
      } else {
        programs.value = []
      }
      
      console.log(`✅ ProgramList loaded ${programs.value.length} programs`)
    } else {
      console.error('❌ ProgramList API response not successful:', response.data)
      programs.value = []
    }
  } catch (error) {
    console.error('❌ Error fetching programs in ProgramList:', error)
    programs.value = []
  } finally {
    loading.value = false
  }
}

const viewProgram = (programId: string) => {
  router.push(`/program/${programId}`)
}

onMounted(() => {
  fetchPrograms()
})
</script>

