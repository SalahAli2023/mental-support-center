export type UserRole = 'Admin' | 'Therapist' | 'Client';

export interface User {
    id: string;
    name: string;
    email: string;
    role: UserRole;
    phone?: string;
    avatar?: string;
    bio?: string;
    joinedAt: string;
    createdAt?: string;
    updatedAt?: string;
}

export interface UsersResponse {
    success: boolean;
    data: User[];
    total: number;
}

export interface UserStats {
    total_users: number;
    total_clients: number;
    total_therapists: number;
    total_admins: number;
    new_users: number;
}