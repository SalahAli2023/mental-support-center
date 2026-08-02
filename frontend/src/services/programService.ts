import api from '@/plugins/axios';
import type { 
  Program, 
  Session, 
  Activity, 
  ProgramFilters, 
  SessionFilters, 
  ActivityFilters 
} from '@/types/program';

export const programService = {
  // Programs
  async getAllPrograms(params?: ProgramFilters) {
    return api.get('/programs', { params });
  },

  async getProgram(id: string) {
    return api.get(`/programs/${id}`);
  },

  async createProgram(data: any) {
    return api.post('/programs', data);
  },

  async updateProgram(id: string, data: any) {
    return api.put(`/programs/${id}`, data);
  },

  async deleteProgram(id: string) {
    return api.delete(`/programs/${id}`);
  },

  async toggleProgramStatus(id: string) {
    return api.patch(`/programs/${id}/toggle-status`);
  },

  async getProgramStatistics(id: string) {
    return api.get(`/programs/${id}/statistics`);
  },

  // Sessions
  async getAllSessions(params?: SessionFilters) {
    return api.get('/sessions', { params });
  },

  async getProgramSessions(programId: string, params?: any) {
    return api.get(`/programs/${programId}/sessions`, { params });
  },

  async getSession(id: string) {
    return api.get(`/sessions/${id}`);
  },

  async createSession(data: any, programId?: string) {
    if (programId) {
      return api.post(`/programs/${programId}/sessions`, data);
    }
    return api.post('/sessions', data);
  },

  async updateSession(id: string, data: any) {
    return api.put(`/sessions/${id}`, data);
  },

  async deleteSession(id: string) {
    return api.delete(`/sessions/${id}`);
  },

  async updateSessionStatus(id: string, status: string) {
    return api.patch(`/sessions/${id}/status`, { status });
  },

  async toggleSessionStatus(id: string) {
    return api.patch(`/sessions/${id}/toggle-status`);
  },

  async reorderSessions(sessions: Array<{ id: string; session_order: number }>) {
    return api.post('/sessions/reorder', { sessions });
  },

  async getTodaySessions() {
    return api.get('/sessions/today');
  },

  // Activities
  async getAllActivities(params?: ActivityFilters) {
    return api.get('/activities', { params });
  },

  async getSessionActivities(sessionId: string) {
    return api.get(`/sessions/${sessionId}/activities`);
  },

  async getActivity(id: string) {
    return api.get(`/activities/${id}`);
  },

  async createActivity(data: any, sessionId?: string) {
    if (sessionId) {
      return api.post(`/sessions/${sessionId}/activities`, data);
    }
    return api.post('/activities', data);
  },

  async updateActivity(id: string, data: any) {
    return api.put(`/activities/${id}`, data);
  },

  async deleteActivity(id: string) {
    return api.delete(`/activities/${id}`);
  },

  async toggleActivityStatus(id: string) {
    return api.patch(`/activities/${id}/toggle-status`);
  },

  async reorderActivities(activities: Array<{ id: string; activity_order: number }>) {
    return api.post('/activities/reorder', { activities });
  },

  // Public Programs
  async getPublicPrograms(params?: any) {
    return api.get('/public/programs', { params });
  },

  async getPublicProgram(id: string) {
    return api.get(`/public/programs/${id}`);
  },

  // Progress & Unlocking
  async checkActivityStatus(programId: string, activityId: string) {
    return api.get(`/programs/${programId}/progress/activities/${activityId}/status`);
  },

  async startActivity(programId: string, activityId: string) {
    return api.post(`/programs/${programId}/progress/activities/${activityId}/start`);
  },

  async completeActivity(programId: string, activityId: string, data?: any) {
    return api.post(`/programs/${programId}/progress/activities/${activityId}/complete`, data);
  }
};