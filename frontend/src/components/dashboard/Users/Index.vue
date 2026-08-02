<template>
  <div class="space-y-4 p-3 sm:p-4">
    <!-- عنوان + فلتر + زر -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-lg font-semibold text-primary sm:text-2xl">
          {{ isAdminsView ? 'إدارة المشرفين' : 'إدارة المستخدمين' }}
        </h1>
        <p class="text-sm text-tertiary mt-1" v-if="isAdminsView">
          عرض وإدارة جميع المشرفين في النظام
        </p>
      </div>
      
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
        <!-- حقل البحث -->
        <div class="relative w-full sm:w-48">
          <input 
            v-model="q" 
            :placeholder="isAdminsView ? 'ابحث بالمشرفين...' : 'ابحث بالمستخدمين...'" 
            class="w-full input" 
            @input="handleSearch"
          />
          <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
        </div>
        
        <!-- فلترة الدور (تظهر فقط في صفحة المستخدمين) -->
        <div class="relative w-full sm:w-48" v-if="!isAdminsView">
          <select 
            v-model="role" 
            class="w-full input"
            @change="handleFilter"
          >
            <option value="">جميع المستخدمين</option>
            <option>Admin</option>
            <option>Therapist</option>
            <option>Client</option>
          </select>
          <div class="absolute inset-y-0 left-3 pointer-events-none flex items-center">
            <svg class="w-4 h-4 text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </div>
        </div>
        
        <!-- زر إضافة مشرف (يظهر فقط في صفحة المشرفين) -->
        <Button 
          v-if="isAdminsView"
          @click="openAdminModal"
          variant="primary"
          class="w-full sm:w-auto"
        >
          <i class="fas fa-plus mr-1"></i>
          إضافة مشرف
        </Button>
      </div>
    </div>

    <!-- حالة التحميل -->
    <div v-if="userStore.loading" class="text-center py-8 text-secondary">
      <div class="inline-flex items-center gap-2">
        <svg class="animate-spin h-5 w-5 text-brand" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>جاري التحميل...</span>
      </div>
    </div>

    <!-- جدول المستخدمين -->
    <Card v-else>
      
      <div class="overflow-x-auto -mx-1 sm:mx-0">
        <!-- عرض الجدول للشاشات المتوسطة والكبيرة -->
        <table class="min-w-full text-sm hidden sm:table">
          <thead>
            <tr class="text-start text-secondary">
              <th class="px-2 py-2 min-w-[100px] sm:px-3 sm:py-3 text-start">المستخدم</th>
              <th class="px-2 py-2 min-w-[100px] sm:px-3 sm:py-3 text-start">البريد الإلكتروني</th>
              <th class="px-2 py-2 min-w-[100px] sm:px-3 sm:py-3 text-start">الدور</th>
              <th class="px-2 py-2 min-w-[100px] sm:px-3 sm:py-3 text-start">رقم الهاتف</th>
              <th class="px-2 py-2 min-w-[130px] sm:px-3 sm:py-3 text-start">تاريخ الانضمام</th>
              <th class="px-2 py-2 min-w-[80px] sm:px-3 sm:py-3 text-start">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id" class="border-t border-primary hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
              <td class="px-2 py-2 text-primary whitespace-nowrap sm:px-3 sm:py-1">
                <div class="flex items-center gap-2">
                  <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-brand to-brand/80 flex items-center justify-center">
                    <span class="text-white font-semibold text-xs">
                      {{ getUserInitials(user.name) }}
                    </span>
                  </div>
                  <div>
                    <div class="font-medium">{{ user.name }}</div>
                    <div class="text-xs text-tertiary">ID: {{ user.id }}</div>
                  </div>
                </div>
              </td>
              <td class="px-2 py-2 text-primary whitespace-nowrap sm:px-3 sm:py-3 text-left direction-ltr">
                {{ user.email }}
              </td>
              <td class="px-2 py-2 sm:px-3 sm:py-3">
                <span
                  class="badge border text-xs whitespace-nowrap"
                  :class="getRoleBadgeClass(user.role)"
                >
                  {{ getRoleText(user.role) }}
                </span>
              </td>
              <td class="px-2 py-2 text-primary whitespace-nowrap sm:px-3 sm:py-3">
                {{ user.phone || 'غير محدد' }}
              </td>
              <td class="px-2 py-2 text-primary whitespace-nowrap text-xs sm:px-3 sm:py-3 sm:text-sm">
                {{ formatDate(user.joined_at) }}
              </td>
              <td class="px-2 py-2 sm:px-3 sm:py-3">
                <div class="flex gap-2">
                  <Button size="sm" variant="outline" class="w-full sm:w-auto" @click="viewUser(user)">
                    <i class="fas fa-eye mr-1"></i>
                    عرض
                  </Button>
                  <Button size="sm" variant="outline" class="w-full sm:w-auto" @click="editUser(user)">
                    <i class="fas fa-edit mr-1"></i>
                    تعديل
                  </Button>
                  <Button 
                    size="sm" 
                    variant="danger" 
                    class="w-full sm:w-auto" 
                    @click="deleteUser(user)"
                    v-if="!isAdminsView"
                  >
                    <i class="fas fa-trash mr-1"></i>
                    حظر
                  </Button>
                  <Button 
                    size="sm" 
                    variant="danger-outline" 
                    class="w-full sm:w-auto" 
                    @click="deactivateAdmin(user)"
                    v-if="isAdminsView && user.id !== currentUserId"
                  >
                    <i class="fas fa-user-slash mr-1"></i>
                    تعطيل
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        
        <!-- عرض البطاقات للشاشات الصغيرة -->
        <div class="space-y-3 sm:hidden">
          <div 
            v-for="user in filteredUsers" 
            :key="user.id" 
            class="bg-primary rounded-lg border border-primary p-3 space-y-2"
          >
            <div class="flex justify-between items-start">
              <div class="flex items-center gap-2">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-brand to-brand/80 flex items-center justify-center">
                  <span class="text-white font-semibold text-sm">
                    {{ getUserInitials(user.name) }}
                  </span>
                </div>
                <div>
                  <div class="font-medium text-primary">{{ user.name }}</div>
                  <div class="text-xs text-tertiary">ID: {{ user.id }}</div>
                </div>
              </div>
              <span
                class="badge border text-xs whitespace-nowrap"
                :class="getRoleBadgeClass(user.role)"
              >
                {{ getRoleText(user.role) }}
              </span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-sm">
              <div>
                <div class="text-tertiary text-xs">البريد الإلكتروني</div>
                <div class="text-primary truncate direction-ltr">{{ user.email }}</div>
              </div>
              <div>
                <div class="text-tertiary text-xs">رقم الهاتف</div>
                <div class="text-primary">{{ user.phone || 'غير محدد' }}</div>
              </div>
              <div class="col-span-2">
                <div class="text-tertiary text-xs">تاريخ الانضمام</div>
                <div class="text-primary">{{ formatDate(user.joined_at) }}</div>
              </div>
            </div>
            <div class="pt-2 space-y-2">
              <Button size="sm" variant="outline" class="w-full" @click="viewUser(user)">
                <i class="fas fa-eye mr-1"></i>
                عرض
              </Button>
              <Button size="sm" variant="outline" class="w-full" @click="editUser(user)">
                <i class="fas fa-edit mr-1"></i>
                تعديل
              </Button>
              <Button 
                size="sm" 
                variant="danger" 
                class="w-full" 
                @click="deleteUser(user)"
                v-if="!isAdminsView"
              >
                <i class="fas fa-trash mr-1"></i>
                حظر
              </Button>
              <Button 
                size="sm" 
                variant="danger-outline" 
                class="w-full" 
                @click="deactivateAdmin(user)"
                v-if="isAdminsView && user.id !== currentUserId"
              >
                <i class="fas fa-user-slash mr-1"></i>
                تعطيل
              </Button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- رسالة عندما لا توجد بيانات -->
      <div v-if="filteredUsers.length === 0 && !userStore.loading" class="text-center py-12">
        <div class="inline-flex flex-col items-center gap-3">
          <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <div>
            <h3 class="text-lg font-medium text-primary mb-1">
              {{ isAdminsView ? 'لا يوجد مشرفين' : 'لا يوجد مستخدمين' }}
            </h3>
            <p class="text-sm text-tertiary">
              {{ isAdminsView 
                ? 'لم يتم إضافة أي مشرفين بعد. ابدأ بإضافة أول مشرف.' 
                : 'لم يتم العثور على مستخدمين مطابقين لبحثك.' 
              }}
            </p>
          </div>
          <Button 
            v-if="isAdminsView"
            @click="openAdminModal"
            variant="primary"
            class="mt-2"
          >
            <i class="fas fa-plus mr-1"></i>
            إضافة أول مشرف
          </Button>
        </div>
      </div>
    </Card>

    <!-- مودال إضافة/تعديل المستخدم -->
    <UserModal 
      :open="showAddModal || showEditModal"
      :user="selectedUser"
      :isAdminContext="isAdminsView"
      @close="closeModal"
      @save="handleSaveUser"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useUserStore } from '../../../stores/users';
