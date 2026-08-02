<template>
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-primary">{{ t('library.title') }}</h1>
                <p class="text-xs sm:text-sm text-secondary mt-1 sm:mt-2">{{ t('library.subtitle') }}</p>
            </div>
            <Button v-if="hasAuth" variant="primary" size="sm sm:lg" class="w-full sm:w-auto flex items-center justify-center gap-2 shadow-soft" @click="upload">
                <CloudArrowUpIcon class="h-4 sm:h-5 w-4 sm:w-5" />
                {{ t('library.upload.upload') }}
            </Button>
        </div>

        <!-- Search and Filters -->
        <Card class="p-3 sm:p-4 md:p-6 shadow-soft">
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <!-- Search Input -->
                <div class="flex-1 relative">
                    <MagnifyingGlassIcon class="absolute right-3 top-1/2 transform -translate-y-1/2 h-4 sm:h-5 w-4 sm:w-5 text-tertiary" />
                    <input v-model="searchQuery" :placeholder="t('library.search_placeholder')"
                        class="w-full pr-10 sm:pr-12 pl-3 sm:pl-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg border border-primary bg-primary text-primary placeholder-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all duration-200"
                        @input="handleSearch" />
                </div>

                <!-- Filter Buttons -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <div class="grid grid-cols-2 sm:flex gap-2">
                        <select v-model="selectedCategory"
                            class="px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg border border-primary bg-primary text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent w-full transition-all duration-200"
                            @change="fetchItems">
                            <option value="">{{ t('library.all_categories') }}</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ locale === 'ar' ? category.name_ar : category.name_en }}
                            </option>
                        </select>

                        <select v-model="selectedType"
                            class="px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg border border-primary bg-primary text-primary focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent w-full transition-all duration-200"
                            @change="fetchItems">
                            <option value="">{{ t('library.all_types') }}</option>
                            <option value="book">{{ t('library.books') }}</option>
                            <option value="research">{{ t('library.research') }}</option>
                            <option value="guide">{{ t('library.guides') }}</option>
                            <option value="article">{{ t('library.articles') }}</option>
                        </select>
                    </div>
                    
                    <Button variant="outline" @click="toggleSort" class="flex items-center justify-center gap-2 w-full sm:w-auto py-2 sm:py-3 border-primary hover:border-brand-500 transition-all duration-200 text-primary hover:text-brand-500">
                        <BarsArrowUpIcon class="h-4 w-4" />
                        <span class="truncate">{{sortOptions.find(opt => opt.value === sortBy)?.label}}</span>
                    </Button>
                </div>
            </div>

            <!-- Active Filters -->
            <div v-if="activeFilters.length > 0" class="flex flex-wrap items-center gap-2 mt-3 sm:mt-4">
                <span v-for="filter in activeFilters" :key="filter.key"
                    class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 rounded-full bg-brand-500/10 text-brand-500 text-xs sm:text-sm border border-brand-500/20 transition-all duration-200 hover:bg-brand-500/20 dark:bg-brand-500/20 dark:text-brand-500/90 dark:border-brand-500/30">
                    {{ filter.label }}
                    <button @click="removeFilter(filter.key)" class="hover:text-brand-500/70 dark:hover:text-brand-500 transition-colors duration-200">
                        <XMarkIcon class="h-3 w-3" />
                    </button>
                </span>
                <button @click="clearFilters" class="text-xs sm:text-sm text-secondary hover:text-primary dark:text-tertiary dark:hover:text-primary transition-colors duration-200">
                    {{ t('library.clear_all') }}
                </button>
            </div>
        </Card>

        <!-- Management Table for Admin -->
        <Card v-if="hasAuth" class="p-3 sm:p-4 md:p-6 shadow-soft">
            <template #header>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <div class="text-base sm:text-lg text-primary font-semibold">إدارة محتويات المكتبة</div>
                    <div class="text-sm text-secondary dark:text-tertiary">
                        إجمالي العناصر: <span class="font-semibold text-brand-500 dark:text-brand-500/90">{{ items.length }}</span>
                    </div>
                </div>
            </template>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center py-6 sm:py-8">
                <div class="animate-spin rounded-full h-6 sm:h-8 w-6 sm:w-8 border-b-2 border-brand-500 dark:border-brand-500/70"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="bg-accent-500/10 dark:bg-accent-500/20 border border-accent-500/30 dark:border-accent-500/40 text-accent-500 dark:text-accent-500/90 px-3 sm:px-4 py-2 sm:py-3 rounded-lg mb-3 sm:mb-4 text-sm sm:text-base">
                {{ error }}
            </div>

            <!-- Table -->
            <div v-else-if="!loading && items.length > 0" class="overflow-x-auto -mx-2 sm:mx-0">
                <div class="min-w-full inline-block align-middle">
                    <!-- Mobile Cards View -->
                    <div class="sm:hidden space-y-3">
                        <div v-for="(item, index) in items" :key="item.id"
                            class="bg-primary border border-primary rounded-lg p-3 space-y-2 shadow-sm hover:shadow-md transition-all duration-200 dark:border-secondary">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-2">
                                    <div class="flex-shrink-0">
                                        <img v-if="item.cover_image" :src="item.cover_image" :alt="item.title_ar"
                                            class="w-8 h-8 rounded-lg object-cover border border-primary dark:border-secondary">
                                        <div v-else
                                            class="w-8 h-8 rounded-lg bg-tertiary dark:bg-secondary flex items-center justify-center border border-primary dark:border-secondary">
                                            <DocumentTextIcon class="h-4 w-4 text-tertiary dark:text-secondary" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-primary text-sm">{{ item.title_ar }}</div>
                                        <div class="text-xs text-secondary dark:text-tertiary">{{ item.author_ar }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span :class="['badge text-xs', item.is_published ? 'badge-brand' : 'badge-neutral']">
                                        {{ item.is_published ? 'منشور' : 'مسودة' }}
                                    </span>
                                    <span v-if="item.is_new" class="badge badge-accent text-xs">
                                        جديد
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <div class="text-secondary dark:text-tertiary">النوع</div>
                                    <div class="text-primary font-semibold">{{ getTypeLabel(item.type) }}</div>
                                </div>
                                <div>
                                    <div class="text-secondary dark:text-tertiary">التصنيف</div>
                                    <div class="text-primary font-semibold">{{ locale === 'ar' ? item.category?.name_ar : item.category?.name_en }}</div>
                                </div>
                                <div>
                                    <div class="text-secondary dark:text-tertiary">المشاهدات</div>
                                    <div class="text-primary font-semibold flex items-center gap-1">
                                        <EyeIcon class="h-3 w-3 text-secondary dark:text-tertiary" />
                                        {{ item.views }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-secondary dark:text-tertiary">التنزيلات</div>
                                    <div class="text-primary font-semibold flex items-center gap-1">
                                        <ArrowDownTrayIcon class="h-3 w-3 text-secondary dark:text-tertiary" />
                                        {{ item.downloads }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2 pt-2">
                                <Button size="xs" variant="outline" @click="handleEdit(item)" 
                                    class="flex-1 text-xs border-primary hover:border-brand-500 text-primary hover:text-brand-500 dark:border-secondary dark:hover:border-brand-500">
                                    تعديل
                                </Button>
                                <button @click="handleTogglePublish(item)"
                                    class="px-2 text-secondary hover:text-primary rounded-lg border border-primary hover:border-brand-500 transition-all duration-200 dark:text-tertiary dark:border-secondary dark:hover:border-brand-500"
                                    :title="item.is_published ? 'إلغاء النشر' : 'نشر'">
                                    <EyeIcon v-if="item.is_published" class="h-4 w-4" />
                                    <EyeSlashIcon v-else class="h-4 w-4" />
                                </button>
                                <Button size="xs" variant="outline" @click="handleDelete(item.id)"
                                    class="flex-1 text-xs text-accent-500 border-accent-500/30 hover:border-accent-500 hover:text-accent-500/90 dark:text-accent-500/90 dark:border-accent-500/40 dark:hover:border-accent-500/70">
                                    حذف
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <table class="min-w-full text-sm hidden sm:table">
                        <thead>
                            <tr class="text-start text-secondary bg-secondary dark:bg-tertiary">
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-start font-semibold min-w-[40px]">#</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-start font-semibold min-w-[150px]">العنوان</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-start font-semibold min-w-[80px]">النوع</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-start font-semibold min-w-[100px]">التصنيف</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-start font-semibold min-w-[80px]">المشاهدات</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-start font-semibold min-w-[80px]">التنزيلات</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-start font-semibold min-w-[80px]">الحالة</th>
                                <th class="px-3 sm:px-4 py-2 sm:py-3 text-start font-semibold min-w-[130px]">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="item.id"
                                class="border-t border-primary hover:bg-secondary transition-all duration-200 dark:border-secondary dark:hover:bg-tertiary">
                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-primary font-semibold text-center">
                                    {{ index + 1 }}
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-primary">
                                    <div class="flex items-center gap-2 sm:gap-3">
                                        <img v-if="item.cover_image" :src="item.cover_image" :alt="item.title_ar"
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg object-cover border border-primary dark:border-secondary">
                                        <div v-else
                                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-tertiary dark:bg-secondary flex items-center justify-center border border-primary dark:border-secondary">
                                            <DocumentTextIcon class="h-4 sm:h-5 w-4 sm:w-5 text-tertiary dark:text-secondary" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-primary text-sm sm:text-base">{{ item.title_ar }}</span>
                                            <span class="text-xs text-secondary dark:text-tertiary">{{ item.author_ar }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-primary">
                                    <span class="badge badge-neutral text-xs sm:text-sm border border-primary dark:border-secondary">
                                        {{ getTypeLabel(item.type) }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-primary">
                                    <span class="badge badge-brand text-xs sm:text-sm">
                                        {{ locale === 'ar' ? item.category?.name_ar : item.category?.name_en }}
                                    </span>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-primary">
                                    <div class="flex items-center gap-1">
                                        <EyeIcon class="h-3 sm:h-4 w-3 sm:w-4 text-secondary dark:text-tertiary" />
                                        {{ item.views }}
                                    </div>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3 text-primary">
                                    <div class="flex items-center gap-1">
                                        <ArrowDownTrayIcon class="h-3 sm:h-4 w-3 sm:w-4 text-secondary dark:text-tertiary" />
                                        {{ item.downloads }}
                                    </div>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <span :class="['badge text-xs sm:text-sm', item.is_published ? 'badge-brand' : 'badge-neutral']">
                                            {{ item.is_published ? 'منشور' : 'مسودة' }}
                                        </span>
                                        <span v-if="item.is_new" class="badge badge-accent text-xs sm:text-sm">
                                            جديد
                                        </span>
                                    </div>
                                </td>

                                <td class="px-3 sm:px-4 py-2 sm:py-3">
                                    <div class="flex gap-1 sm:gap-2">
                                        <Button size="xs sm:sm" variant="outline" @click="handleEdit(item)" 
                                            class="text-xs border-primary hover:border-brand-500 text-primary hover:text-brand-500 dark:border-secondary dark:hover:border-brand-500">
                                            تعديل
                                        </Button>
                                        <button @click="handleTogglePublish(item)"
                                            class="p-1 sm:p-2 text-secondary hover:text-primary transition-colors duration-200 dark:text-tertiary dark:hover:text-primary"
                                            :title="item.is_published ? 'إلغاء النشر' : 'نشر'">
                                            <EyeIcon v-if="item.is_published" class="h-3 sm:h-4 w-3 sm:w-4" />
                                            <EyeSlashIcon v-else class="h-3 sm:h-4 w-3 sm:w-4" />
                                        </button>
                                        <Button size="xs sm:sm" variant="outline" @click="handleDelete(item.id)"
                                            class="text-xs text-accent-500 border-accent-500/30 hover:border-accent-500 hover:text-accent-500/90 dark:text-accent-500/90 dark:border-accent-500/40 dark:hover:border-accent-500/70">
                                            حذف
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="!loading && items.length === 0" class="text-center py-6 sm:py-8 text-secondary dark:text-tertiary">
                <FolderOpenIcon class="h-12 w-12 sm:h-16 sm:w-16 mx-auto mb-3 sm:mb-4 text-tertiary dark:text-secondary" />
                <h3 class="text-base sm:text-lg font-semibold text-primary mb-1 sm:mb-2">لا توجد محتويات</h3>
                <p class="text-secondary dark:text-tertiary mb-3 sm:mb-4 text-sm sm:text-base">لم يتم إضافة أي محتوى للمكتبة بعد</p>
                <Button @click="upload" variant="outline" size="sm sm:default" class="border-primary hover:border-brand-500 text-primary hover:text-brand-500 dark:border-secondary dark:hover:border-brand-500">
                    إضافة محتوى جديد
                </Button>
            </div>
        </Card>

        <!-- Content Layout Toggle (for non-admin users) -->
        <div v-else class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="text-sm sm:text-base text-secondary dark:text-tertiary">
                {{ t('library.showing') }} {{ filteredItems.length }} {{ t('library.of') }} {{ totalItems }}
            </div>
            <div class="flex items-center gap-2">
                <button @click="viewMode = 'grid'" :class="[
                    'p-2 rounded-lg transition-all duration-200',
                    viewMode === 'grid' 
                        ? 'bg-brand-500 text-white shadow-soft' 
                        : 'bg-tertiary dark:bg-secondary border border-primary dark:border-secondary text-primary hover:border-brand-500 hover:text-brand-500'
                ]">
                    <Squares2X2Icon class="h-4 sm:h-5 w-4 sm:w-5" />
                </button>
                <button @click="viewMode = 'list'" :class="[
                    'p-2 rounded-lg transition-all duration-200',
                    viewMode === 'list' 
                        ? 'bg-brand-500 text-white shadow-soft' 
                        : 'bg-tertiary dark:bg-secondary border border-primary dark:border-secondary text-primary hover:border-brand-500 hover:text-brand-500'
                ]">
                    <Bars3Icon class="h-4 sm:h-5 w-4 sm:w-5" />
                </button>
            </div>
        </div>

        <!-- Library Content (for non-admin users) -->
        <div v-if="!hasAuth">
            <div v-if="loading" class="flex justify-center py-8 sm:py-12">
                <div class="animate-spin rounded-full h-8 sm:h-12 w-8 sm:w-12 border-b-2 border-brand-500 dark:border-brand-500/70"></div>
            </div>

            <div v-else-if="error" class="bg-accent-500/10 dark:bg-accent-500/20 border border-accent-500/30 dark:border-accent-500/40 text-accent-500 dark:text-accent-500/90 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-sm sm:text-base">
                {{ error }}
            </div>

            <div v-else-if="filteredItems.length > 0">
                <!-- Grid View -->
                <div v-if="viewMode === 'grid'"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                    <LibraryCard v-for="item in filteredItems" :key="item.id" :item="item" :locale="locale"
                        @download="handleDownload" @view="handleView" />
                </div>

                <!-- List View -->
                <div v-else class="space-y-3 sm:space-y-4">
                    <LibraryListItem v-for="item in filteredItems" :key="item.id" :item="item" :locale="locale"
                        @download="handleDownload" @view="handleView" />
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-8 sm:py-12">
                <FolderOpenIcon class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-tertiary dark:text-secondary" />
                <h3 class="mt-3 sm:mt-4 text-base sm:text-lg font-semibold text-primary">{{ t('library.no_results') }}</h3>
                <p class="mt-1 sm:mt-2 text-secondary dark:text-tertiary text-sm sm:text-base">{{ t('library.no_results_desc') }}</p>
                <Button variant="primary" class="mt-3 sm:mt-4 text-sm sm:text-base shadow-soft hover:shadow-md transition-all duration-200" @click="clearFilters">
                    {{ t('library.clear_filters') }}
                </Button>
            </div>
        </div>

        <!-- Upload Modal -->
        <UploadModal v-if="showUploadModal" :categories="categories" :editing-item="editingItem"
            @close="showUploadModal = false" @uploaded="handleUpload" />

        <!-- Delete Confirmation Modal -->
        <DeleteConfirmModal :show="showDeleteConfirm"
            message="هل أنت متأكد من رغبتك في حذف هذا الكتاب؟ لا يمكن التراجع عن هذا الإجراء." @confirm="confirmDelete"
            @cancel="showDeleteConfirm = false" />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/auth';
import { useLibraryStore, type LibraryItem } from '@/stores/library';
import { useToast } from '@/composables/useToast';
import Button from '@/components/dashboard/component/ui/Button.vue';
import Card from '@/components/dashboard/component/ui/Card.vue';
import LibraryCard from './LibraryCard.vue';
import LibraryListItem from './LibraryListItem.vue';
import UploadModal from './UploadModal.vue';
import DeleteConfirmModal from '../../../components/dashboard/events/DeleteConfirmModal.vue'
import {
    ArrowDownTrayIcon,
    MagnifyingGlassIcon,
    BarsArrowUpIcon,
    XMarkIcon,
    Squares2X2Icon,
    Bars3Icon,
    FolderOpenIcon,
    CloudArrowUpIcon,
    DocumentTextIcon,
    EyeIcon,
    EyeSlashIcon
} from '@heroicons/vue/24/outline';

const { t, locale } = useI18n();
const authStore = useAuthStore();
const libraryStore = useLibraryStore();
const toast = useToast();

// Data
const searchQuery = ref('');
const selectedCategory = ref('');
const selectedType = ref('');
const sortBy = ref('newest');
const viewMode = ref<'grid' | 'list'>('grid');
const showUploadModal = ref(false);
const showDeleteConfirm = ref(false);
const editingItem = ref<LibraryItem | null>(null);
const deleteTargetId = ref<number | null>(null);

// Computed
const hasAuth = computed(() => authStore.isAuthenticated);
const loading = computed(() => libraryStore.loading);
const error = computed(() => libraryStore.error);
const items = computed(() => libraryStore.items);
const categories = computed(() => libraryStore.categories);

const filteredItems = computed(() => {
    let filtered = [...items.value];

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(item =>
            item.title_ar.toLowerCase().includes(query) ||
            item.title_en.toLowerCase().includes(query) ||
            item.description_ar?.toLowerCase().includes(query) ||
            item.description_en?.toLowerCase().includes(query)
        );
    }

    if (selectedCategory.value) {
        filtered = filtered.filter(item => item.category_id.toString() === selectedCategory.value);
    }

    if (selectedType.value) {
        filtered = filtered.filter(item => item.type === selectedType.value);
    }

    switch (sortBy.value) {
        case 'newest':
            filtered.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
            break;
        case 'popular':
            filtered.sort((a, b) => b.downloads - a.downloads);
            break;
        case 'title':
            filtered.sort((a, b) => a.title_en.localeCompare(b.title_en));
            break;
    }

    return filtered;
});

const totalItems = computed(() => items.value.length);

const activeFilters = computed(() => {
    const filters = [];
    if (selectedCategory.value) {
        const category = categories.value.find(cat => cat.id.toString() === selectedCategory.value);
        if (category) {
            filters.push({
                key: 'category',
                label: locale.value === 'ar' ? category.name_ar : category.name_en
            });
        }
    }
    if (selectedType.value) {
        filters.push({
            key: 'type',
            label: t(`library.types.${selectedType.value}`)
        });
    }
    return filters;
});

const sortOptions = [
    { value: 'newest', label: t('library.sort.newest') },
    { value: 'popular', label: t('library.sort.popular') },
    { value: 'title', label: t('library.sort.title') },
];

// Methods
const fetchItems = async () => {
    const params: any = {};

    if (selectedCategory.value) params.category_id = selectedCategory.value;
    if (selectedType.value) params.type = selectedType.value;
    if (searchQuery.value) params.search = searchQuery.value;

    await libraryStore.fetchItems(params);
};

const fetchCategories = async () => {
    await libraryStore.fetchCategories();
};

const upload = () => {
    editingItem.value = null;
    showUploadModal.value = true;
};

const handleEdit = (item: LibraryItem) => {
    editingItem.value = item;
    showUploadModal.value = true;
};

const handleDelete = async (itemId: number) => {
    deleteTargetId.value = itemId;
    showDeleteConfirm.value = true;
};

const confirmDelete = async () => {
    if (!deleteTargetId.value) return;

    try {
        await libraryStore.deleteItem(deleteTargetId.value);
        toast.success('تم حذف الكتاب بنجاح');
    } catch (err: any) {
        console.error('Failed to delete item:', err);
        toast.handleApiError(err, 'حدث خطأ أثناء حذف الكتاب. يرجى المحاولة مرة أخرى.');
    } finally {
        showDeleteConfirm.value = false;
        deleteTargetId.value = null;
    }
};

const handleTogglePublish = async (item: LibraryItem) => {
    try {
        const formData = new FormData();
        formData.append('is_published', item.is_published ? '0' : '1');
        formData.append('_method', 'PUT');

        await libraryStore.updateItem(item.id, formData);

        toast.success(item.is_published ? 'تم إلغاء نشر الكتاب' : 'تم نشر الكتاب بنجاح');
    } catch (err: any) {
        console.error('Failed to toggle publish:', err);
        toast.handleApiError(err, 'حدث خطأ أثناء تحديث حالة الكتاب. يرجى المحاولة مرة أخرى.');
    }
};

const getTypeLabel = (type: string) => {
    const types: { [key: string]: string } = {
        book: 'كتاب',
        research: 'بحث',
        guide: 'دليل',
        article: 'مقال'
    };
    return types[type] || type;
};

const toggleSort = () => {
    const currentIndex = sortOptions.findIndex(opt => opt.value === sortBy.value);
    const nextIndex = (currentIndex + 1) % sortOptions.length;
    sortBy.value = sortOptions[nextIndex].value;
};

const removeFilter = (filterKey: string) => {
    if (filterKey === 'category') {
        selectedCategory.value = '';
    } else if (filterKey === 'type') {
        selectedType.value = '';
    }
    fetchItems();
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    selectedType.value = '';
    fetchItems();
};

const handleSearch = () => {
    clearTimeout((window as any).searchTimeout);
    (window as any).searchTimeout = setTimeout(() => {
        fetchItems();
    }, 500);
};

const handleDownload = async (item: LibraryItem) => {
    try {
        if (item.file_path) {
            window.open(item.file_path, '_blank');
        }
    } catch (err) {
        console.error('Download failed:', err);
    }
};

const handleView = async (item: LibraryItem) => {
    try {
        await libraryStore.incrementViews(item.id);
    } catch (err) {
        console.error('Failed to view item:', err);
    }
};

const handleUpload = async () => {
    try {
        await fetchItems();
        showUploadModal.value = false;
        editingItem.value = null;
    } catch (err) {
        console.error('Failed to refresh after upload:', err);
    }
};

// Lifecycle
onMounted(async () => {
    await Promise.all([fetchItems(), fetchCategories()]);
});

// Watchers
watch([selectedCategory, selectedType], () => {
    fetchItems();
});
</script>

<style scoped>
/* تحسينات شريط التمرير */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: var(--text-tertiary);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--text-secondary);
}

.dark ::-webkit-scrollbar-thumb {
    background: var(--text-tertiary);
}

.dark ::-webkit-scrollbar-thumb:hover {
    background: var(--text-secondary);
}

/* تحسينات للجوال */
@media (max-width: 640px) {
    .button {
        min-height: 44px;
    }
    
    input, select {
        font-size: 16px;
    }
}

/* Badge Styles */
.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
    font-family: "Ciro", sans-serif;
    transition: all 0.2s ease-in-out;
}

.badge-brand {
    background-color: var(--brand-500);
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
</style>