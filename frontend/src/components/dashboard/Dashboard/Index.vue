<template>
	 <div class="space-y-6 p-6 bg-primary min-h-screen">
		<h1 class="text-2xl font-semibold text-primary">{{ t('nav.dashboard') }}</h1>

		<!-- Top hero + side cards -->
		<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
			<!-- Hero analytics card -->
			<div class="relative overflow-hidden rounded-2xl border border-primary bg-brand-500 p-6 text-white shadow lg:col-span-2">
				<div v-if="!loadingMetrics">
					<div class="flex items-start justify-between">
						<div>
							<div class="text-lg font-semibold">ملخص المستخدمين</div>
							<div class="mt-1 text-sm/6 text-white/80">آخر تحديث {{ formatDateTime(lastUpdated) }}</div>
						</div>
						<div class="flex gap-2">
							<span class="h-2 w-2 rounded-full bg-white/60"></span>
							<span class="h-2 w-2 rounded-full bg-white/60"></span>
							<span class="h-2 w-2 rounded-full bg-white"></span>
						</div>
					</div>
					<div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
						<div v-for="metric in heroMetrics" :key="metric.label">
							<div class="text-sm/6 text-white/80">{{ metric.label }}</div>
							<div class="mt-1 text-3xl font-semibold">{{ formatNumber(metric.value) }}</div>
						</div>
					</div>
				</div>
				<div v-else class="h-36 animate-pulse rounded-2xl bg-white/20"></div>
			</div>

			<!-- Appointments statistics -->
			<Card>
				<template #header>إحصائيات المواعيد</template>
				<div class="grid grid-cols-2 gap-3 text-sm">
					<div>
						<div class="text-secondary">قيد التنفيذ</div>
						<div class="mt-1 text-xl font-semibold text-brand-500">{{ formatNumber(upcomingAppointmentsCount) }}</div>
					</div>
					<div>
						<div class="text-secondary">مكتملة</div>
						<div class="mt-1 text-xl font-semibold text-primary">{{ formatNumber(completedAppointmentsCount) }}</div>
					</div>
					<div>
						<div class="text-secondary">ملغاة</div>
						<div class="mt-1 text-xl font-semibold text-accent-500">{{ formatNumber(cancelledAppointmentsCount) }}</div>
					</div>
					<div>
						<div class="text-secondary">إجمالي المواعيد</div>
						<div class="mt-1 text-xl font-semibold text-primary">{{ formatNumber(totalAppointmentsCount) }}</div>
					</div>
				</div>
			</Card>
		</div>

		<!-- KPIs row -->
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<StatCard :label="t('dashboard.kpi_upcoming')" :value="formatNumber(upcomingAppointmentsCount)" delta="" :positive="true">
				<template #icon><CalendarIcon class="h-5 w-5 text-brand-500"/></template>
			</StatCard>
			<StatCard :label="t('dashboard.kpi_clients')" :value="formatNumber(userStatsState.totalClients)" delta="" :positive="true">
				<template #icon><UsersIcon class="h-5 w-5 text-brand-500"/></template>
			</StatCard>
			<StatCard :label="t('dashboard.kpi_new_users')" :value="formatNumber(userStatsState.newUsers)" :delta="''" :positive="true">
				<template #icon><UserPlusIcon class="h-5 w-5 text-accent-500"/></template>
			</StatCard>
			<StatCard label="المعالجون" :value="formatNumber(userStatsState.totalTherapists)" delta="" :positive="true">
				<template #icon><ChartBarIcon class="h-5 w-5 text-brand-500"/></template>
			</StatCard>
		</div>

		<!-- Second row: Upcoming + Quick actions -->
		<div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
			<Card class="xl:col-span-2">
				<template #header>المواعيد القادمة</template>
				<ul class="space-y-2">
					<li 
						v-for="item in upcomingAppointmentsList" 
						:key="item.id" 
						class="flex items-center justify-between rounded-md border border-primary p-3"
					>
						<div class="font-medium text-primary">{{ item.clientName }}</div>
						<div class="text-sm text-secondary">{{ item.dateLabel }}</div>
					</li>
					<li v-if="!upcomingAppointmentsList.length" class="text-sm text-secondary">
						لا توجد مواعيد قادمة حالياً.
					</li>
				</ul>
			</Card>
			<Card>
				<template #header>{{ t('dashboard.quick_actions') }}</template>
				<div class="flex flex-col gap-2">
					<Button variant="primary" class="hover:bg-brand-600" @click="router.push({ name: 'admins' })">
						إضافة مشرف
					</Button>
					<Button variant="secondary" class="hover:bg-primary/20" @click="router.push({ name: 'articles' })">
						إضافة مقالة
					</Button>
					<Button variant="outline" class="hover:bg-brand-500/10 hover:text-brand-600 focus-visible:ring-brand-500 text-primary border-brand-200" @click="router.push({ name: 'therapists' })">
						إضافة معالج
					</Button>
				</div>
			</Card>
		</div>
	</div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import Card from '@/components/dashboard/component/ui/Card.vue';
