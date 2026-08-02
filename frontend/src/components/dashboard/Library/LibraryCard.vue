<template>
    <div class="card group hover:shadow-lg transition-all duration-300 cursor-pointer">
        <!-- Cover Image -->
        <div class="relative aspect-[3/4] rounded-lg overflow-hidden bg-tertiary">
            <img 
                v-if="item.cover_image" 
                :src="item.cover_image" 
                :alt="getTitle"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
                <DocumentIcon class="h-12 w-12 text-secondary" />
            </div>
            
            <!-- Badges -->
            <div class="absolute top-2 left-2 flex gap-1">
                <span 
                    v-if="item.is_new"
                    class="px-2 py-1 text-xs rounded-full bg-green-500 text-white"
                >
                    جديد
                </span>
                <span 
                    class="px-2 py-1 text-xs rounded-full text-white capitalize"
                    :style="{ backgroundColor: categoryColor }"
                >
                    {{ getType }}
                </span>
            </div>
            
            <!-- Quick Actions -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                <div class="flex gap-2">
                    <button 
                        @click.stop="$emit('download', item)"
                        class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-100"
                    >
                        <ArrowDownTrayIcon class="h-5 w-5" />
                    </button>
                    <button 
                        @click.stop="$emit('view', item)"
                        class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-100"
                    >
                        <EyeIcon class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="p-4">
            <h3 class="font-semibold text-primary line-clamp-2 mb-2">{{ getTitle }}</h3>
            <p class="text-sm text-secondary line-clamp-2 mb-3">{{ getDescription }}</p>
            
            <div class="flex items-center justify-between text-xs text-tertiary">
                <span>{{ item.author }}</span>
                <span>{{ item.pages }} صفحة</span>
            </div>
            
            <!-- Rating and Downloads -->
            <div class="flex items-center justify-between mt-3">
                <div class="flex items-center gap-1">
                    <StarIcon class="h-4 w-4 text-yellow-500" />
                    <span class="text-sm">{{ item.rating }}</span>
                    <span class="text-xs text-tertiary">({{ item.rating_count }})</span>
                </div>
                <div class="flex items-center gap-1 text-xs text-tertiary">
                    <ArrowDownTrayIcon class="h-3 w-3" />
                    {{ item.downloads }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { DocumentIcon, ArrowDownTrayIcon, EyeIcon, StarIcon } from '@heroicons/vue/24/outline';
import { computed } from 'vue'; 
interface Props {
    item: any;
    locale: string;
}

const props = defineProps<Props>();
const emit = defineEmits(['download', 'view']);
const getAuthor = computed(() => props.locale === 'ar' ? props.item.author_ar : props.item.author_en);
const getTitle = computed(() => props.locale === 'ar' ? props.item.title_ar : props.item.title_en);
const getDescription = computed(() => props.locale === 'ar' ? props.item.description_ar : props.item.description_en);
const getType = computed(() => {
    const types: any = {
        book: 'كتاب',
        research: 'بحث',
        guide: 'دليل',
        article: 'مقال'
    };
    return types[props.item.type] || props.item.type;
});

const categoryColor = computed(() => {
    // يمكنك ربط الألوان بالتصنيفات الفعلية
    const categoryColors: { [key: number]: string } = {
        1: '#8FAE2F', // therapy
        2: '#C28E86', // assessment  
        3: '#4F86C6', // research
    };
    return categoryColors[props.item.category_id] || '#8FAE2F';
});
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>