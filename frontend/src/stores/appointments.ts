import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/utils/api';

export type AppointmentStatus = 'pending' | 'confirmed' | 'completed' | 'cancelled';

export interface Appointment {
      id: number;
      client_id: number;
      client?: {
            id: number;
            name: string;
            email: string;
      };
      therapist_id: number;
      therapist?: {
            id: number;
            name_ar: string;
            name_en: string;
      };
      starts_at: string;
      ends_at: string | null;
      status: AppointmentStatus;
      notes: string | null;
      cancellation_reason: string | null;
      created_at: string;
      updated_at: string;
}

export const useAppointmentStore = defineStore('appointments', () => {
      const items = ref<Appointment[]>([]);
      const loading = ref(false);
      const currentPage = ref(1);
      const totalPages = ref(1);

      const fetchAll = async (filters = {}) => {
            try {
                  loading.value = true;
                  const params = {
                        page: currentPage.value,
                        per_page: 15,
                        ...filters
                  };

                  const response = await api.get('/appointments', { params });

                  if (response.data.data) {
                        items.value = response.data.data;
                        currentPage.value = response.data.meta?.current_page || 1;
                        totalPages.value = response.data.meta?.last_page || 1;
                  }
            } catch (error) {
                  console.error('Error fetching appointments:', error);
                  throw error;
            } finally {
                  loading.value = false;
            }
      };

      const createAppointment = async (appointmentData: {
            client_id: number;
            therapist_id: number;
            starts_at: string;
            ends_at?: string;
            status?: AppointmentStatus;
            notes?: string;
      }) => {
            try {
                  loading.value = true;
                  const response = await api.post('/appointments', appointmentData);

                  if (response.data) {
                        items.value.unshift(response.data);
                        return response.data;
                  }
            } catch (error) {
                  console.error('Error creating appointment:', error);
                  throw error;
            } finally {
                  loading.value = false;
            }
      };

      const updateAppointment = async (id: number, appointmentData: Partial<Appointment>) => {
            try {
                  loading.value = true;
                  const response = await api.put(`/appointments/${id}`, appointmentData);

                  if (response.data) {
                        const index = items.value.findIndex(a => a.id === id);
                        if (index !== -1) {
                              items.value[index] = response.data;
                        }
                        return response.data;
                  }
            } catch (error) {
                  console.error('Error updating appointment:', error);
                  throw error;
            } finally {
                  loading.value = false;
            }
      };

      const deleteAppointment = async (id: number) => {
            try {
                  loading.value = true;
                  await api.delete(`/appointments/${id}`);
                  items.value = items.value.filter(a => a.id !== id);
            } catch (error) {
                  console.error('Error deleting appointment:', error);
                  throw error;
            } finally {
                  loading.value = false;
            }
      };

      const upcomingCount = () => {
            return items.value.filter(i =>
                  i.status === 'pending' || i.status === 'confirmed'
            ).length;
      };

      return {
            items,
            loading,
            currentPage,
            totalPages,
            fetchAll,
            createAppointment,
            updateAppointment,
            deleteAppointment,
            upcomingCount
      };
});
