import api from '@/utils/api';

export const programService = {
    // ==================== البرامج ====================
    
    /**
     * الحصول على جميع البرامج مع إمكانية التصفية
     * @param {Object} params - معاملات التصفية والترتيب
     */
    getAllPrograms(params) {
        return api.get('/api/programs', { params });
    },

    /**
     * الحصول على برنامج محدد
     * @param {string} id - معرف البرنامج
     */
    getProgram(id) {
        return api.get(`/api/programs/${id}`);
    },

    /**
     * إنشاء برنامج جديد
     * @param {FormData|Object} data - بيانات البرنامج
     */
    createProgram(data) {
        return api.post('/api/programs', data);
    },

    /**
     * تحديث برنامج
     * @param {string} id - معرف البرنامج
     * @param {FormData|Object} data - بيانات التحديث
     */
    updateProgram(id, data) {
        return api.put(`/api/programs/${id}`, data);
    },

    /**
     * حذف برنامج
     * @param {string} id - معرف البرنامج
     */
    deleteProgram(id) {
        return api.delete(`/api/programs/${id}`);
    },

    /**
     * تفعيل/تعطيل البرنامج
     * @param {string} id - معرف البرنامج
     */
    toggleProgramStatus(id) {
        return api.patch(`/api/programs/${id}/toggle-status`);
    },

    /**
     * الحصول على إحصائيات البرنامج
     * @param {string} id - معرف البرنامج
     */
    getProgramStatistics(id) {
        return api.get(`/api/programs/${id}/statistics`);
    },

    /**
     * الحصول على المستخدمين المسجلين في البرنامج
     * @param {string} id - معرف البرنامج
     * @param {Object} params - معاملات التصفية
     */
    getProgramUsers(id, params) {
        return api.get(`/api/programs/${id}/users`, { params });
    },

    // ==================== البرامج العامة (بدون مصادقة) ====================
    
    /**
     * الحصول على البرامج العامة
     * @param {Object} params - معاملات التصفية
     */
    getPublicPrograms(params) {
        // api.js يحتوي على baseURL يتضمن /api بالفعل
        return api.get('/frontend/programs', { params });
    },

    /**
     * الحصول على برنامج عام
     * @param {string} id - معرف البرنامج
     */
    getPublicProgram(id) {
        // api.js يحتوي على baseURL يتضمن /api بالفعل
        return api.get(`/frontend/programs/${id}`);
    },

    /**
     * التسجيل في برنامج
     * @param {string} programId - معرف البرنامج
     */
    enrollInProgram(programId) {
        // api.js يحتوي على baseURL يتضمن /api بالفعل
        return api.post(`/frontend/programs/${programId}/enroll`);
    },

    // ==================== الجلسات ====================

    /**
     * الحصول على جميع الجلسات
     * @param {Object} params - معاملات التصفية
     */
    getAllSessions(params) {
        return api.get('/api/sessions', { params });
    },

    /**
     * الحصول على جلسات برنامج محدد
     * @param {string} programId - معرف البرنامج
     * @param {Object} params - معاملات التصفية
     */
    getProgramSessions(programId, params = {}) {
        return api.get(`/api/sessions/by-program/${programId}`, { params });
    },

    /**
     * الحصول على جلسة محددة
     * @param {string} id - معرف الجلسة
     */
    getSession(id) {
        return api.get(`/api/sessions/${id}`);
    },

    /**
     * الحصول على جلسة مع تقدم المستخدم
     * @param {string} sessionId - معرف الجلسة
     */
    getUserSession(sessionId) {
        return api.get(`/api/sessions/${sessionId}/user-session`);
    },

    /**
     * إنشاء جلسة جديدة
     * @param {Object} data - بيانات الجلسة
     * @param {string|null} programId - معرف البرنامج (اختياري)
     */
    createSession(data, programId = null) {
        if (programId) {
            return api.post(`/api/programs/${programId}/sessions`, data);
        }
        return api.post('/api/sessions', data);
    },

    /**
     * تحديث جلسة
     * @param {string} id - معرف الجلسة
     * @param {Object} data - بيانات التحديث
     */
    updateSession(id, data) {
        return api.put(`/api/sessions/${id}`, data);
    },

    /**
     * حذف جلسة
     * @param {string} id - معرف الجلسة
     */
    deleteSession(id) {
        return api.delete(`/api/sessions/${id}`);
    },

    /**
     * تحديث حالة الجلسة
     * @param {string} id - معرف الجلسة
     * @param {string} status - الحالة الجديدة
     */
    updateSessionStatus(id, status) {
        return api.patch(`/api/sessions/${id}/update-status`, { status });
    },

    /**
     * تفعيل/تعطيل الجلسة
     * @param {string} id - معرف الجلسة
     */
    toggleSessionStatus(id) {
        return api.patch(`/api/sessions/${id}/toggle-status`);
    },

    /**
     * الحصول على جلسات اليوم
     */
    getTodaySessions() {
        return api.get('/api/sessions/today');
    },

    /**
     * إعادة ترتيب الجلسات
     * @param {Array} sessions - مصفوفة الجلسات مع الأوامر الجديدة
     */
    reorderSessions(sessions) {
        return api.post('/api/sessions/reorder', { sessions });
    },

    // ==================== الأنشطة ====================

    /**
     * الحصول على جميع الأنشطة
     * @param {Object} params - معاملات التصفية
     */
    getAllActivities(params) {
        return api.get('/api/activities', { params });
    },

    /**
     * الحصول على أنشطة جلسة محددة
     * @param {string} sessionId - معرف الجلسة
     */
    getSessionActivities(sessionId) {
        return api.get(`/api/activities/by-session/${sessionId}`);
    },

    /**
     * الحصول على نشاط محدد
     * @param {string} id - معرف النشاط
     */
    getActivity(id) {
        return api.get(`/api/activities/${id}`);
    },

    /**
     * الحصول على نشاط مع تقدم المستخدم
     * @param {string} activityId - معرف النشاط
     */
    getUserActivity(activityId) {
        return api.get(`/api/activities/${activityId}/user-activity`);
    },

    /**
     * إنشاء نشاط جديد
     * @param {Object} data - بيانات النشاط
     * @param {string|null} sessionId - معرف الجلسة (اختياري)
     */
    createActivity(data, sessionId = null) {
        if (sessionId) {
            return api.post(`/api/sessions/${sessionId}/activities`, data);
        }
        return api.post('/api/activities', data);
    },

    /**
     * إنشاء عدة أنشطة دفعة واحدة
     * @param {string} sessionId - معرف الجلسة
     * @param {Array} activities - مصفوفة الأنشطة
     */
    bulkCreateActivities(sessionId, activities) {
        return api.post('/api/activities/bulk', {
            session_id: sessionId,
            activities
        });
    },

    /**
     * تحديث نشاط
     * @param {string} id - معرف النشاط
     * @param {Object} data - بيانات التحديث
     */
    updateActivity(id, data) {
        return api.put(`/api/activities/${id}`, data);
    },

    /**
     * حذف نشاط
     * @param {string} id - معرف النشاط
     */
    deleteActivity(id) {
        return api.delete(`/api/activities/${id}`);
    },

    /**
     * تفعيل/تعطيل النشاط
     * @param {string} id - معرف النشاط
     */
    toggleActivityStatus(id) {
        return api.patch(`/api/activities/${id}/toggle-status`);
    },

    /**
     * إعادة ترتيب الأنشطة
     * @param {Array} activities - مصفوفة الأنشطة مع الأوامر الجديدة
     */
    reorderActivities(activities) {
        return api.post('/api/activities/reorder', { activities });
    },

    // ==================== التقديمات والإكمالات ====================

    /**
     * إنشاء تقديم لنشاط
     * @param {Object} data - بيانات التقديم
     */
    createSubmission(data) {
        return api.post('/api/submissions', data);
    },

    /**
     * الحصول على إكمالات جلسة
     * @param {string} sessionId - معرف الجلسة
     */
    getSessionCompletions(sessionId) {
        return api.get(`/api/sessions/${sessionId}/completions`);
    },

    /**
     * إنشاء/تحديث إكمال جلسة
     * @param {string} sessionId - معرف الجلسة
     * @param {Object} data - بيانات الإكمال
     */
    createSessionCompletion(sessionId, data) {
        return api.post(`/api/sessions/${sessionId}/completions`, data);
    },

    // ==================== معالجة الصور ====================

    /**
     * رفع صورة (برنامج أو جلسة)
     * @param {File} image - ملف الصورة
     * @param {string} type - نوع الصورة ('program' أو 'session')
     * @param {string} id - المعرف (للتسمية)
     */
    uploadImage(image, type = 'program', id = null) {
        const formData = new FormData();
        formData.append('image', image);
        formData.append('type', type);
        
        if (id) {
            formData.append('id', id);
        }
        
        return api.post('/api/upload-image', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
    },

    /**
     * معالجة صورة Base64
     * @param {string} base64Image - صورة Base64
     * @param {string} type - نوع الصورة
     */
    processBase64Image(base64Image, type = 'program') {
        return api.post('/api/process-base64-image', {
            image_url: base64Image,
            type
        });
    },

    // ==================== المراحل (Phases) ====================

    /**
     * الحصول على جميع المراحل لبرنامج
     * @param {string} programId - معرف البرنامج
     */
    getPhases(programId) {
        return api.get(`/api/programs/${programId}/phases`);
    },

    /**
     * إنشاء مرحلة جديدة
     * @param {string} programId - معرف البرنامج
     * @param {Object} data - بيانات المرحلة
     */
    createPhase(programId, data) {
        return api.post(`/api/programs/${programId}/phases`, data);
    },

    /**
     * تحديث مرحلة
     * @param {string} programId - معرف البرنامج
     * @param {string} phaseId - معرف المرحلة
     * @param {Object} data - بيانات التحديث
     */
    updatePhase(programId, phaseId, data) {
        return api.put(`/api/programs/${programId}/phases/${phaseId}`, data);
    },

    /**
     * حذف مرحلة
     * @param {string} programId - معرف البرنامج
     * @param {string} phaseId - معرف المرحلة
     */
    deletePhase(programId, phaseId) {
        return api.delete(`/api/programs/${programId}/phases/${phaseId}`);
    },

    /**
     * إعادة ترتيب المراحل
     * @param {string} programId - معرف البرنامج
     * @param {Array} phases - مصفوفة المراحل مع الأوامر الجديدة
     */
    reorderPhases(programId, phases) {
        return api.post(`/api/programs/${programId}/phases/reorder`, { phases });
    },

    // ==================== المهام المنزلية (Homework) ====================

    /**
     * الحصول على جميع المهام المنزلية لجلسة
     * @param {string} sessionId - معرف الجلسة
     */
    getHomework(sessionId) {
        return api.get(`/api/program-sessions/${sessionId}/homework`);
    },

    /**
     * إنشاء مهمة منزلية جديدة
     * @param {string} sessionId - معرف الجلسة
     * @param {Object} data - بيانات المهمة
     */
    createHomework(sessionId, data) {
        return api.post(`/api/program-sessions/${sessionId}/homework`, data);
    },

    /**
     * تحديث مهمة منزلية
     * @param {string} sessionId - معرف الجلسة
     * @param {string} homeworkId - معرف المهمة
     * @param {Object} data - بيانات التحديث
     */
    updateHomework(sessionId, homeworkId, data) {
        return api.put(`/api/program-sessions/${sessionId}/homework/${homeworkId}`, data);
    },

    /**
     * حذف مهمة منزلية
     * @param {string} sessionId - معرف الجلسة
     * @param {string} homeworkId - معرف المهمة
     */
    deleteHomework(sessionId, homeworkId) {
        return api.delete(`/api/program-sessions/${sessionId}/homework/${homeworkId}`);
    },

    /**
     * تسليم مهمة منزلية
     * @param {string} sessionId - معرف الجلسة
     * @param {string} homeworkId - معرف المهمة
     * @param {Object} data - بيانات التسليم
     */
    submitHomework(sessionId, homeworkId, data) {
        return api.post(`/api/program-sessions/${sessionId}/homework/${homeworkId}/submit`, data);
    },

    /**
     * إعادة ترتيب المهام المنزلية
     * @param {string} sessionId - معرف الجلسة
     * @param {Array} homework - مصفوفة المهام مع الأوامر الجديدة
     */
    reorderHomework(sessionId, homework) {
        return api.post(`/api/program-sessions/${sessionId}/homework/reorder`, { homework });
    },

    // ==================== التقدم (Progress) ====================

    /**
     * الحصول على تقدم المستخدم في برنامج
     * @param {string} programId - معرف البرنامج
     */
    getUserProgress(programId) {
        // api.js يحتوي على baseURL يتضمن /api بالفعل
        return api.get(`/frontend/programs/${programId}/progress`);
    },

    /**
     * بدء نشاط
     * @param {string} programId - معرف البرنامج
     * @param {string} activityId - معرف النشاط
     */
    startActivity(programId, activityId) {
        return api.post(`/api/programs/${programId}/progress/activities/${activityId}/start`);
    },

    /**
     * إكمال نشاط
     * @param {string} programId - معرف البرنامج
     * @param {string} activityId - معرف النشاط
     * @param {Object} data - بيانات الإكمال
     */
    completeActivity(programId, activityId, data = {}) {
        return api.post(`/api/programs/${programId}/progress/activities/${activityId}/complete`, data);
    },

    /**
     * التحقق من حالة نشاط
     * @param {string} programId - معرف البرنامج
     * @param {string} activityId - معرف النشاط
     */
    checkActivityStatus(programId, activityId) {
        return api.get(`/api/programs/${programId}/progress/activities/${activityId}/status`);
    },

    // ==================== التقييمات (Assessments) ====================

    /**
     * الحصول على جميع التقييمات لبرنامج
     * @param {string} programId - معرف البرنامج
     */
    getAssessments(programId) {
        return api.get(`/api/programs/${programId}/assessments`);
    },

    /**
     * إنشاء تقييم جديد
     * @param {string} programId - معرف البرنامج
     * @param {Object} data - بيانات التقييم
     */
    createAssessment(programId, data) {
        return api.post(`/api/programs/${programId}/assessments`, data);
    },

    /**
     * تحديث تقييم
     * @param {string} programId - معرف البرنامج
     * @param {string} assessmentId - معرف التقييم
     * @param {Object} data - بيانات التحديث
     */
    updateAssessment(programId, assessmentId, data) {
        return api.put(`/api/programs/${programId}/assessments/${assessmentId}`, data);
    },

    /**
     * حذف تقييم
     * @param {string} programId - معرف البرنامج
     * @param {string} assessmentId - معرف التقييم
     */
    deleteAssessment(programId, assessmentId) {
        return api.delete(`/api/programs/${programId}/assessments/${assessmentId}`);
    },

    /**
     * حفظ نتيجة تقييم
     * @param {string} programId - معرف البرنامج
     * @param {string} assessmentId - معرف التقييم
     * @param {Object} data - بيانات النتيجة
     */
    submitAssessmentResult(programId, assessmentId, data) {
        return api.post(`/api/programs/${programId}/assessments/${assessmentId}/submit-result`, data);
    },

    /**
     * الحصول على نتائج التقييمات لمستخدم
     * @param {string} programId - معرف البرنامج
     * @param {string} userId - معرف المستخدم (اختياري)
     */
    getUserAssessmentResults(programId, userId = null) {
        const url = userId 
            ? `/api/programs/${programId}/assessments/results/${userId}`
            : `/api/programs/${programId}/assessments/results`;
        return api.get(url);
    }
};

// استخدام افتراضي للتوافق مع الاستيرادات
export default programService;

// تصدير الدوال بشكل فردي للاستيراد الاختياري
export const {
    getAllPrograms,
    getProgram,
    createProgram,
    updateProgram,
    deleteProgram,
    toggleProgramStatus,
    getProgramStatistics,
    getPublicPrograms,
    getPublicProgram,
    enrollInProgram,
    getAllSessions,
    getProgramSessions,
    getSession,
    getUserSession,
    createSession,
    updateSession,
    deleteSession,
    updateSessionStatus,
    toggleSessionStatus,
    getTodaySessions,
    reorderSessions,
    getAllActivities,
    getSessionActivities,
    getActivity,
    getUserActivity,
    createActivity,
    bulkCreateActivities,
    updateActivity,
    deleteActivity,
    toggleActivityStatus,
    reorderActivities,
    createSubmission,
    getSessionCompletions,
    createSessionCompletion,
    uploadImage,
    processBase64Image
} = programService;