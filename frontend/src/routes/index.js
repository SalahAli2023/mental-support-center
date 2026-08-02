import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
// import Users from '@/components/dashboard/Users/index.vue'
import UserDetails from '@/components/dashboard/Users/UserDetails.vue'

// --- Backend Pages ---
const AppLayout = () => import('../components/dashboard/component/layout/AppLayout.vue');
const Dashboard = () => import('../components/dashboard/Dashboard/Index.vue');
const Appointments = () => import('../components/dashboard/Appointments/Index.vue');
const Sessions = () => import('../components/dashboard/Sessions/Index.vue');
const Users = () => import('../components/dashboard/Users/Index.vue');
const therapists = () => import('../components/dashboard/Users/therapists/TherapistsManagement.vue');
const clients = () => import('../components/dashboard/Users/clients/PatientsManagement.vue');
const Articles = () => import('../components/dashboard/Articles/Index.vue');
const ArticleCategories = () => import('../components/dashboard/Articles/ArticleCategories.vue');
// const AddArticles = () => import('../components/dashboard/Articles/ArticleForm.vue');
const Programs = () => import('../components/dashboard/Programs/Index.vue');
const ProgramDetails = () => import('../components/dashboard/Programs/ProgramDetails.vue');
const SessionDetails = () => import('../components/dashboard/Programs/SessionDetails.vue');
const Library = () => import('../components/dashboard/Library/Index.vue');
const Assessments = () => import('../components/dashboard/Measures/Measures/Index.vue');
const Categories = () => import('../components/dashboard/Measures/Categories/CategoriesIndex.vue');
const AssessmentResults = () => import('../components/dashboard/Assessments/Index.vue');
const ProgramTracking = () => import('../components/dashboard/Programs/ProgramTracking.vue');
const UserMessages = () => import('../components/dashboard/Messages/Index.vue');


const LegalResources = () => import('../components/dashboard/LegalResources/Index.vue')




const Settings = () => import('../components/dashboard/Settings/Index.vue');
const Events = () => import('../components/dashboard/Events/Index.vue');

// --- Frontend Pages ---
import HomePage from '../components/frontend/home.vue'
import session from '../components/frontend/Session/PatientSessions.vue'
import VideoSession from '../components/frontend/Session/VideoSession.vue'
import AboutPage from '../components/frontend/AboutPage.vue'
import program from '../components/frontend/programs/index.vue'
import ProgramList from '../components/frontend/programs/ProgramList.vue'
import ProgramView from '../components/frontend/programs/ProgramView.vue'
import ActivityView from '../components/frontend/programs/ActivityView.vue'
import EventsPage from '../components/frontend/EventsPage.vue'
import MeasuresPage from '../components/frontend/MeasuresPage.vue'
import ArticleMain from '../components/frontend/article/ArticleMain.vue'
import ArticleDetail from '../components/frontend/article/ArticleDetail.vue'
import Specialists from '../components/frontend/Specialists/TherapistList.vue'
import therapisteDetail from '../components/frontend/Specialists/TherapistProfile.vue'
import LibraryMain from '../components/frontend/libraray/LibraryMain.vue'
import contact from '../components/frontend/contact.vue'
import register from '../components/frontend/RegistrationPage.vue'
import LoginPage from '../components/frontend/LoginPage.vue'
import LegalSocialResources from '../components/frontend/LegalSocialResources.vue'
const SiteStatistics = () => import('../components/dashboard/Stats/SiteStatistics.vue')

// --- Auth Pages ---
const Login = () => import('../components/dashboard/auth/Login.vue');

