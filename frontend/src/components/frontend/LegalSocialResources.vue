<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <Header :language="currentLanguage" />

        <!-- Hero Section -->
        <Hero 
            :title="translate('resourcesPage.title')"
            :subtitle="translate('resourcesPage.description')"
            :buttons="heroButtons"
        />

        <!-- Main Content -->
        <main class="container mx-auto px-4 py-28">
            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-20">
                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-primary-green"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl text-center">
                <p class="text-lg">{{ error }}</p>
                <button @click="fetchData" class="mt-4 bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    إعادة المحاولة
                </button>
            </div>

            <!-- Main Content -->
            <div v-else class="space-y-16">
                <!-- Resources Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Legal Resources Section -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-primary-green bg-opacity-10 rounded-xl flex items-center justify-center">
                                <i class="fas fa-balance-scale text-primary-green text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-800 mb-2">{{ translate('resourcesPage.legalResources.title') }}</h2>
                                <p class="text-gray-600 text-sm">{{ translate('resourcesPage.legalResources.description') }}</p>
                            </div>
                        </div>

                        <!-- Search and Filter -->
                        <div class="mb-6 space-y-4">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <input
                                        v-model="legalSearch"
                                        type="text"
                                        :placeholder="translate('resourcesPage.searchPlaceholder')"
                                        class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-green focus:border-transparent text-sm"
                                    />
                                </div>
                                <select
                                    v-model="selectedLawType"
                                    class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-green focus:border-transparent text-sm"
                                >
                                    <option value="">{{ translate('resourcesPage.allTypes') }}</option>
                                    <option v-for="type in lawTypes" :key="type" :value="type">
                                        {{ type }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Categories Navigation -->
                        <div class="mb-6">
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="category in categories"
                                    :key="category.id"
                                    @click="selectCategory(category.id)"
                                    class="px-3 py-2 rounded-lg text-xs font-medium transition-all duration-300"
                                    :class="selectedCategoryId === category.id
                                        ? 'bg-primary-green text-white shadow-lg'
                                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                >
                                    {{ category.name }}
                                </button>
                            </div>
                        </div>

                        <!-- Laws List -->
                        <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar pr-2">
                            <div
                                v-for="(resource, index) in filteredLegalResources"
                                :key="resource.id"
                                class="bg-white rounded-lg p-4 border border-gray-100 hover:border-primary-green hover:shadow-md transition-all duration-300"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-primary-green font-bold text-base">
                                        {{ resource.article_number }}
                                    </span>
                                    <span class="text-primary-pink text-xs bg-primary-pink/10 px-2 py-1 rounded-full font-medium">
                                        {{ resource.law_type }}
                                    </span>
                                </div>
                                <p class="text-gray-700 leading-relaxed mb-3 text-sm">
                                    {{ resource.text }}
                                </p>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                        {{ resource.category?.name }}
                                    </span>
                                    <button
                                        @click="toggleResourceDetails(resource.id)"
                                        class="text-primary-green hover:text-primary-pink transition-colors text-sm"
                                    >
                                        <i class="fas fa-chevron-down text-xs" :class="{ 'transform rotate-180': expandedResources.includes(resource.id) }"></i>
                                    </button>
                                </div>
                                
                                <!-- Expanded Details -->
                                <div v-if="expandedResources.includes(resource.id)" class="mt-3 pt-3 border-t border-gray-100">
                                    <div class="grid grid-cols-1 gap-3 text-xs text-gray-600">
                                        <div>
                                            <strong class="text-primary-green">النص العربي:</strong>
                                            <p class="mt-1 leading-relaxed">{{ resource.text_ar }}</p>
                                        </div>
                                        <div>
                                            <strong class="text-primary-green">English Text:</strong>
                                            <p class="mt-1 leading-relaxed">{{ resource.text_en }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="filteredLegalResources.length === 0" class="text-center py-8 text-gray-500">
                            <i class="fas fa-search text-2xl mb-2"></i>
                            <p>لا توجد نتائج مطابقة للبحث</p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="legalResources.meta && legalResources.meta.last_page > 1" class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-3">
                            <div class="text-sm text-gray-600">
                                عرض {{ legalResources.data.length }} من {{ legalResources.meta.total }}
                            </div>
                            <div class="flex gap-2">
                                <button
                                    @click="loadMoreLegalResources('prev')"
                                    :disabled="!legalResources.meta.prev_page_url"
                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                                >
                                    السابق
                                </button>
                                <span class="px-3 py-1 text-sm text-gray-600">
                                    {{ legalResources.meta.current_page }} / {{ legalResources.meta.last_page }}
                                </span>
                                <button
                                    @click="loadMoreLegalResources('next')"
                                    :disabled="!legalResources.meta.next_page_url"
                                    class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                                >
                                    التالي
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Social Resources Section -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-primary-pink bg-opacity-10 rounded-xl flex items-center justify-center">
                                <i class="fas fa-hands-helping text-primary-pink text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-800 mb-2">{{ translate('resourcesPage.socialResources.title') }}</h2>
                                <p class="text-gray-600 text-sm">{{ translate('resourcesPage.socialResources.description') }}</p>
                            </div>
                        </div>

                        <!-- Social Resources Content -->
                        <div class="space-y-6">
                            <!-- Emergency Contacts -->
                            <div class="bg-gradient-to-r from-primary-green/5 to-primary-pink/5 rounded-xl p-4 border border-primary-green/20">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ translate('resourcesPage.emergencyContacts.title') }}</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="text-center p-3 bg-white rounded-lg border border-gray-100">
                                        <i class="fas fa-phone-alt text-primary-green text-lg mb-2"></i>
                                        <p class="font-semibold text-sm">الطوارئ</p>
                                        <p class="text-primary-green font-bold">911</p>
                                    </div>
                                    <div class="text-center p-3 bg-white rounded-lg border border-gray-100">
                                        <i class="fas fa-headset text-primary-pink text-lg mb-2"></i>
                                        <p class="font-semibold text-sm">الدعم النفسي</p>
                                        <p class="text-primary-pink font-bold">0800-123-456</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Support Organizations -->
                            <div class="bg-gradient-to-r from-primary-pink/5 to-primary-green/5 rounded-xl p-4 border border-primary-pink/20">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ translate('resourcesPage.supportingOrganizations.title') }}</h3>
                                <div class="space-y-3">
                                    <div v-for="org in supportingOrganizations" :key="org.name" 
                                         class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-100 hover:border-primary-green transition-colors">
                                        <div class="w-8 h-8 rounded-full bg-primary-green flex items-center justify-center flex-shrink-0">
                                            <i :class="org.icon" class="text-white text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm truncate">{{ getTranslatedOrganization(org.name) }}</p>
                                            <p class="text-xs text-gray-600 truncate">{{ getTranslatedOrganizationDesc(org.description) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Resources -->
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Women & Children Rights -->
                                <div class="bg-white rounded-lg p-4 border border-gray-100">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-8 h-8 bg-primary-pink rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-female text-white text-xs"></i>
                                        </div>
                                        <h4 class="text-base font-semibold text-gray-800">{{ translate('resourcesPage.womenChildrenRights.title') }}</h4>
                                    </div>
                                    <ul class="space-y-2">
                                        <li v-for="right in womenChildrenRights" :key="right" 
                                            class="flex items-center gap-2 text-xs text-gray-600">
                                            <i class="fas fa-check text-primary-green text-xs"></i>
                                            <span>{{ getTranslatedRight(right) }}</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Community Support -->
                                <div class="bg-white rounded-lg p-4 border border-gray-100">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-8 h-8 bg-primary-green rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-users text-white text-xs"></i>
                                        </div>
                                        <h4 class="text-base font-semibold text-gray-800">{{ translate('resourcesPage.communitySupport.title') }}</h4>
                                    </div>
                                    <ul class="space-y-2">
                                        <li v-for="support in communitySupport" :key="support" 
                                            class="flex items-center gap-2 text-xs text-gray-600">
                                            <i class="fas fa-check text-primary-green text-xs"></i>
                                            <span>{{ getTranslatedSupport(support) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Section -->
                <div class="bg-gradient-to-r from-primary-green/10 to-primary-pink/10 rounded-2xl p-6 border border-primary-green/20">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">{{ translate('resourcesPage.quickActions.title') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <button 
                            v-for="action in quickActions" 
                            :key="action.text"
                            @click="handleQuickAction(action.action)"
                            class="bg-white rounded-xl p-4 text-center border border-gray-100 hover:border-primary-green hover:shadow-lg transition-all duration-300 group"
                        >
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center transition-colors duration-300"
                                 :class="action.bgColor">
                                <i :class="action.icon" class="text-white text-lg"></i>
                            </div>
                            <h3 class="font-semibold text-gray-800 mb-2 text-sm">{{ action.text }}</h3>
                            <p class="text-gray-600 text-xs">{{ action.description }}</p>
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <Footer :language="currentLanguage" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useTranslations } from '@/composables/useTranslations'
import Header from '@/components/frontend/layouts/Header.vue'
import Hero from '@/components/frontend/layouts/hero.vue'
import Footer from '@/components/frontend/layouts/Footer.vue'

const router = useRouter()
const { translate } = useTranslations()

// إضافة base URL لـ API
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

// Reactive data
const currentLanguage = ref(localStorage.getItem('preferredLanguage') || 'ar')
const loading = ref(false)
const error = ref(null)

// Legal resources data
const legalResources = ref({ data: [], meta: null })
const categories = ref([])
const selectedCategoryId = ref(null)
const selectedLawType = ref('')
const legalSearch = ref('')
const expandedResources = ref([])

// Social resources data
const supportingOrganizations = ref([
    { name: 'hospitals', icon: 'fas fa-hospital', description: 'healthcare' },
    { name: 'courts', icon: 'fas fa-gavel', description: 'legalServices' },
    { name: 'police', icon: 'fas fa-shield-alt', description: 'protection' },
    { name: 'schools', icon: 'fas fa-school', description: 'education' }
])

const womenChildrenRights = ref(['right1', 'right2', 'right3', 'right4'])
const communitySupport = ref(['support1', 'support2', 'support3', 'support4'])
const positiveEducation = ref(['education1', 'education2', 'education3', 'education4'])

// Quick Actions
const quickActions = ref([
    {
        text: 'الدردشة المباشرة',
        description: 'احصل على مساعدة فورية',
        icon: 'fas fa-comments',
        bgColor: 'bg-primary-pink group-hover:bg-primary-pink/80',
        action: 'chat'
    },
    {
        text: 'التواصل مع محامي',
        description: 'استشارة قانونية متخصصة',
        icon: 'fas fa-gavel',
        bgColor: 'bg-primary-green group-hover:bg-primary-green/80',
        action: 'lawyer'
    },
    {
        text: 'الدعم النفسي',
        description: 'جلسات استشارية متخصصة',
        icon: 'fas fa-heart',
        bgColor: 'bg-primary-pink group-hover:bg-primary-pink/80',
        action: 'support'
    },
    {
        text: 'التواصل معنا',
        description: 'مراكز الدعم والمساعدة',
        icon: 'fas fa-envelope',
        bgColor: 'bg-primary-green group-hover:bg-primary-green/80',
        action: 'contact'
    }
])

// Hero buttons
const heroButtons = computed(() => [
    { 
        text: translate('buttons.startJourney'), 
        icon: 'fas fa-play-circle', 
        primary: true,
        action: () => router.push('/get-help')
    },
    { 
        text: translate('buttons.learnMore'), 
        icon: 'fas fa-info-circle', 
        primary: false,
        action: () => router.push('/about')
    }
])

// Computed properties
const isRTL = computed(() => currentLanguage.value === 'ar')

const lawTypes = computed(() => {
    return [...new Set(legalResources.value.data.map(resource => resource.law_type))]
})

const filteredLegalResources = computed(() => {
    let resources = legalResources.value.data

    // Filter by category
    if (selectedCategoryId.value) {
        resources = resources.filter(resource => resource.category_id === selectedCategoryId.value)
    }

    // Filter by law type
    if (selectedLawType.value) {
        resources = resources.filter(resource => resource.law_type === selectedLawType.value)
    }

    // Filter by search
    if (legalSearch.value) {
        const searchTerm = legalSearch.value.toLowerCase()
        resources = resources.filter(resource => 
            resource.text.toLowerCase().includes(searchTerm) ||
            resource.article_number.toLowerCase().includes(searchTerm) ||
            (resource.text_ar && resource.text_ar.toLowerCase().includes(searchTerm))
        )
    }

    return resources
})

// Methods
const fetchData = async () => {
    loading.value = true
    error.value = null

    try {
        await Promise.all([
            fetchLegalResources(),
            fetchCategories()
        ])
    } catch (err) {
        error.value = 'فشل في تحميل البيانات. يرجى المحاولة مرة أخرى.'
        console.error('Error fetching data:', err)
    } finally {
        loading.value = false
    }
}

const fetchLegalResources = async (page = 1) => {
    try {
        const params = new URLSearchParams({
            page: page.toString(),
            per_page: '10',
            locale: currentLanguage.value
        })

        if (selectedCategoryId.value) {
            params.append('category_id', selectedCategoryId.value)
        }

        if (selectedLawType.value) {
            params.append('law_type', selectedLawType.value)
        }

        if (legalSearch.value) {
            params.append('search', legalSearch.value)
        }

        const response = await fetch(`${API_BASE_URL}/legal-resources?${params}`, {
            headers: {
                'Accept': 'application/json',
                'Accept-Language': currentLanguage.value
            }
        })
        
        if (!response.ok) throw new Error('Network response was not ok')
        
        legalResources.value = await response.json()
    } catch (err) {
        console.error('Error fetching legal resources:', err)
        throw new Error('Failed to fetch legal resources')
    }
}

const fetchCategories = async () => {
    try {
        const response = await fetch(`${API_BASE_URL}/legal-resource-categories`, {
            headers: {
                'Accept': 'application/json',
                'Accept-Language': currentLanguage.value
            }
        })
        
        if (!response.ok) throw new Error('Network response was not ok')
        
        categories.value = await response.json()
    } catch (err) {
        console.error('Error fetching categories:', err)
        throw new Error('Failed to fetch categories')
    }
}

const selectCategory = (categoryId) => {
    selectedCategoryId.value = selectedCategoryId.value === categoryId ? null : categoryId
    fetchLegalResources(1)
}

const toggleResourceDetails = (resourceId) => {
    const index = expandedResources.value.indexOf(resourceId)
    if (index > -1) {
        expandedResources.value.splice(index, 1)
    } else {
        expandedResources.value.push(resourceId)
    }
}

const loadMoreLegalResources = (direction) => {
    const currentPage = legalResources.value.meta?.current_page || 1
    const newPage = direction === 'next' ? currentPage + 1 : currentPage - 1
    fetchLegalResources(newPage)
}

const handleQuickAction = (action) => {
    switch (action) {
        case 'chat':
            window.open('/chat', '_blank')
            break
        case 'lawyer':
            router.push('/legal-consultation')
            break
        case 'support':
            router.push('/psychological-support')
            break
        case 'contact':
            router.push('/contact')
            break
    }
}

// Translation methods
const getTranslatedRight = (key) => {
    const translation = translate(`resourcesPage.womenChildrenRights.list.${key}`)
    return typeof translation === 'object' ? translation[currentLanguage.value] : translation
}

const getTranslatedSupport = (key) => {
    const translation = translate(`resourcesPage.communitySupport.list.${key}`)
    return typeof translation === 'object' ? translation[currentLanguage.value] : translation
}

const getTranslatedEducation = (key) => {
    const translation = translate(`resourcesPage.positiveEducation.list.${key}`)
    return typeof translation === 'object' ? translation[currentLanguage.value] : translation
}

const getTranslatedOrganization = (key) => {
    const translation = translate(`resourcesPage.supportingOrganizations.list.${key}`)
    return typeof translation === 'object' ? translation[currentLanguage.value] : translation
}

const getTranslatedOrganizationDesc = (key) => {
    const translation = translate(`resourcesPage.supportingOrganizations.descriptions.${key}`)
    return typeof translation === 'object' ? translation[currentLanguage.value] : translation
}

// Watchers
watch([selectedLawType, legalSearch], () => {
    fetchLegalResources(1)
})

watch(currentLanguage, () => {
    fetchData()
})

// Event listener for language changes
const handleLanguageChange = (event) => {
    currentLanguage.value = event.detail.language
}

// Lifecycle
onMounted(() => {
    window.addEventListener('languageChanged', handleLanguageChange)
    fetchData()
})
</script>

<style scoped>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #10b981 #f1f1f1;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #10b981;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #059669;
}

.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* Responsive improvements */
@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
}
</style>