import { useAuthStore } from '@/stores/auth';
import UserModal from './UserModal.vue';
import type { User } from '../../../types/user';
import Button from '@/components/dashboard/component/ui/Button.vue';
import Card from '@/components/dashboard/component/ui/Card.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

interface Props {
  roleFilter?: string
}

const props = defineProps<Props>()

const router = useRouter()
const userStore = useUserStore();
const authStore = useAuthStore();

const q = ref('');
const role = ref(props.roleFilter || '');
const showAddModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref<User | null>(null);
let searchTimeout: ReturnType<typeof setTimeout>;

const isAdminsView = computed(() => props.roleFilter === 'Admin');
const currentUserId = computed(() => authStore.user?.id);

// تصفية المستخدمين حسب الدور
const filteredUsers = computed(() => {
  if (isAdminsView.value) {
    // في صفحة المشرفين، عرض المستخدمين الذين دورهم Admin فقط
    return userStore.items.filter(user => user.role === 'Admin');
  } else {
    // في صفحة المستخدمين، عرض جميع المستخدمين مع التصفية
    return userStore.items.filter(user => {
      const matchesRole = !role.value || user.role === role.value;
      const matchesSearch = !q.value || 
        user.name.toLowerCase().includes(q.value.toLowerCase()) ||
        user.email.toLowerCase().includes(q.value.toLowerCase());
      return matchesRole && matchesSearch;
    });
  }
});

