<template>
  <div class="space-y-4 p-3 sm:p-4">
    <!-- عنوان + زر -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h1 class="text-lg font-semibold text-primary sm:text-2xl">
        {{ props.filter === 'upcoming' ? t('nav.upcomingAppointments') : 
           props.filter === 'history' ? t('nav.appointmentHistory') : 
           t('nav.appointments') }}
      </h1>
      <Button
        variant="primary"
        class="w-full sm:w-auto"
        @click="openCreate"
        :disabled="loading"
      >
        {{ t('appointments.bookSession') }}
      </Button>
    </div>

    <!-- الفلاتر -->
    <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
      <select
        v-model="statusFilter"
        class="w-full sm:w-48 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
        @change="loadAppointments"
      >
        <option value="">{{ t('appointments.allStatuses') }}</option>
        <option value="scheduled">{{ t('appointments.scheduled') }}</option>
        <option value="completed">{{ t('appointments.completed') }}</option>
        <option value="cancelled">{{ t('appointments.cancelled') }}</option>
        <option value="pending">{{ t('appointments.pending') }}</option>
        <option value="confirmed">{{ t('appointments.confirmed') }}</option>
      </select>

      <select
        v-model="therapistFilter"
        class="w-full sm:w-60 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
        @change="loadAppointments"
      >
        <option value="">{{ t('appointments.allTherapists') }}</option>
        <option
          v-for="therapist in therapistOptions"
          :key="therapist.id"
          :value="therapist.id"
        >
          {{ getTherapistOptionLabel(therapist) }}
        </option>
      </select>

      <select
        v-model="clientFilter"
        class="w-full sm:w-60 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
        @change="loadAppointments"
      >
        <option value="">{{ t('appointments.allClients') }}</option>
        <option
          v-for="client in clients"
          :key="client.id"
          :value="client.id"
        >
          {{ client.name }}
        </option>
      </select>

      <input
        v-model="dateFilter"
        type="date"
        class="w-full sm:w-48 rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
        @change="loadAppointments"
      />
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8 text-secondary">
      {{ t('appointments.loading') }}
    </div>

    <!-- جدول المواعيد -->
    <Card v-else>
      <template #header>{{ t('appointments.list') }}</template>
      <div class="overflow-x-auto -mx-1 sm:mx-0">
        <!-- عرض الجدول للشاشات المتوسطة والكبيرة -->
        <table class="min-w-full text-sm hidden sm:table">
          <thead>
            <tr class="text-start text-secondary">
              <th class="px-2 py-2 min-w-[100px] sm:px-3 sm:py-3 text-start">{{ t('appointments.client') }}</th>
              <th class="px-2 py-2 min-w-[100px] sm:px-3 sm:py-3 text-start">{{ t('appointments.therapist') }}</th>
              <th class="px-2 py-2 min-w-[130px] sm:px-3 sm:py-3 text-start">{{ t('appointments.date') }}</th>
              <th class="px-2 py-2 min-w-[80px] sm:px-3 sm:py-3 text-start">{{ t('appointments.status') }}</th>
              <th class="px-2 py-2 min-w-[80px] sm:px-3 sm:py-3 text-start">{{ t('appointments.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="a in filteredAppointments" :key="a.id" class="border-t border-primary">
              <td class="px-2 py-2 text-primary whitespace-nowrap sm:px-3 sm:py-3">
                {{ a.client?.name || `Client #${a.client_id}` }}
              </td>
              <td class="px-2 py-2 text-primary whitespace-nowrap sm:px-3 sm:py-3">
                {{ getTherapistName(a.therapist) }}
              </td>
              <td class="px-2 py-2 text-primary whitespace-nowrap text-xs sm:px-3 sm:py-3 sm:text-sm">
                {{ formatDate(a.starts_at) }}
              </td>
              <td class="px-2 py-2 sm:px-3 sm:py-3">
                <span
                  class="badge border text-primary text-xs whitespace-nowrap"
                  :class="getStatusClass(a.status)"
                >
                  {{ getStatusLabel(a.status) }}
                </span>
              </td>
              <td class="px-2 py-2 sm:px-3 sm:py-3">
                <div class="flex gap-2">
                  <Button size="sm" variant="outline" class="w-full sm:w-auto" @click="edit(a)">
                    {{ t('appointments.edit') }}
                  </Button>
                  <Button 
                    v-if="(a.status === 'confirmed' || a.status === 'scheduled') && isAdmin" 
                    size="sm" 
                    variant="primary" 
                    class="w-full sm:w-auto" 
                    @click="createSessionFromAppointment(a)"
                    :title="t('appointments.createSession')"
                  >
                    <i class="fas fa-video mr-1"></i>
                    {{ t('appointments.createSession') }}
                  </Button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        
        <!-- عرض البطاقات للشاشات الصغيرة -->
        <div class="space-y-3 sm:hidden">
          <div 
            v-for="a in filteredAppointments" 
            :key="a.id" 
            class="bg-primary rounded-lg border border-primary p-3 space-y-2"
          >
            <div class="flex justify-between items-start">
              <div class="font-medium text-primary">{{ a.client?.name || `Client #${a.client_id}` }}</div>
              <span
                class="badge border text-primary text-xs whitespace-nowrap"
                :class="getStatusClass(a.status)"
              >
                {{ getStatusLabel(a.status) }}
              </span>
            </div>
            <div class="text-sm text-secondary">{{ getTherapistName(a.therapist) }}</div>
            <div class="text-sm text-secondary">{{ formatDate(a.starts_at) }}</div>
            <div class="pt-2 space-y-2">
              <Button size="sm" variant="outline" class="w-full" @click="edit(a)">
                {{ t('appointments.edit') }}
              </Button>
              <Button 
                v-if="(a.status === 'confirmed' || a.status === 'scheduled') && isAdmin" 
                size="sm" 
                variant="primary" 
                class="w-full" 
                @click="createSessionFromAppointment(a)"
              >
                <i class="fas fa-video mr-1"></i>
                {{ t('appointments.createSession') }}
              </Button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="filteredAppointments.length === 0 && !loading" class="text-center py-8 text-secondary">
        {{ t('appointments.noAppointments') }}
      </div>
    </Card>

    <!-- Modal -->
    <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-3 sm:p-4">
      <div class="w-full max-w-md rounded-xl border border-primary bg-primary p-4 shadow-lg max-h-[90vh] overflow-y-auto">
        <div class="mb-4 flex items-center justify-between">
          <div class="text-lg font-semibold text-primary">
            {{ current?.id ? t('appointments.editAppointment') : t('appointments.bookSession') }}
          </div>
          <button class="inline-grid h-8 w-8 place-items-center rounded-lg hover:bg-tertiary text-primary" @click="close">✕</button>
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm text-primary mb-2">{{ t('appointments.client') }}</label>
            <select
              v-model="form.clientId"
              :disabled="!!current"
              class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
            >
              <option value="">{{ t('appointments.selectClient') }}</option>
              <option
                v-for="client in clients"
                :key="client.id"
                :value="client.id"
              >
                {{ client.name }}
              </option>
            </select>
            <p v-if="clientsLoading" class="text-xs text-secondary mt-1">{{ t('appointments.loadingClients') }}</p>
          </div>
          <div>
            <label class="block text-sm text-primary mb-2">{{ t('appointments.therapist') }}</label>
            <select
              v-model="form.therapistId"
              :disabled="!!current"
              class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary"
            >
              <option value="">{{ t('appointments.selectTherapist') }}</option>
              <option
                v-for="therapist in therapistOptions"
                :key="therapist.id"
                :value="therapist.id"
              >
                {{ getTherapistOptionLabel(therapist) }}
              </option>
            </select>
            <p v-if="therapistsLoading" class="text-xs text-secondary mt-1">{{ t('appointments.loadingTherapists') }}</p>
          </div>
          <div>
            <label class="block text-sm text-primary mb-2">{{ t('appointments.dateTime') }}</label>
            <input 
              v-model="form.startsAt" 
              type="datetime-local" 
              class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary" 
            />
          </div>
          <div>
            <label class="block text-sm text-primary mb-2">{{ t('appointments.status') }}</label>
            <select v-model="form.status" class="w-full rounded-lg border border-primary bg-primary px-3 py-2 text-sm text-primary">
              <option value="scheduled">{{ t('appointments.scheduled') }}</option>
              <option value="completed">{{ t('appointments.completed') }}</option>
              <option value="cancelled">{{ t('appointments.cancelled') }}</option>
              <option value="pending">{{ t('appointments.pending') }}</option>
              <option value="confirmed">{{ t('appointments.confirmed') }}</option>
            </select>
          </div>
        </div>
        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
          <Button variant="outline" class="w-full sm:w-auto" @click="close">
            {{ t('appointments.cancel') }}
          </Button>
          <Button variant="primary" class="w-full sm:w-auto" @click="save" :disabled="saving">
            {{ saving ? t('appointments.saving') : t('appointments.save') }}
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '@/stores/auth';
import { useAppointmentStore, type Appointment } from '@/stores/appointments';
import { useUserStore } from '@/stores/users';
import { useTherapistStore } from '@/stores/therapists';
import api from '@/utils/api';
import Button from '@/components/dashboard/component/ui/Button.vue';
import Card from '@/components/dashboard/component/ui/Card.vue';

const { t } = useI18n();
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const appointmentStore = useAppointmentStore();
const userStore = useUserStore();
const therapistStore = useTherapistStore();
const { items: userItems } = storeToRefs(userStore);
const { therapists: therapistItems } = storeToRefs(therapistStore);

// Get filter from route props
const props = defineProps<{
  filter?: 'upcoming' | 'history'
}>();

const isAdmin = computed(() => authStore.user?.role === 'Admin');
const loading = computed(() => appointmentStore.loading);
const appointments = computed(() => appointmentStore.items);
const clientsLoading = ref(false);
const therapistsLoading = ref(false);
const clients = computed(() => userItems.value.filter(user => user.role === 'Client'));
const therapistOptions = computed(() => therapistItems.value || []);

const statusFilter = ref('');
const therapistFilter = ref('');
const clientFilter = ref('');
const dateFilter = ref('');
const saving = ref(false);

// Set initial filter based on route
if (props.filter === 'upcoming') {
  statusFilter.value = '';
} else if (props.filter === 'history') {
  statusFilter.value = 'completed';
}

const modal = ref(false);
const current = ref<Appointment | null>(null);
const form = ref({
  clientId: '',
  therapistId: '',
  startsAt: '',
  status: 'pending' as Appointment['status']
});

// Filter appointments
const filteredAppointments = computed(() => {
  let filtered = appointments.value;

  // Apply route-based filter
  if (props.filter === 'upcoming') {
    // Show only upcoming appointments (scheduled, confirmed, pending)
    filtered = filtered.filter(a => 
      a.status === 'scheduled' || 
      a.status === 'confirmed' || 
      a.status === 'pending' ||
      (a.status !== 'completed' && a.status !== 'cancelled' && new Date(a.starts_at) > new Date())
    );
  } else if (props.filter === 'history') {
    // Show only completed and cancelled appointments
    filtered = filtered.filter(a => 
      a.status === 'completed' || 
      a.status === 'cancelled'
    );
  } else if (statusFilter.value) {
    // Filter by selected status
    filtered = filtered.filter(a => a.status === statusFilter.value);
  }

  // Filter by therapist selection
  if (therapistFilter.value) {
    filtered = filtered.filter(a => String(a.therapist_id) === String(therapistFilter.value));
  }

  if (clientFilter.value) {
    filtered = filtered.filter(a => String(a.client_id) === String(clientFilter.value));
  }

  // Filter by date
  if (dateFilter.value) {
    filtered = filtered.filter(a => {
      const appointmentDate = new Date(a.starts_at).toISOString().split('T')[0];
      return appointmentDate === dateFilter.value;
    });
  }

  return filtered;
});

// Load appointments from API
const loadAppointments = async () => {
  try {
    const filters: any = {};
    if (statusFilter.value) {
      filters.status = statusFilter.value;
    }
    if (dateFilter.value) filters.date = dateFilter.value;
    if (therapistFilter.value) filters.therapist_id = therapistFilter.value;
    if (clientFilter.value) filters.client_id = clientFilter.value;
    
    await appointmentStore.fetchAll(filters);
  } catch (error) {
    console.error('Error loading appointments:', error);
  }
};

const loadClientOptions = async () => {
  try {
    clientsLoading.value = true;
    await userStore.fetchUsers('', 'Client');
  } catch (error) {
    console.error('Error loading clients:', error);
  } finally {
    clientsLoading.value = false;
  }
};

const loadTherapistOptions = async () => {
  try {
    therapistsLoading.value = true;
    await therapistStore.fetchTherapists({ per_page: 200 });
  } catch (error) {
    console.error('Error loading therapists:', error);
  } finally {
    therapistsLoading.value = false;
  }
};

// Format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString(localStorage.getItem('locale') || 'ar', { 
    month: 'short', 
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Get therapist name
const getTherapistName = (therapist: Appointment['therapist']) => {
  if (!therapist) return 'N/A';
  const locale = localStorage.getItem('locale') || 'ar';
  if (locale === 'ar') {
    return therapist.name_ar || therapist.name_en || 'N/A';
  }
  return therapist.name_en || therapist.name_ar || 'N/A';
};

const getTherapistOptionLabel = (therapist: any) => {
  if (!therapist) {
    return '';
  }
  const locale = localStorage.getItem('locale') || 'ar';
  const nameAr = therapist.name_ar || therapist?.user?.name;
  const nameEn = therapist.name_en || therapist?.user?.name;
  return locale === 'ar'
    ? (nameAr || nameEn || '')
    : (nameEn || nameAr || '');
};

// Get status label
const getStatusLabel = (status: string) => {
  const statusMap: Record<string, string> = {
    scheduled: t('appointments.scheduled'),
    completed: t('appointments.completed'),
    cancelled: t('appointments.cancelled'),
    pending: t('appointments.pending'),
    confirmed: t('appointments.confirmed')
  };
  return statusMap[status] || status;
};

// Get status class
const getStatusClass = (status: string) => {
  const classMap: Record<string, string> = {
    scheduled: 'border-emerald-300 text-emerald-700',
    confirmed: 'border-blue-300 text-blue-700',
    completed: 'border-gray-300 text-gray-700',
    cancelled: 'border-rose-300 text-rose-700',
    pending: 'border-yellow-300 text-yellow-700'
  };
  return classMap[status] || 'border-gray-300 text-gray-700';
};

// Modal functions
const openCreate = () => {
  current.value = null;
  form.value = {
    clientId: '',
    therapistId: '',
    startsAt: '',
    status: 'pending'
  };
  modal.value = true;
};

const edit = (a: Appointment) => {
  current.value = a;
  form.value = {
    clientId: String(a.client_id),
    therapistId: String(a.therapist_id),
    startsAt: new Date(a.starts_at).toISOString().slice(0, 16),
    status: a.status
  };
  modal.value = true;
};

const close = () => {
  modal.value = false;
  current.value = null;
};

const save = async () => {
  if (!form.value.startsAt) {
    alert(t('appointments.selectDateTime'));
    return;
  }

  saving.value = true;
  try {
    if (current.value) {
      // Update existing appointment
      await appointmentStore.updateAppointment(current.value.id, {
        starts_at: new Date(form.value.startsAt).toISOString(),
        status: form.value.status
      });
    } else {
      if (!form.value.clientId || !form.value.therapistId) {
        alert(t('appointments.selectClientAndTherapist'));
        saving.value = false;
        return;
      }

      const startISO = new Date(form.value.startsAt).toISOString();
      const defaultDurationMinutes = 45;
      const endISO = new Date(new Date(form.value.startsAt).getTime() + defaultDurationMinutes * 60000).toISOString();

      await appointmentStore.createAppointment({
        client_id: Number(form.value.clientId),
        therapist_id: Number(form.value.therapistId),
        starts_at: startISO,
        ends_at: endISO,
        status: form.value.status,
      });

      // تأكد من إظهار مواعيد المعالج المختار مباشرة
      therapistFilter.value = String(form.value.therapistId);
    }
    modal.value = false;
    await loadAppointments();
  } catch (error: any) {
    console.error('Error saving appointment:', error);
    alert(t('appointments.saveError') + ': ' + (error.response?.data?.message || error.message));
  } finally {
    saving.value = false;
  }
};

// Create session from appointment
const createSessionFromAppointment = async (appointment: Appointment) => {
  if (!confirm(t('appointments.confirmCreateSession', { name: appointment.client?.name || 'Client' }))) {
    return;
  }
  
  try {
    const response = await api.post('/sessions', {
      appointment_id: appointment.id
    });
    
    if (response.data) {
      alert(t('appointments.sessionCreated'));
      router.push({ name: 'sessions' });
    }
  } catch (error: any) {
    console.error('Error creating session:', error);
    alert(t('appointments.sessionCreateError') + ': ' + (error.response?.data?.message || error.message));
  }
};

// Load appointments on mount
onMounted(() => {
  loadAppointments();
  loadClientOptions();
  loadTherapistOptions();
});

// Watch for route changes to update filter
watch(() => route.name, (newRouteName) => {
  if (newRouteName === 'upcoming') {
    statusFilter.value = '';
  } else if (newRouteName === 'history') {
    statusFilter.value = 'completed';
  }
  loadAppointments();
});
</script>
