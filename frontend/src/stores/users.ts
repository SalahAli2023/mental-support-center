import { defineStore } from 'pinia';
import api from '../utils/api';
import type { User, UserRole, UserStats } from '../types/user';
interface UsersState {
  items: User[];
  loading: boolean;
  error: string | null;
}

export const useUserStore = defineStore('users', {
  state: (): UsersState => ({
    items: [],
    loading: false,
    error: null
  }),

  getters: {
    totalClients: (state) => state.items.filter(u => u.role === 'Client').length,
    totalTherapists: (state) => state.items.filter(u => u.role === 'Therapist').length,
    totalAdmins: (state) => state.items.filter(u => u.role === 'Admin').length,
    newUsers: (state) => state.items.filter(u => {
      const joinedDate = new Date(u.joined_at);
      const weekAgo = new Date(Date.now() - 7 * 86400000);
      return joinedDate >= weekAgo;
    }).length,
  },

  actions: {
    async fetchUsers(searchQuery: string = '', roleFilter: string = '') {
      this.loading = true;
      this.error = null;
      
      try {
        const params: any = {};
        if (searchQuery) params.q = searchQuery;
        if (roleFilter) params.role = roleFilter;

        const response = await api.get('/users', { params });
        
        if (response.data.success) {
          this.items = response.data.data;
        } else {
          throw new Error(response.data.message || 'Failed to fetch users');
        }
      } catch (error: any) {
        this.error = error.response?.data?.message || error.message || 'Failed to fetch users';
        console.error('Error fetching users:', error);
      } finally {
        this.loading = false;
      }
    },

    // async createUser(userData: any) {
    //   try {
    //     const response = await api.post('/users', userData);
        
    //     if (response.data.success) {
    //       return response.data.data;
    //     } else {
    //       throw new Error(response.data.message || 'Failed to create user');
    //     }
    //   } catch (error: any) {
    //     const errorMsg = error.response?.data?.message || error.message || 'Failed to create user';
    //     this.error = errorMsg;
    //     throw new Error(errorMsg);
    //   }
    // },

    // async updateUser(userId: number, userData: any) {
    //   try {
    //     const response = await api.put(`/users/${userId}`, userData);
        
    //     if (response.data.success) {
    //       return response.data.data;
    //     } else {
    //       throw new Error(response.data.message || 'Failed to update user');
    //     }
    //   } catch (error: any) {
    //     const errorMsg = error.response?.data?.message || error.message || 'Failed to update user';
    //     this.error = errorMsg;
    //     throw new Error(errorMsg);
    //   }
    // },

    // stores/users.ts

    async updateUser(userId: number, userData: any) {
  try {
    const isFormData = userData instanceof FormData
    
    let response
    
    if (isFormData) {
      // ✅ مع FormData، axios يتعامل مع _method تلقائياً
      response = await api.post(`/users/${userId}`, userData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
    } else {
      response = await api.put(`/users/${userId}`, userData)
    }
    
    if (response.data.success) {
      return response.data.data
    } else {
      throw new Error(response.data.message || 'Failed to update user')
    }
  } catch (error) {
    console.error('Update user error:', error)
    throw error
  }
},

    // ✅ أيضاً تعديل createUser
    async createUser(userData: any) {
  try {
    const isFormData = userData instanceof FormData
    
    let response
    
    if (isFormData) {
      // ✅ مع FormData
      response = await api.post('/users', userData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
    } else {
      // ✅ مع JSON
      response = await api.post('/users', userData)
    }
    
    console.log('📥 Response createUser:', response.data)
    
    if (response.data.success) {
      return response.data.data
    } else {
      throw new Error(response.data.message || 'Failed to create user')
    }
  } catch (error: any) {
    console.error('Create user error:', error)
    // عرض رسائل الخطأ من الـ Backend
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      throw new Error(errors.join('\n'))
    }
    throw error
  }
},

    async deleteUser(userId: number) {
      try {
        const response = await api.delete(`/users/${userId}`);
        
        if (response.data.success) {
          return response.data.data;
        } else {
          throw new Error(response.data.message || 'Failed to delete user');
        }
      } catch (error: any) {
        const errorMsg = error.response?.data?.message || error.message || 'Failed to delete user';
        this.error = errorMsg;
        throw new Error(errorMsg);
      }
    },

    async fetchUserStats() {
      try {
        const response = await api.get('/users/stats');
        
        if (response.data.success) {
          return response.data.data;
        } else {
          throw new Error(response.data.message || 'Failed to fetch user stats');
        }
      } catch (error: any) {
        console.error('Error fetching user stats:', error);
        throw error;
      }
    },

    async getUserById(userId: number) {
      try {
        const response = await api.get(`/users/${userId}`);
        
        if (response.data.success) {
          return response.data.data;
        } else {
          throw new Error(response.data.message || 'Failed to fetch user');
        }
      } catch (error: any) {
        console.error('Error fetching user:', error);
        throw error;
      }
    },

    clearError() {
      this.error = null;
    }
  }
});
