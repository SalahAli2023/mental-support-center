import { createApp } from 'vue';
import '@fortawesome/fontawesome-free/css/all.min.css';
import router from './routes/index.js';
import App from './App.vue';
import { createPinia } from 'pinia';
import { createI18n } from 'vue-i18n';
import ToastContainer from './components/dashboard/component/ui/ToastContainer.vue';

import en from './locales/en.json';
import ar from './locales/ar.json';

import './assets/css/style.css';


const app = createApp(App);
app.component('ToastContainer', ToastContainer);

const pinia = createPinia();
app.use(pinia);

// تهيئة auth store بعد إنشاء pinia
// ملاحظة: تحميل admin_token يتم فقط عند الوصول للواجهة الإدارية (في router.beforeEach)
// هذا يمنع تحميل admin_token تلقائياً عند زيارة الواجهة العامة
import { useAuthStore } from './stores/auth';
const authStore = useAuthStore();

// لا نستدعي initializeAuth تلقائياً هنا
// يتم استدعاؤه فقط في router.beforeEach عند الوصول للواجهة الإدارية

const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || 'ar',
  fallbackLocale: 'ar',
  messages: { en, ar },
});
app.use(i18n);

app.use(router);

app.mount('#app');