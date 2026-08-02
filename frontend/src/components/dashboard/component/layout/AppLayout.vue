<template>
	<div class="min-h-screen bg-primary flex" :dir="direction">
		<!-- Floating settings button -->
		<RouterLink
			:to="{ name: 'settings' }"
			class="fixed bottom-4 z-50 inline-grid h-11 w-11 place-items-center rounded-full bg-brand-500 text-white shadow-lg hover:bg-[#8FAE2F] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 transition-all duration-200"
			:class="direction === 'rtl' ? 'end-3' : 'start-3'"
			aria-label="فتح الإعدادات | Open Settings"
		>
			<Cog6ToothIcon class="h-6 w-6" />
		</RouterLink>

		<!-- Desktop Sidebar - FIXED -->
		<aside :class="[
			'hidden md:flex shrink-0 flex-col border-r border-primary transition-all duration-300 bg-secondary fixed h-screen z-40',
			collapsed ? 'w-20' : 'w-72',
			direction === 'rtl' ? 'right-0' : 'left-0'
		]">
			<!-- Logo -->
			<div class="flex h-16 items-center justify-center px-4">
				<img src="@/assets/images/dashboard/TqUYX8k9ugYomJilTLVf.png" alt="لوحة التحكم | Dashboard" :class="[collapsed ? 'w-10' : 'w-44', 'h-auto']" />
			</div>
			
			<!-- Navigation - Scrollable -->
			<div class="flex-1 overflow-y-auto px-2 py-2">
				<nav class="space-y-1">
					<!-- Dashboard -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'لوحة التحكم' : 'Dashboard'" 
						icon="home" 
						:show-label="!collapsed"
						:to-name="'Dashboard'"
						:collapsed="collapsed"
						:direction="direction"
					/>
					
					<!-- Appointments -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'المواعيد' : 'Appointments'" 
						icon="calendar" 
						:show-label="!collapsed"
						:items="appointmentItems"
						:collapsed="collapsed"
						:direction="direction"
					/>
					
					<!-- Sessions -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'الجلسات' : 'Sessions'" 
						icon="video" 
						:show-label="!collapsed"
						:items="sessionItems"
						:collapsed="collapsed"
						:direction="direction"
					/>
					
					<!-- Users -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'المستخدمين' : 'Users'" 
						icon="users" 
						:show-label="!collapsed"
						:items="userItems"
						:collapsed="collapsed"
						:direction="direction"
					/>
					
					<!-- Articles -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'المقالات' : 'Articles'" 
						icon="document" 
						:show-label="!collapsed"
						:items="articleItems"
						:collapsed="collapsed"
						:direction="direction"
					/>
					
					<!-- Library -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'المكتبة' : 'Library'" 
						icon="folder" 
						:show-label="!collapsed"
						:items="libraryItems"
						:collapsed="collapsed"
						:direction="direction"
					/>

					<!-- Site Statistics -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'إحصائيات الموقع' : 'Site Statistics'" 
						icon="chart" 
						:show-label="!collapsed"
						:items="siteStatsItems"
						:collapsed="collapsed"
						:direction="direction"
					/>

					<!-- User Messages -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'رسائل المستخدمين' : 'User Messages'" 
						icon="inbox" 
						:show-label="!collapsed"
						:items="userMessageItems"
						:collapsed="collapsed"
						:direction="direction"
					/>

					<!-- Events -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'الأحداث' : 'Events'" 
						icon="calendar" 
						:show-label="!collapsed"
						:items="eventItems"
						:collapsed="collapsed"
						:direction="direction"
					/>

					<!-- Psychological Programs -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'البرامج النفسية' : 'Psychological Programs'" 
						icon="calendar-days" 
						:show-label="!collapsed"
						:items="programItems"
						:collapsed="collapsed"
						:direction="direction"
					/>

					<!-- Legal Resources -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'الموارد القانونية' : 'Legal Resources'" 
						icon="scale" 
						:show-label="!collapsed"
						:items="legalResourceItems"
						:collapsed="collapsed"
						:direction="direction"
					/>
					
					<!-- Psychological Assessments -->
					<NavItem 
						:label="currentLocale === 'ar' ? 'المقاييس النفسية' : 'Psychological Assessments'" 
						icon="chart" 
						:show-label="!collapsed"
						:items="assessmentItems"
						:collapsed="collapsed"
						:direction="direction"
					/>
					
					<button
						@click="handleLogout"
						class="mt-4 flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-primary transition-colors duration-200 hover:bg-tertiary"
						:class="direction === 'rtl' ? 'flex-row' : 'flex-row-reverse'"
					>
						<ArrowRightOnRectangleIcon class="h-5 w-5" />
						<span v-if="!collapsed">{{ currentLocale === 'ar' ? 'تسجيل الخروج' : 'Logout' }}</span>
					</button>
				</nav>
			</div>
		</aside>

		<!-- Main Content Area -->
		<div class="flex-1 flex flex-col transition-all duration-300 w-full" :style="contentStyle">
			<header class="sticky top-0 z-30 flex items-center justify-between gap-2 border-b border-primary px-3 md:px-4 py-3 backdrop-blur bg-secondary">
				<div class="flex items-center gap-2">
					<button class="inline-grid h-9 w-9 place-items-center rounded-lg text-primary hover:bg-tertiary md:hidden" @click="drawer = true" :aria-label="currentLocale === 'ar' ? 'فتح القائمة' : 'Open Menu'">
						<Bars3Icon class="h-5 w-5" />
					</button>
					<button class="hidden md:inline-grid h-9 w-9 place-items-center rounded-lg text-primary hover:bg-tertiary" @click="toggleSidebar" :aria-label="currentLocale === 'ar' ? 'طي/فتح الشريط الجانبي' : 'Collapse/Expand Sidebar'">
						<ChevronDoubleRightIcon  v-if="(!collapsed && direction === 'rtl') || (collapsed && direction === 'ltr')" class="h-5 w-5" />
						<ChevronDoubleLeftIcon v-else class="h-5 w-5" />
					</button>
					<div class="relative w-full max-w-full md:max-w-2xl">
						<MagnifyingGlassIcon :class="[
							'pointer-events-none absolute inset-y-0 my-auto h-5 w-5 text-tertiary',
							direction === 'rtl' ? 'start-3' : 'end-3'
						]" />
						<input :placeholder="currentLocale === 'ar' ? 'بحث...' : 'Search...'" :class="[
							'w-full rounded-lg border border-transparent bg-tertiary py-2 text-sm text-primary outline-none ring-1 ring-transparent transition focus:bg-primary focus:ring-2 focus:ring-brand-500',
							direction === 'rtl' ? 'ps-10 pe-3' : 'pe-10 ps-3'
						]" />
					</div>
				</div>
				<div class="flex items-center gap-1.5">
					<button class="inline-grid h-9 w-9 place-items-center rounded-lg text-primary hover:bg-tertiary" @click="toggleLocale" :title="currentLocale === 'ar' ? 'English' : 'العربية'">
						<LanguageIcon class="h-5 w-5" />
						<span class="sr-only">{{ currentLocale === 'ar' ? 'English' : 'العربية' }}</span>
					</button>
					<button class="inline-grid h-9 w-9 place-items-center rounded-lg text-primary hover:bg-tertiary" @click="toggleTheme" :title="isDark ? (currentLocale === 'ar' ? 'الوضع الفاتح' : 'Light Mode') : (currentLocale === 'ar' ? 'الوضع الداكن' : 'Dark Mode')">
						<MoonIcon v-if="isDark" class="h-5 w-5" />
						<SunIcon v-else class="h-5 w-5" />
					</button>
					
					<!-- 🔹 قائمة المستخدم المحسّنة -->
					<div class="relative" ref="userMenuRef">
						<button 
							@click="toggleUserMenu" 
							class="inline-flex items-center gap-2 rounded-lg px-2 py-1 transition-colors duration-200 hover:bg-tertiary focus:outline-none focus:ring-2 focus:ring-brand-500"
							:aria-label="currentLocale === 'ar' ? 'قائمة المستخدم' : 'User Menu'"
						>
							<img 
								:src="userAvatar" 
								:alt="currentLocale === 'ar' ? 'صورة المستخدم' : 'User Avatar'" 
								class="h-9 w-9 rounded-full object-cover border-2 border-transparent hover:border-brand-500 transition-all duration-200"
								@error="handleAvatarError"
							/>
							<div class="hidden sm:flex flex-col items-start text-sm">
								<span class="font-medium text-primary leading-tight">{{ userName }}</span>
								<span class="text-xs text-tertiary leading-tight">{{ userRole }}</span>
							</div>
							<ChevronDownIcon class="hidden sm:block h-4 w-4 text-tertiary transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" />
						</button>

						<!-- القائمة المنسدلة المحسّنة -->
						<div 
							v-if="userMenuOpen" 
							ref="userDropdownRef"
							:class="[
								'user-menu absolute mt-2 w-72 overflow-hidden rounded-xl border border-primary bg-secondary shadow-2xl z-50',
								direction === 'rtl' ? 'end-0' : 'start-0'
							]"
						>
							<!-- رأس القائمة -->
							<div class="px-4 pt-4 pb-3 border-b border-primary bg-primary/30">
								<div class="flex items-center gap-3">
									<img 
										:src="userAvatar" 
										:alt="currentLocale === 'ar' ? 'صورة المستخدم' : 'User Avatar'" 
										class="h-14 w-14 rounded-full object-cover border-2 border-brand-500"
										@error="handleAvatarError"
									/>
									<div class="flex-1 min-w-0">
										<p class="text-sm font-semibold text-primary truncate">{{ userName }}</p>
										<p class="text-xs text-tertiary truncate">{{ userEmail }}</p>
										<span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-brand-500/10 text-brand-500">
											<CheckBadgeIcon class="h-3 w-3" />
											{{ userRole }}
										</span>
									</div>
								</div>
							</div>

							<!-- الأزرار -->
							<div class="p-2 space-y-1">
								<button 
									@click="goToProfile" 
									class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-primary transition-colors duration-200 hover:bg-tertiary"
								>
									<UserIcon class="h-5 w-5 text-tertiary" />
									<span>{{ currentLocale === 'ar' ? 'الملف الشخصي' : 'Profile' }}</span>
								</button>
								
								<button 
									@click="goToSettings" 
									class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-primary transition-colors duration-200 hover:bg-tertiary"
								>
									<Cog6ToothIcon class="h-5 w-5 text-tertiary" />
									<span>{{ currentLocale === 'ar' ? 'الإعدادات' : 'Settings' }}</span>
								</button>
								
								<button 
									@click="goToNotifications" 
									class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-primary transition-colors duration-200 hover:bg-tertiary"
								>
									<BellIcon class="h-5 w-5 text-tertiary" />
									<span class="flex-1 text-start">{{ currentLocale === 'ar' ? 'الإشعارات' : 'Notifications' }}</span>
									<span v-if="unreadNotifications > 0" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-medium text-white">
										{{ unreadNotifications }}
									</span>
								</button>
								
								<hr class="border-primary my-1" />
								
								<button 
									@click="handleLogout" 
									class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-red-500 transition-colors duration-200 hover:bg-red-50 dark:hover:bg-red-950/30"
								>
									<ArrowRightOnRectangleIcon class="h-5 w-5" />
									<span>{{ currentLocale === 'ar' ? 'تسجيل الخروج' : 'Logout' }}</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</header>
			<main class="flex-1 p-3 md:p-4 bg-primary overflow-y-auto">
				<RouterView />
			</main>
		</div>

		<!-- Mobile Drawer - FIXED -->
		<transition name="slide">
			<div v-if="drawer" class="fixed inset-0 z-[9999] flex md:hidden">
				<!-- Dark Background -->
				<div class="absolute inset-0 bg-black/40" @click="drawer = false"></div>

				<!-- Menu -->
				<aside :class="[
					'relative h-full w-72 bg-secondary border-e border-primary shadow-xl p-3 transform transition-transform duration-300 ease-in-out fixed h-screen z-50',
					direction === 'rtl' ? 'left-0' : 'right-0'
				]">
					<div class="mb-2 flex items-center justify-between">
						<div class="flex items-center gap-3">
							<img src='@/assets/images/dashboard/TqUYX8k9ugYomJilTLVf.png' :alt="currentLocale === 'ar' ? 'لوحة التحكم' : 'Dashboard'" class="h-10 w-auto" />
						</div>
						<button class="inline-grid h-9 w-9 place-items-center rounded-lg hover:bg-tertiary text-primary" @click="drawer = false">
							<XMarkIcon class="h-5 w-5" />
						</button>
					</div>

					<nav class="space-y-1 h-[calc(100vh-5rem)] overflow-y-auto">
						<!-- Dashboard -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'لوحة التحكم' : 'Dashboard'" 
							icon="home" 
							:show-label="true"
							:to-name="'Dashboard'"
							:collapsed="false"
							:direction="direction"
						/>
						
						<!-- Appointments -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'المواعيد' : 'Appointments'" 
							icon="calendar" 
							:show-label="true"
							:items="appointmentItems"
							:collapsed="false"
							:direction="direction"
						/>
						
						<!-- Sessions -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'الجلسات' : 'Sessions'" 
							icon="video" 
							:show-label="true"
							:items="sessionItems"
							:collapsed="false"
							:direction="direction"
						/>
						
						<!-- Users -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'المستخدمين' : 'Users'" 
							icon="users" 
							:show-label="true"
							:items="userItems"
							:collapsed="false"
							:direction="direction"
						/>
						
						<!-- Articles -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'المقالات' : 'Articles'" 
							icon="document" 
							:show-label="true"
							:items="articleItems"
							:collapsed="false"
							:direction="direction"
						/>
						
						<!-- Library -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'المكتبة' : 'Library'" 
							icon="folder" 
							:show-label="true"
							:items="libraryItems"
							:collapsed="false"
							:direction="direction"
						/>

						<!-- Site Statistics -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'إحصائيات الموقع' : 'Site Statistics'" 
							icon="chart" 
							:show-label="true"
							:items="siteStatsItems"
							:collapsed="false"
							:direction="direction"
						/>

						<!-- User Messages -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'رسائل المستخدمين' : 'User Messages'" 
							icon="inbox" 
							:show-label="true"
							:items="userMessageItems"
							:collapsed="false"
							:direction="direction"
						/>

						<!-- Events -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'الأحداث' : 'Events'" 
							icon="calendar" 
							:show-label="true"
							:items="eventItems"
							:collapsed="false"
							:direction="direction"
						/>


						<!-- Psychological Programs -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'البرامج النفسية' : 'Psychological Programs'" 
							icon="calendar-days" 
							:show-label="true"
							:items="programItems"
							:collapsed="false"
							:direction="direction"
						/>

						<!-- Legal Resources -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'الموارد القانونية' : 'Legal Resources'" 
							icon="scale" 
							:show-label="true"
							:items="legalResourceItems"
							:collapsed="false"
							:direction="direction"
						/>
						
						<!-- Psychological Assessments -->
						<NavItem 
							:label="currentLocale === 'ar' ? 'المقاييس النفسية' : 'Psychological Assessments'" 
							icon="chart" 
							:show-label="true"
							:items="assessmentItems"
							:collapsed="false"
							:direction="direction"
						/>
						
						<button
							@click="handleLogout"
							class="mt-4 flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-primary transition-colors duration-200 hover:bg-tertiary"
							:class="direction === 'rtl' ? 'flex-row' : 'flex-row-reverse'"
						>
							<ArrowRightOnRectangleIcon class="h-5 w-5" />
							<span>{{ currentLocale === 'ar' ? 'تسجيل الخروج' : 'Logout' }}</span>
						</button>
					</nav>
				</aside>
			</div>
		</transition>
		
		<!-- Toast Container -->
		<ToastContainer />
	</div>