// دالة البحث مع debounce
const handleSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    // في صفحة المشرفين، لا نحتاج للبحث لأننا نعرض فقط المشرفين
    if (!isAdminsView.value) {
      userStore.fetchUsers(q.value, role.value);
    }
  }, 500);
};

// دالة التصفية
const handleFilter = () => {
  if (!isAdminsView.value) {
    userStore.fetchUsers(q.value, role.value);
  }
};

// تنسيق التاريخ
const formatDate = (dateString: string) => {
  try {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
      return 'غير محدد';
    }
    return date.toLocaleDateString('ar-SA', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  } catch (error) {
    console.error('Error formatting date:', error);
    return 'غير محدد';
  }
};

// ألوان البادجات حسب الدور
const getRoleBadgeClass = (role: string) => {
  switch (role) {
    case 'Admin':
      return 'badge-error';
    case 'Therapist':
      return 'badge-info';
    case 'Client':
      return 'badge-success';
    default:
      return 'badge-neutral';
  }
};

// نص الدور بالعربية
const getRoleText = (role: string) => {
  const roles: { [key: string]: string } = {
    'Admin': 'مدير',
    'Therapist': 'معالج',
    'Client': 'عميل'
  };
  return roles[role] || role;
};

// الحروف الأولى من الاسم
const getUserInitials = (name: string) => {
  return name
    .split(' ')
    .map(part => part.charAt(0))
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

// الإجراءات
const viewUser = (user: User) => {
  selectedUser.value = user;
  router.push(`/dashboard/users/${user.id}`);
};

const editUser = (user: User) => {
  selectedUser.value = user;
  showEditModal.value = true;
};

const deleteUser = async (user: User) => {
  if (confirm(`هل أنت متأكد من حذف المستخدم "${user.name}"؟`)) {
    try {
      await userStore.deleteUser(user.id);
      await userStore.fetchUsers();
      alert('تم حذف المستخدم بنجاح');
    } catch (error: any) {
      console.error('Error deleting user:', error);
      alert(error.message || 'حدث خطأ أثناء حذف المستخدم');
    }
  }
};

// تعطيل مشرف
const deactivateAdmin = async (user: User) => {
  if (user.id === currentUserId.value) {
    alert('لا يمكنك تعطيل حسابك الخاص');
    return;
  }

  if (confirm(`هل أنت متأكد من تعطيل المشرف "${user.name}"؟`)) {
    try {
      // تغيير دور المشرف إلى Client (أو أي دور آخر)
      await userStore.updateUser(user.id, { 
        role: 'Client',
        is_active: false 
      });
      await userStore.fetchUsers();
      alert('تم تعطيل المشرف بنجاح');
    } catch (error: any) {
      console.error('Error deactivating admin:', error);
      alert(error.message || 'حدث خطأ أثناء تعطيل المشرف');
    }
  }
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  selectedUser.value = null;
};

const openAdminModal = () => {
  selectedUser.value = null;
  showAddModal.value = true;
};

// const handleSaveUser = async (userData: any) => {
//   try {
//     if (selectedUser.value) {
//       await userStore.updateUser(selectedUser.value.id, userData);
//       alert('تم تحديث المشرف بنجاح');
//     } else {
//       const payload = { 
//         ...userData,
//         role: 'Admin' // التأكد من أن الدور هو Admin
//       };
//       await userStore.createUser(payload);
//       alert('تم إضافة المشرف بنجاح');
//     }
//     closeModal();
//     await userStore.fetchUsers();
//   } catch (error: any) {
//     console.error('Error saving user:', error);
//     alert(error.message || 'حدث خطأ أثناء حفظ المشرف');
//   }
// };

const handleSaveUser = async (userData: any) => {
  try {
    // ✅ التحقق إذا كان userData هو FormData
    const isFormData = userData instanceof FormData
    
    if (selectedUser.value) {
      // ✅ للتعديل - إضافة _method
      if (isFormData) {
        // التأكد من وجود _method
        if (!userData.has('_method')) {
          userData.append('_method', 'PUT')
        }
      }
      
      // ✅ إرسال مع headers صحيحة
      await userStore.updateUser(selectedUser.value.id, userData)
      alert('تم تحديث المشرف بنجاح')
    } else {
      // ✅ للإضافة
      if (isFormData) {
        userData.append('role', 'Admin')
      } else {
        userData.role = 'Admin'
      }
      await userStore.createUser(userData)
      alert('تم إضافة المشرف بنجاح')
    }
    closeModal()
    await userStore.fetchUsers()
  } catch (error: any) {
    console.error('Error saving user:', error)
    alert(error.message || 'حدث خطأ أثناء حفظ المشرف')
  }
}

// تنظيف الـ timeout عند إلغاء تحميل الكومبوننت
onUnmounted(() => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
});

// جلب البيانات عند تحميل الكومبوننت
onMounted(() => {
  userStore.fetchUsers(q.value, role.value);
});

watch(
  () => props.roleFilter,
  (newRole) => {
    role.value = newRole || '';
    userStore.fetchUsers(q.value, role.value);
  }
);
</script>

<style scoped>
.direction-ltr {
  direction: ltr;
  text-align: left;
}

/* تحسين مظهر select */
select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  background-position: left 0.75rem center;
  background-repeat: no-repeat;
  background-size: 1em;
  padding-left: 2.5rem;
  padding-right: 1rem;
}

.dark select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
}
</style>