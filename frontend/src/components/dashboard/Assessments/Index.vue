<template>
	<div class="space-y-6 p-4 sm:p-6 bg-primary min-h-screen">
		<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
			<div>
				<h1 class="text-2xl font-semibold text-primary">نتائج المقاييس</h1>
				<p class="text-secondary text-sm mt-1">عرض جميع نتائج المقاييس النفسية للمستخدمين</p>
			</div>
		</div>

		<!-- حالة التحميل -->
		<div v-if="loading" class="text-center py-12">
			<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-500 mx-auto"></div>
			<p class="text-secondary mt-4">جاري تحميل البيانات...</p>
		</div>

		<!-- رسالة الخطأ -->
		<div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
			<div class="flex items-center gap-2 text-red-700">
				<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
					<path fill-rule="evenodd"
						d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
						clip-rule="evenodd"></path>
				</svg>
				<span>{{ error }}</span>
			</div>
			<button @click="fetchAssessments" class="mt-2 text-sm text-red-600 hover:text-red-800">
				إعادة المحاولة
			</button>
		</div>

		<!-- جدول النتائج -->
		<div v-else-if="assessments.length > 0" class="bg-primary border border-primary rounded-lg overflow-hidden">
			<div class="overflow-x-auto">
				<table class="w-full">
					<thead class="bg-tertiary border-b border-primary">
						<tr>
							<th class="px-4 py-3 text-right text-sm font-semibold text-primary">المقياس
							</th>
							<th class="px-4 py-3 text-right text-sm font-semibold text-primary">المستخدم
							</th>
							<th class="px-4 py-3 text-right text-sm font-semibold text-primary">النتيجة
							</th>
							<th class="px-4 py-3 text-right text-sm font-semibold text-primary">التاريخ
							</th>
							<th class="px-4 py-3 text-right text-sm font-semibold text-primary">الإجراءات
							</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-primary">
						<tr v-for="assessment in assessments" :key="assessment.id"
							class="hover:bg-tertiary transition-colors">
							<td class="px-4 py-3 text-sm text-primary">
								{{ assessment.psychological_scale?.name_ar || 'غير محدد' }}
							</td>
							<td class="px-4 py-3 text-sm text-primary">
								{{ assessment.user?.name || 'مستخدم غير مسجل' }}
							</td>
							<td class="px-4 py-3 text-sm text-primary">
								<span class="font-semibold">{{ assessment.total_score }}</span>
								<span class="text-secondary text-xs"> / {{
									assessment.psychological_scale?.max_score || 'N/A' }}</span>
							</td>
							<td class="px-4 py-3 text-sm text-secondary">
								{{ formatDate(assessment.completed_at || assessment.created_at || '') }}
							</td>
							<td class="px-4 py-3">
								<button @click.stop="viewDetails(assessment)"
									class="text-brand-500 hover:text-brand-600 text-lg"
									title="عرض التفاصيل">
									<i class="fas fa-eye"></i>
								</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<!-- حالة عدم وجود نتائج -->
		<div v-else class="text-center py-12 bg-primary border border-primary rounded-lg">
			<svg class="w-16 h-16 text-secondary mx-auto mb-4" fill="none" stroke="currentColor"
				viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
				</path>
			</svg>
			<p class="text-secondary text-lg font-medium">لا توجد نتائج متاحة</p>
			<p class="text-secondary text-sm mt-2">لم يتم إجراء أي مقاييس بعد</p>
		</div>

		<!-- مودال تفاصيل النتيجة -->
		<div v-if="showDetails && selectedAssessment"
			class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
			<div class="bg-primary border border-primary rounded-xl shadow-xl max-w-lg w-full mx-4 p-6 relative">
				<button class="absolute top-3 left-3 text-secondary hover:text-brand-500" @click="closeDetails">
					<i class="fas fa-times"></i>
				</button>

				<h2 class="text-xl font-semibold text-primary mb-4">
					تفاصيل نتيجة المقياس
				</h2>

				<div class="space-y-3 text-sm text-primary">
					<div class="flex justify-between gap-4">
						<span class="text-secondary">المقياس:</span>
						<span class="font-medium">
							{{ selectedAssessment.psychological_scale?.name_ar || 'غير محدد' }}
						</span>
					</div>

					<div class="flex justify-between gap-4">
						<span class="text-secondary">المستخدم:</span>
						<span class="font-medium">
							{{ selectedAssessment.user?.name || 'مستخدم غير مسجل' }}
						</span>
					</div>

					<div class="flex justify-between gap-4">
						<span class="text-secondary">النتيجة:</span>
						<span class="font-medium">
							{{ selectedAssessment.total_score }}
							<span class="text-secondary text-xs">
								/ {{ selectedAssessment.psychological_scale?.max_score || 'N/A' }}
							</span>
						</span>
					</div>

					<div class="flex justify-between gap-4">
						<span class="text-secondary">التاريخ:</span>
						<span class="font-medium">
							{{ formatDate(selectedAssessment.completed_at || selectedAssessment.created_at
								|| '') }}
						</span>
					</div>

					<div v-if="selectedAssessment.interpretation_level" class="mt-2">
						<span class="text-secondary block mb-1">مستوى التفسير:</span>
						<span
							class="inline-flex items-center px-3 py-1 rounded-full bg-tertiary text-xs font-semibold">
							{{ selectedAssessment.interpretation_level }}
						</span>
					</div>
				</div>

				<div class="mt-6 flex justify-end">
					<button class="px-4 py-2 rounded-lg bg-brand-500 text-white text-sm hover:bg-brand-600"
						@click="closeDetails">
						إغلاق
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
// @ts-ignore
import api from '@/utils/api';
// @ts-ignore
import { useToast } from '@/composables/useToast';

const toast = useToast();

interface Assessment {
	id: string;
	total_score: number;
	completed_at: string;
	created_at: string;
	interpretation_level?: string;
	psychological_scale?: {
		id: string;
		name_ar: string;
		max_score: number;
	};
	user?: {
		id: string;
		name: string;
		email: string;
	};
}

const assessments = ref<Assessment[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
const selectedAssessment = ref<Assessment | null>(null);
const showDetails = ref(false);

const fetchAssessments = async () => {
	loading.value = true;
	error.value = null;

	try {
		const response = await api.get('/assessments');
		console.log('📊 API Response:', response.data);
		console.log('📊 Assessments data:', response.data.data);

		// التحقق من البنية المختلفة للاستجابة
		const data = response.data?.data || response.data || [];
		console.log('📊 Processed data:', data);

		assessments.value = Array.isArray(data) ? data : [];

		if (assessments.value.length === 0) {
			console.warn('⚠️ No assessments found in response');
		}
	} catch (err: any) {
		console.error('❌ Error fetching assessments:', err);
		console.error('❌ Error response:', err.response?.data);
		error.value = 'فشل في تحميل النتائج';
		toast.handleApiError(err, 'حدث خطأ أثناء تحميل نتائج المقاييس');
	} finally {
		loading.value = false;
	}
};

const formatDate = (dateString: string) => {
	if (!dateString) return 'غير محدد';
	const date = new Date(dateString);
	return new Intl.DateTimeFormat('ar-SA', {
		year: 'numeric',
		month: 'long',
		day: 'numeric',
		hour: '2-digit',
		minute: '2-digit'
	}).format(date);
};

const viewDetails = (assessment: Assessment) => {
	selectedAssessment.value = assessment;
	showDetails.value = true;
};

const closeDetails = () => {
	showDetails.value = false;
	selectedAssessment.value = null;
};

onMounted(() => {
	fetchAssessments();
});
</script>