import StatCard from '@/components/dashboard/component/ui/StatCard.vue';
	import Button from '@/components/dashboard/component/ui/Button.vue';
import { CalendarIcon, UsersIcon, UserPlusIcon, ChartBarIcon } from '@heroicons/vue/24/outline';
import { useAppointmentStore } from '@/stores/appointments';
import api from '@/utils/api';
import router from '@/routes/index.js';

const { t, locale } = useI18n();
const appointments = useAppointmentStore();

const loadingMetrics = ref(true);
const dashboardStats = ref({
	total_users: 0,
	total_clients: 0,
	total_therapists: 0,
	total_admins: 0,
	total_articles: 0,
	total_events: 0,
	upcoming_appointments: 0,
	total_appointments: 0,
	completed_appointments: 0,
	cancelled_appointments: 0,
	new_users_this_month: 0
});
const lastUpdated = ref<string>('');

const localeCode = computed(() => (locale.value === 'ar' ? 'ar-SA' : 'en-US'));

const formatNumber = (value: number) => new Intl.NumberFormat(localeCode.value).format(value);
const formatDateTime = (value?: string | null) => {
	if (!value) return '-';
	const date = new Date(value);
	if (Number.isNaN(date.getTime())) return '-';
	return date.toLocaleString(localeCode.value, { dateStyle: 'medium', timeStyle: 'short' });
};

const statsState = computed(() => dashboardStats.value);

const userStatsState = computed(() => ({
	totalUsers: statsState.value.total_users ?? 0,
	totalClients: statsState.value.total_clients ?? 0,
	totalTherapists: statsState.value.total_therapists ?? 0,
	totalAdmins: statsState.value.total_admins ?? 0,
	newUsers: statsState.value.new_users_this_month ?? 0
}));

const heroMetrics = computed(() => ([
	{ label: 'إجمالي المستخدمين', value: statsState.value.total_users },
	{ label: 'العملاء', value: statsState.value.total_clients },
	{ label: 'المعالجون', value: statsState.value.total_therapists },
	{ label: 'المشرفون', value: statsState.value.total_admins }
]));

const upcomingAppointmentsCount = computed(() =>
	statsState.value.upcoming_appointments ?? appointments.items.filter(item => ['pending', 'confirmed'].includes(item.status)).length
);

const completedAppointmentsCount = computed(() =>
	statsState.value.completed_appointments ?? appointments.items.filter(item => item.status === 'completed').length
);

const cancelledAppointmentsCount = computed(() =>
	statsState.value.cancelled_appointments ?? appointments.items.filter(item => item.status === 'cancelled').length
);

const totalAppointmentsCount = computed(() =>
	statsState.value.total_appointments ?? appointments.items.length
);

const upcomingAppointmentsList = computed(() => {
	return [...appointments.items]
		.filter(item => ['pending', 'confirmed'].includes(item.status))
		.sort((a, b) => new Date(a.starts_at).getTime() - new Date(b.starts_at).getTime())
		.slice(0, 6)
		.map(item => ({
			id: item.id,
			clientName: item.client?.name || 'مستخدم',
			dateLabel: formatDateTime(item.starts_at)
		}));
});

const loadDashboardData = async () => {
	loadingMetrics.value = true;
	try {
		const [statsResponse] = await Promise.all([
			api.get('/dashboard/stats'),
			appointments.fetchAll({ per_page: 100 })
		]);

		dashboardStats.value = {
			...dashboardStats.value,
			...statsResponse.data
		};

		lastUpdated.value = new Date().toISOString();
	} catch (error) {
		console.error('Failed to load dashboard data:', error);
	} finally {
		loadingMetrics.value = false;
	}
};

onMounted(() => {
	loadDashboardData();
});
</script>