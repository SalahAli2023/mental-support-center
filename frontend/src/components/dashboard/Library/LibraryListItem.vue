<template>
    <div class="card hover:shadow-lg transition-all duration-300">
        <div class="flex gap-4">
            <!-- Thumbnail -->
            <div class="w-20 h-24 rounded-lg bg-tertiary overflow-hidden flex-shrink-0">
                <img 
                    v-if="item.cover_image" 
                    :src="item.cover_image" 
                    :alt="getTitle"
                    class="w-full h-full object-cover"
                />
                <div v-else class="w-full h-full flex items-center justify-center">
                    <DocumentIcon class="h-8 w-8 text-secondary" />
                </div>
            </div>
            
            <!-- Content -->
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h3 class="font-semibold text-primary truncate">{{ getTitle }}</h3>
                        <p class="text-sm text-secondary mt-1 line-clamp-2">{{ getDescription }}</p>
                        
                        <div class="flex items-center gap-4 mt-2 text-xs text-tertiary">
                            <span>{{ getAuthor }}</span>
                            <span>{{ item.pages }} صفحة</span>
                            <span>{{ item.file_size }}</span>
                            <span>{{ formatDate(item.publish_date) }}</span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <Button 
                            size="sm" 
                            variant="outline"
                            @click="$emit('view', item)"
                        >
                            معاينة
                        </Button>
                        <Button 
                            size="sm" 
                            variant="primary"
                            @click="$emit('download', item)"
                            class="flex items-center gap-1"
                        >
                            <ArrowDownTrayIcon class="h-4 w-4" />
                            تحميل
                        </Button>
                    </div>
                </div>
                
                <!-- Metadata -->
                <div class="flex items-center justify-between mt-3">
                    <div class="flex items-center gap-4">
                        <span 
                            class="px-2 py-1 text-xs rounded-full text-white capitalize"
                            :style="{ backgroundColor: categoryColor }"
                        >
                            {{ getType }}
                        </span>
                        
                        <div class="flex items-center gap-1 text-xs text-tertiary">
                            <ArrowDownTrayIcon class="h-3 w-3" />
                            {{ item.downloads }} تحميل
                        </div>
                        
                        <div class="flex items-center gap-1 text-xs text-tertiary">
                            <EyeIcon class="h-3 w-3" />
                            {{ item.views }} مشاهدة
                        </div>
                    </div>
                    
                    <div v-if="item.is_new" class="px-2 py-1 text-xs rounded-full bg-green-500 text-white">
                        جديد
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Button from '@/components/dashboard/component/ui/Button.vue';
import { DocumentIcon, ArrowDownTrayIcon, EyeIcon, StarIcon } from '@heroicons/vue/24/outline';

interface Props {
    item: any;
    locale: string;
}

const props = defineProps<Props>();
const emit = defineEmits(['download', 'view']);

const getTitle = computed(() => props.locale === 'ar' ? props.item.title_ar : props.item.title_en);
const getDescription = computed(() => props.locale === 'ar' ? props.item.description_ar : props.item.description_en);
const getAuthor = computed(() => props.locale === 'ar' ? props.item.author_ar : props.item.author_en);
const getType = computed(() => {
    const types: any = {
        book: 'كتاب',
        research: 'بحث',
        guide: 'دليل',
        article: 'مقال'
    };
    return types[props.item.type] || props.item.type;
});

const categoryColor = computed(() => '#8FAE2F');

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString(props.locale === 'ar' ? 'ar-SA' : 'en-US');
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>