</template>

<script setup lang="ts">
import { RouterView, RouterLink } from 'vue-router';
import { computed, ref, onMounted, watch, reactive, nextTick, onUnmounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import NavItem from './NavItem.vue';
import { 
	SunIcon, 
	MoonIcon, 
	LanguageIcon,
	Cog6ToothIcon, 
	Bars3Icon, 
	XMarkIcon, 
	ChevronDoubleLeftIcon, 
	ChevronDoubleRightIcon, 
	MagnifyingGlassIcon,
	ArrowRightOnRectangleIcon,
	ChevronDownIcon,
	UserIcon,
	BellIcon,
	CheckBadgeIcon
} from '@heroicons/vue/24/outline';

const authStore = useAuthStore();
const router = useRouter();

// 🔹 Refs للقائمة المنسدلة
const userMenuOpen = ref(false);
const userMenuRef = ref<HTMLElement | null>(null);
const userDropdownRef = ref<HTMLElement | null>(null);

const drawer = ref(false);
const collapsed = ref(false);
const avatarOpen = ref(false);
const currentLocale = ref('ar');

// 🔹 بيانات المستخدم من الـ Store
const userData = computed(() => authStore.user);

const userName = computed(() => {
	if (!userData.value) return currentLocale.value === 'ar' ? 'مستخدم' : 'User'
	return userData.value.name || (currentLocale.value === 'ar' ? 'مستخدم' : 'User')
})

const userEmail = computed(() => {
	if (!userData.value) return ''
	return userData.value.email || ''
})

const userRole = computed(() => {
	if (!userData.value) return currentLocale.value === 'ar' ? 'زائر' : 'Guest'
	const roleMap: Record<string, { ar: string, en: string }> = {
		'Admin': { ar: 'مدير', en: 'Admin' },
		'Therapist': { ar: 'معالج', en: 'Therapist' },
		'Client': { ar: 'عميل', en: 'Client' }
	}
	const role = userData.value.role || ''
	return roleMap[role]?.[currentLocale.value === 'ar' ? 'ar' : 'en'] || role || (currentLocale.value === 'ar' ? 'زائر' : 'Guest')
})

// 🔹 صورة المستخدم
const userAvatar = computed(() => {
	if (!userData.value) return 'https://ui-avatars.com/api/?name=User&background=8FAE2F&color=fff&size=128'
	return userData.value.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(userName.value)}&background=8FAE2F&color=fff&size=128`
})

const handleAvatarError = (event: Event) => {
	const img = event.target as HTMLImageElement
	img.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(userName.value)}&background=8FAE2F&color=fff&size=128`
}

// 🔹 عدد الإشعارات غير المقروءة (يمكن جلبها من API)
const unreadNotifications = ref(3)

// 🔹 دوال قائمة المستخدم
const toggleUserMenu = () => {
	userMenuOpen.value = !userMenuOpen.value
}

const closeUserMenu = () => {
	userMenuOpen.value = false
}

const goToProfile = () => {
	closeUserMenu()
	if (userData.value?.id) {
		router.push(`/dashboard/users/${userData.value.id}`)
	} else {
		// Fallback في حالة عدم وجود ID
		router.push({ name: 'profile' })
	}
}

const goToSettings = () => {
	closeUserMenu()
	router.push({ name: 'settings' })
}

const goToNotifications = () => {
	closeUserMenu()
	router.push({ name: 'notifications' })
}

// 🔹 دوال سابقة
const getLocale = (): string => {
	return localStorage.getItem('locale') || 'ar';
};

const saveLocale = (locale: string) => {
	localStorage.setItem('locale', locale);
};

const initLocale = () => {
	currentLocale.value = getLocale();
	applyLocale();
};

const applyLocale = () => {
	const dir = currentLocale.value === 'ar' ? 'rtl' : 'ltr';
	document.documentElement.lang = currentLocale.value;
	document.documentElement.dir = dir;
};

const toggleLocale = () => {
	currentLocale.value = currentLocale.value === 'ar' ? 'en' : 'ar';
	saveLocale(currentLocale.value);
	applyLocale();
};

const direction = computed(() => currentLocale.value === 'ar' ? 'rtl' : 'ltr');

const getSidebarState = (): boolean => {
	const saved = localStorage.getItem('sidebarCollapsed');
	return saved === 'true';
};

const saveSidebarState = (state: boolean) => {
	localStorage.setItem('sidebarCollapsed', state.toString());
};

const initSidebarState = () => {
	collapsed.value = getSidebarState();
};

const contentStyle = reactive({
	marginRight: '0px',
	marginLeft: '0px'
});

const updateContentMargin = () => {
	if (window.innerWidth >= 768) {
		const marginValue = collapsed.value ? '80px' : '288px';
		if (direction.value === 'rtl') {
			contentStyle.marginRight = marginValue;
			contentStyle.marginLeft = '0px';
		} else {
			contentStyle.marginLeft = marginValue;
			contentStyle.marginRight = '0px';
		}
	} else {
		contentStyle.marginRight = '0px';
		contentStyle.marginLeft = '0px';
	}
};

const toggleSidebar = () => {
	collapsed.value = !collapsed.value;
	saveSidebarState(collapsed.value);
	updateContentMargin();
};

const handleLogout = async () => {
	closeUserMenu()
	await authStore.logout();
	router.push('/admin/login');
};

// تعريف العناصر الفرعية لكل قائمة باللغتين
const appointmentItems = computed(() => [
	{ toName: 'appointments', label: currentLocale.value === 'ar' ? 'جميع المواعيد' : 'All Appointments', icon: 'calendar' },
	{ toName: 'upcoming', label: currentLocale.value === 'ar' ? 'المواعيد القادمة' : 'Upcoming Appointments', icon: 'clock' },
	{ toName: 'history', label: currentLocale.value === 'ar' ? 'سجل المواعيد' : 'Appointment History', icon: 'folder' }
]);

const sessionItems = computed(() => [
	{ toName: 'sessions', label: currentLocale.value === 'ar' ? 'جميع الجلسات' : 'All Sessions', icon: 'video' },
	{ toName: 'active-sessions', label: currentLocale.value === 'ar' ? 'الجلسات النشطة' : 'Active Sessions', icon: 'play' },
	{ toName: 'session-history', label: currentLocale.value === 'ar' ? 'سجل الجلسات' : 'Session History', icon: 'folder' }
]);

const userItems = computed(() => [
	{ toName: 'users', label: currentLocale.value === 'ar' ? 'جميع المستخدمين' : 'All Users', icon: 'users' },
	{ toName: 'clients', label: currentLocale.value === 'ar' ? 'العملاء' : 'Clients', icon: 'user' },
	{ toName: 'therapists', label: currentLocale.value === 'ar' ? 'المعالجين' : 'Therapists', icon: 'academic' },
	{ toName: 'admins', label: currentLocale.value === 'ar' ? 'المشرفين' : 'Admins', icon: 'shield' }
]);

const articleItems = computed(() => [
	{ toName: 'articles', label: currentLocale.value === 'ar' ? 'جميع المقالات' : 'All Articles', icon: 'document' },
	{ toName: 'categories', label: currentLocale.value === 'ar' ? 'تصنيفات المقالات' : 'Article Categories', icon: 'folder' }
]);

const libraryItems = computed(() => [
	{ toName: 'libraries', label: currentLocale.value === 'ar' ? 'المكتبة الرئيسية' : 'Main Library', icon: 'folder' },
]);

const siteStatsItems = computed(() => [
	{ toName: 'site-statistics', label: currentLocale.value === 'ar' ? 'إحصائيات الموقع' : 'Site Statistics', icon: 'chart' }
]);

const userMessageItems = computed(() => [
	{ toName: 'user-messages', label: currentLocale.value === 'ar' ? 'رسائل المستخدمين' : 'User Messages', icon: 'inbox' }
]);

const eventItems = computed(() => [
	{ toName: 'events', label: currentLocale.value === 'ar' ? 'جميع الأحداث' : 'All Events', icon: 'calendar' }
]);

const programItems = computed(() => [
	{ toName: 'programs', label: currentLocale.value === 'ar' ? 'جميع البرامج' : 'All Programs', icon: 'calendar-days' },
	{ toName: 'program-tracking', label: currentLocale.value === 'ar' ? 'متابعة البرامج' : 'Program Tracking', icon: 'chart' }
]);

const legalResourceItems = computed(() => [
	{ toName: 'legal-resources', label: currentLocale.value === 'ar' ? 'جميع الموارد القانونية' : 'All Legal Resources', icon: 'scale' },
]);

const assessmentItems = computed(() => [
	{ toName: 'assessments', label: currentLocale.value === 'ar' ? 'جميع المقاييس' : 'All Assessments', icon: 'chart' },
	{ toName: 'scale-categories', label: currentLocale.value === 'ar' ? 'تصنيفات المقاييس' : 'Scale Categories', icon: 'folder' },
	{ toName: 'assessment-results', label: currentLocale.value === 'ar' ? 'نتائج الاختبارات' : 'Assessment Results', icon: 'chartBar' }
]);

const isDark = computed(() => document.documentElement.classList.contains('dark'));

const toggleTheme = () => {
	const isDarkMode = !document.documentElement.classList.contains('dark');
	document.documentElement.classList.toggle('dark');
	localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
};

// 🔹 إغلاق القائمة عند النقر خارجها
const handleClickOutside = (event: MouseEvent) => {
	const target = event.target as HTMLElement
	
	// إذا كانت القائمة مفتوحة والنقر خارجها
	if (userMenuOpen.value && userMenuRef.value && !userMenuRef.value.contains(target)) {
		closeUserMenu()
	}
}

// 🔹 إغلاق القائمة عند الضغط على ESC
const handleEscapeKey = (event: KeyboardEvent) => {
	if (event.key === 'Escape' && userMenuOpen.value) {
		closeUserMenu()
	}
}

onMounted(() => {
	// تهيئة اللغة
	initLocale();
	
	// تهيئة حالة السايدبار من localStorage
	initSidebarState();
	
	// تحديث الـ margin عند التحميل
	updateContentMargin();
	
	// إغلاق قائمة المستخدم عند النقر خارجها
	document.addEventListener('click', handleClickOutside)
	document.addEventListener('keydown', handleEscapeKey)

	// تحديث الـ margin عند تغيير حجم النافذة
	window.addEventListener('resize', () => {
		if (window.innerWidth >= 768) {
			drawer.value = false;
		}
		updateContentMargin();
	});
	
	// إضافة event listener لحفظ الحالة قبل إغلاق الصفحة
	window.addEventListener('beforeunload', () => {
		saveSidebarState(collapsed.value);
	});
});

onUnmounted(() => {
	// تنظيف event listeners
	document.removeEventListener('click', handleClickOutside)
	document.removeEventListener('keydown', handleEscapeKey)
})

// مراقبة حالة collapsed وحفظها في localStorage وتحديث الـ margin
watch(() => collapsed.value, (newValue) => {
	saveSidebarState(newValue);
	updateContentMargin();
});

// مراقبة تغيير اللغة وتحديث الـ margin
watch(() => currentLocale.value, () => {
	updateContentMargin();
});

// التأكد من إغلاق القائمة الجانبية عند التبديل بين الأجهزة
watch(() => drawer.value, (newValue) => {
	if (newValue && window.innerWidth >= 768) {
		drawer.value = false;
	}
});

// تهيئة الوضع الداكن من localStorage
const savedTheme = localStorage.getItem('theme');
if (savedTheme === 'dark') {
	document.documentElement.classList.add('dark');
} else if (savedTheme === 'light') {
	document.documentElement.classList.remove('dark');
}
</script>

<style scoped>
/* تخصيص scrollbar للقائمة الجانبية */
aside::-webkit-scrollbar {
	width: 4px;
}

aside::-webkit-scrollbar-track {
	background: transparent;
}

aside::-webkit-scrollbar-thumb {
	background: #d1d5db;
	border-radius: 4px;
}

.dark aside::-webkit-scrollbar-thumb {
	background: #4b5563;
}

/* Main content scrollbar */
main::-webkit-scrollbar {
	width: 8px;
}

main::-webkit-scrollbar-track {
	background: #f3f4f6;
}

.dark main::-webkit-scrollbar-track {
	background: #1f2937;
}

main::-webkit-scrollbar-thumb {
	background: #d1d5db;
	border-radius: 4px;
}

.dark main::-webkit-scrollbar-thumb {
	background: #4b5563;
}

/* تحسينات للشاشات الصغيرة */
@media (max-width: 767px) {
	main {
		padding: 1rem;
	}
}

/* تحسينات النص العربي */
:global([lang="ar"]) {
	font-family: 'Cairo', 'Segoe UI', 'Tahoma', 'Geneva', 'Verdana', sans-serif;
}

:global([lang="en"]) {
	font-family: 'Segoe UI', 'Tahoma', 'Geneva', 'Verdana', sans-serif;
}

/* تحسينات للانتقالات السلسة */
.transition-all {
	transition-property: all;
	transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
	transition-duration: 300ms;
}

/* منع التمرير الأفقي */
html, body {
	overflow-x: hidden;
}

/* تحسين z-index للتسلسل */
aside {
	z-index: 40;
}

header {
	z-index: 30;
}

.user-menu {
	z-index: 50;
}

/* التأكد من أن المحتوى يأخذ العرض الكامل */
.flex-1 {
	flex: 1 1 0%;
	min-width: 0;
}

/* تحسين الأداء للعناصر الثابتة */
aside, header {
	will-change: transform;
	backface-visibility: hidden;
}

/* تحسينات للـ RTL/LTR */
.slide-enter-active,
.slide-leave-active {
	transition: transform 0.3s ease;
}

:global([dir="rtl"]) .slide-enter-from,
:global([dir="rtl"]) .slide-leave-to {
	transform: translateX(-100%);
}

:global([dir="ltr"]) .slide-enter-from,
:global([dir="ltr"]) .slide-leave-to {
	transform: translateX(100%);
}

:global([dir="rtl"]) .slide-enter-to,
:global([dir="rtl"]) .slide-leave-from {
	transform: translateX(0);
}

:global([dir="ltr"]) .slide-enter-to,
:global([dir="ltr"]) .slide-leave-from {
	transform: translateX(0);
}

/* تحسينات القائمة المنسدلة للمستخدم */
.user-menu {
	animation: slideDown 0.2s ease-out;
	transform-origin: top center;
}

@keyframes slideDown {
	from {
		opacity: 0;
		transform: scale(0.95) translateY(-10px);
	}
	to {
		opacity: 1;
		transform: scale(1) translateY(0);
	}
}
</style>