// --- دمج جميع المسارات ---
const routes = [
  // Frontend routes (عامة)
  { path: '/', name: 'Home', component: HomePage },
  { path: '/events', name: 'Events', component: EventsPage },
  { path: '/about', name: 'About', component: AboutPage },
  { path: '/Session', name: 'Session', component: session },
  { path: '/session/:roomId', name: 'VideoSession', component: VideoSession, props: true, meta: { requiresAuth: true } },
  { path: '/measures', name: 'Measures', component: MeasuresPage },
  { path: '/article', name: 'ArticleMain', component: ArticleMain },
  { path: '/article/:id', name: 'ArticleDetail', component: ArticleDetail, props: true },
  { path: '/Specialists', name: 'Specialists', component: Specialists, props: true },
  { path: '/booking', name: 'Booking', component: Specialists, props: true },
  { path: '/therapist/:id', name: 'therapisteDetail', component: therapisteDetail, props: true },
  { path: '/library', name: 'library', component: LibraryMain, props: true },
  { path: '/contact', name: 'contact', component: contact, props: true },
  { path: '/register', name: 'register', component: register, props: true },
  { path: '/login', name: 'frontend-login', component: LoginPage, props: true },
  { path: '/legal', name: 'legal', component: LegalSocialResources, props: true },
  { path: '/program', name: 'program', component: program, props: true },
  { path: '/programs', name: 'ProgramList', component: ProgramList },
  { path: '/program/:id', name: 'ProgramView', component: ProgramView, props: true },
  { path: '/program/:programId/activity/:activityId', name: 'ActivityView', component: ActivityView, props: true, meta: { requiresAuth: true } },
  // {
  //   path: '/dashboard/users',
  //   name: 'Users',
  //   component: Users
  // },
  {
    path: '/dashboard/users/:id',
    name: 'UserDetails',
    component: UserDetails,
    props: true
  },
  // Auth routes
  {
    path: '/admin/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true }
  },

  // Backend routes (محمية - تتطلب تسجيل دخول)
  {
    path: '/admin',
    component: AppLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', redirect: { name: 'Dashboard' } },
      { path: 'dashboard', name: 'Dashboard', component: Dashboard },
      { path: 'appointments', name: 'appointments', component: Appointments },
      { path: 'appointments/upcoming', name: 'upcoming', component: Appointments, props: { filter: 'upcoming' } },
      { path: 'appointments/history', name: 'history', component: Appointments, props: { filter: 'history' } },
      { path: 'sessions', name: 'sessions', component: Sessions },
      { path: 'sessions/active', name: 'active-sessions', component: Sessions, props: { filter: 'active' } },
      { path: 'sessions/history', name: 'session-history', component: Sessions, props: { filter: 'history' } },
      { path: 'users', name: 'users', component: Users },
      { path: 'users/admins', name: 'admins', component: Users, props: { roleFilter: 'Admin' } },
      { path: 'therapists', name: 'therapists', component: therapists },
      { path: 'clients', name: 'clients', component: clients },
      { path: 'user-messages', name: 'user-messages', component: UserMessages },
      { path: 'articles', name: 'articles', component: Articles },
      { path: 'categories', name: 'categories', component: ArticleCategories },
      // { path: 'new-article', name: 'new-article', component: AddArticles },
      { path: 'programs', name: 'programs', component: Programs },
      { path: 'programs/tracking', name: 'program-tracking', component: ProgramTracking },
      { path: 'programs/:id', name: 'program-details', component: ProgramDetails, props: true },
      { path: 'programs/:programId/sessions/:sessionId', name: 'session-details', component: SessionDetails, props: true },
      { path: 'libraries', name: 'libraries', component: Library },
      { path: 'events', name: 'events', component: Events },
      { path: 'site-statistics', name: 'site-statistics', component: SiteStatistics },
      { path: 'scale-categories', name: 'scale-categories', component: Categories },

      { path: 'assessments', name: 'assessments', component: Assessments },
      { path: 'assessment-results', name: 'assessment-results', component: AssessmentResults },
      { path: 'legal-resources', name: 'legal-resources', component: LegalResources },

      { path: 'settings', name: 'settings', component: Settings },
    ]
  },

  // Redirect for unknown routes
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
];

// --- إنشاء الراوتر ---
const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

// --- حارس التنقل للحماية والمصادقة ---
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // تحديد نوع المسار: هل هو مسار إداري (dashboard/admin) أم مسار عام (frontend)?
  const isAdminRoute = to.path.startsWith('/admin') || to.path.startsWith('/dashboard');
  const isFrontendRoute = !isAdminRoute;

  // تغيير اتجاه اللغة
  const savedLanguage = localStorage.getItem('preferredLanguage') || 'ar';
  document.documentElement.dir = savedLanguage === 'ar' ? 'rtl' : 'ltr';
  document.documentElement.lang = savedLanguage;

  // للواجهة الإدارية فقط: تهيئة auth store و token
  if (isAdminRoute) {
    // تهيئة auth store إذا كان هناك token محفوظ ولكن لم يتم التهيئة بعد
    if (!authStore.token) {
      const savedToken = localStorage.getItem('admin_token');
      if (savedToken) {
        authStore.token = savedToken;
      }
    }

    // إذا كان هناك token ولكن لا توجد بيانات مستخدم، جلب البيانات
    if (authStore.token && !authStore.user && !authStore.initializing) {
      try {
        await authStore.fetchUser();
      } catch (error) {
        // إذا فشل جلب البيانات وكان الخطأ 401، احذف Token
        if (error instanceof Error && error.message.includes('انتهت صلاحية')) {
          console.error('Token expired, removing...');
          authStore.token = null;
          localStorage.removeItem('admin_token');
        } else {
          // خطأ آخر (مثل مشكلة في الاتصال) - لا تحذف Token، فقط سجل الخطأ
          console.error('Failed to fetch user (non-critical):', error);
        }
      }
    }

    // التحقق من المصادقة للواجهة الإدارية فقط
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
      // إذا كانت الصفحة تتطلب تسجيل دخول ولم يكن هناك token
      next('/admin/login');
      return;
    } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
      // إذا كان هناك token وحاول الدخول لصفحة تسجيل الدخول الإدارية
      next('/admin/dashboard');
      return;
    }
  }

  // للواجهة العامة: لا نتحقق من admin_token على الإطلاق
  // المستخدمون العاديون (العملاء) يمكنهم الوصول بحرية للصفحات العامة
  // حتى لو كان هناك admin_token موجود (لأنه ليس ذا صلة بالواجهة العامة)

  // المسار مسموح
  next();
});

export default router;